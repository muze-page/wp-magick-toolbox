<?php

/**
 * 效果：禁止纯英文评论
 * 来源：https://www.npc.ink/18129.html
 */

if (!class_exists('Npcink_Comment_Ban_Pure_English')) {
    class Npcink_Comment_Ban_Pure_English
    {
        public static function run()
        {
            add_filter('preprocess_comment', array(__CLASS__, 'refused_english_comments'));
        }

        public static function refused_english_comments($incoming_comment)
        {
            $pattern = '/[一-龥]/u';
            if (!preg_match($pattern, $incoming_comment['comment_content'])) {
                $message = '您的评论中必须包含汉字!';
                $message = $message . MaMi_Admin::back_button();
                wp_die($message);
            }
            return $incoming_comment;
        }
    }
}
