<div class="search-results">
    <?php if ( have_posts() ) : ?>
        <div class="search-results__wrapper">
            <div class="search-results__container">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('news-tabs__item mb-2 md:mb-4 lg:mb-6 xl:mb-12'); ?>>
                        <a href="<?php the_permalink();?>">
                            <div class="search-results__info">
                                <span class="font-semibold text-lg"><?php echo get_the_date('Y-m-d') ?></span>
                                <h3>
                                    <?php the_title();?>
                                </h3>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
                <?php the_posts_pagination(); ?>
            </div>
        </div>
    <?php else : ?>
        <div class="search-results__wrapper">
            <div class="search-results__container ">
                <p><?php echo __('Sorry, but nothing matched your search terms. Please try again with different keywords.','ctheme');?></p>
            </div>
        </div>
    <?php endif; ?>
</div>