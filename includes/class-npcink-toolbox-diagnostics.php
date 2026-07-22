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
if (!class_exists('Npcink_Toolbox_Diagnostics')) {
    class Npcink_Toolbox_Diagnostics
    {
        /**
         * 获取诊断摘要
         *
         * @return array DiagnosticSummary
         */
        public static function get_summary()
        {
            $items = self::get_diagnostic_items(self::get_environment());
            $active_modules = get_option(NPCINK_SITE_TOOLBOX_ACTIVE_MODULES, array());
            if (!is_array($active_modules)) {
                $active_modules = array();
            }
            if (in_array('optimize.webp_conversion', $active_modules, true)) {
                $items[] = self::get_webp_support_item();
            }
            $tiers = class_exists('Npcink_Toolbox_Module_Loader') ? Npcink_Toolbox_Module_Loader::get_tiers() : array();
            $module_risks = self::get_module_risks($active_modules, $tiers);

            return array(
                'status'       => self::determine_status($module_risks, $items),
                'items'        => $items,
                'module_risks' => $module_risks,
                'generated_at' => current_time('mysql'),
            );
        }

        /**
         * 获取用于支持与排障的只读功能状态。
         *
         * 返回值只包含运行模块元数据、环境版本和编辑器工具清单，
         * 不包含站点 URL、设置值、凭据、日志或外部探测结果。
         *
         * @return array<string,mixed>
         */
        public static function get_feature_status()
        {
            $registry = class_exists('Npcink_Toolbox_Module_Loader')
                ? Npcink_Toolbox_Module_Loader::get_registry()
                : array();
            $config = class_exists('Npcink_Toolbox_Config_Manager')
                ? Npcink_Toolbox_Config_Manager::get_merged_config()
                : array();
            $active_module_ids = class_exists('Npcink_Toolbox_Module_Loader')
                ? Npcink_Toolbox_Module_Loader::get_active_modules($config)
                : array();
            $search_labels = self::get_search_labels();
            $modules = array();
            $always_loaded = 0;

            foreach ($active_module_ids as $module_id) {
                if (!isset($registry[$module_id]) || !is_array($registry[$module_id])) {
                    continue;
                }

                $meta = $registry[$module_id];
                $is_always_loaded = !empty($meta['always_load']);
                if ($is_always_loaded) {
                    $always_loaded++;
                }

                $modules[] = array(
                    'id'            => $module_id,
                    'label'         => self::get_module_label($module_id, $meta, $search_labels),
                    'category'      => isset($meta['category']) ? (string) $meta['category'] : '',
                    'category_label'=> self::get_category_label(isset($meta['category']) ? $meta['category'] : ''),
                    'view'          => self::get_category_view(isset($meta['category']) ? $meta['category'] : ''),
                    'target_id'     => self::get_module_target_id($meta),
                    'scope'         => isset($meta['scope']) ? (string) $meta['scope'] : 'both',
                    'tier'          => class_exists('Npcink_Toolbox_Module_Loader')
                        ? Npcink_Toolbox_Module_Loader::get_module_tier($module_id)
                        : 'advanced',
                    'always_loaded' => $is_always_loaded,
                );
            }

            usort($modules, function ($left, $right) {
                $category_compare = strcmp($left['category_label'], $right['category_label']);
                if ($category_compare !== 0) {
                    return $category_compare;
                }
                return strcmp($left['label'], $right['label']);
            });

            $editor_tools = self::get_editor_tools();

            return array(
                'plugin' => array(
                    'name'    => 'Npcink Site Toolbox',
                    'version' => defined('NPCINK_SITE_TOOLBOX_VERSION')
                        ? NPCINK_SITE_TOOLBOX_VERSION
                        : '',
                ),
                'environment' => array(
                    'wordpress_version' => get_bloginfo('version'),
                    'php_version'       => PHP_VERSION,
                ),
                'counts' => array(
                    'registered'    => count($registry),
                    'active'        => count($modules),
                    'always_loaded' => $always_loaded,
                    'editor_tools'  => count($editor_tools),
                ),
                'modules'      => $modules,
                'editor_tools' => $editor_tools,
                'diagnostics'  => self::get_summary(),
                'generated_at' => current_time('mysql'),
            );
        }

        /**
         * @return array<string,string>
         */
        private static function get_search_labels()
        {
            if (!class_exists('Npcink_Toolbox_Config_Schema')) {
                return array();
            }

            $labels = array();
            foreach (Npcink_Toolbox_Config_Schema::get_admin_search_index() as $item) {
                if (!empty($item['id']) && !empty($item['label'])) {
                    $labels[$item['id']] = $item['label'];
                }
            }
            return $labels;
        }

        /**
         * @param string               $module_id
         * @param array<string,mixed>  $meta
         * @param array<string,string> $search_labels
         * @return string
         */
        private static function get_module_label($module_id, $meta, $search_labels)
        {
            if (!empty($meta['label'])) {
                return (string) $meta['label'];
            }

            $candidates = array();
            if (!empty($meta['feature_id'])) {
                $candidates[] = $meta['feature_id'];
            }
            if (!empty($meta['option_key'])) {
                $candidates[] = str_replace('.', '-', $meta['option_key']);
            }
            if (!empty($meta['activation_paths']) && is_array($meta['activation_paths'])) {
                foreach ($meta['activation_paths'] as $path) {
                    if (is_string($path)) {
                        $candidates[] = str_replace('.', '-', $path);
                    }
                }
            }

            foreach ($candidates as $candidate) {
                if (isset($search_labels[$candidate])) {
                    return $search_labels[$candidate];
                }
            }

            return $module_id;
        }

        /**
         * 获取模块在设置界面中的语义目标。
         *
         * 始终加载模块没有对应开关，不提供误导性的设置入口。其余模块优先
         * 使用 Registry 中的显式 feature_id，并兼容以配置路径生成的现有 DOM ID。
         *
         * @param array<string,mixed> $meta
         * @return string
         */
        private static function get_module_target_id($meta)
        {
            if (!empty($meta['always_load'])) {
                return '';
            }
            if (!empty($meta['feature_id']) && is_string($meta['feature_id'])) {
                return $meta['feature_id'];
            }
            if (!empty($meta['option_key']) && is_string($meta['option_key'])) {
                return str_replace('.', '-', $meta['option_key']);
            }
            return '';
        }

        /**
         * @param string $category
         * @return string
         */
        private static function get_category_label($category)
        {
            $labels = array(
                'optimize'    => __('站点与媒体', 'npcink-site-toolbox'),
                'page'        => __('内容与页面', 'npcink-site-toolbox'),
                'function'    => __('SEO 与增强', 'npcink-site-toolbox'),
                'domestic'    => __('国内生态', 'npcink-site-toolbox'),
                'performance' => __('存储与维护', 'npcink-site-toolbox'),
            );
            return isset($labels[$category]) ? $labels[$category] : __('其他', 'npcink-site-toolbox');
        }

        /**
         * @param string $category
         * @return string
         */
        private static function get_category_view($category)
        {
            $views = array(
                'optimize'    => 'site',
                'page'        => 'content',
                'function'    => 'seo',
                'domestic'    => 'china',
                'performance' => 'maintenance',
            );
            return isset($views[$category]) ? $views[$category] : '';
        }

        /**
         * @return array<int,array<string,string>>
         */
        private static function get_editor_tools()
        {
            $tools = array();

            if (class_exists('Npcink_Toolbox_Block_Patterns')) {
                foreach (Npcink_Toolbox_Block_Patterns::definitions() as $slug => $definition) {
                    $tools[] = array(
                        'id'          => 'npcink-site-toolbox/' . $slug,
                        'type'        => 'pattern',
                        'title'       => isset($definition['title']) ? (string) $definition['title'] : $slug,
                        'description' => isset($definition['description']) ? (string) $definition['description'] : '',
                    );
                }
            }

            foreach (array('site-stats', 'github-project') as $block_slug) {
                $metadata_path = dirname(__DIR__) . '/blocks/' . $block_slug . '/block.json';
                if (!is_readable($metadata_path)) {
                    continue;
                }
                $metadata_json = file_get_contents($metadata_path);
                $metadata = is_string($metadata_json) ? json_decode($metadata_json, true) : null;
                if (!is_array($metadata) || empty($metadata['name'])) {
                    continue;
                }
                $tools[] = array(
                    'id'          => (string) $metadata['name'],
                    'type'        => 'block',
                    'title'       => isset($metadata['title']) ? (string) $metadata['title'] : $block_slug,
                    'description' => isset($metadata['description']) ? (string) $metadata['description'] : '',
                );
            }

            return $tools;
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
         * 获取 WebP 图片编辑器能力事实。
         *
         * @return array<string,string>
         */
        private static function get_webp_support_item()
        {
            $supported = function_exists('wp_image_editor_supports')
                && wp_image_editor_supports(array('mime_type' => 'image/webp'));

            return array(
                'id'      => 'webp_support',
                'title'   => __('WebP 图片处理', 'npcink-site-toolbox'),
                'status'  => $supported ? 'good' : 'warning',
                'message' => $supported
                    ? __('当前 WordPress 图片编辑器支持 WebP。', 'npcink-site-toolbox')
                    : __('当前 WordPress 图片编辑器不支持 WebP；JPEG 会保持原格式。', 'npcink-site-toolbox'),
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
                    'title'   => __('PHP 版本', 'npcink-site-toolbox'),
                    'status'  => $php_ok ? 'good' : 'critical',
                    'message' => $php_ok
                        ? sprintf(
                            /* translators: %s: Current PHP version. */
                            __('当前 PHP 版本 %s，满足最低要求（7.4+）。', 'npcink-site-toolbox'),
                            $env['php_version']
                        )
                        : sprintf(
                            /* translators: %s: Current PHP version. */
                            __('当前 PHP 版本 %s，低于最低要求 7.4。', 'npcink-site-toolbox'),
                            $env['php_version']
                        ),
                ),
                array(
                    'id'      => 'wp_version',
                    'title'   => __('WordPress 版本', 'npcink-site-toolbox'),
                    'status'  => $wp_ok ? 'good' : 'warning',
                    'message' => $wp_ok
                        ? sprintf(
                            /* translators: %s: Current WordPress version. */
                            __('当前 WordPress 版本 %s。', 'npcink-site-toolbox'),
                            $env['wp_version']
                        )
                        : sprintf(
                            /* translators: %s: Current WordPress version. */
                            __('当前 WordPress 版本 %s，建议升级至 6.0+。', 'npcink-site-toolbox'),
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

            $registry = class_exists('Npcink_Toolbox_Module_Loader') ? Npcink_Toolbox_Module_Loader::get_registry() : array();
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
                            ? __('该模块被标记为高风险，可能影响站点稳定性。', 'npcink-site-toolbox')
                            : __('该模块为实验性功能，不建议在生产环境长期开启。', 'npcink-site-toolbox'),
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
