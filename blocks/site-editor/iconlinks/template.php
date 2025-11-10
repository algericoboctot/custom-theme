<?php

// Create id attribute allowing for custom "anchor" value
$block_id = 'mainmenu-' . $block['id'];
if (!empty($block['anchor'])) {
    $block_id = $block['anchor'];
}

// Get the description field
$description = get_field('description');

// Get the icons repeater field
$icons = get_field('icons');

// Create class attribute allowing for custom "className" and "align" values
$class_name = 'mainmenu';
if (!empty($block['className'])) {
    $class_name .= ' ' . $block['className'];
}


// Check if we have content to display
if ($description || $icons) : ?>
    <div class="header-icons-block <?php echo $class_name; ?>">
        <?php if ($description) : ?>
            <div class="header-description">
                <p><?php echo esc_html($description); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($icons && is_array($icons)) : ?>
            <div class="header-icons-grid">
                <?php foreach ($icons as $icon) : ?>
                    <?php 
                        // Ensure we have valid data
                        if (!is_array($icon) || !isset($icon['icon'])) {
                            continue;
                        }
                        
                        $icon_data = $icon['icon'];
                        $icon_link_data = isset($icon['link']) ? $icon['link'] : '';
                        
                        // Debug: Log the icon data
                        if (defined('WP_DEBUG') && WP_DEBUG) {
                            error_log('Header Icons Debug - Icon data: ' . print_r($icon_data, true));
                            error_log('Header Icons Debug - Link data: ' . print_r($icon_link_data, true));
                        }
                        
                        // Check for zero height issue
                        if (defined('WP_DEBUG') && WP_DEBUG && !empty($image_url)) {
                            $image_info = getimagesize($image_url);
                            if ($image_info && $image_info[1] == 0) {
                                error_log('Header Icons Warning - Image has zero height: ' . $image_url);
                            }
                        }
                        
                        // Extract image URL (since icon field returns URL string)
                        $image_url = is_string($icon_data) ? $icon_data : '';
                        
                        // Extract link URL (since link field returns array)
                        $icon_link = '';
                        if (is_array($icon_link_data) && isset($icon_link_data['url'])) {
                            $icon_link = $icon_link_data['url'];
                        } elseif (is_string($icon_link_data)) {
                            $icon_link = $icon_link_data;
                        }
                        
                        // Skip if no valid image URL
                        if (empty($image_url)) {
                            continue;
                        }
                    ?>
                    
                    <div class="header-icon-item">
                        <?php if (!empty($icon_link)) : ?>
                            <a href="<?php echo esc_url($icon_link); ?>" class="header-icon-link">
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     alt=""
                                     class="header-icon-image">
                            </a>
                        <?php else : ?>
                            <img src="<?php echo esc_url($image_url); ?>" 
                                 alt=""
                                 class="header-icon-image">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
