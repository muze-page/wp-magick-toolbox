<?php

/**
 * 效果：维护提示
 * 来源：
 */

if (!class_exists('Npcink_Maintenance_Tips')) {
    class Npcink_Maintenance_Tips
    {

        private static $configs; //配置
        private static $blogname; //博客名
        private static $blogdescription; //博客描述
        private static $url; //路径
        /**
         * 传来的页面类型
         */
        public static function run($config)
        {
            self::$configs = $config;
            self::$blogname =  get_bloginfo('name');
            self::$blogdescription = get_bloginfo('description');
            self::$url = plugin_dir_url((__FILE__)) . 'maintenance/';
            //检查是否是管理员
            add_action('template_redirect', array(__CLASS__, 'check_administrator_permission'));
        }
        public static  function check_administrator_permission()
        {
            // if (!current_user_can('edit_themes') || !is_user_logged_in()) {
            if (!current_user_can('administrator')) {
                if (self::$configs === "default") {
                    wp_die(self::$blogname . ' 升级维护中，过一会再来吧！');
                }
                if (self::$configs === "red") {
                    add_action('get_header', array(__CLASS__, 'lxtx_wp_maintenance_mode'));
                }
                if (self::$configs === "purple") {
                    //add_action('get_header', array(__CLASS__, 'lxtx_wp_maintenance_mode'));
                }
            }
        }

        public static   function lxtx_wp_maintenance_mode()
        {
            $logo = self::$url . 'image/tips.svg';
            wp_die('<div style="text-align:center">
            
            <img src="' . $logo . '" alt="' . self::$blogname . '" /><br /><br />' . self::$blogname . '正在例行维护中，请稍候...</div>', '站点维护中 - ' . self::$blogname . ' - ' . self::$blogdescription, array('response' => '503'));
        }
    }
}
