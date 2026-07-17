<?php
defined('ABSPATH') || exit;

/**
 * 效果：简单SEO - 分类和标签添加输入框
 * 来源：https://www.npc.ink/4596.html
 */
if (!class_exists('MaBox_Seo_Category_Add_Meat')) {
    class MaBox_Seo_Category_Add_Meat implements MaBox_Module_Interface
    {
        public static function run($config = array())
        {
            //添加分类的关键词
            add_action('category_add_form_fields', array(__CLASS__, 'add_category_field'), 10, 2); // 分类添加字段
            add_action('category_edit_form_fields', array(__CLASS__, 'edit_category_field'), 10, 2); // 分类编辑字段
            add_action('created_category', array(__CLASS__, 'taxonomy_metadate'), 10, 1); // 保存数据
            add_action('edited_category', array(__CLASS__, 'taxonomy_metadate'), 10, 1); // 保存数据
            //添加标签的关键词
        }

        // 分类添加字段
        public static  function add_category_field()
        {
            echo '<div class="form-field">
            <label for="cat-title">分类标题</label>
            <input name="cat-title" id="cat-title" type="text" value="" size="40">
            <p>用于SEO自定义标题</p>
          </div>';

            echo '<div class="form-field">
			<label for="cat-words">分类关键字</label>
            <input name="cat-words" id="cat-words" type="text" value="" size="40">
            <p>用于SEO自定义关键字</p>
          </div>';
        }


        // 分类编辑字段
        public static function edit_category_field($tag)
        {
            echo '<tr class="form-field">
            <th scope="row"><label for="cat-title">分类标题</label></th>
            <td>
                <input name="cat-title" id="cat-title" type="text" value="';
            echo esc_attr(get_option('cat-title-' . $tag->term_id)) . '" size="40"/><br>
                <span class="cat-title">用于' . esc_html($tag->name) . '分类SEO自定义标题</span>
            </td>
        </tr>';

            echo '<tr class="form-field">
            <th scope="row"><label for="cat-words">分类关键字</label></th>
            <td>
                <input name="cat-words" id="cat-words" type="text" value="';
            echo esc_attr(get_option('cat-words-' . $tag->term_id)) . '" size="40"/><br>
                <span class="cat-words">用于' . esc_html($tag->name) . '分类SEO自定义关键字，用英文逗号分隔，如：keyword1,keyword2,keyword3</span>
            </td>
        </tr>';
        }


        // 保存数据
        public static function taxonomy_metadate($term_id)
        {
            if (!current_user_can('manage_categories')) {
                return $term_id;
            }

            $action = isset($_POST['action']) && is_string($_POST['action'])
                ? sanitize_key(wp_unslash($_POST['action']))
                : '';
            $nonce_valid = false;

            if ('add-tag' === $action && isset($_POST['_wpnonce_add-tag']) && is_string($_POST['_wpnonce_add-tag'])) {
                $nonce = sanitize_text_field(wp_unslash($_POST['_wpnonce_add-tag']));
                $nonce_valid = wp_verify_nonce($nonce, 'add-tag') !== false;
            } elseif ('editedtag' === $action && isset($_POST['_wpnonce']) && is_string($_POST['_wpnonce'])) {
                $nonce = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
                $nonce_valid = wp_verify_nonce($nonce, 'update-tag_' . absint($term_id)) !== false;
            }

            if (
                !$nonce_valid
                || !isset($_POST['cat-title'], $_POST['cat-words'])
                || !is_string($_POST['cat-title'])
                || !is_string($_POST['cat-words'])
            ) {
                return $term_id;
            }

            $title_key = 'cat-title-' . absint($term_id);
            $title_value = sanitize_text_field(wp_unslash($_POST['cat-title']));
            $words_key = 'cat-words-' . absint($term_id);
            $words_value = sanitize_text_field(wp_unslash($_POST['cat-words']));

            update_option($title_key, $title_value);
            update_option($words_key, $words_value);

            return $term_id;
        }
    }
}
