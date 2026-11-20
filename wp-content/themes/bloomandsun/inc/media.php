<?php
/**
 * Médias : toutes les images en WebP.
 *
 * - À l'envoi (médiathèque, fiche produit, téléversement par script) : une image JPG ou PNG est
 *   convertie en WebP avant d'être enregistrée ; l'original n'est pas gardé.
 * - Les tailles générées par WordPress et WooCommerce (vignettes, galerie, -scaled…) sont en WebP.
 * - GIF et SVG ne sont pas touchés.
 *
 * Images déjà en base : scripts/media-webp.php.
 */

const BLOOMANDSUN_WEBP_QUALITY = 82;
const BLOOMANDSUN_WEBP_SOURCES = [ 'image/jpeg', 'image/png' ];

/**
 * Convertit une image en WebP dans le même dossier (nom unique). Renvoie le chemin du WebP, vide en cas d'échec.
 * L'original n'est pas supprimé.
 */
function bloomandsun_convert_to_webp( string $path ): string {
	$editor = wp_get_image_editor( $path );

	if ( is_wp_error( $editor ) ) {
		return '';
	}

	$editor->maybe_exif_rotate(); // photos de téléphone : on applique l'orientation avant de perdre l'EXIF
	$editor->set_quality( BLOOMANDSUN_WEBP_QUALITY );

	$dir    = dirname( $path );
	$target = wp_unique_filename( $dir, pathinfo( $path, PATHINFO_FILENAME ) . '.webp' );
	$saved  = $editor->save( "$dir/$target", 'image/webp' );

	return is_wp_error( $saved ) ? '' : (string) $saved['path'];
}

// Fichier envoyé : remplacé par sa version WebP avant la création du média.
add_filter( 'wp_handle_upload', function ( array $upload ): array {
	if ( empty( $upload['file'] ) || ! in_array( $upload['type'] ?? '', BLOOMANDSUN_WEBP_SOURCES, true ) ) {
		return $upload;
	}

	$webp = bloomandsun_convert_to_webp( $upload['file'] );

	if ( '' === $webp ) {
		return $upload; // conversion impossible : on garde l'original plutôt que de perdre l'image
	}

	wp_delete_file( $upload['file'] );

	return [
		'file' => $webp,
		'url'  => trailingslashit( dirname( $upload['url'] ) ) . basename( $webp ),
		'type' => 'image/webp',
	];
} );

// Tailles générées : en WebP.
add_filter( 'image_editor_output_format', function ( array $formats ): array {
	return array_merge( $formats, array_fill_keys( BLOOMANDSUN_WEBP_SOURCES, 'image/webp' ) );
} );

// Qualité du WebP.
add_filter( 'wp_editor_set_quality', function ( int $quality, string $mime_type ): int {
	return 'image/webp' === $mime_type ? BLOOMANDSUN_WEBP_QUALITY : $quality;
}, 10, 2 );
