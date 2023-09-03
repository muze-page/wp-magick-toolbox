<?php //沉默是金


/**
 * WordPress外链新窗口打开并使用php页面go跳转
 * https://www.dujin.org/12762.html
 */
function the_content_nofollowss($content)
{
    preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $matches);
    if ($matches) {
        foreach ($matches[2] as $val) {
            if (strpos($val, '://') !== false && strpos($val, home_url()) === false && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i', $val)) {
                $content = str_replace("href=\"$val\"", "href=\"" . home_url() . "/golink/?url=$val\" ", $content);
            }
        }
    }
    return $content;
}
add_filter('the_content', 'the_content_nofollowss', 999);



function my_custom_plugin_setup()
{
    // 检查是否已经存在自定义页面
    $page_slug = 'goto'; //链接
    $config = 'my_custom_plugin_page_aa'; //唯一标识
    $existing_page_id = get_option($config);

    if ($existing_page_id) {
        return; // 页面已经存在，不执行后续操作
    }

    // 创建新页面
    $page_title = '禁止删除：外链跳转中间页专用(编辑此页无效果)';
    $page_content = 'hello';

    $page = array(
        'post_title'   => $page_title,
        'post_content' => $page_content,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => $page_slug
    );

    // 添加页面，并获取页面ID
    $page_id = wp_insert_post($page);

    // 设置页面模板为无效模板，以避免外部访问该页面
    update_post_meta($page_id, '_wp_page_template', 'invalid-template.php');

    // 隐藏页面在页面管理中的显示选项
    $page_data = array(
        'ID'          => $page_id,
        'post_type'   => 'page',
        'post_status' => 'publish'
    );
    wp_update_post($page_data);

    // 存储页面ID
    update_option($config, $page_id);
}

add_action('init', 'my_custom_plugin_setup');
