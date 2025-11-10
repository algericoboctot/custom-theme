<?php

/**
 * Remove the WordPress text at the bottom of the admin
 *
 * @param  string $text current footer text.
 * @return string the changed footer text
 */
add_filter( 'admin_footer_text', 'remove_footer_text' );
function remove_footer_text() {
    return '';
}

/**
 * Change logo URL on WP login page to point to site's homepage
 *
 * @return string 	Homepage URL
 */
add_filter( 'login_headerurl', function() {
	return get_home_url();
});

/**
* Remove File Editor on Admin
*
* @return boolean 
*/
define( 'DISALLOW_FILE_EDIT', true );


/**
* Allow upload of svg
*
* @return void
*/
function enable_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

add_filter('upload_mimes', 'enable_svg_uploads');

function allow_zip_uploads($mimes) {
    $mimes['zip'] = 'application/zip';
    return $mimes;
}
add_filter('upload_mimes', 'allow_zip_uploads');

/**
 * Register menu functionality, initilize plugin functionality
 *
 * @return void
 */
add_action( 'init', 'menu_nit' );

function menu_nit() {
    // Register Menu
    register_nav_menus(
        array(
            'main_menu' => 'Navigation items for Header menu.',
            'footer_col_1' => 'Navigation items for Footer Column 1',
            'footer_col_2' => 'Navigation items for Footer Column 2',
            'footer_col_3' => 'Navigation items for Footer Column 3',
            'footer_col_4' => 'Navigation items for Footer Column 4',
            'top_menu' => 'Navigation items for Top menu'
        )
    );
}

// 1) Handle toggle & persist to user meta
add_action('admin_init', function () {
    if (!current_user_can('manage_options')) return;

    if (isset($_GET['toggle-show-all-menu'])) {
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'toggle_show_all_menu')) {
            wp_die('Security check failed.');
        }
        $user_id = get_current_user_id();
        $current = get_user_meta($user_id, 'show_all_admin_menu', true) === '1';
        update_user_meta($user_id, 'show_all_admin_menu', $current ? '0' : '1');

        // Redirect back without the toggle params to keep URLs clean
        $back = wp_get_referer();
        if (!$back) $back = admin_url('index.php');
        $back = remove_query_arg(['toggle-show-all-menu', '_wpnonce'], $back);
        wp_safe_redirect($back);
        exit;
    }
});

// 2) Hide menu items unless the user’s preference is ON
add_action('admin_menu', function () {
    if (get_user_meta(get_current_user_id(), 'show_all_admin_menu', true) === '1') {
        return; // showing all, do nothing
    }

    global $menu;

    // Prefer slugs for reliability (titles can change or be localized)
    $hiddenMenuSlugs = [
        'edit.php',            // Posts
        'options-general.php', // Settings
        'users.php',           // Users
    ];

    foreach ($menu as $index => $item) {
        $slug = isset($item[2]) ? $item[2] : '';
        if (in_array($slug, $hiddenMenuSlugs, true)) {
            unset($menu[$index]);
        }
    }
}, 999);

// 3) Button to toggle the preference (only for admins)
add_action('admin_footer', function () {
    if (!current_user_can('manage_options')) return;

    $is_showing_all = get_user_meta(get_current_user_id(), 'show_all_admin_menu', true) === '1';
    $toggle_url = add_query_arg([
        'toggle-show-all-menu' => '1',
        '_wpnonce'             => wp_create_nonce('toggle_show_all_menu'),
    ], $_SERVER['REQUEST_URI']);

    $btn_text = $is_showing_all ? __('Show less','mprc') : __('Show all menu','mprc');
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.createElement('a');
        btn.href = <?php echo json_encode($toggle_url); ?>;
        btn.innerText = <?php echo json_encode($btn_text); ?>;
        btn.style.cssText = 'display:block;margin:20px;padding:8px 16px;background:#2271b1;color:#fff;text-align:center;border-radius:4px;text-decoration:none;';
        const target = document.querySelector('#adminmenuwrap');
        if (target) target.appendChild(btn);
    });
    </script>
    <?php
});