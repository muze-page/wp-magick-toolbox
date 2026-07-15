<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
}

if (!class_exists('WP_Error')) {
    class WP_Error
    {
        private $errors = array();
        private $error_data = array();

        public function __construct($code = '', $message = '', $data = array())
        {
            if ($code !== '') {
                $this->errors[$code] = array($message);
                $this->error_data[$code] = $data;
            }
        }

        public function get_error_codes()
        {
            return array_keys($this->errors);
        }

        public function get_error_code()
        {
            $codes = $this->get_error_codes();
            return isset($codes[0]) ? $codes[0] : '';
        }

        public function get_error_message($code = '')
        {
            $code = $code !== '' ? $code : $this->get_error_code();
            return isset($this->errors[$code][0]) ? $this->errors[$code][0] : '';
        }

        public function get_error_data($code = '')
        {
            $code = $code !== '' ? $code : $this->get_error_code();
            return isset($this->error_data[$code]) ? $this->error_data[$code] : null;
        }
    }
}

if (!function_exists('add_action')) {
    define('MABOX_LOGIN_SECURITY_ACTION_STUB', true);

    function add_action($hook_name, $callback, $priority = 10, $accepted_args = 1)
    {
        $GLOBALS['_test_mabox_actions'][] = array(
            'hook' => $hook_name,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        );
        return true;
    }
}

if (!function_exists('add_filter')) {
    define('MABOX_LOGIN_SECURITY_FILTER_STUB', true);

    function add_filter($hook_name, $callback, $priority = 10, $accepted_args = 1)
    {
        $GLOBALS['_test_mabox_filters'][] = array(
            'hook' => $hook_name,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        );
        return true;
    }
}

if (!function_exists('has_action')) {
    function has_action($hook_name, $callback = false)
    {
        $sources = array(
            isset($GLOBALS['_test_mabox_actions']) ? $GLOBALS['_test_mabox_actions'] : array(),
            isset($GLOBALS['_test_widgets_actions']) ? $GLOBALS['_test_widgets_actions'] : array(),
        );
        foreach ($sources as $hooks) {
            foreach ($hooks as $hook) {
                if ($hook['hook'] === $hook_name && ($callback === false || $hook['callback'] === $callback)) {
                    return $hook['priority'];
                }
            }
        }
        return false;
    }
}

if (!function_exists('has_filter')) {
    function has_filter($hook_name, $callback = false)
    {
        $hooks = isset($GLOBALS['_test_mabox_filters']) ? $GLOBALS['_test_mabox_filters'] : array();
        foreach ($hooks as $hook) {
            if ($hook['hook'] === $hook_name && ($callback === false || $hook['callback'] === $callback)) {
                return $hook['priority'];
            }
        }
        return false;
    }
}

if (!function_exists('get_user_by')) {
    function get_user_by($field, $value)
    {
        return isset($GLOBALS['_test_mabox_users'][$field][$value])
            ? $GLOBALS['_test_mabox_users'][$field][$value]
            : false;
    }
}

if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in()
    {
        return !empty($GLOBALS['_test_mabox_logged_in']);
    }
}

if (!function_exists('wp_salt')) {
    function wp_salt($scheme = 'auth')
    {
        return 'mabox-test-' . $scheme;
    }
}

if (!function_exists('wp_unslash')) {
    function wp_unslash($value)
    {
        return stripslashes_deep($value);
    }
}

require_once dirname(__FILE__) . '/../../admin/partials/domestic/login_security/index.php';

class LoginSecurityTest extends TestCase
{
    private $originalServer;
    private $originalGet;

    protected function setUp(): void
    {
        $this->originalServer = $_SERVER;
        $this->originalGet = $_GET;

        $GLOBALS['_test_mabox_actions'] = array();
        $GLOBALS['_test_mabox_filters'] = array();
        $GLOBALS['_test_widgets_actions'] = array();
        $GLOBALS['_test_transient_store'] = array();
        $GLOBALS['_test_mabox_logged_in'] = false;
        $GLOBALS['_test_mabox_users'] = array(
            'login' => array(
                'known-user' => (object) array('ID' => 42),
                'other-user' => (object) array('ID' => 84),
            ),
            'email' => array(
                'known@example.com' => (object) array('ID' => 42),
            ),
        );

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = '203.0.113.10';
        $_GET = array();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
        $_GET = $this->originalGet;
    }

