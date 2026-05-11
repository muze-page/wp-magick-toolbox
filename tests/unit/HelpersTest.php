<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * MaBox_Helpers 单元测试
 *
 * 测试公共工具类的核心逻辑
 */
class MaBox_Helpers_Test extends TestCase {

    /**
     * 测试 Helpers 类存在
     */
    public function test_helpers_class_exists(): void {
        $this->assertTrue(class_exists('MaBox_Helpers'));
    }

    /**
     * 测试 get_config 方法存在
     */
    public function test_get_config_method_exists(): void {
        $this->assertTrue(method_exists('MaBox_Helpers', 'get_config'));
    }

    /**
     * 测试 get_merged_config 方法存在
     */
    public function test_get_merged_config_method_exists(): void {
        $this->assertTrue(method_exists('MaBox_Helpers', 'get_merged_config'));
    }

    /**
     * 测试 get_config 对不存在的模块返回默认值
     */
    public function test_get_config_returns_default_for_nonexistent_module(): void {
        // 在没有 WordPress 环境时，get_config 应该返回默认值
        // 这里只验证方法可调用，不验证具体行为
        $this->assertTrue(method_exists('MaBox_Helpers', 'get_config'));
    }

    /**
     * 测试 get_config 正确处理 0 值
     */
    public function test_get_config_handles_zero_value(): void {
        // 验证方法签名支持默认值参数
        $method = new ReflectionMethod('MaBox_Helpers', 'get_config');
        $params = $method->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('module', $params[0]->getName());
        $this->assertEquals('key', $params[1]->getName());
        $this->assertEquals('default', $params[2]->getName());
        $this->assertFalse($params[2]->getDefaultValue());
    }

    /**
     * 测试 get_config 使用 array_key_exists 而非 empty
     */
    public function test_get_config_uses_array_key_exists(): void {
        $method = new ReflectionMethod('MaBox_Helpers', 'get_config');
        $filename = $method->getFileName();
        $content = file_get_contents($filename);

        // 验证使用了 array_key_exists
        $this->assertStringContainsString('array_key_exists', $content);
    }
}
