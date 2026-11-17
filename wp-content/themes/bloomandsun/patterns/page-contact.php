<?php
/**
 * Title: Page — Contact
 * Slug: bloomandsun/page-contact
 * Categories: bloomandsun
 * Block Types: core/post-content
 *
 * Contenu de la page Contact (modifiable dans Pages > Contact) :
 * introduction, blocs « Infos du magasin » et « Plan du magasin », formulaire.
 * Adresse, horaires et plan : Réglages > Infos du magasin.
 *
 * Formulaire : Contact Form 7 (menu « Contact » de l'admin), formulaire « Contact Bloom & Sun ».
 * Sur une autre installation, remplacer l'identifiant a9f242a par celui du formulaire.
 */
?>
<!-- wp:group {"tagName":"section","className":"bs-contact","layout":{"type":"default"}} -->
<section class="wp-block-group bs-contact">
	<!-- wp:group {"className":"bs-container","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container">
		<!-- wp:group {"className":"bs-contact__intro","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-contact__intro">
			<!-- wp:paragraph {"className":"bs-chip"} -->
			<p class="bs-chip">Contact</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":1} -->
			<h1 class="wp-block-heading">Venez nous <em>rendre visite</em></h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"bs-contact__lead"} -->
			<p class="bs-contact__lead">Prêt-à-porter &amp; accessoires féminins, choisis avec soin au cœur du Havre. Une invitation au voyage &amp; good vibes&nbsp;: on vous attend pour essayer, discuter et vous conseiller.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-contact__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-contact__grid">
			<!-- wp:bloomandsun/store-card /-->

			<!-- wp:bloomandsun/store-map /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"bs-contact__write","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-contact__write">
			<!-- wp:group {"className":"bs-contact__write-text","layout":{"type":"default"}} -->
			<div class="wp-block-group bs-contact__write-text">
				<!-- wp:heading -->
				<h2 class="wp-block-heading">Écrivez-<em>nous</em></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>Une question sur une pièce, une taille, un retrait en magasin&nbsp;? On vous répond au plus vite.</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"bs-chip bs-chip--insta"} -->
				<p class="bs-chip bs-chip--insta"><a href="https://www.instagram.com/bloomandsun_boutique/" target="_blank" rel="noreferrer noopener">Ou en message sur Instagram</a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:shortcode -->
[contact-form-7 id="a9f242a" title="Contact Bloom &amp; Sun" html_class="bs-contact__form"]
			<!-- /wp:shortcode -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
