<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SmallDateApiComplianceTest extends TestCase
{
    public function test_compliance_footer_uses_wordpress_site_time(): void
    {
        $source = $this->readProductionFile('admin/partials/domestic/compliance/index.php');

        $this->assertStringContainsString("wp_date('Y')", $source);
        $this->assertSame(0, preg_match('/(?<![A-Za-z0-9_])date\s*\(/', $source));
    }

    public function test_image_rename_uses_wordpress_site_time_and_random_api(): void
    {
        $source = $this->readProductionFile('admin/partials/optimize/medium/image_rename.php');

        $this->assertStringContainsString("wp_date('YmdHis')", $source);
        $this->assertStringContainsString('wp_rand(10, 99)', $source);
        $this->assertSame(0, preg_match('/(?<![A-Za-z0-9_])date\s*\(/', $source));
        $this->assertSame(0, preg_match('/(?<![A-Za-z0-9_])rand\s*\(/', $source));
    }

    private function readProductionFile(string $relativePath): string
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/' . $relativePath);
        $this->assertIsString($source);

        return $source;
    }
}
