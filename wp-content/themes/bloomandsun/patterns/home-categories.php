<?php
/**
 * Title: Accueil — Nos collections (catégories)
 * Slug: bloomandsun/home-categories
 * Categories: bloomandsun
 *
 * Les 5 rayons de la boutique. Toute la carte est cliquable
 * (le lien de l'étiquette est étiré sur la carte en CSS).
 */

$bloomandsun_categories = [
	[ 'slug' => 'robes',              'label' => 'Robes',              'color' => 'rose' ],
	[ 'slug' => 'hauts-et-mailles',   'label' => 'Hauts et mailles',   'color' => 'sand' ],
	[ 'slug' => 'pantalons-et-jupes', 'label' => 'Pantalons et jupes', 'color' => 'sage' ],
	[ 'slug' => 'vestes-et-manteaux', 'label' => 'Vestes et manteaux', 'color' => 'mist' ],
	[ 'slug' => 'accessoires',        'label' => 'Accessoires',        'color' => 'petal' ],
];
?>
<!-- wp:group {"tagName":"section","className":"bs-section bs-categories","layout":{"type":"default"}} -->
<section class="wp-block-group bs-section bs-categories">
	<!-- wp:group {"className":"bs-container","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container">
		<!-- wp:group {"className":"bs-section__head","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-section__head">
			<!-- wp:group {"layout":{"type":"default"}} -->
			<div class="wp-block-group">
				<!-- wp:heading -->
				<h2 class="wp-block-heading">Nos <em>collections</em></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"bs-section__intro"} -->
				<p class="bs-section__intro">Robes, mailles, manteaux, accessoires… en ligne comme en magasin.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo bloomandsun_shop_url(); ?>">Toutes les collections</a></div>
<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-cats__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-cats__grid">
			<?php foreach ( $bloomandsun_categories as $bloomandsun_cat ) : ?>
			<!-- wp:group {"className":"bs-photo bs-cat","backgroundColor":"<?php echo esc_attr( $bloomandsun_cat['color'] ); ?>","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-photo bs-cat has-<?php echo esc_attr( $bloomandsun_cat['color'] ); ?>-background-color has-background">
				<!-- wp:paragraph {"className":"bs-cat__name"} -->
				<p class="bs-cat__name"><a href="<?php echo bloomandsun_category_url( $bloomandsun_cat['slug'] ); ?>"><?php echo esc_html( $bloomandsun_cat['label'] ); ?></a></p>
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
