<?php
/**
 * Bloom & Sun — informations du magasin.
 *
 * - Réglages > Infos du magasin : adresse, horaires, Instagram, position du plan
 * - bloomandsun_store_info() : lit ces réglages (utilisé par l'accueil, le pied de page, la page Contact)
 * - Blocs « Infos du magasin » et « Plan du magasin » (dossier blocks/)
 */

const BLOOMANDSUN_STORE_OPTION = 'bloomandsun_store';

/**
 * Valeurs par défaut (utilisées tant que les réglages n'ont pas été enregistrés).
 */
function bloomandsun_store_defaults(): array {
	return [
		'street'    => '61 rue Paul Doumer',
		'city'      => '76600 Le Havre',
		'open_days' => 'Ouvert du mardi au samedi',
		'hours'     => "Mardi – samedi | 10h – 19h\nDimanche, lundi | Fermé",
		'tagline'   => 'Prêt-à-porter & accessoires féminins',
		'baseline'  => 'Une invitation au voyage & good vibes',
		'instagram' => 'https://www.instagram.com/bloomandsun_boutique/',
		'lat'       => '49.4923712',
		'lon'       => '0.1038993',
	];
}

/**
 * Toutes les informations du magasin, prêtes à afficher.
 */
function bloomandsun_store_info(): array {
	$s = wp_parse_args( (array) get_option( BLOOMANDSUN_STORE_OPTION, [] ), bloomandsun_store_defaults() );

	// Horaires : une ligne par plage, « Jours | Heures »
	$hours = [];
	foreach ( preg_split( '/\R/', (string) $s['hours'] ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( '' !== $parts[0] ) {
			$hours[] = [ $parts[0], $parts[1] ?? '' ];
		}
	}

	$address = trim( $s['street'] . ', ' . $s['city'], ', ' );
	$lat     = (float) $s['lat'];
	$lon     = (float) $s['lon'];

	return [
		'name'       => 'Bloom & Sun',
		'tagline'    => $s['tagline'],
		'baseline'   => $s['baseline'],
		'street'     => $s['street'],
		'city'       => $s['city'],
		'address'    => $address,
		'hours'      => $hours,
		'open_days'  => $s['open_days'],
		'instagram'  => $s['instagram'],
		// Itinéraire : simple lien (aucun cookie tant qu'on ne clique pas)
		'directions' => 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address ),
		// Plan OpenStreetMap intégré : pas de cookies publicitaires (RGPD)
		'map_embed'  => sprintf(
			'https://www.openstreetmap.org/export/embed.html?bbox=%1$F%%2C%2$F%%2C%3$F%%2C%4$F&layer=mapnik&marker=%5$F%%2C%6$F',
			$lon - 0.006, $lat - 0.0032, $lon + 0.006, $lat + 0.0032, $lat, $lon
		),
		'map_link'   => sprintf( 'https://www.openstreetmap.org/?mlat=%1$F&mlon=%2$F#map=17/%1$F/%2$F', $lat, $lon ),
	];
}

/*
 * Page de réglages : Réglages > Infos du magasin
 * ---------------------------------------------------------------------------
 */
function bloomandsun_store_fields(): array {
	return [
		'street'    => [ 'Adresse', 'text', '' ],
		'city'      => [ 'Code postal et ville', 'text', '' ],
		'open_days' => [ 'Jours d\'ouverture', 'text', 'Affiché dans la pastille verte, ex. « Ouvert du mardi au samedi ».' ],
		'hours'     => [ 'Horaires', 'textarea', 'Une ligne par plage, au format « Jours | Heures ». Ex. : Mardi – samedi | 10h – 19h' ],
		'tagline'   => [ 'Activité', 'text', 'Ex. « Prêt-à-porter & accessoires féminins ».' ],
		'baseline'  => [ 'Phrase d\'accroche', 'text', '' ],
		'instagram' => [ 'Lien Instagram', 'url', '' ],
		'lat'       => [ 'Latitude du plan', 'text', 'Position du repère sur le plan (clic droit sur Google Maps > copier les coordonnées).' ],
		'lon'       => [ 'Longitude du plan', 'text', '' ],
	];
}

add_action( 'admin_init', function () {
	register_setting( 'bloomandsun_store', BLOOMANDSUN_STORE_OPTION, [
		'type'              => 'array',
		'sanitize_callback' => static function ( $input ): array {
			$clean = [];
			foreach ( bloomandsun_store_fields() as $key => [ , $type ] ) {
				$value = (string) ( $input[ $key ] ?? '' );
				$clean[ $key ] = match ( $type ) {
					'url'      => esc_url_raw( $value ),
					'textarea' => sanitize_textarea_field( $value ),
					default    => sanitize_text_field( $value ),
				};
			}
			return $clean;
		},
	] );
} );

add_action( 'admin_menu', function () {
	add_options_page( 'Infos du magasin', 'Infos du magasin', 'manage_options', 'bloomandsun-store', 'bloomandsun_store_settings_page' );
} );

function bloomandsun_store_settings_page(): void {
	$values = wp_parse_args( (array) get_option( BLOOMANDSUN_STORE_OPTION, [] ), bloomandsun_store_defaults() );
	?>
	<div class="wrap">
		<h1>Infos du magasin</h1>
		<p>Ces informations s'affichent sur la page Contact, dans la section « Le magasin » de l'accueil et dans le pied de page.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'bloomandsun_store' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( bloomandsun_store_fields() as $key => [ $label, $type, $help ] ) :
					$id   = 'bloomandsun-store-' . $key;
					$name = BLOOMANDSUN_STORE_OPTION . '[' . $key . ']';
					?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td>
							<?php if ( 'textarea' === $type ) : ?>
								<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="4" class="large-text"><?php echo esc_textarea( $values[ $key ] ); ?></textarea>
							<?php else : ?>
								<input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $values[ $key ] ); ?>" class="regular-text">
							<?php endif; ?>
							<?php if ( $help ) : ?>
								<p class="description"><?php echo esc_html( $help ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/*
 * Blocs dynamiques « Infos du magasin » et « Plan du magasin »
 * ---------------------------------------------------------------------------
 */
add_action( 'init', function () {
	register_block_type( get_theme_file_path( 'blocks/store-card' ) );
	register_block_type( get_theme_file_path( 'blocks/store-map' ) );
} );
