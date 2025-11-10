<?php
/**
 * ACF Block Asset Manager
 * Handles all asset loading, external dependencies, and site editor integration
 */

class ACF_Block_Asset_Manager {
    
    private $config;
    private $enqueued_external_assets = []; // Track enqueued assets
    private static $global_enqueued_assets = []; // Global tracking across all instances
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    /**
     * Process custom assets with improved duplicate prevention
     */
    public function process_custom_assets($block_data, $block_dir, $relative_block_dir) {
        $theme_uri = get_template_directory_uri();
        $external_js_handles = [];
        
        // Handle external_assets first to get dependency handles
        if (isset($block_data['external_assets'])) {
            $external_js_handles = $this->register_external_assets($block_data['name'], $block_data['external_assets']);
        }
        
        // Handle enqueue_style
        if (!isset($block_data['enqueue_style'])) {
            $style_file = $block_dir . '/style.css';
            if (file_exists($style_file)) {
                $style_url = $theme_uri . '/' . $relative_block_dir . '/style.css';
                $block_data['enqueue_style'] = $style_url;
            }
        } else {
            $block_data['enqueue_style'] = $this->resolve_asset_path($block_data['enqueue_style'], $block_dir, $relative_block_dir);
        }
        
        // Handle enqueue_script with dependencies
        if (!isset($block_data['enqueue_script'])) {
            $script_file = $block_dir . '/script.js';
            if (file_exists($script_file)) {
                $script_url = $theme_uri . '/' . $relative_block_dir . '/script.js';
                $block_data['enqueue_script'] = $script_url;
                
                // Add external JS as dependencies if they exist
                if (!empty($external_js_handles)) {
                    $this->add_script_dependencies($block_data['name'], $external_js_handles);
                }
            }
        } else {
            $block_data['enqueue_script'] = $this->resolve_asset_path($block_data['enqueue_script'], $block_dir, $relative_block_dir);
            
            // Add external JS as dependencies if they exist
            if (!empty($external_js_handles)) {
                $this->add_script_dependencies($block_data['name'], $external_js_handles);
            }
        }
        
        // Handle custom_style (custom path)
        if (isset($block_data['custom_style'])) {
            $block_data['enqueue_style'] = $this->resolve_custom_asset_path($block_data['custom_style']);
        }
        
        // Handle custom_script (custom path)
        if (isset($block_data['custom_script'])) {
            $block_data['enqueue_script'] = $this->resolve_custom_asset_path($block_data['custom_script']);
            
            // Add external JS as dependencies if they exist
            if (!empty($external_js_handles)) {
                $this->add_script_dependencies($block_data['name'], $external_js_handles);
            }
        }
        
        return $block_data;
    }

    /**
     * Register external assets with support for both admin and frontend
     */
    private function register_external_assets($block_name, $external_assets) {
        $asset_key = md5($block_name . serialize($external_assets));
        
        error_log("ACF Block Manager: Registering external assets for block '{$block_name}'");
        
        if (isset(self::$global_enqueued_assets[$asset_key])) {
            error_log("ACF Block Manager: Assets already registered for '{$block_name}'");
            return self::$global_enqueued_assets[$asset_key]['js'] ?? [];
        }
        
        $external_js_handles = [];
        $external_css_handles = [];
        
        // Register for both frontend and admin
        $this->enqueue_assets_for_context('wp_enqueue_scripts', $block_name, $external_assets);
        $this->enqueue_assets_for_context('admin_enqueue_scripts', $block_name, $external_assets);
        
        // Prepare return handles
        if (isset($external_assets['js'])) {
            $external_js_handles = array_keys($external_assets['js']);
        }
        if (isset($external_assets['css'])) {
            $external_css_handles = array_keys($external_assets['css']);
        }
        
        self::$global_enqueued_assets[$asset_key] = [
            'js' => $external_js_handles,
            'css' => $external_css_handles
        ];
        
        return $external_js_handles;
    }
    
