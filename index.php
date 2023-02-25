<?php //沉默是金

//添加顶级菜单
function sandbox_create_menu_page()
{
    add_menu_page(
        '沙盒选项', // 此菜单对应页面上显示的标题
        '沙盒', // 要为此实际菜单项显示的文本
        'administrator', // 哪种类型的用户可以看到此菜单
        'sandbox_id', // The unique ID - that is, the slug - for this menu item 此菜单项的唯一ID（即段塞）
        'sandbox_menu_page_display', // 呈现此页面的菜单时要调用的函数的名称
        'dashicons-welcome-widgets-menus', //菜单图标
        '90', //顺序
    );
} // end sandbox_create_menu_page
add_action('admin_menu', 'sandbox_create_menu_page');

//添加回调函数
function sandbox_menu_page_display666()
{

    ?>
	<!-- 在默认WordPress“wrap”容器中创建标题 -->
	<div class="wrap">
		<!--标题-->
		<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
		<!-- 在保存设置时调用WordPress函数以呈现错误. -->
		<?php settings_errors();?>
		<!-- 创建用于呈现选项的表单 -->
		<form method="post" action="options.php">
			<?php settings_fields('sandbox');?>
			<?php do_settings_sections('sandbox');?>
			<?php submit_button();?>
		</form>
	</div><!-- /.wrap -->
<?php

} // end sandbox_menu_page_display

function sandbox_menu_page_display()
{
    ?>
        <!-- 在默认WordPress“wrap”容器中创建标题 -->
        <div class="wrap">

           <!--标题-->
		<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
        <!-- 在保存设置时调用WordPress函数以呈现错误. -->
            <?php settings_errors();?>

            <?php
if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    } // end if

    //设置默认值
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'display_options';
    ?>

		<h2 class="nav-tab-wrapper">
			<a href="?page=sandbox_id&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">显示选项</a>
			<a href="?page=sandbox_id&tab=social_options" class="nav-tab <?php echo $active_tab == 'social_options' ? 'nav-tab-active' : ''; ?>">效果展示</a>
		</h2>



        <form method="post" action="options.php">
		<?php
//根据tab显示对应的内容
    if ($active_tab == 'display_options') {
        settings_fields('sandbox');
        do_settings_sections('sandbox');
    } else {
        settings_fields('sandbox-two');
        do_settings_sections('sandbox-two');
    } // end if/else

    submit_button();

    ?>
	</form>

        </div><!-- /.wrap -->
    <?php
} // end sandbox_theme_display

/**
 * 在设置菜单 - 常规菜单底部添加设置
 */
/* ------------------------------------------------------------------------ *
 *设置注册
 * ------------------------------------------------------------------------ */
/**
 *通过注册节来初始化主题选项页，
 *字段和设置。
 *
 *此函数使用“admin_init”钩子注册。
 */
add_action('admin_init', 'sandbox_initialize_theme_options');
function sandbox_initialize_theme_options()
{
    // 首先，我们注册一个部分。这是必要的，因为所有未来的选项都必须属于一个。
    add_settings_section(
        'general_settings_section', // 用于标识此部分以及用于注册选项的ID
        '沙盒设置', // 要在管理页面上显示的标题
        'sandbox_general_options_callback', // 用于呈现节描述的回调
        'sandbox' // 添加此部分选项的页面
    );

    //注册一个部分，用来显示效果
    add_settings_section(
        'general_settings_hcf',
        '使用选项',
        'option_test_hcf', //呈现内容的回调
        'sandbox-two',
    );

    //节
    // 接下来，我们将介绍用于切换内容元素可见性的字段。
    add_settings_field(
        'show_header', // 用于标识整个主题中的字段的ID
        '头部', // 选项接口元素左侧的标签
        'sandbox_toggle_header_callback', // 负责呈现选项界面的函数的名称
        'sandbox', // 将显示此选项的页面
        'general_settings_section', // 此字段所属的节的名称
        array( // 要传递给回调的参数数组。在这种情况下，只是一个描述。
            '激活此设置以显示标题。',
        )
    );

//第二个字段
    add_settings_field(
        'show_content',
        '内容',
        'sandbox_toggle_content_callback',
        'sandbox',
        'general_settings_section',
        array(
            '激活此设置以显示内容。',
        )
    );

//第三个字段
    add_settings_field(
        'show_footer',
        '底部',
        'sandbox_toggle_footer_callback',
        'sandbox',
        'general_settings_section',
        array(
            '激活此设置以显示页脚。',
        )
    );

// 最后，我们用WordPress注册这些字段
    register_setting(
        'sandbox', //位置
        'style_option' //选项
    );

} //结束sandbox_initialize_theme_options
/* ------------------------------------------------------------------------ *
 *节回调
 * ------------------------------------------------------------------------ */
