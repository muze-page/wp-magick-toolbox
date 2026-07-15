<?php

/**
 * 效果：必应统计
 * 来源：
 */
if (!class_exists('MaBox_Biying_Tonji')) {
    class MaBox_Biying_Tonji implements MaBox_Module_Interface
    {
        private static $option;
        public static function run($config = array())
        {
            self::$option = isset($config['biying_tonji']) && is_string($config['biying_tonji'])
                ? $config['biying_tonji']
                : '';
            add_action('wp_head', array(__CLASS__, 'meta_tag'));
        }
        public static function meta_tag()
        {
            if (!empty(self::$option)) {
                $option = esc_attr(self::$option);
                echo '<meta name="msvalidate.01" content="' . $option . '" />' . "\n";
            }
        }
    }
}