    /**
     * Enqueue assets for specific context
     */
    private function enqueue_assets_for_context($hook, $block_name, $external_assets) {
        add_action($hook, function() use ($block_name, $external_assets, $hook) {
            
            if (!$this->should_load_assets($block_name, $hook)) {
                return;
            }
            
            $context_suffix = ($hook === 'admin_enqueue_scripts') ? '-admin' : '-frontend';
            
            error_log("ACF Block Manager: Loading assets for '{$block_name}' in context '{$hook}'");
            
            // Process CSS
            if (isset($external_assets['css'])) {
                foreach ($external_assets['css'] as $handle => $css) {
                    $unique_handle = $handle . $context_suffix;
                    $url = $this->resolve_custom_asset_path($css['src']);
                    
                    if (!wp_style_is($unique_handle, 'enqueued')) {
                        wp_enqueue_style(
                            $unique_handle,
                            $url,
                            $css['deps'] ?? [],
                            $css['version'] ?? '1.0.0'
                        );
                        error_log("ACF Block Manager: Enqueued CSS '{$unique_handle}' - {$url}");
                    }
                }
            }
            
            // Process JS
            if (isset($external_assets['js'])) {
                foreach ($external_assets['js'] as $handle => $js) {
                    $unique_handle = $handle . $context_suffix;
                    $url = $this->resolve_custom_asset_path($js['src']);
                    
                    if (!wp_script_is($unique_handle, 'enqueued')) {
                        wp_enqueue_script(
                            $unique_handle,
                            $url,
                            $js['deps'] ?? [],
                            $js['version'] ?? '1.0.0',
                            true
                        );
                        error_log("ACF Block Manager: Enqueued JS '{$unique_handle}' - {$url}");
                    }
                }
            }
        }, 10);
    }
    
