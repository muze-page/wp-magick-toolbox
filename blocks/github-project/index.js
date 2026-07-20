( function ( blocks, blockEditor, components, element, i18n, ServerSideRender ) {
	'use strict';

	var createElement = element.createElement;
	var Fragment = element.Fragment;
	var InspectorControls = blockEditor.InspectorControls;
	var useBlockProps = blockEditor.useBlockProps;
	var PanelBody = components.PanelBody;
	var Placeholder = components.Placeholder;
	var TextControl = components.TextControl;
	var TextareaControl = components.TextareaControl;
	var __ = i18n.__;
	var repositoryPattern = /^https:\/\/(?:www\.)?github\.com\/[A-Za-z0-9](?:[A-Za-z0-9-]{0,37}[A-Za-z0-9])?\/[A-Za-z0-9._-]{1,100}\/?$/i;

	function isRepositoryUrl( value ) {
		return repositoryPattern.test( ( value || '' ).trim().replace( /\.git\/?$/i, '' ) );
	}

	function repositoryControl( value, setAttributes ) {
		var isInvalid = value !== '' && ! isRepositoryUrl( value );

		return createElement( TextControl, {
			__next40pxDefaultSize: true,
			__nextHasNoMarginBottom: true,
			help: isInvalid
				? __( '请输入 https://github.com/owner/repository 格式的公开仓库地址。', 'npcink-site-toolbox' )
				: __( '仅获取公开仓库信息，不需要 GitHub Token。', 'npcink-site-toolbox' ),
			label: __( 'GitHub 仓库地址', 'npcink-site-toolbox' ),
			onChange: function ( nextValue ) {
				setAttributes( { repositoryUrl: nextValue } );
			},
			placeholder: 'https://github.com/owner/repository',
			type: 'url',
			value: value,
		} );
	}

	function descriptionControl( value, setAttributes ) {
		return createElement( TextareaControl, {
			__nextHasNoMarginBottom: true,
			help: __( '留空时使用 GitHub 项目描述；接口不可用时仍可显示这段摘要。', 'npcink-site-toolbox' ),
			label: __( '自定义项目摘要', 'npcink-site-toolbox' ),
			onChange: function ( nextValue ) {
				setAttributes( { customDescription: nextValue } );
			},
			value: value,
		} );
	}

	function Edit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;
		var repositoryUrl = attributes.repositoryUrl || '';
		var customDescription = attributes.customDescription || '';
		var isValid = isRepositoryUrl( repositoryUrl );
		var inspector = createElement(
			InspectorControls,
			null,
			createElement(
				PanelBody,
				{ title: __( '项目设置', 'npcink-site-toolbox' ) },
				repositoryControl( repositoryUrl, setAttributes ),
				descriptionControl( customDescription, setAttributes )
			)
		);
		var previewProps = useBlockProps( isValid
			? { className: 'npcink-github-project-editor' }
			: {
				icon: 'admin-links',
				instructions: __( '粘贴公开 GitHub 仓库地址，自动展示项目说明和实时数据。', 'npcink-site-toolbox' ),
				label: __( 'GitHub 项目', 'npcink-site-toolbox' ),
			}
		);
		var preview = isValid
			? createElement(
				'div',
				previewProps,
				createElement( ServerSideRender, {
					attributes: attributes,
					block: 'npcink/github-project',
				} )
			)
			: createElement(
				Placeholder,
				previewProps,
				repositoryControl( repositoryUrl, setAttributes )
			);

		return createElement( Fragment, null, inspector, preview );
	}

	blocks.registerBlockType( 'npcink/github-project', {
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
	window.wp.i18n,
	window.wp.serverSideRender
);
