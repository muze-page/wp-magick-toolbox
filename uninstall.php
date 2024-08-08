<?php

/**
 * 在卸载插件时激发。
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.npc.ink
 * @since      1.0.0
 *
 * @package    Dema
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}



//执行卸载插件时的动作
//require plugin_dir_path(__FILE__) . 'admin/partials/function/config/remove_config.php';
//function run_mare_uninstall()
//{
//	$plugin = new MaBox_Config_Remove_Config();
//	$plugin->run();
//}
//run_mare_uninstall();

//TODO:待检查
function delete_custom_field_from_all_posts($field_key)
{
	$args = array(
		'post_type' => array('post', 'page'),  // 可以修改为其他自定义文章类型
		'posts_per_page' => -1,  // 获取所有文章
		'post_status' => 'any',  // 获取所有状态的文章
	);

	$posts = get_posts($args);

	foreach ($posts as $post) {
		$post_id = $post->ID;
		$existing_value = get_post_meta($post_id, $field_key, true);

		if (!empty($existing_value)) {
			delete_post_meta($post_id, $field_key);
			echo "Deleted custom field '{$field_key}' from post {$post_id}.<br>";
		} else {
			echo "Custom field '{$field_key}' not found in post {$post_id}.<br>";
		}
	}
}

// 使用示例：删除名为 'custom_field_options' 的自定义字段
delete_custom_field_from_all_posts('mabox_trends_special');


//delete_option("Magick_ToolBox_Option");
delete_option(MAGICK_MIXTURE_OPTION);
