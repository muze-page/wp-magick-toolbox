<?php
/**
 * 面向公共的
 */
if (!class_exists('Magick_Mixtrue_Public')) {
    class Magick_Mixtrue_Public
    {
        public function __construct()
        {
            echo "666";
        }
        public static function runs()
        {
            //add_action('wp_body_open', array(__CLASS__, 'add_page_particle'));
            //add_action('wp_enqueue_scripts', array(__CLASS__, 'add_page_particle_js'));
        }
        /**
         * 效果：页面添加烟花粒子
         * 来源：https://www.iowen.cn/canvas-click-effect-second-edition/
         */

        //添加文件
        public static function add_page_particle()
        {
            return "666";

        }
        //加载js
        public static function add_page_particle_js()
        {
            wp_enqueue_script(
                MAGICK_MIXTURE_NAME,
                plugin_dir_url(\dirname(__FILE__)) . 'js/style-click-particle.js',
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
        }
    }
}
