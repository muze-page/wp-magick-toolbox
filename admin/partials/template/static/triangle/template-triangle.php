<?php
get_header();
// Custom template code goes here

// 获取当前页面的标题
$page_title = get_the_title();


// 获取当前页面的内容
$page_content = apply_filters('the_content', get_post_field('post_content', get_the_ID()));

?>
<div class="triangle">
    <!--标题-->
    <header class="entry-header">
        <h2 class="triangle-center entry-title">
            <?php echo $page_title; ?>
        </h2>
    </header>




    <div id="triangle" class="triangle-center">
        <svg id="Layer_1" data-name="Layer 1" version="1.1" viewBox="0 0 2000 2000">
            <polygon
                class="cls-1"
                points="928 781 1021 951 784.5 1371.97 1618 1371.97 1530.32 1544 509 1539 928 781"></polygon>
            <polygon
                class="cls-3"
                points="1618 1371.97 784.5 1371.97 874.93 1211 1346 1211 923.1 456 1110.06 456 1618 1371.97"></polygon>
            <g id="Layer_2" data-name="Layer 2">
                <polygon
                    class="cls-2"
                    points="418 1372.74 509 1539 928 781 1162.32 1211 1346 1211 923.1 456 418 1372.74"></polygon>
            </g>
        </svg>
    </div>

    <!--内容-->
    <div class="entry-content">
        <?php echo $page_content; ?>
    </div>
</div>




<?php



get_footer();
