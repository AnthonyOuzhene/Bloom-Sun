/**
 * Événement : ouverture et fermeture du panneau (parts/event.html).
 * - S'ouvre au clic sur un lien vers « #bs-event » (pastille, ligne du bandeau d'annonce).
 * - Se ferme avec la croix, la touche Échap ou un clic sur le voile.
 * - Accessibilité : rôle « dialog », focus dans le panneau, retour du focus à la fermeture.
 * Chargé uniquement quand un événement est annoncé ou en cours (inc/events.php).
 */
( function () {
	let drawer = document.querySelector( '.bs-event-drawer' );
	let veil = document.querySelector( '.bs-event-veil' );

	if ( ! drawer ) {
		return;
	}

	let title = drawer.querySelector( '.bs-event-drawer__title' );
	let lastFocus = null;

	drawer.setAttribute( 'role', 'dialog' );
	drawer.setAttribute( 'aria-modal', 'true' );
	if ( title ) {
		title.id = title.id || 'bs-event-title';
		drawer.setAttribute( 'aria-labelledby', title.id );
	}
	drawer.inert = true;
	drawer.tabIndex = -1;

	function open() {
		lastFocus = document.activeElement;
		drawer.inert = false;
		veil.hidden = false;
		document.documentElement.classList.add( 'bs-event-open' );
		requestAnimationFrame( function () {
			drawer.classList.add( 'is-open' );
			veil.classList.add( 'is-open' );
			( drawer.querySelector( '.bs-event-drawer__close' ) || drawer ).focus();
		} );
	}

	function close() {
		drawer.classList.remove( 'is-open' );
		veil.classList.remove( 'is-open' );
		drawer.inert = true;
		document.documentElement.classList.remove( 'bs-event-open' );
		setTimeout( function () {
			veil.hidden = true;
		}, 300 );
		if ( lastFocus ) {
			lastFocus.focus();
		}
	}

	document.addEventListener( 'click', function ( event ) {
		let link = event.target.closest( 'a[href="#bs-event"]' );
		if ( link ) {
			event.preventDefault();
			open();
		} else if ( event.target.closest( '[data-bs-event-close]' ) ) {
			close();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( ! drawer.classList.contains( 'is-open' ) ) {
			return;
		}
		if ( 'Escape' === event.key ) {
			close();
		}
		// Le focus reste dans le panneau
		if ( 'Tab' === event.key ) {
			let items = drawer.querySelectorAll( 'a[href], button' );
			let first = items[ 0 ];
			let last = items[ items.length - 1 ];
			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		}
	} );
} )();
