<?php
/**
 * Pages boutique (WooCommerce) : réglages qui ne peuvent pas vivre dans les gabarits.
 *
 * Gabarits : templates/archive-product.html (liste + catégories), single-product.html,
 * page-cart.html, page-checkout.html (+ parts/checkout/header.html, footer.html).
 * Styles : assets/css/shop/.
 */

/*
 * Filtres par attribut : le gabarit désigne l'attribut par une classe
 * (bs-filter-attribute--taille, bs-filter-attribute--couleur) plutôt que par son
 * identifiant, qui change d'une installation à l'autre.
 */
function bloomandsun_set_filter_attribute_ids( array $block ): array {
	if ( 'woocommerce/product-filter-attribute' === $block['blockName']
		&& preg_match( '/\bbs-filter-attribute--([a-z0-9-]+)/', $block['attrs']['className'] ?? '', $m ) ) {
		$block['attrs']['attributeId'] = (int) wc_attribute_taxonomy_id_by_name( $m[1] );
	}

	$block['innerBlocks'] = array_map( 'bloomandsun_set_filter_attribute_ids', $block['innerBlocks'] ?? [] );

	return $block;
}

// Le bloc « Filtres » affiche lui-même ses enfants : on les règle depuis le bloc parent.
add_filter( 'render_block_data', function ( array $block ): array {
	return 'woocommerce/product-filters' === $block['blockName'] ? bloomandsun_set_filter_attribute_ids( $block ) : $block;
} );

// Fil d'Ariane : « Accueil » plutôt que le nom du site, séparateur discret.
add_filter( 'woocommerce_breadcrumb_defaults', function ( array $defaults ): array {
	$defaults['home']      = 'Accueil';
	$defaults['delimiter'] = '<span class="bs-breadcrumbs__sep" aria-hidden="true">/</span>';

	return $defaults;
} );

// Nombre de produits par ligne et par page sur les pages catégorie (cohérent avec le gabarit).
add_filter( 'loop_shop_per_page', fn() => 12 );

// Icône « Mon compte » de l'en-tête : toujours le pictogramme au trait, jamais la photo de profil
// (WooCommerce affiche l'avatar Gravatar, un rond gris, quand on est connecté).
add_filter( 'pre_render_block', function ( $pre, array $block ) {
	if ( 'woocommerce/customer-account' === $block['blockName'] ) {
		add_filter( 'pre_get_avatar', '__return_empty_string' );
	}

	return $pre;
}, 10, 2 );

add_filter( 'render_block_woocommerce/customer-account', function ( string $html ): string {
	remove_filter( 'pre_get_avatar', '__return_empty_string' );

	return $html;
} );
