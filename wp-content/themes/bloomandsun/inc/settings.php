<?php
/**
 * Pages de réglages du back-office (moteur commun).
 *
 * Un module déclare sa page avec bloomandsun_register_settings_page() :
 *   [
 *     'id'       => 'store',                  identifiant interne
 *     'option'   => 'bloomandsun_store',      option WordPress où sont enregistrées les valeurs
 *     'title'    => 'Infos du magasin',       titre de la page et du menu
 *     'icon'     => 'dashicons-store',        icône du menu
 *     'position' => 58,                       position dans le menu
 *     'intro'    => '…',                      texte d'introduction
 *     'fields'   => [ clé => champ ],
 *   ]
 * Champ : [ 'section', 'label', 'type' (text|textarea|url|checkbox|select), 'default', 'help', 'options' ]
 * « options » (listes) : tableau valeur => libellé, ou nom de fonction qui le renvoie.
 *
 * Affichage : views/admin/settings.php. Droit requis : BLOOMANDSUN_SETTINGS_CAP (inc/permissions.php).
 */

/**
 * Registre des pages déclarées.
 */
function bloomandsun_settings_registry( ?array $page = null ): array {
	static $pages = [];

	if ( $page ) {
		$pages[ $page['id'] ] = $page;
	}

	return $pages;
}

function bloomandsun_register_settings_page( array $page ): void {
	bloomandsun_settings_registry( $page );
}

/**
 * Valeur d'un réglage : valeur enregistrée, sinon valeur par défaut.
 */
function bloomandsun_setting( string $page_id, string $key ): string {
	$page  = bloomandsun_settings_registry()[ $page_id ] ?? null;
	$field = $page['fields'][ $key ] ?? null;

	if ( ! $field ) {
		return '';
	}

	$saved = (array) get_option( $page['option'], [] );

	return (string) ( $saved[ $key ] ?? $field['default'] ?? '' );
}

/**
 * Choix possibles d'un champ « liste ».
 */
function bloomandsun_setting_options( array $field ): array {
	$options = $field['options'] ?? [];

	return is_callable( $options ) ? (array) call_user_func( $options ) : (array) $options;
}

/**
 * Nettoie les valeurs envoyées par le formulaire d'une page.
 */
function bloomandsun_settings_sanitize( array $page, $input ): array {
	$input = (array) $input;
	$clean = [];

	foreach ( $page['fields'] as $key => $field ) {
		$value = (string) ( $input[ $key ] ?? '' );

		$clean[ $key ] = match ( $field['type'] ) {
			'url'      => esc_url_raw( $value ),
			'textarea' => sanitize_textarea_field( $value ),
			'checkbox' => '1' === $value ? '1' : '0',
			'select'   => array_key_exists( $value, bloomandsun_setting_options( $field ) ) ? $value : (string) ( $field['default'] ?? '' ),
			default    => sanitize_text_field( $value ),
		};
	}

	return $clean;
}

/**
 * Données préparées pour la vue : rubrique => liste de champs.
 */
function bloomandsun_settings_view_data( array $page ): array {
	$sections = [];

	foreach ( $page['fields'] as $key => $field ) {
		$sections[ $field['section'] ][] = [
			'id'      => "{$page['option']}-$key",
			'name'    => "{$page['option']}[$key]",
			'label'   => $field['label'],
			'type'    => $field['type'],
			'value'   => bloomandsun_setting( $page['id'], $key ),
			'help'    => $field['help'] ?? '',
			'options' => 'select' === $field['type'] ? bloomandsun_setting_options( $field ) : [],
		];
	}

	return [
		'title'    => $page['title'],
		'intro'    => $page['intro'] ?? '',
		'group'    => $page['option'],
		'sections' => $sections,
	];
}

add_action( 'admin_init', function () {
	foreach ( bloomandsun_settings_registry() as $page ) {
		register_setting( $page['option'], $page['option'], [
			'type'              => 'array',
			'sanitize_callback' => static fn( $input ) => bloomandsun_settings_sanitize( $page, $input ),
		] );

		// Enregistrement via options.php : autorisé avec le droit du thème (pas besoin d'être super administrateur).
		add_filter( "option_page_capability_{$page['option']}", static fn() => BLOOMANDSUN_SETTINGS_CAP );
	}
} );

add_action( 'admin_menu', function () {
	foreach ( bloomandsun_settings_registry() as $page ) {
		add_menu_page(
			$page['title'],
			$page['title'],
			BLOOMANDSUN_SETTINGS_CAP,
			"bloomandsun-{$page['id']}",
			static fn() => bloomandsun_view( 'admin/settings', bloomandsun_settings_view_data( $page ) ),
			$page['icon'] ?? 'dashicons-admin-generic',
			$page['position'] ?? 58
		);
	}
} );
