<?php
/**
 * ACF Block Helper Functions
 * Utility functions for working with ACF blocks
 */

class ACF_Block_Helper {
    
    /**
     * Helper function to get block template part
     */
    public static function get_template($block_name, $variables = []) {
        $template_path = get_template_directory() . "/template-parts/blocks/{$block_name}.php";
        
        if (file_exists($template_path)) {
            // Extract variables for use in template
            if (!empty($variables)) {
                extract($variables);
            }
            
            include $template_path;
        }
    }
    
    /**
     * Helper function to get ACF field with fallback
     */
    public static function get_field_with_fallback($field_name, $default = '', $post_id = null) {
        $value = get_field($field_name, $post_id);
        return !empty($value) ? $value : $default;
    }
    
    /**
     * Helper function to render ACF block wrapper
     */
    public static function render_block_wrapper($block, $content = '', $is_preview = false) {
        $block_id = 'block-' . $block['id'];
        $class_name = 'acf-block acf-block-' . str_replace('acf/', '', $block['name']);
        
        if (!empty($block['className'])) {
            $class_name .= ' ' . $block['className'];
        }
        
        if (!empty($block['align'])) {
            $class_name .= ' align' . $block['align'];
        }
        
        if ($is_preview) {
            $class_name .= ' is-preview';
        }
        
        printf(
            '<div id="%s" class="%s">%s</div>',
            esc_attr($block_id),
            esc_attr($class_name),
            $content
        );
    }
    
    /**
     * Get block context information
     */
    public static function get_block_context($block) {
        $context = [
            'id' => $block['id'] ?? '',
            'name' => $block['name'] ?? '',
            'is_preview' => isset($block['preview']) && $block['preview'],
            'align' => $block['align'] ?? '',
            'anchor' => $block['anchor'] ?? '',
            'class_name' => $block['className'] ?? '',
            'post_id' => get_the_ID(),
        ];
        
        return $context;
    }
    
    /**
     * Check if block has specific field value
     */
    public static function has_field_value($field_name, $block_id = null) {
        $value = get_field($field_name, $block_id);
        
        if (is_array($value)) {
            return !empty($value);
        }
        
        return !empty(trim($value));
    }
    
    /**
     * Get responsive image from ACF image field
     */
    public static function get_responsive_image($image_field, $size = 'full', $class = '', $alt = '') {
        if (empty($image_field)) {
            return '';
        }
        
        $image_id = is_array($image_field) ? $image_field['ID'] : $image_field;
        
        if (empty($image_id)) {
            return '';
        }
        
        $alt_text = $alt ?: get_post_meta($image_id, '_wp_attachment_image_alt', true);
        
        return wp_get_attachment_image($image_id, $size, false, [
            'class' => $class,
            'alt' => $alt_text,
            'loading' => 'lazy'
        ]);
    }
    
    /**
     * Safely get repeater field count
     */
    public static function get_repeater_count($field_name, $post_id = null) {
        if (!function_exists('get_field')) {
            return 0;
        }
        
        $repeater = get_field($field_name, $post_id);
        
        if (!is_array($repeater)) {
            return 0;
        }
        
        return count($repeater);
    }
    
    /**
     * Generate unique block ID
     */
    public static function generate_block_id($block_name, $suffix = '') {
        $base_id = sanitize_title($block_name);
        
        if (!empty($suffix)) {
            $base_id .= '-' . sanitize_title($suffix);
        }
        
        $unique_id = $base_id . '-' . wp_generate_uuid4();
        
        return substr($unique_id, 0, 50); // Limit length
    }
    
    /**
     * Get block anchor with fallback to generated ID
     */
    public static function get_block_anchor($block, $fallback_prefix = 'block') {
        if (!empty($block['anchor'])) {
            return sanitize_title($block['anchor']);
        }
        
        // Generate fallback anchor
        $block_name = str_replace('acf/', '', $block['name'] ?? '');
        $block_id = $block['id'] ?? '';
        
        return $fallback_prefix . '-' . $block_name . '-' . substr($block_id, -8);
    }
    
