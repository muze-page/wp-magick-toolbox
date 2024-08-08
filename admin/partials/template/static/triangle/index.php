<?php

/**
 * 静态页面模版
 */
if (!class_exists('Npcink_Template_Triangle')) {
    class Npcink_Template_Triangle
    {
        public static function run()
        {
            add_action('wp_enqueue_scripts', array(__CLASS__, 'theme_enqueue_styles'));
        }

        public static function theme_enqueue_styles()
        {

            $build_css =  plugin_dir_url(__DIR__) . 'triangle/triangle.css';
            $style_css =  plugin_dir_url(__DIR__) . 'triangle/style.css';
            // 如果当前页面模板是 template-aaa.php，则加载特定的 CSS 文件
            if (is_page_template('template-triangle.php')) {
                wp_enqueue_style(MAGICK_MIXTURE_NAME . '_triangle', $build_css, array(), MAGICK_MIXTURE_VERSION, 'all');
                wp_enqueue_style(MAGICK_MIXTURE_NAME . '_triangle-style', $style_css, array(), MAGICK_MIXTURE_VERSION, 'all');
            }
        }
    }
}
