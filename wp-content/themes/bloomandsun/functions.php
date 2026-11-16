<?php
/**
 * Bloom & Sun — fonctions du thème.
 */

/*
 * Feuilles de style (assets/css/)
 * ---------------------------------------------------------------------------
 * - base.css, header.css, footer.css : sur toutes les pages
 * - products.css : seulement si le bloc « Collection de produits » est affiché
 * - home/<section>.css : seulement si la section est affichée sur la page
 *   (sur l'accueil, mais aussi sur toute page où la composition est insérée)
 */

// Sections : classe CSS du groupe principal => fichier assets/css/home/<fichier>.css
const BLOOMANDSUN_SECTION_STYLES = [
	'bs-hero'        => 'hero',
	'bs-categories'  => 'categories',
	'bs-lookbook'    => 'lookbook',
	'bs-reassurance' => 'reassurance',
	'bs-store'       => 'store',
	'bs-newsletter'  => 'newsletter',
];

/**
 * Déclare une feuille de style du thème. La version est la date de
 * modification du fichier : le navigateur recharge le CSS dès qu'il change.
 */
function bloomandsun_register_style( string $handle, string $file, array $deps = [ 'bloomandsun-base' ] ): void {
	wp_register_style(
		$handle,
		get_theme_file_uri( "assets/css/$file" ),
		$deps,
		(string) filemtime( get_theme_file_path( "assets/css/$file" ) )
	);
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );

	// L'éditeur reçoit tous les fichiers, pour un rendu identique au site.
	$editor_styles = [ 'assets/css/base.css', 'assets/css/header.css', 'assets/css/footer.css', 'assets/css/products.css' ];
	foreach ( BLOOMANDSUN_SECTION_STYLES as $file ) {
		$editor_styles[] = "assets/css/home/$file.css";
	}
	add_editor_style( $editor_styles );
} );

add_action( 'init', function () {
	bloomandsun_register_style( 'bloomandsun-base', 'base.css', [] );
	bloomandsun_register_style( 'bloomandsun-header', 'header.css' );
	bloomandsun_register_style( 'bloomandsun-footer', 'footer.css' );
	bloomandsun_register_style( 'bloomandsun-products', 'products.css' );

	foreach ( BLOOMANDSUN_SECTION_STYLES as $file ) {
		bloomandsun_register_style( "bloomandsun-home-$file", "home/$file.css" );
	}
} );

/**
 * Liste des styles conditionnels dont la page a besoin.
 * Remplie pendant l'affichage des blocs, lue au moment de charger les CSS.
 */
function bloomandsun_needed_styles( ?string $handle = null ): array {
	static $handles = [];

	if ( $handle ) {
		$handles[ $handle ] = true;
	}

	return array_keys( $handles );
}

// Pendant l'affichage de la page, on note les sections et blocs présents.
// (Dans un thème à blocs, la page est rendue avant le <head>.)
add_filter( 'render_block', function ( string $html, array $block ): string {
	if ( 'woocommerce/product-collection' === $block['blockName'] ) {
		bloomandsun_needed_styles( 'bloomandsun-products' );
	}

	if ( 'core/group' === $block['blockName'] ) {
		$classes = explode( ' ', $block['attrs']['className'] ?? '' );

		foreach ( BLOOMANDSUN_SECTION_STYLES as $class => $file ) {
			if ( in_array( $class, $classes, true ) ) {
				bloomandsun_needed_styles( "bloomandsun-home-$file" );
			}
		}
	}

	return $html;
}, 10, 2 );

// Chargement des CSS, après ceux de WordPress pour que les nôtres priment.
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'bloomandsun-base' );
	wp_enqueue_style( 'bloomandsun-header' );
	wp_enqueue_style( 'bloomandsun-footer' );

	foreach ( bloomandsun_needed_styles() as $handle ) {
		wp_enqueue_style( $handle );
	}
} );

// Catégorie « Bloom & Sun » dans l'outil d'insertion de compositions.
add_action( 'init', function () {
	register_block_pattern_category( 'bloomandsun', [ 'label' => 'Bloom & Sun' ] );
} );

/**
 * URL d'une catégorie de produits à partir de son slug.
 * Renvoie la page Boutique si la catégorie n'existe pas encore.
 */
function bloomandsun_category_url( string $slug ): string {
	$link = get_term_link( $slug, 'product_cat' );

	if ( is_wp_error( $link ) ) {
		$link = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	}

	return esc_url( $link );
}

/**
 * URL de la page Boutique, avec des paramètres optionnels (ex. tri par date).
 */
function bloomandsun_shop_url( array $args = [] ): string {
	$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	return esc_url( add_query_arg( $args, $url ) );
}
