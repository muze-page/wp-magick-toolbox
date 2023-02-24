<?php
/*
Plugin Name: 魔法合剂插件
Plugin URI: https://www.npc.ink/
Description: 目前主要是统计功能
Version: 0.0.3
Author: Muze
Author URI: https://www.npc.ink/
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
define('MAGICK_MIXTURE_VERSION', '0.0.3');

/**
 * 用于定义国际化的核心插件类，
 */
require plugin_dir_path(__FILE__) . 'includes/class-magick-mixtrue.php';

require plugin_dir_path(__FILE__) . 'index.php';

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

$magick_tool = new Magick_Mixtrue_Tool;

//echo '<h1>当前文章评论已打开</h1>';
//$magick_tool->p($id);

//添加一个下拉框

//注册一个设置
add_action('admin_init', 'magick_plugin_options');
function magick_plugin_options()
{
    // 1、首先，我们注册一个部分。这是必要的，因为所有未来的选项都必须属于一个。
    add_settings_section(
        'magick_settings_section', // 用于标识此部分以及用于注册选项的ID
        '自定义设置', // 要在管理页面上显示的标题
        'magick_plugin_options_callback', // 用于呈现节描述的回调
        'general' // 添加此部分选项的页面
    );

    //2、注册这个设置
    register_setting(
        'general', //选项组
        'magick_plugin_config', //选项名称,存储选项用
        'rudr_validates', //数据验证
    );

    //3、添加一个数字输入框选项
    add_settings_field(
        'selected_frame', // 用于标识整个主题中的字段的ID
        '数量', // 选项接口元素左侧的标签
        'magick_selected_frame_callback', // 负责呈现选项界面的函数的名称
        'general', // 将显示此选项的页面
        'magick_settings_section', // 此字段所属的节的名称
        array( // 要传递给回调的参数数组。在这种情况下，只是一个描述。
            'label_for' => 'magick_plugin_config',
            'class' => 'hello', // for <tr> element
            'name' => 'magick_plugin_config', // 传递任何自定义参数
            'msg' => "推荐输入20-50内的值",
        )
    );

} //结束magick_plugin_options
/* ------------------------------------------------------------------------ *
 *节回调
 * ------------------------------------------------------------------------ */
/**
 *此函数为“常规选项”页面提供简单说明。
 *
 *它是通过作为参数传递从“magick_plugin_options”函数调用的
 *在add_settings_section函数中。
 */
function magick_plugin_options_callback()
{
    //拿到选项
    $option = get_option('magick_plugin_config');
    echo "您输入的值是：" . $option;

} //结束magick_plugin_options_callback

//数字输入框设置的回调
function magick_selected_frame_callback($args)
{

    printf(
        '<input type="number" id="%s" name="%s" value="%d" />',
        $args['name'],
        $args['name'],
        get_option($args['name'], 2), // 2 is the default number of slides

    );
    //提示信息
    echo "<label>".$args['msg']."</label>";

} // end magick_show_link_callback

//自定义的消毒功能
function rudr_validates($input)
{
    // 首先消毒
    $input = absint($input);

    if ($input < 2) { // 某些条件
        add_settings_error(
            'rudr_slider_settings_errors',
            'not-enough', // 错误消息ID的一部分 id="setting-error-not-enough"
            '幻灯片的最小数量应至少为 2!',
            'error' // success, warning, 信息
        );
        // 如果验证失败，则获取上一个字段值
        $input = get_option('num_of_slides');
    }

    return $input;
}
