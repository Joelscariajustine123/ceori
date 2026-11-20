/**
 * Editor registration for the CEORI dynamic blocks.
 *
 * Block definitions come from PHP (window.ceoriBlocks), so attributes and their
 * controls are declared once in inc/blocks.php. Each block shows a live,
 * server-rendered preview with settings in the sidebar.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, TextControl, SelectControl, ToggleControl, RangeControl, Disabled } = wp.components;
	const ServerSideRender = wp.serverSideRender;
	const defs = window.ceoriBlocks || {};

	function control( key, attr, value, setAttributes ) {
		const onChange = ( v ) => setAttributes( { [ key ]: v } );
		if ( attr.options ) {
			const options = Object.keys( attr.options ).map( ( k ) => ( { value: k, label: attr.options[ k ] } ) );
			return el( SelectControl, { key, label: attr.label, value, options, onChange, __nextHasNoMarginBottom: true } );
		}
		if ( attr.type === 'boolean' ) {
			return el( ToggleControl, { key, label: attr.label, checked: !! value, onChange, __nextHasNoMarginBottom: true } );
		}
		if ( attr.type === 'number' ) {
			return el( RangeControl, { key, label: attr.label, value, min: attr.min || 0, max: attr.max || 12, onChange, __nextHasNoMarginBottom: true } );
		}
		return el( TextControl, { key, label: attr.label, value: value || '', onChange, __nextHasNoMarginBottom: true } );
	}

	Object.keys( defs ).forEach( ( name ) => {
		const def = defs[ name ];
		const attrKeys = Object.keys( def.attributes || {} );

		registerBlockType( name, {
			title: def.title,
			description: def.description,
			icon: def.icon,
			category: 'ceori',
			edit( props ) {
				const blockProps = useBlockProps();
				const postId = wp.data.select( 'core/editor' ) ? wp.data.select( 'core/editor' ).getCurrentPostId() : 0;
				return el(
					Fragment,
					null,
					attrKeys.length
						? el(
								InspectorControls,
								null,
								el(
									PanelBody,
									{ title: def.title, initialOpen: true },
									attrKeys.map( ( key ) => control( key, def.attributes[ key ], props.attributes[ key ], props.setAttributes ) )
								)
						  )
						: null,
					el(
						'div',
						blockProps,
						el(
							Disabled,
							null,
							el( ServerSideRender, {
								block: name,
								attributes: props.attributes,
								urlQueryArgs: { post_id: postId },
							} )
						)
					)
				);
			},
			save: () => null,
		} );
	} );
} )( window.wp );
