<?php
/**
 * Éditeurs et extensions.
 */

/*
 * Classic Editor : articles en éditeur classique, pages en éditeur de blocs
 * (les pages s'appuient sur les blocs du thème).
 */

// Réglage par défaut de Classic Editor (« classique pour tous ») : il coupe l'éditeur
// de blocs partout (priorité 100). On le réactive juste après, pour les pages seulement.
add_filter( 'use_block_editor_for_post_type', function ( bool $use_block_editor, string $post_type ): bool {
	return 'page' === $post_type ? true : $use_block_editor;
}, 101, 2 );

// Si l'option « Autoriser les utilisateurs à changer d'éditeur » est activée :
// les pages ne proposent que l'éditeur de blocs.
add_filter( 'classic_editor_enabled_editors_for_post_type', function ( array $editors, string $post_type ): array {
	return 'page' === $post_type ? [ 'classic_editor' => false, 'block_editor' => true ] : $editors;
}, 10, 2 );

/*
 * Contact Form 7 : pas de <p>/<br> ajoutés automatiquement dans les formulaires
 * (la mise en page vient du balisage du formulaire et de pages/contact.css).
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );
