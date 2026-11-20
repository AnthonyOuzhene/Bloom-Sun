<?php
/**
 * Convertit en WebP les images JPG et PNG déjà présentes dans la médiathèque
 * (les nouvelles le sont à l'envoi : wp-content/themes/bloomandsun/inc/media.php).
 *
 * Pour chaque image : WebP créé à partir de l'original, tailles régénérées en WebP,
 * anciens fichiers supprimés. Les produits, événements et pages qui l'utilisent gardent
 * le même média (même identifiant) : rien à refaire de ce côté.
 *
 * ddev wp eval-file scripts/medias-webp.php   (peut être relancé : ne traite que ce qui reste en JPG/PNG)
 */
require_once ABSPATH . 'wp-admin/includes/image.php';

$ids = get_posts( [
	'post_type'      => 'attachment',
	'post_status'    => 'any',
	'post_mime_type' => BLOOMANDSUN_WEBP_SOURCES,
	'posts_per_page' => -1,
	'fields'         => 'ids',
] );

if ( ! $ids ) {
	WP_CLI::success( 'Aucune image JPG ou PNG : tout est déjà en WebP.' );
	return;
}

$done = 0;

foreach ( $ids as $id ) {
	$current  = get_attached_file( $id );           // fichier affiché (souvent « -scaled »)
	$original = wp_get_original_image_path( $id );  // fichier envoyé à l'origine
	$source   = $original && file_exists( $original ) ? $original : $current;

	if ( ! $source || ! file_exists( $source ) ) {
		WP_CLI::warning( "#$id : fichier introuvable, ignoré." );
		continue;
	}

	$webp = bloomandsun_convert_to_webp( $source );

	if ( '' === $webp ) {
		WP_CLI::warning( "#$id : conversion impossible, image laissée telle quelle." );
		continue;
	}

	// Anciens fichiers : original, version « -scaled » et toutes les tailles.
	$old_files = array_unique( array_filter( [ $source, $current ] ) );
	$meta      = wp_get_attachment_metadata( $id );

	foreach ( (array) ( $meta['sizes'] ?? [] ) as $size ) {
		$old_files[] = path_join( dirname( $current ), $size['file'] );
	}

	// Le média pointe vers le WebP, tailles régénérées en WebP.
	update_attached_file( $id, $webp );
	wp_update_post( [ 'ID' => $id, 'post_mime_type' => 'image/webp' ] );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $webp ) );

	foreach ( $old_files as $file ) {
		if ( $file !== $webp && file_exists( $file ) ) {
			wp_delete_file( $file );
		}
	}

	WP_CLI::log( sprintf( '  + #%d %s → %s', $id, basename( $source ), basename( $webp ) ) );
	$done++;
}

WP_CLI::success( "$done image(s) converties en WebP." );
