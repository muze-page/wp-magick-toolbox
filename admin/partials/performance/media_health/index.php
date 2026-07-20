<?php
defined('ABSPATH') || exit;
if (!class_exists('Npcink_Toolbox_Performance_Media_Health')) {
    class Npcink_Toolbox_Performance_Media_Health implements Npcink_Toolbox_Module_Interface {
        const ATTACHMENT_SCAN_BATCH_SIZE = 100;
        const ATTACHMENT_SCAN_LIMIT = 500;

        private static $config;
        public static function run($config = array()) {
            self::$config = $config;
            if (empty($config['enabled'])) return;
        }
        public static function ajax_check() {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', '权限不足', array('status' => 403));
            }
            $issues = array();
            global $wpdb;

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Administrator-triggered live aggregate; cached diagnostic counts would be stale.
            $missing_alt = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = %s WHERE p.post_type = %s AND p.post_mime_type LIKE %s AND (pm.meta_value IS NULL OR pm.meta_value = '')",
                '_wp_attachment_image_alt',
                'attachment',
                'image/%'
            ));
            if ($missing_alt > 0) {
                $issues[] = array('type' => '缺少Alt', 'count' => intval($missing_alt));
            }

            $attachment_scan = self::scan_recent_attachments();
            if ($attachment_scan['large'] > 0) {
                $issues[] = array(
                    'type'         => $attachment_scan['sampled']
                        ? sprintf('超大图片（最近 %d 个附件抽样）', $attachment_scan['checked'])
                        : '超大图片',
                    'count'        => $attachment_scan['large'],
                    'sampled'      => $attachment_scan['sampled'],
                    'sample_size'  => $attachment_scan['checked'],
                    'total_attachments' => $attachment_scan['total'],
                );
            }

            if ($attachment_scan['chinese'] > 0) {
                $issues[] = array(
                    'type'  => $attachment_scan['sampled']
                        ? sprintf('中文文件名（最近 %d 个附件抽样）', $attachment_scan['checked'])
                        : '中文文件名',
                    'count' => $attachment_scan['chinese'],
                );
            }

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Administrator-triggered bounded diagnostic returns at most 100 current candidates.
            $unused = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT p.ID FROM {$wpdb->posts} p WHERE p.post_type = %s AND NOT EXISTS (SELECT 1 FROM {$wpdb->posts} parent WHERE parent.post_content LIKE CONCAT(%s, p.guid, %s) OR parent.post_excerpt LIKE CONCAT(%s, p.guid, %s)) LIMIT 100",
                    'attachment',
                    '%',
                    '%',
                    '%',
                    '%'
                )
            );
            if (count($unused) > 0) {
                $issues[] = array('type' => '可能未使用', 'count' => count($unused));
            }

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Administrator-triggered live aggregate; cached diagnostic counts would be stale.
            $missing_featured = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = %s WHERE p.post_status = %s AND p.post_type = %s AND pm.meta_id IS NULL", '_thumbnail_id', 'publish', 'post'));
            if ($missing_featured > 0) {
                $issues[] = array('type' => '无特色图文章', 'count' => intval($missing_featured));
            }

            return rest_ensure_response(array(
                'success' => true,
                'data'    => array(
                    'issues' => $issues,
                    'attachment_scan' => array(
                        'checked' => $attachment_scan['checked'],
                        'total'   => $attachment_scan['total'],
                        'sampled' => $attachment_scan['sampled'],
                    ),
                ),
            ));
        }
        public static function ajax_fix_alt() {
            if (!current_user_can('manage_options')) {
                return new \WP_Error('rest_forbidden', '权限不足', array('status' => 403));
            }
            $query = new WP_Query(array(
                'post_type'              => 'attachment',
                'post_status'            => 'inherit',
                'post_mime_type'         => 'image',
                'posts_per_page'         => 50,
                'orderby'                => 'ID',
                'order'                  => 'ASC',
                'no_found_rows'          => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Administrator-triggered repair is bounded to 50 image attachments.
                'meta_query'             => array(
                    'relation' => 'OR',
                    array('key' => '_wp_attachment_image_alt', 'compare' => 'NOT EXISTS'),
                    array('key' => '_wp_attachment_image_alt', 'value' => '', 'compare' => '='),
                ),
            ));
            $fixed = 0;
            foreach ($query->posts as $img) {
                if (!is_object($img) || !isset($img->ID)) continue;
                $alt = !empty($img->post_title) ? $img->post_title : '图片';
                update_post_meta($img->ID, '_wp_attachment_image_alt', sanitize_text_field($alt));
                $fixed++;
            }
            return rest_ensure_response(array(
                'success' => true,
                'data'    => array('fixed' => $fixed),
            ));
        }

        private static function scan_recent_attachments() {
            $checked = 0;
            $large = 0;
            $chinese = 0;
            $total = 0;
            $page = 1;

            while ($checked < self::ATTACHMENT_SCAN_LIMIT) {
                $query = new WP_Query(array(
                    'post_type'              => 'attachment',
                    'post_status'            => 'inherit',
                    'posts_per_page'         => self::ATTACHMENT_SCAN_BATCH_SIZE,
                    'paged'                  => $page,
                    'orderby'                => 'ID',
                    'order'                  => 'DESC',
                    'no_found_rows'          => $page > 1,
                    'update_post_meta_cache' => false,
                    'update_post_term_cache' => false,
                ));
                $attachments = is_array($query->posts) ? $query->posts : array();
                if ($page === 1) {
                    $total = max(0, intval($query->found_posts));
                }
                if (empty($attachments)) break;

                $image_ids = array();
                foreach ($attachments as $attachment) {
                    if (
                        is_object($attachment)
                        && isset($attachment->ID, $attachment->post_mime_type)
                        && strpos((string) $attachment->post_mime_type, 'image/') === 0
                    ) {
                        $image_ids[] = intval($attachment->ID);
                    }
                }
                if (!empty($image_ids)) {
                    update_meta_cache('post', $image_ids);
                }

                foreach ($attachments as $attachment) {
                    if ($checked >= self::ATTACHMENT_SCAN_LIMIT) break;
                    if (!is_object($attachment) || !isset($attachment->ID)) continue;
                    $checked++;

                    $post_name = isset($attachment->post_name) ? (string) $attachment->post_name : '';
                    if (preg_match('/[\x{4e00}-\x{9fff}]/u', $post_name)) $chinese++;

                    $mime_type = isset($attachment->post_mime_type)
                        ? (string) $attachment->post_mime_type
                        : '';
                    if (strpos($mime_type, 'image/') !== 0) continue;

                    $file = get_attached_file(intval($attachment->ID));
                    if (!is_string($file) || !is_file($file)) continue;
                    $size = filesize($file);
                    if (is_int($size) && $size > 512000) $large++;
                }

                if (count($attachments) < self::ATTACHMENT_SCAN_BATCH_SIZE) break;
                $page++;
            }

            return array(
                'checked' => $checked,
                'total'   => $total,
                'large'   => $large,
                'chinese' => $chinese,
                'sampled' => $total > $checked,
            );
        }
    }
}
