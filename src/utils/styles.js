/**
 * Generate inline styles for Section block.
 *
 * @param {Object} attributes Block attributes.
 * @return {Object} Inline styles object.
 */
export function generateSectionStyles( attributes ) {
	const {
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
		marginTop,
		marginRight,
		marginBottom,
		marginLeft,
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

	const styles = {
		display: 'flex',
		flexDirection: flexDirection || 'row',
		justifyContent: justifyContent || 'flex-start',
		alignItems: alignItems || 'stretch',
		flexWrap: flexWrap || 'nowrap',
	};

	// Content width
	if ( contentWidth === 'boxed' && maxWidth ) {
		styles.maxWidth = maxWidth;
		styles.marginLeft = 'auto';
		styles.marginRight = 'auto';
	}

	// Min height
	if ( minHeight === 'custom' && minHeightValue ) {
		styles.minHeight = minHeightValue;
	}

	// Gap
	if ( gap ) {
		styles.gap = gap;
	}

	// Padding
	if ( paddingTop ) styles.paddingTop = paddingTop;
	if ( paddingRight ) styles.paddingRight = paddingRight;
	if ( paddingBottom ) styles.paddingBottom = paddingBottom;
	if ( paddingLeft ) styles.paddingLeft = paddingLeft;

	// Margin (only if not boxed, as boxed uses auto margins)
	if ( contentWidth !== 'boxed' ) {
		if ( marginTop ) styles.marginTop = marginTop;
		if ( marginRight ) styles.marginRight = marginRight;
		if ( marginBottom ) styles.marginBottom = marginBottom;
		if ( marginLeft ) styles.marginLeft = marginLeft;
	} else {
		if ( marginTop ) styles.marginTop = marginTop;
		if ( marginBottom ) styles.marginBottom = marginBottom;
	}

	// Background color with opacity
	if ( backgroundColor ) {
		const opacity = backgroundOpacity !== undefined ? backgroundOpacity : 1;
		// Convert hex to rgba if needed
		if ( backgroundColor.startsWith( '#' ) ) {
			const r = parseInt( backgroundColor.slice( 1, 3 ), 16 );
			const g = parseInt( backgroundColor.slice( 3, 5 ), 16 );
			const b = parseInt( backgroundColor.slice( 5, 7 ), 16 );
			styles.backgroundColor = `rgba(${ r }, ${ g }, ${ b }, ${ opacity })`;
		} else {
			styles.backgroundColor = backgroundColor;
		}
	}

	// Background image
	if ( backgroundImage?.url ) {
		styles.backgroundImage = `url(${ backgroundImage.url })`;
		styles.backgroundPosition = backgroundPosition || 'center center';
		styles.backgroundSize = backgroundSize || 'cover';
		styles.backgroundRepeat = backgroundRepeat || 'no-repeat';
	}

	// Background gradient
	if ( backgroundGradient ) {
		styles.backgroundImage = backgroundGradient;
	}

	// Border
	if ( borderTopWidth ) {
		styles.borderTopWidth = borderTopWidth;
		styles.borderTopStyle = borderStyle || 'solid';
	}
	if ( borderRightWidth ) {
		styles.borderRightWidth = borderRightWidth;
		styles.borderRightStyle = borderStyle || 'solid';
	}
	if ( borderBottomWidth ) {
		styles.borderBottomWidth = borderBottomWidth;
		styles.borderBottomStyle = borderStyle || 'solid';
	}
	if ( borderLeftWidth ) {
		styles.borderLeftWidth = borderLeftWidth;
		styles.borderLeftStyle = borderStyle || 'solid';
	}
	if ( borderColor ) {
		styles.borderColor = borderColor;
	}

	// Border radius
	if ( borderTopLeftRadius ) styles.borderTopLeftRadius = borderTopLeftRadius;
	if ( borderTopRightRadius ) styles.borderTopRightRadius = borderTopRightRadius;
	if ( borderBottomRightRadius ) styles.borderBottomRightRadius = borderBottomRightRadius;
	if ( borderBottomLeftRadius ) styles.borderBottomLeftRadius = borderBottomLeftRadius;

	// Box shadow
	if ( boxShadowEnabled ) {
		const x = boxShadowX || '0px';
		const y = boxShadowY || '0px';
		const blur = boxShadowBlur || '0px';
		const spread = boxShadowSpread || '0px';
		const color = boxShadowColor || 'rgba(0,0,0,0.1)';
		styles.boxShadow = `${ x } ${ y } ${ blur } ${ spread } ${ color }`;
	}

	// Z-index
	if ( zIndex ) {
		styles.zIndex = zIndex;
	}

	// Overflow
	if ( overflow && overflow !== 'visible' ) {
		styles.overflow = overflow;
	}

	return styles;
}

/**
 * Generate inline styles for Columns block.
 *
 * @param {Object} attributes Block attributes.
 * @return {Object} Inline styles object.
 */
export function generateColumnsStyles( attributes ) {
	const {
		verticalAlignment,
		columnGap,
		stackOnMobile,
	} = attributes;

	const styles = {
		display: 'flex',
		flexWrap: 'wrap',
	};

	// Vertical alignment
	if ( verticalAlignment ) {
		switch ( verticalAlignment ) {
			case 'top':
				styles.alignItems = 'flex-start';
				break;
			case 'center':
				styles.alignItems = 'center';
				break;
			case 'bottom':
				styles.alignItems = 'flex-end';
				break;
			case 'stretch':
				styles.alignItems = 'stretch';
				break;
			default:
				styles.alignItems = 'stretch';
		}
	}

	// Column gap
	if ( columnGap ) {
		styles.gap = columnGap;
	}

	return styles;
}

/**
 * Generate inline styles for individual Column.
 *
 * @param {Object} columnData Column data.
 * @param {number} totalColumns Total number of columns.
 * @return {Object} Inline styles object.
 */
export function generateColumnStyles( columnData, totalColumns ) {
	const {
		width,
		backgroundColor,
		paddingTop,
		paddingRight,
		paddingBottom,
		paddingLeft,
	} = columnData || {};

	const styles = {
		flex: '1 1 0',
		minWidth: 0,
	};

	// Custom width
	if ( width ) {
		styles.flex = `0 0 ${ width }`;
		styles.maxWidth = width;
	} else {
		// Equal widths
		const equalWidth = `${ 100 / totalColumns }%`;
		styles.flex = `0 0 ${ equalWidth }`;
		styles.maxWidth = equalWidth;
	}

	// Background color
	if ( backgroundColor ) {
		styles.backgroundColor = backgroundColor;
	}

	// Padding
	if ( paddingTop ) styles.paddingTop = paddingTop;
	if ( paddingRight ) styles.paddingRight = paddingRight;
	if ( paddingBottom ) styles.paddingBottom = paddingBottom;
	if ( paddingLeft ) styles.paddingLeft = paddingLeft;

	return styles;
}