/**
 *此函数为“常规选项”页面提供简单说明。
 *
 *它是通过作为参数传递从“sandbox_initialize_theme_options”函数调用的
 *在add_settings_section函数中。
 */
function sandbox_general_options_callback()
{
    echo '<p>选择要显示的内容区域。</p>';
} //结束sandbox_general_options_callback

/* ------------------------------------------------------------------------ *
 *字段回调
 * ------------------------------------------------------------------------ */

/**
 *此函数呈现用于切换头元素可见性的接口元素。
 *
 *它接受一个参数数组，并期望数组中的第一个元素是描述
 *将显示在复选框旁边。
 */

function sandbox_toggle_header_callback($args)
{
// 首先，我们阅读选项集合
    $options = get_option('style_option');
    //判断下，没有值就给个默认值
    $show_header = isset($options['show_header']) ? (bool) $options['show_header'] : false;
    //注意，元素的ID和name属性应与add_settings_field调用中的ID属性相匹配
    $html = '<input type="checkbox" id="show_header" name="style_option[show_header]" value="1" ' . checked(1, $show_header, false) . '/>';

    //在这里，我们将获取数组的第一个参数，并将其添加到复选框旁边的标签中
    $html .= '<label for="show_header"> ' . $args[0] . '</label>';

    echo $html;

} // end sandbox_toggle_header_callback

//第二个设置回调函数
function sandbox_toggle_content_callback($args)
{
    $options = get_option('style_option');
    $show_content = isset($options['show_content']) ? (bool) $options['show_content'] : false;
    $html = '<input type="checkbox" id="show_content" name="style_option[show_content]" value="1" ' . checked(1, $show_content, false) . '/>';
    $html .= '<label for="show_content"> ' . $args[0] . '</label>';

    echo $html;

} // end sandbox_toggle_content_callback

//第三个设置回调函数
function sandbox_toggle_footer_callback($args)
{

    $options = get_option('style_option');
    $show_footer = isset($options['show_footer']) ? (bool) $options['show_footer'] : false;
    $html = '<input type="checkbox" id="show_footer" name="style_option[show_footer]" value="1" ' . checked(1, $show_footer, false) . '/>';
    $html .= '<label for="show_footer"> ' . $args[0] . '</label>';

    echo $html;

} // end sandbox_toggle_footer_callback

//使用回调
function magick_option_test_switch($a = false)
{
    if ($a) {
        //有值
        echo "您选择了";
        return;
    } else {
        //无值
        echo "您没有选择";
        return;
    }
}
function option_test_hcf()
{
    $options = get_option('style_option');
    ?>

    您的首页：<?php magick_option_test_switch(isset($options['show_header']) ? $options['show_header'] : false); //判断下，没有值就给个默认值?>
    <br />
    您的内容：<?php magick_option_test_switch(isset($options['show_content']) ? $options['show_content'] : false);?>
    <br />
    您的底部：<?php magick_option_test_switch(isset($options['show_footer']) ? $options['show_footer'] : false);?>


    <?php

}
