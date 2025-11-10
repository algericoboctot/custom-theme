<?php
/**
 * Title: Footer Simplified
 * Slug: ctheme/footer-simple
 * Categories: footer
 * Description: Site logo, footer menu, social links, newsletter.
 * Inserter: yes
 * @package MB_Navus
 * @subpackage MB_Navus
 * @since MB Navus 1.0
 */
?>
<!-- wp:group {"metadata":{"categories":["footer"],"patternName":"ctheme/footer-simple","name":"Footer Simplified"},"className":"container","layout":{"type":"constrained"}} -->
<div class="wp-block-group container"><!-- wp:columns {"className":"footer__columns"} -->
<div class="wp-block-columns footer__columns"><!-- wp:column {"width":"45%"} -->
<div class="wp-block-column" style="flex-basis:45%"><!-- wp:image {"width":"180px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized max-sm:size-auto sm:size-auto"><img src="http://localhost/custom-theme/wp-content/themes/ctheme/assets/images/logo.svg" alt="" style="width:180px"/></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":"10px"} -->
<div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"className":"footer__address footer__info"} -->
<p class="footer__address footer__info">Vilhelmo Berbomo g. 10, LT-92221 Klaipėda</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"footer__phone footer__info"} -->
<p class="footer__phone footer__info"><a href="tel:+37065553597">+37065553597</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"footer__email footer__info"} -->
<p class="footer__email footer__info"><a href="mailto:info@mtec.lt">info@mtec.lt</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"35%"} -->
<div class="wp-block-column" style="flex-basis:35%"><!-- wp:heading {"className":"footer__header"} -->
<h2 class="wp-block-heading footer__header">Informacija</h2>
<!-- /wp:heading -->

<!-- wp:acf/footermenu {"name":"acf/footermenu","data":{},"mode":"preview","className":"two"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%"><!-- wp:heading {"className":"footer__header"} -->
<h2 class="wp-block-heading footer__header">Draugaukime</h2>
<!-- /wp:heading -->

<!-- wp:social-links {"iconColor":"accent-1","iconColorValue":"#e71332","className":"is-style-logos-only social-icons"} -->
<ul class="wp-block-social-links has-icon-color is-style-logos-only social-icons"><!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:acf/copyrights {"name":"acf/copyrights","mode":"preview"} /-->

<!-- wp:acf/navuslogo {"name":"acf/navuslogo","mode":"preview"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->