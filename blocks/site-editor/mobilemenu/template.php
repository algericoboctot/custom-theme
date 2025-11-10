<?php
    /**
     * Main Menu Block Template
     * Path: /blocks/site-editor/mainmenu/template.php
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

<div class="mobile-menu hidden <?php echo $class_name; ?>">
    <nav class="mobile-menu__nav role="navigation" aria-label="Mobile Navigation">
        <?php
            // Output the footer navigation
            wp_nav_menu(
                [
                    'container'         => 'div',
                    'container_class'   => 'mobile-menu__wrap',
                    'menu_class'        => 'mobile-menu__list',
                    'theme_location'    => 'main_menu',
                    'walker'            => new Toggle_Menu_Walker()
                ]
            );
        ?>
    </nav>
</div>

<?php