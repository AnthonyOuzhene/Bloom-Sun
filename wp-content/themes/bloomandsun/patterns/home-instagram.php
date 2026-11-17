<?php
/**
 * Title: Accueil — Instagram
 * Slug: bloomandsun/home-instagram
 * Categories: bloomandsun
 *
 * Les 6 carrés sont des emplacements : remplace le groupe « bs-instagram__grid »
 * par le bloc Smash Balloon Instagram Feed une fois l'extension configurée.
 */

$bloomandsun_squares = [ 'rose', 'sand', 'sage', 'mist', 'petal', 'coral' ];
?>
<!-- wp:group {"tagName":"section","className":"bs-section bs-instagram","layout":{"type":"default"}} -->
<section class="wp-block-group bs-section bs-instagram">
	<!-- wp:group {"className":"bs-container","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container">
		<!-- wp:group {"className":"bs-section__head","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-section__head">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Sur <em>Instagram</em></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"bs-chip bs-chip--insta"} -->
			<p class="bs-chip bs-chip--insta"><a href="https://www.instagram.com/bloomandsun_boutique/" target="_blank" rel="noreferrer noopener">@bloomandsun_boutique</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-instagram__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-instagram__grid">
			<?php foreach ( $bloomandsun_squares as $bloomandsun_color ) : ?>
			<!-- wp:group {"className":"bs-photo","backgroundColor":"<?php echo esc_attr( $bloomandsun_color ); ?>","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-photo has-<?php echo esc_attr( $bloomandsun_color ); ?>-background-color has-background"></div>
			<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->

		<!-- wp:paragraph {"className":"bs-note bs-instagram__note"} -->
		<p class="bs-note bs-instagram__note">Les 6 dernières publications, mises à jour automatiquement.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