    /**
     * Check if assets should load
     */
    private function should_load_assets($block_name, $hook) {
        $block_with_prefix = strpos($block_name, 'acf/') === 0 ? $block_name : 'acf/' . $block_name;
        
        if ($hook === 'admin_enqueue_scripts') {
            global $pagenow;
            
            // Load in post/page editor
            if (in_array($pagenow, ['post.php', 'post-new.php', 'site-editor.php'])) {
                error_log("ACF Block Manager: Loading admin assets for '{$block_with_prefix}' in editor");
                return true;
            }
            
            // Load in site editor
            if ($this->is_site_editor()) {
                error_log("ACF Block Manager: Loading admin assets for '{$block_with_prefix}' in site editor");
                return true;
            }
            
            // Load in any admin context where blocks might be used
            if (is_admin() && function_exists('get_current_screen')) {
                $screen = get_current_screen();
                if ($screen && in_array($screen->base, ['post', 'post-new', 'edit'])) {
                    error_log("ACF Block Manager: Loading admin assets for '{$block_with_prefix}' in admin screen: {$screen->base}");
                    return true;
                }
            }
            
            // Load in REST API context (for editor)
            if (defined('REST_REQUEST') && REST_REQUEST) {
                error_log("ACF Block Manager: Loading admin assets for '{$block_with_prefix}' in REST context");
                return true;
            }
            
            error_log("ACF Block Manager: NOT loading admin assets for '{$block_with_prefix}' - pagenow: {$pagenow}");
            return false;
        }
        
        if ($hook === 'wp_enqueue_scripts') {
            // Frontend: check if block exists
            if (!is_admin()) {
                $has_block = has_block($block_with_prefix);
                error_log("ACF Block Manager: Frontend check - Block '{$block_with_prefix}' exists: " . ($has_block ? 'Yes' : 'No'));
                return $has_block;
            }
            
            // Site editor iframe
            if ($this->is_site_editor_iframe() || $this->is_site_editor_canvas()) {
                error_log("ACF Block Manager: Loading frontend assets for '{$block_with_prefix}' in site editor iframe");
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get current context for asset loading
     */
    public function get_current_context() {
        if ($this->is_site_editor()) {
            return 'site_editor';
        }
        
        global $post, $pagenow;
        
        if (is_admin() && in_array($pagenow, ['post.php', 'post-new.php'])) {
            $post_type = $_GET['post_type'] ?? ($post ? $post->post_type : 'post');
            $post_id = $_GET['post'] ?? ($post ? $post->ID : null);
            
            if ($post_type === 'page' && $post_id && $this->is_front_page($post_id)) {
                return 'front_page';
            }
            
            if ($post_type === 'page') {
                return 'pages';
            } elseif ($post_type === 'post') {
                return 'posts';
            } else {
                return 'custom_post_' . $post_type;
            }
        }
        
        if (!is_admin()) {
            if (is_front_page()) {
                return 'front_page';
            } elseif (is_page()) {
                return 'pages';
            } elseif (is_single()) {
                return 'posts';
            }
        }
        
        return 'default';
    }

    /**
     * Get ACF blocks allowed for specific context
     */
    private function get_acf_blocks_for_context($context) {
        $allowed_acf_blocks = [];
        
        if (strpos($context, 'custom_post_') === 0) {
            $post_type = str_replace('custom_post_', '', $context);
            if (isset($this->config['custom_post_types'][$post_type]['blocks'])) {
                $allowed_acf_blocks = $this->config['custom_post_types'][$post_type]['blocks'];
            }
        }
        elseif (isset($this->config['contexts'][$context]['blocks'])) {
            $allowed_acf_blocks = $this->config['contexts'][$context]['blocks'];
        }
        
        if ($this->should_include_global_acf_blocks($context)) {
            $global_blocks = $this->config['global_blocks'] ?? [];
            $allowed_acf_blocks = array_merge($allowed_acf_blocks, $global_blocks);
        }
        
        return $allowed_acf_blocks;
    }

    /**
     * Check if global ACF blocks should be included for this context
     */
    private function should_include_global_acf_blocks($context) {
        $global_contexts = ['pages', 'posts', 'front_page', 'site_editor'];
        return in_array($context, $global_contexts);
    }

    /**
     * Check if the current post is the front page
     */
    private function is_front_page($post_id) {
        if (get_option('show_on_front') === 'page') {
            $front_page_id = get_option('page_on_front');
            return $front_page_id && $post_id == $front_page_id;
        }
        return false;
    }

    /**
     * Check if we're in the front page context
     */
    private function is_front_page_context() {
        global $post, $pagenow;
        
        if (get_option('show_on_front') !== 'page') {
            return false;
        }
        
        $front_page_id = get_option('page_on_front');
        if (!$front_page_id) {
            return false;
        }
        
        if (is_admin()) {
            $post_id = $_GET['post'] ?? ($post ? $post->ID : null);
            return $post_id && $post_id == $front_page_id;
        }
        
        return is_front_page();
    }

    /**
     * Check if we're in the site editor
     */
    public function is_site_editor() {
        global $pagenow;
        
        if (is_admin()) {
            if ($pagenow === 'site-editor.php') {
                return true;
            }
            
            if (isset($_GET['page']) && $_GET['page'] === 'gutenberg-edit-site') {
                return true;
            }
            
            if (isset($_GET['page']) && strpos($_GET['page'], 'edit-site') !== false) {
                return true;
            }
            
            if (defined('REST_REQUEST') && REST_REQUEST) {
                $request_uri = $_SERVER['REQUEST_URI'] ?? '';
                if (strpos($request_uri, '/wp/v2/templates') !== false || 
                    strpos($request_uri, '/wp/v2/template-parts') !== false) {
                    return true;
                }
            }
        }
        
        if ($this->is_site_editor_iframe()) {
            return true;
        }
        
        if ($this->is_site_editor_canvas()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if we're in the site editor iframe context
     */
    private function is_site_editor_iframe() {
        if (isset($_GET['canvas']) && $_GET['canvas'] === 'edit') {
            return true;
        }
        
        if (isset($_GET['iframe']) && $_GET['iframe'] === 'true') {
            return true;
        }
        
        if (isset($_GET['context']) && $_GET['context'] === 'edit') {
            return true;
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'site-editor.php') !== false || 
            strpos($referer, 'edit-site') !== false) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if we're in the site editor canvas context
     */
    private function is_site_editor_canvas() {
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen && isset($screen->id) && 
                (strpos($screen->id, 'site-editor') !== false || 
                 strpos($screen->id, 'edit-site') !== false)) {
                return true;
            }
        }
        
        if (isset($_GET['mode']) && $_GET['mode'] === 'edit') {
            return true;
        }
        
        if (defined('IFRAME_REQUEST') && IFRAME_REQUEST) {
            return true;
        }
        
        return false;
    }

    /**
     * Add script dependencies
     */
    private function add_script_dependencies($block_name, $external_handles) {
        add_action('wp_enqueue_scripts', function() use ($block_name, $external_handles) {
            $script_handle = $this->get_block_script_handle($block_name);
            
            if ($script_handle && wp_script_is($script_handle, 'registered')) {
                global $wp_scripts;
                
                $current_deps = isset($wp_scripts->registered[$script_handle]->deps) 
                    ? $wp_scripts->registered[$script_handle]->deps 
                    : [];
                
                $new_deps = array_merge($current_deps, $external_handles);
                $wp_scripts->registered[$script_handle]->deps = array_unique($new_deps);
            }
        }, 20);
    }

    /**
     * Get the script handle for a block
     */
    private function get_block_script_handle($block_name) {
        return 'acf-block-' . str_replace('/', '-', $block_name) . '-script';
    }
    
    /**
     * Resolve asset path
     */
    private function resolve_asset_path($asset_path, $block_dir, $relative_block_dir) {
        if (filter_var($asset_path, FILTER_VALIDATE_URL)) {
            return $asset_path;
        }
        
        $theme_uri = get_template_directory_uri();
        
        if (strpos($asset_path, '/') === 0) {
            return $theme_uri . $asset_path;
        }
        
        if (file_exists($block_dir . '/' . $asset_path)) {
            return $theme_uri . '/' . $relative_block_dir . '/' . $asset_path;
        }
        
        return $theme_uri . '/' . ltrim($asset_path, '/');
    }
    
    /**
     * Resolve custom asset path with constants
     */
    private function resolve_custom_asset_path($asset_path) {
        $original_path = $asset_path;
        
        $asset_path = str_replace('{THEME_URI}', get_template_directory_uri(), $asset_path);
        $asset_path = str_replace('{ASSETS_URI}', get_template_directory_uri() . '/assets', $asset_path);
        
        if (defined('PLUGIN_URL')) {
            $asset_path = str_replace('{PLUGIN_URL}', PLUGIN_URL, $asset_path);
        }
        
        if ($original_path !== $asset_path) {
            error_log("ACF Block Manager: Resolved '{$original_path}' to '{$asset_path}'");
        }
        
        return $asset_path;
    }
    
    /**
     * Enqueue block assets for frontend
     */
    public function enqueue_block_assets() {
        if ($this->is_site_editor_iframe() || $this->is_site_editor_canvas()) {
            wp_enqueue_style('wp-block-library');
            wp_enqueue_style('wp-block-library-theme');
        }
        
        $this->force_load_critical_block_assets();
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($this->is_site_editor() && !$this->is_site_editor_iframe()) {
            // Admin styles for site editor interface
        }
    }
    
    /**
     * Force load critical block assets
     */
    private function force_load_critical_block_assets() {
        $critical_blocks = [];
        
        foreach ($critical_blocks as $block_name => $assets) {
            if (has_block($block_name)) {
                error_log("ACF Block Manager: Force loading critical assets for '{$block_name}'");
                
                if (isset($assets['css'])) {
                    foreach ($assets['css'] as $handle => $css) {
                        if (!wp_style_is($handle, 'enqueued')) {
                            wp_enqueue_style(
                                $handle,
                                $this->resolve_custom_asset_path($css['src']),
                                $css['deps'] ?? [],
                                $css['version'] ?? null
                            );
                            error_log("ACF Block Manager: Force enqueued CSS '{$handle}'");
                        }
                    }
                }
                
                if (isset($assets['js'])) {
                    foreach ($assets['js'] as $handle => $js) {
                        if (!wp_script_is($handle, 'enqueued')) {
                            wp_enqueue_script(
                                $handle,
                                $this->resolve_custom_asset_path($js['src']),
                                $js['deps'] ?? [],
                                $js['version'] ?? null,
                                true
                            );
                            error_log("ACF Block Manager: Force enqueued JS '{$handle}'");
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Get all globally enqueued assets
     */
    public static function get_global_enqueued_assets() {
        return self::$global_enqueued_assets;
    }
    
    /**
     * Check if specific asset is already globally enqueued
     */
    public static function is_asset_globally_enqueued($block_name, $external_assets) {
        $asset_key = md5($block_name . serialize($external_assets));
        return isset(self::$global_enqueued_assets[$asset_key]);
    }
    
    /**
     * Clear global asset cache
     */
    public static function clear_global_asset_cache() {
        self::$global_enqueued_assets = [];
        error_log('ACF Block Manager: Global asset cache cleared');
    }
    
    /**
     * Debug asset loading issues
     */
    public function debug_assets() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        echo '<div style="background: #f0f0f0; padding: 15px; margin: 10px; border: 1px solid #ccc;">';
        echo '<h4>Asset Manager Debug Info</h4>';
        
        // Current context
        echo '<p><strong>Current Context:</strong> ' . $this->get_current_context() . '</p>';
        echo '<p><strong>Is Site Editor:</strong> ' . ($this->is_site_editor() ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Is Admin:</strong> ' . (is_admin() ? 'Yes' : 'No') . '</p>';
        
        // Global asset tracking
        echo '<p><strong>Global Enqueued Assets:</strong></p>';
        echo '<pre>' . print_r(self::$global_enqueued_assets, true) . '</pre>';
        
        // Current page info
        global $pagenow, $post;
        echo '<p><strong>Current Page:</strong> ' . $pagenow . '</p>';
        if ($post) {
            echo '<p><strong>Post ID:</strong> ' . $post->ID . '</p>';
            echo '<p><strong>Post Type:</strong> ' . $post->post_type . '</p>';
        }
        
        // Check if category-posts-tab block assets should load
        $block_name = 'acf/category-posts-tab';
        echo '<p><strong>Testing Asset Loading for:</strong> ' . $block_name . '</p>';
        echo '<p><strong>Admin Assets Should Load:</strong> ' . ($this->should_load_assets($block_name, 'admin_enqueue_scripts') ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Frontend Assets Should Load:</strong> ' . ($this->should_load_assets($block_name, 'wp_enqueue_scripts') ? 'Yes' : 'No') . '</p>';
        
        echo '</div>';
    }
}