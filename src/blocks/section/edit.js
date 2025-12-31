/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { 
	useBlockProps, 
	InnerBlocks,
	InspectorControls,
	BlockControls
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
	ColorPicker,
	GradientPicker,
	MediaUpload,
	MediaUploadCheck,
	__experimentalBorderControl as BorderControl
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { generateSectionCSS } from './utils/generateCSS';
import './editor.scss';

/**
 * Edit component for Section block.
 */
export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		uniqueId,
		contentWidth,
		maxWidth,
		minHeight,
		htmlTag,
		overflow,
		flexDirection,
		justifyContent,
		alignItems,
		flexWrap,
		gap,
		padding,
		margin,
		backgroundColor,
		backgroundOpacity,
		backgroundGradient,
		backgroundImage,
		backgroundOverlay,
		border,
		boxShadow,
		customClass,
		customId,
		zIndex,
		cssFilters,
		transform,
		transitionDuration,
		hideOnDevices
	} = attributes;

	// Set unique ID on first load
	useEffect( () => {
		if ( ! uniqueId ) {
			setAttributes( { uniqueId: clientId } );
		}
	}, [ clientId, uniqueId, setAttributes ] );

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

	// Generate CSS for live preview
	const sectionCSS = generateSectionCSS( attributes, uniqueId || clientId );

	// Block props with dynamic classes and styles
	const blockProps = useBlockProps( {
		className: `wpdsgn-section wpdsgn-section-${uniqueId || clientId} ${customClass}`,
		id: customId || undefined,
		style: {
			display: 'flex',
			flexDirection: getResponsiveValue( flexDirection ),
			justifyContent: getResponsiveValue( justifyContent ),
			alignItems: getResponsiveValue( alignItems ),
			flexWrap: getResponsiveValue( flexWrap ),
			gap: `${getResponsiveValue( gap )?.row || '20px'} ${getResponsiveValue( gap )?.column || '20px'}`,
			padding: `${getResponsiveValue( padding )?.top || '20px'} ${getResponsiveValue( padding )?.right || '20px'} ${getResponsiveValue( padding )?.bottom || '20px'} ${getResponsiveValue( padding )?.left || '20px'}`,
			margin: `${getResponsiveValue( margin )?.top || '0px'} ${getResponsiveValue( margin )?.right || '0px'} ${getResponsiveValue( margin )?.bottom || '0px'} ${getResponsiveValue( margin )?.left || '0px'}`,
			backgroundColor: backgroundColor || 'transparent',
			backgroundImage: backgroundImage?.url ? `url(${backgroundImage.url})` : 'none',
			backgroundPosition: backgroundImage?.position || 'center center',
			backgroundSize: backgroundImage?.size || 'cover',
			backgroundRepeat: backgroundImage?.repeat || 'no-repeat',
			backgroundAttachment: backgroundImage?.attachment || 'scroll',
			minHeight: getResponsiveValue( minHeight ) || 'auto',
			maxWidth: contentWidth === 'boxed' ? maxWidth : '100%',
			width: '100%',
			overflow: overflow || 'visible',
			zIndex: zIndex || 'auto'
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
									{/* Layout Settings */}
									<PanelBody
										title={ __( 'Layout Settings', 'wp-dsgn-blocks' ) }
										initialOpen={ true }
									>
										<SelectControl
											label={ __( 'Content Width', 'wp-dsgn-blocks' ) }
											value={ contentWidth }
											options={ [
												{ label: __( 'Full Width', 'wp-dsgn-blocks' ), value: 'full' },
												{ label: __( 'Boxed', 'wp-dsgn-blocks' ), value: 'boxed' }
											] }
											onChange={ ( value ) => setAttributes( { contentWidth: value } ) }
										/>

										{ contentWidth === 'boxed' && (
											<UnitControl
												label={ __( 'Max Width', 'wp-dsgn-blocks' ) }
												value={ maxWidth }
												onChange={ ( value ) => setAttributes( { maxWidth: value } ) }
												units={ [
													{ value: 'px', label: 'px' },
													{ value: '%', label: '%' },
													{ value: 'vw', label: 'vw' }
												] }
											/>
										) }

										<SelectControl
											label={ __( 'HTML Tag', 'wp-dsgn-blocks' ) }
											value={ htmlTag }
											options={ [
												{ label: 'section', value: 'section' },
												{ label: 'div', value: 'div' },
												{ label: 'header', value: 'header' },
												{ label: 'footer', value: 'footer' },
												{ label: 'article', value: 'article' },
												{ label: 'aside', value: 'aside' },
												{ label: 'main', value: 'main' }
											] }
											onChange={ ( value ) => setAttributes( { htmlTag: value } ) }
										/>

										<SelectControl
											label={ __( 'Overflow', 'wp-dsgn-blocks' ) }
											value={ overflow }
											options={ [
												{ label: __( 'Visible', 'wp-dsgn-blocks' ), value: 'visible' },
												{ label: __( 'Hidden', 'wp-dsgn-blocks' ), value: 'hidden' },
												{ label: __( 'Scroll', 'wp-dsgn-blocks' ), value: 'scroll' },
												{ label: __( 'Auto', 'wp-dsgn-blocks' ), value: 'auto' }
											] }
											onChange={ ( value ) => setAttributes( { overflow: value } ) }
										/>
									</PanelBody>

									{/* Responsive Min Height */}
									<PanelBody
										title={ __( 'Minimum Height', 'wp-dsgn-blocks' ) }
										initialOpen={ false }
									>
										<TabPanel
											className="wpdsgn-responsive-tabs"
											activeClass="is-active"
											tabs={ responsiveTabs }
										>
											{ ( deviceTab ) => (
												<UnitControl
													label={ __( `Min Height (${deviceTab.title})`, 'wp-dsgn-blocks' ) }
													value={ getResponsiveValue( minHeight, deviceTab.name ) }
													onChange={ ( value ) => setResponsiveValue( 'minHeight', deviceTab.name, value ) }
													units={ [
														{ value: 'px', label: 'px' },
														{ value: 'vh', label: 'vh' },
														{ value: '%', label: '%' },
														{ value: 'auto', label: 'auto' }
													] }
												/>
											) }
										</TabPanel>
									</PanelBody>

									{/* Flexbox Controls */}
									<PanelBody
										title={ __( 'Flexbox Controls', 'wp-dsgn-blocks' ) }
										initialOpen={ false }
									>
										<TabPanel
											className="wpdsgn-responsive-tabs"
											activeClass="is-active"
											tabs={ responsiveTabs }
										>
											{ ( deviceTab ) => (
												<div className="wpdsgn-flexbox-controls">
													{/* Flex Direction */}
													<div className="wpdsgn-control-group">
														<label className="wpdsgn-control-label">
															{ __( 'Direction', 'wp-dsgn-blocks' ) }
														</label>
														<ButtonGroup>
															{ [
																{ value: 'row', label: __( 'Row', 'wp-dsgn-blocks' ) },
																{ value: 'row-reverse', label: __( 'Row ↺', 'wp-dsgn-blocks' ) },
																{ value: 'column', label: __( 'Column', 'wp-dsgn-blocks' ) },
																{ value: 'column-reverse', label: __( 'Column ↺', 'wp-dsgn-blocks' ) }
															].map( ( option ) => (
																<Button
																	key={ option.value }
																	isPressed={ getResponsiveValue( flexDirection, deviceTab.name ) === option.value }
																	onClick={ () => setResponsiveValue( 'flexDirection', deviceTab.name, option.value ) }
																>
																	{ option.label }
																</Button>
															) ) }
														</ButtonGroup>
													</div>

													{/* Justify Content */}
													<div className="wpdsgn-control-group">
														<label className="wpdsgn-control-label">
															{ __( 'Justify Content', 'wp-dsgn-blocks' ) }
														</label>
														<ButtonGroup>
															{ [
																{ value: 'flex-start', label: __( 'Start', 'wp-dsgn-blocks' ) },
																{ value: 'center', label: __( 'Center', 'wp-dsgn-blocks' ) },
																{ value: 'flex-end', label: __( 'End', 'wp-dsgn-blocks' ) },
																{ value: 'space-between', label: __( 'Between', 'wp-dsgn-blocks' ) },
																{ value: 'space-around', label: __( 'Around', 'wp-dsgn-blocks' ) },
																{ value: 'space-evenly', label: __( 'Evenly', 'wp-dsgn-blocks' ) }
															].map( ( option ) => (
																<Button
																	key={ option.value }
																	isPressed={ getResponsiveValue( justifyContent, deviceTab.name ) === option.value }
																	onClick={ () => setResponsiveValue( 'justifyContent', deviceTab.name, option.value ) }
																>
																	{ option.label }
																</Button>
															) ) }
														</ButtonGroup>
													</div>

													{/* Align Items */}
													<div className="wpdsgn-control-group">
														<label className="wpdsgn-control-label">
															{ __( 'Align Items', 'wp-dsgn-blocks' ) }
														</label>
														<ButtonGroup>
															{ [
																{ value: 'flex-start', label: __( 'Start', 'wp-dsgn-blocks' ) },
																{ value: 'center', label: __( 'Center', 'wp-dsgn-blocks' ) },
																{ value: 'flex-end', label: __( 'End', 'wp-dsgn-blocks' ) },
																{ value: 'stretch', label: __( 'Stretch', 'wp-dsgn-blocks' ) },
																{ value: 'baseline', label: __( 'Baseline', 'wp-dsgn-blocks' ) }
															].map( ( option ) => (
																<Button
																	key={ option.value }
																	isPressed={ getResponsiveValue( alignItems, deviceTab.name ) === option.value }
																	onClick={ () => setResponsiveValue( 'alignItems', deviceTab.name, option.value ) }
																>
																	{ option.label }
																</Button>
															) ) }
														</ButtonGroup>
													</div>

													{/* Flex Wrap */}
													<div className="wpdsgn-control-group">
														<label className="wpdsgn-control-label">
															{ __( 'Flex Wrap', 'wp-dsgn-blocks' ) }
														</label>
														<ButtonGroup>
															{ [
																{ value: 'nowrap', label: __( 'No Wrap', 'wp-dsgn-blocks' ) },
																{ value: 'wrap', label: __( 'Wrap', 'wp-dsgn-blocks' ) },
																{ value: 'wrap-reverse', label: __( 'Wrap ↺', 'wp-dsgn-blocks' ) }
															].map( ( option ) => (
																<Button
																	key={ option.value }
																	isPressed={ getResponsiveValue( flexWrap, deviceTab.name ) === option.value }
																	onClick={ () => setResponsiveValue( 'flexWrap', deviceTab.name, option.value ) }
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
																label={ __( 'Row Gap', 'wp-dsgn-blocks' ) }
																value={ getResponsiveValue( gap, deviceTab.name )?.row || '20px' }
																onChange={ ( value ) => {
																	const currentGap = getResponsiveValue( gap, deviceTab.name ) || {};
																	setResponsiveValue( 'gap', deviceTab.name, {
																		...currentGap,
																		row: value
																	} );
																} }
																units={ [
																	{ value: 'px', label: 'px' },
																	{ value: 'em', label: 'em' },
																	{ value: 'rem', label: 'rem' }
																] }
															/>
															<UnitControl
																label={ __( 'Column Gap', 'wp-dsgn-blocks' ) }
																value={ getResponsiveValue( gap, deviceTab.name )?.column || '20px' }
																onChange={ ( value ) => {
																	const currentGap = getResponsiveValue( gap, deviceTab.name ) || {};
																	setResponsiveValue( 'gap', deviceTab.name, {
																		...currentGap,
																		column: value
																	} );
																} }
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

									{/* Spacing Controls */}
									<PanelBody
										title={ __( 'Spacing', 'wp-dsgn-blocks' ) }
										initialOpen={ false }
									>
										<TabPanel
											className="wpdsgn-responsive-tabs"
											activeClass="is-active"
											tabs={ responsiveTabs }
										>
											{ ( deviceTab ) => (
												<div className="wpdsgn-spacing-controls">
													<BoxControl
														label={ __( 'Padding', 'wp-dsgn-blocks' ) }
														values={ getResponsiveValue( padding, deviceTab.name ) }
														onChange={ ( value ) => setResponsiveValue( 'padding', deviceTab.name, value ) }
														units={ [
															{ value: 'px', label: 'px' },
															{ value: 'em', label: 'em' },
															{ value: 'rem', label: 'rem' },
															{ value: '%', label: '%' }
														] }
													/>
													<BoxControl
														label={ __( 'Margin', 'wp-dsgn-blocks' ) }
														values={ getResponsiveValue( margin, deviceTab.name ) }
														onChange={ ( value ) => setResponsiveValue( 'margin', deviceTab.name, value ) }
														units={ [
															{ value: 'px', label: 'px' },
															{ value: 'em', label: 'em' },
															{ value: 'rem', label: 'rem' },
															{ value: '%', label: '%' },
															{ value: 'auto', label: 'auto' }
														] }
													/>
												</div>
											) }
										</TabPanel>
									</PanelBody>
								</>
							) }

							{ tab.name === 'style' && (
								<>
									{/* Background Settings */}
									<PanelBody
										title={ __( 'Background', 'wp-dsgn-blocks' ) }
										initialOpen={ true }
									>
										<TabPanel
											className="wpdsgn-background-tabs"
											activeClass="is-active"
											tabs={ [
												{
													name: 'color',
													title: __( 'Color', 'wp-dsgn-blocks' ),
													className: 'wpdsgn-bg-tab-color'
												},
												{
													name: 'image',
													title: __( 'Image', 'wp-dsgn-blocks' ),
													className: 'wpdsgn-bg-tab-image'
												},
												{
													name: 'overlay',
													title: __( 'Overlay', 'wp-dsgn-blocks' ),
													className: 'wpdsgn-bg-tab-overlay'
												}
											] }
										>
											{ ( bgTab ) => (
												<div className={ `wpdsgn-bg-content wpdsgn-bg-content-${bgTab.name}` }>
													{ bgTab.name === 'color' && (
														<>
															<ColorPicker
																color={ backgroundColor }
																onChange={ ( value ) => setAttributes( { backgroundColor: value } ) }
																enableAlpha
															/>
															<RangeControl
																label={ __( 'Opacity', 'wp-dsgn-blocks' ) }
																value={ backgroundOpacity }
																onChange={ ( value ) => setAttributes( { backgroundOpacity: value } ) }
																min={ 0 }
																max={ 1 }
																step={ 0.1 }
															/>
															<GradientPicker
																value={ backgroundGradient }
																onChange={ ( value ) => setAttributes( { backgroundGradient: value } ) }
															/>
														</>
													) }

													{ bgTab.name === 'image' && (
														<>
															<MediaUploadCheck>
																<MediaUpload
																	onSelect={ ( media ) => {
																		setAttributes( {
																			backgroundImage: {
																				...backgroundImage,
																				url: media.url,
																				id: media.id
																			}
																		} );
																	} }
																	allowedTypes={ [ 'image' ] }
																	value={ backgroundImage.id }
																	render={ ( { open } ) => (
																		<Button
																			onClick={ open }
																			variant="secondary"
																		>
																			{ backgroundImage.url ? __( 'Replace Image', 'wp-dsgn-blocks' ) : __( 'Select Image', 'wp-dsgn-blocks' ) }
																		</Button>
																	) }
																/>
															</MediaUploadCheck>

															{ backgroundImage.url && (
																<>
																	<SelectControl
																		label={ __( 'Position', 'wp-dsgn-blocks' ) }
																		value={ backgroundImage.position }
																		options={ [
																			{ label: __( 'Center Center', 'wp-dsgn-blocks' ), value: 'center center' },
																			{ label: __( 'Center Top', 'wp-dsgn-blocks' ), value: 'center top' },
																			{ label: __( 'Center Bottom', 'wp-dsgn-blocks' ), value: 'center bottom' },
																			{ label: __( 'Left Center', 'wp-dsgn-blocks' ), value: 'left center' },
																			{ label: __( 'Right Center', 'wp-dsgn-blocks' ), value: 'right center' }
																		] }
																		onChange={ ( value ) => {
																			setAttributes( {
																				backgroundImage: {
																					...backgroundImage,
																					position: value
																				}
																			} );
																		} }
																	/>

																	<SelectControl
																		label={ __( 'Size', 'wp-dsgn-blocks' ) }
																		value={ backgroundImage.size }
																		options={ [
																			{ label: __( 'Cover', 'wp-dsgn-blocks' ), value: 'cover' },
																			{ label: __( 'Contain', 'wp-dsgn-blocks' ), value: 'contain' },
																			{ label: __( 'Auto', 'wp-dsgn-blocks' ), value: 'auto' }
																		] }
																		onChange={ ( value ) => {
																			setAttributes( {
																				backgroundImage: {
																					...backgroundImage,
																					size: value
																				}
																			} );
																		} }
																	/>

																	<SelectControl
																		label={ __( 'Repeat', 'wp-dsgn-blocks' ) }
																		value={ backgroundImage.repeat }
																		options={ [
																			{ label: __( 'No Repeat', 'wp-dsgn-blocks' ), value: 'no-repeat' },
																			{ label: __( 'Repeat', 'wp-dsgn-blocks' ), value: 'repeat' },
																			{ label: __( 'Repeat X', 'wp-dsgn-blocks' ), value: 'repeat-x' },
																			{ label: __( 'Repeat Y', 'wp-dsgn-blocks' ), value: 'repeat-y' }
																		] }
																		onChange={ ( value ) => {
																			setAttributes( {
																				backgroundImage: {
																					...backgroundImage,
																					repeat: value
																				}
																			} );
																		} }
																	/>

																	<RangeControl
																		label={ __( 'Image Opacity', 'wp-dsgn-blocks' ) }
																		value={ backgroundImage.opacity }
																		onChange={ ( value ) => {
																			setAttributes( {
																				backgroundImage: {
																					...backgroundImage,
																					opacity: value
																				}
																			} );
																		} }
																		min={ 0 }
																		max={ 1 }
																		step={ 0.1 }
																	/>
																</>
															) }
														</>
													) }

													{ bgTab.name === 'overlay' && (
														<>
															<ColorPicker
																color={ backgroundOverlay.color }
																onChange={ ( value ) => {
																	setAttributes( {
																		backgroundOverlay: {
																			...backgroundOverlay,
																			color: value
																		}
																	} );
																} }
																enableAlpha
															/>
															<RangeControl
																label={ __( 'Overlay Opacity', 'wp-dsgn-blocks' ) }
																value={ backgroundOverlay.opacity }
																onChange={ ( value ) => {
																	setAttributes( {
																		backgroundOverlay: {
																			...backgroundOverlay,
																			opacity: value
																		}
																	} );
																} }
																min={ 0 }
																max={ 1 }
																step={ 0.1 }
															/>
															<SelectControl
																label={ __( 'Blend Mode', 'wp-dsgn-blocks' ) }
																value={ backgroundOverlay.blendMode }
																options={ [
																	{ label: __( 'Normal', 'wp-dsgn-blocks' ), value: 'normal' },
																	{ label: __( 'Multiply', 'wp-dsgn-blocks' ), value: 'multiply' },
																	{ label: __( 'Screen', 'wp-dsgn-blocks' ), value: 'screen' },
																	{ label: __( 'Overlay', 'wp-dsgn-blocks' ), value: 'overlay' },
																	{ label: __( 'Darken', 'wp-dsgn-blocks' ), value: 'darken' },
																	{ label: __( 'Lighten', 'wp-dsgn-blocks' ), value: 'lighten' }
																] }
																onChange={ ( value ) => {
																	setAttributes( {
																		backgroundOverlay: {
																			...backgroundOverlay,
																			blendMode: value
																		}
																	} );
																} }
															/>
														</>
													) }
												</div>
											) }
										</TabPanel>
									</PanelBody>

									{/* Border & Effects */}
									<PanelBody
										title={ __( 'Border & Effects', 'wp-dsgn-blocks' ) }
										initialOpen={ false }
									>
										<BorderControl
											label={ __( 'Border', 'wp-dsgn-blocks' ) }
											value={ border }
											onChange={ ( value ) => setAttributes( { border: value } ) }
										/>
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
											help={ __( 'Add a custom ID for this section.', 'wp-dsgn-blocks' ) }
										/>

										<RangeControl
											label={ __( 'Z-Index', 'wp-dsgn-blocks' ) }
											value={ zIndex }
											onChange={ ( value ) => setAttributes( { zIndex: value } ) }
											min={ -10 }
											max={ 100 }
											help={ __( 'Control the stacking order of this section.', 'wp-dsgn-blocks' ) }
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
				<InnerBlocks />
			</div>

			{/* Inject CSS for live preview */}
			<style>{ sectionCSS }</style>
		</>
	);
}

