<?php
/**
 * Instagram : les dernières publications du compte du magasin dans la section « Sur Instagram »
 * de l'accueil (parts/home/instagram.html).
 *
 * - Page de réglages « Instagram » (super administrateur) : jeton d'accès et identifiant du compte.
 *   Deux sortes de jetons (app Meta for Developers, « API Instagram ») :
 *   · « EAA… » (connexion unifiée, jeton utilisateur système) : API graph.facebook.com,
 *     compte interrogé par son identifiant, n'expire jamais ;
 *   · « IG… » (connexion Instagram) : API graph.instagram.com, compte « /me »,
 *     renouvelé chaque semaine (il expire sinon au bout de 60 jours).
 * - Toutes les heures (WP-Cron) : lecture des publications, copie des photos dans
 *   uploads/bloomandsun-instagram/ (rien n'est chargé depuis les serveurs de Meta par les visiteurs).
 * - Affichage : carrousel à défilement automatique, source de liaison « bloomandsun/instagram »
 *   (inc/bindings.php), clés image / alt / caption, argument « index » (1 à 12). Sans publication,
 *   la vignette de couleur du gabarit reste affichée.
 */

const BLOOMANDSUN_INSTAGRAM_COUNT  = 12;
const BLOOMANDSUN_INSTAGRAM_OPTION = 'bloomandsun_instagram';       // réglages (jeton)
const BLOOMANDSUN_INSTAGRAM_FEED   = 'bloomandsun_instagram_feed';  // publications affichées
const BLOOMANDSUN_INSTAGRAM_STATE  = 'bloomandsun_instagram_state'; // compte, dernière synchro, erreur, renouvellement
const BLOOMANDSUN_INSTAGRAM_DIR    = 'bloomandsun-instagram';       // dossier des photos dans uploads/
const BLOOMANDSUN_INSTAGRAM_CRON   = 'bloomandsun_instagram_sync';
const BLOOMANDSUN_INSTAGRAM_API    = 'https://graph.instagram.com'; // jetons « IG… »
const BLOOMANDSUN_INSTAGRAM_GRAPH  = 'https://graph.facebook.com';  // jetons « EAA… »

bloomandsun_register_settings_page( [
	'id'         => 'instagram',
	'option'     => BLOOMANDSUN_INSTAGRAM_OPTION,
	'title'      => 'Instagram',
	'icon'       => 'dashicons-instagram',
	'position'   => 59,
	'capability' => 'manage_options',
	'intro'      => 'Les 12 dernières publications du compte s\'affichent dans la section « Sur Instagram » de l\'accueil. Elles sont mises à jour toutes les heures.',
	'fields'     => [
		'token'      => [ 'section' => 'Connexion', 'label' => 'Jeton d\'accès', 'type' => 'password', 'default' => '', 'help' => static fn(): string => bloomandsun_instagram_status() ],
		'account_id' => [ 'section' => 'Connexion', 'label' => 'Identifiant du compte', 'type' => 'text', 'default' => '17841443991928573', 'help' => 'Pour un jeton « EAA… » : numéro affiché sous le nom du compte dans l\'app Meta (API Instagram → Générer des tokens d\'accès).' ],
	],
] );

/*
 * Lecture
 * ---------------------------------------------------------------------------
 */

/**
 * Une publication à afficher (1 à 12) : image (URL sur le site), alt, caption (début de la légende,
 * affiché au survol), link. Vide s'il n'y en a pas.
 */
function bloomandsun_instagram_post( int $index ): array {
	$post = ( (array) get_option( BLOOMANDSUN_INSTAGRAM_FEED, [] ) )[ $index - 1 ] ?? null;

	if ( ! is_array( $post ) || empty( $post['file'] ) ) {
		return [];
	}

	return [
		'image'   => trailingslashit( wp_upload_dir()['baseurl'] ) . BLOOMANDSUN_INSTAGRAM_DIR . '/' . $post['file'],
		'alt'     => (string) ( $post['alt'] ?? '' ),
		'caption' => (string) ( $post['caption'] ?? '' ),
		'link'    => (string) ( $post['link'] ?? '' ),
	];
}

