<?php
/**
 * Title: Header Institution Detailed
 * Slug: ctheme/header-ins-detailed
 * Categories: header
 * Description: Site header with site logo and icon links, search language switch, navigation.
 * Inserter: yes
 * @package MB_Navus
 * @subpackage MB_Navus
 * @since MB Navus 1.0
 */
?>


<!-- wp:group {"metadata":{"categories":["header"],"patternName":"ctheme/header-ins-simple","name":"Header Instituion Simple"},"backgroundColor":"accent-4","layout":{"type":"default"}} -->
<div class="wp-block-group has-accent-4-background-color has-background"><!-- wp:group {"metadata":{"name":"content"},"className":"max-w-7xl w-full mx-auto","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group max-w-7xl w-full mx-auto"><!-- wp:site-logo {"className":"header__logo order-2 lg:order-1"} /-->

<!-- wp:acf/mainmenu {"name":"acf/mainmenu","mode":"preview","className":"order-3 lg:order-2"} /-->

<!-- wp:group {"className":"order-1 header__top-icons md:order-2 lg:order-3","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group order-1 header__top-icons md:order-2 lg:order-3"><!-- wp:acf/searchbtn {"name":"acf/searchbtn","mode":"preview"} /-->

<!-- wp:social-links {"iconColor":"accent-2","iconColorValue":"#ffffff","className":"is-style-logos-only social-icons","style":{"spacing":{"blockGap":{"left":"6px"}}}} -->
<ul class="wp-block-social-links has-icon-color is-style-logos-only social-icons"><!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
<!-- /wp:social-links -->

<!-- wp:acf/iconlinks {"name":"acf/iconlinks","data":{"description":"","_description":"field_688716f8c78d2","icons_0_icon":229,"_icons_0_icon":"field_688715502890c","icons_0_link":{"title":"Test","url":"#","target":""},"_icons_0_link":"field_688716b82890d","icons_1_icon":230,"_icons_1_icon":"field_688715502890c","icons_1_link":{"title":"Test","url":"#","target":""},"_icons_1_link":"field_688716b82890d","icons_2_icon":231,"_icons_2_icon":"field_688715502890c","icons_2_link":{"title":"Test","url":"#","target":""},"_icons_2_link":"field_688716b82890d","icons":3,"_icons":"field_688714b491d0a"},"mode":"preview"} /-->

<!-- wp:acf/languageswitcher {"name":"acf/languageswitcher","mode":"preview"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Ieškoti...","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"className":"header__search"} /-->

<!-- wp:acf/mobilemenu {"name":"acf/mobilemenu","mode":"preview"} /--></div>
<!-- /wp:group -->