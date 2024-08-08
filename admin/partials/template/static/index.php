<?php

/**
 * 静态页面模版
 */
if (!class_exists('Npcink_Template_Static')) {
    class Npcink_Template_Static
    {
        public static function run($option)
        {
            //爱心页面
            $love = MaBox_Admin::get_config($option, 'love');
            if ($love === true) {
                add_action('wp_head', array(__CLASS__, 'add_hello_header'));
            }
        }
        public static function add_hello_header()
        {
            echo '<div style="background-color: yellow; text-align: center;">你好</div>';
        }
    } //end
}
