<?php

@ini_set('upload_max_filesize', '512M');
@ini_set('post_max_size', '512M');
@ini_set('max_execution_time', '900');

// Remove unnecessary items from head
remove_action( 'wp_head', 'rsd_link' );

remove_action( 'wp_head', 'wlwmanifest_link' );

remove_action( 'wp_head', 'wp_generator' );

define('THEME_URL', get_template_directory());

define('ASSETS_URI', get_stylesheet_directory_uri());

define('THEME_URI', get_template_directory_uri());

define('THEME_DIR', get_stylesheet_directory());

define('NO_IMAGE_URL', ASSETS_URI . '/assets/images/navus-noimage.svg');

/**
* Include assets
* Contains logic for enqueuing styles and scripts

*/
 require_once THEME_URL . '/functions/assets.php';


/**
* Include acf block registration
*
*/
require_once THEME_URL . '/functions/acfblocks/block-manager.php';
require_once THEME_URL . '/functions/acfblocks/assets-manager.php';
require_once THEME_URL . '/functions/acfblocks/helper.php';
//require_once THEME_URL . '/functions/acfblocks-reg.php';

 /**
 * Include admin-related functionality
 */
 require_once THEME_URL . '/functions/admin.php';

 /**
 * Include frontend-related functionality
 */
 require_once THEME_URL . '/functions/frontend.php';

  /**
 * Include admin-related functionality
 */

 require_once THEME_URL . '/functions/hooks.php';