    /**
     * Check if we're in block editor preview
     */
    public static function is_preview($block = null) {
        if ($block && isset($block['preview'])) {
            return $block['preview'];
        }
        
        // Alternative check
        return defined('REST_REQUEST') && REST_REQUEST && 
               isset($_GET['context']) && $_GET['context'] === 'edit';
    }
    
    /**
     * Get block classes array
     */
    public static function get_block_classes($block, $additional_classes = []) {
        $classes = ['acf-block'];
        
        // Add block name class
        if (!empty($block['name'])) {
            $classes[] = 'acf-block-' . str_replace('acf/', '', $block['name']);
        }
        
        // Add custom class name
        if (!empty($block['className'])) {
            $classes[] = $block['className'];
        }
        
        // Add alignment class
        if (!empty($block['align'])) {
            $classes[] = 'align' . $block['align'];
        }
        
        // Add preview class
        if (self::is_preview($block)) {
            $classes[] = 'is-preview';
        }
        
        // Add additional classes
        if (!empty($additional_classes)) {
            $classes = array_merge($classes, (array)$additional_classes);
        }
        
        return array_filter($classes);
    }
    
    /**
     * Render block classes as string
     */
    public static function render_block_classes($block, $additional_classes = []) {
        $classes = self::get_block_classes($block, $additional_classes);
        return implode(' ', $classes);
    }
    
    /**
     * Get block inline styles
     */
    public static function get_block_styles($block, $additional_styles = []) {
        $styles = [];
        
        // Add background color if set
        if (!empty($block['backgroundColor'])) {
            $styles['background-color'] = $block['backgroundColor'];
        }
        
        // Add text color if set
        if (!empty($block['textColor'])) {
            $styles['color'] = $block['textColor'];
        }
        
        // Add custom styles
        if (!empty($additional_styles)) {
            $styles = array_merge($styles, $additional_styles);
        }
        
        return $styles;
    }
    
    /**
     * Render block styles as inline CSS
     */
    public static function render_block_styles($block, $additional_styles = []) {
        $styles = self::get_block_styles($block, $additional_styles);
        
        if (empty($styles)) {
            return '';
        }
        
        $style_string = '';
        foreach ($styles as $property => $value) {
            $style_string .= $property . ': ' . $value . '; ';
        }
        
        return trim($style_string);
    }
    
    /**
     * Sanitize and validate color value
     */
    public static function sanitize_color($color) {
        if (empty($color)) {
            return '';
        }
        
        // Remove any whitespace
        $color = trim($color);
        
        // Check if it's a valid hex color
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            return $color;
        }
        
        // Check if it's a valid RGB/RGBA
        if (preg_match('/^rgba?\([0-9\s,\.%]+\)$/', $color)) {
            return $color;
        }
        
        // Check if it's a valid HSL/HSLA
        if (preg_match('/^hsla?\([0-9\s,\.%deg]+\)$/', $color)) {
            return $color;
        }
        
        // Check if it's a valid CSS color name
        $valid_colors = [
            'transparent', 'black', 'white', 'red', 'green', 'blue', 'yellow', 
            'orange', 'purple', 'pink', 'gray', 'grey', 'brown', 'cyan', 'magenta'
        ];
        
        if (in_array(strtolower($color), $valid_colors)) {
            return $color;
        }
        
        return '';
    }
    
    /**
     * Get block data for JavaScript
     */
    public static function get_block_data_for_js($block, $additional_data = []) {
        $data = [
            'id' => $block['id'] ?? '',
            'name' => $block['name'] ?? '',
            'isPreview' => self::is_preview($block),
            'anchor' => self::get_block_anchor($block),
            'classes' => self::get_block_classes($block),
        ];
        
        if (!empty($additional_data)) {
            $data = array_merge($data, $additional_data);
        }
        
        return $data;
    }
    
    /**
     * Render block data as JSON for JavaScript
     */
    public static function render_block_data_for_js($block, $additional_data = []) {
        $data = self::get_block_data_for_js($block, $additional_data);
        return wp_json_encode($data);
    }
}

/**
 * Global helper functions for backward compatibility and ease of use
 */

/**
 * Helper function to get block template part
 */
