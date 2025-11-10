<?php

    // Check if we're in preview mode with preview image
    if (isset($block['data']['preview_image'])) {
        $preview_url = get_template_directory_uri() . '/blocks/global/gallery/' . $block['data']['preview_image'];
        echo '<img src="' . esc_url($preview_url) . '" alt="' . esc_attr($block['title']) . ' Preview" style="width: 100%; height: auto;">';
        return;
    }

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = '';
    if (!empty($block['className'])) {
        $class_name .= $block['className'];
    }

    $gallery_id = 'gallery-' . uniqid();
    $enable_masonry = get_field('fixed_masonry_layout');
    $gallery = get_field('gallery') ?? [];
    $display_count = get_field('images_to_show');
    $shuffle = get_field('shuffle_images');
    $lightbox_loop = get_field('lightbox_loop');
    $show_counter = get_field('show_counter');
    $show_captions = get_field('show_captions');
    $shuffle_scope = 'request';
    $col_class = !$enable_masonry ? "columns-2 md:columns-3 xl:columns-4": "grid-cols-2 md:grid-cols-3 xl:grid-cols-4";
    $gap = get_field('gap_between_images');
    $gap_class = "{$gap}";
    $item_class = !$enable_masonry ? "mb-[{$gap}px]" : "mb-0";

    // Process images and get full data
    $processed_images = [];

    if (!empty($gallery)) {
        foreach ($gallery as $image) {
            if (isset($image['id'])) {
                $attachment = get_post($image['id']);
                if ($attachment) {
                    $image_data = wp_get_attachment_image_src($image['id'], 'full');
                    $thumbnail_data = wp_get_attachment_image_src($image['id'], 'medium_large');
                    
                    if ($image_data) {
                        $processed_images[] = array(
                            'id' => $image['id'],
                            'url' => $image_data[0],
                            'width' => $image_data[1],
                            'height' => $image_data[2],
                            'thumbnail' => $thumbnail_data ? $thumbnail_data[0] : $image_data[0],
                            'alt' => get_post_meta($image['id'], '_wp_attachment_image_alt', true),
                            'caption' => $attachment->post_excerpt,
                            'title' => $attachment->post_title
                        );
                    }
                }
            }
        }
    }

    // Apply shuffle if enabled
    if ($shuffle && !empty($processed_images)) {
        $seed = '';
        switch ($shuffle_scope) {
            case 'day':
                $seed = date('Y-m-d');
                break;
            case 'user':
                $seed = wp_get_session_token();
                break;
            default: // request
                $seed = uniqid();
        }
        
        // Create deterministic shuffle based on seed
        srand(crc32($seed . $gallery_id));
        shuffle($processed_images);
        srand(); // Reset random seed
    }

    // Get display images (first N) - MOVED AFTER SHUFFLE
    $display_images = array_slice($processed_images, 0, $display_count);

    // Prepare lightbox data - PASS ONLY DISPLAY IMAGES
    $lightbox_data = array(
        'galleryId' => $gallery_id,
        'images' => $display_images, // Changed from $processed_images to $display_images
        'display_count' => count($display_images), // Use actual count
        'options' => array(
            'loop' => $lightbox_loop,
            'showCounter' => $show_counter,
            'showCaption' => $show_captions,
            'gap' => $gap
        )
    );
    
?>

<div 
    class="masonry-gallery max-w-7xl mx-auto"
    data-gallery='<?php echo esc_attr(json_encode($lightbox_data)); ?>'
>
    <?php if (!empty($display_images)): ?>
        <div class="masonry-gallery__grid <?php echo $class_name; ?> <?php echo !$enable_masonry ? 'block' : 'masonry-gallery--fixed'; ?> <?php echo esc_attr($col_class); ?> gap-[<?php echo esc_attr($gap_class); ?>px]">
            <?php foreach ($display_images as $index => $image): ?>
                <figure class="masonry-gallery__item mb-2.5" data-index="<?php echo $index; ?>">
                    <a href="<?php echo esc_url($image['url']); ?>" 
                       class="masonry-gallery__link"
                       data-pswp-width="<?php echo esc_attr($image['width']); ?>"
                       data-pswp-height="<?php echo esc_attr($image['height']); ?>"
                       data-pswp-src="<?php echo esc_url($image['url']); ?>"
                       data-pswp-srcset="<?php echo esc_attr(wp_get_attachment_image_srcset($image['id'])); ?>"
                       data-pswp-sizes="<?php echo esc_attr(wp_get_attachment_image_sizes($image['id'])); ?>"
                       aria-label="<?php echo esc_attr(sprintf(__('View %s', 'fornett'), $image['title'] ?: $image['alt'])); ?>">
                        <img src="<?php echo esc_url($image['thumbnail']); ?>"
                             alt="<?php echo esc_attr($image['alt']); ?>"
                             loading="lazy"
                             class="masonry-gallery__image"
                             width="<?php echo esc_attr($image['width']); ?>"
                             height="<?php echo esc_attr($image['height']); ?>">
                        <div class="masonry-gallery__overlay"></div>
                    </a>
                    
                    <?php if ($show_captions && !empty($image['caption'])): ?>
                        <figcaption class="masonry-gallery__caption">
                            <?php echo esc_html($image['caption']); ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="masonry-gallery__empty">
            <p><?php esc_html_e('No images selected for this gallery.', 'fornett'); ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Simple Lightbox Container -->
<div class="lightbox" id="lightbox" style="display: none;">
    <div class="lightbox__overlay"></div>
    <div class="lightbox__content">
        <button class="lightbox__close" aria-label="Close lightbox">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <button class="lightbox__prev" aria-label="Previous image">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <button class="lightbox__next" aria-label="Next image">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        
        <div class="lightbox__image-container">
            <img class="lightbox__image" src="" alt="">
        </div>
        
        <div class="lightbox__caption"></div>
        <div class="lightbox__counter"></div>
    </div>
</div>