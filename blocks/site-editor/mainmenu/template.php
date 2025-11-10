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

<div class="main-menu <?php echo $class_name; ?>">
    <nav class="main-header__nav max-md:hidden" role="navigation" aria-label="Main Navigation">
        <?php
            // Output the footer navigation
            wp_nav_menu(
                [
                    'container'         => 'div',
                    'container_class'   => 'header-menu',
                    'menu_class'        => 'header-menu__list',
                    'theme_location'    => 'main_menu',
                    'walker'            => new Toggle_Menu_Walker()
                ]
            );
        ?>
    </nav>
    <button class="mobile-menu__button relative max-md:block hidden" type="button" aria-expanded="false">
        <svg class="burger-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 16H28" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 8H28" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 24H28" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg class="close-icon hidden" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M24 8L8 24" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8 8L24 24" class="stroke-white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="sr-only">button</span>
    </button>
</div>

<?php