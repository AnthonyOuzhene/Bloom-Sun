<?php
/**
 * Bloom & Sun — produits de DÉMONSTRATION (développement uniquement).
 *
 * Crée une douzaine de produits fictifs avec photos (scripts/demo-images/), catégories,
 * variations de taille et de couleur, une promotion et des « Idées cadeaux ».
 * Tous les produits et photos créés portent la marque _bs_demo : ils se suppriment d'un coup.
 *
 * Créer :     ddev wp eval-file scripts/demo-produits.php
 * Supprimer : ddev wp eval-file scripts/demo-produits.php supprimer
 *
 * À NE PAS lancer en production.
 */

if ( ! defined( 'WP_CLI' ) || ! function_exists( 'wc_get_product' ) ) {
	echo "À lancer avec WP-CLI, WooCommerce activé.\n";
	return;
}

/*
 * Suppression
 * ---------------------------------------------------------------------------
 */
$demo_ids = static fn( string $type ): array => get_posts( [
	'post_type'      => $type,
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'fields'         => 'ids',
	'meta_key'       => '_bs_demo',
] );

if ( in_array( 'supprimer', $args ?? [], true ) ) {
	$products = $demo_ids( 'product' );
	foreach ( $products as $id ) {
		$product = wc_get_product( $id );
		if ( $product ) {
			foreach ( $product->get_children() as $child ) {
				wp_delete_post( $child, true );
			}
			$product->delete( true );
		}
	}
	$images = $demo_ids( 'attachment' );
	foreach ( $images as $id ) {
		wp_delete_attachment( $id, true );
	}
	WP_CLI::success( count( $products ) . ' produits et ' . count( $images ) . ' photos de démonstration supprimés.' );
	return;
}

if ( $demo_ids( 'product' ) ) {
	WP_CLI::error( 'Des produits de démonstration existent déjà. Supprime-les d\'abord : ddev wp eval-file scripts/demo-produits.php supprimer' );
}

/*
 * Outils
 * ---------------------------------------------------------------------------
 */
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$image = static function ( string $name, string $alt ): int {
	static $cache = [];
	if ( isset( $cache[ $name ] ) ) {
		return $cache[ $name ];
	}
	$source = __DIR__ . "/demo-images/$name.jpg";
	$tmp    = wp_tempnam( $name );
	copy( $source, $tmp );
	$id = media_handle_sideload( [ 'name' => "$name.jpg", 'tmp_name' => $tmp ], 0 );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "$name : " . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_bs_demo', 1 );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return $cache[ $name ] = $id;
};

$term_id = static fn( string $slug, string $taxonomy ): int => (int) ( get_term_by( 'slug', $slug, $taxonomy )->term_id ?? 0 );

$attribute = static function ( string $slug, array $values, bool $variation ) use ( $term_id ): WC_Product_Attribute {
	$taxonomy = wc_attribute_taxonomy_name( $slug );
	$attr     = new WC_Product_Attribute();
	$attr->set_id( wc_attribute_taxonomy_id_by_name( $slug ) );
	$attr->set_name( $taxonomy );
	$attr->set_options( array_map( static fn( $v ) => $term_id( sanitize_title( $v ), $taxonomy ), $values ) );
	$attr->set_visible( true );
	$attr->set_variation( $variation );
	return $attr;
};

/*
 * Les produits
 * [nom, catégorie, prix, prix soldé, photos, tailles, couleurs, matière, idée cadeau, description]
 * ---------------------------------------------------------------------------
 */
$products = [
	[ 'Cardigan Lou', 'gilets', 79, null, [ 'cardigan-creme', 'maille-texture' ], [ 'S', 'M', 'L' ], [ 'Écru', 'Gris' ], 'Laine', false, 'Un cardigan épais en maille torsadée, à porter ouvert sur une robe ou fermé sur un jean. Coupe légèrement oversize.' ],
	[ 'Gilet Anna', 'gilets', 65, null, [ 'cardigan-gris' ], [ 'S', 'M', 'L' ], [ 'Gris' ], 'Laine', false, 'Gilet long et enveloppant, idéal en demi-saison.' ],
	[ 'Robe Capucine', 'robes', 85, null, [ 'robe-creme' ], [ 'XS', 'S', 'M', 'L' ], [ 'Écru' ], 'Coton', false, 'Robe fluide à manches longues, taille marquée par une ceinture à nouer.' ],
	[ 'Robe Margaux', 'robes', 92, 69, [ 'robe-fleurie-rose' ], [ 'S', 'M', 'L' ], [ 'Rose' ], 'Viscose', false, 'Robe imprimée fleurie, volants aux épaules. Légère et lumineuse.' ],
	[ 'Jupe plissée Ondine', 'jupes', 59, null, [ 'jupe-plissee' ], [ 'S', 'M', 'L' ], [ 'Beige' ], 'Synthétique', false, 'Jupe midi plissée qui accompagne chaque pas.' ],
	[ 'Jupe Salomé', 'jupes', 72, null, [ 'jupe-verte' ], [ 'S', 'M' ], [ 'Vert' ], 'Viscose', false, 'Jupe longue satinée, un vert profond pour les soirées d\'hiver.' ],
	[ 'Manteau Suzanne', 'manteaux', 169, 139, [ 'manteau-beige' ], [ 'S', 'M', 'L' ], [ 'Camel' ], 'Laine', false, 'Manteau droit en drap de laine, col tailleur, doublé.' ],
	[ 'Manteau Iris', 'manteaux', 189, null, [ 'manteau-rose' ], [ 'S', 'M', 'L' ], [ 'Rose' ], 'Laine', false, 'Trench ceinturé rose poudré, la pièce forte de la saison.' ],
	[ 'Veste Louise', 'vestes', 129, null, [ 'veste-fourrure' ], [ 'S', 'M', 'L' ], [ 'Camel' ], 'Synthétique', false, 'Veste camel à col en fausse fourrure, chaude et douce.' ],
	[ 'Pull Maëlle', 'pulls', 69, null, [ 'col-roule-camel' ], [ 'S', 'M', 'L', 'XL' ], [ 'Camel' ], 'Cachemire', true, 'Col roulé en maille fine, toucher cachemire.' ],
	[ 'Cabas Riviera', 'sacs-et-maroquinerie', 55, null, [ 'sac-paille' ], [], [], '', true, 'Grand cabas en paille tressée, anses en cuir.' ],
	[ 'Sac Jeanne', 'sacs-et-maroquinerie', 95, null, [ 'sac-cuir' ], [], [], 'Cuir', false, 'Sac en cuir grainé, bandoulière réglable.' ],
	[ 'Écharpe Alba', 'foulards-et-echarpes', 39, null, [ 'echarpe-carreaux' ], [], [], 'Laine', true, 'Écharpe à carreaux, laine douce.' ],
	[ 'Bougie Ambre', 'bougies-et-plantes', 29, null, [ 'bougie-pot' ], [], [], '', true, 'Bougie parfumée à l\'ambre, coulée dans un pot en verre. 40 h de combustion.' ],
	[ 'Plante en boîte Flora', 'bougies-et-plantes', 35, null, [ 'plante-orchidee' ], [], [], '', true, 'Une plante prête à offrir, dans sa boîte cadeau.' ],
	[ 'Collier Soleil', 'bijoux', 42, null, [ 'collier' ], [], [], '', true, 'Collier fin et sa médaille soleil, finition dorée.' ],
];

