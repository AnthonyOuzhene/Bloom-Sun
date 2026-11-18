/**
 * Galerie de l'événement (back-office) : choix des photos dans la médiathèque,
 * limitée à data-max photos. Les identifiants sont enregistrés dans le champ caché.
 */
( function ( $ ) {
	$( '.bs-event-gallery' ).each( function () {
		let $gallery = $( this );
		let $input = $gallery.find( '.bs-event-gallery__input' );
		let $list = $gallery.find( '.bs-event-gallery__list' );
		let $add = $gallery.find( '.bs-event-gallery__add' );
		let $count = $gallery.find( '.bs-event-gallery__count' );
		let max = parseInt( $gallery.data( 'max' ), 10 ) || 3;
		let frame;

		function ids() {
			return $list.children().map( function () {
				return String( $( this ).data( 'id' ) );
			} ).get();
		}

		function refresh() {
			let n = ids().length;
			$input.val( ids().join( ',' ) );
			$count.text( n + ' / ' + max );
			$add.prop( 'disabled', n >= max );
		}

		function addItem( id, url ) {
			if ( ids().indexOf( String( id ) ) !== -1 || ids().length >= max ) {
				return;
			}
			$( '<li>' ).attr( 'data-id', id )
				.append( $( '<img alt="">' ).attr( 'src', url ) )
				.append( '<button type="button" class="bs-event-gallery__remove" aria-label="Retirer cette photo">×</button>' )
				.appendTo( $list );
		}

		$add.on( 'click', function () {
			if ( ! frame ) {
				frame = wp.media( {
					title: 'Photos de l\'événement',
					button: { text: 'Ajouter' },
					library: { type: 'image' },
					multiple: true,
				} );

				frame.on( 'select', function () {
					frame.state().get( 'selection' ).each( function ( attachment ) {
						let a = attachment.toJSON();
						let thumb = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
						addItem( a.id, thumb );
					} );
					refresh();
				} );
			}
			frame.open();
		} );

		$list.on( 'click', '.bs-event-gallery__remove', function () {
			$( this ).closest( 'li' ).remove();
			refresh();
		} );

		// Réordonner par glisser-déposer (la première photo est la principale)
		if ( $.fn.sortable ) {
			$list.sortable( { update: refresh } );
		}

		refresh();
	} );
} )( jQuery );
