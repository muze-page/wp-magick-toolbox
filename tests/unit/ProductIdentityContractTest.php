<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ProductIdentityContractTest extends TestCase
{
    private const DISPLAY_NAME = 'Npcink Site Toolbox';
    private const SLUG = 'npcink-site-toolbox';
    private const VERSION = '3.1.0';

    public function test_main_plugin_file_defines_the_public_identity(): void
    {
        $root = $this->root();
        $main = $this->source('npcink-site-toolbox.php');

        $this->assertFileDoesNotExist($root . '/magick-tool-box.php');
        $this->assertMatchesRegularExpression('/^[ \t]*\*[ \t]*Plugin Name:[ \t]*' . preg_quote(self::DISPLAY_NAME, '/') . '$/m', $main);
        $this->assertMatchesRegularExpression('/^[ \t]*\*[ \t]*Version:[ \t]*' . preg_quote(self::VERSION, '/') . '$/m', $main);
        $this->assertMatchesRegularExpression('/^[ \t]*\*[ \t]*Text Domain:[ \t]*' . preg_quote(self::SLUG, '/') . '$/m', $main);
        $this->assertStringContainsString("define('MAGICK_MIXTURE_NAME', '" . self::SLUG . "')", $main);
        $this->assertStringContainsString("define('MAGICK_MIXTURE_VERSION', '" . self::VERSION . "')", $main);
        $this->assertStringContainsString('(new Magick_Mixture())->run();', $main);
        $this->assertStringNotContainsString('function npcink_site_toolbox_run()', $main);
        $this->assertStringContainsString('plugins.php?page=' . self::SLUG, $main);
    }

    public function test_public_protocols_use_the_product_slug(): void
    {
        $registry = $this->source('includes/class-mabox-rest-route-registry.php');
        $admin = $this->source('admin/class-magick-mixture-admin.php');
        $rate_limiter = $this->source('includes/class-magick-rate-limiter.php');

        $this->assertStringContainsString("private static \$namespace = '" . self::SLUG . "/v1'", $registry);
        $this->assertStringContainsString("'" . self::SLUG . "'", $admin);
        $this->assertStringContainsString("rest_url('" . self::SLUG . "/v1')", $admin);
        $this->assertStringContainsString("'npcink_site_toolbox_public_api'", $admin);
        $this->assertStringContainsString("get_header('x-" . self::SLUG . "-nonce')", $rate_limiter);
    }

    public function test_build_and_package_metadata_use_one_slug(): void
    {
        $composer = json_decode($this->source('composer.json'), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('npcink/site-toolbox', $composer['name']);

        $build = $this->source('bin/build-release-zip.sh');
        $verify = $this->source('bin/verify-release-zip.sh');
        $workflow = $this->source('.github/workflows/ci.yml');

        $this->assertStringContainsString('PLUGIN_SLUG="' . self::SLUG . '"', $build);
        $this->assertStringContainsString(self::SLUG . '.zip', $build);
        $this->assertStringContainsString('PLUGIN_SLUG="' . self::SLUG . '"', $verify);
        $this->assertStringContainsString('"' . self::SLUG . '.php"', $verify);
        $this->assertStringContainsString('name: ' . self::SLUG, $workflow);
        $this->assertStringContainsString(self::SLUG . '.zip', $workflow);
    }

    /**
     * @dataProvider currentRuntimeSourceProvider
     */
    public function test_current_runtime_sources_do_not_expose_retired_public_identity(string $relative_path): void
    {
        $source = $this->source($relative_path);

        foreach (array('magick-toolbox', '/mabox/v1', 'MaBox_config', 'x-mabox-nonce', 'mabox_public_api') as $retired) {
            $this->assertStringNotContainsString($retired, $source, $relative_path);
        }
    }

    /**
     * @return array<string,array{string}>
     */
    public function currentRuntimeSourceProvider(): array
    {
        return array(
            'main plugin file' => array('npcink-site-toolbox.php'),
            'admin controller' => array('admin/class-magick-mixture-admin.php'),
            'privacy surface' => array('admin/partials/privacy/index.php'),
            'REST registry' => array('includes/class-mabox-rest-route-registry.php'),
            'rate limiter' => array('includes/class-magick-rate-limiter.php'),
            'admin REST client' => array('vite/admin/src/axios/public.ts'),
            'admin data context' => array('vite/admin/src/tool/dataContext.tsx'),
        );
    }

    private function source(string $relative_path): string
    {
        $source = file_get_contents($this->root() . '/' . $relative_path);
        $this->assertIsString($source, $relative_path);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 2);
    }
}
