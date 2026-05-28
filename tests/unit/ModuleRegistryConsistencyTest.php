<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ModuleRegistryConsistency_Test extends TestCase {

    private static $plugin_dir;

    public static function setUpBeforeClass(): void {
        self::$plugin_dir = dirname(__DIR__, 2);
    }

    public function test_registry_module_files_exist(): void {
        $registry = MaBox_Module_Loader::get_registry();
        $partials_dir = self::$plugin_dir . '/admin/partials/';

        foreach ($registry as $module_id => $meta) {
            $file = $partials_dir . $meta['file'];
            $this->assertFileExists(
                $file,
                "Module '$module_id' file does not exist at: {$meta['file']}"
            );
        }
    }

    public function test_no_escape_module_file_exists(): void {
        $file = self::$plugin_dir . '/admin/partials/optimize/site/no_escape.php';
        $this->assertFileExists($file);
    }

    public function test_no_escape_class_exists(): void {
        require_once self::$plugin_dir . '/admin/partials/optimize/site/no_escape.php';
        $this->assertTrue(class_exists('MaBox_No_Escape'));
    }

    public function test_no_escape_implements_interface(): void {
        $this->assertTrue(
            is_subclass_of('MaBox_No_Escape', 'MaBox_Module_Interface'),
            'MaBox_No_Escape should implement MaBox_Module_Interface'
        );
    }

    public function test_no_escape_has_run_method(): void {
        $this->assertTrue(method_exists('MaBox_No_Escape', 'run'));
    }

    public function test_h5_main_removed_from_registry(): void {
        $registry = MaBox_Module_Loader::get_registry();
        $this->assertArrayNotHasKey('h5.main', $registry);
    }

    public function test_function_b2_removed_from_registry(): void {
        $registry = MaBox_Module_Loader::get_registry();
        $this->assertArrayNotHasKey('function.b2', $registry);
    }

    public function test_h5_main_removed_from_tiers(): void {
        $tiers = MaBox_Module_Loader::get_tiers();
        foreach ($tiers as $tier => $modules) {
            $this->assertNotContains('h5.main', $modules, "h5.main should not be in tier '$tier'");
        }
    }

    public function test_function_b2_removed_from_tiers(): void {
        $tiers = MaBox_Module_Loader::get_tiers();
        foreach ($tiers as $tier => $modules) {
            $this->assertNotContains('function.b2', $modules, "function.b2 should not be in tier '$tier'");
        }
    }

    public function test_h5_php_file_deleted(): void {
        $file = self::$plugin_dir . '/admin/partials/h5.php';
        $this->assertFileDoesNotExist($file);
    }

    public function test_b2_directory_deleted(): void {
        $dir = self::$plugin_dir . '/admin/partials/function/b2';
        $this->assertDirectoryDoesNotExist($dir);
    }

    public function test_jvectormap_files_deleted(): void {
        $map_dir = self::$plugin_dir . '/admin/partials/shortcode/pendant/merc_map/';
        $this->assertFileDoesNotExist($map_dir . 'jquery-jvectormap-1.2.2.min.js');
        $this->assertFileDoesNotExist($map_dir . 'jquery-jvectormap-cn-merc-en.js');
        $this->assertFileDoesNotExist($map_dir . 'jquery-jvectormap-1.2.2.css');
    }

    public function test_maintenance_deleted_templates_absent(): void {
        $maintenance_dir = self::$plugin_dir . '/admin/partials/page/function/maintenance/';
        $this->assertDirectoryDoesNotExist($maintenance_dir . 'purple');
        $this->assertDirectoryDoesNotExist($maintenance_dir . 'lighting');
        $this->assertDirectoryDoesNotExist($maintenance_dir . 'masking');
        $this->assertDirectoryDoesNotExist($maintenance_dir . 'rotate');
    }

    public function test_maintenance_kept_templates_present(): void {
        $maintenance_dir = self::$plugin_dir . '/admin/partials/page/function/maintenance/';
        $this->assertDirectoryExists($maintenance_dir . 'default');
        $this->assertFileExists($maintenance_dir . 'red.php');
    }

    public function test_merc_map_shortcode_handler_exists(): void {
        $file = self::$plugin_dir . '/admin/partials/shortcode/pendant/merc_map/index.php';
        $this->assertFileExists($file);
    }

    public function test_merc_map_implements_interface(): void {
        require_once self::$plugin_dir . '/admin/partials/shortcode/pendant/merc_map/index.php';
        $this->assertTrue(
            is_subclass_of('MaBox_ShortCode_Merc_Map', 'MaBox_Module_Interface'),
            'MaBox_ShortCode_Merc_Map should implement MaBox_Module_Interface'
        );
    }

    public function test_schema_has_no_h5_branch(): void {
        $schema = MaBox_Config_Schema::get_schema();
        $this->assertArrayNotHasKey('h5', $schema);
    }

    public function test_schema_has_no_b2_branch(): void {
        $schema = MaBox_Config_Schema::get_schema();
        $this->assertIsArray($schema['function']);
        $this->assertArrayNotHasKey('b2', $schema['function']);
    }

    public function test_config_manager_has_no_h5_mapping(): void {
        $map = MaBox_Config_Manager::get_module_map();
        $this->assertArrayNotHasKey('h5', $map);
    }

    public function test_merc_map_local_echarts_exists(): void {
        $assets_dir = self::$plugin_dir . '/admin/partials/shortcode/pendant/merc_map/assets/';
        $this->assertFileExists($assets_dir . 'echarts.min.js');
    }

    public function test_merc_map_local_china_geojson_exists(): void {
        $assets_dir = self::$plugin_dir . '/admin/partials/shortcode/pendant/merc_map/assets/';
        $this->assertFileExists($assets_dir . 'china.json');
    }

    public function test_merc_map_no_cdn_china_js_reference(): void {
        $file = self::$plugin_dir . '/admin/partials/shortcode/pendant/merc_map/index.php';
        $content = file_get_contents($file);
        $this->assertStringNotContainsString('echarts@6/map/js/china.js', $content);
        $this->assertStringNotContainsString('jquery-jvectormap', $content);
    }

    public function test_merc_map_uses_local_assets(): void {
        $file = self::$plugin_dir . '/admin/partials/shortcode/pendant/merc_map/index.php';
        $content = file_get_contents($file);
        $this->assertStringContainsString("self::\$assets_url . 'echarts.min.js'", $content);
        $this->assertStringContainsString('echarts.registerMap', $content);
    }

    public function test_merc_map_validates_coordinates(): void {
        $file = self::$plugin_dir . '/admin/partials/shortcode/pendant/merc_map/index.php';
        $content = file_get_contents($file);
        $this->assertStringContainsString('lat >= -90 && lat <= 90', $content);
        $this->assertStringContainsString('lng >= -180 && lng <= 180', $content);
    }

    public function test_no_escape_no_global_the_title_filter(): void {
        $file = self::$plugin_dir . '/admin/partials/optimize/site/no_escape.php';
        $content = file_get_contents($file);
        $this->assertStringNotContainsString("add_filter('the_title'", $content);
        $this->assertStringContainsString("add_filter('document_title_parts'", $content);
    }

    public function test_census_single_no_b2_div_id(): void {
        $file = self::$plugin_dir . '/admin/partials/function/auxiliary/census-single.php';
        $content = file_get_contents($file);
        $this->assertStringNotContainsString('MaBox_b2_shop_count', $content);
    }

    public function test_vite_count_dist_exists(): void {
        $dist_dir = self::$plugin_dir . '/vite/count/dist/';
        $this->assertFileExists($dist_dir . 'index.css');
        $this->assertFileExists($dist_dir . 'index.js');
    }
}