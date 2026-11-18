/**
 * Carrousel de la section « Idées cadeaux » : flèches précédent / suivant.
 * (Sur mobile, la ligne défile au doigt grâce au CSS.)
 * Chargé uniquement quand la section est affichée (inc/home.php).
 */
document.querySelectorAll( '.bs-gifts' ).forEach( function ( section ) {
	let rail = section.querySelector( '.wc-block-product-template' );

	if ( ! rail ) {
		return;
	}

	section.querySelectorAll( '[data-bs-scroll]' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			let card = rail.querySelector( 'li' );
			let step = card ? card.getBoundingClientRect().width + 18 : 300;

			rail.scrollBy( { left: step * Number( button.dataset.bsScroll ), behavior: 'smooth' } );
		} );
	} );
} );
