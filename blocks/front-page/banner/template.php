<?php

    // Check if we're in preview mode with preview image
    if (isset($block['data']['preview_image'])) {
        $preview_url = get_template_directory_uri() . '/blocks/front-page/banner/' . $block['data']['preview_image'];
        echo '<img src="' . esc_url($preview_url) . '" alt="' . esc_attr($block['title']) . ' Preview" style="width: 100%; height: auto;">';
        return;
    }
    // Create class attribute allowing for custom "className" and "align" values
    $class_name = '';
    if (!empty($block['className'])) {
        $class_name .= ' ' . $block['className'];
    }

    $banner = get_field('banner_list');
    $slider_settings = get_field('slider_settings');
    $loop = isset($slider_settings['loop']) ? (bool) $slider_settings['loop'] : true;
    $autoplay = isset($slider_settings['autoplay']) ? (bool) $slider_settings['autoplay'] : true;
    $delay = isset($slider_settings['delay']) && $slider_settings['delay'] !== '' ? (int) $slider_settings['delay'] : 5000;
    $pause_on_hover = isset($slider_settings['pause_on_hover']) ? (bool) $slider_settings['pause_on_hover'] : true;
    $speed = isset($slider_settings['speed']) && $slider_settings['speed'] !== '' ? (int) $slider_settings['speed'] : 300;
    $effect = !empty($slider_settings['effect']) ? $slider_settings['effect'] : 'fade';
    $show_pagination = isset($slider_settings['pagination']) ? (bool) $slider_settings['pagination'] : true;
    $size = 'full';
?>
<?php if ( !empty($banner) ) : ?>
    <div class="<?php echo $class_name; ?>">
        <div 
            class="hero-banner swiper"
            data-loop="<?php echo $loop ? 'true' : 'false'; ?>"
            data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
            data-delay="<?php echo esc_attr($delay); ?>"
            data-pause-on-hover="<?php echo $pause_on_hover ? 'true' : 'false'; ?>"
            data-speed="<?php echo esc_attr($speed); ?>"
            data-effect="<?php echo esc_attr($effect); ?>"
            data-pagination="<?php echo $show_pagination ? 'true' : 'false'; ?>"
        >
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper z-0">
                <!-- Slides -->
                <?php
                    $count = 1;
                    foreach ( $banner as $key => $banner_item ) :
                        $image = $banner_item['image'];
                ?>
                <div class="swiper-slide">
                    <div class="hero-banner__image bg-[var(--wp--preset--color--base)] relative h-[340px] md:h-[420px] lg:h-[560px]">
                        <?php
                            if( $image ) {
                                echo wp_get_attachment_image($image, 'large', false, [
                                    'loading'       => $key === 0 ? 'eager' : 'lazy',
                                    'class'         => 'absolute inset-0 block md:hidden w-full h-full object-cover opacity-60',
                                    'fetchpriority' => $key === 0 ? 'high' : 'auto'
                                ] );
                            }
                        ?>
                        <?php
                            if( $image ) {
                                echo wp_get_attachment_image($image, 'full', false, [
                                    'loading'       => $key === 0 ? 'eager' : 'lazy',
                                    'class'         => 'absolute inset-0 hidden md:block w-full h-full object-cover opacity-60',
                                    'fetchpriority' => $key === 0 ? 'high' : 'auto'
                                ] );
                            }
                        ?>
                    </div>
                    <div class="px-4 hero-banner__info absolute bottom-0 left-0 w-full h-full">
                        <div class="max-w-7xl w-full mx-auto h-full flex flex-col justify-center">
                            <div class="w-full lg:w-1/2">
                                <?php if (!empty($banner_item['title'])) : ?>
                                    <h2 class="hero-banner__title text-[var(--wp--preset--color--accent-2)] mb-4 md:mb-6"><?php echo $banner_item['title']; ?></h2>
                                <?php endif; ?>
                                <?php if (!empty($banner_item['description'])) : ?>
                                    <div class="hero-banner__desc text-[var(--wp--preset--color--accent-2)] mb-4 md:mb-8 lg:mb-12">
                                        <?php echo $banner_item['description']; ?>
                                    </div>
                                <?php endif; ?>
                                <?php
                                    if ($banner_item['include_cta_button']) :
                                        if (!empty($banner_item['button'])) : 
                                ?>
                                    <div class="hero-banner__link wp-block-button h-auto">
                                        <a 
                                            class="wp-block-button__link inline-block"
                                            href="<?php echo $banner_item['button']['url']; ?>"
                                            <?php echo !empty($banner_item['link']['target']) ? 'target="_blank"' : '';?>
                                        >
                                            <?php echo $banner_item['button']['title']; ?>
                                        </a>
                                    </div>
                                <?php 
                                        endif;
                                    endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                ...
            </div>
            <!-- If we need pagination -->
            <?php if ( $show_pagination ) : ?>
            <div class="swiper-pagination z-10 hero-banner__pagination"></div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>