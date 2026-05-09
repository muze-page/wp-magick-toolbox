<?php
/**
 * 模块加载器
 *
 * 基于注册表动态加载模块，替代硬编码的 build_active_modules_map + activate_module。
 *
 * @since 2.1.0
 */

if (!class_exists('MaBox_Module_Loader')) {
    class MaBox_Module_Loader {

        private static $registry = null;

        public static function get_registry() {
            if (self::$registry === null) {
                self::$registry = require plugin_dir_path(__FILE__) . 'modules/registry.php';
            }
            return self::$registry;
        }

        public static function get_active_modules($config) {
            $registry = self::get_registry();
            $active = array();

            foreach ($registry as $module_id => $meta) {
                if (!empty($meta['always_load'])) {
                    $active[] = $module_id;
                    continue;
                }

                if (!empty($meta['mobile_only']) && !wp_is_mobile()) {
                    continue;
                }

                if (!empty($meta['theme_requirement'])) {
                    if (!MaBox_Tool::theme_active($meta['theme_requirement'])) {
                        continue;
                    }
                }

                $option_key = $meta['option_key'];
                $value = self::get_nested_value($config, $option_key);

                if (empty($value)) {
                    continue;
                }

                if (is_string($value) && $value === 'false') {
                    continue;
                }

                $active[] = $module_id;
            }

            return $active;
        }

        public static function load_module($module_id, $config) {
            $registry = self::get_registry();

            if (!isset($registry[$module_id])) {
                return;
            }

            $meta = $registry[$module_id];
            $file = plugin_dir_path(__FILE__) . 'partials/' . $meta['file'];

            if (!file_exists($file)) {
                return;
            }

            require_once $file;

            if (!class_exists($meta['class'])) {
                return;
            }

            if (!empty($meta['config_path'])) {
                $module_config = self::get_nested_value($config, $meta['config_path']);
                call_user_func(array($meta['class'], 'run'), $module_config);
            } else {
                call_user_func(array($meta['class'], 'run'));
            }
        }

        public static function get_module_meta($module_id) {
            $registry = self::get_registry();
            return isset($registry[$module_id]) ? $registry[$module_id] : null;
        }

        public static function get_modules_by_category($category) {
            $registry = self::get_registry();
            $modules = array();

            foreach ($registry as $module_id => $meta) {
                if ($meta['category'] === $category) {
                    $modules[$module_id] = $meta;
                }
            }

            return $modules;
        }

        public static function get_all_module_ids() {
            return array_keys(self::get_registry());
        }

        private static function get_nested_value($data, $path) {
            $keys = explode('.', $path);
            $current = $data;

            foreach ($keys as $key) {
                if (is_array($current) && isset($current[$key])) {
                    $current = $current[$key];
                } elseif (is_object($current) && isset($current->$key)) {
                    $current = $current->$key;
                } else {
                    return null;
                }
            }

            return $current;
        }
    }
}
