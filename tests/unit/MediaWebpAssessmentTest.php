<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class MediaWebpAssessmentTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $pluginDir = dirname(__DIR__, 2);
        require_once $pluginDir . '/includes/interface-npcink-toolbox-module.php';
        require_once $pluginDir . '/admin/partials/performance/media_health/index.php';
    }

    public function test_file_extension_is_authoritative_when_classifying_converted_images(): void
    {
        $method = new ReflectionMethod(Npcink_Toolbox_Performance_Media_Health::class, 'classify_image_format');
        $method->setAccessible(true);

        $this->assertSame('webp', $method->invoke(null, '/uploads/image.webp', 'image/jpeg'));
        $this->assertSame('jpeg', $method->invoke(null, '/uploads/image.jpg', 'image/webp'));
        $this->assertSame('png', $method->invoke(null, '/uploads/image.png', 'image/jpeg'));
        $this->assertSame('other', $method->invoke(null, '/uploads/image.svg', 'image/svg+xml'));
    }

    public function test_continuous_queue_is_bounded_without_expanding_each_server_batch(): void
    {
        $this->assertSame(50, Npcink_Toolbox_Performance_Media_Health::WEBP_CONTINUOUS_MAX_CANDIDATES);
        $this->assertSame(5, Npcink_Toolbox_Webp_Batch::MAX_BATCH_SIZE);

        $source = file_get_contents(
            dirname(__DIR__, 2) . '/admin/partials/performance/media_health/index.php'
        );
        $this->assertIsString($source);
        $this->assertSame(2, substr_count(
            $source,
            'count($restorable_ids) < self::WEBP_CONTINUOUS_MAX_CANDIDATES'
        ) + substr_count(
            $source,
            'count($batch_candidate_ids) < self::WEBP_CONTINUOUS_MAX_CANDIDATES'
        ));
    }

    /**
     * @dataProvider recommendationProvider
     */
    public function test_recommendation_requires_support_cleanup_sample_value_and_scale(
        bool $supported,
        int $candidateCount,
        int $candidateBytes,
        array $sample,
        string $expected
    ): void {
        $method = new ReflectionMethod(Npcink_Toolbox_Performance_Media_Health::class, 'get_webp_recommendation');
        $method->setAccessible(true);

        $this->assertSame(
            $expected,
            $method->invoke(null, $supported, $candidateCount, $candidateBytes, $sample)
        );
    }

    public static function recommendationProvider(): array
    {
        $good = array(
            'successful'              => 3,
            'savings_percent'         => 25.0,
            'temporary_files_cleaned' => true,
        );

        return array(
            'unsupported'        => array(false, 100, 300000000, $good, 'unsupported'),
            'no candidates'      => array(true, 0, 0, $good, 'no_candidates'),
            'cleanup failed'     => array(true, 100, 300000000, array_merge($good, array('temporary_files_cleaned' => false)), 'cleanup_failed'),
            'insufficient sample'=> array(true, 10, 10000000, array_merge($good, array('successful' => 2)), 'insufficient_sample'),
            'low savings'        => array(true, 10, 10000000, array_merge($good, array('savings_percent' => 5.0)), 'low_savings'),
            'below scale'        => array(true, 10, 10000000, $good, 'below_scale'),
            'consider batch'     => array(true, 100, 300000000, $good, 'consider_batch'),
        );
    }
}
