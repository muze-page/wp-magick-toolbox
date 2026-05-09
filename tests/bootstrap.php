<?php
/**
 * PHPUnit Bootstrap
 *
 * 初始化 WordPress 测试环境
 */

// 检查 WordPress 测试套件是否可用
if ( ! defined( 'WP_TESTS_DIR' ) ) {
    $wp_tests_dir = getenv( 'WP_TESTS_DIR' );
    if ( ! $wp_tests_dir ) {
        // 尝试在常见位置查找
        $possible_paths = array(
            '/tmp/wordpress-tests-lib',
            getenv( 'HOME' ) . '/wordpress-tests-lib',
        );
        foreach ( $possible_paths as $path ) {
            if ( file_exists( $path . '/includes/functions.php' ) ) {
                $wp_tests_dir = $path;
                break;
            }
        }
    }
}

if ( $wp_tests_dir && file_exists( $wp_tests_dir . '/includes/functions.php' ) ) {
    require_once $wp_tests_dir . '/includes/functions.php';

    function _manually_load_plugin() {
        require dirname( __FILE__ ) . '/magick-tool-box.php';
    }
    tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

    require $wp_tests_dir . '/includes/bootstrap.php';
} else {
    // 如果没有 WordPress 测试环境，提供基本加载
    if ( ! class_exists( 'WP_UnitTestCase' ) ) {
        class WP_UnitTestCase extends PHPUnit\Framework\TestCase {
            // 基本测试用例类
        }
    }
}

// 加载插件核心类
if ( file_exists( dirname( __FILE__ ) . '/includes/class-magick-helpers.php' ) ) {
    require_once dirname( __FILE__ ) . '/includes/class-magick-helpers.php';
}
if ( file_exists( dirname( __FILE__ ) . '/includes/class-magick-config-manager.php' ) ) {
    require_once dirname( __FILE__ ) . '/includes/class-magick-config-manager.php';
}
