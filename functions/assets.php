<?php

function my_theme_enqueue_assets() {
    // Enqueue header style on the frontend (not in the admin area)

    wp_enqueue_style(
        'custom-header', // Unique handle for the stylesheet
        get_template_directory_uri() . '/assets/css/global/header.css', // Correct path to the file in your child theme
        [], // Dependencies (empty array if none)
        filemtime(get_template_directory() . '/assets/css/global/header.css'),
        'all'    // Media type
    );
    
    wp_enqueue_style(
        'custom-global', // Unique handle for the stylesheet
        get_template_directory_uri() . '/assets/css/global/global.css', // Correct path to the file in your child theme
        [], // Dependencies (empty array if none)
        filemtime(get_template_directory() . '/assets/css/global/global.css'),
        'all'    // Media type
    );

    wp_enqueue_style(
        'tailwindcss', // Unique handle for the stylesheet
        get_template_directory_uri() . '/assets/css/global/output.css', // Correct path to the file in your child theme
        [], // Dependencies (empty array if none)
        filemtime(get_template_directory() . '/assets/css/global/output.css'),
        'all'    // Media type
    );

    wp_enqueue_style(
        'custom-footer', // Unique handle for the stylesheet
        get_template_directory_uri() . '/assets/css/global/footer.css', // Correct path to the file in your child theme
        [], // Dependencies (empty array if none)
        filemtime(get_template_directory() . '/assets/css/global/footer.css'),
        'all'    // Media type
    );
    
    if ( !is_admin() ) {
        wp_enqueue_script(
        'global',
        get_template_directory_uri() . '/assets/js/global/global.js',
        [],
        filemtime(get_template_directory() . '/assets/js/global/global.js'),
        true);
    }
}

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_assets', 101);

function my_admin_function($hook) {
    wp_enqueue_style(
        'tailwindcss', // Unique handle for the stylesheet
        get_template_directory_uri() . '/assets/css/global/output.css', // Correct path to the file in your child theme
        [], // Dependencies (empty array if none)
        filemtime(get_template_directory() . '/assets/css/global/output.css'),
        'all'    // Media type
    );
}