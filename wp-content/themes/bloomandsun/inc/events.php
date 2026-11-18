<?php
/**
 * Événements au magasin (collaborations : tatoueuse, créatrice, atelier…).
 *
 * - Type de contenu « Événements » (bs_event) + taxonomie « Type d'événement » (bs_event_type)
 * - Champs : artiste, Instagram, Facebook, dates de début et de fin, horaires, 3 photos maximum
 *   (formulaire : views/admin/event-details.php, galerie : assets/js/admin-event-gallery.js)
 * - Affichage automatique selon les dates (parts/layout/event.html, source de liaison « bloomandsun/event ») :
 *   « bientôt » → ligne dans le bandeau d'annonce, X jours avant (réglage « Page d'accueil ») ;
 *   « en cours » → pastille sur le côté droit ; au clic, panneau de détails.
 * Pas de page publique : les événements ne s'affichent que dans le panneau.
 */

const BLOOMANDSUN_EVENT_TYPE     = 'bs_event';
const BLOOMANDSUN_EVENT_TAXONOMY = 'bs_event_type';
const BLOOMANDSUN_EVENT_MAX_IMAGES = 3;

/**
 * Champs de l'événement : clé => [libellé, type, aide].
 */
function bloomandsun_event_fields(): array {
	return [
		'artist'    => [ 'Artiste', 'text', 'Nom de l\'artiste ou de la créatrice invitée.' ],
		'instagram' => [ 'Instagram de l\'artiste', 'url', 'Lien complet, ex. https://www.instagram.com/…' ],
		'facebook'  => [ 'Facebook de l\'artiste', 'url', 'Facultatif.' ],
		'start'     => [ 'Date de début', 'date', '' ],
		'end'       => [ 'Date de fin', 'date', 'Identique à la date de début pour un événement d\'une journée.' ],
		'hours'     => [ 'Horaires', 'text', 'Ex. « 11h – 18h ». Vide : horaires habituels du magasin.' ],
	];
}

/*
 * Type de contenu et taxonomie
 * ---------------------------------------------------------------------------
 */
add_action( 'init', function () {
	register_post_type( BLOOMANDSUN_EVENT_TYPE, [
		'labels'       => [
			'name'               => 'Événements',
			'singular_name'      => 'Événement',
			'menu_name'          => 'Événements',
			'add_new'            => 'Ajouter',
			'add_new_item'       => 'Ajouter un événement',
			'edit_item'          => 'Modifier l\'événement',
			'new_item'           => 'Nouvel événement',
			'search_items'       => 'Rechercher un événement',
			'not_found'          => 'Aucun événement',
			'not_found_in_trash' => 'Aucun événement dans la corbeille',
			'all_items'          => 'Tous les événements',
		],
		'public'       => false, // pas de page publique
		'show_ui'      => true,
		'show_in_menu' => true,
		'show_in_rest' => false, // éditeur classique (description)
		'menu_position' => 26,
		'menu_icon'    => 'dashicons-calendar-alt',
		'supports'     => [ 'title', 'editor' ],
	] );

	register_taxonomy( BLOOMANDSUN_EVENT_TAXONOMY, BLOOMANDSUN_EVENT_TYPE, [
		'labels'            => [
			'name'          => 'Types d\'événement',
			'singular_name' => 'Type d\'événement',
			'menu_name'     => 'Types',
			'add_new_item'  => 'Ajouter un type',
			'edit_item'     => 'Modifier le type',
			'search_items'  => 'Rechercher un type',
			'not_found'     => 'Aucun type',
		],
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true, // cases à cocher dans le formulaire
		'show_in_rest'      => false,
	] );

	foreach ( bloomandsun_event_fields() as $key => [ , $type ] ) {
		register_post_meta( BLOOMANDSUN_EVENT_TYPE, "_bs_event_$key", [
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => match ( $type ) {
				'url'   => 'esc_url_raw',
				'date'  => 'bloomandsun_event_sanitize_date',
				default => 'sanitize_text_field',
			},
		] );
	}

	register_post_meta( BLOOMANDSUN_EVENT_TYPE, '_bs_event_images', [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'bloomandsun_event_sanitize_images',
	] );
} );

