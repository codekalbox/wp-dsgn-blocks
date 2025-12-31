/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	InnerBlocks,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	RangeControl,
	Button,
	ToggleControl,
	ColorPicker,
	__experimentalUnitControl as UnitControl,
	__experimentalBoxControl as BoxControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { generateSectionStyles } from '../utils/styles';

/**
 * Edit component for Section block.
 *
 * @param {Object}   props               Block properties.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @return {Element} Section block edit component.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		htmlTag,
		contentWidth,
		maxWidth,
		minHeight,
		minHeightValue,
		flexDirection,
		justifyContent,
		alignItems,
		flexWrap,
		gap,
		paddingTop,
		paddingRight,
		paddingBottom,
		paddingLeft,
		paddingLinked,
		marginTop,
		marginRight,
		marginBottom,
		marginLeft,
		marginLinked,
		backgroundColor,
		backgroundOpacity,
		backgroundImage,
		backgroundPosition,
		backgroundSize,
		backgroundRepeat,
		backgroundGradient,
		borderTopWidth,
		borderRightWidth,
		borderBottomWidth,
		borderLeftWidth,
		borderStyle,
		borderColor,
		borderTopLeftRadius,
		borderTopRightRadius,
		borderBottomRightRadius,
		borderBottomLeftRadius,
		boxShadowX,
		boxShadowY,
		boxShadowBlur,
		boxShadowSpread,
		boxShadowColor,
		boxShadowEnabled,
		zIndex,
		overflow,
	} = attributes;

	const styles = generateSectionStyles( attributes );
	const blockProps = useBlockProps( {
		style: styles,
	} );

	// Helper function to update padding
	const updatePadding = ( side, value ) => {
		if ( paddingLinked ) {
			setAttributes( {
				paddingTop: value,
				paddingRight: value,
				paddingBottom: value,
				paddingLeft: value,
			} );
		} else {
			setAttributes( { [ `padding${ side }` ]: value } );
		}
	};

	// Helper function to update margin
	const updateMargin = ( side, value ) => {
		if ( marginLinked ) {
			setAttributes( {
				marginTop: value,
				marginRight: value,
				marginBottom: value,
				marginLeft: value,
			} );
		} else {
			setAttributes( { [ `margin${ side }` ]: value } );
		}
	};

	return (
		<>
			<InspectorControls>
				{/* Layout Panel */}
				<PanelBody
					title={ __( 'Layout', 'flexblocks-layout-builder' ) }
					initialOpen={ true }
				>
					<SelectControl
						label={ __( 'HTML Tag', 'flexblocks-layout-builder' ) }
						value={ htmlTag }
						options={ [
							{ label: __( 'Section', 'flexblocks-layout-builder' ), value: 'section' },
							{ label: __( 'Div', 'flexblocks-layout-builder' ), value: 'div' },
							{ label: __( 'Header', 'flexblocks-layout-builder' ), value: 'header' },
							{ label: __( 'Footer', 'flexblocks-layout-builder' ), value: 'footer' },
							{ label: __( 'Article', 'flexblocks-layout-builder' ), value: 'article' },
							{ label: __( 'Aside', 'flexblocks-layout-builder' ), value: 'aside' },
						] }
						onChange={ ( value ) => setAttributes( { htmlTag: value } ) }
					/>
					<SelectControl
						label={ __( 'Content Width', 'flexblocks-layout-builder' ) }
						value={ contentWidth }
						options={ [
							{ label: __( 'Full Width', 'flexblocks-layout-builder' ), value: 'full' },
							{ label: __( 'Boxed', 'flexblocks-layout-builder' ), value: 'boxed' },
						] }
						onChange={ ( value ) => setAttributes( { contentWidth: value } ) }
					/>
					{ contentWidth === 'boxed' && (
						<TextControl
							label={ __( 'Max Width', 'flexblocks-layout-builder' ) }
							value={ maxWidth }
							onChange={ ( value ) => setAttributes( { maxWidth: value } ) }
							help={ __( 'e.g., 1200px, 80%, 1140px', 'flexblocks-layout-builder' ) }
						/>
					) }
					<SelectControl
						label={ __( 'Minimum Height', 'flexblocks-layout-builder' ) }
						value={ minHeight }
						options={ [
							{ label: __( 'Auto', 'flexblocks-layout-builder' ), value: 'auto' },
							{ label: __( 'Custom', 'flexblocks-layout-builder' ), value: 'custom' },
						] }
						onChange={ ( value ) => setAttributes( { minHeight: value } ) }
					/>
					{ minHeight === 'custom' && (
						<TextControl
							label={ __( 'Min Height Value', 'flexblocks-layout-builder' ) }
							value={ minHeightValue }
							onChange={ ( value ) => setAttributes( { minHeightValue: value } ) }
							help={ __( 'e.g., 400px, 50vh, 100%', 'flexblocks-layout-builder' ) }
						/>
					) }
				</PanelBody>

				{/* Flexbox Panel */}
				<PanelBody
					title={ __( 'Flexbox', 'flexblocks-layout-builder' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Flex Direction', 'flexblocks-layout-builder' ) }
						value={ flexDirection }
						options={ [
							{ label: __( 'Row', 'flexblocks-layout-builder' ), value: 'row' },
							{ label: __( 'Row Reverse', 'flexblocks-layout-builder' ), value: 'row-reverse' },
							{ label: __( 'Column', 'flexblocks-layout-builder' ), value: 'column' },
							{ label: __( 'Column Reverse', 'flexblocks-layout-builder' ), value: 'column-reverse' },
						] }
						onChange={ ( value ) => setAttributes( { flexDirection: value } ) }
					/>
					<SelectControl
						label={ __( 'Justify Content', 'flexblocks-layout-builder' ) }
						value={ justifyContent }
						options={ [
							{ label: __( 'Flex Start', 'flexblocks-layout-builder' ), value: 'flex-start' },
							{ label: __( 'Flex End', 'flexblocks-layout-builder' ), value: 'flex-end' },
							{ label: __( 'Center', 'flexblocks-layout-builder' ), value: 'center' },
							{ label: __( 'Space Between', 'flexblocks-layout-builder' ), value: 'space-between' },
							{ label: __( 'Space Around', 'flexblocks-layout-builder' ), value: 'space-around' },
							{ label: __( 'Space Evenly', 'flexblocks-layout-builder' ), value: 'space-evenly' },
						] }
						onChange={ ( value ) => setAttributes( { justifyContent: value } ) }
					/>
					<SelectControl
						label={ __( 'Align Items', 'flexblocks-layout-builder' ) }
						value={ alignItems }
						options={ [
							{ label: __( 'Flex Start', 'flexblocks-layout-builder' ), value: 'flex-start' },
							{ label: __( 'Flex End', 'flexblocks-layout-builder' ), value: 'flex-end' },
							{ label: __( 'Center', 'flexblocks-layout-builder' ), value: 'center' },
							{ label: __( 'Stretch', 'flexblocks-layout-builder' ), value: 'stretch' },
							{ label: __( 'Baseline', 'flexblocks-layout-builder' ), value: 'baseline' },
						] }
						onChange={ ( value ) => setAttributes( { alignItems: value } ) }
					/>
					<SelectControl
						label={ __( 'Flex Wrap', 'flexblocks-layout-builder' ) }
						value={ flexWrap }
						options={ [
							{ label: __( 'No Wrap', 'flexblocks-layout-builder' ), value: 'nowrap' },
							{ label: __( 'Wrap', 'flexblocks-layout-builder' ), value: 'wrap' },
							{ label: __( 'Wrap Reverse', 'flexblocks-layout-builder' ), value: 'wrap-reverse' },
						] }
						onChange={ ( value ) => setAttributes( { flexWrap: value } ) }
					/>
					<TextControl
						label={ __( 'Gap', 'flexblocks-layout-builder' ) }
						value={ gap }
						onChange={ ( value ) => setAttributes( { gap: value } ) }
						help={ __( 'e.g., 20px, 1rem, 2em', 'flexblocks-layout-builder' ) }
					/>
				</PanelBody>

				{/* Spacing Panel */}
				<PanelBody
					title={ __( 'Spacing', 'flexblocks-layout-builder' ) }
					initialOpen={ false }
				>
					<div style={ { marginBottom: '16px' } }>
						<strong>{ __( 'Padding', 'flexblocks-layout-builder' ) }</strong>
						<ToggleControl
							label={ __( 'Link Values', 'flexblocks-layout-builder' ) }
							checked={ paddingLinked }
							onChange={ ( value ) => setAttributes( { paddingLinked: value } ) }
						/>
					</div>
					<TextControl
						label={ __( 'Top', 'flexblocks-layout-builder' ) }
						value={ paddingTop }
						onChange={ ( value ) => updatePadding( 'Top', value ) }
					/>
					{ ! paddingLinked && (
						<>
							<TextControl
								label={ __( 'Right', 'flexblocks-layout-builder' ) }
								value={ paddingRight }
								onChange={ ( value ) => updatePadding( 'Right', value ) }
							/>
							<TextControl
								label={ __( 'Bottom', 'flexblocks-layout-builder' ) }
								value={ paddingBottom }
								onChange={ ( value ) => updatePadding( 'Bottom', value ) }
							/>
							<TextControl
								label={ __( 'Left', 'flexblocks-layout-builder' ) }
								value={ paddingLeft }
								onChange={ ( value ) => updatePadding( 'Left', value ) }
							/>
						</>
					) }

					<hr style={ { margin: '20px 0' } } />

					<div style={ { marginBottom: '16px' } }>
						<strong>{ __( 'Margin', 'flexblocks-layout-builder' ) }</strong>
						<ToggleControl
							label={ __( 'Link Values', 'flexblocks-layout-builder' ) }
							checked={ marginLinked }
							onChange={ ( value ) => setAttributes( { marginLinked: value } ) }
						/>
					</div>
					<TextControl
						label={ __( 'Top', 'flexblocks-layout-builder' ) }
						value={ marginTop }
						onChange={ ( value ) => updateMargin( 'Top', value ) }
					/>
					{ ! marginLinked && (
						<>
							<TextControl
								label={ __( 'Right', 'flexblocks-layout-builder' ) }
								value={ marginRight }
								onChange={ ( value ) => updateMargin( 'Right', value ) }
							/>
							<TextControl
								label={ __( 'Bottom', 'flexblocks-layout-builder' ) }
								value={ marginBottom }
								onChange={ ( value ) => updateMargin( 'Bottom', value ) }
							/>
							<TextControl
								label={ __( 'Left', 'flexblocks-layout-builder' ) }
								value={ marginLeft }
								onChange={ ( value ) => updateMargin( 'Left', value ) }
							/>
						</>
					) }
				</PanelBody>

				{/* Background Panel */}
				<PanelBody
					title={ __( 'Background', 'flexblocks-layout-builder' ) }
					initialOpen={ false }
				>
					<div style={ { marginBottom: '16px' } }>
						<strong>{ __( 'Background Color', 'flexblocks-layout-builder' ) }</strong>
						<ColorPicker
							color={ backgroundColor }
							onChangeComplete={ ( value ) =>
								setAttributes( { backgroundColor: value.hex } )
							}
							disableAlpha={ false }
						/>
					</div>
					<RangeControl
						label={ __( 'Opacity', 'flexblocks-layout-builder' ) }
						value={ backgroundOpacity }
						onChange={ ( value ) => setAttributes( { backgroundOpacity: value } ) }
						min={ 0 }
						max={ 1 }
						step={ 0.01 }
					/>

					<hr style={ { margin: '20px 0' } } />

					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									backgroundImage: {
										url: media.url,
										id: media.id,
									},
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ backgroundImage?.id }
							render={ ( { open } ) => (
								<>
									<Button variant="secondary" onClick={ open }>
										{ backgroundImage
											? __( 'Change Image', 'flexblocks-layout-builder' )
											: __( 'Select Image', 'flexblocks-layout-builder' ) }
									</Button>
									{ backgroundImage && (
										<Button
											variant="tertiary"
											isDestructive
											onClick={ () =>
												setAttributes( { backgroundImage: null } )
											}
											style={ { marginLeft: '8px' } }
										>
											{ __( 'Remove', 'flexblocks-layout-builder' ) }
										</Button>
									) }
								</>
							) }
						/>
					</MediaUploadCheck>

					{ backgroundImage && (
						<>
							<SelectControl
								label={ __( 'Position', 'flexblocks-layout-builder' ) }
								value={ backgroundPosition }
								options={ [
									{ label: __( 'Center Center', 'flexblocks-layout-builder' ), value: 'center center' },
									{ label: __( 'Top Left', 'flexblocks-layout-builder' ), value: 'top left' },
									{ label: __( 'Top Center', 'flexblocks-layout-builder' ), value: 'top center' },
									{ label: __( 'Top Right', 'flexblocks-layout-builder' ), value: 'top right' },
									{ label: __( 'Center Left', 'flexblocks-layout-builder' ), value: 'center left' },
									{ label: __( 'Center Right', 'flexblocks-layout-builder' ), value: 'center right' },
									{ label: __( 'Bottom Left', 'flexblocks-layout-builder' ), value: 'bottom left' },
									{ label: __( 'Bottom Center', 'flexblocks-layout-builder' ), value: 'bottom center' },
									{ label: __( 'Bottom Right', 'flexblocks-layout-builder' ), value: 'bottom right' },
								] }
								onChange={ ( value ) => setAttributes( { backgroundPosition: value } ) }
							/>
							<SelectControl
								label={ __( 'Size', 'flexblocks-layout-builder' ) }
								value={ backgroundSize }
								options={ [
									{ label: __( 'Cover', 'flexblocks-layout-builder' ), value: 'cover' },
									{ label: __( 'Contain', 'flexblocks-layout-builder' ), value: 'contain' },
									{ label: __( 'Auto', 'flexblocks-layout-builder' ), value: 'auto' },
								] }
								onChange={ ( value ) => setAttributes( { backgroundSize: value } ) }
							/>
							<SelectControl
								label={ __( 'Repeat', 'flexblocks-layout-builder' ) }
								value={ backgroundRepeat }
								options={ [
									{ label: __( 'No Repeat', 'flexblocks-layout-builder' ), value: 'no-repeat' },
									{ label: __( 'Repeat', 'flexblocks-layout-builder' ), value: 'repeat' },
									{ label: __( 'Repeat X', 'flexblocks-layout-builder' ), value: 'repeat-x' },
									{ label: __( 'Repeat Y', 'flexblocks-layout-builder' ), value: 'repeat-y' },
								] }
								onChange={ ( value ) => setAttributes( { backgroundRepeat: value } ) }
							/>
						</>
					) }

					<hr style={ { margin: '20px 0' } } />

					<TextControl
						label={ __( 'Gradient', 'flexblocks-layout-builder' ) }
						value={ backgroundGradient }
						onChange={ ( value ) => setAttributes( { backgroundGradient: value } ) }
						help={ __( 'e.g., linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'flexblocks-layout-builder' ) }
					/>
				</PanelBody>

				{/* Border & Effects Panel */}
				<PanelBody
					title={ __( 'Border & Effects', 'flexblocks-layout-builder' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Border Style', 'flexblocks-layout-builder' ) }
						value={ borderStyle }
						options={ [
							{ label: __( 'Solid', 'flexblocks-layout-builder' ), value: 'solid' },
							{ label: __( 'Dashed', 'flexblocks-layout-builder' ), value: 'dashed' },
							{ label: __( 'Dotted', 'flexblocks-layout-builder' ), value: 'dotted' },
							{ label: __( 'Double', 'flexblocks-layout-builder' ), value: 'double' },
							{ label: __( 'None', 'flexblocks-layout-builder' ), value: 'none' },
						] }
						onChange={ ( value ) => setAttributes( { borderStyle: value } ) }
					/>
					<TextControl
						label={ __( 'Border Width (Top)', 'flexblocks-layout-builder' ) }
						value={ borderTopWidth }
						onChange={ ( value ) => setAttributes( { borderTopWidth: value } ) }
					/>
					<TextControl
						label={ __( 'Border Width (Right)', 'flexblocks-layout-builder' ) }
						value={ borderRightWidth }
						onChange={ ( value ) => setAttributes( { borderRightWidth: value } ) }
					/>
					<TextControl
						label={ __( 'Border Width (Bottom)', 'flexblocks-layout-builder' ) }
						value={ borderBottomWidth }
						onChange={ ( value ) => setAttributes( { borderBottomWidth: value } ) }
					/>
					<TextControl
						label={ __( 'Border Width (Left)', 'flexblocks-layout-builder' ) }
						value={ borderLeftWidth }
						onChange={ ( value ) => setAttributes( { borderLeftWidth: value } ) }
					/>
					<div style={ { marginTop: '16px' } }>
						<strong>{ __( 'Border Color', 'flexblocks-layout-builder' ) }</strong>
						<ColorPicker
							color={ borderColor }
							onChangeComplete={ ( value ) =>
								setAttributes( { borderColor: value.hex } )
							}
						/>
					</div>

					<hr style={ { margin: '20px 0' } } />

					<TextControl
						label={ __( 'Border Radius (Top Left)', 'flexblocks-layout-builder' ) }
						value={ borderTopLeftRadius }
						onChange={ ( value ) => setAttributes( { borderTopLeftRadius: value } ) }
					/>
					<TextControl
						label={ __( 'Border Radius (Top Right)', 'flexblocks-layout-builder' ) }
						value={ borderTopRightRadius }
						onChange={ ( value ) => setAttributes( { borderTopRightRadius: value } ) }
					/>
					<TextControl
						label={ __( 'Border Radius (Bottom Right)', 'flexblocks-layout-builder' ) }
						value={ borderBottomRightRadius }
						onChange={ ( value ) => setAttributes( { borderBottomRightRadius: value } ) }
					/>
					<TextControl
						label={ __( 'Border Radius (Bottom Left)', 'flexblocks-layout-builder' ) }
						value={ borderBottomLeftRadius }
						onChange={ ( value ) => setAttributes( { borderBottomLeftRadius: value } ) }
					/>

					<hr style={ { margin: '20px 0' } } />

					<ToggleControl
						label={ __( 'Enable Box Shadow', 'flexblocks-layout-builder' ) }
						checked={ boxShadowEnabled }
						onChange={ ( value ) => setAttributes( { boxShadowEnabled: value } ) }
					/>
					{ boxShadowEnabled && (
						<>
							<TextControl
								label={ __( 'X Offset', 'flexblocks-layout-builder' ) }
								value={ boxShadowX }
								onChange={ ( value ) => setAttributes( { boxShadowX: value } ) }
							/>
							<TextControl
								label={ __( 'Y Offset', 'flexblocks-layout-builder' ) }
								value={ boxShadowY }
								onChange={ ( value ) => setAttributes( { boxShadowY: value } ) }
							/>
							<TextControl
								label={ __( 'Blur', 'flexblocks-layout-builder' ) }
								value={ boxShadowBlur }
								onChange={ ( value ) => setAttributes( { boxShadowBlur: value } ) }
							/>
							<TextControl
								label={ __( 'Spread', 'flexblocks-layout-builder' ) }
								value={ boxShadowSpread }
								onChange={ ( value ) => setAttributes( { boxShadowSpread: value } ) }
							/>
							<div style={ { marginTop: '16px' } }>
								<strong>{ __( 'Shadow Color', 'flexblocks-layout-builder' ) }</strong>
								<ColorPicker
									color={ boxShadowColor }
									onChangeComplete={ ( value ) =>
										setAttributes( { boxShadowColor: value.hex } )
									}
								/>
							</div>
						</>
					) }
				</PanelBody>

				{/* Advanced Panel */}
				<PanelBody
					title={ __( 'Advanced', 'flexblocks-layout-builder' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'Z-Index', 'flexblocks-layout-builder' ) }
						value={ zIndex }
						onChange={ ( value ) => setAttributes( { zIndex: value } ) }
						type="number"
					/>
					<SelectControl
						label={ __( 'Overflow', 'flexblocks-layout-builder' ) }
						value={ overflow }
						options={ [
							{ label: __( 'Visible', 'flexblocks-layout-builder' ), value: 'visible' },
							{ label: __( 'Hidden', 'flexblocks-layout-builder' ), value: 'hidden' },
							{ label: __( 'Scroll', 'flexblocks-layout-builder' ), value: 'scroll' },
							{ label: __( 'Auto', 'flexblocks-layout-builder' ), value: 'auto' },
						] }
						onChange={ ( value ) => setAttributes( { overflow: value } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="flexblocks-section-inner">
					<InnerBlocks />
				</div>
			</div>
		</>
	);
}

