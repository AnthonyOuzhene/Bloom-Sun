<?php
/**
 * Title: Accueil — Nouveautés
 * Slug: bloomandsun/home-new-arrivals
 * Categories: bloomandsun
 *
 * Les 4 derniers produits publiés (bloc « Collection de produits » de WooCommerce).
 * Le cœur et les pastilles de couleur viendront avec YITH Wishlist et Variation Swatches.
 */
?>
<!-- wp:group {"tagName":"section","className":"bs-section bs-section--blush bs-new-arrivals","layout":{"type":"default"}} -->
<section class="wp-block-group bs-section bs-section--blush bs-new-arrivals">
	<!-- wp:group {"className":"bs-container","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container">
		<!-- wp:group {"className":"bs-section__head","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-section__head">
			<!-- wp:heading {"className":"bs-section__title"} -->
			<h2 class="wp-block-heading bs-section__title">Nouveautés</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"bs-section__more"} -->
			<p class="bs-section__more"><a href="<?php echo bloomandsun_shop_url( [ 'orderby' => 'date' ] ); ?>">Tout voir</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:woocommerce/product-collection {"queryId":10,"query":{"perPage":4,"pages":1,"offset":0,"postType":"product","order":"desc","orderBy":"date","search":"","exclude":[],"inherit":false,"taxQuery":{},"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[],"filterable":false},"tagName":"div","displayLayout":{"type":"flex","columns":4,"shrinkColumns":true},"dimensions":{"widthType":"fill"},"queryContextIncludes":["collection"],"className":"bs-products"} -->
		<div class="wp-block-woocommerce-product-collection bs-products">
			<!-- wp:woocommerce/product-template -->
				<!-- wp:woocommerce/product-image {"showSaleBadge":false,"imageSizing":"single","aspectRatio":"3/4","scale":"cover"} -->
					<!-- wp:woocommerce/product-sale-badge {"align":"left"} /-->
				<!-- /wp:woocommerce/product-image -->
				<!-- wp:post-title {"level":3,"isLink":true,"className":"bs-product__title","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->
				<!-- wp:woocommerce/product-price {"className":"bs-product__price"} /-->
			<!-- /wp:woocommerce/product-template -->

			<!-- wp:woocommerce/product-collection-no-results -->
				<!-- wp:paragraph {"className":"bs-empty"} -->
				<p class="bs-empty">Les nouvelles pièces arrivent très bientôt.</p>
				<!-- /wp:paragraph -->
			<!-- /wp:woocommerce/product-collection-no-results -->
		</div>
		<!-- /wp:woocommerce/product-collection -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
