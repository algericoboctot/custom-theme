<?php
    /**
     * Footer Menu Block Template
     * Path: /blocks/site-editor/footermenu/template.php
     */

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = '';
    if (!empty($block['className'])) {
        $class_name .= ' ' . $block['className'];
    }
    if (!empty($block['align'])) {
        $class_name .= ' align' . $block['align'];
    }

?>

<nav class="footermenu__nav" role="navigation" aria-label="Main Navigation">
    <?php
        // Get selected menu from ACF field
        $selected_menu = get_field('selected_menu');
        
        // Prepare menu arguments
        $menu_args = [
            'container'         => 'div',
            'container_class'   => 'footermenu',
            'menu_class'        => 'footermenu__list ' . $class_name,
        ];
        
        // Use selected menu if available, otherwise fall back to theme location
        if (!empty($selected_menu)) {
            // Convert to string for comparison
            $selected_menu_str = (string) $selected_menu;
            
            // Check if it's a theme location (starts with 'location_')
            if (strpos($selected_menu_str, 'location_') === 0) {
                $location = str_replace('location_', '', $selected_menu_str);
                $menu_args['theme_location'] = $location;
            } else {
                // Use menu ID (ensure it's an integer)
                $menu_args['menu'] = absint($selected_menu);
            }
        } else {
            // Fallback to default theme location for backward compatibility
            $menu_args['theme_location'] = 'footer_menu';
        }
        
        // Output the footer navigation
        wp_nav_menu($menu_args);
    ?>
</nav>