// Types de départ (une seule fois). La cliente peut en ajouter dans Événements > Types.
add_action( 'admin_init', function () {
	if ( get_option( 'bloomandsun_event_types_seeded' ) ) {
		return;
	}

	foreach ( [ 'Atelier', 'Lancement', 'Pop-up créateur', 'Tatouage', 'Vente privée' ] as $name ) {
		if ( ! term_exists( $name, BLOOMANDSUN_EVENT_TAXONOMY ) ) {
			wp_insert_term( $name, BLOOMANDSUN_EVENT_TAXONOMY );
		}
	}

	update_option( 'bloomandsun_event_types_seeded', 1 );
} );

/*
 * Nettoyage des valeurs
 * ---------------------------------------------------------------------------
 */
function bloomandsun_event_sanitize_date( $value ): string {
	$date = DateTime::createFromFormat( '!Y-m-d', (string) $value );

	return $date ? $date->format( 'Y-m-d' ) : '';
}

function bloomandsun_event_sanitize_images( $value ): string {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
	$ids = array_filter( $ids, static fn( int $id ) => wp_attachment_is_image( $id ) );

	return implode( ',', array_slice( array_values( array_unique( $ids ) ), 0, BLOOMANDSUN_EVENT_MAX_IMAGES ) );
}

/**
 * Valeur brute d'un champ.
 */
function bloomandsun_event_meta( int $post_id, string $key ): string {
	return (string) get_post_meta( $post_id, "_bs_event_$key", true );
}

/*
 * Formulaire « Détails de l'événement »
 * ---------------------------------------------------------------------------
 */
add_action( 'add_meta_boxes_' . BLOOMANDSUN_EVENT_TYPE, function ( WP_Post $post ) {
	add_meta_box( 'bloomandsun-event-details', 'Détails de l\'événement', static function () use ( $post ) {
		bloomandsun_view( 'admin/event-details', bloomandsun_event_form_data( $post ) );
	}, BLOOMANDSUN_EVENT_TYPE, 'normal', 'high' );
} );

/**
 * Données préparées pour la vue du formulaire.
 */
function bloomandsun_event_form_data( WP_Post $post ): array {
	$fields = [];
	foreach ( bloomandsun_event_fields() as $key => [ $label, $type, $help ] ) {
		$fields[] = [
			'id'    => "bs-event-$key",
			'name'  => "bs_event[$key]",
			'label' => $label,
			'type'  => $type,
			'value' => bloomandsun_event_meta( $post->ID, $key ),
			'help'  => $help,
		];
	}

	$images = [];
	foreach ( array_filter( explode( ',', bloomandsun_event_meta( $post->ID, 'images' ) ) ) as $id ) {
		$images[] = [ 'id' => (int) $id, 'url' => (string) wp_get_attachment_image_url( (int) $id, 'thumbnail' ) ];
	}

	return [
		'fields' => $fields,
		'images' => $images,
		'max'    => BLOOMANDSUN_EVENT_MAX_IMAGES,
		'nonce'  => 'bloomandsun_event_save',
	];
}

