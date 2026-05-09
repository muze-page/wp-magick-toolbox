<?php
/**
 * WordPress 测试配置
 *
 * 使用 WP-CLI 或手动设置 WordPress 测试环境
 */

// WordPress 测试目录路径
$wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';

// WordPress 安装路径
$wp_dir = getenv( 'WP_DIR' ) ?: '/tmp/wordpress';

// 数据库配置（测试用）
define( 'DB_NAME', 'wordpress_test' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_';

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHPUNIT__TESTS_CONFIG', __FILE__ );
