<?php
/**
 * PHPUnit Bootstrap
 *
 * 初始化 WordPress 测试环境
 */

// 定义 ABSPATH，防止测试文件中的访问守卫直接退出
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

// 定义插件常量（供纯单元测试使用）
if ( ! defined( 'MAGICK_MIXTURE_OPTION' ) ) {
	define( 'MAGICK_MIXTURE_OPTION', 'Magick_ToolBox_Option' );
}
if ( ! defined( 'MAGICK_MIXTURE_CONFIG_VERSION' ) ) {
	define( 'MAGICK_MIXTURE_CONFIG_VERSION', 'Magick_ToolBox_Config_Version' );
}
if ( ! defined( 'MAGICK_MIXTURE_CONFIG_BACKUP' ) ) {
	define( 'MAGICK_MIXTURE_CONFIG_BACKUP', 'Magick_ToolBox_Config_Backup' );
}

// WordPress 核心函数 mock（纯单元测试环境）
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $option, $default = false ) {
		global $_test_option_store;
		return $_test_option_store[ $option ] ?? $default;
	}
}
if ( ! function_exists( 'update_option' ) ) {
	function update_option( $option, $value, $autoload = null ) {
		global $_test_option_store;
		$_test_option_store[ $option ] = $value;
		return true;
	}
}
if ( ! function_exists( 'delete_option' ) ) {
	function delete_option( $option ) {
		global $_test_option_store;
		unset( $_test_option_store[ $option ] );
		return true;
	}
}
if ( ! function_exists( 'get_transient' ) ) {
	function get_transient( $transient ) {
		global $_test_transient_store;
		return $_test_transient_store[ $transient ] ?? false;
	}
}
if ( ! function_exists( 'set_transient' ) ) {
	function set_transient( $transient, $value, $expiration = 0 ) {
		global $_test_transient_store;
		$_test_transient_store[ $transient ] = $value;
		return true;
	}
}
if ( ! function_exists( 'delete_transient' ) ) {
	function delete_transient( $transient ) {
		global $_test_transient_store;
		unset( $_test_transient_store[ $transient ] );
		return true;
	}
}
if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file ) {
		return trailingslashit( dirname( $file ) );
	}
}
if ( ! function_exists( 'trailingslashit' ) ) {
	function trailingslashit( $string ) {
		return rtrim( $string, '/\\' ) . '/';
	}
}
if ( ! function_exists( 'wp_parse_args' ) ) {
	function wp_parse_args( $args, $defaults = array() ) {
		if ( is_object( $args ) ) {
			$parsed_args = get_object_vars( $args );
		} elseif ( is_array( $args ) ) {
			$parsed_args = &$args;
		} else {
			wp_parse_str( $args, $parsed_args );
		}
		return array_merge( $defaults, $parsed_args );
	}
}
if ( ! function_exists( 'wp_parse_str' ) ) {
	function wp_parse_str( $string, &$array ) {
		parse_str( $string, $array );
		if ( get_magic_quotes_gpc() ) {
			$array = stripslashes_deep( $array );
		}
	}
}
if ( ! function_exists( 'stripslashes_deep' ) ) {
	function stripslashes_deep( $value ) {
		return map_deep( $value, 'stripslashes_from_strings_only' );
	}
}
if ( ! function_exists( 'map_deep' ) ) {
	function map_deep( $value, $callback ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $index => $item ) {
				$value[ $index ] = map_deep( $item, $callback );
			}
		} elseif ( is_object( $value ) ) {
			$object_vars = get_object_vars( $value );
			foreach ( $object_vars as $property_name => $property_value ) {
				$value->$property_name = map_deep( $property_value, $callback );
			}
		} else {
			$value = call_user_func( $callback, $value );
		}
		return $value;
	}
}
if ( ! function_exists( 'stripslashes_from_strings_only' ) ) {
	function stripslashes_from_strings_only( $value ) {
		return is_string( $value ) ? stripslashes( $value ) : $value;
	}
}
if ( ! function_exists( 'get_magic_quotes_gpc' ) ) {
	function get_magic_quotes_gpc() {
		return false;
	}
}

// 加载插件自动加载器
if ( file_exists( dirname( __FILE__ ) . '/../includes/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/../includes/autoload.php';
}

// 检查 WordPress 测试套件是否可用（完整集成测试路径）
if ( ! defined( 'WP_TESTS_DIR' ) ) {
	$wp_tests_dir = getenv( 'WP_TESTS_DIR' );
	if ( ! $wp_tests_dir ) {
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
		require dirname( __FILE__ ) . '/../magick-tool-box.php';
	}
	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

	require $wp_tests_dir . '/includes/bootstrap.php';
} else {
	// 纯单元测试环境：提供 WP_UnitTestCase fallback
	if ( ! class_exists( 'WP_UnitTestCase' ) ) {
		class WP_UnitTestCase extends PHPUnit\Framework\TestCase {
		}
	}
}
