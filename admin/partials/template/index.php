<?php

/**
 * 页面模版
 */

if (!class_exists('Npcink_Template')) {
    class Npcink_Template
    {
        public static function run()
        {
            //获取设置选项值
            $config = MaBox_Admin::get_seting('template');

            /**
             * 页面模版 - 静态页面
             */
            require_once plugin_dir_path(__FILE__) . 'static/index.php';
            $static =  MaBox_Admin::get_config($config, 'static');
            Npcink_Template_Static::run($static);

           
        }
    }
}
