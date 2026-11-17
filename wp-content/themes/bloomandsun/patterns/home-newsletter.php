<?php
/**
 * Title: Accueil — Newsletter
 * Slug: bloomandsun/home-newsletter
 * Categories: bloomandsun
 *
 * Grande carte corail. Le formulaire est une maquette (il n'envoie rien) :
 * remplace le bloc HTML par le formulaire MailPoet ou Brevo une fois l'extension installée.
 */
?>
<!-- wp:group {"tagName":"section","className":"bs-section bs-newsletter","layout":{"type":"default"}} -->
<section class="wp-block-group bs-section bs-newsletter">
	<!-- wp:group {"className":"bs-container","layout":{"type":"default"}} -->
	<div class="wp-block-group bs-container">
		<!-- wp:group {"className":"bs-newsletter__card","layout":{"type":"default"}} -->
		<div class="wp-block-group bs-newsletter__card">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Les nouveautés <em>en avant-première</em></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Un e-mail quand les nouvelles pièces arrivent en magasin. Pas plus.</p>
			<!-- /wp:paragraph -->

			<!-- wp:html -->
			<form class="bs-newsletter__form" action="#" method="post">
				<label class="screen-reader-text" for="bs-newsletter-email">Adresse e-mail</label>
				<input id="bs-newsletter-email" type="email" name="email" placeholder="votre@email.fr" autocomplete="email" required>
				<button type="submit" class="wp-element-button">S'inscrire</button>
			</form>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
