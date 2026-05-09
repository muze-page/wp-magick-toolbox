<?php

if (!class_exists('MaBox_ShortCode_Bilibili')) {
    class MaBox_ShortCode_Bilibili
    {
        public static function run()
        {
            add_shortcode('mabox_bilibili', array(__CLASS__, 'render'));
            add_action('wp_enqueue_scripts', function () {
                global $post;
                if (isset($post) && has_shortcode($post->post_content, 'mabox_bilibili')) {
                    self::load_assets();
                }
            });
        }

        public static function render($atts, $content = null)
        {
            $a = shortcode_atts(array(
                'bvid' => '',
                'aid' => '',
                'page' => 1,
                'autoplay' => 0,
                'muted' => 0,
                'width' => '100%',
                'height' => '500',
                'danmaku' => 1,
            ), $atts);

            if (empty($a['bvid']) && empty($a['aid'])) {
                return '<p style="color:#f56c6c;padding:12px;border:1px dashed #f56c6c;border-radius:4px;">Bilibili 短代码需要 bvid 或 aid 参数</p>';
            }

            $player_id = 'bilibili_player_' . uniqid();
            $src = 'https://player.bilibili.com/player.html?';

            if (!empty($a['bvid'])) {
                $src .= 'bvid=' . esc_attr($a['bvid']);
            } elseif (!empty($a['aid'])) {
                $src .= 'aid=' . esc_attr($a['aid']);
            }

            $src .= '&page=' . intval($a['page']);
            $src .= '&autoplay=' . intval($a['autoplay']);
            $src .= '&muted=' . intval($a['muted']);
            $src .= '&danmaku=' . intval($a['danmaku']);
            $src .= '&high_quality=1';

            $width = esc_attr($a['width']);
            $height = intval($a['height']);

            ob_start();
            ?>
            <div class="mabox-bilibili-wrapper" style="position:relative;width:<?php echo $width; ?>;max-width:100%;padding-bottom:<?php echo ($height / 16 * 9) / $height * 100; ?>%;height:0;overflow:hidden;border-radius:8px;margin:16px 0;">
                <iframe id="<?php echo $player_id; ?>" src="<?php echo $src; ?>" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true" style="position:absolute;width:100%;height:100%;left:0;top:0;" sandbox="allow-top-navigation allow-same-origin allow-forms allow-scripts" loading="lazy"></iframe>
            </div>
            <?php
            return ob_get_clean();
        }

        public static function load_assets()
        {
            if (is_admin()) {
                return;
            }
            $dir = plugin_dir_url(__DIR__) . 'bilibili/';
            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_bilibili_css',
                $dir . 'style.css',
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
        }
    }
}
