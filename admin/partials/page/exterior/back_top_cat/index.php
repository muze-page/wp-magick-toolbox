<?php

/**
 * 上吊猫
 * 来源：https://www.bber.cn/97.html#respond
 */
if (!class_exists('Npcink_Page_Back_Top_Cat')) {
    class Npcink_Page_Back_Top_Cat
    {
        public static function run()
        {
            //加载jS
            add_action('wp_enqueue_scripts', array(__CLASS__, 'load_js'));

            //添加代码
            add_action('wp_footer', array(__CLASS__, 'add_code'));
        }

        public static function add_code()
        {
            echo '
            <div class="back-to-top cd-top faa-float animated cd-is-visible" style="top: -600px;">9527</div>
            <style>  
            /*隐藏网页滚动条*/
            ::-webkit-scrollbar {
            display: none; /* 针对 Chrome、Safari、Opera */
            }
            </style>
            ';
        }
        public static function load_js()
        {
            //加载jS
            //判断下，是否在前端页中
            if (is_admin()) {
                return;
            }

            //准备数据
            $build_js =  plugin_dir_url(__DIR__) . 'back_top_cat/szgotop.js';
            $build_css =  plugin_dir_url(__DIR__) . 'back_top_cat/szgotop.css';

            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_public_back_top_cat_js',
                $build_js,
                array('jquery'),
                MAGICK_MIXTURE_VERSION,
                false
            );

            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_public_back_top_cat_css',
                $build_css,
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
        }
    }
}
