<?php //沉默是金






// 替换评论作者提供的网址链接属性
function replace_comment_link_attributes($content)
{
    $pattern = '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/';
    $replacement = '<a$1href="$2"$3 rel="external nofollow" target="_blank">$4</a>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}



//add_filter('get_comment_text', 'replace_comment_link_attributes');

// 允许任何来源的跨域请求
function allow_cors()
{
    header("Access-Control-Allow-Origin: *");
}
//add_action('init', 'allow_cors');

//判断是微信中打开


function add_hello_footer()
{
?>
    <script>
        function is_weixn_qq() {
            var ua = navigator.userAgent.toLowerCase();
            if (ua.match(/MicroMessenger/i) == "micromessenger") {
                alert('微信中打开');
            } else if (ua.match(/QQ/i) == "qq") {
                alert('QQ中打开');
            }
            return false;
        }
        is_weixn_qq();
        /*
                var ua = navigator.userAgent.toLowerCase();
                var isWeixin = ua.indexOf('micromessenger') != -1;
                if (isWeixin) {
                    console.log('微信中打开');
                    alert('微信中打开');
                } 
                    */
    </script>
<?php
}
//add_action('wp_footer', 'add_hello_footer');
