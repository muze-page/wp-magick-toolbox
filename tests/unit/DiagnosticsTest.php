<?php
// 如果直接访问此文件，请中止。
defined('ABSPATH') || exit;

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . '/../../includes/class-npcink-toolbox-diagnostics.php';

/**
 * Npcink_Toolbox_Diagnostics 单元测试
 *
 * 验证诊断摘要契约、状态优先级和模块风险统计。
 *
 * @since 2.5.0
 */
class DiagnosticsTest extends TestCase {

    public function test_class_exists(): void {
        $this->assertTrue(class_exists('Npcink_Toolbox_Diagnostics'));
    }

    public function test_summary_uses_factual_contract(): void {
        $this->setWordPressState(array());

        $summary = Npcink_Toolbox_Diagnostics::get_summary();

        $this->assertSame(
            array('status', 'items', 'module_risks', 'generated_at'),
            array_keys($summary)
        );
        $this->assertSame('good', $summary['status']);
        $this->assertIsArray($summary['items']);
        $this->assertNotEmpty($summary['items']);
        $this->assertSame(array('php_version', 'wp_version'), array_column($summary['items'], 'id'));
        $this->assertSame(array('good', 'good'), array_column($summary['items'], 'status'));
        $this->assertSame(array(), $summary['module_risks']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $summary['generated_at']);
    }

    public function test_feature_status_is_read_only_and_uses_runtime_facts(): void {
        $this->setWordPressState(array());

        $status = Npcink_Toolbox_Diagnostics::get_feature_status();

        $this->assertSame(
            array('plugin', 'environment', 'counts', 'modules', 'editor_tools', 'diagnostics', 'generated_at'),
            array_keys($status)
        );
        $this->assertSame('Npcink Site Toolbox', $status['plugin']['name']);
        $this->assertSame(PHP_VERSION, $status['environment']['php_version']);
        $this->assertSame(count(Npcink_Toolbox_Module_Loader::get_registry()), $status['counts']['registered']);
        $this->assertSame(count($status['modules']), $status['counts']['active']);
        $this->assertSame(5, $status['counts']['editor_tools']);
        $this->assertCount(5, $status['editor_tools']);
        $this->assertContains('pattern', array_column($status['editor_tools'], 'type'));
        $this->assertContains('block', array_column($status['editor_tools'], 'type'));
        $this->assertContains('optimize.widgets', array_column($status['modules'], 'id'));

        $widget_index = array_search('optimize.widgets', array_column($status['modules'], 'id'), true);
        $this->assertIsInt($widget_index);
        $this->assertSame('站点小工具', $status['modules'][$widget_index]['label']);
        $this->assertTrue($status['modules'][$widget_index]['always_loaded']);
        $this->assertSame('', $status['modules'][$widget_index]['target_id']);

        foreach ($status['modules'] as $module) {
            $this->assertIsString($module['target_id']);
            $this->assertNotSame($module['id'], $module['label']);
        }

        $serialized = json_encode($status);
        $this->assertIsString($serialized);
        $this->assertStringNotContainsString('access_key', $serialized);
        $this->assertStringNotContainsString('secret_key', $serialized);
        $this->assertStringNotContainsString('site_url', $serialized);
    }

    public function test_runtime_setting_targets_open_the_specific_feature(): void {
        $method = new ReflectionMethod('Npcink_Toolbox_Diagnostics', 'get_module_target_id');
        $registry = Npcink_Toolbox_Module_Loader::get_registry();

        $this->assertSame(
            'optimize-medium-upload_auto_name',
            $method->invoke(null, $registry['optimize.image_rename'])
        );
        $this->assertSame(
            'performance-oss-enabled',
            $method->invoke(null, $registry['performance.oss'])
        );
        $this->assertSame(
            'page-function-first_picture',
            $method->invoke(null, $registry['page.first_picture'])
        );
        $this->assertSame('', $method->invoke(null, $registry['optimize.widgets']));
    }

    public function test_feature_status_replaces_the_retired_query_debug_panel(): void {
        $source = file_get_contents(dirname(__FILE__) . '/../../admin/class-npcink-toolbox-admin.php');
        $this->assertIsString($source);
        $this->assertStringNotContainsString('npcink_site_toolbox_debug', $source);
        $this->assertStringNotContainsString('render_debug_panel', $source);
        $this->assertStringContainsString("'/diagnostics/features'", $source);
    }

    public function test_removed_derived_contract_methods_do_not_exist(): void {
        $this->assertFalse(method_exists('Npcink_Toolbox_Diagnostics', 'calculate_score'));
        $this->assertFalse(method_exists('Npcink_Toolbox_Diagnostics', 'get_recommendations'));
        $this->assertFalse(method_exists('Npcink_Toolbox_Diagnostics', 'get_fix_suggestions'));
        $this->assertFalse(method_exists('Npcink_Toolbox_Diagnostics', 'get_service_hints'));
    }

    public function test_summary_ignores_invalid_active_module_option(): void {
        $this->setWordPressState(array(NPCINK_SITE_TOOLBOX_ACTIVE_MODULES => 'invalid'));

        $summary = Npcink_Toolbox_Diagnostics::get_summary();

        $this->assertSame('good', $summary['status']);
        $this->assertSame(array(), $summary['module_risks']);
    }

