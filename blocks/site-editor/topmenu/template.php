<?php
    /**
     * Top Menu Block Template
     * Path: /blocks/site-editor/topmenu/template.php
     */

    // Create id attribute allowing for custom "anchor" value
    $block_id = 'mainmenu-' . $block['id'];
    if (!empty($block['anchor'])) {
        $block_id = $block['anchor'];
    }

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = 'mainmenu';
    if (!empty($block['className'])) {
        $class_name .= ' ' . $block['className'];
    }
    if (!empty($block['align'])) {
        $class_name .= ' align' . $block['align'];
    }

?>

<div class="top-menu <?php echo $class_name; ?>">
    <nav class="top-header__nav hide-on-medium" role="navigation" aria-label="Main Navigation">
        <?php
            // Output the footer navigation
            wp_nav_menu(
                [
                    'container'         => 'div',
                    'container_class'   => 'top-menu',
                    'menu_class'        => 'top-menu__list',
                    'theme_location'    => 'top_menu',
                    'walker'            => new Toggle_Menu_Walker()
                ]
            );
        ?>
    </nav>
</div>