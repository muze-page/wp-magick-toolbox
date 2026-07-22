<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!function_exists('add_filter')) {
    function add_filter($hook_name, $callback, $priority = 10, $accepted_args = 1)
    {
        $GLOBALS['_test_mabox_filters'][] = array(
            'hook'          => $hook_name,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );
        return true;
    }
}

if (!function_exists('wp_image_editor_supports')) {
    function wp_image_editor_supports($args = array())
    {
        $GLOBALS['_test_webp_support_args'][] = $args;
        return !empty($GLOBALS['_test_webp_supported']);
    }
}

require_once dirname(__DIR__, 2) . '/includes/interface-npcink-toolbox-module.php';
require_once dirname(__DIR__, 2) . '/admin/partials/optimize/medium/webp_conversion.php';
require_once dirname(__DIR__, 2) . '/includes/class-npcink-toolbox-diagnostics.php';

final class WebpConversionTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['_test_mabox_filters'] = array();
        $GLOBALS['_test_webp_support_args'] = array();
        $GLOBALS['_test_webp_supported'] = false;
        $GLOBALS['_test_option_store'][NPCINK_SITE_TOOLBOX_ACTIVE_MODULES] = array();
    }

    public function test_disabled_module_does_not_register_a_format_filter(): void
    {
        $GLOBALS['_test_webp_supported'] = true;

        Npcink_Toolbox_Medium_Webp_Conversion::run(array('webp_conversion' => false));

        $this->assertSame(array(), $GLOBALS['_test_mabox_filters']);
        $this->assertSame(array(), $GLOBALS['_test_webp_support_args']);
    }

    public function test_unsupported_editor_fails_closed_without_changing_uploads(): void
    {
        Npcink_Toolbox_Medium_Webp_Conversion::run(array('webp_conversion' => true));

        $this->assertSame(array(array('mime_type' => 'image/webp')), $GLOBALS['_test_webp_support_args']);
        $this->assertSame(array(), $GLOBALS['_test_mabox_filters']);
    }

    public function test_supported_editor_registers_the_core_output_format_filter(): void
    {
        $GLOBALS['_test_webp_supported'] = true;

        Npcink_Toolbox_Medium_Webp_Conversion::run(array('webp_conversion' => true));

        $this->assertCount(1, $GLOBALS['_test_mabox_filters']);
        $this->assertSame('image_editor_output_format', $GLOBALS['_test_mabox_filters'][0]['hook']);
        $this->assertSame(
            array('Npcink_Toolbox_Medium_Webp_Conversion', 'map_jpeg_to_webp'),
            $GLOBALS['_test_mabox_filters'][0]['callback']
        );
        $this->assertSame(10, $GLOBALS['_test_mabox_filters'][0]['priority']);
        $this->assertSame(3, $GLOBALS['_test_mabox_filters'][0]['accepted_args']);
    }

    public function test_mapping_preserves_core_and_third_party_formats_and_only_adds_jpeg(): void
    {
        $mapped = Npcink_Toolbox_Medium_Webp_Conversion::map_jpeg_to_webp(
            array(
                'image/heic' => 'image/jpeg',
                'image/png'  => 'image/avif',
            ),
            '/uploads/example.jpg',
            'image/jpeg'
        );

        $this->assertSame('image/jpeg', $mapped['image/heic']);
        $this->assertSame('image/avif', $mapped['image/png']);
        $this->assertSame('image/webp', $mapped['image/jpeg']);
        $this->assertCount(3, $mapped);
    }

    public function test_module_uses_no_destructive_upload_hooks_or_file_deletion(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 2) . '/admin/partials/optimize/medium/webp_conversion.php'
        );
        $this->assertIsString($source);

        foreach (array('pre_move_uploaded_file', 'sanitize_file_name', 'wp_handle_upload', 'unlink(') as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $source);
        }
        $this->assertStringContainsString("'image_editor_output_format'", $source);
        $this->assertStringContainsString("'image/jpeg'] = 'image/webp'", $source);
        $this->assertStringNotContainsString("'image/png'] = 'image/webp'", $source);
    }

    public function test_registry_and_schema_keep_the_feature_opt_in_and_rest_safe(): void
    {
        $registry = Npcink_Toolbox_Module_Loader::get_registry();
        $schema = Npcink_Toolbox_Config_Schema::get_schema();
        $defaults = Npcink_Toolbox_Config_Schema::get_defaults();
        $meta = $registry['optimize.webp_conversion'];

        $this->assertSame('optimize.medium.webp_conversion', $meta['option_key']);
        $this->assertSame('optimize.medium', $meta['config_path']);
        $this->assertSame('both', $meta['scope']);
        $this->assertSame('boolean', $schema['optimize']['medium']['webp_conversion']['type']);
        $this->assertFalse($defaults['optimize']['medium']['webp_conversion']);
    }

    public function test_runtime_diagnostics_report_supported_and_unsupported_editors_factually(): void
    {
        $GLOBALS['_test_option_store'][NPCINK_SITE_TOOLBOX_ACTIVE_MODULES] = array(
            'optimize.webp_conversion',
        );

        $unsupported = Npcink_Toolbox_Diagnostics::get_summary();
        $unsupported_index = array_search('webp_support', array_column($unsupported['items'], 'id'), true);
        $this->assertIsInt($unsupported_index);
        $this->assertSame('warning', $unsupported['items'][$unsupported_index]['status']);
        $this->assertStringContainsString('保持原格式', $unsupported['items'][$unsupported_index]['message']);

        $GLOBALS['_test_webp_supported'] = true;
        $supported = Npcink_Toolbox_Diagnostics::get_summary();
        $supported_index = array_search('webp_support', array_column($supported['items'], 'id'), true);
        $this->assertIsInt($supported_index);
        $this->assertSame('good', $supported['items'][$supported_index]['status']);
        $this->assertStringContainsString('支持 WebP', $supported['items'][$supported_index]['message']);
    }

    public function test_admin_bootstrap_exposes_only_the_boolean_support_fact(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/admin/class-npcink-toolbox-admin.php');
        $this->assertIsString($source);
        $this->assertStringContainsString("'webpSupported'", $source);
        $this->assertStringContainsString("array('mime_type' => 'image/webp')", $source);
    }
}