    public function test_diagnostic_items_only_publish_displayed_facts(): void {
        $this->setWordPressState(array());
        $items = $this->getDiagnosticItems();

        foreach ($items as $item) {
            $this->assertSame(array('id', 'title', 'status', 'message'), array_keys($item));
        }
    }

    public function test_retired_login_verification_is_absent_from_diagnostics(): void {
        $this->setWordPressState(array());
        $items = $this->getDiagnosticItems();

        $this->assertNotContains('login_security', array_column($items, 'id'));
    }

    public function test_determine_status_prioritizes_critical_items(): void {
        $status = $this->determineStatus(
            array(array('tier' => 'high_risk')),
            array(
                array('status' => 'warning'),
                array('status' => 'critical'),
                array('status' => 'good'),
            )
        );

        $this->assertSame('critical', $status);
    }

    public function test_determine_status_warning_for_visible_warning_item_without_module_risks(): void {
        $status = $this->determineStatus(
            array(),
            array(
                array('status' => 'good'),
                array('status' => 'warning'),
            )
        );

        $this->assertSame('warning', $status);
    }

    /**
     * @dataProvider moduleRiskProvider
     */
    public function test_determine_status_warning_for_module_risk(string $tier): void {
        $status = $this->determineStatus(
            array(array('tier' => $tier)),
            array(array('status' => 'good'))
        );

        $this->assertSame('warning', $status);
    }

    public function moduleRiskProvider(): array {
        return array(
            'high risk'    => array('high_risk'),
            'experimental' => array('experimental'),
        );
    }

    public function test_determine_status_good_when_every_check_passes(): void {
        $this->assertSame(
            'good',
            $this->determineStatus(array(), array(array('status' => 'good')))
        );
    }

    public function test_determine_status_warning_when_no_checks_are_available(): void {
        $this->assertSame('warning', $this->determineStatus(array(), array()));
    }

    public function test_module_risks_empty_without_active_tiered_modules(): void {
        $method = new ReflectionMethod('Npcink_Toolbox_Diagnostics', 'get_module_risks');

        $this->assertSame(array(), $method->invoke(null, array(), array()));
    }

    public function test_module_risks_only_include_active_tiered_modules(): void {
        $method = new ReflectionMethod('Npcink_Toolbox_Diagnostics', 'get_module_risks');
        $risks = $method->invoke(
            null,
            array('performance.db_clean', 'experimental.module', 'ordinary.module'),
            array(
                'high_risk'   => array('performance.db_clean', 'inactive.module'),
                'experimental' => array('experimental.module'),
            )
        );

        $this->assertSame(
            array('performance.db_clean', 'experimental.module'),
            array_column($risks, 'module_id')
        );
        $this->assertSame(array('high_risk', 'experimental'), array_column($risks, 'tier'));
        $this->assertSame(array('数据库清理优化', 'experimental.module'), array_column($risks, 'title'));
        foreach ($risks as $risk) {
            $this->assertSame(array('module_id', 'tier', 'title', 'message'), array_keys($risk));
            $this->assertNotSame('', $risk['title']);
            $this->assertNotSame('', $risk['message']);
        }
    }

    public function test_all_tiered_risk_modules_have_user_facing_labels(): void {
        $registry = Npcink_Toolbox_Module_Loader::get_registry();
        $tiers = Npcink_Toolbox_Module_Loader::get_tiers();

        foreach (array_merge($tiers['high_risk'], $tiers['experimental']) as $module_id) {
            $this->assertArrayHasKey($module_id, $registry);
            $this->assertNotEmpty($registry[$module_id]['label'], $module_id . ' should have a user-facing label');
        }
    }

    public function test_placeholder_translations_have_comments_and_ordered_multi_placeholders(): void {
        $source = file_get_contents(dirname(__FILE__) . '/../../includes/class-npcink-toolbox-diagnostics.php');
        $this->assertIsString($source);

        preg_match_all(
            '/\/\* translators: [^\r\n]+ \*\/\R\s*__\([^\r\n]*%/',
            $source,
            $documented_placeholders
        );

        $this->assertCount(4, $documented_placeholders[0]);
    }

    private function determineStatus(array $module_risks, array $items): string {
        $method = new ReflectionMethod('Npcink_Toolbox_Diagnostics', 'determine_status');

        return $method->invoke(null, $module_risks, $items);
    }

    private function getDiagnosticItems(): array {
        $method = new ReflectionMethod('Npcink_Toolbox_Diagnostics', 'get_diagnostic_items');
        $env = array(
            'php_version'        => '8.2',
            'wp_version'         => '6.9',
        );

        return $method->invoke(null, $env);
    }

    private function setWordPressState(array $options): void {
        if (!function_exists('get_bloginfo')) {
            function get_bloginfo($show = '') {
                return '6.4';
            }
        }

        $GLOBALS['_test_option_store'] = array_merge(array(
            NPCINK_SITE_TOOLBOX_ACTIVE_MODULES => array(),
        ), $options);
    }
}
