<?php
/**
 * Contenus en base : pas de balise de style (<em>, <strong>, <br>, style="…").
 * - titres de la page Contact : <em> → <span class="bs-accent">
 * - page Panier : bloc « Espacement » de WooCommerce (style="height:40px") retiré
 *
 * ddev wp eval-file scripts/contenus-sans-style.php   (peut être relancé sans risque)
 */
$pages = [
	'contact' => static fn( string $c ): string => preg_replace(
		'#(<h[1-3] class="wp-block-heading[^"]*">[^<]*)<em>(.*?)</em>#',
		'$1<span class="bs-accent">$2</span>',
		$c
	),
	'panier'  => static fn( string $c ): string => preg_replace(
		'#\s*<!-- wp:spacer \{[^}]*\} -->\s*<div style="[^"]*" aria-hidden="true" class="wp-block-spacer"></div>\s*<!-- /wp:spacer -->#',
		'',
		$c
	),
];

foreach ( $pages as $slug => $clean ) {
	$page = get_page_by_path( $slug );

	if ( ! $page ) {
		WP_CLI::warning( "Page « $slug » introuvable." );
		continue;
	}

	$content = $clean( $page->post_content );

	if ( $content === $page->post_content ) {
		WP_CLI::log( "  = $slug : rien à changer" );
		continue;
	}

	wp_update_post( [ 'ID' => $page->ID, 'post_content' => wp_slash( $content ) ] );
	WP_CLI::log( "  + $slug : nettoyée" );
}

WP_CLI::success( 'Contenus vérifiés.' );