/**
 * Valeur pour la source de liaison : args « key » (image|alt|caption) et « index ».
 */
function bloomandsun_instagram( array $args ): string {
	return (string) ( bloomandsun_instagram_post( (int) ( $args['index'] ?? 0 ) )[ $args['key'] ?? '' ] ?? '' );
}

/**
 * Les publications, pour l'aperçu dans l'éditeur.
 */
function bloomandsun_instagram_editor_data(): array {
	return array_map( 'bloomandsun_instagram_post', range( 1, BLOOMANDSUN_INSTAGRAM_COUNT ) );
}

/**
 * État affiché sous le champ du jeton.
 */
function bloomandsun_instagram_status(): string {
	$state = (array) get_option( BLOOMANDSUN_INSTAGRAM_STATE, [] );
	$date  = static fn( int $time ): string => wp_date( 'j F Y à H\hi', $time );

	if ( '' === bloomandsun_setting( 'instagram', 'token' ) ) {
		return 'Pas encore connecté : collez ici le jeton généré dans l\'app Meta (API Instagram).';
	}

	if ( ! empty( $state['error'] ) ) {
		return sprintf( 'Erreur lors de la dernière mise à jour (%s) : %s', $date( (int) $state['checked'] ), $state['error'] );
	}

	if ( empty( $state['synced'] ) ) {
		return 'Jeton enregistré, première mise à jour en cours.';
	}

	$token_info = bloomandsun_instagram_is_meta_token( bloomandsun_setting( 'instagram', 'token' ) )
		? 'Jeton permanent.'
		: 'Jeton renouvelé le ' . $date( (int) ( $state['refreshed'] ?? $state['synced'] ) ) . '.';

	return sprintf(
		'Connecté à @%s. Dernière mise à jour : %s (%d publications). %s',
		$state['username'] ?? '?',
		$date( (int) $state['synced'] ),
		(int) ( $state['count'] ?? 0 ),
		$token_info
	);
}

/**
 * Jeton Meta « EAA… » (connexion unifiée) plutôt qu'Instagram « IG… » ?
 */
function bloomandsun_instagram_is_meta_token( string $token ): bool {
	return '' !== $token && ! str_starts_with( $token, 'IG' );
}

/*
 * Synchronisation
 * ---------------------------------------------------------------------------
 */

/**
 * Appel à l'API (adresse complète). Renvoie la réponse décodée ou une erreur lisible.
 */
function bloomandsun_instagram_request( string $url, array $args ): array|WP_Error {
	$response = wp_remote_get( add_query_arg( urlencode_deep( $args ), $url ), [ 'timeout' => 15 ] );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code || ! is_array( $body ) ) {
		return new WP_Error( 'bloomandsun_instagram', $body['error']['message'] ?? "réponse inattendue d'Instagram (code $code)." );
	}

	return $body;
}

/**
 * Dossier des photos copiées (créé si besoin).
 */
function bloomandsun_instagram_dir(): string {
	$dir = trailingslashit( wp_upload_dir()['basedir'] ) . BLOOMANDSUN_INSTAGRAM_DIR;
	wp_mkdir_p( $dir );

	return $dir;
}

/**
 * Copie une photo d'Instagram sur le site, au format portrait d'Instagram (4:5, 640 × 800 px).
 * Renvoie le nom du fichier, vide en cas d'échec.
 */
