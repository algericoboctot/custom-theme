<?php

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = '';
    if (!empty($block['className'])) {
        $class_name .= $block['className'];
    }

    $logo_type = get_field('logo_type');
    $image = get_field('logo_image');
    $text = get_field('logo_text');
    $color_text = get_field('text_color');

    $logo_image = $logo_type === "image" ? $image : NO_IMAGE_URL;

?>

<div class="site-logo">
    <a class="site-logo__link" href="<?php echo site_url();?>">
        <?php if ($logo_type === 'image') : ?>
            <span class="site-logo__image">
                <img class="w-full h-full object-contain absolute left-0 top-0" src="<?php echo $logo_image; ?>" loading="eager" fetchpriority='high' alt="<?php echo get_bloginfo('name');?>">
            </span>
        <?php endif; ?>
        <?php if ($logo_type === 'text') : ?>
            <span class="site-logo__text" style="color: <?php echo $color_text;?>"><?php echo $text; ?></span>
        <?php endif; ?>
    </a>
</div>