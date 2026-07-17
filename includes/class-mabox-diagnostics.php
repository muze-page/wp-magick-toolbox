<?php
// 如果直接访问此文件，请中止。
defined('ABSPATH') || exit;

/**
 * 站点运行诊断聚合层
 *
 * 只聚合影响插件运行的环境事实和已启用风险模块。
 * 可选功能配置与搜索健康由各自页面负责，不在此重复评价。
 *
 * 不新增持久化 option，所有数据实时计算。
 *
 * @since 2.5.0
 */
if (!class_exists('MaBox_Diagnostics')) {
    class MaBox_Diagnostics
    {
        /**
         * 获取诊断摘要
         *
         * @return array DiagnosticSummary
         */
        public static function get_summary()
        {
            $items = self::get_diagnostic_items(self::get_environment());
            $active_modules = get_option(MAGICK_TOOLBOX_ACTIVE_MODULES, array());
            if (!is_array($active_modules)) {
                $active_modules = array();
            }
            $tiers = class_exists('MaBox_Module_Loader') ? MaBox_Module_Loader::get_tiers() : array();
            $module_risks = self::get_module_risks($active_modules, $tiers);

            return array(
                'status'       => self::determine_status($module_risks, $items),
                'items'        => $items,
                'module_risks' => $module_risks,
                'generated_at' => current_time('mysql'),
            );
        }

        /**
         * 获取影响插件运行的环境信息
         *
         * @return array
         */
        private static function get_environment()
        {
            return array(
                'php_version' => PHP_VERSION,
                'wp_version'  => get_bloginfo('version'),
            );
        }

        /**
         * 生成运行环境检查项
         *
         * @param array $env
         * @return array
         */
        private static function get_diagnostic_items($env)
        {
            $php_ok = version_compare($env['php_version'], '7.4', '>=');
            $wp_main_version = preg_replace('/^(\d+\.\d+).*/', '$1', $env['wp_version']);
            $wp_ok = version_compare($wp_main_version, '6.0', '>=');

            return array(
                array(
                    'id'      => 'php_version',
                    'title'   => __('PHP 版本', 'magick-toolbox'),
                    'status'  => $php_ok ? 'good' : 'critical',
                    'message' => $php_ok
                        ? sprintf(
                            /* translators: %s: Current PHP version. */
                            __('当前 PHP 版本 %s，满足最低要求（7.4+）。', 'magick-toolbox'),
                            $env['php_version']
                        )
                        : sprintf(
                            /* translators: %s: Current PHP version. */
                            __('当前 PHP 版本 %s，低于最低要求 7.4。', 'magick-toolbox'),
                            $env['php_version']
                        ),
                ),
                array(
                    'id'      => 'wp_version',
                    'title'   => __('WordPress 版本', 'magick-toolbox'),
                    'status'  => $wp_ok ? 'good' : 'warning',
                    'message' => $wp_ok
                        ? sprintf(
                            /* translators: %s: Current WordPress version. */
                            __('当前 WordPress 版本 %s。', 'magick-toolbox'),
                            $env['wp_version']
                        )
                        : sprintf(
                            /* translators: %s: Current WordPress version. */
                            __('当前 WordPress 版本 %s，建议升级至 6.0+。', 'magick-toolbox'),
                            $env['wp_version']
                        ),
                ),
            );
        }

        /**
         * 获取已启用高风险或实验性模块
         *
         * @param array $active_modules
         * @param array $tiers
         * @return array
         */
        private static function get_module_risks($active_modules, $tiers)
        {
            $module_risks = array();
            if (empty($tiers) || empty($active_modules)) {
                return $module_risks;
            }

            $registry = class_exists('MaBox_Module_Loader') ? MaBox_Module_Loader::get_registry() : array();
            foreach (array('high_risk', 'experimental') as $tier) {
                if (!isset($tiers[$tier])) {
                    continue;
                }

                foreach (array_intersect($active_modules, $tiers[$tier]) as $module_id) {
                    $meta = isset($registry[$module_id]) ? $registry[$module_id] : null;
                    $module_risks[] = array(
                        'module_id' => $module_id,
                        'tier'      => $tier,
                        'title'     => $meta && !empty($meta['label']) ? $meta['label'] : $module_id,
                        'message'   => $tier === 'high_risk'
                            ? __('该模块被标记为高风险，可能影响站点稳定性。', 'magick-toolbox')
                            : __('该模块为实验性功能，不建议在生产环境长期开启。', 'magick-toolbox'),
                    );
                }
            }

            return $module_risks;
        }

        /**
         * 确定总体状态
         *
         * @param array $module_risks
         * @param array $items
         * @return string good|warning|critical
         */
        private static function determine_status($module_risks, $items)
        {
            foreach ($items as $item) {
                if ($item['status'] === 'critical') {
                    return 'critical';
                }
            }

            foreach ($items as $item) {
                if ($item['status'] === 'warning') {
                    return 'warning';
                }
            }

            if (empty($items) || !empty($module_risks)) {
                return 'warning';
            }

            return 'good';
        }

        /**
         * 安全获取嵌套数组值
         *
         * @param array  $data
         * @param string ...$keys
         * @return array|null
         */
        public static function get_nested($data, ...$keys)
        {
            $current = $data;
            foreach ($keys as $key) {
                if (is_array($current) && isset($current[$key])) {
                    $current = $current[$key];
                } else {
                    return null;
                }
            }
            return $current;
        }
    }
}
