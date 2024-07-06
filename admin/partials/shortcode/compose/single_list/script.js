jQuery(document).ready(function($) {
    // 页面加载完成后执行的代码

    // 检测并报告任何错误
    function checkForErrors() {
        $('.past-posts-listing').each(function() {
            var $this = $(this);
            if ($this.find('.past-post-item').length === 0) {
                $this.html('<p>No posts found. Please check the post IDs or links.</p>');
            }
        });
    }

    // 调用错误检查函数
    checkForErrors();
});