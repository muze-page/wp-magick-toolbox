<?php //沉默是金


//上传图片重命名
add_filter('wp_handle_upload_prefilter', 'custom_upload_filter' );
function custom_upload_filter( $file ){
    $info = pathinfo($file['name']);
    $ext = $info['extension'];
    $filedate = date('YmdHis').rand(10,99);//为了避免时间重复，再加一段2位的随机数
    $file['name'] = $filedate.'.'.$ext;
    return $file;
}