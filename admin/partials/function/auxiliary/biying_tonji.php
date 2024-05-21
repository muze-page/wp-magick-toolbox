<?php

/**
 * 效果：必应统计
 * 来源：
 */
if (!class_exists('Npcink_Biying_Tonji')) {
    class Npcink_Biying_Tonji
    {
        private static $option;
        public static function run($config)
        {
            self::$option = $config;
            add_action('wp_head', array(__CLASS__, 'biying'));
        }
        public static function biying()
        {
            echo '<meta name="msvalidate.01" content="' . self::$option . '" />';
            echo "\n";
        }
    }
}
