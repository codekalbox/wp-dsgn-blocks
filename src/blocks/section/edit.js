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
	TextControl,
	RangeControl,
	ButtonGroup,
	Button,
	__experimentalUnitControl as UnitControl,
	__experimentalBoxControl as BoxControl,
	ColorPicker,
	MediaUpload,
	MediaUploadCheck
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
				{/* Layout Panel */}
				<PanelBody
					title={ __( 'Layout', 'wp-dsgn-blocks' ) }
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

					<UnitControl
						label={ __( 'Min Height', 'wp-dsgn-blocks' ) }
						value={ getResponsiveValue( minHeight, currentDevice ) }
						onChange={ ( value ) => setResponsiveValue( 'minHeight', currentDevice, value ) }
						units={ [
							{ value: 'px', label: 'px' },
							{ value: 'vh', label: 'vh' },
							{ value: '%', label: '%' },
							{ value: 'auto', label: 'auto' }
						] }
					/>

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
				</PanelBody>

				{/* Flexbox Panel */}
				<PanelBody
					title={ __( 'Flexbox', 'wp-dsgn-blocks' ) }
					initialOpen={ false }
				>
					<div style={{ marginBottom: '16px' }}>
						<label style={{ display: 'block', marginBottom: '8px', fontWeight: '500' }}>
							{ __( 'Direction', 'wp-dsgn-blocks' ) }
						</label>
						<ButtonGroup>
							{ [
								{ value: 'row', label: __( 'Row', 'wp-dsgn-blocks' ) },
								{ value: 'column', label: __( 'Column', 'wp-dsgn-blocks' ) }
							].map( ( option ) => (
								<Button
									key={ option.value }
									isPressed={ getResponsiveValue( flexDirection, currentDevice ) === option.value }
									onClick={ () => setResponsiveValue( 'flexDirection', currentDevice, option.value ) }
								>
									{ option.label }
								</Button>
							) ) }
						</ButtonGroup>
					</div>

					<div style={{ marginBottom: '16px' }}>
						<label style={{ display: 'block', marginBottom: '8px', fontWeight: '500' }}>
							{ __( 'Justify Content', 'wp-dsgn-blocks' ) }
						</label>
						<ButtonGroup>
							{ [
								{ value: 'flex-start', label: __( 'Start', 'wp-dsgn-blocks' ) },
								{ value: 'center', label: __( 'Center', 'wp-dsgn-blocks' ) },
								{ value: 'flex-end', label: __( 'End', 'wp-dsgn-blocks' ) },
								{ value: 'space-between', label: __( 'Between', 'wp-dsgn-blocks' ) }
							].map( ( option ) => (
								<Button
									key={ option.value }
									isPressed={ getResponsiveValue( justifyContent, currentDevice ) === option.value }
									onClick={ () => setResponsiveValue( 'justifyContent', currentDevice, option.value ) }
								>
									{ option.label }
								</Button>
							) ) }
						</ButtonGroup>
					</div>

					<div style={{ marginBottom: '16px' }}>
						<label style={{ display: 'block', marginBottom: '8px', fontWeight: '500' }}>
							{ __( 'Align Items', 'wp-dsgn-blocks' ) }
						</label>
						<ButtonGroup>
							{ [
								{ value: 'flex-start', label: __( 'Start', 'wp-dsgn-blocks' ) },
								{ value: 'center', label: __( 'Center', 'wp-dsgn-blocks' ) },
								{ value: 'flex-end', label: __( 'End', 'wp-dsgn-blocks' ) },
								{ value: 'stretch', label: __( 'Stretch', 'wp-dsgn-blocks' ) }
							].map( ( option ) => (
								<Button
									key={ option.value }
									isPressed={ getResponsiveValue( alignItems, currentDevice ) === option.value }
									onClick={ () => setResponsiveValue( 'alignItems', currentDevice, option.value ) }
								>
									{ option.label }
								</Button>
							) ) }
						</ButtonGroup>
					</div>

					<UnitControl
						label={ __( 'Gap', 'wp-dsgn-blocks' ) }
						value={ getResponsiveValue( gap, currentDevice )?.column || '20px' }
						onChange={ ( value ) => {
							const currentGap = getResponsiveValue( gap, currentDevice ) || {};
							setResponsiveValue( 'gap', currentDevice, { ...currentGap, column: value, row: value } );
						} }
						units={ [
							{ value: 'px', label: 'px' },
							{ value: 'rem', label: 'rem' },
							{ value: '%', label: '%' }
						] }
					/>
				</PanelBody>

				{/* Spacing Panel */}
				<PanelBody
					title={ __( 'Spacing', 'wp-dsgn-blocks' ) }
					initialOpen={ false }
				>
					<BoxControl
						label={ __( 'Padding', 'wp-dsgn-blocks' ) }
						values={ getResponsiveValue( padding, currentDevice ) }
						onChange={ ( value ) => setResponsiveValue( 'padding', currentDevice, value ) }
						units={ [
							{ value: 'px', label: 'px' },
							{ value: 'rem', label: 'rem' },
							{ value: '%', label: '%' }
						] }
					/>

					<BoxControl
						label={ __( 'Margin', 'wp-dsgn-blocks' ) }
						values={ getResponsiveValue( margin, currentDevice ) }
						onChange={ ( value ) => setResponsiveValue( 'margin', currentDevice, value ) }
						units={ [
							{ value: 'px', label: 'px' },
							{ value: 'rem', label: 'rem' },
							{ value: '%', label: '%' }
						] }
					/>
				</PanelBody>

				{/* Background Panel */}
				<PanelBody
					title={ __( 'Background', 'wp-dsgn-blocks' ) }
					initialOpen={ false }
				>
					<ColorPicker
						color={ backgroundColor }
						onChange={ ( value ) => setAttributes( { backgroundColor: value } ) }
						enableAlpha
					/>

					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) => setAttributes( { 
								backgroundImage: {
									url: media.url,
									id: media.id,
									position: 'center center',
									size: 'cover',
									repeat: 'no-repeat'
								}
							} ) }
							allowedTypes={ [ 'image' ] }
							value={ backgroundImage?.id }
							render={ ( { open } ) => (
								<Button 
									onClick={ open }
									variant="secondary"
									style={{ width: '100%', marginTop: '12px' }}
								>
									{ backgroundImage?.url ? __( 'Change Image', 'wp-dsgn-blocks' ) : __( 'Select Image', 'wp-dsgn-blocks' ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>

					{ backgroundImage?.url && (
						<Button 
							onClick={ () => setAttributes( { backgroundImage: null } ) }
							variant="secondary"
							isDestructive
							style={{ width: '100%', marginTop: '8px' }}
						>
							{ __( 'Remove Image', 'wp-dsgn-blocks' ) }
						</Button>
					) }
				</PanelBody>

				{/* Advanced Panel */}
				<PanelBody
					title={ __( 'Advanced', 'wp-dsgn-blocks' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'CSS Class', 'wp-dsgn-blocks' ) }
						value={ customClass }
						onChange={ ( value ) => setAttributes( { customClass: value } ) }
					/>

					<TextControl
						label={ __( 'CSS ID', 'wp-dsgn-blocks' ) }
						value={ customId }
						onChange={ ( value ) => setAttributes( { customId: value } ) }
					/>

					<RangeControl
						label={ __( 'Z-Index', 'wp-dsgn-blocks' ) }
						value={ zIndex }
						onChange={ ( value ) => setAttributes( { zIndex: value } ) }
						min={ -10 }
						max={ 100 }
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
			</InspectorControls>

			{/* Inject CSS for live preview */}
			<style>{ sectionCSS }</style>

			<div { ...blockProps }>
				<InnerBlocks 
					allowedBlocks={ true }
					template={ [
						[ 'core/paragraph', { placeholder: __( 'Add content...', 'wp-dsgn-blocks' ) } ]
					] }
				/>
			</div>
		</>
	);
}
