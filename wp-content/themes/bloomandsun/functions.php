<?php
/**
 * Bloom & Sun — fonctions du thème.
 */

require_once __DIR__ . '/inc/store.php'; // infos du magasin, réglages, blocs

/*
 * Feuilles de style (assets/css/)
 * ---------------------------------------------------------------------------
 * - base.css, header.css, footer.css : sur toutes les pages
 * - products.css : seulement si le bloc « Collection de produits » est affiché
 * - home/<section>.css et pages/<page>.css : seulement si la section est
 *   affichée sur la page (où que la composition soit insérée)
 */

// Sections : classe CSS du groupe principal => fichier assets/css/<fichier>.css
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
 * Déclare une feuille de style du thème. La version est la date de
 * modification du fichier : le navigateur recharge le CSS dès qu'il change.
 */
function bloomandsun_register_style( string $handle, string $file, array $deps = [ 'bloomandsun-base' ] ): void {
	$path = get_theme_file_path( "assets/css/$file" );

	wp_register_style(
		$handle,
		get_theme_file_uri( "assets/css/$file" ),
		$deps,
		file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' )
	);
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );

	// L'éditeur reçoit tous les fichiers, pour un rendu identique au site.
	$editor_styles = [ 'assets/css/base.css', 'assets/css/header.css', 'assets/css/footer.css', 'assets/css/products.css' ];
	foreach ( BLOOMANDSUN_SECTION_STYLES as $file ) {
		$editor_styles[] = "assets/css/$file.css";
	}
	add_editor_style( $editor_styles );
} );

add_action( 'init', function () {
	bloomandsun_register_style( 'bloomandsun-base', 'base.css', [] );
	bloomandsun_register_style( 'bloomandsun-header', 'header.css' );
	bloomandsun_register_style( 'bloomandsun-footer', 'footer.css' );
	bloomandsun_register_style( 'bloomandsun-products', 'products.css' );

	foreach ( BLOOMANDSUN_SECTION_STYLES as $file ) {
		bloomandsun_register_style( bloomandsun_section_handle( $file ), "$file.css" );
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
				bloomandsun_needed_styles( bloomandsun_section_handle( $file ) );
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
 * Renvoie la page boutique de WooCommerce si la catégorie n'existe pas encore.
 */
function bloomandsun_category_url( string $slug ): string {
	$link = get_term_link( $slug, 'product_cat' );

	if ( is_wp_error( $link ) ) {
		$link = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	}

	return esc_url( $link );
}

/**
 * URL de la page boutique de WooCommerce, avec des paramètres optionnels (ex. tri par date).
 */
function bloomandsun_shop_url( array $args = [] ): string {
	$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	return esc_url( add_query_arg( $args, $url ) );
}

/**
 * URL d'une page à partir de son slug (ex. « le-magasin »).
 * Renvoie l'adresse attendue si la page n'existe pas encore.
 */
function bloomandsun_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );

	return esc_url( $page ? get_permalink( $page ) : home_url( "/$slug/" ) );
}

/**
 * Bloc « lien de navigation » prêt à insérer dans une composition.
 */
function bloomandsun_nav_link( string $label, string $url ): string {
	$attrs = [ 'label' => $label, 'url' => $url, 'kind' => 'custom', 'isTopLevelLink' => true ];

	return '<!-- wp:navigation-link ' . wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ' /-->';
}

/**
 * Liens du menu principal, partagés par l'en-tête (bureau et mobile).
 *
 * @return array<string, array{0: string, 1: string}> clé => [libellé, URL]
 */
function bloomandsun_menu_links(): array {
	return [
		'nouveautes'  => [ 'Nouveautés', bloomandsun_shop_url( [ 'orderby' => 'date' ] ) ],
		'vetements'   => [ 'Vêtements', bloomandsun_category_url( 'vetements' ) ],
		'accessoires' => [ 'Accessoires', bloomandsun_category_url( 'accessoires' ) ],
		'magasin'     => [ 'Le magasin', bloomandsun_page_url( 'le-magasin' ) ],
		'contact'     => [ 'Contact', bloomandsun_page_url( 'contact' ) ],
	];
}

/*
 * Contact Form 7
 * ---------------------------------------------------------------------------
 * Pas de <p>/<br> ajoutés automatiquement dans les formulaires :
 * la mise en page vient du balisage du formulaire et de pages/contact.css.
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/*
 * Éditeurs (extension Classic Editor)
 * ---------------------------------------------------------------------------
 * Articles : éditeur classique (préférence pour l'écriture).
 * Pages : éditeur de blocs, nécessaire aux compositions et blocs du thème
 * (page Contact, blocs « Infos du magasin » et « Plan du magasin »…).
 */

// Réglage par défaut de Classic Editor (« classique pour tous ») : il coupe l'éditeur
// de blocs partout (priorité 100). On le réactive juste après, pour les pages seulement.
add_filter( 'use_block_editor_for_post_type', function ( bool $use_block_editor, string $post_type ): bool {
	return 'page' === $post_type ? true : $use_block_editor;
}, 101, 2 );

// Si l'option « Autoriser les utilisateurs à changer d'éditeur » est activée :
// les pages ne proposent que l'éditeur de blocs.
add_filter( 'classic_editor_enabled_editors_for_post_type', function ( array $editors, string $post_type ): array {
	if ( 'page' === $post_type ) {
		return [ 'classic_editor' => false, 'block_editor' => true ];
	}

	return $editors;
}, 10, 2 );
