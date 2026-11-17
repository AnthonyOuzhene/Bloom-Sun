<?php
/**
 * Title: En-tête (logo centré)
 * Slug: bloomandsun/header
 * Categories: bloomandsun
 * Inserter: false
 *
 * Menu réparti de part et d'autre du logo, icônes au trait fin,
 * filet doré au petit soleil. Sur mobile, un seul menu « hamburger ».
 * Les liens sont calculés à partir des vraies pages et catégories
 * (voir bloomandsun_menu_links() dans functions.php).
 */

$bloomandsun_links = bloomandsun_menu_links();
$bloomandsun_nav   = static function ( array $keys ) use ( $bloomandsun_links ): string {
	return implode( "\n", array_map( static fn( $k ) => bloomandsun_nav_link( ...$bloomandsun_links[ $k ] ), $keys ) );
};
?>
<!-- wp:group {"className":"bs-header","layout":{"type":"default"}} -->
<div class="wp-block-group bs-header">
	<!-- wp:group {"className":"bs-container bs-header__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container bs-header__inner">
		<!-- wp:navigation {"className":"bs-nav bs-nav--left","overlayMenu":"never","layout":{"type":"flex","justifyContent":"left"}} -->
		<?php echo $bloomandsun_nav( [ 'nouveautes', 'vetements', 'accessoires' ] ); ?>
		<!-- /wp:navigation -->

		<!-- wp:navigation {"className":"bs-nav-mobile","overlayMenu":"always","layout":{"type":"flex","orientation":"vertical"}} -->
		<?php echo $bloomandsun_nav( array_keys( $bloomandsun_links ) ); ?>
		<!-- /wp:navigation -->

		<!-- wp:pattern {"slug":"bloomandsun/logo"} /-->

		<!-- wp:group {"className":"bs-header__right","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-header__right">
			<!-- wp:navigation {"className":"bs-nav bs-nav--right","overlayMenu":"never","layout":{"type":"flex","justifyContent":"right"}} -->
			<?php echo $bloomandsun_nav( [ 'magasin', 'contact' ] ); ?>
			<!-- /wp:navigation -->

			<!-- wp:group {"className":"bs-header__icons","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-header__icons">
				<!-- wp:search {"label":"Rechercher","showLabel":false,"placeholder":"Rechercher un produit","buttonText":"Rechercher","buttonPosition":"button-only","buttonUseIcon":true,"isSearchFieldHidden":true,"query":{"post_type":"product"},"className":"bs-search"} /-->

				<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"line"} /-->

				<!-- wp:paragraph {"className":"bs-wishlist"} -->
				<p class="bs-wishlist"><a class="bs-icon-link" href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>" aria-label="Liste d'envies"></a></p>
				<!-- /wp:paragraph -->

				<!-- wp:woocommerce/mini-cart {"miniCartIcon":"bag"} /-->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:html -->
	<div class="bs-header__line" aria-hidden="true">
		<svg viewBox="0 0 88 44" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"><path d="M4 40h80"/><path d="M30 40a14 14 0 0 1 28 0"/><path d="M44 4v14M28 9l6 11M60 9l-6 11M16 20l10 8M72 20l-10 8M8 32h10M70 32h10" stroke-dasharray="6 3 2 3"/></svg>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
