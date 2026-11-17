<?php
/**
 * Informations du magasin.
 *
 * Modifiables dans Apparence > Personnaliser > Infos du magasin
 * (WordPress génère le formulaire : on ne déclare ici que les champs).
 * Affichées dans les gabarits via la source de liaison « bloomandsun/store » (inc/bindings.php).
 */

/**
 * Champs : clé => [libellé, type de contrôle, valeur par défaut, aide].
 */
function bloomandsun_store_fields(): array {
	return [
		'open_days'    => [ 'Jours d\'ouverture', 'text', 'Ouvert du mardi au samedi', 'Affiché dans la pastille verte.' ],
		'tagline'      => [ 'Activité', 'text', 'Prêt-à-porter & accessoires féminins', '' ],
		'baseline'     => [ 'Phrase d\'accroche', 'text', 'Une invitation au voyage & good vibes', '' ],
		'street'       => [ 'Adresse', 'text', '61 rue Paul Doumer', '' ],
		'city'         => [ 'Code postal et ville', 'text', '76600 Le Havre', '' ],
		'hours_1_days' => [ 'Horaires — jours (ligne 1)', 'text', 'Mardi – samedi', '' ],
		'hours_1_time' => [ 'Horaires — heures (ligne 1)', 'text', '10h – 19h', '' ],
		'hours_2_days' => [ 'Horaires — jours (ligne 2)', 'text', 'Dimanche, lundi', '' ],
		'hours_2_time' => [ 'Horaires — heures (ligne 2)', 'text', 'Fermé', '' ],
		'instagram'    => [ 'Lien Instagram', 'url', 'https://www.instagram.com/bloomandsun_boutique/', '' ],
	];
}

/**
 * Valeur d'une information du magasin (réglage enregistré, sinon valeur par défaut).
 * Clés calculées : « address » (adresse complète) et « directions » (lien d'itinéraire).
 */
function bloomandsun_store( string $key ): string {
	$fields = bloomandsun_store_fields();

	if ( isset( $fields[ $key ] ) ) {
		return (string) get_theme_mod( "bloomandsun_store_$key", $fields[ $key ][2] );
	}

	$address = trim( bloomandsun_store( 'street' ) . ', ' . bloomandsun_store( 'city' ), ', ' );

	return match ( $key ) {
		'address'    => $address,
		'directions' => 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address ),
		default      => '',
	};
}

/**
 * Toutes les informations, clé => valeur (pour l'éditeur).
 */
function bloomandsun_store_all(): array {
	$keys = array_merge( array_keys( bloomandsun_store_fields() ), [ 'address', 'directions' ] );

	return array_combine( $keys, array_map( 'bloomandsun_store', $keys ) );
}

// Apparence > Personnaliser > Infos du magasin
add_action( 'customize_register', function ( WP_Customize_Manager $customizer ) {
	$customizer->add_section( 'bloomandsun_store', [
		'title'       => 'Infos du magasin',
		'description' => 'Affichées sur la page Contact, dans la section « Le magasin » de l\'accueil et dans le pied de page.',
		'priority'    => 30,
	] );

	foreach ( bloomandsun_store_fields() as $key => [ $label, $type, $default, $help ] ) {
		$customizer->add_setting( "bloomandsun_store_$key", [
			'default'           => $default,
			'sanitize_callback' => 'url' === $type ? 'esc_url_raw' : 'sanitize_text_field',
		] );

		$customizer->add_control( "bloomandsun_store_$key", [
			'label'       => $label,
			'description' => $help,
			'section'     => 'bloomandsun_store',
			'type'        => $type,
		] );
	}
} );
