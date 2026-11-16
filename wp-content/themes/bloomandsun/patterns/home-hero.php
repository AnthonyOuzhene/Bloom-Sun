<?php
/**
 * Title: Accueil — Bandeau principal
 * Slug: bloomandsun/home-hero
 * Categories: bloomandsun
 *
 * Les blocs « Photo » sont des groupes colorés : dans l'éditeur,
 * ajoute une image de fond (Styles > Arrière-plan) et supprime l'étiquette.
 */
?>
<!-- wp:group {"tagName":"section","className":"bs-section bs-hero","layout":{"type":"default"}} -->
<section class="wp-block-group bs-section bs-hero">
	<!-- wp:group {"className":"bs-container bs-hero__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container bs-hero__inner">
		<!-- wp:group {"className":"bs-hero__text","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-hero__text">
			<!-- wp:paragraph {"className":"bs-eyebrow"} -->
			<p class="bs-eyebrow">Concept store · Collection automne-hiver</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":1,"className":"bs-hero__title"} -->
			<h1 class="wp-block-heading bs-hero__title">Des pièces douces pour les jours plus courts</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"bs-lead"} -->
			<p class="bs-lead">Mailles, robes fluides et manteaux choisis en boutique, à porter dès maintenant. Commandez en ligne ou venez essayer.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"className":"bs-actions"} -->
			<div class="wp-block-buttons bs-actions"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo bloomandsun_shop_url(); ?>">Découvrir la collection</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/inspirations/' ) ); ?>">Voir les inspirations</a></div>
<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-hero__media","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-hero__media">
			<!-- wp:group {"className":"bs-photo bs-photo--arch-corner bs-hero__main","backgroundColor":"rose","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-photo bs-photo--arch-corner bs-hero__main has-rose-background-color has-background">
				<!-- wp:paragraph {"className":"bs-photo__label"} -->
				<p class="bs-photo__label">Photo : tenue portée, plan large</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"bs-photo","backgroundColor":"sand","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-photo has-sand-background-color has-background">
				<!-- wp:paragraph {"className":"bs-photo__label"} -->
				<p class="bs-photo__label">Photo : détail matière</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"bs-photo","backgroundColor":"sage","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-photo has-sage-background-color has-background">
				<!-- wp:paragraph {"className":"bs-photo__label"} -->
				<p class="bs-photo__label">Photo : accessoire</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
