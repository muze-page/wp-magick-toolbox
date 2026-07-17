<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!class_exists('WP_Query')) {
    class WP_Query
    {
        public static $captured_queries = array();
        public $found_posts;

        public function __construct($args = array())
        {
            self::$captured_queries[] = $args;
            $this->found_posts = isset($GLOBALS['_test_wp_query_found_posts'])
                ? (int) $GLOBALS['_test_wp_query_found_posts']
                : 0;
        }
    }
}

if (!function_exists('wp_count_posts')) {
    function wp_count_posts($type = 'post', $perm = '')
    {
        $GLOBALS['_test_wp_count_posts_type'] = $type;

        return isset($GLOBALS['_test_wp_count_posts_result'])
            ? $GLOBALS['_test_wp_count_posts_result']
            : (object) array();
    }
}

require_once dirname(__FILE__) . '/../../includes/class-magick-mixture-tool.php';

final class ToolPostCountTest extends TestCase
{
    protected function setUp(): void
    {
        if (!property_exists('WP_Query', 'captured_queries')) {
            $this->markTestSkipped('The query capture stub is only used by the pure unit-test suite.');
        }

        WP_Query::$captured_queries = array();
        $GLOBALS['_test_wp_query_found_posts'] = 17;
        $GLOBALS['_test_wp_count_posts_result'] = (object) array(
            'publish' => 23,
            'draft' => 5,
        );
        unset($GLOBALS['_test_wp_count_posts_type']);
        PostCountMaBoxTool::setCurrentSiteDateTime('2026-01-04 18:00:00');
    }

    protected function tearDown(): void
    {
        PostCountMaBoxTool::setCurrentSiteDateTime(null);
        unset(
            $GLOBALS['_test_wp_query_found_posts'],
            $GLOBALS['_test_wp_count_posts_result'],
            $GLOBALS['_test_wp_count_posts_type']
        );
    }

    public function test_calendar_ranges_use_bounded_count_queries(): void
    {
        $this->assertSame(17, PostCountMaBoxTool::get_total_release_amount('today'));
        $this->assertSame(17, PostCountMaBoxTool::get_total_release_amount('week'));
        $this->assertSame(17, PostCountMaBoxTool::get_total_release_amount('month'));
        $this->assertSame(17, PostCountMaBoxTool::get_total_release_amount('year'));

        $queries = WP_Query::$captured_queries;
        $this->assertCount(4, $queries);
        $this->assertSame(
            array('year' => 2026, 'month' => 1, 'day' => 4),
            $queries[0]['date_query'][0]
        );
        $this->assertSame(
            array('after' => '2025-12-29', 'before' => '2026-01-04', 'inclusive' => true),
            $queries[1]['date_query'][0]
        );
        $this->assertSame(array('year' => 2026, 'month' => 1), $queries[2]['date_query'][0]);
        $this->assertSame(array('year' => 2026), $queries[3]['date_query'][0]);

        foreach ($queries as $query) {
            $this->assertSame('ids', $query['fields']);
            $this->assertSame(1, $query['posts_per_page']);
            $this->assertTrue($query['ignore_sticky_posts']);
            $this->assertArrayNotHasKey('post__not_in', $query);
            $this->assertArrayNotHasKey('no_found_rows', $query);
            $this->assertArrayNotHasKey('suppress_filters', $query);
        }
    }

    public function test_total_count_respects_requested_post_type_and_status(): void
    {
        $this->assertSame(5, PostCountMaBoxTool::get_total_release_amount('total', 'page', 'draft'));
        $this->assertSame('page', $GLOBALS['_test_wp_count_posts_type']);
        $this->assertSame(0, PostCountMaBoxTool::get_total_release_amount('total', 'page', 'private'));
    }
}

final class PostCountMaBoxTool extends MaBox_Tool
{
    private static $currentSiteDateTime;

    public static function setCurrentSiteDateTime(?string $dateTime): void
    {
        self::$currentSiteDateTime = $dateTime === null ? null : new DateTimeImmutable($dateTime);
    }

    protected static function current_site_datetime()
    {
        return self::$currentSiteDateTime ?? parent::current_site_datetime();
    }
}
