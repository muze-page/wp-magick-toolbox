<?php
/**
 * 公共工具类
 *
 * 所有功能模块共享的公共逻辑。
 * 此类在插件初始化时必然加载，模块可安全调用。
 */

if (!class_exists('MaBox_Helpers')) {
    class MaBox_Helpers
    {
        /**
         * 获取用户真实 IP
         */
        public static function get_real_ip()
        {
            $ip = '';
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ip = trim($ips[0]);
            } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            }
            return sanitize_text_field($ip);
        }

        /**
         * 判断是否为移动端
         */
        public static function is_mobile()
        {
            return wp_is_mobile();
        }

        /**
         * 判断当前用户是否已登录
         */
        public static function is_logged_in()
        {
            return is_user_logged_in();
        }

        /**
         * 获取当前文章 ID
         */
        public static function get_current_post_id()
        {
            return get_the_ID();
        }

        /**
         * 安全获取配置值（MaBox_Admin::get_config 的快捷方式）
         */
        public static function get_config($config, $key, $default = false)
        {
            return MaBox_Admin::get_config($config, $key, $default);
        }
    }
}
