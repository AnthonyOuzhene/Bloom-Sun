<?php
/**
 * Page Contact : ajoute le bouton Facebook à côté d'Instagram.
 * ddev wp eval-file scripts/contact-facebook.php
 */
$page    = get_page_by_path( 'contact' );
$content = $page->post_content;

if ( str_contains( $content, '"key":"facebook"' ) ) {
	WP_CLI::success( 'Le bouton Facebook est déjà là.' );
	return;
}

$instagram = '<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://www.instagram.com/bloomandsun_boutique/" target="_blank" rel="noreferrer noopener">Instagram</a></div>
<!-- /wp:button -->';

$facebook = $instagram . '

<!-- wp:button {"className":"is-style-outline","linkTarget":"_blank","rel":"noreferrer noopener","metadata":{"bindings":{"url":{"source":"bloomandsun/store","args":{"key":"facebook"}}}}} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://www.facebook.com/profile.php?id=61594915953501" target="_blank" rel="noreferrer noopener">Facebook</a></div>
<!-- /wp:button -->';

if ( ! str_contains( $content, $instagram ) ) {
	WP_CLI::error( 'Bouton Instagram introuvable dans la page Contact.' );
}

$content = str_replace( $instagram, $facebook, $content );
// Classe abîmée à l'enregistrement (- perdu) : bs-chipu002du002dinsta → bs-chip--insta
$content = str_replace( 'bs-chipu002du002dinsta', 'bs-chip--insta', $content );

wp_update_post( [ 'ID' => $page->ID, 'post_content' => wp_slash( $content ) ] );
WP_CLI::success( 'Bouton Facebook ajouté à la page Contact.' );
