<?php
/**
 * Title: Header E-Commerce
 * Slug: ctheme/header-ecommerce
 * Categories: header
 * Description: Site header with top menu, search, login button, compare, wishlist, cart, logo, navigation.
 * Inserter: yes
 * @package MB_Navus
 * @subpackage MB_Navus
 * @since MB Navus 1.0
 */
?>

<!-- wp:group {"metadata":{"categories":["header"],"patternName":"ctheme/header-ecommerce","name":"Header E-Commerce"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"className":"py-3 sm:py-5","style":{"spacing":{"margin":{"top":"0px","bottom":"0px"},"padding":{"right":"1rem","left":"1rem"}}},"backgroundColor":"accent-5","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group py-3 sm:py-5 has-accent-5-background-color has-background" style="margin-top:0px;margin-bottom:0px;padding-right:1rem;padding-left:1rem"><!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:acf/topmenu {"name":"acf/topmenu","mode":"preview","className":"max-md:mx-auto"} /-->

<!-- wp:group {"className":"max-md:w-full","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group max-md:w-full"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Ieškoti...","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"className":"ecommerce-search max-md:w-[180px]"} /-->

<!-- wp:group {"className":"gap-x-2 lg:gap-x-4","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group gap-x-2 lg:gap-x-4"><!-- wp:acf/iconcompare {"name":"acf/iconcompare","mode":"preview","className":"header__compare max-md:ml-auto"} /-->

<!-- wp:acf/iconwishlists {"name":"acf/iconwishlists","mode":"preview","className":"header__wishlists"} /-->

<!-- wp:loginout {"className":"header__login"} /-->

<!-- wp:woocommerce/mini-cart {"miniCartIcon":"bag","iconColor":{"color":"#ffffff","name":"Accent 2","slug":"accent-2","class":"has-accent-2-icon-color"},"productCountColor":{"color":"#000000","name":"Base","slug":"base","class":"has-base-product-count-color"},"productCountVisibility":"always","className":"header__cart"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"w-full py-3 sm:py-5","style":{"spacing":{"margin":{"top":"0px","bottom":"0px"},"padding":{"right":"1rem","left":"1rem"}}},"backgroundColor":"accent-4","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group w-full py-3 sm:py-5 has-accent-4-background-color has-background" style="margin-top:0px;margin-bottom:0px;padding-right:1rem;padding-left:1rem"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"className":"header__logo"} /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:acf/mainmenu {"name":"acf/mainmenu","mode":"preview"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:acf/mobilemenu {"name":"acf/mobilemenu","mode":"preview"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->