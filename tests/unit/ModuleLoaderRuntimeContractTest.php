<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ModuleLoaderRuntimeContractTest extends TestCase {
    private $registry_property;
    private $original_registry;

    protected function setUp(): void {
        $plugin_dir = dirname(__DIR__, 2);
        require_once $plugin_dir . '/includes/interface-mabox-module.php';
        require_once $plugin_dir . '/tests/fixtures/modules/loader-contract-modules.php';

        $reflection = new ReflectionClass('MaBox_Module_Loader');
        $this->registry_property = $reflection->getProperty('registry');
        $this->registry_property->setAccessible(true);
        $this->original_registry = $this->registry_property->getValue(null);

        MaBox_Loader_Contract_Test_Module::$argument_count = null;
        MaBox_Loader_Contract_Test_Module::$received_config = null;
        MaBox_Loader_Non_Interface_Test_Module::$did_run = false;
    }

    protected function tearDown(): void {
        $this->registry_property->setValue(null, $this->original_registry);
    }

    public function test_loader_passes_an_empty_array_without_a_config_path(): void {
        $this->set_test_registry('MaBox_Loader_Contract_Test_Module');

        MaBox_Module_Loader::load_module('test.contract', array('ignored' => true));

        $this->assertSame(1, MaBox_Loader_Contract_Test_Module::$argument_count);
        $this->assertSame(array(), MaBox_Loader_Contract_Test_Module::$received_config);
    }

    public function test_loader_passes_the_resolved_configuration_subtree(): void {
        $this->set_test_registry('MaBox_Loader_Contract_Test_Module', 'settings.module');
        $expected = array('enabled' => true, 'mode' => 'strict');

        MaBox_Module_Loader::load_module(
            'test.contract',
            array('settings' => array('module' => $expected))
        );

        $this->assertSame(1, MaBox_Loader_Contract_Test_Module::$argument_count);
        $this->assertSame($expected, MaBox_Loader_Contract_Test_Module::$received_config);
    }

    public function test_loader_normalizes_missing_or_non_array_configuration_to_an_empty_array(): void {
        $this->set_test_registry('MaBox_Loader_Contract_Test_Module', 'settings.module');

        MaBox_Module_Loader::load_module('test.contract', array());
        $this->assertSame(array(), MaBox_Loader_Contract_Test_Module::$received_config);

        MaBox_Loader_Contract_Test_Module::$received_config = null;
        MaBox_Module_Loader::load_module(
            'test.contract',
            array('settings' => array('module' => 'not-an-array'))
        );
        $this->assertSame(array(), MaBox_Loader_Contract_Test_Module::$received_config);
    }

    public function test_loader_refuses_a_module_that_does_not_implement_the_interface(): void {
        $this->set_test_registry('MaBox_Loader_Non_Interface_Test_Module');

        MaBox_Module_Loader::load_module('test.contract', array());

        $this->assertFalse(MaBox_Loader_Non_Interface_Test_Module::$did_run);
    }

    public function test_loader_keeps_single_option_key_activation_semantics(): void {
        $this->set_test_registry('MaBox_Loader_Contract_Test_Module');

        $this->assertSame(
            array('test.contract'),
            MaBox_Module_Loader::get_active_modules(array('settings' => array('enabled' => true)))
        );
        $this->assertSame(
            array(),
            MaBox_Module_Loader::get_active_modules(array('settings' => array('enabled' => false)))
        );
        $this->assertSame(
            array(),
            MaBox_Module_Loader::get_active_modules(array('settings' => array('enabled' => 'false')))
        );
    }

    public function test_activation_paths_use_explicit_any_of_semantics_without_duplicates(): void {
        $this->set_test_registry(
            'MaBox_Loader_Contract_Test_Module',
            null,
            array(
                'option_key' => 'settings.primary_enabled',
                'activation_paths' => array(
                    'settings.primary_enabled',
                    'settings.secondary_enabled',
                ),
            )
        );

        $this->assertSame(
            array(),
            MaBox_Module_Loader::get_active_modules(array(
                'settings' => array(
                    'primary_enabled' => false,
                    'secondary_enabled' => false,
                    'non_triggering_value' => 'configured',
                ),
            ))
        );
        $this->assertSame(
            array('test.contract'),
            MaBox_Module_Loader::get_active_modules(array(
                'settings' => array(
                    'primary_enabled' => false,
                    'secondary_enabled' => true,
                ),
            ))
        );
        $this->assertSame(
            array('test.contract'),
            MaBox_Module_Loader::get_active_modules(array(
                'settings' => array(
                    'primary_enabled' => true,
                    'secondary_enabled' => true,
                ),
            ))
        );
    }

    public function test_invalid_activation_paths_fail_closed_without_option_key_fallback(): void {
        $invalid_contracts = array(
            array(),
            array('settings.secondary_enabled'),
            array('settings.primary_enabled', 'settings.primary_enabled'),
            array('primary' => 'settings.primary_enabled'),
            array('settings.primary_enabled', array('settings.secondary_enabled')),
            array('settings.primary_enabled', 'settings..secondary_enabled'),
        );

        foreach ($invalid_contracts as $activation_paths) {
            $this->set_test_registry(
                'MaBox_Loader_Contract_Test_Module',
                null,
                array(
                    'option_key' => 'settings.primary_enabled',
                    'activation_paths' => $activation_paths,
                )
            );

            $this->assertSame(
                array(),
                MaBox_Module_Loader::get_active_modules(array(
                    'settings' => array(
                        'primary_enabled' => true,
                        'secondary_enabled' => true,
                    ),
                )),
                'Malformed activation_paths must not silently fall back to option_key'
            );
        }
    }

    private function set_test_registry($class, $config_path = null, $extra_meta = array()): void {
        $meta = array(
            'class'      => $class,
            'file'       => '../../tests/fixtures/modules/loader-contract-modules.php',
            'option_key' => 'settings.enabled',
        );

        if ($config_path !== null) {
            $meta['config_path'] = $config_path;
        }

        $meta = array_merge($meta, $extra_meta);

        $this->registry_property->setValue(null, array('test.contract' => $meta));
    }
}
