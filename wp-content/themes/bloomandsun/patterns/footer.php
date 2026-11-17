<?php
/**
 * Title: Pied de page
 * Slug: bloomandsun/footer
 * Categories: bloomandsun
 * Inserter: false
 *
 * Les liens sont calculés à partir des vraies pages et catégories.
 */

$bloomandsun_list = static function ( array $items, string $class = 'bs-footer__links' ): string {
	$lis = array_map(
		static fn( $item ) => "<!-- wp:list-item -->\n<li><a href=\"{$item[1]}\">" . esc_html( $item[0] ) . "</a></li>\n<!-- /wp:list-item -->",
		$items
	);

	return '<!-- wp:list {"className":"' . $class . '"} -->' . "\n"
		. '<ul class="wp-block-list ' . $class . '">' . implode( "\n\n", $lis ) . "</ul>\n"
		. '<!-- /wp:list -->';
};

$bloomandsun_store = bloomandsun_store_info();

$bloomandsun_shop = [
	[ 'Nouveautés', bloomandsun_shop_url( [ 'orderby' => 'date' ] ) ],
	[ 'Robes', bloomandsun_category_url( 'robes' ) ],
	[ 'Hauts et mailles', bloomandsun_category_url( 'hauts-et-mailles' ) ],
	[ 'Vestes et manteaux', bloomandsun_category_url( 'vestes-et-manteaux' ) ],
	[ 'Accessoires', bloomandsun_category_url( 'accessoires' ) ],
];
$bloomandsun_help = [
	[ 'Livraison et retours', bloomandsun_page_url( 'livraison-et-retours' ) ],
	[ 'Guide des tailles', bloomandsun_page_url( 'guide-des-tailles' ) ],
	[ 'FAQ', bloomandsun_page_url( 'faq' ) ],
	[ 'Contact', bloomandsun_page_url( 'contact' ) ],
];
$bloomandsun_social = [
	[ 'Instagram', $bloomandsun_store['instagram'] ],
	[ 'Facebook', '#' ],
];
$bloomandsun_legal = [
	[ 'CGV', bloomandsun_page_url( 'cgv' ) ],
	[ 'Mentions légales', bloomandsun_page_url( 'mentions-legales' ) ],
	[ 'Confidentialité', bloomandsun_page_url( 'politique-de-confidentialite' ) ],
	[ 'Gestion des cookies', bloomandsun_page_url( 'gestion-des-cookies' ) ],
];
?>
<!-- wp:group {"className":"bs-footer","layout":{"type":"default"}} -->
<div class="wp-block-group bs-footer">
	<!-- wp:group {"className":"bs-container bs-footer__grid","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container bs-footer__grid">
		<!-- wp:group {"className":"bs-footer__brand","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-footer__brand">
			<!-- wp:pattern {"slug":"bloomandsun/logo"} /-->

			<!-- wp:paragraph -->
			<p><?php echo esc_html( $bloomandsun_store['tagline'] ); ?><br><?php echo esc_html( $bloomandsun_store['address'] ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-footer__col","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-footer__col">
			<!-- wp:heading {"level":2,"className":"bs-footer__title"} -->
			<h2 class="wp-block-heading bs-footer__title">Boutique</h2>
			<!-- /wp:heading -->
			<?php echo $bloomandsun_list( $bloomandsun_shop ); ?>
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-footer__col","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-footer__col">
			<!-- wp:heading {"level":2,"className":"bs-footer__title"} -->
			<h2 class="wp-block-heading bs-footer__title">Aide</h2>
			<!-- /wp:heading -->
			<?php echo $bloomandsun_list( $bloomandsun_help ); ?>
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-footer__col","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-footer__col">
			<!-- wp:heading {"level":2,"className":"bs-footer__title"} -->
			<h2 class="wp-block-heading bs-footer__title">Suivez-nous</h2>
			<!-- /wp:heading -->
			<?php echo $bloomandsun_list( $bloomandsun_social, 'bs-footer__social' ); ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"bs-container bs-footer__bottom","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container bs-footer__bottom">
		<?php echo $bloomandsun_list( $bloomandsun_legal, 'bs-footer__legal' ); ?>

		<!-- wp:paragraph -->
		<p>© Bloom &amp; Sun</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