function bloomandsun_instagram_store_image( string $id, string $remote_url ): string {
	$id = preg_replace( '/\D/', '', $id );

	if ( '' === $id || '' === $remote_url ) {
		return '';
	}

	$file = "$id-4x5.jpg";
	$path = bloomandsun_instagram_dir() . "/$file";

	if ( file_exists( $path ) ) {
		return $file;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$tmp = download_url( $remote_url, 20 );

	if ( is_wp_error( $tmp ) ) {
		return '';
	}

	$editor = wp_get_image_editor( $tmp );

	if ( ! is_wp_error( $editor ) ) {
		$editor->resize( 640, 800, true );
		$editor->save( $path, 'image/jpeg' );
	}

	wp_delete_file( $tmp );

	return file_exists( $path ) ? $file : '';
}

/**
 * Supprime les photos qui ne sont plus affichées.
 */
function bloomandsun_instagram_cleanup( array $keep ): void {
	foreach ( glob( bloomandsun_instagram_dir() . '/*.jpg' ) ?: [] as $path ) {
		if ( ! in_array( basename( $path ), $keep, true ) ) {
			wp_delete_file( $path );
		}
	}
}

/**
 * Renouvelle un jeton « IG… » une fois par semaine (Instagram l'accepte dès qu'il a 24 h).
 * Un jeton Meta « EAA… » (utilisateur système) n'expire pas : rien à faire.
 */
function bloomandsun_instagram_refresh_token( string $token, array &$state ): string {
	if ( bloomandsun_instagram_is_meta_token( $token ) || (int) ( $state['refreshed'] ?? 0 ) > time() - WEEK_IN_SECONDS ) {
		return $token;
	}

	$response = bloomandsun_instagram_request( BLOOMANDSUN_INSTAGRAM_API . '/refresh_access_token', [ 'grant_type' => 'ig_refresh_token', 'access_token' => $token ] );

	if ( is_wp_error( $response ) || empty( $response['access_token'] ) ) {
		return $token; // nouvel essai à la prochaine synchronisation
	}

	$state['refreshed'] = time();

	$settings          = (array) get_option( BLOOMANDSUN_INSTAGRAM_OPTION, [] );
	$settings['token'] = (string) $response['access_token'];

	// Enregistrement interne : pas de nouvelle synchronisation (voir bloomandsun_instagram_token_saved).
	remove_action( 'update_option_' . BLOOMANDSUN_INSTAGRAM_OPTION, 'bloomandsun_instagram_token_saved' );
	update_option( BLOOMANDSUN_INSTAGRAM_OPTION, $settings );
	add_action( 'update_option_' . BLOOMANDSUN_INSTAGRAM_OPTION, 'bloomandsun_instagram_token_saved', 10, 2 );

	return $settings['token'];
}

/**
 * Met à jour les publications affichées.
 */
function bloomandsun_instagram_sync(): void {
	$token = bloomandsun_setting( 'instagram', 'token' );

	if ( '' === $token ) {
		return;
	}

	$state            = (array) get_option( BLOOMANDSUN_INSTAGRAM_STATE, [] );
	$state['checked'] = time();
	$token            = bloomandsun_instagram_refresh_token( $token, $state );

	// Jeton Meta : graph.facebook.com/<identifiant du compte> ; jeton Instagram : graph.instagram.com/me.
	$account_id = preg_replace( '/\D/', '', bloomandsun_setting( 'instagram', 'account_id' ) );
	$base       = bloomandsun_instagram_is_meta_token( $token )
		? BLOOMANDSUN_INSTAGRAM_GRAPH . '/' . $account_id
		: BLOOMANDSUN_INSTAGRAM_API . '/me';

	$account = bloomandsun_instagram_is_meta_token( $token ) && '' === $account_id
		? new WP_Error( 'bloomandsun_instagram', 'identifiant du compte manquant.' )
		: bloomandsun_instagram_request( $base, [ 'fields' => 'username', 'access_token' => $token ] );
	$media   = is_wp_error( $account ) ? $account : bloomandsun_instagram_request( "$base/media", [
		'fields'       => 'id,caption,media_type,media_url,thumbnail_url,permalink',
		'limit'        => BLOOMANDSUN_INSTAGRAM_COUNT,
		'access_token' => $token,
	] );

	if ( is_wp_error( $media ) ) {
		$state['error'] = $media->get_error_message();
		update_option( BLOOMANDSUN_INSTAGRAM_STATE, $state, false );
		return; // les publications précédentes restent affichées
	}

	$posts = [];

	foreach ( (array) ( $media['data'] ?? [] ) as $item ) {
		$remote = 'VIDEO' === ( $item['media_type'] ?? '' ) ? ( $item['thumbnail_url'] ?? '' ) : ( $item['media_url'] ?? '' );
		$file   = bloomandsun_instagram_store_image( (string) ( $item['id'] ?? '' ), (string) $remote );

		if ( '' === $file ) {
			continue;
		}

		$caption = trim( wp_strip_all_tags( (string) ( $item['caption'] ?? '' ) ) );

		$posts[] = [
			'file'    => $file,
			'alt'     => '' !== $caption ? wp_trim_words( $caption, 10, '…' ) : 'Publication Instagram de Bloom & Sun',
			'caption' => wp_trim_words( $caption, 22, '…' ),
			'link'    => esc_url_raw( (string) ( $item['permalink'] ?? '' ) ),
		];
	}

	update_option( BLOOMANDSUN_INSTAGRAM_FEED, $posts );
	bloomandsun_instagram_cleanup( array_column( $posts, 'file' ) );

	$state['username'] = (string) ( $account['username'] ?? '' );
	$state['synced']   = time();
	$state['count']    = count( $posts );
	$state['error']    = '';
	update_option( BLOOMANDSUN_INSTAGRAM_STATE, $state, false );
}

add_action( BLOOMANDSUN_INSTAGRAM_CRON, 'bloomandsun_instagram_sync' );

add_action( 'init', function () {
	if ( ! wp_next_scheduled( BLOOMANDSUN_INSTAGRAM_CRON ) ) {
		wp_schedule_event( time(), 'hourly', BLOOMANDSUN_INSTAGRAM_CRON );
	}
} );

/**
 * Nouveau jeton (ou nouvel identifiant) enregistré dans la page de réglages :
 * le jeton est considéré comme neuf et on synchronise tout de suite.
 */
function bloomandsun_instagram_token_saved( $old, $new ): void {
	static $running = false;

	$old     = (array) $old;
	$new     = (array) $new;
	$changed = ( $old['token'] ?? '' ) !== ( $new['token'] ?? '' ) || ( $old['account_id'] ?? '' ) !== ( $new['account_id'] ?? '' );

	if ( $running || '' === (string) ( $new['token'] ?? '' ) || ! $changed ) {
		return;
	}

	$running = true;

	$state              = (array) get_option( BLOOMANDSUN_INSTAGRAM_STATE, [] );
	$state['refreshed'] = time();
	unset( $state['synced'], $state['error'] );
	update_option( BLOOMANDSUN_INSTAGRAM_STATE, $state, false );

	bloomandsun_instagram_sync();

	$running = false;
}

add_action( 'update_option_' . BLOOMANDSUN_INSTAGRAM_OPTION, 'bloomandsun_instagram_token_saved', 10, 2 );
add_action( 'add_option_' . BLOOMANDSUN_INSTAGRAM_OPTION, fn( string $option, $value ) => bloomandsun_instagram_token_saved( [], $value ), 10, 2 );

/*
 * Affichage
 * ---------------------------------------------------------------------------
 */

// Image d'une publication : lien vers la publication ; sans publication, l'image n'est pas affichée
// (le carré de couleur du gabarit reste visible).
add_filter( 'render_block_core/image', function ( string $html, array $block ): string {
	if ( ! in_array( 'bs-instagram__post', explode( ' ', $block['attrs']['className'] ?? '' ), true ) ) {
		return $html;
	}

	$post = bloomandsun_instagram_post( (int) ( $block['attrs']['metadata']['bindings']['url']['args']['index'] ?? 0 ) );

	if ( ! $post ) {
		return '';
	}

	bloomandsun_enqueue_carousel();

	$tags = new WP_HTML_Tag_Processor( $html );

	if ( $post['link'] && $tags->next_tag( 'a' ) ) {
		$tags->set_attribute( 'href', $post['link'] );
	}

	return $tags->get_updated_html();
}, 10, 2 );
