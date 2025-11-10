<?php

    // Make sure your template starts properly
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }

    // Check if we're in preview mode with preview image
    if (isset($block['data']['preview_image'])) {
        $preview_url = get_template_directory_uri() . '/blocks/global/statnumbers/' . $block['data']['preview_image'];
        echo '<img src="' . esc_url($preview_url) . '" alt="' . esc_attr($block['title']) . ' Preview" style="width: 100%; height: auto;">';
        return;
    }

    $stats = get_field('statistics');

    $class_name = '';
    if ( ! empty( $block['className'] ) ) {
        $class_name .= $block['className'];
    }
?>

<?php if(!empty($stats)) : ?>
    <div class="statistics px-4 py-8 sm:py-12 xl:py-16">
        <div class="statistics__lists max-w-7xl w-full mx-auto">
            <?php foreach( $stats as $key => $stat ) : ?>
                <div class="statistics__item <?php echo ($key % 2 === 1) ? ' even' : ''; ?>">
                    <div class="statistics__content">
                        <div class="stats-numbers">
                            <span data-current-number="<?php echo $stat['stats']?>" class="current-number">
                                <?php echo $stat['stats']; ?>
                            </span><?php echo ($stat['plus_sign']) ? '<span class="plus-sign">+</span>' : '';?>
                            <span data-max-number="<?php echo $stat['stats']; ?>" class="max-number hidden"><?php echo $stat['stats']; ?></span>
                        </div>
                        <?php if(!empty($stat['title'])) : ?>
                            <h2 class="statistics__title"><?php echo $stat['title'];?></h2>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif;