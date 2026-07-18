( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	var createElement = element.createElement;
	var Fragment = element.Fragment;
	var InspectorControls = blockEditor.InspectorControls;
	var useBlockProps = blockEditor.useBlockProps;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var ToggleControl = components.ToggleControl;
	var __ = i18n.__;

	function toggle( attributes, setAttributes, key, label ) {
		return createElement( ToggleControl, {
			__nextHasNoMarginBottom: true,
			checked: attributes[ key ],
			label: label,
			onChange: function ( value ) {
				var nextAttributes = {};
				nextAttributes[ key ] = value;
				setAttributes( nextAttributes );
			},
		} );
	}

	function Edit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;
		var items = [
			[ 'showPosts', __( '文章', 'npcink-site-toolbox' ) ],
			[ 'showComments', __( '评论', 'npcink-site-toolbox' ) ],
			[ 'showCategories', __( '分类', 'npcink-site-toolbox' ) ],
			[ 'showUsers', __( '用户', 'npcink-site-toolbox' ) ],
		].filter( function ( item ) {
			return attributes[ item[ 0 ] ];
		} );
		var preview = items.length
			? createElement(
				'dl',
				{ className: 'npcink-site-stats__items' },
				items.map( function ( item ) {
					return createElement(
						'div',
						{ className: 'npcink-site-stats__item', key: item[ 0 ] },
						createElement( 'dt', { className: 'npcink-site-stats__label' }, item[ 1 ] ),
						createElement( 'dd', { className: 'npcink-site-stats__value' }, '\u2014' )
					);
				} )
			)
			: createElement(
				'p',
				{ className: 'npcink-site-stats__empty' },
				__( '请至少选择一个统计项目。', 'npcink-site-toolbox' )
			);

		return createElement(
			Fragment,
			null,
			createElement(
				InspectorControls,
				null,
				createElement(
					PanelBody,
					{ title: __( '显示设置', 'npcink-site-toolbox' ) },
					createElement( TextControl, {
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						label: __( '标题', 'npcink-site-toolbox' ),
						onChange: function ( value ) {
							setAttributes( { title: value } );
						},
						value: attributes.title,
					} ),
					toggle( attributes, setAttributes, 'showPosts', __( '显示文章数量', 'npcink-site-toolbox' ) ),
					toggle( attributes, setAttributes, 'showComments', __( '显示评论数量', 'npcink-site-toolbox' ) ),
					toggle( attributes, setAttributes, 'showCategories', __( '显示分类数量', 'npcink-site-toolbox' ) ),
					toggle( attributes, setAttributes, 'showUsers', __( '显示用户数量', 'npcink-site-toolbox' ) )
				)
			),
			createElement(
				'section',
				useBlockProps( { className: 'npcink-site-stats' } ),
				attributes.title
					? createElement( 'h2', { className: 'npcink-site-stats__title' }, attributes.title )
					: null,
				preview
			)
		);
	}

	blocks.registerBlockType( 'npcink/site-stats', {
		edit: Edit,
		save: function () {
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.element,
	window.wp.i18n
);
