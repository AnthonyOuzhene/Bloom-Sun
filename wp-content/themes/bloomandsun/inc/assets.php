<?php
/**
 * Chargement des feuilles de style et du script d'éditeur.
 *
 * - base.css, header.css, footer.css : sur toutes les pages
 * - products.css : seulement si le bloc « Collection de produits » est affiché
 * - home/*.css, pages/*.css : seulement si la section correspondante est affichée
 */

// Section : classe CSS du groupe principal => fichier assets/css/<fichier>.css
const BLOOMANDSUN_SECTION_STYLES = [
	'bs-hero'        => 'home/hero',
	'bs-categories'  => 'home/categories',
	'bs-reassurance' => 'home/reassurance',
	'bs-store'       => 'home/store',
	'bs-instagram'   => 'home/instagram',
	'bs-newsletter'  => 'home/newsletter',
	'bs-contact'     => 'pages/contact',
];

/**
 * Identifiant WordPress d'une feuille de section (ex. « home/hero » → « bloomandsun-home-hero »).
 */
function bloomandsun_section_handle( string $file ): string {
	return 'bloomandsun-' . str_replace( '/', '-', $file );
}

/**
 * Version d'un fichier du thème : sa date de modification (le navigateur recharge dès qu'il change).
 */
function bloomandsun_file_version( string $relative_path ): string {
	$path = get_theme_file_path( $relative_path );

	return file_exists( $path ) ? (string) filemtime( $path ) : (string) wp_get_theme()->get( 'Version' );
}

/**
 * Déclare une feuille de style du thème.
 */
function bloomandsun_register_style( string $handle, string $file, array $deps = [ 'bloomandsun-base' ] ): void {
	wp_register_style( $handle, get_theme_file_uri( "assets/css/$file" ), $deps, bloomandsun_file_version( "assets/css/$file" ) );
}

/**
 * Styles conditionnels dont la page a besoin (remplie pendant l'affichage des blocs).
 */
function bloomandsun_needed_styles( ?string $handle = null ): array {
	static $handles = [];

	if ( $handle ) {
		$handles[ $handle ] = true;
	}

	return array_keys( $handles );
}

add_action( 'init', function () {
	bloomandsun_register_style( 'bloomandsun-base', 'base.css', [] );
	bloomandsun_register_style( 'bloomandsun-header', 'header.css' );
	bloomandsun_register_style( 'bloomandsun-footer', 'footer.css' );
	bloomandsun_register_style( 'bloomandsun-products', 'products.css' );

	foreach ( BLOOMANDSUN_SECTION_STYLES as $file ) {
		bloomandsun_register_style( bloomandsun_section_handle( $file ), "$file.css" );
	}
} );

// Pendant l'affichage (avant le <head> dans un thème à blocs), on note les sections présentes.
add_filter( 'render_block', function ( string $html, array $block ): string {
	if ( 'woocommerce/product-collection' === $block['blockName'] ) {
		bloomandsun_needed_styles( 'bloomandsun-products' );
	}

	if ( 'core/group' === $block['blockName'] ) {
		$classes = explode( ' ', $block['attrs']['className'] ?? '' );

		foreach ( BLOOMANDSUN_SECTION_STYLES as $class => $file ) {
			if ( in_array( $class, $classes, true ) ) {
				bloomandsun_needed_styles( bloomandsun_section_handle( $file ) );
			}
		}
	}

	return $html;
}, 10, 2 );

// Site : CSS du thème après ceux de WordPress, pour qu'ils priment.
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'bloomandsun-base' );
	wp_enqueue_style( 'bloomandsun-header' );
	wp_enqueue_style( 'bloomandsun-footer' );

	foreach ( bloomandsun_needed_styles() as $handle ) {
		wp_enqueue_style( $handle );
	}
} );

// Éditeur : tous les CSS, pour un rendu identique au site.
add_action( 'after_setup_theme', function () {
	$styles = [ 'assets/css/base.css', 'assets/css/header.css', 'assets/css/footer.css', 'assets/css/products.css' ];

	foreach ( BLOOMANDSUN_SECTION_STYLES as $file ) {
		$styles[] = "assets/css/$file.css";
	}

	add_editor_style( $styles );
} );

// Éditeur : affiche les vraies valeurs des données liées (infos du magasin, liens).
add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_script(
		'bloomandsun-editor-bindings',
		get_theme_file_uri( 'assets/js/editor-bindings.js' ),
		[ 'wp-blocks' ],
		bloomandsun_file_version( 'assets/js/editor-bindings.js' ),
		true
	);

	wp_add_inline_script(
		'bloomandsun-editor-bindings',
		'window.bloomandsunBindings = ' . wp_json_encode( bloomandsun_bindings_editor_data() ) . ';',
		'before'
	);
} );
