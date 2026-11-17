<?php
/**
 * Prise en charge du thème et affichage des vues du back-office.
 */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );
} );

/**
 * Affiche une vue du back-office (dossier views/).
 * La vue ne fait qu'afficher : les données lui sont préparées par la logique (inc/).
 *
 * @param string $name Nom de la vue, ex. « admin/store-settings ».
 * @param array  $data Données mises à disposition de la vue dans $data.
 */
function bloomandsun_view( string $name, array $data = [] ): void {
	$file = get_theme_file_path( "views/$name.php" );

	if ( is_readable( $file ) ) {
		( static function () use ( $file, $data ) {
			require $file;
		} )();
	}
}
