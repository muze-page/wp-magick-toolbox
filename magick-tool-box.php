<?php
/*
 * Plugin Name: WP Magick Toolbox【BETA】
 * Description: 魔法工具箱，诸多实用且有趣的功能合集，简单易用；详情请见插件中的「关于」页内容
 * Plugin URI: https://www.npc.ink/277510.html
 * Version: 2.0.8
 * Author: Npcink
 * Author URI: https://www.npc.ink/
 * Requires at least: 4.6
 * Requires PHP:      7.0
 */
//调试内容，在后台顶部显示一个通知
// 如果直接调用此文件，请中止。
if (!defined('WPINC')) {
    die;
}

/**
 * 当前插件版本。
 *从1.0.0版本开始，使用SemVer-https://semver.org
 *重命名此插件，并在发布新版本时进行更新。
 */
//定义插件名
define('MAGICK_MIXTURE_NAME', 'magick-optimize');
//定义插件版本
define('MAGICK_MIXTURE_VERSION', '2.0.8');
//定义保存选项字段
define('MAGICK_MIXTURE_OPTION', "Magick_ToolBox_Option");

/**
 * 用于定义需要用到的插件类，
 */
require plugin_dir_path(__FILE__) . 'includes/class-magick-mixtrue.php';




/**
 * 开始执行插件。
 *
 *由于插件内的所有内容都是通过钩子注册的，
 *然后从文件中的这一点启动插件
 *不影响页面生命周期。
 *
 */
function run_magick_mixture()
{

    $plugin = new Magick_Mixtrue();
    $plugin->run();
}
run_magick_mixture();



//设置按钮
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $links[] = '<a href="' . get_admin_url(null, 'plugins.php?page=MaBox_config') . '">' . __('设置', 'n') . '</a>';
    return $links;
});



//测试类 - 开发用，正式用记得注释掉
//require plugin_dir_path(__FILE__) . 'index.php';



// 创建空白页面函数
function create_blank_page_with_content()
{
    // 检查是否已经创建过页面，避免重复创建
    if (get_page_by_title('Blank Page') === null) {
        // 设置页面标题和内容
        $page_title = 'Blank Page';
        $page_content = '666';

        // 创建页面对象
        $new_page = array(
            'post_title'    => $page_title,
            'post_content'  => $page_content,
            'post_status'   => 'publish',
            'post_type'     => 'page',  // 指定为页面类型
        );

        // 插入页面到数据库
        $new_page_id = wp_insert_post($new_page);

        // 检查页面是否成功创建
        if ($new_page_id) {
            echo "Blank page successfully created with ID: " . $new_page_id;
        } else {
            echo "Failed to create blank page. Please try again.";
        }
    } else {
        echo "The blank page already exists.";
    }
}

// 在 init 钩子后执行创建页面操作
//add_action('init', 'create_blank_page_with_content');
