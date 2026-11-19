<?php
/**
 * Page d'accueil : contenus administrables par la cliente.
 *
 * - Section « Idées cadeaux » : produits portant l'étiquette « Idée cadeau »,
 *   textes et affichage réglables (menu « Page d'accueil »).
 * - Section « Nos collections » : choix des 5 catégories mises en avant ;
 *   nom, lien et photo viennent de la catégorie elle-même.
 *
 * Affichées dans les gabarits via la source de liaison « bloomandsun/home » (inc/bindings.php).
 */

const BLOOMANDSUN_GIFT_TAG = 'idee-cadeau';

bloomandsun_register_settings_page( [
	'id'       => 'home',
	'option'   => 'bloomandsun_home',
	'title'    => 'Page d\'accueil',
	'icon'     => 'dashicons-admin-home',
	'position' => 57,
	'intro'    => 'Contenus de la page d\'accueil que vous pouvez faire évoluer selon vos envies.',
	'fields'   => [
		'gifts_visible'  => [ 'section' => 'Idées cadeaux', 'label' => 'Afficher la section', 'type' => 'checkbox', 'default' => '1', 'help' => 'Produits affichés : ceux dont la case « Idée cadeau » est cochée dans la fiche produit. Sans aucun produit, la section reste masquée.' ],
		'gifts_position' => [ 'section' => 'Idées cadeaux', 'label' => 'Position', 'type' => 'select', 'default' => 'after', 'options' => [ 'top' => 'En haut de page (temps fort : fêtes, fête des mères…)', 'after' => 'Après les nouveautés' ] ],
		'gifts_color'    => [ 'section' => 'Idées cadeaux', 'label' => 'Couleur', 'type' => 'select', 'default' => 'terracotta', 'options' => [ 'terracotta' => 'Terracotta', 'sapin' => 'Vert sapin et or' ] ],
		'gifts_ribbon'   => [ 'section' => 'Idées cadeaux', 'label' => 'Ruban', 'type' => 'text', 'default' => '', 'help' => 'Texte du ruban en angle, ex. « Noël · Fête des mères ». Laisser vide pour ne pas afficher de ruban.' ],
		'gifts_kicker'   => [ 'section' => 'Idées cadeaux', 'label' => 'Petit titre', 'type' => 'text', 'default' => 'Sélection du moment' ],
		'gifts_title'    => [ 'section' => 'Idées cadeaux', 'label' => 'Titre', 'type' => 'text', 'default' => 'Idées cadeaux' ],
		'gifts_text'     => [ 'section' => 'Idées cadeaux', 'label' => 'Texte', 'type' => 'textarea', 'default' => 'Bougies, plantes en boîte, foulards, bijoux… de jolies attentions choisies en magasin, à offrir ou à s\'offrir.' ],
		'gifts_button'   => [ 'section' => 'Idées cadeaux', 'label' => 'Texte du bouton', 'type' => 'text', 'default' => 'Toutes les idées cadeaux' ],
		'gifts_count'    => [ 'section' => 'Idées cadeaux', 'label' => 'Nombre de produits', 'type' => 'select', 'default' => '6', 'options' => [ '4' => '4 produits', '6' => '6 produits', '8' => '8 produits', '10' => '10 produits' ] ],
		'events_notice'  => [ 'section' => 'Événements', 'label' => 'Annoncer l\'événement', 'type' => 'select', 'default' => '7', 'options' => [ '0' => 'Seulement le jour même', '3' => '3 jours avant', '7' => '7 jours avant', '14' => '14 jours avant' ], 'help' => 'Avant l\'événement : une ligne dans le bandeau d\'annonce. Pendant : la pastille sur le côté droit. Les événements se gèrent dans le menu « Événements ».' ],
		'card_1'        => [ 'section' => 'Nos collections', 'label' => 'Carte 1', 'type' => 'select', 'default' => 'robes', 'options' => 'bloomandsun_category_options', 'help' => 'La photo de chaque carte est celle de la catégorie (Produits > Catégories > Miniature).' ],
		'card_2'        => [ 'section' => 'Nos collections', 'label' => 'Carte 2', 'type' => 'select', 'default' => 'pulls', 'options' => 'bloomandsun_category_options' ],
		'card_3'        => [ 'section' => 'Nos collections', 'label' => 'Carte 3', 'type' => 'select', 'default' => 'manteaux', 'options' => 'bloomandsun_category_options' ],
		'card_4'        => [ 'section' => 'Nos collections', 'label' => 'Carte 4', 'type' => 'select', 'default' => 'jeans', 'options' => 'bloomandsun_category_options' ],
		'card_5'        => [ 'section' => 'Nos collections', 'label' => 'Carte 5', 'type' => 'select', 'default' => 'accessoires', 'options' => 'bloomandsun_category_options' ],
	],
] );

/**
 * Catégories de produits proposées dans les listes : slug => « Parent › Catégorie ».
 */
