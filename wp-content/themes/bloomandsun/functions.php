<?php
/**
 * Bloom & Sun — fonctions du thème.
 */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );
	add_editor_style( 'assets/css/main.css' ); // même rendu dans l'éditeur
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'bloomandsun',
		get_theme_file_uri( 'assets/css/main.css' ),
		[],
		wp_get_theme()->get( 'Version' )
	);
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
