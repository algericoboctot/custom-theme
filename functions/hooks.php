<?php

/**
 * Output style to change logo on login
 *
 * @return void
 */

add_action( 'login_head', 'mb_login_logo' );

function mb_login_logo() {
    $logo = get_field('logo','options');
    $bg_style = get_field('background_style','options');
    $color = get_field('logo_color','options');
    $image = get_field('logo_image','options');
    $bg_option = "";

    if ( $bg_style == 'color' ) {
        $bg_option = 'background-color: ' . $color . ';';
    } else {
        $bg_option = 'background-image: url(' . $image . ');';
    }
?>
    <style>
        body.login {
            <?php echo $bg_option; ?>
        }
        .login h1 {
            height: 92px;
            display: block;
        }
        h1 a {
            background-image:url('<?php echo $logo ?>') !important;
            background-size: 225px auto !important;
            background-position: center center !important;
            height: 100% !important;
            width: auto !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        .login form { 
            margin-top: 25px !important;
            border: 1px solid #000000;
        }

        #nav {
            float: right !important;
            width: 50%;
            padding: 0 !important;
            text-align: right !important;
        }

        #backtoblog {
            float: left !important;
            width: 50%;
            padding: 0 !important;
            margin-top: 24px;
        }
        .wpml-login-ls {
            clear: both;
            width: 320px;
        }

        #login {
            width: 350px;
        }

        #wpml-login-ls-form {
            flex-direction: row;
            align-items: center;
            display: flex;
        }

        #wpml-login-ls-form label {
            margin-right: 15px;
        }
        .wp-core-ui select {
            margin-right: auto;
        }
        #loginform {
            border-radius: 9px;
        }
        #loginform #wp-submit {
            background-color: #e71332;
            border-color: #e71332;
            border-radius: 32px;
            transition: all .25s ease-in;
            &:hover {
                background-color: #fff;
                border-color: #949494;
                color: #000;
            }
        }
        #loginform #user_login,
        #loginform #user_pass {
            border-radius: 32px;
            border: 1px solid #949494;
            &:focus {
                border-color: #e71332;
                outline: none;
                box-shadow: none;
            }
        }
        #loginform #user_login {
            padding-left: 20px;
            padding-right: 20px;
        }
        #loginform #user_pass {
            padding-left: 20px;
            padding-right: 30px;
        }
        .language-switcher {
            width: 350px;
        }

        .login-action-lostpassword .notice-info {
            margin-top: 30px;
        }
        .login-action-login #backtoblog,
        .login-action-login .privacy-policy-page-link ,
        .login-action-login .language-switcher,
        .login-action-lostpassword #backtoblog,
        .login-action-lostpassword .privacy-policy-page-link,
        .login-action-lostpassword .language-switcher {
            display: none !important;
        }

        .login #nav a {
            color: #fff;
            &:hover {
                color: #e71332;
            }
        }
    </style>
<?php
}

class Toggle_Menu_Walker extends Walker_Nav_Menu {
    
    // Start Level - wrap submenu in ul
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu level-$depth\">\n";
    }
    
    // End Level
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    // Start Element - each menu item
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Check if item has children
        $has_children = in_array('menu-item-has-children', $classes);
        
        // Add custom classes
        if ($has_children) {
            $classes[] = 'has-submenu';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        $item_output = isset($args->before) ? $args->before : '';
        
        // Start the wrapper div
        $item_output .= '<div class="menu-item">';
        
        $item_output .= '<a' . $attributes . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        $item_output .= '</a>';
        
        // Add arrow span for items with children (inside the wrapper div)
        if ($has_children) {
            $item_output .= '<span class="menu-arrow"></span>';
        }
        
        // Close the wrapper div
        $item_output .= '</div>';
        
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    // End Element
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

// Add 'no-js' class to <html> tag
add_filter('language_attributes', function($output) {
    if (strpos($output, 'class=') === false) {
        $output .= ' class="no-js"';
    } else {
        $output = str_replace('class="', 'class="no-js ', $output);
    }
    return $output;
});

// Add script to replace 'no-js' with 'js' - but DON'T run it immediately
add_action('wp_head', function() {
    ?>
    <script>
        // Wait for the DOM to be ready before replacing the class
        document.addEventListener('DOMContentLoaded', function() {
            document.documentElement.className = 
                document.documentElement.className.replace(/\bno-js\b/, 'js');
        });
    </script>
    <?php
}, 1);

/**
 * Populate Footer Menu selector with available WordPress menus
 */
add_filter('acf/load_field/name=selected_menu', 'populate_footer_menu_choices');
function populate_footer_menu_choices($field) {
    // Only process if this is the selected_menu field
    if (!isset($field['name']) || $field['name'] !== 'selected_menu') {
        return $field;
    }
    
    // Get all registered menus
    $menus = wp_get_nav_menus();
    
    // Clear existing choices
    $field['choices'] = array();
    
    // Add each menu as a choice
    if ($menus && !is_wp_error($menus)) {
        foreach ($menus as $menu) {
            $field['choices'][$menu->term_id] = $menu->name;
        }
    }
    
    // Also add theme locations as options
    $locations = get_nav_menu_locations();
    $registered_locations = get_registered_nav_menus();
    
    if ($locations && $registered_locations) {
        foreach ($registered_locations as $location => $name) {
            if (isset($locations[$location])) {
                $menu = wp_get_nav_menu_object($locations[$location]);
                if ($menu && !is_wp_error($menu)) {
                    $field['choices']['location_' . $location] = $name . ' (Theme Location)';
                }
            }
        }
    }
    
    return $field;
}