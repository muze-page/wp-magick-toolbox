<?php
get_header();
// Custom template code goes here
// 获取当前页面的标题
$page_title = get_the_title();
?>
<div class="card-container">
    <header class="entry-header">
        <h1 class="entry-title">
            <?php echo $page_title; ?>
        </h1>
    </header>
    <div class=" entry-content">
        <?php
        // 获取当前页面的ID
        $post_id = get_the_ID();

        // 获取自定义字段的值
        $value = get_post_meta($post_id, 'mabox_trends_special', true);

        // 如果自定义字段的值为空，则显示提示信息并退出
        if (empty($value)) {
            echo '暂未设置';
            return get_footer(); // 获取页脚;
        }

        // 构建查询参数
        $args = array(
            's' => $value, // 搜索标题中包含指定值的文章
            'post_type' => 'post', // 文章类型为post（可根据需要修改）
            'posts_per_page' => -1, // 显示所有符合条件的文章
        );

        // 查询文章列表
        $query = new WP_Query($args);

        // 输出文章列表
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();


        ?>


                <div class="card">
                    <a href="<?php the_permalink(); ?>" target="_blank">

                        <div class="container">
                            <!-- 显示特色图像 -->
                            <?php if (has_post_thumbnail()) : ?>

                                <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')); ?>" alt="<?php the_title_attribute(); ?>">

                            <?php endif; ?>
                        </div>

                        <div class="card-header">
                            <span><?php the_title(); ?></span>
                            <span>
                                作者：<?php the_author(); ?><br />
                                日期：<?php echo get_the_date('Y-m-d'); ?>
                            </span>
                        </div>

                        <span class="temp"></span>

                    </a>
                </div>


            <?php
            endwhile;
            wp_reset_postdata(); // 重置文章查询
        else :

            ?>
            <p>没有找到符合条件的文章。</p>
        <?php
        endif;
        ?>
    </div>
</div>
<?php

get_footer(); // 获取页脚
?>