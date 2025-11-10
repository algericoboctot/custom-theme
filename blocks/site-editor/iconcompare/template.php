<?php

// Get compare data
if (class_exists('YITH_Woocompare_Frontend')) {
    $compare_page_id = get_option('yith_woocompare_compare_page_id');
    $compare_url = $compare_page_id ? get_permalink($compare_page_id) : '#';
    
    // Get compare count from cookie or session
    $compare_list = isset($_COOKIE['yith_woocompare_list']) ? json_decode(stripslashes($_COOKIE['yith_woocompare_list']), true) : array();
    $count = is_array($compare_list) ? count($compare_list) : 0;
} else {
    $compare_url = '#';
    $count = 0;
}

// Block wrapper attributes
$block_wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'compare-button-block'
]);
?>

<div <?php echo $block_wrapper_attributes; ?>>
    <a href="<?php echo esc_url($compare_url); ?>" class="header__compare-btn relative">
        <svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="inactive">
            <path d="M17.3536 14.3536C17.5488 14.1583 17.5488 13.8417 17.3536 13.6464L14.1716 10.4645C13.9763 10.2692 13.6597 10.2692 13.4645 10.4645C13.2692 10.6597 13.2692 10.9763 13.4645 11.1716L16.2929 14L13.4645 16.8284C13.2692 17.0237 13.2692 17.3403 13.4645 17.5355C13.6597 17.7308 13.9763 17.7308 14.1716 17.5355L17.3536 14.3536ZM17 14L17 13.5L2 13.5L2 14L2 14.5L17 14.5L17 14Z" fill="#ffffff"></path>
            <path d="M1.64645 4.64644C1.45118 4.84171 1.45118 5.15829 1.64645 5.35355L4.82843 8.53553C5.02369 8.73079 5.34027 8.73079 5.53553 8.53553C5.73079 8.34027 5.73079 8.02369 5.53553 7.82843L2.70711 5L5.53553 2.17157C5.7308 1.97631 5.7308 1.65973 5.53553 1.46446C5.34027 1.2692 5.02369 1.2692 4.82843 1.46446L1.64645 4.64644ZM17 5L17 4.5L2 4.5L2 5L2 5.5L17 5.5L17 5Z" fill="#ffffff"></path>
        </svg>
        <span class="header__compare-text sr-only">Compare</span>
        <?php if ($count > 0): ?>
            <span class="header__compare-count"><?php echo $count; ?></span>
        <?php endif; ?>
    </a>
</div>