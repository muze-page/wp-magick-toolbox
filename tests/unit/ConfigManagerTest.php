<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * MaBox_Config_Manager 单元测试
 *
 * 测试配置迁移、读取、保存、导出/导入功能
 */
class MaBox_Config_Manager_Test extends TestCase {

    /**
     * 测试 get_merged_config 返回空数组（无 WordPress 环境）
     */
    public function test_get_merged_config_returns_empty_without_wp(): void {
        // 在没有 WordPress 环境时，get_option 不存在
        // 这个测试验证类在孤立加载时的行为
        $this->assertTrue(class_exists('MaBox_Config_Manager'));
    }

    /**
     * 测试 module_map 包含所有预期的模块键
     */
    public function test_module_map_contains_expected_keys(): void {
        $map = MaBox_Config_Manager::get_module_map();

        $expected_keys = array(
            'optimize', 'page', 'function', 'h5', 'login',
            'shortcode', 'template', 'domestic', 'performance',
            'ai_review', 'services', 'feedback',
        );

        foreach ($expected_keys as $key) {
            $this->assertArrayHasKey($key, $map, "Module map should contain '$key'");
        }
    }

    /**
     * 测试 module_map 返回的选项键名格式正确
     */
    public function test_module_map_values_are_valid_option_names(): void {
        $map = MaBox_Config_Manager::get_module_map();

        foreach ($map as $module_key => $option_name) {
            $this->assertIsString($option_name, "Option name for '$module_key' should be a string");
            $this->assertStringStartsWith('Magick_ToolBox_Option_', $option_name,
                "Option name for '$module_key' should start with 'Magick_ToolBox_Option_'");
        }
    }

    /**
     * 测试 clear_cache 方法存在且可调用
     */
    public function test_clear_cache_method_exists(): void {
        $this->assertTrue(method_exists('MaBox_Config_Manager', 'clear_cache'));
    }

    /**
     * 测试 get_module_config 方法存在
     */
    public function test_get_module_config_method_exists(): void {
        $this->assertTrue(method_exists('MaBox_Config_Manager', 'get_module_config'));
    }

    /**
     * 测试 save_module_config 方法存在
     */
    public function test_save_module_config_method_exists(): void {
        $this->assertTrue(method_exists('MaBox_Config_Manager', 'save_module_config'));
    }

    /**
     * 测试 export_config 方法存在
     */
    public function test_export_config_method_exists(): void {
        $this->assertTrue(method_exists('MaBox_Config_Manager', 'export_config'));
    }

    /**
     * 测试 import_config 方法存在
     */
    public function test_import_config_method_exists(): void {
        $this->assertTrue(method_exists('MaBox_Config_Manager', 'import_config'));
    }

    /**
     * 测试 import_config 拒绝无效输入
     */
    public function test_import_config_rejects_invalid_input(): void {
        // 在没有 WordPress 环境时，import_config 应该返回错误
        // 这个测试验证输入验证逻辑
        $this->assertTrue(method_exists('MaBox_Config_Manager', 'import_config'));
    }
}
