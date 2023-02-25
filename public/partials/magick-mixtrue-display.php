<?php

/**
 * 为插件提供面向公众的视图
 *
 *此文件用于标记插件的面向公共方面。
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public/partials
 */

if (!class_exists('Magick_Mixtrue_Display')) {
    class Magick_Mixtrue_Display
    {
        private static $plugin_data = array();

        public function __construct($name, $version)
        {
            self::$plugin_data['name'] = $name;
            self::$plugin_data['version'] = $version;

            //加载表情包

            //判断，是否加载表情
            if (get_option('slider_ons') === 'yes') {
                self::load_owo();

            }

        }

        /**
         * 加载表情包
         */
        public static function load_owo()
        {

            //判断，当前文章或页面是否开启评论

            //加载js和css资源
            add_action('wp_enqueue_scripts', array(__CLASS__, 'load_owo_resouce'));
            //加载配置js
            add_action('wp_footer', array(__CLASS__, 'load_owo_comment_js'));
            //加载表情包位置
            add_filter('comment_form_defaults', array(__CLASS__, 'load_owo_content'));

        }

        /**
         * 加载表情用资源
         */
        public static function load_owo_resouce()
        {
            wp_enqueue_script(
                self::$plugin_data['name'],
                plugin_dir_url(\dirname(__FILE__)) . 'js/OwO.min.js',
                array(),
                self::$plugin_data['version'],
                false
            );

            wp_enqueue_style(
                self::$plugin_data['name'],
                plugin_dir_url(\dirname(__FILE__)) . 'css/OwO.min.css',
                array(),
                self::$plugin_data['version'],
                'all'
            );
        }

        /**
         * 加载表情用JS
         */
        public static function load_owo_comment_js()
        {
            //输入框定位
            $target_id = 'comment';

            //拿到表情包用js地址
            $json_src = plugin_dir_url(\dirname(__FILE__)) . 'json/OwO.json';
            ?>
        <script>
            let $src = '<?php echo $json_src ?>';
            let $target = '<?php echo $target_id ?>'
            var OwO_demo = new OwO({
                logo: 'OωO表情',
                container: document.getElementsByClassName('OwO')[0],
                target: document.getElementById($target),
                api: $src,
                position: 'down',
                width: '100%',
                maxHeight: '250px'
            });

        </script>
        <?php
}

/**
 * 加载表情用文件内容
 */
        public static function load_owo_content($default)
        {
            $commenter = wp_get_current_commenter();
            $default['comment_field'] .= '<div class="OwO"></div>
        <style>
        .OwO {
            padding: 0 0 20px 0;
        }
        .OwO .OwO-body {
            position: initial!important;
        }
        </style>
        ';

            return $default;

        }

    }
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
