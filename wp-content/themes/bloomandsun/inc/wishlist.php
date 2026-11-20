<?php
/**
 * Liste d'envies (sans compte client).
 *
 * - La liste est un cookie « bs_wishlist » : identifiants de produits séparés par des virgules,
 *   le plus récent en premier. Il est écrit par assets/js/wishlist.js.
 * - Affichage : templates/page-wishlist.html, modèle de la page /liste-d-envies/
 *   (collection de produits « bs-wishlist-products »)
 *   et le modèle du bouton cœur dans parts/layout/header.html.
 * - Ici : lecture du cookie, choix des produits de la page, chargement du JS et du CSS.
 */

const BLOOMANDSUN_WISHLIST_COOKIE = 'bs_wishlist';
const BLOOMANDSUN_WISHLIST_MAX    = 48;
const BLOOMANDSUN_WISHLIST_PAGE   = 'liste-d-envies';

/**
 * Produits de la liste d'envies (identifiants), dans l'ordre du cookie.
 *
 * @return int[]
 */
function bloomandsun_wishlist_ids(): array {
	$raw = sanitize_text_field( wp_unslash( $_COOKIE[ BLOOMANDSUN_WISHLIST_COOKIE ] ?? '' ) );
	$ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );

	return array_slice( array_values( array_unique( $ids ) ), 0, BLOOMANDSUN_WISHLIST_MAX );
}

/**
 * Règle la collection « bs-wishlist-products » (et la cherche dans les blocs enfants) :
 * produits du cookie, dans le même ordre ; [0] = aucun produit, le bloc affiche alors son message.
 */
function bloomandsun_wishlist_set_products( array $block ): array {
	if ( 'woocommerce/product-collection' === $block['blockName'] && in_array( 'bs-wishlist-products', explode( ' ', $block['attrs']['className'] ?? '' ), true ) ) {
		$ids = bloomandsun_wishlist_ids();

		$block['attrs']['query']['woocommerceHandPickedProducts'] = $ids ?: [ 0 ];
		$block['attrs']['query']['perPage']                       = BLOOMANDSUN_WISHLIST_MAX;
		$block['attrs']['query']['orderBy']                       = 'post__in';
	}

	$block['innerBlocks'] = array_map( 'bloomandsun_wishlist_set_products', $block['innerBlocks'] ?? [] );

	return $block;
}

// Réglé depuis le groupe principal de la page : les blocs imbriqués reçoivent leur contexte
// de leur parent, un filtre posé sur la collection elle-même arriverait trop tard.
add_filter( 'render_block_data', function ( array $block ): array {
	return in_array( 'bs-wishlist-page', explode( ' ', $block['attrs']['className'] ?? '' ), true ) ? bloomandsun_wishlist_set_products( $block ) : $block;
} );

// La page dépend du cookie de chaque visiteur : jamais en cache.
add_action( 'template_redirect', function () {
	if ( is_page( BLOOMANDSUN_WISHLIST_PAGE ) ) {
		nocache_headers();
	}
} );

// Cœurs et compteur : sur toutes les pages (les cartes produit sont partout).
add_action( 'wp_enqueue_scripts', function () {
	bloomandsun_register_style( 'bloomandsun-wishlist', 'wishlist.css' );
	wp_enqueue_style( 'bloomandsun-wishlist' );

	wp_enqueue_script(
		'bloomandsun-wishlist',
		get_theme_file_uri( 'assets/js/wishlist.js' ),
		[],
		bloomandsun_file_version( 'assets/js/wishlist.js' ),
		[ 'in_footer' => true, 'strategy' => 'defer' ]
	);

	wp_add_inline_script(
		'bloomandsun-wishlist',
		'window.bloomandsunWishlist = ' . wp_json_encode( [
			'cookie' => BLOOMANDSUN_WISHLIST_COOKIE,
			'max'    => BLOOMANDSUN_WISHLIST_MAX,
			'path'   => COOKIEPATH ?: '/',
		] ) . ';',
		'before'
	);
}, 20 );
