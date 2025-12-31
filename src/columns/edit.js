/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	InnerBlocks,
	BlockControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
	Button,
	ButtonGroup,
	ColorPicker,
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { generateColumnsStyles, generateColumnStyles } from '../utils/styles';

/**
 * Preset layouts configuration.
 */
const PRESET_LAYOUTS = {
	equal: { label: __( 'Equal', 'flexblocks-layout-builder' ), widths: null },
	'50-50': {
		label: __( '50 / 50', 'flexblocks-layout-builder' ),
		widths: [ '50%', '50%' ],
	},
	'33-33-33': {
		label: __( '33 / 33 / 33', 'flexblocks-layout-builder' ),
		widths: [ '33.333%', '33.333%', '33.333%' ],
	},
	'25-75': {
		label: __( '25 / 75', 'flexblocks-layout-builder' ),
		widths: [ '25%', '75%' ],
	},
	'75-25': {
		label: __( '75 / 25', 'flexblocks-layout-builder' ),
		widths: [ '75%', '25%' ],
	},
	'25-50-25': {
		label: __( '25 / 50 / 25', 'flexblocks-layout-builder' ),
		widths: [ '25%', '50%', '25%' ],
	},
	'20-60-20': {
		label: __( '20 / 60 / 20', 'flexblocks-layout-builder' ),
		widths: [ '20%', '60%', '20%' ],
	},
};

/**
 * Edit component for Columns block.
 *
 * @param {Object}   props               Block properties.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @param {string}   props.clientId      Block client ID.
 * @return {Element} Columns block edit component.
 */
