/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { 
	useBlockProps, 
	InnerBlocks,
	InspectorControls
} from '@wordpress/block-editor';
import { 
	PanelBody,
	SelectControl,
	ToggleControl,
	RangeControl,
	ButtonGroup,
	Button,
	__experimentalUnitControl as UnitControl
} from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { generateColumnsCSS } from './utils/generateCSS';
import './editor.scss';

/**
 * Edit component for Columns block.
 */
export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		uniqueId,
		columnCount,
		presetLayout,
		verticalAlignment,
		columnGap,
		stackOnMobile,
		equalHeight
	} = attributes;

	// Set unique ID on first load
	useEffect( () => {
		if ( ! uniqueId ) {
			setAttributes( { uniqueId: clientId } );
		}
	}, [ clientId, uniqueId, setAttributes ] );

	// Update template when column count changes
	useEffect( () => {
		// This will trigger a re-render with the new template
	}, [ columnCount ] );

	// Generate CSS for live preview
	const columnsCSS = generateColumnsCSS( attributes, uniqueId || clientId );

	// Block props with dynamic classes and styles
	const blockProps = useBlockProps( {
		className: `wpdsgn-columns wpdsgn-columns-${uniqueId || clientId}`,
		style: {
			display: 'flex',
			flexWrap: 'wrap',
			gap: columnGap?.desktop || '20px',
			alignItems: verticalAlignment?.desktop || 'stretch'
		}
	} );

	// Generate column template based on count
	const getColumnTemplate = () => {
		const template = [];
		for ( let i = 0; i < columnCount; i++ ) {
			template.push( [ 'core/column', {} ] );
		}
		return template;
	};

	// Preset layout options
	const presetLayouts = [
		{ label: __( 'Equal', 'wp-dsgn-blocks' ), value: 'equal' },
		{ label: __( '50 / 50', 'wp-dsgn-blocks' ), value: '50-50' },
		{ label: __( '33 / 67', 'wp-dsgn-blocks' ), value: '33-67' },
		{ label: __( '67 / 33', 'wp-dsgn-blocks' ), value: '67-33' },
		{ label: __( '25 / 75', 'wp-dsgn-blocks' ), value: '25-75' },
		{ label: __( '75 / 25', 'wp-dsgn-blocks' ), value: '75-25' },
		{ label: __( '33 / 33 / 33', 'wp-dsgn-blocks' ), value: '33-33-33' },
		{ label: __( '25 / 50 / 25', 'wp-dsgn-blocks' ), value: '25-50-25' }
	];

	return (
		<>
			<InspectorControls>
				{/* Layout Panel */}
				<PanelBody
					title={ __( 'Layout', 'wp-dsgn-blocks' ) }
					initialOpen={ true }
				>
					<RangeControl
						label={ __( 'Columns', 'wp-dsgn-blocks' ) }
						value={ columnCount }
						onChange={ ( value ) => setAttributes( { columnCount: value } ) }
						min={ 1 }
						max={ 6 }
					/>

					{ columnCount <= 4 && (
						<SelectControl
							label={ __( 'Layout', 'wp-dsgn-blocks' ) }
							value={ presetLayout }
							options={ presetLayouts.filter( layout => {
								// Filter layouts based on column count
								if ( columnCount === 2 ) {
									return [ 'equal', '50-50', '33-67', '67-33', '25-75', '75-25' ].includes( layout.value );
								} else if ( columnCount === 3 ) {
									return [ 'equal', '33-33-33', '25-50-25' ].includes( layout.value );
								}
								return layout.value === 'equal';
							} ) }
							onChange={ ( value ) => setAttributes( { presetLayout: value } ) }
						/>
					) }

					<div style={{ marginBottom: '16px' }}>
						<label style={{ display: 'block', marginBottom: '8px', fontWeight: '500' }}>
							{ __( 'Vertical Alignment', 'wp-dsgn-blocks' ) }
						</label>
						<ButtonGroup>
							{ [
								{ value: 'flex-start', label: __( 'Top', 'wp-dsgn-blocks' ) },
								{ value: 'center', label: __( 'Center', 'wp-dsgn-blocks' ) },
								{ value: 'flex-end', label: __( 'Bottom', 'wp-dsgn-blocks' ) },
								{ value: 'stretch', label: __( 'Stretch', 'wp-dsgn-blocks' ) }
							].map( ( option ) => (
								<Button
									key={ option.value }
									isPressed={ ( verticalAlignment?.desktop || 'stretch' ) === option.value }
									onClick={ () => setAttributes( { 
										verticalAlignment: { 
											...verticalAlignment, 
											desktop: option.value 
										} 
									} ) }
								>
									{ option.label }
								</Button>
							) ) }
						</ButtonGroup>
					</div>

					<UnitControl
						label={ __( 'Column Gap', 'wp-dsgn-blocks' ) }
						value={ columnGap?.desktop || '20px' }
						onChange={ ( value ) => setAttributes( { 
							columnGap: { 
								...columnGap, 
								desktop: value 
							} 
						} ) }
						units={ [
							{ value: 'px', label: 'px' },
							{ value: 'rem', label: 'rem' },
							{ value: '%', label: '%' }
						] }
					/>

					<ToggleControl
						label={ __( 'Stack on Mobile', 'wp-dsgn-blocks' ) }
						checked={ stackOnMobile }
						onChange={ ( value ) => setAttributes( { stackOnMobile: value } ) }
					/>

					<ToggleControl
						label={ __( 'Equal Height', 'wp-dsgn-blocks' ) }
						checked={ equalHeight }
						onChange={ ( value ) => setAttributes( { equalHeight: value } ) }
					/>
				</PanelBody>
			</InspectorControls>

			{/* Inject CSS for live preview */}
			<style>{ columnsCSS }</style>

			<div { ...blockProps }>
				<InnerBlocks 
					allowedBlocks={ [ 'core/column' ] }
					template={ getColumnTemplate() }
					templateInsertUpdatesSelection={ false }
				/>
			</div>
		</>
	);
}
