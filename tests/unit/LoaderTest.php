<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * MaBox_Module_Loader 单元测试
 *
 * 测试模块加载器的核心逻辑（不依赖 WordPress 环境）
 */
class MaBox_Module_Loader_Test extends TestCase {

    /**
     * 测试 Loader 类存在
     */
    public function test_loader_class_exists(): void {
        $this->assertTrue(class_exists('MaBox_Module_Loader'));
    }

    /**
     * 测试 get_registry 返回数组且包含预期模块
     */
    public function test_get_registry_returns_array_with_modules(): void {
        $registry = MaBox_Module_Loader::get_registry();

        $this->assertIsArray($registry);
        $this->assertNotEmpty($registry);

        // 验证核心模块存在
        $this->assertArrayHasKey('optimize.hide_top_toolbar', $registry);
        $this->assertArrayHasKey('seo.seo_home', $registry);
        $this->assertArrayHasKey('auxiliary.census_single', $registry);
    }

    /**
     * 测试注册表中每个模块都有必需的元数据
     */
    public function test_registry_entries_have_required_metadata(): void {
        $registry = MaBox_Module_Loader::get_registry();
        $required_keys = array('class', 'file', 'option_key', 'category', 'scope');

        foreach ($registry as $module_id => $meta) {
            foreach ($required_keys as $key) {
                $this->assertArrayHasKey($key, $meta, "Module '$module_id' should have '$key' in metadata");
            }
        }
    }

    /**
     * 测试注册表中 scope 值有效
     */
    public function test_registry_scope_values_are_valid(): void {
        $registry = MaBox_Module_Loader::get_registry();
        $valid_scopes = array('admin', 'frontend', 'both');

        foreach ($registry as $module_id => $meta) {
            if (isset($meta['scope'])) {
                $this->assertContains(
                    $meta['scope'],
                    $valid_scopes,
                    "Module '$module_id' has invalid scope: '{$meta['scope']}'"
                );
            }
        }
    }

    /**
     * 测试 get_module_meta 返回正确数据
     */
    public function test_get_module_meta_returns_correct_data(): void {
        $meta = MaBox_Module_Loader::get_module_meta('optimize.hide_top_toolbar');

        $this->assertIsArray($meta);
        $this->assertEquals('MaBox_Hide_Top_Toolbar', $meta['class']);
        $this->assertEquals('optimize/site/hide_top_toolbar.php', $meta['file']);
    }

    /**
     * 测试 get_module_meta 对不存在的模块返回 null
     */
    public function test_get_module_meta_returns_null_for_nonexistent_module(): void {
        $meta = MaBox_Module_Loader::get_module_meta('nonexistent.module');
        $this->assertNull($meta);
    }

    /**
     * 测试 get_modules_by_category 按分类过滤
     */
    public function test_get_modules_by_category_filters_correctly(): void {
        $optimize_modules = MaBox_Module_Loader::get_modules_by_category('optimize');

        $this->assertIsArray($optimize_modules);
        $this->assertNotEmpty($optimize_modules);

        foreach ($optimize_modules as $module_id => $meta) {
            $this->assertEquals('optimize', $meta['category']);
        }
    }

    /**
     * 测试 get_all_module_ids 返回所有模块 ID
     */
    public function test_get_all_module_ids_returns_all_ids(): void {
        $registry = MaBox_Module_Loader::get_registry();
        $ids = MaBox_Module_Loader::get_all_module_ids();

        $this->assertIsArray($ids);
        $this->assertCount(count($registry), $ids);
    }

    /**
     * 测试接口契约存在
     */
    public function test_module_interface_exists(): void {
        $this->assertTrue(interface_exists('MaBox_Module_Interface'));
    }

    /**
     * 测试接口定义了 run 方法
     */
    public function test_interface_defines_run_method(): void {
        $this->assertTrue(method_exists('MaBox_Module_Interface', 'run'));
    }
}