export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		columnCount,
		columns,
		verticalAlignment,
		columnGap,
		stackOnMobile,
		mobileBreakpoint,
		reverseOnMobile,
		presetLayout,
	} = attributes;

	// Initialize columns array when column count changes
	useEffect( () => {
		const newColumns = [ ...columns ];
		if ( newColumns.length < columnCount ) {
			// Add new columns
			for ( let i = newColumns.length; i < columnCount; i++ ) {
				newColumns.push( {
					width: '',
					backgroundColor: '',
					paddingTop: '',
					paddingRight: '',
					paddingBottom: '',
					paddingLeft: '',
				} );
			}
		} else if ( newColumns.length > columnCount ) {
			// Remove excess columns
			newColumns.splice( columnCount );
		}
		setAttributes( { columns: newColumns } );
	}, [ columnCount ] );

	const columnsStyles = generateColumnsStyles( attributes );
	const blockProps = useBlockProps( {
		style: columnsStyles,
		className: `flexblocks-columns flexblocks-columns-${ columnCount }`,
	} );

	/**
	 * Update column count.
	 *
	 * @param {number} count New column count.
	 */
	const updateColumnCount = ( count ) => {
		setAttributes( {
			columnCount: count,
			presetLayout: 'equal',
		} );
	};

	/**
	 * Apply preset layout.
	 *
	 * @param {string} preset Preset key.
	 */
	const applyPreset = ( preset ) => {
		setAttributes( { presetLayout: preset } );

		if ( preset === 'equal' ) {
			// Reset all widths
			const updatedColumns = columns.map( ( col ) => ( {
				...col,
				width: '',
			} ) );
			setAttributes( { columns: updatedColumns } );
		} else {
			const layout = PRESET_LAYOUTS[ preset ];
			if ( layout && layout.widths ) {
				// Update column count if needed
				if ( layout.widths.length !== columnCount ) {
					setAttributes( { columnCount: layout.widths.length } );
				}

				// Apply widths
				const updatedColumns = layout.widths.map( ( width, index ) => ( {
					...( columns[ index ] || {} ),
					width,
				} ) );
				setAttributes( { columns: updatedColumns } );
			}
		}
	};

	/**
	 * Update individual column property.
	 *
	 * @param {number} index Column index.
	 * @param {string} prop  Property name.
	 * @param {*}      value New value.
	 */
	const updateColumn = ( index, prop, value ) => {
		const updatedColumns = [ ...columns ];
		updatedColumns[ index ] = {
			...updatedColumns[ index ],
			[ prop ]: value,
		};
		setAttributes( { columns: updatedColumns } );
	};

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon="minus"
						label={ __( 'Decrease Columns', 'flexblocks-layout-builder' ) }
						onClick={ () =>
							columnCount > 1 && updateColumnCount( columnCount - 1 )
						}
						disabled={ columnCount <= 1 }
					/>
					<ToolbarButton
						icon="plus"
						label={ __( 'Increase Columns', 'flexblocks-layout-builder' ) }
						onClick={ () =>
							columnCount < 6 && updateColumnCount( columnCount + 1 )
						}
						disabled={ columnCount >= 6 }
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				{/* Layout Panel */}
				<PanelBody
					title={ __( 'Layout', 'flexblocks-layout-builder' ) }
					initialOpen={ true }
				>
					<RangeControl
						label={ __( 'Number of Columns', 'flexblocks-layout-builder' ) }
						value={ columnCount }
						onChange={ updateColumnCount }
						min={ 1 }
						max={ 6 }
					/>

					<div style={ { marginBottom: '16px' } }>
						<strong>{ __( 'Preset Layouts', 'flexblocks-layout-builder' ) }</strong>
						<div style={ { marginTop: '8px', display: 'grid', gap: '8px' } }>
							{ Object.keys( PRESET_LAYOUTS )
								.filter(
									( key ) =>
										key === 'equal' ||
										! PRESET_LAYOUTS[ key ].widths ||
										PRESET_LAYOUTS[ key ].widths.length === columnCount
								)
								.map( ( key ) => (
									<Button
										key={ key }
										variant={
											presetLayout === key ? 'primary' : 'secondary'
										}
										onClick={ () => applyPreset( key ) }
										style={ { width: '100%' } }
									>
										{ PRESET_LAYOUTS[ key ].label }
									</Button>
								) ) }
						</div>
					</div>

					<TextControl
						label={ __( 'Column Gap', 'flexblocks-layout-builder' ) }
						value={ columnGap }
						onChange={ ( value ) => setAttributes( { columnGap: value } ) }
						help={ __( 'e.g., 20px, 1rem, 2em', 'flexblocks-layout-builder' ) }
					/>
				</PanelBody>

				{/* Alignment Panel */}
				<PanelBody
					title={ __( 'Alignment', 'flexblocks-layout-builder' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Vertical Alignment', 'flexblocks-layout-builder' ) }
						value={ verticalAlignment }
						options={ [
							{ label: __( 'Top', 'flexblocks-layout-builder' ), value: 'top' },
							{
								label: __( 'Center', 'flexblocks-layout-builder' ),
								value: 'center',
							},
							{ label: __( 'Bottom', 'flexblocks-layout-builder' ), value: 'bottom' },
							{
								label: __( 'Stretch', 'flexblocks-layout-builder' ),
								value: 'stretch',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { verticalAlignment: value } )
						}
					/>
				</PanelBody>

				{/* Responsive Panel */}
				<PanelBody
					title={ __( 'Responsive', 'flexblocks-layout-builder' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Stack on Mobile', 'flexblocks-layout-builder' ) }
						checked={ stackOnMobile }
						onChange={ ( value ) => setAttributes( { stackOnMobile: value } ) }
					/>
					{ stackOnMobile && (
						<>
							<TextControl
								label={ __( 'Mobile Breakpoint', 'flexblocks-layout-builder' ) }
								value={ mobileBreakpoint }
								onChange={ ( value ) =>
									setAttributes( { mobileBreakpoint: value } )
								}
								help={ __(
									'e.g., 768px, 48em',
									'flexblocks-layout-builder'
								) }
							/>
							<ToggleControl
								label={ __(
									'Reverse Column Order on Mobile',
									'flexblocks-layout-builder'
								) }
								checked={ reverseOnMobile }
								onChange={ ( value ) =>
									setAttributes( { reverseOnMobile: value } )
								}
							/>
						</>
					) }
				</PanelBody>

				{/* Individual Column Settings */}
				{ columns.map( ( column, index ) => (
					<PanelBody
						key={ index }
						title={ `${ __( 'Column', 'flexblocks-layout-builder' ) } ${ index + 1 }` }
						initialOpen={ false }
					>
						<TextControl
							label={ __( 'Custom Width', 'flexblocks-layout-builder' ) }
							value={ column.width || '' }
							onChange={ ( value ) => updateColumn( index, 'width', value ) }
							help={ __(
								'e.g., 50%, 300px, 33.333%',
								'flexblocks-layout-builder'
							) }
						/>

						<div style={ { marginTop: '16px', marginBottom: '16px' } }>
							<strong>
								{ __( 'Background Color', 'flexblocks-layout-builder' ) }
							</strong>
							<ColorPicker
								color={ column.backgroundColor || '' }
								onChangeComplete={ ( value ) =>
									updateColumn( index, 'backgroundColor', value.hex )
								}
							/>
							{ column.backgroundColor && (
								<Button
									variant="tertiary"
									isDestructive
									onClick={ () =>
										updateColumn( index, 'backgroundColor', '' )
									}
									style={ { marginTop: '8px' } }
								>
									{ __( 'Clear Color', 'flexblocks-layout-builder' ) }
								</Button>
							) }
						</div>

						<hr />

						<div style={ { marginTop: '16px' } }>
							<strong>{ __( 'Padding', 'flexblocks-layout-builder' ) }</strong>
						</div>
						<TextControl
							label={ __( 'Top', 'flexblocks-layout-builder' ) }
							value={ column.paddingTop || '' }
							onChange={ ( value ) =>
								updateColumn( index, 'paddingTop', value )
							}
						/>
						<TextControl
							label={ __( 'Right', 'flexblocks-layout-builder' ) }
							value={ column.paddingRight || '' }
							onChange={ ( value ) =>
								updateColumn( index, 'paddingRight', value )
							}
						/>
						<TextControl
							label={ __( 'Bottom', 'flexblocks-layout-builder' ) }
							value={ column.paddingBottom || '' }
							onChange={ ( value ) =>
								updateColumn( index, 'paddingBottom', value )
							}
						/>
						<TextControl
							label={ __( 'Left', 'flexblocks-layout-builder' ) }
							value={ column.paddingLeft || '' }
							onChange={ ( value ) =>
								updateColumn( index, 'paddingLeft', value )
							}
						/>
					</PanelBody>
				) ) }
			</InspectorControls>

			<div { ...blockProps }>
				{ Array.from( { length: columnCount } ).map( ( _, index ) => {
					const columnData = columns[ index ] || {};
					const columnStyles = generateColumnStyles( columnData, columnCount );

					return (
						<div
							key={ index }
							className="flexblocks-column"
							style={ columnStyles }
						>
							<InnerBlocks
								templateLock={ false }
								renderAppender={
									index === 0 ? InnerBlocks.ButtonBlockAppender : false
								}
							/>
						</div>
					);
				} ) }
			</div>
		</>
	);
}

