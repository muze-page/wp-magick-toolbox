<?php

/**
 * 效果：移除插件设置选项内容
 */
if (!class_exists('MaBox_Config_Remove_Config')) {
    class MaBox_Config_Remove_Config
    {
        public static function run()
        {
            add_action('wp_head', array(__CLASS__, 'add_hello_header'));
        }
        public static  function add_hello_header()
        {
            echo '<div style="background-color: yellow; text-align: center;">你好</div>';
        }
    }
}
