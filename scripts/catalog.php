<?php
/**
 * Bloom & Sun — organisation du catalogue WooCommerce.
 *
 * Crée (ou met à jour) :
 * - les catégories : Vêtements et Accessoires, avec leurs sous-catégories ;
 * - les attributs globaux : Taille, Couleur, Matière, avec leurs valeurs ;
 * - l'étiquette « Idée cadeau ».
 *
 * Peut être relancé sans risque : ce qui existe déjà est réutilisé.
 * Les anciennes catégories ne sont supprimées que si elles ne contiennent aucun produit.
 *
 * Lancement :   ddev wp eval-file scripts/catalog.php
 * Production :  wp eval-file scripts/catalog.php
 */

if ( ! defined( 'WP_CLI' ) || ! function_exists( 'wc_create_attribute' ) ) {
	echo "À lancer avec WP-CLI, WooCommerce activé.\n";
	return;
}

/*
 * Ce que l'on veut obtenir
 * ---------------------------------------------------------------------------
 */
$categories = [
	'vetements'   => [ 'Vêtements', [
		'robes'     => 'Robes',
		'pulls'     => 'Pulls',
		'gilets'    => 'Gilets',
		'vestes'    => 'Vestes',
		'manteaux'  => 'Manteaux',
		'pantalons' => 'Pantalons',
		'jupes'     => 'Jupes',
		'jeans'     => 'Jeans',
	] ],
	'accessoires' => [ 'Accessoires', [
		'sacs-et-maroquinerie' => 'Sacs & maroquinerie',
		'ceintures'            => 'Ceintures',
		'foulards-et-echarpes' => 'Foulards & écharpes',
		'bijoux'               => 'Bijoux',
		'cagoules'             => 'Cagoules',
		'bougies-et-plantes'   => 'Bougies & plantes',
	] ],
];

// Anciennes catégories (première maquette) : supprimées si elles sont vides.
$obsolete = [ 'hauts-et-mailles', 'pantalons-et-jupes', 'vestes-et-manteaux' ];

$attributes = [
	'taille'  => [ 'Taille', [ 'XS', 'S', 'M', 'L', 'XL', 'Taille unique', '34', '36', '38', '40', '42', '44' ] ],
	'couleur' => [ 'Couleur', [ 'Écru', 'Blanc', 'Beige', 'Camel', 'Marron', 'Noir', 'Gris', 'Rose', 'Rouge', 'Bordeaux', 'Vert', 'Bleu', 'Doré', 'Argenté' ] ],
	'matiere' => [ 'Matière', [ 'Coton', 'Laine', 'Cachemire', 'Lin', 'Soie', 'Viscose', 'Denim', 'Cuir', 'Synthétique' ] ],
];

$tags = [ 'idee-cadeau' => 'Idée cadeau' ];

/*
 * Outils
 * ---------------------------------------------------------------------------
 */
$category = static function ( string $slug, string $name, int $parent, int $order ): int {
	$term = get_term_by( 'slug', $slug, 'product_cat' );

	if ( $term ) {
		wp_update_term( $term->term_id, 'product_cat', [ 'name' => $name, 'parent' => $parent ] );
		$id = (int) $term->term_id;
		WP_CLI::log( "  = $name (existe déjà)" );
	} else {
		$result = wp_insert_term( $name, 'product_cat', [ 'slug' => $slug, 'parent' => $parent ] );
		if ( is_wp_error( $result ) ) {
			WP_CLI::warning( "$name : " . $result->get_error_message() );
			return 0;
		}
		$id = (int) $result['term_id'];
		WP_CLI::log( "  + $name" );
	}

	update_term_meta( $id, 'order', $order ); // ordre d'affichage dans WooCommerce

	return $id;
};

/*
 * 1. Catégories
 * ---------------------------------------------------------------------------
 */
WP_CLI::log( 'Catégories :' );
$order = 0;
foreach ( $categories as $parent_slug => [ $parent_name, $children ] ) {
	$parent_id = $category( $parent_slug, $parent_name, 0, $order++ );
	$child_order = 0;
	foreach ( $children as $slug => $name ) {
		$category( $slug, $name, $parent_id, $child_order++ );
	}
}

WP_CLI::log( 'Anciennes catégories :' );
foreach ( $obsolete as $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term ) {
		continue;
	}
	if ( 0 === (int) $term->count ) {
		wp_delete_term( $term->term_id, 'product_cat' );
		WP_CLI::log( "  - {$term->name} (supprimée, elle était vide)" );
	} else {
		WP_CLI::warning( "« {$term->name} » contient {$term->count} produit(s) : déplace-les vers les nouvelles catégories, puis relance le script." );
	}
}

/*
 * 2. Attributs et leurs valeurs
 * ---------------------------------------------------------------------------
 */
WP_CLI::log( 'Attributs :' );
foreach ( $attributes as $slug => [ $name, $values ] ) {
	if ( ! wc_attribute_taxonomy_id_by_name( $slug ) ) {
		$created = wc_create_attribute( [ 'name' => $name, 'slug' => $slug, 'type' => 'select', 'order_by' => 'menu_order', 'has_archives' => false ] );
		if ( is_wp_error( $created ) ) {
			WP_CLI::warning( "$name : " . $created->get_error_message() );
			continue;
		}
		WP_CLI::log( "  + $name" );
	} else {
		WP_CLI::log( "  = $name (existe déjà)" );
	}

	// La taxonomie d'un attribut tout juste créé n'est enregistrée qu'à la requête suivante.
	$taxonomy = wc_attribute_taxonomy_name( $slug );
	if ( ! taxonomy_exists( $taxonomy ) ) {
		register_taxonomy( $taxonomy, 'product', [ 'hierarchical' => false ] );
	}

	foreach ( $values as $position => $value ) {
		$existing = term_exists( $value, $taxonomy );
		$term_id  = $existing ? (int) $existing['term_id'] : (int) ( wp_insert_term( $value, $taxonomy )['term_id'] ?? 0 );
		if ( $term_id ) {
			update_term_meta( $term_id, 'order', $position );
		}
	}
	WP_CLI::log( '      ' . implode( ', ', $values ) );
}

/*
 * 3. Étiquettes
 * ---------------------------------------------------------------------------
 */
WP_CLI::log( 'Étiquettes :' );
foreach ( $tags as $slug => $name ) {
	if ( get_term_by( 'slug', $slug, 'product_tag' ) ) {
		WP_CLI::log( "  = $name (existe déjà)" );
	} else {
		wp_insert_term( $name, 'product_tag', [ 'slug' => $slug ] );
		WP_CLI::log( "  + $name" );
	}
}

WP_CLI::log( 'Marques : ' . ( taxonomy_exists( 'product_brand' ) ? 'disponibles dans Produits > Marques.' : 'non disponibles avec cette version de WooCommerce.' ) );
WP_CLI::success( 'Catalogue prêt.' );
