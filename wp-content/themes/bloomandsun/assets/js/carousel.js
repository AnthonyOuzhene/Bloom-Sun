/**
 * Carrousels : flèches précédent / suivant et défilement continu.
 * (Sur mobile, la ligne défile aussi au doigt grâce au CSS.)
 *
 * Une section carrousel contient un rail qui défile et des boutons [data-bs-scroll="-1|1"].
 * Sections : Idées cadeaux (parts/home/gifts.html), Instagram (parts/home/instagram.html).
 * Chargé seulement quand une de ces sections est affichée (bloomandsun_enqueue_carousel(), inc/assets.php).
 *
 * Défilement continu : lent et sans fin (les vignettes sont recopiées à la suite, copies masquées
 * aux lecteurs d'écran). En pause au survol des vignettes, au focus clavier, au toucher, après un clic sur une flèche,
 * quand la section n'est pas à l'écran ; désactivé si le visiteur a demandé moins d'animations.
 */
( function () {
	'use strict';

	// Section => rail qui défile, vitesse du défilement continu (px par seconde, 0 = pas de défilement)
	const carousels = {
		'.bs-gifts': { rail: '.wc-block-product-template', speed: 0 },
		'.bs-instagram': { rail: '.bs-instagram__rail', speed: 40 },
	};

	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function step( rail ) {
		const item = rail.firstElementChild;
		const gap = parseFloat( window.getComputedStyle( rail ).columnGap ) || 0;

		return item ? item.getBoundingClientRect().width + gap : 300;
	}

	// La section est-elle (au moins en partie) à l'écran ?
	function onScreen( element ) {
		const box = element.getBoundingClientRect();

		return box.bottom > 0 && box.top < window.innerHeight;
	}

	// Recopie les vignettes à la suite : la boucle reprend sans saut visible. Renvoie la première copie.
	function duplicate( rail ) {
		const copies = Array.from( rail.children ).map( ( item ) => {
			const copy = item.cloneNode( true );

			copy.setAttribute( 'aria-hidden', 'true' );
			copy.querySelectorAll( 'a, button' ).forEach( ( link ) => link.setAttribute( 'tabindex', '-1' ) );
			rail.appendChild( copy );

			return copy;
		} );

		return copies[ 0 ];
	}

	function continuous( section, rail, speed ) {
		let pausedUntil = 0;
		let hovered = false;
		let position = 0;
		let last = 0;

		const firstCopy = duplicate( rail );
		rail.classList.add( 'is-continuous' ); // pas d'aimantation pendant le défilement (instagram.css)

		if ( ! firstCopy ) {
			return;
		}

		const pause = ( ms ) => { pausedUntil = Date.now() + ms; };

		// Pause au survol des vignettes (ou quand l'une d'elles a le focus clavier)
		rail.addEventListener( 'mouseenter', () => { hovered = true; } );
		rail.addEventListener( 'mouseleave', () => { hovered = false; } );
		rail.addEventListener( 'focusin', () => { hovered = true; } );
		rail.addEventListener( 'focusout', () => { hovered = false; } );
		rail.addEventListener( 'touchstart', () => pause( 6000 ), { passive: true } );
		section.querySelectorAll( '[data-bs-scroll]' ).forEach( ( button ) => {
			button.addEventListener( 'click', () => pause( 1500 ) );
		} );

		function frame( time ) {
			const elapsed = last ? Math.min( time - last, 100 ) : 0;
			last = time;

			if ( hovered || Date.now() < pausedUntil || ! onScreen( rail ) ) {
				position = rail.scrollLeft; // reprend là où le visiteur s'est arrêté
			} else {
				// Longueur d'un tour : de la première vignette à sa copie (espaces compris)
				const loop = firstCopy.offsetLeft - rail.firstElementChild.offsetLeft;

				position += ( speed * elapsed ) / 1000;

				if ( position >= loop ) {
					position -= loop;
				}

				rail.scrollLeft = position;
			}

			window.requestAnimationFrame( frame );
		}

		window.requestAnimationFrame( frame );
	}

	Object.keys( carousels ).forEach( ( sectionSelector ) => {
		const config = carousels[ sectionSelector ];

		document.querySelectorAll( sectionSelector ).forEach( ( section ) => {
			const rail = section.querySelector( config.rail );

			if ( ! rail ) {
				return;
			}

			section.querySelectorAll( '[data-bs-scroll]' ).forEach( ( button ) => {
				button.addEventListener( 'click', () => {
					rail.scrollBy( { left: step( rail ) * Number( button.dataset.bsScroll ), behavior: 'smooth' } );
				} );
			} );

			if ( config.speed && ! reducedMotion ) {
				continuous( section, rail, config.speed );
			}
		} );
	} );
}() );
