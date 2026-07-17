<?php
// 如果直接访问此文件，请中止。
defined('ABSPATH') || exit;
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
            // Forwarded headers are client-controlled unless a trusted proxy boundary exists.
            if (!isset($_SERVER['REMOTE_ADDR']) || !is_string($_SERVER['REMOTE_ADDR'])) {
                return '';
            }

            $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
            return filter_var($ip, FILTER_VALIDATE_IP) !== false ? $ip : '';
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
         * 安全获取配置值（直接读取 Config_Manager，不依赖 MaBox_Admin）
         */
        public static function get_config($module, $key, $default = false)
        {
            $module_config = MaBox_Config_Manager::get_module_config($module);
            if (is_array($module_config) && array_key_exists($key, $module_config)) {
                return $module_config[$key];
            }
            return $default;
        }

        /**
         * 获取完整合并配置
         */
        public static function get_merged_config()
        {
            return MaBox_Config_Manager::get_merged_config();
        }
    }
}
