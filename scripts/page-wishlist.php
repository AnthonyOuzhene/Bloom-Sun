<?php
/**
 * Crée la page « Liste d'envies » (adresse /liste-d-envies/) et lui attribue
 * le modèle « page-wishlist » (templates/page-wishlist.html, déclaré dans theme.json).
 *
 * ddev wp eval-file scripts/page-wishlist.php   (peut être relancé sans risque)
 */
$slug = 'liste-d-envies';
$page = get_page_by_path( $slug );

if ( $page ) {
	update_post_meta( $page->ID, '_wp_page_template', 'page-wishlist' );
	WP_CLI::success( 'La page « Liste d\'envies » existe déjà : modèle « page-wishlist » attribué.' );
	return;
}

$id = wp_insert_post( [
	'post_type'     => 'page',
	'post_status'   => 'publish',
	'post_title'    => 'Liste d\'envies',
	'post_name'     => $slug,
	'post_content'  => '',
	'page_template' => 'page-wishlist',
] );

if ( is_wp_error( $id ) ) {
	WP_CLI::error( $id->get_error_message() );
}

WP_CLI::success( "Page « Liste d'envies » créée (ID $id) : " . get_permalink( $id ) );