function get_acf_block_template($block_name, $variables = []) {
    return ACF_Block_Helper::get_template($block_name, $variables);
}

/**
 * Helper function to get ACF field with fallback
 */
function get_acf_field_with_fallback($field_name, $default = '', $post_id = null) {
    return ACF_Block_Helper::get_field_with_fallback($field_name, $default, $post_id);
}

/**
 * Helper function to render ACF block wrapper
 */
function render_acf_block_wrapper($block, $content = '', $is_preview = false) {
    return ACF_Block_Helper::render_block_wrapper($block, $content, $is_preview);
}

/**
 * Additional helper function to manually clear asset cache if needed
 */
function acf_block_manager_clear_cache() {
    if (current_user_can('manage_options')) {
        ACF_Block_Asset_Manager::clear_global_asset_cache();
        error_log('ACF Block Manager: Asset cache manually cleared');
    }
}

/**
 * Additional helper function to debug asset status
 */
function acf_block_manager_debug_assets() {
    if (current_user_can('manage_options')) {
        $assets = ACF_Block_Asset_Manager::get_global_enqueued_assets();
        error_log('ACF Block Manager: Current global assets: ' . print_r($assets, true));
        return $assets;
    }
    return [];
}

/**
 * Get responsive image helper
 */
function get_acf_responsive_image($image_field, $size = 'full', $class = '', $alt = '') {
    return ACF_Block_Helper::get_responsive_image($image_field, $size, $class, $alt);
}

/**
 * Check if block has field value
 */
function acf_block_has_field($field_name, $block_id = null) {
    return ACF_Block_Helper::has_field_value($field_name, $block_id);
}

/**
 * Get block anchor with fallback
 */
function get_acf_block_anchor($block, $fallback_prefix = 'block') {
    return ACF_Block_Helper::get_block_anchor($block, $fallback_prefix);
}

/**
 * Get block classes as string
 */
function get_acf_block_classes($block, $additional_classes = []) {
    return ACF_Block_Helper::render_block_classes($block, $additional_classes);
}

/**
 * Get block styles as string
 */
function get_acf_block_styles($block, $additional_styles = []) {
    return ACF_Block_Helper::render_block_styles($block, $additional_styles);
}

/**
 * Check if in block preview mode
 */
function is_acf_block_preview($block = null) {
    return ACF_Block_Helper::is_preview($block);
}

/**
 * Enhanced block wrapper function with full feature support
 */
function render_acf_block_wrapper_enhanced($block, $content = '', $additional_classes = [], $additional_styles = []) {
    $block_id = ACF_Block_Helper::get_block_anchor($block);
    $classes = ACF_Block_Helper::render_block_classes($block, $additional_classes);
    $styles = ACF_Block_Helper::render_block_styles($block, $additional_styles);
    
    $style_attr = !empty($styles) ? ' style="' . esc_attr($styles) . '"' : '';
    
    printf(
        '<div id="%s" class="%s"%s>%s</div>',
        esc_attr($block_id),
        esc_attr($classes),
        $style_attr,
        $content
    );
}

/**
 * Save ACF JSON files using field group title only (disable default "group_XXXX.json")
 */

// Disable ACF's built-in JSON auto-save entirely
add_filter('acf/settings/save_json', '__return_false');

// Custom save handler
add_action('acf/update_field_group', function($field_group) {
    $path = get_stylesheet_directory() . '/acf-json';

    // Ensure directory exists
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }

    // Generate filename from sanitized title
    $title = sanitize_title($field_group['title']);
    $file  = trailingslashit($path) . $title . '.json';

    // Save JSON
    $json = json_encode($field_group, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $json);
}, 20);

// Load from custom path
add_filter('acf/settings/load_json', function($paths) {
    $paths = []; // clear default
    $paths[] = get_stylesheet_directory() . '/acf-json';

    if (is_child_theme()) {
        $paths[] = get_template_directory() . '/acf-json';
    }

    return $paths;
});

// Create directory early if missing
add_action('after_setup_theme', function() {
    $acf_json_dir = get_stylesheet_directory() . '/acf-json';
    if (!file_exists($acf_json_dir)) {
        wp_mkdir_p($acf_json_dir);
    }
});