    public function test_attempt_limit_registers_only_pre_hash_and_login_state_hooks(): void
    {
        MaBox_Domestic_Login_Security::run($this->attemptConfig());

        $this->assertSame(
            5,
            has_filter('wp_authenticate_user', array('MaBox_Domestic_Login_Security', 'check_attempt_lock'))
        );
        $this->assertSame(
            10,
            has_action('wp_login_failed', array('MaBox_Domestic_Login_Security', 'record_failed_login'))
        );
        $this->assertSame(
            10,
            has_action('wp_login', array('MaBox_Domestic_Login_Security', 'clear_attempt_state'))
        );
        $this->assertFalse(has_filter('authenticate', array('MaBox_Domestic_Login_Security', 'check_login_lock')));
    }

    public function test_anonymous_author_guard_registers_only_its_two_boundaries(): void
    {
        MaBox_Domestic_Login_Security::run(array('anonymous_author_guard_enabled' => true));

        $this->assertSame(
            0,
            has_action('template_redirect', array('MaBox_Domestic_Login_Security', 'guard_anonymous_author_query'))
        );
        $this->assertSame(
            10,
            has_filter('rest_endpoints', array('MaBox_Domestic_Login_Security', 'filter_anonymous_user_endpoints'))
        );
        $this->assertFalse(has_filter('wp_authenticate_user'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_emergency_constant_bypasses_only_attempt_protection(): void
    {
        define('MABOX_DISABLE_LOGIN_PROTECTION', true);

        MaBox_Domestic_Login_Security::run(array(
            'attempt_limit_enabled' => true,
            'anonymous_author_guard_enabled' => true,
        ));

        $this->assertFalse(has_filter('wp_authenticate_user'));
        $this->assertFalse(has_action('wp_login_failed'));
        $this->assertSame(
            0,
            has_action('template_redirect', array('MaBox_Domestic_Login_Security', 'guard_anonymous_author_query'))
        );
        $this->assertSame(
            10,
            has_filter('rest_endpoints', array('MaBox_Domestic_Login_Security', 'filter_anonymous_user_endpoints'))
        );

        $endpoints = array(
            '/wp/v2/users' => array('collection'),
            '/wp/v2/posts' => array('posts'),
        );
        $filtered = MaBox_Domestic_Login_Security::filter_anonymous_user_endpoints($endpoints);
        $this->assertArrayNotHasKey('/wp/v2/users', $filtered);
        $this->assertArrayHasKey('/wp/v2/posts', $filtered);

        $_GET['author'] = '42';
        $this->assertTrue($this->invokePrivate('is_anonymous_numeric_author_request'));
    }

    public function test_direct_peer_ip_ignores_untrusted_forwarded_header(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.20';
        MaBox_Domestic_Login_Security::run($this->attemptConfig());

        $this->assertSame('203.0.113.10', $this->invokePrivate('resolve_client_ip'));
    }

    public function test_trusted_proxy_chain_is_resolved_from_right_to_left(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.20, 10.0.0.2';
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'trusted_proxies' => "10.0.0.2\n10.0.0.3",
        )));

        $this->assertSame('198.51.100.20', $this->invokePrivate('resolve_client_ip'));
    }

