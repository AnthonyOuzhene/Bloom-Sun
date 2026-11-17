<?php
/**
 * Droits d'accès au back-office.
 *
 * - Super administrateur (le développeur) : accès complet — thème, extensions, réglages.
 *   Rôle « bloomandsun_super_admin », ajouté au compte en plus du rôle Administrateur.
 * - Administrateur (la cliente) : contenus, WooCommerce, utilisateurs, Infos du magasin, Page d'accueil.
 *   Pas d'accès à Apparence, Extensions, Réglages, mises à jour, import/export.
 * - Gestionnaire de boutique (rôle WooCommerce, pour une future vendeuse) : idem sans
 *   les utilisateurs ni les réglages WooCommerce avancés ; pas d'accès à Apparence.
 *
 * Les droits sont ajustés à l'exécution (filtre user_has_cap) : rien n'est retiré
 * dans la base. Si le thème est désactivé, chacun retrouve les droits standards de son rôle.
 */

const BLOOMANDSUN_SUPER_ADMIN_ROLE = 'bloomandsun_super_admin';

// Droit propre au thème : modifier les pages de réglages (Infos du magasin, Page d'accueil).
const BLOOMANDSUN_SETTINGS_CAP = 'manage_bloomandsun_settings';

// Thème et apparence : réservés au super administrateur.
const BLOOMANDSUN_THEME_CAPS = [ 'edit_theme_options', 'customize', 'switch_themes', 'edit_themes', 'install_themes', 'update_themes', 'delete_themes', 'edit_css' ];

// Maintenance du site : réservée au super administrateur.
const BLOOMANDSUN_SITE_CAPS = [ 'manage_options', 'activate_plugins', 'install_plugins', 'update_plugins', 'delete_plugins', 'edit_plugins', 'update_core', 'edit_files', 'import', 'export' ];

/**
 * L'utilisateur est-il super administrateur ?
 */
function bloomandsun_is_super_admin( ?WP_User $user = null ): bool {
	$user = $user ?? wp_get_current_user();

	return in_array( BLOOMANDSUN_SUPER_ADMIN_ROLE, (array) $user->roles, true );
}

// Création du rôle « Super administrateur » (une seule fois), avec les droits d'un administrateur.
add_action( 'init', function () {
	if ( ! get_role( BLOOMANDSUN_SUPER_ADMIN_ROLE ) ) {
		$admin = get_role( 'administrator' );
		add_role( BLOOMANDSUN_SUPER_ADMIN_ROLE, 'Super administrateur', $admin ? $admin->capabilities : [ 'read' => true ] );
	}
} );

add_filter( 'user_has_cap', function ( array $allcaps, array $caps, array $args, WP_User $user ): array {
	// Super administrateur : tout est permis (sauf ce que WordPress interdit à tous).
	if ( bloomandsun_is_super_admin( $user ) ) {
		foreach ( array_diff( $caps, [ 'do_not_allow' ] ) as $cap ) {
			$allcaps[ $cap ] = true;
		}
		$allcaps[ BLOOMANDSUN_SETTINGS_CAP ] = true;

		return $allcaps;
	}

	$roles      = (array) $user->roles;
	$is_admin   = in_array( 'administrator', $roles, true );
	$is_manager = in_array( 'shop_manager', $roles, true );

	if ( $is_admin || $is_manager ) {
		foreach ( BLOOMANDSUN_THEME_CAPS as $cap ) {
			$allcaps[ $cap ] = false;
		}
		$allcaps[ BLOOMANDSUN_SETTINGS_CAP ] = true;
	}

	if ( $is_admin ) {
		foreach ( BLOOMANDSUN_SITE_CAPS as $cap ) {
			$allcaps[ $cap ] = false;
		}
	}

	return $allcaps;
}, 10, 4 );

// Seul un super administrateur peut attribuer le rôle de super administrateur…
add_filter( 'editable_roles', function ( array $roles ): array {
	if ( ! bloomandsun_is_super_admin() ) {
		unset( $roles[ BLOOMANDSUN_SUPER_ADMIN_ROLE ] );
	}

	return $roles;
} );

// … et modifier ou supprimer le compte d'un super administrateur.
add_filter( 'map_meta_cap', function ( array $caps, string $cap, int $user_id, array $args ): array {
	if ( in_array( $cap, [ 'edit_user', 'delete_user', 'remove_user', 'promote_user' ], true ) && ! empty( $args[0] ) ) {
		$target = get_userdata( (int) $args[0] );

		if ( $target && bloomandsun_is_super_admin( $target ) && ! bloomandsun_is_super_admin( get_userdata( $user_id ) ?: null ) ) {
			return [ 'do_not_allow' ];
		}
	}

	return $caps;
}, 10, 4 );

// Contact Form 7 : formulaires réservés au super administrateur (ils font partie de la structure du site).
add_filter( 'wpcf7_map_meta_cap', function ( array $meta_caps ): array {
	return array_fill_keys( array_keys( $meta_caps ), 'manage_options' );
} );

// Éditeur de blocs : seul le super administrateur peut déverrouiller la mise en page d'une page.
add_filter( 'block_editor_settings_all', function ( array $settings ): array {
	if ( ! bloomandsun_is_super_admin() ) {
		$settings['canLockBlocks'] = false;
	}

	return $settings;
} );

/*
 * Plans OpenStreetMap dans le contenu des pages (page Contact).
 * Sans le droit « unfiltered_html » (gestionnaire de boutique), WordPress supprime les <iframe>
 * à l'enregistrement : on autorise la balise, puis on retire toute iframe qui ne vient pas d'OpenStreetMap.
 */
add_filter( 'wp_kses_allowed_html', function ( array $tags, $context ): array {
	if ( 'post' === $context ) {
		$tags['iframe'] = [ 'src' => true, 'title' => true, 'loading' => true ];
	}

	return $tags;
}, 10, 2 );

add_filter( 'pre_kses', function ( $content ) {
	return is_string( $content )
		? preg_replace( '#<iframe\b(?![^>]*\bsrc="https://www\.openstreetmap\.org/)[^>]*>.*?</iframe>#is', '', $content )
		: $content;
} );
