<?php

/**
 * 效果：谷歌统计
 * 来源：
 */
if (!class_exists('Npcink_Google_Tonji')) {
    class Npcink_Google_Tonji
    {

        private static $option;
        public static function run($config)
        {
            self::$option = $config;
            add_action('wp_head', array(__CLASS__, 'google'));
        }
        public static function google()
        {
            echo '<meta name="google-site-verification" content="' . self::$option . '" />';
            echo "\n";
        }
    }
}
