<?php
/**
 * Sources de liaison (Block Bindings) : les gabarits .html déclarent d'où vient
 * une valeur, ce fichier la fournit. Aucun HTML ici, uniquement des données.
 *
 * Sources : « bloomandsun/store » (Infos du magasin), « bloomandsun/home » (Page d'accueil),
 * « bloomandsun/links » (liens vers la boutique, les catégories, les étiquettes, les pages).
 *
 * Exemples dans un gabarit :
 *   paragraphe → {"source":"bloomandsun/store","args":{"key":"street"}}
 *   bouton     → {"source":"bloomandsun/links","args":{"key":"category","slug":"robes"}}
 */

// Catégories et pages utilisées par les gabarits (pour l'aperçu dans l'éditeur).
const BLOOMANDSUN_CATEGORY_SLUGS = [ 'vetements', 'robes', 'pulls', 'gilets', 'vestes', 'manteaux', 'pantalons', 'jupes', 'jeans', 'accessoires' ];
const BLOOMANDSUN_TAG_SLUGS      = [ 'idee-cadeau' ];
const BLOOMANDSUN_PAGE_SLUGS     = [ 'le-magasin', 'contact', 'livraison-et-retours', 'guide-des-tailles', 'faq', 'cgv', 'mentions-legales', 'politique-de-confidentialite', 'gestion-des-cookies', 'liste-d-envies' ];

/**
 * URL de la boutique WooCommerce.
 */
function bloomandsun_link_shop(): string {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/**
 * URL d'une catégorie de produits ; la boutique si la catégorie n'existe pas encore.
 */
function bloomandsun_link_category( string $slug ): string {
	$link = get_term_link( $slug, 'product_cat' );

	return is_wp_error( $link ) ? bloomandsun_link_shop() : $link;
}

/**
 * URL d'une page ; l'adresse attendue si la page n'existe pas encore.
 */
function bloomandsun_link_page( string $slug ): string {
	$page = get_page_by_path( $slug );

	return $page ? get_permalink( $page ) : home_url( "/$slug/" );
}

/**
 * URL d'une étiquette de produits ; la boutique si l'étiquette n'existe pas encore.
 */
function bloomandsun_link_tag( string $slug ): string {
	$link = get_term_link( $slug, 'product_tag' );

	return is_wp_error( $link ) ? bloomandsun_link_shop() : $link;
}

/**
 * Résout un lien à partir des arguments de liaison.
 */
function bloomandsun_link( array $args ): string {
	$slug = (string) ( $args['slug'] ?? '' );

	return match ( $args['key'] ?? '' ) {
		'shop'     => bloomandsun_link_shop(),
		'new'      => add_query_arg( 'orderby', 'date', bloomandsun_link_shop() ),
		'category' => bloomandsun_link_category( $slug ),
		'tag'      => bloomandsun_link_tag( $slug ),
		'page'     => bloomandsun_link_page( $slug ),
		'logo'     => get_theme_file_uri( 'assets/images/logo.svg' ),
		'cart'     => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' ),
		default    => home_url( '/' ),
	};
}

add_action( 'init', function () {
	register_block_bindings_source( 'bloomandsun/store', [
		'label'              => 'Infos du magasin',
		'get_value_callback' => static fn( array $args ): string => bloomandsun_store( (string) ( $args['key'] ?? '' ) ),
	] );

	register_block_bindings_source( 'bloomandsun/home', [
		'label'              => 'Page d\'accueil',
		'get_value_callback' => static fn( array $args ): string => bloomandsun_home( (string) ( $args['key'] ?? '' ) ),
	] );

	register_block_bindings_source( 'bloomandsun/event', [
		'label'              => 'Événement',
		'get_value_callback' => static fn( array $args ): string => bloomandsun_event( (string) ( $args['key'] ?? '' ) ),
	] );

	register_block_bindings_source( 'bloomandsun/links', [
		'label'              => 'Liens du site',
		'get_value_callback' => static fn( array $args ): string => bloomandsun_link( $args ),
	] );
} );

/**
 * Données pour l'aperçu dans l'éditeur (assets/js/editor-bindings.js).
 */
function bloomandsun_bindings_editor_data(): array {
	return [
		'store' => bloomandsun_store_all(),
		'home'  => bloomandsun_home_all(),
		'event' => bloomandsun_event_editor_data(),
		'links' => [
			'shop'       => bloomandsun_link( [ 'key' => 'shop' ] ),
			'new'        => bloomandsun_link( [ 'key' => 'new' ] ),
			'logo'       => bloomandsun_link( [ 'key' => 'logo' ] ),
			'cart'       => bloomandsun_link( [ 'key' => 'cart' ] ),
			'categories' => array_combine( BLOOMANDSUN_CATEGORY_SLUGS, array_map( 'bloomandsun_link_category', BLOOMANDSUN_CATEGORY_SLUGS ) ),
			'pages'      => array_combine( BLOOMANDSUN_PAGE_SLUGS, array_map( 'bloomandsun_link_page', BLOOMANDSUN_PAGE_SLUGS ) ),
			'tags'       => array_combine( BLOOMANDSUN_TAG_SLUGS, array_map( 'bloomandsun_link_tag', BLOOMANDSUN_TAG_SLUGS ) ),
		],
	];
}
