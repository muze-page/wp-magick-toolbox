<?php

if (!class_exists('MaBox_ShortCode_Reward')) {
    class MaBox_ShortCode_Reward
    {
        public static function run()
        {
            add_shortcode('mabox_reward', array(__CLASS__, 'render'));
            add_action('wp_enqueue_scripts', function () {
                global $post;
                if (isset($post) && has_shortcode($post->post_content, 'mabox_reward')) {
                    self::load_assets();
                }
            });
        }

        public static function render($atts, $content = null)
        {
            $a = shortcode_atts(array(
                'title' => '',
                'wx_text' => '',
                'ali_text' => '',
            ), $atts);

            $config = self::get_config();
            $wx_qr = esc_url($config['wx_qr']);
            $ali_qr = esc_url($config['ali_qr']);

            if (empty($wx_qr) && empty($ali_qr)) {
                return '';
            }

            $title = !empty($a['title']) ? esc_html($a['title']) : esc_html($config['title']);
            $wx_text = !empty($a['wx_text']) ? esc_html($a['wx_text']) : esc_html($config['wx_text']);
            $ali_text = !empty($a['ali_text']) ? esc_html($a['ali_text']) : esc_html($config['ali_text']);
            $btn_text = esc_html($config['btn_text']);
            $modal_id = 'mabox_reward_' . uniqid();

            ob_start();
            ?>
            <div class="mabox-reward">
                <button class="mabox-reward-btn" onclick="document.getElementById('<?php echo $modal_id; ?>').classList.add('active')">
                    <?php echo $btn_text; ?>
                </button>
                <div class="mabox-reward-modal" id="<?php echo $modal_id; ?>" onclick="if(event.target===this)this.classList.remove('active')">
                    <div class="mabox-reward-content">
                        <button class="mabox-reward-close" onclick="this.parentElement.parentElement.classList.remove('active')">&times;</button>
                        <h3 class="mabox-reward-title"><?php echo $title; ?></h3>
                        <div class="mabox-reward-tabs">
                            <div class="mabox-reward-tab-list">
                                <?php if (!empty($wx_qr)): ?>
                                <button class="mabox-reward-tab active" data-target="wx" onclick="maboxRewardSwitchTab(this)"><?php echo $wx_text; ?></button>
                                <?php endif; ?>
                                <?php if (!empty($ali_qr)): ?>
                                <button class="mabox-reward-tab" data-target="ali" onclick="maboxRewardSwitchTab(this)"><?php echo $ali_text; ?></button>
                                <?php endif; ?>
                            </div>
                            <div class="mabox-reward-qrs">
                                <?php if (!empty($wx_qr)): ?>
                                <div class="mabox-reward-qr active" id="wx">
                                    <img src="<?php echo $wx_qr; ?>" alt="微信收款码" />
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($ali_qr)): ?>
                                <div class="mabox-reward-qr" id="ali">
                                    <img src="<?php echo $ali_qr; ?>" alt="支付宝收款码" />
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        public static function load_assets()
        {
            if (is_admin()) {
                return;
            }
            $dir = plugin_dir_url(__DIR__) . 'reward/';
            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_reward_css',
                $dir . 'style.css',
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_reward_js',
                $dir . 'script.js',
                array(),
                MAGICK_MIXTURE_VERSION,
                true
            );
        }

        private static function get_config()
        {
            $shortcode = MaBox_Config_Manager::get_module_config('shortcode');
            $compose = MaBox_Admin::get_config($shortcode, 'compose', array());
            return array(
                'reward' => MaBox_Admin::get_config($compose, 'reward', false),
                'wx_qr' => MaBox_Admin::get_config($compose, 'reward_wx_qr', ''),
                'ali_qr' => MaBox_Admin::get_config($compose, 'reward_ali_qr', ''),
                'title' => MaBox_Admin::get_config($compose, 'reward_title', '感谢您的支持'),
                'wx_text' => MaBox_Admin::get_config($compose, 'reward_wx_text', '微信'),
                'ali_text' => MaBox_Admin::get_config($compose, 'reward_ali_text', '支付宝'),
                'btn_text' => MaBox_Admin::get_config($compose, 'reward_btn_text', '打赏'),
            );
        }
    }
}
