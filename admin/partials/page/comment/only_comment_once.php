<?php

defined('ABSPATH') || exit;

/**
 * 效果：一篇文章只能评论一次，管理员不受影响
 * 来源：https://www.npc.ink/13477.html
 */

if (!class_exists('MaBox_Comment_Only_Once')) {
    class MaBox_Comment_Only_Once implements MaBox_Module_Interface
    {
        public static function run($config = array())
        {
            add_filter('pre_comment_approved', array(__CLASS__, 'ludou_only_one_comment'), 10, 2);
        }

        // 获取评论用户的ip，参考wp-includes/comment.php
        public static function ludou_getIP()
        {
            return MaBox_Helpers::get_real_ip();
        }
        public static function ludou_only_one_comment($approved, $commentdata)
        {
            global $wpdb;
            $currentUser = wp_get_current_user();

            // 不限制管理员发表评论
            if (empty($currentUser->roles) || !in_array('administrator', $currentUser->roles)) {
                $bool = $wpdb->get_var($wpdb->prepare(
                    "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_author = %s OR comment_author_email = %s OR comment_author_IP = %s) LIMIT 0, 1",
                    $commentdata['comment_post_ID'],
                    $commentdata['comment_author'],
                    $commentdata['comment_author_email'],
                    self::ludou_getIP()
                ));

                if ($bool) {
                    return new \WP_Error('comment_once_only', '本站每篇文章仅允许评论一次。');
                }
            }

            return $approved;
        }
    }
}
