<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PerformanceComplianceTest extends TestCase
{
    public function test_today_user_count_uses_wordpress_user_query_date_contract(): void
    {
        $source = $this->source('includes/class-npcink-toolbox-tool.php');

        $this->assertStringContainsString('$today_users = new WP_User_Query(array(', $source);
        $this->assertStringContainsString("'date_query'  => array(", $source);
        $this->assertStringContainsString("'count_total' => true", $source);
        $this->assertStringContainsString('$today_users->get_total()', $source);
        $this->assertStringNotContainsString('$wpdb', $source);
        $this->assertStringContainsString('$total_users = get_user_count();', $source);
        $this->assertStringNotContainsString('count_users()', $source);
        $this->assertStringNotContainsString('SELECT COUNT(ID) FROM', $source);
    }

    public function test_media_health_scans_attachments_in_bounded_batches(): void
    {
        $source = $this->source('admin/partials/performance/media_health/index.php');

        $this->assertStringContainsString('const ATTACHMENT_SCAN_BATCH_SIZE = 100;', $source);
        $this->assertStringContainsString('const ATTACHMENT_SCAN_LIMIT = 500;', $source);
        $this->assertStringContainsString('private static function scan_recent_attachments()', $source);
        $this->assertStringContainsString('while ($checked < self::ATTACHMENT_SCAN_LIMIT)', $source);
        $this->assertStringContainsString("update_meta_cache('post', \$image_ids);", $source);
        $this->assertStringContainsString("'attachment_scan' => array(", $source);
        $this->assertStringNotContainsString('SELECT ID, guid', $source);
        $this->assertStringNotContainsString('SELECT ID, post_name', $source);
    }

    public function test_oss_never_deletes_the_local_media_fallback(): void
    {
        $source = $this->source('admin/partials/performance/oss/index.php');

        $this->assertStringNotContainsString('wp_delete_file(', $source);
        $this->assertSame(0, preg_match('/(?<![A-Za-z0-9_])unlink\s*\(/', $source));
    }

    private function source(string $relativePath): string
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/' . $relativePath);
        $this->assertIsString($source);

        return $source;
    }
}
