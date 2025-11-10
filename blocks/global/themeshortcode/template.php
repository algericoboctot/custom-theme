<?php
    $shortcode = get_field('shortcode');

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = '';
    if (!empty($block['className'])) {
        $class_name .= ' ' . $block['className'];
    }
?>

<div class="<?php echo $class_name; ?>">
    <?php echo do_shortcode( $shortcode ); ?>
</div>