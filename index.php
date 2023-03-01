<?php //沉默是金

//载入所需VUE框架
function magick_load_vue()
{
    wp_register_script(
        MAGICK_MIXTURE_NAME,
        plugin_dir_url(__FILE__) . 'public/js/style-click-particle.js',
        array(),
        MAGICK_MIXTURE_VERSION,
        true
    );
}
//add_action('wp_enqueue_scripts', 'magick_load_vue');


