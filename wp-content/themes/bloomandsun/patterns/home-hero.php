<?php
/**
 * Title: Accueil — Bandeau principal
 * Slug: bloomandsun/home-hero
 * Categories: bloomandsun
 *
 * Grande carte vert sapin : texte à gauche, photo ronde (filet doré),
 * petite photo de détail et badge « Nouvelle collection » à droite.
 * Photos : dans l'éditeur, sélectionner le groupe coloré,
 * Styles > Arrière-plan > Image, puis supprimer l'étiquette.
 */
?>
<!-- wp:group {"tagName":"section","className":"bs-hero","layout":{"type":"default"}} -->
<section class="wp-block-group bs-hero">
	<!-- wp:group {"className":"bs-container","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container">
		<!-- wp:group {"className":"bs-hero__card","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-hero__card">
			<!-- wp:group {"className":"bs-hero__text","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-hero__text">
				<!-- wp:paragraph {"className":"bs-chip"} -->
				<p class="bs-chip">Concept store · Automne-hiver</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"className":"bs-hero__title"} -->
				<h1 class="wp-block-heading bs-hero__title">Des pièces <em>douces</em> pour les jours plus courts</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"bs-hero__lead"} -->
				<p class="bs-hero__lead">Mailles, robes fluides et manteaux choisis en magasin, à porter dès maintenant. Commandez en ligne ou venez essayer.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"className":"bs-actions"} -->
				<div class="wp-block-buttons bs-actions"><!-- wp:button {"className":"bs-btn-arrow"} -->
<div class="wp-block-button bs-btn-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo bloomandsun_shop_url(); ?>">Découvrir la collection</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo bloomandsun_page_url( 'le-magasin' ); ?>">Venir au magasin</a></div>
<!-- /wp:button --></div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"bs-hero__visual","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-hero__visual">
				<!-- wp:group {"className":"bs-photo bs-hero__circle","backgroundColor":"rose","layout":{"type":"default"}} -->
				<div class="wp-block-group bs-photo bs-hero__circle has-rose-background-color has-background">
					<!-- wp:paragraph {"className":"bs-photo__label"} -->
					<p class="bs-photo__label">Photo : tenue portée</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"bs-photo bs-hero__small","backgroundColor":"sand","layout":{"type":"default"}} -->
				<div class="wp-block-group bs-photo bs-hero__small has-sand-background-color has-background">
					<!-- wp:paragraph {"className":"bs-photo__label"} -->
					<p class="bs-photo__label">Photo : détail</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

				<!-- wp:html -->
				<svg class="bs-hero__badge" viewBox="0 0 120 120" aria-hidden="true">
					<defs><path id="bs-badge-path" d="M60,60 m-42,0 a42,42 0 1,1 84,0 a42,42 0 1,1 -84,0"/></defs>
					<circle cx="60" cy="60" r="59" fill="#A65F4F"/>
					<text fill="#FBF7F2" font-family="Outfit, sans-serif" font-size="10" font-weight="500" letter-spacing="2"><textPath href="#bs-badge-path" textLength="258">NOUVELLE COLLECTION · AUTOMNE-HIVER ·</textPath></text>
					<g transform="translate(44 44) scale(1.333)" fill="none" stroke="#FBF7F2" stroke-width="1.6" stroke-linecap="round"><path d="M3 17h18"/><path d="M12 6v4M6.3 9.3l2.2 2.2M17.7 9.3l-2.2 2.2M4 14h2.5M17.5 14H20"/><path d="M8.5 17a3.5 3.5 0 0 1 7 0"/></g>
				</svg>
				<!-- /wp:html -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