function bloomandsun_category_options(): array {
	$terms = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false, 'orderby' => 'name' ] );

	if ( is_wp_error( $terms ) ) {
		return [];
	}

	$options = [];
	foreach ( $terms as $term ) {
		$parent                   = $term->parent ? get_term( $term->parent, 'product_cat' ) : null;
		$options[ $term->slug ] = ( $parent && ! is_wp_error( $parent ) ? $parent->name . ' › ' : '' ) . $term->name;
	}
	asort( $options );

	return $options;
}

/**
 * Valeur d'un contenu de l'accueil.
 * Clés calculées : card_N_label, card_N_url, card_N_image (d'après la catégorie choisie).
 */
function bloomandsun_home( string $key ): string {
	if ( preg_match( '/^card_(\d)_(label|url|image)$/', $key, $m ) ) {
		$slug = bloomandsun_setting( 'home', "card_{$m[1]}" );
		$term = get_term_by( 'slug', $slug, 'product_cat' );

		return match ( $m[2] ) {
			'label' => $term ? $term->name : ucfirst( str_replace( '-', ' ', $slug ) ),
			'url'   => bloomandsun_link_category( $slug ),
			'image' => $term ? (string) wp_get_attachment_image_url( (int) get_term_meta( $term->term_id, 'thumbnail_id', true ), 'large' ) : '',
		};
	}

	return bloomandsun_setting( 'home', $key );
}

/**
 * Tous les contenus, clé => valeur (pour l'aperçu dans l'éditeur).
 */
function bloomandsun_home_all(): array {
	$keys = array_keys( bloomandsun_settings_registry()['home']['fields'] );
	foreach ( range( 1, 5 ) as $n ) {
		array_push( $keys, "card_{$n}_label", "card_{$n}_url", "card_{$n}_image" );
	}

	return array_combine( $keys, array_map( 'bloomandsun_home', $keys ) );
}

/**
 * La section « Idées cadeaux » doit-elle s'afficher ?
 */
function bloomandsun_gifts_visible(): bool {
	$tag = get_term_by( 'slug', BLOOMANDSUN_GIFT_TAG, 'product_tag' );

	return '1' === bloomandsun_home( 'gifts_visible' ) && $tag && $tag->count > 0;
}

// Produits de la section : ceux qui portent l'étiquette « Idée cadeau » (nombre réglable).
add_filter( 'render_block_data', function ( array $block ): array {
	$classes = explode( ' ', $block['attrs']['className'] ?? '' );

	if ( 'woocommerce/product-collection' === $block['blockName'] && in_array( 'bs-gifts-products', $classes, true ) ) {
		$tag = get_term_by( 'slug', BLOOMANDSUN_GIFT_TAG, 'product_tag' );

		$block['attrs']['query']['taxQuery'] = [ 'product_tag' => [ $tag ? (int) $tag->term_id : 0 ] ];
		$block['attrs']['query']['perPage']  = (int) bloomandsun_home( 'gifts_count' );
	}

	return $block;
} );

/*
 * Emplacements de la section dans templates/front-page.html :
 * deux parties « gifts », l'une en haut (bs-gifts-slot--top), l'autre après les nouveautés
 * (bs-gifts-slot--after). Seule celle qui correspond au réglage « Position » est rendue.
 */
add_filter( 'pre_render_block', function ( ?string $pre, array $block ): ?string {
	if ( 'core/template-part' !== $block['blockName'] || 'home/gifts' !== ( $block['attrs']['slug'] ?? '' ) ) {
		return $pre;
	}

	$classes = explode( ' ', $block['attrs']['className'] ?? '' );
	$slot    = in_array( 'bs-gifts-slot--top', $classes, true ) ? 'top' : 'after';

	return ( bloomandsun_gifts_visible() && bloomandsun_home( 'gifts_position' ) === $slot ) ? $pre : '';
}, 10, 2 );

add_filter( 'render_block', function ( string $html, array $block ): string {
	$classes = explode( ' ', $block['attrs']['className'] ?? '' );

	// Section : masquée si désactivée ou sans produit ; couleur choisie ; script du carrousel.
	if ( 'core/group' === $block['blockName'] && in_array( 'bs-gifts', $classes, true ) ) {
		if ( ! bloomandsun_gifts_visible() ) {
			return '';
		}

		bloomandsun_enqueue_carousel();

		$tags = new WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag() ) {
			$tags->add_class( 'bs-gifts--' . sanitize_html_class( bloomandsun_home( 'gifts_color' ) ) );
		}

		return $tags->get_updated_html();
	}

	// Ruban sans texte : pas de ruban.
	if ( 'core/paragraph' === $block['blockName'] && in_array( 'bs-gifts__ribbon', $classes, true ) && '' === trim( wp_strip_all_tags( $html ) ) ) {
		return '';
	}

	// Carte de catégorie sans photo : pas d'image vide.
	if ( 'core/image' === $block['blockName'] && in_array( 'bs-cat__image', $classes, true ) && ! preg_match( '/<img[^>]+src="[^"]+"/', $html ) ) {
		return '';
	}

	return $html;
}, 20, 2 );
