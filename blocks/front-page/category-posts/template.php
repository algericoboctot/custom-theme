<?php
    /**
    * Category Posts Block Template
    * Displays current category and its 3 latest posts
    */

    // Check if we're in preview mode with preview image
    if (isset($block['data']['preview_image'])) {
        $preview_url = get_template_directory_uri() . '/blocks/front-page/category-posts/' . $block['data']['preview_image'];
        echo '<img src="' . esc_url($preview_url) . '" alt="' . esc_attr($block['title']) . ' Preview" style="width: 100%; height: auto;">';
        return;
    }

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = '';
    if (!empty($block['className'])) {
        $class_name .= ' ' . $block['className'];
    }

    // Get the category from ACF field
    $category = get_field('category_posts');

    echo '<div class="'.$class_name.' ">';

    if ($category) {
        // Get category information
        $category_id = $category->term_id;
        $category_name = $category->name;
        $category_link = get_term_link($category);
        $category_description = $category->description;
        
        // Query for 3 latest posts from this category
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 3,
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $category_id,
                ),
            ),
            'orderby' => 'date',
            'order' => 'DESC',
        );
        
        $category_posts = new WP_Query($args);
        
        if ($category_posts->have_posts()) : ?>
            <div class="category-posts__block px-4 py-8 sm:py-12 xl:py-16">
                <div class="max-w-7xl w-full mx-auto">
                    <h2 class="category-posts__sec-title">
                        <?php echo esc_html($category_name); ?>
                    </h2>
                    <!-- Posts Grid -->
                    <div class="category-posts__lists grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <?php while ($category_posts->have_posts()) : $category_posts->the_post(); ?>
                            <a class="category-posts__item" href="<?php the_permalink(); ?>">
                                <?php get_template_part('layouts/posts/layout'); ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                    <div class="category-posts__link wp-block-button">
                        <a href="<?php echo esc_url($category_link); ?>" class="wp-block-button__link">
                            <?php echo __('View All').' '.esc_html($category_name); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="category-posts__block">
                <h2 class="category-posts__title">
                    <?php echo esc_html($category_name); ?>
                </h2>
                <div class="category-posts__lin wp-block-button">
                    <a href="<?php echo esc_url($category_link); ?>" class="wp-block-button__link">
                        <?php echo __('View All').' '.esc_html($category_name); ?>
                    </a>
                </div>
            </div>
        <?php endif;
        
        // Reset post data
        wp_reset_postdata();
        
    } else {
        // No category selected
        echo '<div class="category-posts-block">';
        echo '<p>Please select a category in the block settings.</p>';
        echo '</div>';
    }
echo '</div>';
?>
