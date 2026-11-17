<?php
/**
 * Informations du magasin.
 *
 * Modifiables dans le back-office, menu « Infos du magasin »
 * (administrateur et gestionnaire de boutique, droit BLOOMANDSUN_STORE_CAP).
 * Affichées dans les gabarits via la source de liaison « bloomandsun/store » (inc/bindings.php).
 * Affichage de la page de réglages : views/admin/store-settings.php.
 */

const BLOOMANDSUN_STORE_OPTION = 'bloomandsun_store';

/**
 * Champs : clé => [rubrique, libellé, type, valeur par défaut, aide].
 */
function bloomandsun_store_fields(): array {
	return [
		'open_days'    => [ 'Présentation', 'Jours d\'ouverture', 'text', 'Ouvert du mardi au samedi', 'Affiché dans la pastille verte.' ],
		'tagline'      => [ 'Présentation', 'Activité', 'text', 'Prêt-à-porter & accessoires féminins', '' ],
		'baseline'     => [ 'Présentation', 'Phrase d\'accroche', 'text', 'Une invitation au voyage & good vibes', '' ],
		'street'       => [ 'Adresse', 'Rue', 'text', '61 rue Paul Doumer', '' ],
		'city'         => [ 'Adresse', 'Code postal et ville', 'text', '76600 Le Havre', '' ],
		'hours_1_days' => [ 'Horaires', 'Jours (ligne 1)', 'text', 'Mardi – samedi', '' ],
		'hours_1_time' => [ 'Horaires', 'Heures (ligne 1)', 'text', '10h – 19h', '' ],
		'hours_2_days' => [ 'Horaires', 'Jours (ligne 2)', 'text', 'Dimanche, lundi', '' ],
		'hours_2_time' => [ 'Horaires', 'Heures (ligne 2)', 'text', 'Fermé', '' ],
		'instagram'    => [ 'Réseaux sociaux', 'Lien Instagram', 'url', 'https://www.instagram.com/bloomandsun_boutique/', '' ],
	];
}

/**
 * Valeur d'une information du magasin (réglage enregistré, sinon valeur par défaut).
 * Clés calculées : « address » (adresse complète) et « directions » (lien d'itinéraire).
 */
function bloomandsun_store( string $key ): string {
	$fields = bloomandsun_store_fields();

	if ( isset( $fields[ $key ] ) ) {
		$saved = (array) get_option( BLOOMANDSUN_STORE_OPTION, [] );

		return (string) ( $saved[ $key ] ?? $fields[ $key ][3] );
	}

	$address = trim( bloomandsun_store( 'street' ) . ', ' . bloomandsun_store( 'city' ), ', ' );

	return match ( $key ) {
		'address'    => $address,
		'directions' => 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address ),
		default      => '',
	};
}

/**
 * Toutes les informations, clé => valeur (pour l'aperçu dans l'éditeur).
 */
function bloomandsun_store_all(): array {
	$keys = array_merge( array_keys( bloomandsun_store_fields() ), [ 'address', 'directions' ] );

	return array_combine( $keys, array_map( 'bloomandsun_store', $keys ) );
}

/**
 * Nettoie les valeurs envoyées par le formulaire.
 */
function bloomandsun_store_sanitize( $input ): array {
	$clean = [];

	foreach ( bloomandsun_store_fields() as $key => [ , , $type ] ) {
		$value         = (string) ( $input[ $key ] ?? '' );
		$clean[ $key ] = 'url' === $type ? esc_url_raw( $value ) : sanitize_text_field( $value );
	}

	return $clean;
}

/**
 * Données préparées pour la vue : rubrique => liste de champs.
 */
function bloomandsun_store_view_data(): array {
	$sections = [];

	foreach ( bloomandsun_store_fields() as $key => [ $section, $label, $type, , $help ] ) {
		$sections[ $section ][] = [
			'id'    => "bloomandsun-store-$key",
			'name'  => BLOOMANDSUN_STORE_OPTION . "[$key]",
			'label' => $label,
			'type'  => $type,
			'value' => bloomandsun_store( $key ),
			'help'  => $help,
		];
	}

	return [
		'title'    => 'Infos du magasin',
		'intro'    => 'Ces informations s\'affichent sur la page Contact, dans la section « Le magasin » de l\'accueil et dans le pied de page.',
		'group'    => 'bloomandsun_store',
		'sections' => $sections,
	];
}

add_action( 'admin_init', function () {
	register_setting( 'bloomandsun_store', BLOOMANDSUN_STORE_OPTION, [
		'type'              => 'array',
		'sanitize_callback' => 'bloomandsun_store_sanitize',
	] );
} );

// Enregistrement via options.php : autorisé avec le droit du thème (pas besoin d'être administrateur).
add_filter( 'option_page_capability_bloomandsun_store', fn() => BLOOMANDSUN_STORE_CAP );

add_action( 'admin_menu', function () {
	add_menu_page(
		'Infos du magasin',
		'Infos du magasin',
		BLOOMANDSUN_STORE_CAP,
		'bloomandsun-store',
		fn() => bloomandsun_view( 'admin/store-settings', bloomandsun_store_view_data() ),
		'dashicons-store',
		58
	);
} );