// Enregistrement du formulaire.
add_action( 'save_post_' . BLOOMANDSUN_EVENT_TYPE, function ( int $post_id ) {
	$nonce = $_POST['bloomandsun_event_nonce'] ?? ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	if ( ! wp_verify_nonce( $nonce, 'bloomandsun_event_save' ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	$input = wp_unslash( (array) ( $_POST['bs_event'] ?? [] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	foreach ( array_keys( bloomandsun_event_fields() ) as $key ) {
		update_post_meta( $post_id, "_bs_event_$key", (string) ( $input[ $key ] ?? '' ) );
	}

	// Date de fin vide ou antérieure au début : événement d'une journée.
	$start = bloomandsun_event_meta( $post_id, 'start' );
	$end   = bloomandsun_event_meta( $post_id, 'end' );
	if ( $start && ( ! $end || $end < $start ) ) {
		update_post_meta( $post_id, '_bs_event_end', $start );
	}

	update_post_meta( $post_id, '_bs_event_images', (string) ( $input['images'] ?? '' ) );
} );

// Galerie : médiathèque WordPress + script de la galerie, seulement sur l'écran d'un événement.
add_action( 'admin_enqueue_scripts', function () {
	$screen = get_current_screen();

	if ( ! $screen || BLOOMANDSUN_EVENT_TYPE !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'bloomandsun-event-gallery', get_theme_file_uri( 'assets/js/admin-event-gallery.js' ), [ 'jquery', 'jquery-ui-sortable' ], bloomandsun_file_version( 'assets/js/admin-event-gallery.js' ), true );
	wp_enqueue_style( 'bloomandsun-event-admin', get_theme_file_uri( 'assets/css/admin/event.css' ), [], bloomandsun_file_version( 'assets/css/admin/event.css' ) );
} );

// Liste des événements : colonne « Dates », triée par date de début.
add_filter( 'manage_' . BLOOMANDSUN_EVENT_TYPE . '_posts_columns', function ( array $columns ): array {
	return array_slice( $columns, 0, 2, true ) + [ 'bs_dates' => 'Dates', 'bs_artist' => 'Artiste' ] + array_slice( $columns, 2, null, true );
} );

add_action( 'manage_' . BLOOMANDSUN_EVENT_TYPE . '_posts_custom_column', function ( string $column, int $post_id ) {
	if ( 'bs_dates' === $column ) {
		echo esc_html( bloomandsun_event_date_label( bloomandsun_event_meta( $post_id, 'start' ), bloomandsun_event_meta( $post_id, 'end' ) ) );
	} elseif ( 'bs_artist' === $column ) {
		echo esc_html( bloomandsun_event_meta( $post_id, 'artist' ) );
	}
}, 10, 2 );

add_action( 'pre_get_posts', function ( WP_Query $query ) {
	if ( is_admin() && $query->is_main_query() && BLOOMANDSUN_EVENT_TYPE === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', '_bs_event_start' );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'DESC' );
	}
} );

/*
 * Dates en français
 * ---------------------------------------------------------------------------
 */
function bloomandsun_fr_date( string $ymd, bool $with_day = true ): string {
	$date = DateTime::createFromFormat( '!Y-m-d', $ymd );
	if ( ! $date ) {
		return '';
	}

	$days   = [ 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi' ];
	$months = [ 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre' ];

	$label = $date->format( 'j' ) . ' ' . $months[ (int) $date->format( 'n' ) - 1 ];

	return $with_day ? $days[ (int) $date->format( 'w' ) ] . ' ' . $label : $label;
}

/**
 * « Samedi 22 novembre », « Du 22 au 24 novembre » ou « Du 30 novembre au 2 décembre ».
 */
function bloomandsun_event_date_label( string $start, string $end ): string {
	if ( ! $start ) {
		return '';
	}
	if ( ! $end || $end === $start ) {
		return bloomandsun_fr_date( $start );
	}

	$same_month = substr( $start, 0, 7 ) === substr( $end, 0, 7 );
	$from       = $same_month ? (string) (int) substr( $start, 8, 2 ) : bloomandsun_fr_date( $start, false );

	return 'Du ' . $from . ' au ' . bloomandsun_fr_date( $end, false );
}

/*
 * Événement à afficher (le plus proche, annoncé ou en cours)
 * ---------------------------------------------------------------------------
 */
function bloomandsun_current_event(): ?array {
	static $event = false;

	if ( false !== $event ) {
		return $event;
	}

	$today  = wp_date( 'Y-m-d' );
	$notice = max( 0, (int) bloomandsun_home( 'events_notice' ) );
	$limit  = wp_date( 'Y-m-d', strtotime( "+$notice days", current_time( 'timestamp' ) ) );

	$posts = get_posts( [
		'post_type'      => BLOOMANDSUN_EVENT_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => '_bs_event_start',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => [
			[ 'key' => '_bs_event_start', 'value' => $limit, 'compare' => '<=', 'type' => 'DATE' ],
			[ 'key' => '_bs_event_end', 'value' => $today, 'compare' => '>=', 'type' => 'DATE' ],
		],
	] );

	$event = $posts ? bloomandsun_event_data( $posts[0], $today ) : null;

	return $event;
}

/**
 * Toutes les valeurs affichables d'un événement.
 */
function bloomandsun_event_data( WP_Post $post, string $today ): array {
	$id     = $post->ID;
	$start  = bloomandsun_event_meta( $id, 'start' );
	$end    = bloomandsun_event_meta( $id, 'end' ) ?: $start;
	$live   = $start <= $today && $today <= $end;
	$single = $start === $end;
	$artist = bloomandsun_event_meta( $id, 'artist' );
	$types  = wp_get_post_terms( $id, BLOOMANDSUN_EVENT_TAXONOMY, [ 'fields' => 'names' ] );
	$date   = bloomandsun_event_date_label( $start, $end );
	$title  = get_the_title( $post );

	$data = [
		'state'        => $live ? 'live' : 'soon',
		'title'        => $title,
		'type'         => is_wp_error( $types ) ? '' : implode( ' · ', $types ),
		'artist'       => $artist,
		'instagram'    => bloomandsun_event_meta( $id, 'instagram' ),
		'facebook'     => bloomandsun_event_meta( $id, 'facebook' ),
		'date_label'   => $date,
		'hours'        => bloomandsun_event_meta( $id, 'hours' ) ?: bloomandsun_store( 'hours_1_time' ),
		'address'      => bloomandsun_store( 'address' ),
		'description'  => esc_html( trim( wp_strip_all_tags( $post->post_content ) ) ), // retours à la ligne gérés en CSS (white-space)
		'status_label' => $live ? ( $single ? 'Aujourd\'hui au magasin' : 'En ce moment au magasin' ) : 'Bientôt au magasin',
		'pill_kicker'  => $live && $single ? 'Aujourd\'hui' : 'En ce moment',
		'announce'     => $date . ' · ' . $title . ( $artist ? ' avec ' . $artist : '' ) . ' au magasin',
	];

	$ids = array_values( array_filter( explode( ',', bloomandsun_event_meta( $id, 'images' ) ) ) );
	foreach ( range( 1, BLOOMANDSUN_EVENT_MAX_IMAGES ) as $n ) {
		$image_id              = (int) ( $ids[ $n - 1 ] ?? 0 );
		$data[ "image_$n" ]     = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '';
		$data[ "image_{$n}_alt" ] = $image_id ? ( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?: $title ) : '';
	}

	return $data;
}

/**
 * Valeur pour la source de liaison « bloomandsun/event ».
 */
function bloomandsun_event( string $key ): string {
	return (string) ( bloomandsun_current_event()[ $key ] ?? '' );
}

/**
 * Données pour l'aperçu dans l'éditeur : l'événement en cours, sinon un exemple.
 */
function bloomandsun_event_editor_data(): array {
	return bloomandsun_current_event() ?? [
		'title'        => 'Titre de l\'événement',
		'type'         => 'Type',
		'artist'       => 'Nom de l\'artiste',
		'date_label'   => 'Samedi 22 novembre',
		'hours'        => '11h – 18h',
		'address'      => bloomandsun_store( 'address' ),
		'description'  => 'Description de l\'événement.',
		'status_label' => 'Aujourd\'hui au magasin',
		'pill_kicker'  => 'Aujourd\'hui',
		'announce'     => 'Samedi 22 novembre · Titre de l\'événement au magasin',
	];
}

/*
 * Affichage selon l'état (gabarits : parts/layout/event.html, parts/layout/header.html)
 * - bs-event            : tout le module, masqué sans événement
 * - bs-event-soon-only  : seulement avant l'événement (ligne du bandeau d'annonce)
 * - bs-event-live-only  : seulement pendant l'événement (pastille)
 * - bs-hide-empty       : masqué si la valeur liée est vide (photo, réseau social…)
 * ---------------------------------------------------------------------------
 */
add_filter( 'pre_render_block', function ( ?string $pre, array $block ): ?string {
	$classes = explode( ' ', $block['attrs']['className'] ?? '' );
	$event   = null;

	foreach ( [ 'bs-event' => null, 'bs-event-soon-only' => 'soon', 'bs-event-live-only' => 'live' ] as $class => $state ) {
		if ( in_array( $class, $classes, true ) ) {
			$event = $event ?? bloomandsun_current_event();
			if ( ! $event || ( $state && $event['state'] !== $state ) ) {
				return '';
			}
		}
	}

	return $pre;
}, 10, 2 );

add_filter( 'render_block', function ( string $html, array $block ): string {
	$classes = explode( ' ', $block['attrs']['className'] ?? '' );

	if ( in_array( 'bs-event', $classes, true ) ) {
		wp_enqueue_script( 'bloomandsun-event', get_theme_file_uri( 'assets/js/event.js' ), [], bloomandsun_file_version( 'assets/js/event.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	}

	if ( ! in_array( 'bs-hide-empty', $classes, true ) ) {
		return $html;
	}

	$empty = match ( $block['blockName'] ) {
		'core/image'  => ! preg_match( '/<img[^>]+src="[^"]+"/', $html ),
		'core/button' => ! preg_match( '/href="[^"#][^"]*"/', $html ),
		default       => '' === trim( wp_strip_all_tags( $html ) ),
	};

	return $empty ? '' : $html;
}, 20, 2 );
