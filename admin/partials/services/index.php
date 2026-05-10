<?php
/**
 * 增值服务基础设施
 *
 * 功能：
 * - 服务展示配置（定制开发、代部署、主题适配、技术支持）
 * - 联系方式配置（微信、邮件、网站）
 * - 服务案例管理
 *
 * @since 2.3.0
 */
if (!class_exists('MaBox_Services')) {
    class MaBox_Services {

        private static $config;

        public static function run($config) {
            self::$config = $config;
            add_action('mabox_register_rest_routes', array(__CLASS__, 'register_rest_routes'));
        }

        public static function get_service_info() {
            return array(
                'wechat_qr'       => !empty(self::$config['wechat_qr']) ? self::$config['wechat_qr'] : '',
                'wechat_id'       => !empty(self::$config['wechat_id']) ? self::$config['wechat_id'] : '',
                'email'           => !empty(self::$config['email']) ? self::$config['email'] : '',
                'website'         => !empty(self::$config['website']) ? self::$config['website'] : '',
                'services'        => array(
                    'custom_dev'    => !empty(self::$config['service_custom_dev']),
                    'deployment'    => !empty(self::$config['service_deployment']),
                    'theme_adapt'   => !empty(self::$config['service_theme_adapt']),
                    'support'       => !empty(self::$config['service_support']),
                ),
                'cases'           => !empty(self::$config['cases']) ? self::$config['cases'] : array(),
            );
        }

        public static function register_rest_routes() {
            register_rest_route('mabox/v1', '/services/info', array(
                'methods'             => 'GET',
                'callback'            => array(__CLASS__, 'rest_get_info'),
                'permission_callback' => '__return_true', // 公开服务信息，用于前端展示
            ));
        }

        public static function rest_get_info() {
            return rest_ensure_response(self::get_service_info());
        }
    }
}
