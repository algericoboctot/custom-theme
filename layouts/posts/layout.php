<?php if (has_post_thumbnail()) : ?>
    <div class="posts__image">
        <?php the_post_thumbnail('medium', ['class' => 'w-full h-48 object-cover']); ?>
    </div>
<?php endif; ?>
<div class="posts___content p-4">
    <div class="posts__meta text-lg font-bold text-accent-5 mb-3">
        <time datetime="<?php echo get_the_date('c'); ?>">
            <?php echo get_the_date(); ?>
        </time>
    </div>
    <h3 class="posts__title mb-2">
        <?php the_title(); ?>
    </h3>
</div>