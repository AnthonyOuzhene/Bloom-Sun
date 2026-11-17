/**
 * Bloc « Plan du magasin » : aperçu en direct dans l'éditeur (rendu par render.php)
 * et lien vers les réglages. Pas d'outil de compilation nécessaire.
 */
( function ( wp ) {
	const el = wp.element.createElement;

	wp.blocks.registerBlockType( 'bloomandsun/store-map', {
		edit: function () {
			const blockProps = wp.blockEditor.useBlockProps();

			return el(
				'div',
				blockProps,
				el(
					wp.blockEditor.InspectorControls,
					null,
					el(
						wp.components.PanelBody,
						{ title: 'Modifier ces informations' },
						el( 'p', null, 'Adresse, horaires, Instagram et plan se modifient dans Réglages > Infos du magasin.' ),
						el( wp.components.Button, { variant: 'secondary', href: 'options-general.php?page=bloomandsun-store', target: '_blank' }, 'Ouvrir les réglages' )
					)
				),
				el( wp.serverSideRender, { block: 'bloomandsun/store-map' } )
			);
		},
		save: function () {
			return null; // bloc dynamique : rendu par render.php
		},
	} );
} )( window.wp );
