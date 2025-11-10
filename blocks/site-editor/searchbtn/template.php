
<?php
        // Create class attribute allowing for custom "className" and "align" values
        $class_name = 'mainmenu';
        if (!empty($block['className'])) {
            $class_name .= ' ' . $block['className'];
        }
?>
<button
    class="header__searchbtn <?php echo $class_name; ?>"
    aria-label="<?php esc_attr_e('Search', 'ctheme'); ?>"
>
    <span class="sr-only"><?php _e('Search', 'ctheme'); ?></span>
</button>