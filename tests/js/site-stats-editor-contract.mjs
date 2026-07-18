import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';

const root = resolve( dirname( fileURLToPath( import.meta.url ) ), '../..' );
const source = await readFile( resolve( root, 'blocks/site-stats/index.js' ), 'utf8' );
const registrations = [];

function createElement( type, props, ...children ) {
	return { type, props: props || {}, children };
}

const window = {
	wp: {
		blocks: {
			registerBlockType( name, settings ) {
				registrations.push( { name, settings } );
				return settings;
			},
		},
		blockEditor: {
			InspectorControls: 'InspectorControls',
			useBlockProps: ( props = {} ) => props,
		},
		components: {
			PanelBody: 'PanelBody',
			TextControl: 'TextControl',
			ToggleControl: 'ToggleControl',
		},
		element: {
			createElement,
			Fragment: 'Fragment',
		},
		i18n: {
			__: ( text ) => text,
		},
	},
};

vm.runInNewContext( source, { window } );

assert.equal( registrations.length, 1 );
assert.equal( registrations[ 0 ].name, 'npcink/site-stats' );
assert.equal( registrations[ 0 ].settings.save(), null );

const changes = [];
const tree = registrations[ 0 ].settings.edit( {
	attributes: {
		title: '站点数据',
		showPosts: true,
		showComments: true,
		showCategories: true,
		showUsers: true,
	},
	setAttributes: ( change ) => changes.push( change ),
} );

function collect( node, type, results = [] ) {
	if ( ! node || typeof node !== 'object' ) {
		return results;
	}
	if ( node.type === type ) {
		results.push( node );
	}
	for ( const child of node.children || [] ) {
		if ( Array.isArray( child ) ) {
			for ( const nested of child ) {
				collect( nested, type, results );
			}
		} else {
			collect( child, type, results );
		}
	}
	return results;
}

const textControls = collect( tree, 'TextControl' );
const toggles = collect( tree, 'ToggleControl' );
const previews = collect( tree, 'dd' );

assert.equal( textControls.length, 1 );
assert.equal( toggles.length, 4 );
assert.equal( previews.length, 4 );

textControls[ 0 ].props.onChange( '新的标题' );
toggles.find( ( control ) => control.props.label === '显示评论数量' ).props.onChange( false );

assert.equal( JSON.stringify( changes[ 0 ] ), JSON.stringify( { title: '新的标题' } ) );
assert.equal( JSON.stringify( changes[ 1 ] ), JSON.stringify( { showComments: false } ) );

process.stdout.write( 'site-stats editor contract: ok\n' );
