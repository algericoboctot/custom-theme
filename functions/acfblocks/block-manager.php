<?php
/**
 * Complete ACF Block System with Front Page Support and Global Block Assets
 * Add this to your theme's functions.php file or as a separate file and require it
 */

class ACF_Block_Manager {
    
    private $config;
    
    public function __construct() {
        $this->config = $this->get_block_configuration();
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', [$this, 'register_acf_blocks'], 10);
        add_filter('allowed_block_types_all', [$this, 'filter_only_acf_blocks'], 10, 2);
        add_filter('block_categories_all', [$this, 'add_custom_block_categories'], 10, 2);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_block_assets'], 20);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets'], 10);
        // Uncomment to enable debug mode
        // add_action('wp_footer', [$this, 'debug_blocks']);
        // add_action('admin_footer', [$this, 'debug_blocks']);
    }
    
    /**
     * Main block configuration
     * Define all your blocks and their contexts here
     */
    private function get_block_configuration() {
        return [
            'contexts' => [
                'site_editor' => [
                    'blocks' => [
                        'acf/mainmenu',
                        'acf/footermenu',
                        'acf/languageswitcher',
                        'acf/iconlinks',
                        'acf/searchbtn',
                        'acf/topmenu',
                        'acf/navuslogo',
                        'acf/copyrights',
                        'acf/mobilemenu',
                        'acf/searchresults',
                        'acf/iconwishlists',
                        'acf/iconcompare',
                        'acf/sitelogo'
                    ],
                    'category' => 'site-builder',
                    'category_title' => 'Site Builder',
                    'category_icon' => 'admin-customizer'
                ],
                'front_page' => [
                    'blocks' => [
                        'acf/hero-banner',
                        'acf/category-posts',
                        'acf/category-posts-tab'
                    ],
                    'category' => 'homepage-blocks',
                    'category_title' => 'Homepage Blocks',
                    'category_icon' => 'admin-home'
                ],
                'pages' => [
                    'blocks' => [
                        // Add page-specific blocks here
                    ],
                    'category' => 'page-blocks',
                    'category_title' => 'Page Blocks',
                    'category_icon' => 'admin-page'
                ],
                'posts' => [
                    'blocks' => [
                        // Add post-specific blocks here
                    ],
                    'category' => 'post-blocks',
                    'category_title' => 'Post Blocks',
                    'category_icon' => 'admin-post'
                ]
            ],
            'custom_post_types' => [
                'services' => [
                    'blocks' => [
                        // Add service-specific blocks here
                    ],
                    'category' => 'service-blocks',
                    'category_title' => 'Service Blocks',
                    'category_icon' => 'admin-tools'
                ],
                'portfolio' => [
                    'blocks' => [
                        // Add portfolio-specific blocks here
                    ],
                    'category' => 'portfolio-blocks',
                    'category_title' => 'Portfolio Blocks',
                    'category_icon' => 'portfolio'
                ],
                'events' => [
                    'blocks' => [
                        // Add event-specific blocks here
                    ],
                    'category' => 'event-blocks',
                    'category_title' => 'Event Blocks',
                    'category_icon' => 'calendar-alt'
                ]
            ],
            'global_blocks' => [
                'acf/logoslisting',
                'acf/statnumbers',
                'acf/gallery',
                'acf/themeshortcode'
            ]
        ];
    }
    
    /**
     * Register all ACF blocks without filtering default blocks
     */
    public function register_acf_blocks() {
        if (!function_exists('acf_register_block_type')) {
            return;
        }
        
        // Register blocks from JSON files
        $this->register_blocks_from_directory();
        
        // Register blocks programmatically (fallback)
        $this->register_blocks_programmatically();
        
        // Log successful registration
        error_log('ACF Block Manager: All blocks registered. Default WordPress blocks remain unchanged.');
    }
    
    /**
     * Register blocks from JSON files in the blocks directory
     */
    private function register_blocks_from_directory() {
        $blocks_dir = get_template_directory() . '/blocks/';
        
        if (!is_dir($blocks_dir)) {
            error_log('ACF Block Manager: Blocks directory not found at ' . $blocks_dir);
            return;
        }
        
        // Scan for block.json files in all subdirectories
        $block_files = $this->find_block_json_files($blocks_dir);
        
        error_log('ACF Block Manager: Found ' . count($block_files) . ' block files');
        
        foreach ($block_files as $block_file) {
            $this->register_block_from_json($block_file);
        }
    }
    
    /**
     * Find all block.json files recursively
     */
    private function find_block_json_files($directory) {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->getFilename() === 'block.json') {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }
    
    /**
     * Register a single block from JSON file
     */
    private function register_block_from_json($block_file) {
        $block_data = json_decode(file_get_contents($block_file), true);
        
        if (!$block_data) {
            error_log('ACF Block Manager: Invalid JSON in ' . $block_file);
            return;
        }
        
        // Get block directory and ensure proper path separators
        $block_dir = dirname($block_file);
        $theme_dir = get_template_directory();
        
        // Calculate relative path with proper separators
        $relative_block_dir = str_replace($theme_dir, '', $block_dir);
        $relative_block_dir = str_replace('\\', '/', $relative_block_dir);
        $relative_block_dir = ltrim($relative_block_dir, '/');
        
        // Set template path
        if (!isset($block_data['render_template'])) {
            $template_file = $block_dir . '/template.php';
        } else {
            if (strpos($block_data['render_template'], '/') === 0) {
                $template_file = $theme_dir . $block_data['render_template'];
            } else {
                $template_file = $block_dir . '/' . $block_data['render_template'];
            }
        }
        
        // Check if template file exists
        if (!file_exists($template_file)) {
            error_log('ACF Block Manager: Template not found for ' . $block_data['name'] . ' at ' . $template_file);
            return;
        }
        
        $block_data['render_template'] = $template_file;
        
        // Handle custom asset paths
        $block_data = $this->process_custom_assets($block_data, $block_dir, $relative_block_dir);
        
        // Clean up custom properties before registration
        $custom_props = ['post_types', 'exclude_post_types', 'context', 'custom_style', 'custom_script', 'external_assets'];
        foreach ($custom_props as $prop) {
            unset($block_data[$prop]);
        }
        
        // Debug logging
        error_log('ACF Block Manager: Registering block ' . $block_data['name']);
        
        acf_register_block_type($block_data);
    }
    
    /**
     * Process custom asset paths and external assets
     */
    private function process_custom_assets($block_data, $block_dir, $relative_block_dir) {
        $theme_uri = get_template_directory_uri();
        
        // Handle external_assets FIRST (these are shared libraries like Swiper, Bootstrap, etc.)
        if (isset($block_data['external_assets'])) {
            $this->register_external_assets($block_data['name'], $block_data['external_assets']);
        }
        
        // Handle enqueue_style (block's own CSS)
        if (!isset($block_data['enqueue_style'])) {
            $style_file = $block_dir . '/style.css';
            if (file_exists($style_file)) {
                $style_url = $theme_uri . '/' . $relative_block_dir . '/style.css';
                $block_data['enqueue_style'] = $style_url;
            }
        } else {
            $block_data['enqueue_style'] = $this->resolve_asset_path($block_data['enqueue_style'], $block_dir, $relative_block_dir);
        }
        
        // Handle enqueue_script (block's own JS) - Always use ACF's built-in mechanism
        if (!isset($block_data['enqueue_script'])) {
            $script_file = $block_dir . '/script.js';
            if (file_exists($script_file)) {
                $script_url = $theme_uri . '/' . $relative_block_dir . '/script.js';
                $block_data['enqueue_script'] = $script_url;
            }
        } else {
            $block_data['enqueue_script'] = $this->resolve_asset_path($block_data['enqueue_script'], $block_dir, $relative_block_dir);
        }
        
        // Handle custom_style (custom path override)
        if (isset($block_data['custom_style'])) {
            $block_data['enqueue_style'] = $this->resolve_custom_asset_path($block_data['custom_style']);
        }
        
        // Handle custom_script (custom path override)
        if (isset($block_data['custom_script'])) {
            $block_data['enqueue_script'] = $this->resolve_custom_asset_path($block_data['custom_script']);
        }
        
        return $block_data;
    }
    
    /**
     * Register block script with proper dependencies
     */
    private function register_block_script($block_name, $script_file, $script_url, $block_data) {
        $dependencies = ['jquery'];
        
        if (isset($block_data['external_assets']['js'])) {
            foreach ($block_data['external_assets']['js'] as $handle => $js) {
                $dependencies[] = $handle;
            }
        }
        
        $handle = 'block-acf-' . $block_name;
        
        // Frontend scripts - only register once
        add_action('wp_enqueue_scripts', function() use ($handle, $block_name, $script_file, $script_url, $dependencies) {
            // Check if already registered to avoid duplicates
            if (wp_script_is($handle, 'registered')) {
                return;
            }
            
            if ($script_file && !file_exists($script_file)) {
                return;
            }
            
            wp_register_script(
                $handle,
                $script_url,
                $dependencies,
                $script_file ? filemtime($script_file) : null,
                true
            );
            
            wp_enqueue_script($handle);
            
        }, 15);
        
        // Editor scripts
        add_action('enqueue_block_editor_assets', function() use ($handle, $block_name, $script_file, $script_url, $dependencies, $block_data) {
            $editor_handle = $handle . '-editor';
            
            // Check if already registered
            if (wp_script_is($editor_handle, 'registered')) {
                return;
            }
            
            // Enqueue external dependencies first in editor
            if (isset($block_data['external_assets']['js'])) {
                foreach ($block_data['external_assets']['js'] as $dep_handle => $js) {
                    $dep_editor_handle = $dep_handle . '-editor';
                    
                    if (!wp_script_is($dep_editor_handle, 'registered')) {
                        wp_enqueue_script(
                            $dep_editor_handle,
                            $this->resolve_custom_asset_path($js['src']),
                            $js['deps'] ?? ['jquery'],
                            $js['version'] ?? null,
                            true
                        );
                    }
                }
            }
            
            if ($script_file && file_exists($script_file)) {
                wp_enqueue_script(
                    $editor_handle,
                    $script_url,
                    array_merge(['jquery'], isset($block_data['external_assets']['js']) ? array_map(function($k) { return $k . '-editor'; }, array_keys($block_data['external_assets']['js'])) : []),
                    filemtime($script_file),
                    true
                );
            }
        }, 10);
    }
    
    /**
     * Register external assets for blocks
     */
    private function register_external_assets($block_name, $external_assets) {
        // Frontend assets
        add_action('wp_enqueue_scripts', function() use ($external_assets) {
            if (isset($external_assets['css'])) {
                foreach ($external_assets['css'] as $handle => $css) {
                    // Check if already registered
                    if (wp_style_is($handle, 'registered')) {
                        continue;
                    }
                    
                    wp_enqueue_style(
                        $handle,
                        $this->resolve_custom_asset_path($css['src']),
                        $css['deps'] ?? [],
                        $css['version'] ?? null
                    );
                }
            }
            
            if (isset($external_assets['js'])) {
                foreach ($external_assets['js'] as $handle => $js) {
                    // Check if already registered
                    if (wp_script_is($handle, 'registered')) {
                        continue;
                    }
                    
                    wp_enqueue_script(
                        $handle,
                        $this->resolve_custom_asset_path($js['src']),
                        $js['deps'] ?? [],
                        $js['version'] ?? null,
                        $js['in_footer'] ?? true
                    );
                }
            }
        }, 8);
        
        // Block Editor assets
        add_action('enqueue_block_editor_assets', function() use ($external_assets) {
            if (isset($external_assets['css'])) {
                foreach ($external_assets['css'] as $handle => $css) {
                    $editor_handle = $handle . '-editor';
                    
                    // Check if already registered
                    if (wp_style_is($editor_handle, 'registered')) {
                        continue;
                    }
                    
                    wp_enqueue_style(
                        $editor_handle,
                        $this->resolve_custom_asset_path($css['src']),
                        $css['deps'] ?? [],
                        $css['version'] ?? null
                    );
                }
            }
            
            if (isset($external_assets['js'])) {
                foreach ($external_assets['js'] as $handle => $js) {
                    $editor_handle = $handle . '-editor';
                    
                    // Check if already registered
                    if (wp_script_is($editor_handle, 'registered')) {
                        continue;
                    }
                    
                    wp_enqueue_script(
                        $editor_handle,
                        $this->resolve_custom_asset_path($js['src']),
                        $js['deps'] ?? ['jquery'],
                        $js['version'] ?? null,
                        true
                    );
                }
            }
        }, 5);
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
     * Resolve custom asset path with support for constants
     */
    private function resolve_custom_asset_path($asset_path) {
        $asset_path = str_replace('{THEME_URI}', get_template_directory_uri(), $asset_path);
        $asset_path = str_replace('{ASSETS_URI}', get_template_directory_uri() . '/assets', $asset_path);
        
        if (defined('PLUGIN_URL')) {
            $asset_path = str_replace('{PLUGIN_URL}', PLUGIN_URL, $asset_path);
        }
        
        if (filter_var($asset_path, FILTER_VALIDATE_URL)) {
            return $asset_path;
        }
        
        if (strpos($asset_path, '/') === 0) {
            return get_template_directory_uri() . $asset_path;
        }
        
        return get_template_directory_uri() . '/' . ltrim($asset_path, '/');
    }
    
    /**
     * Register blocks programmatically
     */
    private function register_blocks_programmatically() {
        // Add programmatic blocks here if needed
    }
    
    /**
     * Filter ONLY ACF blocks based on context, keep all default WordPress blocks
     */
    public function filter_only_acf_blocks($allowed_blocks, $editor_context) {
        if ($allowed_blocks === true) {
            $allowed_blocks = $this->get_all_registered_blocks();
        }
        
        $current_context = $this->get_current_editor_context($editor_context);
        
        $all_blocks = $this->get_all_registered_blocks();
        
        $acf_blocks = array_filter($all_blocks, function($block) {
            return strpos($block, 'acf/') === 0;
        });
        
        $non_acf_blocks = array_filter($all_blocks, function($block) {
            return strpos($block, 'acf/') !== 0;
        });
        
        $allowed_acf_blocks = $this->get_acf_blocks_for_context($current_context);
        
        $filtered_blocks = array_merge($non_acf_blocks, $allowed_acf_blocks);
        
        return $filtered_blocks;
    }
    
    /**
     * Get all registered blocks
     */
    private function get_all_registered_blocks() {
        $registry = WP_Block_Type_Registry::get_instance();
        return array_keys($registry->get_all_registered());
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
     * Get current editor context
     */
    private function get_current_editor_context($editor_context) {
        if (isset($editor_context->name) && $editor_context->name === 'core/edit-site') {
            return 'site_editor';
        }
        
        if (isset($editor_context->post)) {
            $post_type = $editor_context->post->post_type;
            $post_id = $editor_context->post->ID;
            
            if ($post_type === 'page' && $this->is_front_page($post_id)) {
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
        
        return 'default';
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
     * Add custom block categories based on context
     */
    public function add_custom_block_categories($categories, $editor_context) {
        $current_context = $this->get_current_editor_context($editor_context);
        $new_categories = [];
        
        if (strpos($current_context, 'custom_post_') === 0) {
            $post_type = str_replace('custom_post_', '', $current_context);
            if (isset($this->config['custom_post_types'][$post_type])) {
                $config = $this->config['custom_post_types'][$post_type];
                $new_categories[] = [
                    'slug' => $config['category'],
                    'title' => $config['category_title'],
                    'icon' => $config['category_icon']
                ];
            }
        }
        
        if (isset($this->config['contexts'][$current_context])) {
            $config = $this->config['contexts'][$current_context];
            $new_categories[] = [
                'slug' => $config['category'],
                'title' => $config['category_title'],
                'icon' => $config['category_icon']
            ];
        }
        
        return array_merge($categories, $new_categories);
    }
    
    /**
     * Enqueue block assets for frontend
     */
    public function enqueue_block_assets() {
        // Enqueue global theme block assets
        wp_enqueue_style(
            'acf-blocks-global',
            get_template_directory_uri() . '/assets/css/blocks.css',
            [],
            wp_get_theme()->get('Version')
        );
        
        wp_enqueue_script(
            'acf-blocks-global',
            get_template_directory_uri() . '/assets/js/blocks.js',
            ['jquery'],
            wp_get_theme()->get('Version'),
            true
        );
        
        // Enqueue global blocks assets
        $this->enqueue_global_blocks_assets();
    }
    
    /**
     * Enqueue assets for global blocks
     */
    private function enqueue_global_blocks_assets() {
        if (empty($this->config['global_blocks'])) {
            return;
        }
        
        $blocks_dir = get_template_directory() . '/blocks/';
        $theme_uri = get_template_directory_uri();
        
        foreach ($this->config['global_blocks'] as $block_name) {
            // Remove 'acf/' prefix
            $clean_name = str_replace('acf/', '', $block_name);
            
            // Check if already enqueued to prevent duplicates
            $style_handle = 'global-block-' . $clean_name;
            $script_handle = 'global-block-' . $clean_name;
            
            // Try to find the block directory in multiple locations
            $possible_dirs = [
                $blocks_dir . 'global/' . $clean_name,
                $blocks_dir . 'site-editor/' . $clean_name,
                $blocks_dir . 'pages/' . $clean_name,
                $blocks_dir . 'front-page/' . $clean_name,
            ];
            
            foreach ($possible_dirs as $block_dir) {
                if (!is_dir($block_dir)) {
                    continue;
                }
                
                // Calculate relative path
                $relative_dir = str_replace(get_template_directory(), '', $block_dir);
                $relative_dir = str_replace('\\', '/', $relative_dir);
                $relative_dir = ltrim($relative_dir, '/');
                
                // Enqueue CSS if exists and not already enqueued
                $style_file = $block_dir . '/style.css';
                if (file_exists($style_file) && !wp_style_is($style_handle, 'enqueued')) {
                    wp_enqueue_style(
                        $style_handle,
                        $theme_uri . '/' . $relative_dir . '/style.css',
                        [],
                        filemtime($style_file)
                    );
                }
                
                // Enqueue JS if exists and not already enqueued
                $script_file = $block_dir . '/script.js';
                if (file_exists($script_file) && !wp_script_is($script_handle, 'enqueued')) {
                    wp_enqueue_script(
                        $script_handle,
                        $theme_uri . '/' . $relative_dir . '/script.js',
                        ['jquery'],
                        filemtime($script_file),
                        true
                    );
                }
                
                // Found the block, no need to check other directories
                break;
            }
        }
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (!in_array($hook, ['post.php', 'post-new.php', 'site-editor.php'])) {
            return;
        }
        
        // wp_enqueue_style(
        //     'acf-blocks-admin',
        //     get_template_directory_uri() . '/assets/css/blocks-admin.css',
        //     [],
        //     wp_get_theme()->get('Version')
        // );
        
        // wp_enqueue_script(
        //     'acf-blocks-admin',
        //     get_template_directory_uri() . '/assets/js/blocks-admin.js',
        //     ['wp-blocks', 'wp-element', 'wp-editor'],
        //     wp_get_theme()->get('Version'),
        //     true
        // );
    }
    
    /**
     * Enqueue editor-specific assets
     */
    public function enqueue_editor_assets() {
        // Enqueue global blocks assets in editor
        $this->enqueue_global_blocks_assets();
    }
    
    /**
     * Debug block registration issues
     */
    public function debug_blocks() {
        if (!current_user_can('manage_options') || !isset($_GET['debug_acf_blocks'])) {
            return;
        }
        
        echo '<div style="background: white; padding: 20px; margin: 20px; border: 1px solid #ccc; font-family: monospace;">';
        echo '<h3>ACF Block Debug Information</h3>';
        
        $blocks_dir = get_template_directory() . '/blocks/';
        echo '<p><strong>Blocks Directory:</strong> ' . $blocks_dir . '</p>';
        echo '<p><strong>Directory Exists:</strong> ' . (is_dir($blocks_dir) ? 'Yes' : 'No') . '</p>';
        echo '<p><strong>Theme Directory:</strong> ' . get_template_directory() . '</p>';
        echo '<p><strong>Theme URI:</strong> ' . get_template_directory_uri() . '</p>';
        
        // Show global blocks
        echo '<h4>Global Blocks Configuration:</h4>';
        echo '<ul>';
        foreach ($this->config['global_blocks'] as $block) {
            echo '<li>' . $block . '</li>';
        }
        echo '</ul>';
        
        if (is_dir($blocks_dir)) {
            $block_files = $this->find_block_json_files($blocks_dir);
            echo '<h4>Found Block Files (' . count($block_files) . '):</h4>';
            
            foreach ($block_files as $file) {
                $block_dir = dirname($file);
                $relative_path = str_replace(get_template_directory(), '', $block_dir);
                
                echo '<div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0;">';
                echo '<strong>Block:</strong> ' . basename($block_dir) . '<br>';
                echo '<strong>Path:</strong> ' . $relative_path . '<br>';
                
                $template = $block_dir . '/template.php';
                $style = $block_dir . '/style.css';
                $script = $block_dir . '/script.js';
                
                echo '<strong>Template:</strong> ' . (file_exists($template) ? '✓' : '✗') . '<br>';
                echo '<strong>Style:</strong> ' . (file_exists($style) ? '✓' : '✗') . '<br>';
                echo '<strong>Script:</strong> ' . (file_exists($script) ? '✓' : '✗') . '<br>';
                echo '</div>';
            }
        }
        
        // Debug current context
        global $post;
        if ($post) {
            echo '<h4>Current Context:</h4>';
            echo '<p><strong>Post ID:</strong> ' . $post->ID . '</p>';
            echo '<p><strong>Post Type:</strong> ' . $post->post_type . '</p>';
            echo '<p><strong>Is Front Page:</strong> ' . ($this->is_front_page($post->ID) ? 'Yes' : 'No') . '</p>';
        }
        
        echo '</div>';
    }
}

// Initialize the ACF Block Manager
new ACF_Block_Manager();