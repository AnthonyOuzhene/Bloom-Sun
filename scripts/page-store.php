<?php
/**
 * Page « Le magasin » (adresse /le-magasin/) :
 * - lui attribue le modèle « page-store » (templates/page-store.html, déclaré dans theme.json) ;
 * - la remplit avec le contenu de départ de scripts/content/store.html.
 *
 * Ensuite, le contenu se modifie dans l'éditeur (Pages → Le magasin) : ce fichier ne sert qu'au
 * premier remplissage (en local, puis au déploiement).
 *
 * Dans store.html, les photos sont repérées par le titre du média dans la médiathèque :
 *   {{id:boutiique-5}}  → identifiant du média
 *   {{url:boutiique-5}} → adresse de sa grande taille
 *
 * ddev wp eval-file scripts/page-store.php           (contenu ajouté seulement si la page est vide)
 * ddev wp eval-file scripts/page-store.php remplacer (écrase le contenu actuel)
 */
$page = get_page_by_path( 'le-magasin' );

if ( ! $page ) {
	WP_CLI::error( 'Page « le-magasin » introuvable.' );
}

update_post_meta( $page->ID, '_wp_page_template', 'page-store' );

if ( '' !== trim( $page->post_content ) && 'remplacer' !== ( $args[0] ?? '' ) ) {
	WP_CLI::success( 'Modèle « page-store » attribué. Contenu actuel conservé (relancer avec « remplacer » pour l\'écraser).' );
	return;
}

$content = file_get_contents( __DIR__ . '/content/store.html' );
$missing = [];

$content = preg_replace_callback( '/\{\{(id|url):([a-z0-9-]+)\}\}/', function ( array $m ) use ( &$missing ): string {
	$found = get_posts( [ 'post_type' => 'attachment', 'title' => $m[2], 'posts_per_page' => 1, 'fields' => 'ids' ] );

	if ( ! $found ) {
		$missing[ $m[2] ] = true;
		return '0';
	}

	return 'id' === $m[1] ? (string) $found[0] : (string) wp_get_attachment_image_url( $found[0], 'large' );
}, $content );

if ( $missing ) {
	WP_CLI::error( 'Photos introuvables dans la médiathèque : ' . implode( ', ', array_keys( $missing ) ) );
}

wp_update_post( [ 'ID' => $page->ID, 'post_content' => wp_slash( $content ) ] );
WP_CLI::success( 'Page « Le magasin » remplie (modèle « page-store ») : ' . get_permalink( $page ) );
