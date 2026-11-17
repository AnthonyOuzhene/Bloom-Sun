/**
 * Aperçu des données liées dans l'éditeur de blocs.
 *
 * Les gabarits lient certains textes et liens aux sources « bloomandsun/store »
 * et « bloomandsun/links » (voir inc/bindings.php). Sur le site, PHP fournit
 * les valeurs ; ici, on affiche les mêmes valeurs dans l'éditeur.
 * Les données arrivent dans window.bloomandsunBindings (inc/assets.php).
 */
( function ( wp, data ) {
	if ( ! wp || ! wp.blocks || ! wp.blocks.registerBlockBindingsSource || ! data ) {
		return;
	}

	function resolveLink( args ) {
		switch ( args && args.key ) {
			case 'shop':
				return data.links.shop;
			case 'new':
				return data.links.new;
			case 'category':
				return data.links.categories[ args.slug ] || data.links.shop;
			case 'page':
				return data.links.pages[ args.slug ] || '';
			case 'tag':
				return data.links.tags[ args.slug ] || data.links.shop;
			default:
				return '';
		}
	}

	function source( name, label, resolve ) {
		wp.blocks.registerBlockBindingsSource( {
			name: name,
			label: label,
			getValues: function ( context ) {
				var values = {};
				Object.keys( context.bindings ).forEach( function ( attribute ) {
					values[ attribute ] = resolve( context.bindings[ attribute ].args || {} );
				} );
				return values;
			},
			canUserEditValue: function () {
				return false; // se modifient dans les menus « Infos du magasin » et « Page d'accueil »
			},
		} );
	}

	source( 'bloomandsun/store', 'Infos du magasin', function ( args ) {
		return data.store[ args.key ] || '';
	} );

	source( 'bloomandsun/home', 'Page d\'accueil', function ( args ) {
		return data.home[ args.key ] || '';
	} );

	source( 'bloomandsun/links', 'Liens du site', resolveLink );
} )( window.wp, window.bloomandsunBindings );
