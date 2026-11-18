<?php
/**
 * Informations du magasin.
 *
 * Page de réglages « Infos du magasin » (moteur commun : inc/settings.php).
 * Affichées dans les gabarits via la source de liaison « bloomandsun/store » (inc/bindings.php).
 */

bloomandsun_register_settings_page( [
	'id'       => 'store',
	'option'   => 'bloomandsun_store',
	'title'    => 'Infos du magasin',
	'icon'     => 'dashicons-store',
	'position' => 58,
	'intro'    => 'Ces informations s\'affichent sur la page Contact, dans la section « Le magasin » de l\'accueil et dans le pied de page.',
	'fields'   => [
		'open_days'    => [ 'section' => 'Présentation', 'label' => 'Jours d\'ouverture', 'type' => 'text', 'default' => 'Ouvert du mardi au samedi', 'help' => 'Affiché dans la pastille verte.' ],
		'tagline'      => [ 'section' => 'Présentation', 'label' => 'Activité', 'type' => 'text', 'default' => 'Prêt-à-porter & accessoires féminins' ],
		'baseline'     => [ 'section' => 'Présentation', 'label' => 'Phrase d\'accroche', 'type' => 'text', 'default' => 'Une invitation au voyage & good vibes' ],
		'street'       => [ 'section' => 'Adresse', 'label' => 'Rue', 'type' => 'text', 'default' => '61 rue Paul Doumer' ],
		'city'         => [ 'section' => 'Adresse', 'label' => 'Code postal et ville', 'type' => 'text', 'default' => '76600 Le Havre' ],
		'hours_1_days' => [ 'section' => 'Horaires', 'label' => 'Jours (ligne 1)', 'type' => 'text', 'default' => 'Mardi – samedi' ],
		'hours_1_time' => [ 'section' => 'Horaires', 'label' => 'Heures (ligne 1)', 'type' => 'text', 'default' => '10h – 19h' ],
		'hours_2_days' => [ 'section' => 'Horaires', 'label' => 'Jours (ligne 2)', 'type' => 'text', 'default' => 'Dimanche, lundi' ],
		'hours_2_time' => [ 'section' => 'Horaires', 'label' => 'Heures (ligne 2)', 'type' => 'text', 'default' => 'Fermé' ],
		'instagram'    => [ 'section' => 'Réseaux sociaux', 'label' => 'Lien Instagram', 'type' => 'url', 'default' => 'https://www.instagram.com/bloomandsun_boutique/' ],
		'facebook'     => [ 'section' => 'Réseaux sociaux', 'label' => 'Lien Facebook', 'type' => 'url', 'default' => 'https://www.facebook.com/profile.php?id=61594915953501' ],
	],
] );

/**
 * Valeur d'une information du magasin.
 * Clés calculées : « address » (adresse complète) et « directions » (lien d'itinéraire).
 */
function bloomandsun_store( string $key ): string {
	$address = trim( bloomandsun_setting( 'store', 'street' ) . ', ' . bloomandsun_setting( 'store', 'city' ), ', ' );

	return match ( $key ) {
		'address'    => $address,
		'directions' => 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address ),
		default      => bloomandsun_setting( 'store', $key ),
	};
}

/**
 * Toutes les informations, clé => valeur (pour l'aperçu dans l'éditeur).
 */
function bloomandsun_store_all(): array {
	$keys = array_merge( array_keys( bloomandsun_settings_registry()['store']['fields'] ), [ 'address', 'directions' ] );

	return array_combine( $keys, array_map( 'bloomandsun_store', $keys ) );
}
