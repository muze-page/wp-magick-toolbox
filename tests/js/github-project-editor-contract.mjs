import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';

const root = resolve( dirname( fileURLToPath( import.meta.url ) ), '../..' );
const source = await readFile( resolve( root, 'blocks/github-project/index.js' ), 'utf8' );
const registrations = [];
const blockPropsCalls = [];

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
				useBlockProps: ( props = {} ) => {
					blockPropsCalls.push( props );
					return props;
				},
		},
		components: {
			PanelBody: 'PanelBody',
			Placeholder: 'Placeholder',
			TextControl: 'TextControl',
		},
		element: {
			createElement,
			Fragment: 'Fragment',
		},
		i18n: {
			__: ( text ) => text,
		},
		serverSideRender: 'ServerSideRender',
	},
};

vm.runInNewContext( source, { window } );

assert.equal( registrations.length, 1 );
assert.equal( registrations[ 0 ].name, 'npcink/github-project' );
assert.equal( registrations[ 0 ].settings.save(), null );

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

const changes = [];
const emptyBlockPropsStart = blockPropsCalls.length;
const emptyTree = registrations[ 0 ].settings.edit( {
	attributes: { repositoryUrl: '' },
	setAttributes: ( change ) => changes.push( change ),
} );
assert.equal( blockPropsCalls.length - emptyBlockPropsStart, 1 );
const emptyControls = collect( emptyTree, 'TextControl' );

assert.equal( collect( emptyTree, 'Placeholder' ).length, 1 );
assert.equal( collect( emptyTree, 'ServerSideRender' ).length, 0 );
assert.equal( emptyControls.length, 2 );
emptyControls[ 1 ].props.onChange( 'https://github.com/muze-page/npcink-site-toolbox' );
assert.equal(
	JSON.stringify( changes[ 0 ] ),
	JSON.stringify( { repositoryUrl: 'https://github.com/muze-page/npcink-site-toolbox' } )
);

const validBlockPropsStart = blockPropsCalls.length;
const validTree = registrations[ 0 ].settings.edit( {
	attributes: { repositoryUrl: 'https://github.com/muze-page/npcink-site-toolbox' },
	setAttributes() {},
} );
assert.equal( blockPropsCalls.length - validBlockPropsStart, 1 );
const previews = collect( validTree, 'ServerSideRender' );

assert.equal( collect( validTree, 'Placeholder' ).length, 0 );
assert.equal( previews.length, 1 );
assert.equal( previews[ 0 ].props.block, 'npcink/github-project' );
assert.equal(
	previews[ 0 ].props.attributes.repositoryUrl,
	'https://github.com/muze-page/npcink-site-toolbox'
);

const invalidBlockPropsStart = blockPropsCalls.length;
const invalidTree = registrations[ 0 ].settings.edit( {
	attributes: { repositoryUrl: 'https://example.com/not-github/repository' },
	setAttributes() {},
} );
assert.equal( blockPropsCalls.length - invalidBlockPropsStart, 1 );
assert.equal( collect( invalidTree, 'Placeholder' ).length, 1 );
assert.equal( collect( invalidTree, 'ServerSideRender' ).length, 0 );

process.stdout.write( 'github-project editor contract: ok\n' );
