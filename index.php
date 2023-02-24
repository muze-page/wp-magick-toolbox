<?php //沉默是金

add_action('admin_menu', 'rudr_top_lvl_menu');

function rudr_top_lvl_menu()
{

    $icon = '<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M1280 704q0-26-19-45t-45-19q-172 0-318 49.5t-259.5 134-235.5 219.5q-19 21-19 45 0 26 19 45t45 19q24 0 45-19 27-24 74-71t67-66q137-124 268.5-176t313.5-52q26 0 45-19t19-45zm512-198q0 95-20 193-46 224-184.5 383t-357.5 268q-214 108-438 108-148 0-286-47-15-5-88-42t-96-37q-16 0-39.5 32t-45 70-52.5 70-60 32q-43 0-63.5-17.5t-45.5-59.5q-2-4-6-11t-5.5-10-3-9.5-1.5-13.5q0-35 31-73.5t68-65.5 68-56 31-48q0-4-14-38t-16-44q-9-51-9-104 0-115 43.5-220t119-184.5 170.5-139 204-95.5q55-18 145-25.5t179.5-9 178.5-6 163.5-24 113.5-56.5l29.5-29.5 29.5-28 27-20 36.5-16 43.5-4.5q39 0 70.5 46t47.5 112 24 124 8 96z"/></svg>';

    add_menu_page(
        '幻灯片设置', // page <title>Title</title>
        '幻灯片', // link text
        'manage_options', // 用户能力
        'rudr_slider', // page slug
        'rudr_slider_page_callback', // 此函数打印页面内容
        'data:image/svg+xml;base64,' . base64_encode($icon),
        4// menu position
    );
}

function rudr_slider_page_callback()
{
    ?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title() ?></h1>
			<form method="post" action="options.php">
				<?php
settings_errors('rudr_slider_settings_errors'); // here we are
    settings_fields('rudr_slider_settings'); // 设置组名称
    do_settings_sections('rudr_slider'); // just a page slug，只是一个页面段
    submit_button(); // 保存按钮
    ?>
			</form>
		</div>
	<?php
}

add_action('admin_init', 'rudr_settings_fields');
function rudr_settings_fields()
{

    // 我创建了变量以使事情更清楚
    $page_slug = 'rudr_slider';
    $option_group = 'rudr_slider_settings';

    // 1. 创建节
    add_settings_section(
        'rudr_section_id', // section ID
        '', // title (optional)
        '', // 显示节的回调函数（可选）
        $page_slug //显示的位置
    );

    // 2. 保存选项用字段
    register_setting(
        $option_group, //选项组
        'slider_on', //选项名称
        'rudr_sanitize_checkbox' //数据验证
    );

    register_setting($option_group, 'num_of_slides', 'rudr_validate');

    // 3. 添加字段
    add_settings_field(
        'slider_on',
        '显示幻灯片',
        'rudr_checkbox', // 函数打印字段
        $page_slug,
        'rudr_section_id' // 节的 ID
    );

    add_settings_field(
        'num_of_slides',
        '幻灯片数量',
        'rudr_number',
        $page_slug,
        'rudr_section_id',
        array(
            'label_for' => 'num_of_slides',
            'class' => 'hello', // for <tr> element
            'name' => 'num_of_slides', // 传递任何自定义参数
        )
    );

    /**
     * 创建第二个节，用作选项展示
     */
    add_settings_section(
        'rudr_section_display', // section ID
        '展示效果', // title (optional)
        'rudr_section_callback',
        $page_slug
    );

}

// 打印字段HTML的自定义回调函数
function rudr_number($args)
{
    printf(
        '<input type="number" id="%s" name="%s" value="%d" />',
        $args['name'],
        $args['name'],
        get_option($args['name'], 2) // 2 is the default number of slides
    );
}
// 用于打印复选框字段HTML的自定义回调函数
function rudr_checkbox($args)
{
    $value = get_option('slider_on');
    ?>
		<label>
			<input type="checkbox" name="slider_on" <?php checked($value, 'yes')?> /> Yes
		</label>
	<?php
}

/**
 * 数据验证
 */
// 复选框字段的自定义清除功能
function rudr_sanitize_checkbox($value)
{
    return 'on' === $value ? 'yes' : 'no';
}

//自定义的消毒功能
function rudr_validate($input)
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

//显示保存成功消息
add_action('admin_notices', 'rudr_notice');

function rudr_notice()
{

    $settings_errors = get_settings_errors('rudr_slider_settings_errors');
    // 如果有任何错误，请退出
    if (!empty($settings_errors)) {
        return;
    }

    if (
        isset($_GET['page'])
        && 'rudr_slider' == $_GET['page']
        && isset($_GET['settings-updated'])
        && true == $_GET['settings-updated']
    ) {
        ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<strong>选项设置已保存</strong>
				</p>
			</div>
		<?php
}

}

/**
 * 效果展示
 */
function rudr_section_callback()
{
//获取选项值
    $switch = get_option('slider_on');
    $num = get_option('num_of_slides');
    ?>
您的开关状态：<?php echo $switch; ?>
<br />
您填写的数字是：<?php echo $num; ?>
    <?php
}




