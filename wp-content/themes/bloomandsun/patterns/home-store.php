<?php
/**
 * Title: Accueil — Le magasin
 * Slug: bloomandsun/home-store
 * Categories: bloomandsun
 *
 * Photo du magasin (pastille « Venez essayer ! ») + carte vert sapin.
 * Adresse et horaires : bloomandsun_store_info() dans functions.php.
 */

$bloomandsun_store = bloomandsun_store_info();
?>
<!-- wp:group {"tagName":"section","className":"bs-section bs-store","layout":{"type":"default"}} -->
<section class="wp-block-group bs-section bs-store">
	<!-- wp:group {"className":"bs-container bs-store__grid","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container bs-store__grid">
		<!-- wp:group {"className":"bs-photo bs-store__photo","backgroundColor":"rose","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-photo bs-store__photo has-rose-background-color has-background">
			<!-- wp:paragraph {"className":"bs-store__sticker"} -->
			<p class="bs-store__sticker">Venez<br>essayer&nbsp;!</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"bs-photo__label"} -->
			<p class="bs-photo__label">Photo : la vitrine ou l'intérieur du magasin</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-sapin-card bs-store__card","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-sapin-card bs-store__card">
			<!-- wp:paragraph {"className":"bs-chip bs-chip--open"} -->
			<p class="bs-chip bs-chip--open"><?php echo esc_html( $bloomandsun_store['open_days'] ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading -->
			<h2 class="wp-block-heading">Le magasin, <em>on vous y attend</em></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Venez essayer, on vous conseille.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"bs-address"} -->
			<p class="bs-address"><?php echo esc_html( $bloomandsun_store['street'] ); ?><br><?php echo esc_html( $bloomandsun_store['city'] ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:group {"className":"bs-hours","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-hours">
				<?php foreach ( $bloomandsun_store['hours'] as [ $bloomandsun_days, $bloomandsun_time ] ) : ?>
				<!-- wp:paragraph {"className":"bs-hours__day"} -->
				<p class="bs-hours__day"><?php echo esc_html( $bloomandsun_days ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"bs-hours__time"} -->
				<p class="bs-hours__time"><?php echo esc_html( $bloomandsun_time ); ?></p>
				<!-- /wp:paragraph -->
				<?php endforeach; ?>
			</div>
			<!-- /wp:group -->

			<!-- wp:buttons {"className":"bs-actions"} -->
			<div class="wp-block-buttons bs-actions"><!-- wp:button {"className":"bs-btn-arrow"} -->
<div class="wp-block-button bs-btn-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $bloomandsun_store['directions'] ); ?>" target="_blank" rel="noreferrer noopener">Itinéraire</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo bloomandsun_page_url( 'contact' ); ?>">Nous contacter</a></div>
<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
