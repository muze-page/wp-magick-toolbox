<?php //沉默是金

//撰写首页用接口
//根据设置，输出首页要展示用的数据
add_action('rest_api_init', function () {
    register_rest_route('carbon-fields/v1', 'posts', array(
        'methods' => 'GET',
        'callback' => 'mytheme_get_posts',
    ));
});

function mytheme_get_posts($request)
{
    $args = array(
        'posts_per_page' => 11, // 获取最新的11篇文章
        'post_status' => 'publish', // 只获取已发布的文章
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $result = array();

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            $post_title = get_the_title();
            $post_excerpt = get_the_excerpt(); // 获取文章摘要
            $post_date = get_the_date('Y-m-d H:i:s');

            $post_categories = get_the_category();
            $cat_array = array();
            foreach ($post_categories as $cat) {
                $cat_array[] = array(
                    'id' => $cat->cat_ID,
                    'name' => $cat->name,
                );
            }
            $categories = $cat_array;

            $featured_image = array();
            if (has_post_thumbnail()) {
                $thumbnail_id = get_post_thumbnail_id($post_id);
                $featured_image['url'] = wp_get_attachment_url($thumbnail_id);
                $featured_image['caption'] = get_post($thumbnail_id)->post_excerpt;
                $featured_image['details'] = wp_get_attachment_metadata($thumbnail_id);
            }
            $post_content = apply_filters('the_content', get_the_content()); // 获取文章正文内容
            $response = array(
                'id' => $post_id,
                'date' => $post_date,
                'title' => $post_title,
                'excerpt' => $post_excerpt,
                'image' => $featured_image,
                'cat' => $categories,
                'content' => $post_content,
            );

            $result[] = $response;
        }

        wp_reset_postdata();

        return $result; // 返回文章数据
    }

    return new WP_Error('no_posts', 'No posts found', array('status' => 404)); // 若无文章，返回404错误
}


add_action( 'rest_api_init', function() {
    register_rest_route( 'mytheme/v1', 'postssli', array(
      'methods' => 'GET',
      'callback' => 'mytheme_get_posts_data',
    ) );
  } );
  
  function mytheme_get_posts_data() {
    $post_ids = carbon_get_theme_option( 'comm_h5_index_tone' ); // 获取主题选项 'comm_h5_index_tone' 的值，即多个文章 ID
    if ( ! $post_ids ) {
      return new WP_Error( 'no_post', 'No post found', array( 'status' => 404 ) );
    }
    $posts_data = array();
    foreach ($post_ids as $post_id) {
      $post = get_post( $post_id );
      if ( ! $post ) {
        continue;
      }
      $post_title = get_the_title($post_id);
      $post_excerpt = get_the_excerpt($post_id);
      $featured_image = array();
      if (has_post_thumbnail($post_id)) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        $featured_image['url'] = wp_get_attachment_url($thumbnail_id);
        $featured_image['caption'] = get_post($thumbnail_id)->post_excerpt;
        $featured_image['details'] = wp_get_attachment_metadata($thumbnail_id);
      }
      $post_categories = get_the_category($post_id);
      $cat_array = array();
      foreach ($post_categories as $cat) {
        $cat_array[] = array(
          'id' => $cat->cat_ID,
          'name' => $cat->name,
        );
      }
      $categories = $cat_array;
      $post_content = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );
    
      $response = array(
        'id' => $post_id,
        'title' => $post_title,
        'excerpt' => $post_excerpt,
        'image' => $featured_image,
        'cat' => $categories,
        'content' => $post_content,
      );
      $posts_data[] = $response;
    }
    return $posts_data; // 返回文章数据
  }
  