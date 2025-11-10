<?php
    /**
     * Category Posts Tab Block Template
     * Displays categories with their 3 latest posts and category links
     */

    // Check if we're in preview mode with preview image
    if (isset($block['data']['preview_image'])) {
        $preview_url = get_template_directory_uri() . '/blocks/front-page/category-posts-tab/' . $block['data']['preview_image'];
        echo '<img src="' . esc_url($preview_url) . '" alt="' . esc_attr($block['title']) . ' Preview" style="width: 100%; height: auto;">';
        return;
    }

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = '';
    if (!empty($block['className'])) {
        $class_name .= ' ' . $block['className'];
    }
    

    // Get all categories
    $categories = get_categories([
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => true,
    ]);

    if ($categories && !empty($categories)) {
?>
    
    <div class="category-posts-tab__block px-4 py-12 xl:py-16 <?php echo $class_name; ?>" data-category-tabs>
        <div class="max-w-7xl w-full mx-auto">
            <!-- Tab Navigation -->
            <div class="category-posts-tab__navigation mb-8">
                <div class="flex flex-wrap">
                    <?php foreach ($categories as $index => $category) : 
                        $category_id = $category->term_id;
                        $category_name = $category->name;
                        $category_link = get_term_link($category);
                        $is_active = $index === 0 ? 'active' : '';
                    ?>
                        <button 
                            class="category-tab-btn px-4 py-2 md:px-6 md:py-3 text-sm md:text-base font-medium transition-all duration-300 <?php echo $is_active; ?>"
                            data-category-id="<?php echo esc_attr($category_id); ?>"
                            data-category-name="<?php echo esc_attr($category_name); ?>"
                            data-category-link="<?php echo esc_url($category_link); ?>"
                        >
                            <?php echo esc_html($category_name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tab Contents -->
            <div class="category-posts-tab__contents">
                <?php foreach ($categories as $index => $category) : 
                    $category_id = $category->term_id;
                    $category_name = $category->name;
                    $category_link = get_term_link($category);
                    $is_active = $index === 0 ? 'active' : '';
                    
                    // Query for posts from this category
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
                    
                    $posts_query = new WP_Query($args);
                ?>
                    <div class="category-tab-content <?php echo $is_active; ?>" data-category-content="<?php echo esc_attr($category_id); ?>">
                        <?php if ($posts_query->have_posts()) : ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 md:justify-center lg:grid-cols-3 gap-6 mb-8">
                                <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                                    <article class="category-posts-tab__item bg-accent-2 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                                        <a href="<?php the_permalink(); ?>" class="block">
                                            <?php get_template_part('layouts/posts/layout'); ?>
                                        </a>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                            
                            <!-- Category Link Button -->
                            <div class="category-posts__link wp-block-button">
                                <a href="<?php echo esc_url($category_link); ?>" class="wp-block-button__link inline-flex items-center">
                                    View All <?php echo esc_html($category_name); ?> Posts
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        <?php else : ?>
                            <div class="text-center py-12">
                                <p class="text-accent-4 text-lg">No posts found in <?php echo esc_html($category_name); ?> category.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php 
                    wp_reset_postdata();
                endforeach; ?>
            </div>
        </div>
    </div>
    
    <?php
}
?>
