<?php
/**
 * Title: Header Main
 * Slug: ctheme/header
 * Categories: header
 * Description: Site header with logo, navigation, button, language switcher.
 * Inserter: yes
 * @package MB_Navus
 * @subpackage MB_Navus
 * @since MB Navus 1.0
 */
?>

<!-- wp:group {"metadata":{"categories":["header"],"patternName":"ctheme/header","name":"Header Main"},"style":{"spacing":{"padding":{"right":"1rem","left":"1rem"}}},"backgroundColor":"accent-4","layout":{"type":"default"}} -->
<div class="wp-block-group has-accent-4-background-color has-background" style="padding-right:1rem;padding-left:1rem"><!-- wp:group {"metadata":{"name":"Container"},"className":"max-w-7xl w-full mx-auto py-4 md:py-6 lg:py-8","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group max-w-7xl w-full mx-auto py-4 md:py-6 lg:py-8"><!-- wp:site-logo /-->

<!-- wp:acf/mainmenu {"name":"acf/mainmenu","mode":"preview"} /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Button Text</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:acf/languageswitcher {"name":"acf/languageswitcher","mode":"preview"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->