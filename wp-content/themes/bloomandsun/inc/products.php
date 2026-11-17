<?php
/**
 * Fiche produit (back-office) : case à cocher « Idée cadeau ».
 *
 * La case pilote l'étiquette produit « Idée cadeau » (BLOOMANDSUN_GIFT_TAG, inc/home.php) :
 * cochée = étiquette ajoutée, décochée = étiquette retirée.
 * La section « Idées cadeaux » de l'accueil affiche les produits qui la portent.
 * Le champ est généré par WooCommerce (woocommerce_wp_checkbox) : aucun HTML ici.
 */

const BLOOMANDSUN_GIFT_FIELD = '_bloomandsun_gift';

// Onglet « Général » des données produit : la case, sous les prix.
add_action( 'woocommerce_product_options_general_product_data', function () {
	global $post;

	woocommerce_wp_checkbox( [
		'id'          => BLOOMANDSUN_GIFT_FIELD,
		'label'       => 'Idée cadeau',
		'description' => 'Afficher ce produit dans la section « Idées cadeaux » de l\'accueil.',
		'value'       => has_term( BLOOMANDSUN_GIFT_TAG, 'product_tag', $post ) ? 'yes' : 'no',
	] );
} );

// Enregistrement : ajoute ou retire l'étiquette selon la case.
add_action( 'woocommerce_process_product_meta', function ( int $product_id ) {
	$checked = isset( $_POST[ BLOOMANDSUN_GIFT_FIELD ] ); // phpcs:ignore WordPress.Security.NonceVerification -- nonce vérifié par WooCommerce

	if ( $checked ) {
		wp_add_object_terms( $product_id, BLOOMANDSUN_GIFT_TAG, 'product_tag' );
	} else {
		wp_remove_object_terms( $product_id, BLOOMANDSUN_GIFT_TAG, 'product_tag' );
	}
}, 20 );
