<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class OssModuleContractTest extends TestCase
{
    private static $pluginDir;

    public static function setUpBeforeClass(): void
    {
        self::$pluginDir = dirname(__DIR__, 2);

        require_once self::$pluginDir . '/includes/interface-npcink-toolbox-module.php';
        require_once self::$pluginDir . '/admin/partials/performance/oss/index.php';
    }

    public function test_oss_module_implements_module_interface(): void
    {
        $this->assertTrue(
            is_subclass_of('Npcink_Toolbox_Performance_Oss', 'Npcink_Toolbox_Module_Interface'),
            'Npcink_Toolbox_Performance_Oss should implement Npcink_Toolbox_Module_Interface'
        );
    }

    public function test_oss_module_can_run_with_default_disabled_config(): void
    {
        $this->assertNull(Npcink_Toolbox_Performance_Oss::run());
    }

    public function test_oss_syncs_the_complete_attachment_after_metadata_generation(): void
    {
        $source = file_get_contents(self::$pluginDir . '/admin/partials/performance/oss/index.php');
        $this->assertIsString($source);

        $this->assertStringContainsString(
            "add_filter('wp_generate_attachment_metadata', array(__CLASS__, 'sync_attachment_to_oss'), 20, 3)",
            $source
        );
        $this->assertStringContainsString('OFFLOADED_META', $source);
        $this->assertStringContainsString(
            'update_post_meta($attachment_id, self::OFFLOADED_META, self::target_fingerprint());',
            $source
        );
        $this->assertStringContainsString('is_offloaded_to_current_target($attachment_id)', $source);
        $this->assertStringNotContainsString("add_filter('wp_handle_upload'", $source);
        $this->assertStringNotContainsString("add_filter('wp_handle_sideload'", $source);
    }

    public function test_oss_keeps_local_files_and_removes_the_unsafe_setting(): void
    {
        $source = file_get_contents(self::$pluginDir . '/admin/partials/performance/oss/index.php');
        $schema = file_get_contents(self::$pluginDir . '/includes/class-npcink-toolbox-config-schema.php');
        $component = file_get_contents(self::$pluginDir . '/vite/admin/src/components/performance/oss.tsx');

        $this->assertIsString($source);
        $this->assertIsString($schema);
        $this->assertIsString($component);
        $this->assertStringNotContainsString('wp_delete_file(', $source);
        $this->assertStringNotContainsString("'delete_local'", $schema);
        $this->assertStringNotContainsString('delete_local', $component);
    }

    public function test_tencent_cos_v5_authorization_matches_fixed_vector(): void
    {
        $method = new ReflectionMethod(Npcink_Toolbox_Performance_Oss::class, 'build_tencent_authorization');
        $method->setAccessible(true);

        $authorization = $method->invoke(
            null,
            'PUT',
            '/folder/test%20image.png',
            'examplebucket-1250000000.cos.ap-beijing.myqcloud.com',
            'AKIDEXAMPLE',
            'secret-example',
            1557989151,
            1557996351
        );

        $this->assertSame(
            'q-sign-algorithm=sha1&q-ak=AKIDEXAMPLE&q-sign-time=1557989151;1557996351'
            . '&q-key-time=1557989151;1557996351&q-header-list=host&q-url-param-list='
            . '&q-signature=9d21e4ba7566a9434bce0e8a977f86bbf7d29755',
            $authorization
        );
    }

    public function test_object_key_path_segments_are_encoded_without_losing_directories(): void
    {
        $method = new ReflectionMethod(Npcink_Toolbox_Performance_Oss::class, 'encode_object_key');
        $method->setAccessible(true);

        $this->assertSame(
            '/2026/07/test%20image%23.png',
            $method->invoke(null, '2026/07/test image#.png')
        );
    }

    public function test_target_fingerprint_is_stable_but_changes_with_the_storage_target(): void
    {
        $method = new ReflectionMethod(Npcink_Toolbox_Performance_Oss::class, 'target_fingerprint');
        $method->setAccessible(true);

        Npcink_Toolbox_Performance_Oss::run(array(
            'enabled'  => false,
            'provider' => 'aliyun',
            'bucket'   => 'bucket-a',
            'region'   => 'cn-hangzhou',
            'domain'   => 'https://cdn.example.com/',
        ));
        $first = $method->invoke(null);

        $this->assertSame('585582f8438eb442c6108141af57289f4be1d86ef560041b8f572818d30ac519', $first);

        Npcink_Toolbox_Performance_Oss::run(array(
            'enabled'  => false,
            'provider' => 'aliyun',
            'bucket'   => 'bucket-b',
            'region'   => 'cn-hangzhou',
            'domain'   => 'https://cdn.example.com',
        ));

        $this->assertNotSame($first, $method->invoke(null));
    }
}
