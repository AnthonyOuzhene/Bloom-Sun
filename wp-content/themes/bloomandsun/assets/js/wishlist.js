/**
 * Liste d'envies : cœurs sur les cartes produit et la fiche produit, compteur de l'en-tête.
 *
 * - La liste est gardée dans un cookie (identifiants séparés par des virgules, le plus récent
 *   en premier) : PHP le lit pour afficher la page « Liste d'envies » (inc/wishlist.php).
 * - Le bouton cœur est copié depuis le modèle <template id="bs-wish-button"> de l'en-tête
 *   (parts/layout/header.html) : aucun HTML n'est écrit ici.
 */
( function () {
	'use strict';

	const template = document.getElementById( 'bs-wish-button' );

	if ( ! template ) {
		return;
	}

	const settings = window.bloomandsunWishlist || {};
	const cookieName = settings.cookie || 'bs_wishlist';
	const max = settings.max || 48;
	const path = settings.path || '/';
	const oneYear = 60 * 60 * 24 * 365;

	let ids = read();

	/* Cookie
	   ---------------------------------------------------------------------- */
	function read() {
		const match = document.cookie.match( new RegExp( '(?:^|; )' + cookieName + '=([^;]*)' ) );

		if ( ! match ) {
			return [];
		}

		return decodeURIComponent( match[ 1 ] )
			.split( ',' )
			.map( Number )
			.filter( ( id ) => id > 0 );
	}

	function write() {
		let cookie = cookieName + '=' + encodeURIComponent( ids.join( ',' ) );

		cookie += '; path=' + path;
		cookie += '; max-age=' + ( ids.length ? oneYear : 0 );
		cookie += '; SameSite=Lax';

		if ( 'https:' === window.location.protocol ) {
			cookie += '; Secure';
		}

		document.cookie = cookie;
	}

	function has( id ) {
		return ids.includes( id );
	}

	function toggle( id ) {
		ids = has( id ) ? ids.filter( ( item ) => item !== id ) : [ id, ...ids ].slice( 0, max );
		write();
		refresh();
	}

	/* Affichage
	   ---------------------------------------------------------------------- */
	function updateButton( button ) {
		const active = has( Number( button.dataset.productId ) );
		const label = active ? button.dataset.labelRemove : button.dataset.labelAdd;

		button.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
		button.title = label;
		button.querySelector( '.bs-wish__label' ).textContent = label;
	}

	function refresh() {
		document.querySelectorAll( '.bs-wish' ).forEach( updateButton );
		document.querySelectorAll( '.bs-wishlist__count' ).forEach( ( count ) => {
			count.textContent = ids.length ? String( ids.length ) : '';
		} );
	}

	function createButton( id ) {
		const button = template.content.firstElementChild.cloneNode( true );

		button.dataset.productId = String( id );
		updateButton( button );

		return button;
	}

	// Ajoute un cœur aux cartes produit (y compris celles chargées plus tard par les filtres).
	function decorate() {
		document.querySelectorAll( 'li.wc-block-product:not([data-bs-wish])' ).forEach( ( card ) => {
			const match = card.className.match( /\bpost-(\d+)\b/ );

			if ( ! match ) {
				return;
			}

			card.dataset.bsWish = '';
			( card.querySelector( '.wc-block-components-product-image' ) || card ).appendChild( createButton( Number( match[ 1 ] ) ) );
		} );

		// Fiche produit : cœur sur la photo principale.
		const gallery = document.querySelector( '.bs-product__gallery:not([data-bs-wish])' );
		const product = document.body.className.match( /\bpostid-(\d+)\b/ );

		if ( gallery && product ) {
			gallery.dataset.bsWish = '';
			gallery.appendChild( createButton( Number( product[ 1 ] ) ) );
		}
	}

	/* Événements
	   ---------------------------------------------------------------------- */
	document.addEventListener( 'click', ( event ) => {
		const button = event.target.closest( '.bs-wish' );

		if ( ! button ) {
			return;
		}

		event.preventDefault();
		toggle( Number( button.dataset.productId ) );

		// Page « Liste d'envies » : la carte retirée disparaît ; liste vide => message de la page.
		const list = button.closest( '.bs-wishlist-products' );

		if ( list && 'false' === button.getAttribute( 'aria-pressed' ) ) {
			button.closest( 'li.wc-block-product' ).remove();

			if ( ! list.querySelector( 'li.wc-block-product' ) ) {
				window.location.reload();
			}
		}
	} );

	// Retour arrière dans le navigateur, autre onglet : on relit le cookie.
	window.addEventListener( 'pageshow', () => {
		ids = read();
		refresh();
	} );

	let pending = false;

	new MutationObserver( () => {
		if ( pending ) {
			return;
		}

		pending = true;
		window.requestAnimationFrame( () => {
			pending = false;
			decorate();
		} );
	} ).observe( document.body, { childList: true, subtree: true } );

	decorate();
	refresh();
}() );
