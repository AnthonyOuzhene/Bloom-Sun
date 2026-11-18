<?php
/**
 * Crée la page « Liste d'envies » (affichée par templates/page-liste-d-envies.html).
 *
 * ddev wp eval-file scripts/page-liste-envies.php   (peut être relancé sans risque)
 */
$slug = 'liste-d-envies';

if ( get_page_by_path( $slug ) ) {
	WP_CLI::success( 'La page « Liste d\'envies » existe déjà.' );
	return;
}

$id = wp_insert_post( [
	'post_type'    => 'page',
	'post_status'  => 'publish',
	'post_title'   => 'Liste d\'envies',
	'post_name'    => $slug,
	'post_content' => '',
] );

if ( is_wp_error( $id ) ) {
	WP_CLI::error( $id->get_error_message() );
}

WP_CLI::success( "Page « Liste d'envies » créée (ID $id) : " . get_permalink( $id ) );
