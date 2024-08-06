<?php
/*
 * Plugin Name: WP Magick Toolbox【BETA】
 * Description: 魔法工具箱，诸多实用且有趣的功能合集，简单易用；详情请见插件中的「关于」页内容
 * Plugin URI: https://www.npc.ink/277510.html
 * Version: 2.0.81
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
define('MAGICK_MIXTURE_VERSION', '2.0.81');
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
// 添加可用的页面模板
function custom_page_templates($templates)
{
    // 添加新的页面模板
    $new_templates = array(
        'template-one.php' => 'Custom Template one',
        'template-two.php' => 'Another Custom Template two'
        // 添加更多页面模板，按照相同的格式进行添加
    );

    // 合并新的模板数组到现有的模板数组中
    $templates = array_merge($templates, $new_templates);

    return $templates;
}
//add_filter('theme_page_templates', 'custom_page_templates');



// 根据选择的页面模板加载指定模板文件
function load_custom_template($template)
{
    global $post;

    // 定义页面模板数组
    $custom_templates = array(
        'template-one.php' => 'template/template-one.php',
        'template-two.php' => 'template/template-two.php'
        // 添加更多页面模板和对应的文件路径，按照相同的格式进行添加
    );

    // 获取当前页面模板的文件路径
    $current_template_slug = get_page_template_slug($post->ID);
    $current_template_path = isset($custom_templates[$current_template_slug]) ? plugin_dir_path(__FILE__) . $custom_templates[$current_template_slug] : '';

    // 如果找到匹配的模板文件路径，则返回该路径
    if (!empty($current_template_path)) {
        return $current_template_path;
    }

    return $template;
}
//add_filter('template_include', 'load_custom_template');
