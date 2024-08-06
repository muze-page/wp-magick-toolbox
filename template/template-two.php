<?php
    get_header();
// Custom template code goes here
echo "two";
// 获取当前页面的标题
$page_title = get_the_title();
echo $page_title;

// 获取当前页面的内容
$page_content = apply_filters( 'the_content', get_post_field( 'post_content', get_the_ID() ) );
echo $page_content;

get_footer();