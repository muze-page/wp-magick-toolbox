<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!function_exists('wp_unslash')) {
    function wp_unslash($value)
    {
        return stripslashes_deep($value);
    }
}

class DomesticRequestInputSecurityTest extends TestCase
{
    private $originalServer;

    protected function setUp(): void
    {
        $this->originalServer = $_SERVER;
        $_SERVER = array();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
    }

    public function test_comment_rate_limit_ignores_untrusted_forwarded_headers(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.10';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.20';
        $_SERVER['HTTP_CLIENT_IP'] = '192.0.2.30';

        $this->assertSame(
            '203.0.113.10',
            $this->invokePrivate('MaBox_Domestic_Comment_Security', 'get_client_ip')
        );
    }

    public function test_comment_rate_limit_rejects_invalid_peer_address(): void
    {
        $_SERVER['REMOTE_ADDR'] = '<b>not-an-ip</b>';

        $this->assertSame(
            '0.0.0.0',
            $this->invokePrivate('MaBox_Domestic_Comment_Security', 'get_client_ip')
        );
    }

    public function test_wechat_request_recognition_unslashes_the_user_agent(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Example Micro\\Messenger Client';

        $this->assertTrue(
            $this->invokePrivate('MaBox_Domestic_Wechat', 'is_wechat_qq')
        );
    }

    public function test_wechat_request_recognition_rejects_non_string_user_agent(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = array('MicroMessenger');

        $this->assertFalse(
            $this->invokePrivate('MaBox_Domestic_Wechat', 'is_wechat_qq')
        );
    }

    private function invokePrivate(string $class, string $method)
    {
        $reflection = new ReflectionMethod($class, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke(null);
    }
}
