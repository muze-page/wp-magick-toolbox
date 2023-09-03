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
                $content = str_replace("href=\"$val\"", "href=\"" . home_url() . "/go/?url=$val\" ", $content);
            }
        }
    }
    return $content;
}
//add_filter('the_content', 'the_content_nofollowss', 999);