/*
 * Création
 * ---------------------------------------------------------------------------
 */
$created = 0;
foreach ( $products as $i => [ $name, $cat, $price, $sale, $photos, $sizes, $colors, $material, $gift, $text ] ) {
	$variable = (bool) $sizes;
	$product  = $variable ? new WC_Product_Variable() : new WC_Product_Simple();

	$product->set_name( $name );
	$product->set_status( 'publish' );
	$product->set_description( $text );
	$product->set_short_description( $text );
	$product->set_category_ids( array_filter( [ $term_id( $cat, 'product_cat' ) ] ) );
	$product->set_sku( 'DEMO-' . strtoupper( sanitize_title( $name ) ) );

	$ids = array_values( array_filter( array_map( static fn( $p ) => $image( $p, $name ), $photos ) ) );
	if ( $ids ) {
		$product->set_image_id( $ids[0] );
		$product->set_gallery_image_ids( array_slice( $ids, 1 ) );
	}

	$attributes = [];
	if ( $sizes ) {
		$attributes[] = $attribute( 'taille', $sizes, true );
	}
	if ( $colors ) {
		$attributes[] = $attribute( 'couleur', $colors, count( $colors ) > 1 );
	}
	if ( $material ) {
		$attributes[] = $attribute( 'matiere', [ $material ], false );
	}
	$product->set_attributes( $attributes );

	if ( ! $variable ) {
		$product->set_regular_price( (string) $price );
		if ( $sale ) {
			$product->set_sale_price( (string) $sale );
		}
		$product->set_manage_stock( true );
		$product->set_stock_quantity( 4 );
	}

	$id = $product->save();
	update_post_meta( $id, '_bs_demo', 1 );
	if ( $gift ) {
		wp_set_object_terms( $id, 'idee-cadeau', 'product_tag', true );
	}

	// Variations : une par taille (× couleur si plusieurs), stock de 1 à 3, une taille épuisée
	if ( $variable ) {
		$color_options = count( $colors ) > 1 ? $colors : [ null ];
		foreach ( $color_options as $color ) {
			foreach ( $sizes as $n => $size ) {
				$variation = new WC_Product_Variation();
				$variation->set_parent_id( $id );
				$attrs = [ wc_attribute_taxonomy_name( 'taille' ) => sanitize_title( $size ) ];
				if ( $color ) {
					$attrs[ wc_attribute_taxonomy_name( 'couleur' ) ] = sanitize_title( $color );
				}
				$variation->set_attributes( $attrs );
				$variation->set_regular_price( (string) $price );
				if ( $sale ) {
					$variation->set_sale_price( (string) $sale );
				}
				$variation->set_manage_stock( true );
				$variation->set_stock_quantity( 0 === ( $i + $n ) % 4 ? 0 : ( $n % 3 ) + 1 );
				$variation->save();
			}
		}
		WC_Product_Variable::sync( $id );
	}

	// Ordre de publication : les premiers de la liste sont les plus récents
	wp_update_post( [ 'ID' => $id, 'post_date' => wp_date( 'Y-m-d H:i:s', time() - $i * HOUR_IN_SECONDS ) ] );
	$created++;
	WP_CLI::log( "  + $name" . ( $variable ? ' (' . implode( '/', $sizes ) . ')' : '' ) . ( $gift ? ' · idée cadeau' : '' ) );
}

wc_delete_product_transients();
WP_CLI::success( "$created produits de démonstration créés. Pour les supprimer : ddev wp eval-file scripts/demo-produits.php supprimer" );
