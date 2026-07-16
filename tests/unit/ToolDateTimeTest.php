<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . '/../../includes/class-magick-mixture-tool.php';

final class ToolDateTimeTest extends TestCase
{
    protected function tearDown(): void
    {
        TestableMaBoxTool::setCurrentSiteDateTime(null);
    }

    public function test_recent_seven_days_cross_month_and_leap_day(): void
    {
        TestableMaBoxTool::setCurrentSiteDateTime('2024-03-01 12:30:45');

        $this->assertSame(
            array(
                'a' => array(
                    '2024-03-01',
                    '2024-02-29',
                    '2024-02-28',
                    '2024-02-27',
                    '2024-02-26',
                    '2024-02-25',
                    '2024-02-24',
                ),
            ),
            TestableMaBoxTool::get_time()
        );
    }

    public function test_date_range_is_inclusive_and_calendar_based(): void
    {
        $this->assertSame(
            array('2024-02-28', '2024-02-29', '2024-03-01'),
            TestableMaBoxTool::getDateFromRange('2024-02-28', '2024-03-01')
        );
        $this->assertSame(array(), TestableMaBoxTool::getDateFromRange('2024-03-02', '2024-03-01'));
        $this->assertSame(array(), TestableMaBoxTool::getDateFromRange('not-a-date', '2024-03-01'));
    }

    public function test_week_ranges_start_on_monday_even_when_today_is_sunday(): void
    {
        TestableMaBoxTool::setCurrentSiteDateTime('2026-01-04 18:00:00');

        $this->assertSame(
            array(
                '2025-12-29',
                '2025-12-30',
                '2025-12-31',
                '2026-01-01',
                '2026-01-02',
                '2026-01-03',
                '2026-01-04',
            ),
            TestableMaBoxTool::get_time_long('this_week')
        );
        $this->assertSame(
            array(
                '2025-12-22',
                '2025-12-23',
                '2025-12-24',
                '2025-12-25',
                '2025-12-26',
                '2025-12-27',
                '2025-12-28',
            ),
            TestableMaBoxTool::get_time_long('last_week')
        );
    }

    public function test_month_ranges_handle_year_boundary(): void
    {
        TestableMaBoxTool::setCurrentSiteDateTime('2026-01-04 18:00:00');

        $this->assertSame('2026-01-01', TestableMaBoxTool::get_time_long('this_month')[0]);
        $this->assertSame('2026-01-31', TestableMaBoxTool::get_time_long('this_month')[30]);
        $this->assertSame('2025-12-01', TestableMaBoxTool::get_time_long('last_month')[0]);
        $this->assertSame('2025-12-31', TestableMaBoxTool::get_time_long('last_month')[30]);
        $this->assertSame('参数错误！', TestableMaBoxTool::get_time_long('unknown'));
    }

    public function test_export_handle_time_returns_day_boundaries(): void
    {
        $this->assertSame(
            '2024-02-29 00:00:00',
            TestableMaBoxTool::export_handle_time('start', '2024-02-29')
        );
        $this->assertSame(
            '2024-02-29 23:59:59',
            TestableMaBoxTool::export_handle_time('end', '2024-02-29')
        );
        $this->assertSame('', TestableMaBoxTool::export_handle_time('unknown', '2024-02-29'));
        $this->assertSame('', TestableMaBoxTool::export_handle_time('start', 'not-a-date'));
    }

    public function test_production_source_has_no_runtime_date_or_fixed_china_offset(): void
    {
        $source = file_get_contents(dirname(__FILE__) . '/../../includes/class-magick-mixture-tool.php');
        $this->assertIsString($source);
        $this->assertSame(0, preg_match('/(?<![A-Za-z0-9_])date\s*\(/', $source));
        $this->assertStringNotContainsString('- 8 * 60 * 60', $source);
    }
}

final class TestableMaBoxTool extends MaBox_Tool
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
