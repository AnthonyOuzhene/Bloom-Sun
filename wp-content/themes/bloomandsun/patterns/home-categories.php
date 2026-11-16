<?php
/**
 * Title: Accueil — Par envie (catégories)
 * Slug: bloomandsun/home-categories
 * Categories: bloomandsun
 *
 * Toute la carte est cliquable : le lien du titre est étiré sur la carte en CSS.
 */

$bloomandsun_categories = [
	[ 'slug' => 'robes',              'label' => 'Robes',              'color' => 'rose' ],
	[ 'slug' => 'hauts-et-mailles',   'label' => 'Hauts et mailles',   'color' => 'sand' ],
	[ 'slug' => 'pantalons-et-jupes', 'label' => 'Pantalons et jupes', 'color' => 'sage' ],
	[ 'slug' => 'vestes-et-manteaux', 'label' => 'Vestes et manteaux', 'color' => 'mist' ],
];
?>
<!-- wp:group {"tagName":"section","className":"bs-section bs-categories","layout":{"type":"default"}} -->
<section class="wp-block-group bs-section bs-categories">
	<!-- wp:group {"className":"bs-container","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container">
		<!-- wp:heading {"className":"bs-section__title"} -->
		<h2 class="wp-block-heading bs-section__title">Par envie</h2>
		<!-- /wp:heading -->

		<!-- wp:group {"className":"bs-grid bs-grid--4","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-grid bs-grid--4">
			<?php foreach ( $bloomandsun_categories as $bloomandsun_cat ) : ?>
			<!-- wp:group {"className":"bs-cat-card","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-cat-card">
				<!-- wp:group {"className":"bs-photo bs-photo--portrait","backgroundColor":"<?php echo esc_attr( $bloomandsun_cat['color'] ); ?>","layout":{"type":"default"}} -->
				<div class="wp-block-group bs-photo bs-photo--portrait has-<?php echo esc_attr( $bloomandsun_cat['color'] ); ?>-background-color has-background"></div>
				<!-- /wp:group -->

				<!-- wp:paragraph {"className":"bs-cat-card__title"} -->
				<p class="bs-cat-card__title"><a href="<?php echo bloomandsun_category_url( $bloomandsun_cat['slug'] ); ?>"><?php echo esc_html( $bloomandsun_cat['label'] ); ?></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
