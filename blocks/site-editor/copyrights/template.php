<?php
    // Make sure your template starts properly
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    
    // Get copyright text from ACF field with fallback to default
    $copyright_text = get_field('copyright_text');
    
    // Fallback to default if field is empty
    if (empty($copyright_text)) {
        $copyright_text = __('MB Navus. All rights reserved.', 'ctheme');
    }
    
    // Replace {year} placeholder with current year if present
    $current_year = date('Y');
    $copyright_text = str_replace('{year}', $current_year, $copyright_text);
?>

<div class="footer__copyrights">
    <p><?php echo esc_html($copyright_text); ?></p>
</div>