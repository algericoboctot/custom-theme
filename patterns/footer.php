<?php
/**
 * Title: Footer Main
 * Slug: ctheme/footer
 * Categories: footer
 * Description: Site logo, footer menu, social links, newsletter.
 * Inserter: yes
 * @package MB_Navus
 * @subpackage MB_Navus
 * @since MB Navus 1.0
 */
?>
<!-- wp:group {"metadata":{"categories":["footer"],"patternName":"ctheme/footer","name":"Footer Main"},"className":"container","layout":{"type":"constrained"}} -->
<div class="wp-block-group container"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:heading {"className":"footer__header"} -->
<h2 class="wp-block-heading footer__header">Susisiekite</h2>
<!-- /wp:heading -->

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

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:heading {"className":"footer__header"} -->
<h2 class="wp-block-heading footer__header">Informacija</h2>
<!-- /wp:heading -->

<!-- wp:acf/footermenu {"name":"acf/footermenu","mode":"preview"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:heading {"className":"footer__header"} -->
<h2 class="wp-block-heading footer__header">Naudinga</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Verslui</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Lojalumo pupelės</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Kavos pasirinkimo gidas</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:heading {"className":"footer__header"} -->
<h2 class="wp-block-heading footer__header">Naujienlaiškis</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[newsletter_form form="1"]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:acf/copyrights {"name":"acf/copyrights","mode":"preview"} /-->

<!-- wp:acf/navuslogo {"name":"acf/navuslogo","mode":"preview"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->