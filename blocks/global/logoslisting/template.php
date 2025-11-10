<?php

    if (isset($block['data']['preview_image'])) {
        $preview_url = get_template_directory_uri() . '/blocks/global/logoslisting/' . $block['data']['preview_image'];
        echo '<img src="' . esc_url($preview_url) . '" alt="' . esc_attr($block['title']) . ' Preview" style="width: 100%; height: auto;">';
        return;
    }

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = '';
    if (!empty($block['className'])) {
        $class_name .= ' ' . $block['className'];
    }
    if (!empty($block['align'])) {
        $class_name .= ' align' . $block['align'];
    }

    $logos = get_field('logos_listing');
    $title = get_field('logo_section_title');
    $size = 'full';
?>
<?php if ( !empty($logos) ) : ?>
    <div class="py-10 xl:py-12">
        <?php if( $title ) : ?>
            <h2 class="text-center text-[var(--wp--preset--color--accent-2)] mb-8"><?php echo $title; ?></h2>
        <?php endif; ?>
        <div class="max-w-7xl w-full mx-auto px-[80px] relative">
            <div class="logo-listings__prev swiper-button-prev"></div>
            <div class="logo-listings swiper">
                <div class="swiper-wrapper">
                    <?php
                        $count = 1;
                        foreach ( $logos as $key => $item ) :
                            $logo = $item['logo'];
                            $link = $item['link'];
                    ?>
                    <div class="swiper-slide">
                        <?php if( !empty($link) ) : ?>
                            <a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
                                <?php
                                    if( $logo ) {
                                    echo wp_get_attachment_image( $logo, 'full', false, [
                                        'loading'       => $key === 0 ? 'eager' : 'lazy',
                                        'fetchpriority' => $key === 0 ? 'high' : 'auto'
                                        ] );
                                        }
                                    ?>
                                </a>
                        <?php else: ?>
                            <?php
                                if( $logo ) {
                                    echo wp_get_attachment_image( $logo, 'full', false, [
                                        'loading'       => $key === 0 ? 'eager' : 'lazy',
                                        'fetchpriority' => $key === 0 ? 'high' : 'auto'
                                    ] );
                                }
                            ?>  
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="logo-listings__next swiper-button-next"></div>
        </div>
    </div>
<?php endif; ?>