<?php
/**
 * Simple Wishlist Button Block Template
 */

// Get wishlist data
if (function_exists('YITH_WCWL')) {
    $wishlist_url = YITH_WCWL()->get_wishlist_url();
    $count = yith_wcwl_count_products();
} else {
    $wishlist_url = '#';
    $count = 0;
}

// Block wrapper attributes
$block_wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'wishlist-button-block'
]);
?>

<div <?php echo $block_wrapper_attributes; ?>>
    <a href="<?php echo esc_url($wishlist_url); ?>" class="wishlist-header-btn relative">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
        <span class="wishlist-text sr-only">Wishlist</span>
        <?php if ($count > 0): ?>
            <span class="wishlist-count"><?php echo $count; ?></span>
        <?php endif; ?>
    </a>
</div>