<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!defined('MAGICK_MIXTURE_VERSION')) {
    define('MAGICK_MIXTURE_VERSION', '3.2.0');
}
if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}
if (!function_exists('esc_url')) {
    function esc_url($url)
    {
        return filter_var((string) $url, FILTER_SANITIZE_URL);
    }
}
if (!function_exists('esc_html')) {
    function esc_html($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('wp_date')) {
    function wp_date($format)
    {
        return $format === 'Y' ? '2026' : '';
    }
}
if (!function_exists('get_bloginfo')) {
    function get_bloginfo($show = '')
    {
        if ($show === 'name') {
            return 'Test Site';
        }
        if ($show === 'version') {
            return '6.4';
        }
        return '';
    }
}
if (!function_exists('wp_register_style')) {
    function wp_register_style($handle, $src, $deps = array(), $version = false, $media = 'all')
    {
        return true;
    }
}
if (!function_exists('wp_add_inline_style')) {
    function wp_add_inline_style($handle, $data)
    {
        $GLOBALS['_test_inline_styles'][$handle][] = $data;
        return true;
    }
}
if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle)
    {
        return true;
    }
}
if (!function_exists('wp_register_script')) {
    function wp_register_script($handle, $src, $deps = array(), $version = false, $args = array())
    {
        return true;
    }
}
if (!function_exists('wp_add_inline_script')) {
    function wp_add_inline_script($handle, $data, $position = 'after')
    {
        $GLOBALS['_test_inline_scripts'][$handle][] = $data;
        return true;
    }
}
if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle)
    {
        return true;
    }
}
if (!function_exists('wp_json_encode')) {
    function wp_json_encode($value, $flags = 0, $depth = 512)
    {
        return json_encode($value, $flags, $depth);
    }
}
if (!function_exists('is_ssl')) {
    function is_ssl()
    {
        return !empty($GLOBALS['_test_is_ssl']);
    }
}

require_once dirname(__DIR__, 2) . '/includes/interface-mabox-module.php';
require_once dirname(__DIR__, 2) . '/admin/partials/domestic/compliance/index.php';

final class DomesticComplianceRuntimeTest extends TestCase
{
    private $cookies;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cookies = $_COOKIE;
        $_COOKIE = array();
        $GLOBALS['_test_inline_scripts'] = array();
        $GLOBALS['_test_inline_styles'] = array();
        $GLOBALS['_test_is_ssl'] = false;
    }

    protected function tearDown(): void
    {
        $_COOKIE = $this->cookies;
        parent::tearDown();
    }

    public function test_icp_footer_uses_the_configured_query_link(): void
    {
        $this->setConfig(array(
            'icp_enabled' => true,
            'icp_number' => '京ICP备12345678号',
            'icp_link' => 'https://beian.miit.gov.cn/',
        ));

        ob_start();
        MaBox_Domestic_Compliance::render_footer();
        $output = (string) ob_get_clean();

        $this->assertStringContainsString('href="https://beian.miit.gov.cn/"', $output);
        $this->assertStringContainsString('rel="nofollow noopener"', $output);
        $this->assertStringContainsString('京ICP备12345678号', $output);
    }

    public function test_cookie_banner_uses_configured_content_without_inner_html(): void
    {
        $this->setConfig(array(
            'cookie_enabled' => true,
            'cookie_style' => 'center',
            'cookie_title' => '隐私提示',
            'cookie_content' => '站点使用 Cookie 改善体验。',
            'cookie_button' => '同意',
        ));

        MaBox_Domestic_Compliance::enqueue_cookie_assets();

        $script = $GLOBALS['_test_inline_scripts']['mabox-cookie-script'][0] ?? '';
        $style = $GLOBALS['_test_inline_styles']['mabox-cookie-style'][0] ?? '';
        $payload = $this->extractPayload($script);
        $this->assertSame('站点使用 Cookie 改善体验。', $payload['content']);
        $this->assertSame('隐私提示', $payload['title']);
        $this->assertSame('同意', $payload['button']);
        $this->assertStringContainsString('textContent', $script);
        $this->assertStringContainsString("addEventListener('click'", $script);
        $this->assertStringNotContainsString('innerHTML', $script);
        $this->assertStringContainsString('SameSite=Lax', $script);
        $this->assertStringContainsString('transform:translate(-50%,-50%)', $style);
    }

    public function test_cookie_banner_marks_the_cookie_secure_on_https(): void
    {
        $GLOBALS['_test_is_ssl'] = true;
        $this->setConfig(array('cookie_enabled' => true));

        MaBox_Domestic_Compliance::enqueue_cookie_assets();

        $script = $GLOBALS['_test_inline_scripts']['mabox-cookie-script'][0] ?? '';
        $payload = $this->extractPayload($script);
        $this->assertStringContainsString('SameSite=Lax; Secure', $payload['cookie']);
        $this->assertSame('本网站使用 Cookie 来改善您的体验。', $payload['content']);
    }

    /**
     * @return array<string,string>
     */
    private function extractPayload(string $script): array
    {
        $matched = preg_match('/const c=(\{.*?\});const b=/', $script, $matches);
        $this->assertSame(1, $matched, $script);
        $payload = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($payload);

        return $payload;
    }

    private function setConfig(array $config): void
    {
        $reflection = new ReflectionProperty('MaBox_Domestic_Compliance', 'config');
        $reflection->setAccessible(true);
        $reflection->setValue(null, $config);
    }
}
