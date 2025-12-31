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
	TabPanel,
	SelectControl,
	ToggleControl,
	TextControl,
	RangeControl,
	ButtonGroup,
	Button,
	__experimentalUnitControl as UnitControl,
	__experimentalBoxControl as BoxControl,
	ColorPicker
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
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
		customWidths,
		verticalAlignment,
		horizontalAlignment,
		columnGap,
		rowGap,
		stackOnMobile,
		stackBreakpoint,
		stackDirection,
		equalHeight,
		reverseColumns,
		columnSettings,
		customClass,
		customId,
		hideOnDevices
	} = attributes;

	// Set unique ID on first load
	useEffect( () => {
		if ( ! uniqueId ) {
			setAttributes( { uniqueId: clientId } );
		}
	}, [ clientId, uniqueId, setAttributes ] );

	// Initialize column settings when column count changes
	useEffect( () => {
		const newColumnSettings = [];
		for ( let i = 0; i < columnCount; i++ ) {
			newColumnSettings.push( columnSettings[ i ] || {
				width: '',
				backgroundColor: '',
				padding: {
					top: '',
					right: '',
					bottom: '',
					left: '',
					linked: true
				},
				customClass: '',
				order: i + 1,
				verticalAlign: 'auto'
			} );
		}
		setAttributes( { columnSettings: newColumnSettings } );
	}, [ columnCount ] );

	// Get current device for responsive controls
	const deviceType = useSelect( ( select ) => {
		const { __experimentalGetPreviewDeviceType } = select( 'core/edit-post' ) || {};
		return __experimentalGetPreviewDeviceType ? __experimentalGetPreviewDeviceType() : 'Desktop';
	}, [] );

	const currentDevice = deviceType.toLowerCase();

	// Helper function to get responsive value
	const getResponsiveValue = ( responsiveObj, device = currentDevice, fallback = '' ) => {
		if ( ! responsiveObj || typeof responsiveObj !== 'object' ) {
			return fallback;
		}
		return responsiveObj[ device ] || responsiveObj.desktop || fallback;
	};

	// Helper function to set responsive value
	const setResponsiveValue = ( attributeName, device, value ) => {
		const currentValues = attributes[ attributeName ] || {};
		setAttributes( {
			[ attributeName ]: {
				...currentValues,
				[ device ]: value
			}
		} );
	};

	// Preset layouts
	const presetLayouts = {
		equal: { label: __( 'Equal', 'wp-dsgn-blocks' ), widths: [] },
		'50-50': { label: '50/50', widths: [ '50%', '50%' ] },
		'33-66': { label: '33/66', widths: [ '33.33%', '66.67%' ] },
		'66-33': { label: '66/33', widths: [ '66.67%', '33.33%' ] },
		'25-75': { label: '25/75', widths: [ '25%', '75%' ] },
		'75-25': { label: '75/25', widths: [ '75%', '25%' ] },
		'33-33-33': { label: '33/33/33', widths: [ '33.33%', '33.33%', '33.33%' ] },
		'25-50-25': { label: '25/50/25', widths: [ '25%', '50%', '25%' ] },
		'50-25-25': { label: '50/25/25', widths: [ '50%', '25%', '25%' ] },
		'25-25-50': { label: '25/25/50', widths: [ '25%', '25%', '50%' ] },
		'25-25-25-25': { label: '25/25/25/25', widths: [ '25%', '25%', '25%', '25%' ] },
		'40-20-20-20': { label: '40/20/20/20', widths: [ '40%', '20%', '20%', '20%' ] }
	};

	// Generate CSS for live preview
	const columnsCSS = generateColumnsCSS( attributes, uniqueId || clientId );

	// Create column template based on count
	const getColumnTemplate = () => {
		const template = [];
		for ( let i = 0; i < columnCount; i++ ) {
			template.push( [ 'core/column', {} ] );
		}
		return template;
	};

	// Block props with dynamic classes and styles
	const blockProps = useBlockProps( {
		className: `wpdsgn-columns wpdsgn-columns-${uniqueId || clientId} wpdsgn-columns-${columnCount} ${customClass}`,
		id: customId || undefined,
		style: {
			display: 'flex',
			flexDirection: getResponsiveValue( reverseColumns ) ? 'row-reverse' : 'row',
			alignItems: getResponsiveValue( verticalAlignment ) || 'stretch',
			justifyContent: getResponsiveValue( horizontalAlignment ) || 'flex-start',
			gap: `${getResponsiveValue( rowGap ) || '20px'} ${getResponsiveValue( columnGap ) || '20px'}`,
			height: equalHeight ? 'auto' : 'auto'
		}
	} );

	// Responsive tabs for Inspector Controls
	const responsiveTabs = [
		{
			name: 'desktop',
			title: __( 'Desktop', 'wp-dsgn-blocks' ),
			className: 'wpdsgn-responsive-tab'
		},
		{
			name: 'tablet',
			title: __( 'Tablet', 'wp-dsgn-blocks' ),
			className: 'wpdsgn-responsive-tab'
		},
		{
			name: 'mobile',
			title: __( 'Mobile', 'wp-dsgn-blocks' ),
			className: 'wpdsgn-responsive-tab'
		}
	];

	return (
		<>
			<InspectorControls>
				<TabPanel
					className="wpdsgn-main-tabs"
					activeClass="is-active"
					tabs={ [
						{
							name: 'layout',
							title: __( 'Layout', 'wp-dsgn-blocks' ),
							className: 'wpdsgn-tab-layout'
						},
						{
							name: 'style',
							title: __( 'Style', 'wp-dsgn-blocks' ),
							className: 'wpdsgn-tab-style'
						},
						{
							name: 'advanced',
							title: __( 'Advanced', 'wp-dsgn-blocks' ),
							className: 'wpdsgn-tab-advanced'
						}
					] }
				>
					{ ( tab ) => (
						<div className={ `wpdsgn-tab-content wpdsgn-tab-content-${tab.name}` }>
							{ tab.name === 'layout' && (
								<>
									{/* Column Configuration */}
									<PanelBody
										title={ __( 'Column Configuration', 'wp-dsgn-blocks' ) }
										initialOpen={ true }
									>
										<RangeControl
											label={ __( 'Number of Columns', 'wp-dsgn-blocks' ) }
											value={ columnCount }
											onChange={ ( value ) => setAttributes( { columnCount: value } ) }
											min={ 1 }
											max={ 6 }
											step={ 1 }
										/>

										<SelectControl
											label={ __( 'Preset Layout', 'wp-dsgn-blocks' ) }
											value={ presetLayout }
											options={ Object.keys( presetLayouts ).map( key => ( {
												label: presetLayouts[ key ].label,
												value: key
											} ) ) }
											onChange={ ( value ) => {
												setAttributes( { presetLayout: value } );
												if ( value !== 'custom' && presetLayouts[ value ] ) {
													setAttributes( { customWidths: presetLayouts[ value ].widths } );
												}
											} }
										/>

										{ presetLayout === 'custom' && (
											<div className="wpdsgn-custom-widths">
												<p>{ __( 'Custom Column Widths', 'wp-dsgn-blocks' ) }</p>
												{ Array.from( { length: columnCount } ).map( ( _, index ) => (
													<UnitControl
														key={ index }
														label={ __( `Column ${index + 1} Width`, 'wp-dsgn-blocks' ) }
														value={ customWidths[ index ] || '' }
														onChange={ ( value ) => {
															const newWidths = [ ...customWidths ];
															newWidths[ index ] = value;
															setAttributes( { customWidths: newWidths } );
														} }
														units={ [
															{ value: '%', label: '%' },
															{ value: 'px', label: 'px' },
															{ value: 'fr', label: 'fr' }
														] }
													/>
												) ) }
											</div>
										) }
									</PanelBody>

									{/* Alignment & Spacing */}
									<PanelBody
										title={ __( 'Alignment & Spacing', 'wp-dsgn-blocks' ) }
										initialOpen={ false }
									>
										<TabPanel
											className="wpdsgn-responsive-tabs"
											activeClass="is-active"
											tabs={ responsiveTabs }
										>
											{ ( deviceTab ) => (
												<div className="wpdsgn-alignment-controls">
													{/* Vertical Alignment */}
													<div className="wpdsgn-control-group">
														<label className="wpdsgn-control-label">
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
																	isPressed={ getResponsiveValue( verticalAlignment, deviceTab.name ) === option.value }
																	onClick={ () => setResponsiveValue( 'verticalAlignment', deviceTab.name, option.value ) }
																>
																	{ option.label }
																</Button>
															) ) }
														</ButtonGroup>
													</div>

													{/* Horizontal Alignment */}
													<div className="wpdsgn-control-group">
														<label className="wpdsgn-control-label">
															{ __( 'Horizontal Alignment', 'wp-dsgn-blocks' ) }
														</label>
														<ButtonGroup>
															{ [
																{ value: 'flex-start', label: __( 'Left', 'wp-dsgn-blocks' ) },
																{ value: 'center', label: __( 'Center', 'wp-dsgn-blocks' ) },
																{ value: 'flex-end', label: __( 'Right', 'wp-dsgn-blocks' ) },
																{ value: 'space-between', label: __( 'Between', 'wp-dsgn-blocks' ) },
																{ value: 'space-around', label: __( 'Around', 'wp-dsgn-blocks' ) }
															].map( ( option ) => (
																<Button
																	key={ option.value }
																	isPressed={ getResponsiveValue( horizontalAlignment, deviceTab.name ) === option.value }
																	onClick={ () => setResponsiveValue( 'horizontalAlignment', deviceTab.name, option.value ) }
																>
																	{ option.label }
																</Button>
															) ) }
														</ButtonGroup>
													</div>

													{/* Gap Controls */}
													<div className="wpdsgn-control-group">
														<label className="wpdsgn-control-label">
															{ __( 'Gap', 'wp-dsgn-blocks' ) }
														</label>
														<div className="wpdsgn-gap-controls">
															<UnitControl
																label={ __( 'Column Gap', 'wp-dsgn-blocks' ) }
																value={ getResponsiveValue( columnGap, deviceTab.name ) || '20px' }
																onChange={ ( value ) => setResponsiveValue( 'columnGap', deviceTab.name, value ) }
																units={ [
																	{ value: 'px', label: 'px' },
																	{ value: 'em', label: 'em' },
																	{ value: 'rem', label: 'rem' }
																] }
															/>
															<UnitControl
																label={ __( 'Row Gap', 'wp-dsgn-blocks' ) }
																value={ getResponsiveValue( rowGap, deviceTab.name ) || '20px' }
																onChange={ ( value ) => setResponsiveValue( 'rowGap', deviceTab.name, value ) }
																units={ [
																	{ value: 'px', label: 'px' },
																	{ value: 'em', label: 'em' },
																	{ value: 'rem', label: 'rem' }
																] }
															/>
														</div>
													</div>
												</div>
											) }
										</TabPanel>
									</PanelBody>

									{/* Responsive Behavior */}
									<PanelBody
										title={ __( 'Responsive Behavior', 'wp-dsgn-blocks' ) }
										initialOpen={ false }
									>
										<ToggleControl
											label={ __( 'Stack on Mobile', 'wp-dsgn-blocks' ) }
											checked={ stackOnMobile }
											onChange={ ( value ) => setAttributes( { stackOnMobile: value } ) }
											help={ __( 'Stack columns vertically on mobile devices.', 'wp-dsgn-blocks' ) }
										/>

										{ stackOnMobile && (
											<>
												<UnitControl
													label={ __( 'Mobile Breakpoint', 'wp-dsgn-blocks' ) }
													value={ stackBreakpoint.mobile }
													onChange={ ( value ) => {
														setAttributes( {
															stackBreakpoint: {
																...stackBreakpoint,
																mobile: value
															}
														} );
													} }
													units={ [
														{ value: 'px', label: 'px' }
													] }
												/>

												<SelectControl
													label={ __( 'Stack Direction', 'wp-dsgn-blocks' ) }
													value={ stackDirection }
													options={ [
														{ label: __( 'Normal (First to Last)', 'wp-dsgn-blocks' ), value: 'normal' },
														{ label: __( 'Reverse (Last to First)', 'wp-dsgn-blocks' ), value: 'reverse' }
													] }
													onChange={ ( value ) => setAttributes( { stackDirection: value } ) }
												/>
											</>
										) }

										<TabPanel
											className="wpdsgn-responsive-tabs"
											activeClass="is-active"
											tabs={ responsiveTabs }
										>
											{ ( deviceTab ) => (
												<ToggleControl
													label={ __( `Reverse Columns (${deviceTab.title})`, 'wp-dsgn-blocks' ) }
													checked={ getResponsiveValue( reverseColumns, deviceTab.name ) }
													onChange={ ( value ) => setResponsiveValue( 'reverseColumns', deviceTab.name, value ) }
												/>
											) }
										</TabPanel>
									</PanelBody>
								</>
							) }

							{ tab.name === 'style' && (
								<>
									{/* Individual Column Settings */}
									<PanelBody
										title={ __( 'Individual Column Settings', 'wp-dsgn-blocks' ) }
										initialOpen={ true }
									>
										{ Array.from( { length: columnCount } ).map( ( _, index ) => (
											<PanelBody
												key={ index }
												title={ __( `Column ${index + 1}`, 'wp-dsgn-blocks' ) }
												initialOpen={ false }
											>
												<UnitControl
													label={ __( 'Custom Width', 'wp-dsgn-blocks' ) }
													value={ columnSettings[ index ]?.width || '' }
													onChange={ ( value ) => {
														const newSettings = [ ...columnSettings ];
														newSettings[ index ] = {
															...( newSettings[ index ] || {} ),
															width: value
														};
														setAttributes( { columnSettings: newSettings } );
													} }
													units={ [
														{ value: '%', label: '%' },
														{ value: 'px', label: 'px' },
														{ value: 'fr', label: 'fr' }
													] }
												/>

												<ColorPicker
													color={ columnSettings[ index ]?.backgroundColor || '' }
													onChange={ ( value ) => {
														const newSettings = [ ...columnSettings ];
														newSettings[ index ] = {
															...( newSettings[ index ] || {} ),
															backgroundColor: value
														};
														setAttributes( { columnSettings: newSettings } );
													} }
													enableAlpha
												/>

												<BoxControl
													label={ __( 'Padding', 'wp-dsgn-blocks' ) }
													values={ columnSettings[ index ]?.padding || {} }
													onChange={ ( value ) => {
														const newSettings = [ ...columnSettings ];
														newSettings[ index ] = {
															...( newSettings[ index ] || {} ),
															padding: value
														};
														setAttributes( { columnSettings: newSettings } );
													} }
													units={ [
														{ value: 'px', label: 'px' },
														{ value: 'em', label: 'em' },
														{ value: 'rem', label: 'rem' },
														{ value: '%', label: '%' }
													] }
												/>

												<RangeControl
													label={ __( 'Order', 'wp-dsgn-blocks' ) }
													value={ columnSettings[ index ]?.order || index + 1 }
													onChange={ ( value ) => {
														const newSettings = [ ...columnSettings ];
														newSettings[ index ] = {
															...( newSettings[ index ] || {} ),
															order: value
														};
														setAttributes( { columnSettings: newSettings } );
													} }
													min={ 1 }
													max={ columnCount }
												/>

												<SelectControl
													label={ __( 'Vertical Self-Alignment', 'wp-dsgn-blocks' ) }
													value={ columnSettings[ index ]?.verticalAlign || 'auto' }
													options={ [
														{ label: __( 'Auto', 'wp-dsgn-blocks' ), value: 'auto' },
														{ label: __( 'Start', 'wp-dsgn-blocks' ), value: 'flex-start' },
														{ label: __( 'Center', 'wp-dsgn-blocks' ), value: 'center' },
														{ label: __( 'End', 'wp-dsgn-blocks' ), value: 'flex-end' },
														{ label: __( 'Stretch', 'wp-dsgn-blocks' ), value: 'stretch' }
													] }
													onChange={ ( value ) => {
														const newSettings = [ ...columnSettings ];
														newSettings[ index ] = {
															...( newSettings[ index ] || {} ),
															verticalAlign: value
														};
														setAttributes( { columnSettings: newSettings } );
													} }
												/>

												<TextControl
													label={ __( 'Custom CSS Class', 'wp-dsgn-blocks' ) }
													value={ columnSettings[ index ]?.customClass || '' }
													onChange={ ( value ) => {
														const newSettings = [ ...columnSettings ];
														newSettings[ index ] = {
															...( newSettings[ index ] || {} ),
															customClass: value
														};
														setAttributes( { columnSettings: newSettings } );
													} }
												/>
											</PanelBody>
										) ) }
									</PanelBody>
								</>
							) }

							{ tab.name === 'advanced' && (
								<>
									{/* Advanced Settings */}
									<PanelBody
										title={ __( 'Advanced Settings', 'wp-dsgn-blocks' ) }
										initialOpen={ true }
									>
										<ToggleControl
											label={ __( 'Equal Height Columns', 'wp-dsgn-blocks' ) }
											checked={ equalHeight }
											onChange={ ( value ) => setAttributes( { equalHeight: value } ) }
											help={ __( 'Force all columns to have the same height.', 'wp-dsgn-blocks' ) }
										/>

										<TextControl
											label={ __( 'Custom CSS Class', 'wp-dsgn-blocks' ) }
											value={ customClass }
											onChange={ ( value ) => setAttributes( { customClass: value } ) }
											help={ __( 'Add custom CSS classes separated by spaces.', 'wp-dsgn-blocks' ) }
										/>

										<TextControl
											label={ __( 'Custom ID', 'wp-dsgn-blocks' ) }
											value={ customId }
											onChange={ ( value ) => setAttributes( { customId: value } ) }
											help={ __( 'Add a custom ID for this columns block.', 'wp-dsgn-blocks' ) }
										/>
									</PanelBody>

									{/* Responsive Visibility */}
									<PanelBody
										title={ __( 'Responsive Visibility', 'wp-dsgn-blocks' ) }
										initialOpen={ false }
									>
										<ToggleControl
											label={ __( 'Hide on Desktop', 'wp-dsgn-blocks' ) }
											checked={ hideOnDevices.desktop }
											onChange={ ( value ) => {
												setAttributes( {
													hideOnDevices: {
														...hideOnDevices,
														desktop: value
													}
												} );
											} }
										/>

										<ToggleControl
											label={ __( 'Hide on Tablet', 'wp-dsgn-blocks' ) }
											checked={ hideOnDevices.tablet }
											onChange={ ( value ) => {
												setAttributes( {
													hideOnDevices: {
														...hideOnDevices,
														tablet: value
													}
												} );
											} }
										/>

										<ToggleControl
											label={ __( 'Hide on Mobile', 'wp-dsgn-blocks' ) }
											checked={ hideOnDevices.mobile }
											onChange={ ( value ) => {
												setAttributes( {
													hideOnDevices: {
														...hideOnDevices,
														mobile: value
													}
												} );
											} }
										/>
									</PanelBody>
								</>
							) }
						</div>
					) }
				</TabPanel>
			</InspectorControls>

			<div { ...blockProps }>
				<InnerBlocks
					template={ getColumnTemplate() }
					templateLock="all"
					allowedBlocks={ [ 'core/column' ] }
				/>
			</div>

			{/* Inject CSS for live preview */}
			<style>{ columnsCSS }</style>
		</>
	);
}

