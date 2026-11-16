<?php
/**
 * Title: Accueil — Le magasin
 * Slug: bloomandsun/home-store
 * Categories: bloomandsun
 *
 * Remplace [ADRESSE DU MAGASIN] et le lien « Itinéraire » par la vraie adresse.
 */
?>
<!-- wp:group {"tagName":"section","className":"bs-section bs-store","layout":{"type":"default"}} -->
<section class="wp-block-group bs-section bs-store">
	<!-- wp:group {"className":"bs-container bs-split bs-split--store","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container bs-split bs-split--store">
		<!-- wp:group {"className":"bs-split__text","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-split__text">
			<!-- wp:paragraph {"className":"bs-eyebrow"} -->
			<p class="bs-eyebrow">Le magasin</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading -->
			<h2 class="wp-block-heading">Venez essayer, on vous conseille</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>[ADRESSE DU MAGASIN]</p>
			<!-- /wp:paragraph -->

			<!-- wp:group {"className":"bs-hours","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-hours">
				<!-- wp:paragraph {"className":"bs-hours__day"} -->
				<p class="bs-hours__day">Mardi – samedi</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p>[HORAIRES]</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"bs-hours__day"} -->
				<p class="bs-hours__day">Dimanche, lundi</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p>[HORAIRES]</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:buttons {"className":"bs-actions"} -->
			<div class="wp-block-buttons bs-actions"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://www.google.com/maps/search/?api=1&amp;query=Bloom%20%26%20Sun%20concept%20store" target="_blank" rel="noreferrer noopener">Itinéraire</a></div>
<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-photo bs-store__photo","backgroundColor":"rose","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-photo bs-store__photo has-rose-background-color has-background">
			<!-- wp:paragraph {"className":"bs-photo__label"} -->
			<p class="bs-photo__label">Photo : la vitrine ou l'intérieur du magasin</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