    public function test_spoofed_leftmost_forwarded_address_does_not_override_the_nearest_untrusted_client(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.0.2.99, 198.51.100.20, 10.0.0.2';
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'trusted_proxies' => "10.0.0.2\n10.0.0.3",
        )));

        $this->assertSame('198.51.100.20', $this->invokePrivate('resolve_client_ip'));
    }

    public function test_trusted_ipv6_proxy_chain_is_normalized_and_resolved(): void
    {
        $_SERVER['REMOTE_ADDR'] = '2001:0db8:0000:0000:0000:0000:0000:0003';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = implode(', ', array(
            '2001:0db8:0000:0000:0000:0000:0000:0020',
            '2001:db8::2',
        ));
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'trusted_proxies' => "2001:db8::2\n2001:db8::3",
        )));

        $this->assertSame('2001:db8::20', $this->invokePrivate('resolve_client_ip'));
    }

    public function test_trusted_proxies_accept_exact_ips_only(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.20';
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'trusted_proxies' => '10.0.0.0/24',
        )));

        $this->assertSame('10.0.0.3', $this->invokePrivate('resolve_client_ip'));
    }

    public function test_mixed_valid_and_invalid_trusted_proxy_configuration_fails_closed_as_a_whole(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.20';
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'trusted_proxies' => "10.0.0.3\n10.0.0.0/24",
        )));

        $this->assertSame('10.0.0.3', $this->invokePrivate('resolve_client_ip'));
    }

    public function test_invalid_or_incomplete_trusted_proxy_chain_cannot_resolve_client(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.3';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.20, not-an-ip';
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'trusted_proxies' => '10.0.0.3',
        )));

        $this->assertNull($this->invokePrivate('resolve_client_ip'));

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $this->assertNull($this->invokePrivate('resolve_client_ip'));

        $_SERVER['REMOTE_ADDR'] = 'not-an-ip';
        $this->assertNull($this->invokePrivate('resolve_client_ip'));
    }

    public function test_failures_are_counted_by_existing_user_and_client_ip_in_a_fixed_window(): void
    {
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'attempt_limit_count' => 3,
        )));
        $error = new WP_Error('incorrect_password', 'Incorrect password');
        $first_key = $this->invokePrivate('counter_key', array(42, '203.0.113.10'));

        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);
        $first_state = $GLOBALS['_test_transient_store'][$first_key];
        $this->assertSame(1, $first_state['count']);
        $this->assertGreaterThanOrEqual(time() + (15 * MINUTE_IN_SECONDS) - 2, $first_state['window_expires_at']);

        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);
        $second_state = $GLOBALS['_test_transient_store'][$first_key];
        $this->assertSame(2, $second_state['count']);
        $this->assertSame($first_state['window_expires_at'], $second_state['window_expires_at']);

        $_SERVER['REMOTE_ADDR'] = '203.0.113.11';
        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);
        $other_ip_key = $this->invokePrivate('counter_key', array(42, '203.0.113.11'));
        $this->assertSame(1, $GLOBALS['_test_transient_store'][$other_ip_key]['count']);

        $_SERVER['REMOTE_ADDR'] = '203.0.113.10';
        MaBox_Domestic_Login_Security::record_failed_login('other-user', $error);
        $other_user_key = $this->invokePrivate('counter_key', array(84, '203.0.113.10'));
        $this->assertSame(1, $GLOBALS['_test_transient_store'][$other_user_key]['count']);
    }

    public function test_expired_fixed_window_starts_again_from_one(): void
    {
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'attempt_limit_count' => 3,
        )));
        $counter_key = $this->invokePrivate('counter_key', array(42, '203.0.113.10'));
        $GLOBALS['_test_transient_store'][$counter_key] = array(
            'count' => 2,
            'window_expires_at' => time() - 1,
        );

        MaBox_Domestic_Login_Security::record_failed_login(
            'known-user',
            new WP_Error('incorrect_password', 'Incorrect password')
        );

        $state = $GLOBALS['_test_transient_store'][$counter_key];
        $this->assertSame(1, $state['count']);
        $this->assertGreaterThan(time(), $state['window_expires_at']);
    }

    public function test_unknown_user_or_unresolved_ip_is_never_counted(): void
    {
        MaBox_Domestic_Login_Security::run($this->attemptConfig());
        $error = new WP_Error('invalid_username', 'Unknown user');

        MaBox_Domestic_Login_Security::record_failed_login('missing-user', $error);
        $this->assertSame(array(), $GLOBALS['_test_transient_store']);

        $_SERVER['REMOTE_ADDR'] = 'not-an-ip';
        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);
        $this->assertSame(array(), $GLOBALS['_test_transient_store']);
    }

    public function test_username_and_email_share_the_same_user_ip_counter_and_lock(): void
    {
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'attempt_limit_count' => 2,
        )));
        $error = new WP_Error('incorrect_password', 'Incorrect password');
        $counter_key = $this->invokePrivate('counter_key', array(42, '203.0.113.10'));
        $lock_key = $this->invokePrivate('lock_key', array(42, '203.0.113.10'));

        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);
        $this->assertSame(1, $GLOBALS['_test_transient_store'][$counter_key]['count']);

        MaBox_Domestic_Login_Security::record_failed_login('known@example.com', $error);
        $this->assertArrayNotHasKey($counter_key, $GLOBALS['_test_transient_store']);
        $this->assertArrayHasKey($lock_key, $GLOBALS['_test_transient_store']);
    }

    public function test_limit_creates_independent_fixed_lock_and_ignores_its_own_error(): void
    {
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array(
            'attempt_limit_count' => 2,
            'lock_duration_minutes' => 30,
        )));
        $error = new WP_Error('incorrect_password', 'Incorrect password');
        $counter_key = $this->invokePrivate('counter_key', array(42, '203.0.113.10'));
        $lock_key = $this->invokePrivate('lock_key', array(42, '203.0.113.10'));

        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);
        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);

        $this->assertArrayNotHasKey($counter_key, $GLOBALS['_test_transient_store']);
        $this->assertArrayHasKey($lock_key, $GLOBALS['_test_transient_store']);
        $lock_state = $GLOBALS['_test_transient_store'][$lock_key];
        $this->assertGreaterThanOrEqual(time() + (30 * MINUTE_IN_SECONDS) - 2, $lock_state['expires_at']);

        $locked = MaBox_Domestic_Login_Security::check_attempt_lock(
            (object) array('ID' => 42),
            'unused-password'
        );
        $this->assertInstanceOf(WP_Error::class, $locked);
        $this->assertSame(MaBox_Domestic_Login_Security::LOCK_ERROR_CODE, $locked->get_error_code());

        MaBox_Domestic_Login_Security::record_failed_login('known-user', $locked);
        $this->assertSame($lock_state, $GLOBALS['_test_transient_store'][$lock_key]);
    }

    public function test_expired_lock_is_deleted_and_no_longer_blocks_password_verification(): void
    {
        MaBox_Domestic_Login_Security::run($this->attemptConfig());
        $user = (object) array('ID' => 42);
        $lock_key = $this->invokePrivate('lock_key', array(42, '203.0.113.10'));
        $GLOBALS['_test_transient_store'][$lock_key] = array(
            'expires_at' => time() - 1,
        );

        $result = MaBox_Domestic_Login_Security::check_attempt_lock($user, 'unused-password');

        $this->assertSame($user, $result);
        $this->assertArrayNotHasKey($lock_key, $GLOBALS['_test_transient_store']);
    }

    public function test_successful_login_clears_counter_and_lock_for_the_same_user_ip_pair(): void
    {
        MaBox_Domestic_Login_Security::run($this->attemptConfig(array('attempt_limit_count' => 2)));
        $error = new WP_Error('incorrect_password', 'Incorrect password');
        $counter_key = $this->invokePrivate('counter_key', array(42, '203.0.113.10'));
        $lock_key = $this->invokePrivate('lock_key', array(42, '203.0.113.10'));

        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);
        MaBox_Domestic_Login_Security::record_failed_login('known-user', $error);
        $GLOBALS['_test_transient_store'][$counter_key] = array(
            'count' => 1,
            'window_expires_at' => time() + 100,
        );

        MaBox_Domestic_Login_Security::clear_attempt_state(
            'known-user',
            (object) array('ID' => 42)
        );

        $this->assertArrayNotHasKey($counter_key, $GLOBALS['_test_transient_store']);
        $this->assertArrayNotHasKey($lock_key, $GLOBALS['_test_transient_store']);
    }

    public function test_runtime_defaults_and_bounds_match_schema_contract(): void
    {
        MaBox_Domestic_Login_Security::run(array(
            'attempt_limit_enabled' => true,
            'attempt_limit_count' => 1,
            'attempt_window_minutes' => 0,
            'lock_duration_minutes' => 2000,
        ));
        $bounded = $this->readPrivateStaticProperty('config');
        $this->assertSame(2, $bounded['attempt_limit_count']);
        $this->assertSame(1, $bounded['attempt_window_minutes']);
        $this->assertSame(1440, $bounded['lock_duration_minutes']);

        MaBox_Domestic_Login_Security::run(array());
        $defaults = $this->readPrivateStaticProperty('config');
        $this->assertFalse($defaults['attempt_limit_enabled']);
        $this->assertSame(5, $defaults['attempt_limit_count']);
        $this->assertSame(15, $defaults['attempt_window_minutes']);
        $this->assertSame(30, $defaults['lock_duration_minutes']);
        $this->assertSame('', $defaults['trusted_proxies']);
        $this->assertFalse($defaults['anonymous_author_guard_enabled']);

        MaBox_Domestic_Login_Security::run(array(
            'attempt_limit_enabled' => true,
            'attempt_limit_count' => 2.9,
            'attempt_window_minutes' => 20.0,
            'lock_duration_minutes' => '40',
        ));
        $strict_integers = $this->readPrivateStaticProperty('config');
        $this->assertSame(5, $strict_integers['attempt_limit_count']);
        $this->assertSame(15, $strict_integers['attempt_window_minutes']);
        $this->assertSame(30, $strict_integers['lock_duration_minutes']);
    }

    public function test_anonymous_rest_guard_removes_only_core_user_routes(): void
    {
        MaBox_Domestic_Login_Security::run(array('anonymous_author_guard_enabled' => true));
        $endpoints = array(
            '/wp/v2/users' => array('collection'),
            '/wp/v2/users/(?P<id>[\\d]+)' => array('item'),
            '/wp/v2/users/me' => array('me'),
            '/wp/v2/posts' => array('posts'),
            '/custom/v1/users' => array('custom'),
        );

        $filtered = MaBox_Domestic_Login_Security::filter_anonymous_user_endpoints($endpoints);
        $this->assertArrayNotHasKey('/wp/v2/users', $filtered);
        $this->assertArrayNotHasKey('/wp/v2/users/(?P<id>[\\d]+)', $filtered);
        $this->assertArrayNotHasKey('/wp/v2/users/me', $filtered);
        $this->assertArrayHasKey('/wp/v2/posts', $filtered);
        $this->assertArrayHasKey('/custom/v1/users', $filtered);

        $GLOBALS['_test_mabox_logged_in'] = true;
        $this->assertSame($endpoints, MaBox_Domestic_Login_Security::filter_anonymous_user_endpoints($endpoints));
    }

    public function test_numeric_author_query_is_guarded_only_for_anonymous_requests(): void
    {
        MaBox_Domestic_Login_Security::run(array('anonymous_author_guard_enabled' => true));
        $_GET['author'] = '42';
        $this->assertTrue($this->invokePrivate('is_anonymous_numeric_author_request'));

        $GLOBALS['_test_mabox_logged_in'] = true;
        $this->assertFalse($this->invokePrivate('is_anonymous_numeric_author_request'));

        $GLOBALS['_test_mabox_logged_in'] = false;
        $_GET['author'] = 'not-numeric';
        $this->assertFalse($this->invokePrivate('is_anonymous_numeric_author_request'));
    }

    /**
     * @dataProvider coreNormalizedPositiveAuthorProvider
     */
    public function test_author_values_that_core_normalizes_to_a_positive_id_are_guarded($author): void
    {
        MaBox_Domestic_Login_Security::run(array('anonymous_author_guard_enabled' => true));
        $_GET['author'] = $author;

        $this->assertTrue($this->invokePrivate('is_anonymous_numeric_author_request'));
    }

    public function coreNormalizedPositiveAuthorProvider(): array
    {
        return array(
            'mixed prefix' => array('x1'),
            'encoded plus after query parsing' => array('+1'),
            'mixed suffix' => array('1foo'),
            'positive list' => array('0,2'),
        );
    }

    public function test_author_guard_ignores_values_core_cannot_turn_into_a_positive_author_id(): void
    {
        MaBox_Domestic_Login_Security::run(array('anonymous_author_guard_enabled' => true));

        foreach (array('not-numeric', '-1', '0', '', array('1')) as $author) {
            $_GET['author'] = $author;
            $this->assertFalse(
                $this->invokePrivate('is_anonymous_numeric_author_request'),
                'Only scalar values normalized by Core to a positive author ID should be guarded'
            );
        }
    }

    public function test_retired_login_behaviors_and_fields_are_absent(): void
    {
        $retired_methods = array(
            'record_ip_failure',
            'check_ip_lock',
            'custom_login_redirect',
            'filter_login_url',
            'ban_user_enumeration',
            'notify_login',
            'log_success_login',
            'log_failed_login',
            'check_ip_whitelist',
        );
        foreach ($retired_methods as $method) {
            $this->assertFalse(method_exists('MaBox_Domestic_Login_Security', $method));
        }

        $source = file_get_contents(dirname(__FILE__) . '/../../admin/partials/domestic/login_security/index.php');
        $retired_fields = array(
            'fail_limit_enabled',
            'fail_limit_count',
            'fail_lock_duration',
            'ip_lock_enabled',
            'ip_lock_count',
            'ip_lock_duration',
            'custom_login_enabled',
            'custom_login_slug',
            'ban_enumeration_enabled',
            'login_notify_enabled',
            'login_log_enabled',
            'ip_whitelist_enabled',
            'ip_whitelist',
        );
        foreach ($retired_fields as $field) {
            $this->assertStringNotContainsString($field, $source);
        }
    }

    private function attemptConfig($overrides = array()): array
    {
        return array_merge(array(
            'attempt_limit_enabled' => true,
            'attempt_limit_count' => 5,
            'attempt_window_minutes' => 15,
            'lock_duration_minutes' => 30,
            'trusted_proxies' => '',
            'anonymous_author_guard_enabled' => false,
        ), $overrides);
    }

    private function invokePrivate($method, $arguments = array())
    {
        $reflection = new ReflectionMethod('MaBox_Domestic_Login_Security', $method);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs(null, $arguments);
    }

    private function readPrivateStaticProperty($property)
    {
        $reflection = new ReflectionProperty('MaBox_Domestic_Login_Security', $property);
        $reflection->setAccessible(true);
        return $reflection->getValue();
    }
}
