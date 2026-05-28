<?php

if (!class_exists('MaBox_ShortCode_Wx_Unlock')) {
    class MaBox_ShortCode_Wx_Unlock
    {
        public static function run()
        {
            add_shortcode('mabox_wx_unlock', array(__CLASS__, 'render'));
            add_action('wp_enqueue_scripts', function () {
                global $post;
                if (isset($post) && has_shortcode($post->post_content, 'mabox_wx_unlock')) {
                    self::load_assets();
                }
            });
        }

        public static function render($atts, $content = null)
        {
            $a = shortcode_atts(array(
                'tip' => '',
                'keyword_tip' => '',
            ), $atts);

            $config = self::get_config();
            $is_unlocked = self::check_unlocked();

            if ($is_unlocked) {
                return do_shortcode($content);
            }

            $tip = !empty($a['tip']) ? esc_html($a['tip']) : esc_html($config['unlock_tip']);
            $keyword_tip = !empty($a['keyword_tip']) ? esc_html($a['keyword_tip']) : esc_html($config['keyword_tip']);
            $wx_name = esc_html($config['wx_name']);
            $wx_qrcode = esc_url($config['wx_qrcode']);

            ob_start();
            ?>
            <div class="mabox-wx-unlock" id="mabox-wx-unlock-<?php echo esc_attr(uniqid()); ?>">
                <div class="mabox-wx-unlock-mask">
                    <div class="mabox-wx-unlock-box">
                        <div class="mabox-wx-unlock-icon">
                            <svg viewBox="0 0 1024 1024" width="48" height="48"><path d="M663.7 512.3c-45.1 0-84.4 24.5-105.7 60.8-21.3-36.3-60.6-60.8-105.7-60.8-66.3 0-120.1 53.8-120.1 120.1 0 66.3 53.8 120.1 120.1 120.1 45.1 0 84.4-24.5 105.7-60.8 21.3 36.3 60.6 60.8 105.7 60.8 66.3 0 120.1-53.8 120.1-120.1 0-66.3-53.8-120.1-120.1-120.1z" fill="#07C160"/><path d="M512 0C229.2 0 0 229.2 0 512s229.2 512 512 512 512-229.2 512-512S794.8 0 512 0zm0 960C264.6 960 64 759.4 64 512S264.6 64 512 64s448 200.6 448 448-200.6 448-448 448z" fill="#07C160"/></svg>
                        </div>
                        <h3 class="mabox-wx-unlock-title"><?php echo $tip; ?></h3>
                        <?php if (!empty($wx_name)): ?>
                        <p class="mabox-wx-unlock-desc">关注公众号 <strong><?php echo $wx_name; ?></strong>，回复关键词获取验证码</p>
                        <?php endif; ?>
                        <?php if (!empty($wx_qrcode)): ?>
                        <div class="mabox-wx-unlock-qr">
                            <img src="<?php echo $wx_qrcode; ?>" alt="公众号二维码" />
                        </div>
                        <?php endif; ?>
                        <p class="mabox-wx-unlock-keyword-tip"><?php echo $keyword_tip; ?></p>
                        <div class="mabox-wx-unlock-input-wrap">
                            <input type="text" class="mabox-wx-unlock-input" placeholder="请输入验证码" id="mabox-wx-code" />
                            <button class="mabox-wx-unlock-btn" id="mabox-wx-verify-btn">解锁</button>
                        </div>
                        <div class="mabox-wx-unlock-error" id="mabox-wx-error"></div>
                    </div>
                </div>
                <div class="mabox-wx-unlock-blur">
                    <?php echo do_shortcode($content); ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        public static function ajax_verify()
        {
            check_ajax_referer('mabox_wx_unlock', 'nonce');
            $config = self::get_config();
            $code = sanitize_text_field($_POST['code']);
            $valid_codes = self::get_valid_codes($config);

            if (in_array($code, $valid_codes)) {
                setcookie('mabox_wx_unlocked', '1', time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
                wp_send_json_success(array('message' => '解锁成功'));
            }

            wp_send_json_error(array('message' => '验证码错误，请检查后重试'));
        }

        public static function check_unlocked()
        {
            return isset($_COOKIE['mabox_wx_unlocked']) && $_COOKIE['mabox_wx_unlocked'] === '1';
        }

        public static function load_assets()
        {
            if (is_admin()) {
                return;
            }
            $dir = plugin_dir_url(__DIR__) . 'wx_unlock/';
            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_wx_unlock_css',
                $dir . 'style.css',
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_wx_unlock_js',
                $dir . 'script.js',
                array(),
                MAGICK_MIXTURE_VERSION,
                true
            );
            wp_localize_script(MAGICK_MIXTURE_NAME . '_wx_unlock_js', 'maboxWxUnlock', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mabox_wx_unlock'),
            ));
        }

        private static function get_config()
        {
            $shortcode = MaBox_Config_Manager::get_module_config('shortcode');
            $compose = MaBox_Admin::get_config($shortcode, 'compose', array());
            return array(
                'wx_name' => MaBox_Admin::get_config($compose, 'wx_unlock_name', ''),
                'wx_qrcode' => MaBox_Admin::get_config($compose, 'wx_unlock_qrcode', ''),
                'unlock_codes' => MaBox_Admin::get_config($compose, 'wx_unlock_codes', ''),
                'unlock_tip' => MaBox_Admin::get_config($compose, 'wx_unlock_tip', '关注公众号获取验证码'),
                'keyword_tip' => MaBox_Admin::get_config($compose, 'wx_unlock_keyword_tip', '关注公众号，回复关键词获取验证码'),
            );
        }

        private static function get_valid_codes($config)
        {
            if (empty($config['unlock_codes'])) {
                return array();
            }
            $codes = explode("\n", trim($config['unlock_codes']));
            return array_map('trim', $codes);
        }
    }
}
