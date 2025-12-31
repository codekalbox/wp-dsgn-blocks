/**
 * Generate CSS for Section block.
 *
 * @param {Object} attributes Block attributes
 * @param {string} uniqueId   Unique block ID
 * @return {string} Generated CSS
 */
export function generateSectionCSS( attributes, uniqueId ) {
	const {
		contentWidth,
		maxWidth,
		minHeight,
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
		zIndex,
		cssFilters,
		transform,
		transitionDuration,
		hideOnDevices
	} = attributes;

	if ( ! uniqueId ) {
		return '';
	}

	const selector = `.wpdsgn-section-${uniqueId}`;
	let css = '';

	// Helper function to get responsive value
	const getResponsiveValue = ( responsiveObj, device, fallback = '' ) => {
		if ( ! responsiveObj || typeof responsiveObj !== 'object' ) {
			return fallback;
		}
		return responsiveObj[ device ] || responsiveObj.desktop || fallback;
	};

	// Helper function to generate spacing values
	const generateSpacing = ( spacingObj, device ) => {
		const spacing = getResponsiveValue( spacingObj, device );
		if ( ! spacing || typeof spacing !== 'object' ) {
			return '';
		}

		const { top, right, bottom, left } = spacing;
		if ( ! top && ! right && ! bottom && ! left ) {
			return '';
		}

		return `${top || '0'} ${right || '0'} ${bottom || '0'} ${left || '0'}`;
	};

	// Helper function to generate gap values
	const generateGap = ( gapObj, device ) => {
		const gap = getResponsiveValue( gapObj, device );
		if ( ! gap || typeof gap !== 'object' ) {
			return '';
		}

		const { row, column } = gap;
		if ( ! row && ! column ) {
			return '';
		}

		return `${row || '0'} ${column || '0'}`;
	};

	// Base styles
	css += `${selector} {`;
	css += `display: flex;`;
	
	// Layout
	if ( contentWidth === 'boxed' && maxWidth ) {
		css += `max-width: ${maxWidth};`;
		css += `margin-left: auto;`;
		css += `margin-right: auto;`;
	} else if ( contentWidth === 'full' ) {
		css += `width: 100%;`;
	}

	if ( overflow ) {
		css += `overflow: ${overflow};`;
	}

	if ( zIndex !== undefined && zIndex !== '' ) {
		css += `z-index: ${zIndex};`;
	}

	// Desktop styles
	const desktopFlexDirection = getResponsiveValue( flexDirection, 'desktop' );
	const desktopJustifyContent = getResponsiveValue( justifyContent, 'desktop' );
	const desktopAlignItems = getResponsiveValue( alignItems, 'desktop' );
	const desktopFlexWrap = getResponsiveValue( flexWrap, 'desktop' );
	const desktopMinHeight = getResponsiveValue( minHeight, 'desktop' );
	const desktopPadding = generateSpacing( padding, 'desktop' );
	const desktopMargin = generateSpacing( margin, 'desktop' );
	const desktopGap = generateGap( gap, 'desktop' );

	if ( desktopFlexDirection ) {
		css += `flex-direction: ${desktopFlexDirection};`;
	}
	if ( desktopJustifyContent ) {
		css += `justify-content: ${desktopJustifyContent};`;
	}
	if ( desktopAlignItems ) {
		css += `align-items: ${desktopAlignItems};`;
	}
	if ( desktopFlexWrap ) {
		css += `flex-wrap: ${desktopFlexWrap};`;
	}
	if ( desktopMinHeight && desktopMinHeight !== 'auto' ) {
		css += `min-height: ${desktopMinHeight};`;
	}
	if ( desktopPadding ) {
		css += `padding: ${desktopPadding};`;
	}
	if ( desktopMargin ) {
		css += `margin: ${desktopMargin};`;
	}
	if ( desktopGap ) {
		css += `gap: ${desktopGap};`;
	}

	// Background
	if ( backgroundColor ) {
		const opacity = backgroundOpacity !== undefined ? backgroundOpacity : 1;
		css += `background-color: ${backgroundColor};`;
		if ( opacity < 1 ) {
			css += `background-color: ${backgroundColor}${Math.round(opacity * 255).toString(16).padStart(2, '0')};`;
		}
	}

	if ( backgroundGradient ) {
		css += `background-image: ${backgroundGradient};`;
	}

	if ( backgroundImage && backgroundImage.url ) {
		css += `background-image: url(${backgroundImage.url});`;
		css += `background-position: ${backgroundImage.position || 'center center'};`;
		css += `background-size: ${backgroundImage.size || 'cover'};`;
		css += `background-repeat: ${backgroundImage.repeat || 'no-repeat'};`;
		css += `background-attachment: ${backgroundImage.attachment || 'scroll'};`;
		
		if ( backgroundImage.opacity !== undefined && backgroundImage.opacity < 1 ) {
			css += `opacity: ${backgroundImage.opacity};`;
		}
	}

	// Border
	if ( border && border.type && border.type !== 'none' ) {
		const borderWidth = border.width;
		if ( borderWidth && typeof borderWidth === 'object' ) {
			const { top, right, bottom, left, linked } = borderWidth;
			if ( linked && top ) {
				css += `border: ${top} ${border.type} ${border.color || '#000000'};`;
			} else {
				if ( top ) css += `border-top: ${top} ${border.type} ${border.color || '#000000'};`;
				if ( right ) css += `border-right: ${right} ${border.type} ${border.color || '#000000'};`;
				if ( bottom ) css += `border-bottom: ${bottom} ${border.type} ${border.color || '#000000'};`;
				if ( left ) css += `border-left: ${left} ${border.type} ${border.color || '#000000'};`;
			}
		}

		const borderRadius = border.radius;
		if ( borderRadius && typeof borderRadius === 'object' ) {
			const { topLeft, topRight, bottomRight, bottomLeft, linked } = borderRadius;
			if ( linked && topLeft ) {
				css += `border-radius: ${topLeft};`;
			} else {
				css += `border-radius: ${topLeft || '0'} ${topRight || '0'} ${bottomRight || '0'} ${bottomLeft || '0'};`;
			}
		}
	}

	// Box Shadow
	if ( boxShadow && Array.isArray( boxShadow ) && boxShadow.length > 0 ) {
		const shadows = boxShadow.map( shadow => {
			const { x, y, blur, spread, color, inset } = shadow;
			return `${inset ? 'inset ' : ''}${x || 0}px ${y || 0}px ${blur || 0}px ${spread || 0}px ${color || 'rgba(0,0,0,0.1)'}`;
		} );
		css += `box-shadow: ${shadows.join(', ')};`;
	}

	// CSS Filters
	if ( cssFilters ) {
		const filters = [];
		if ( cssFilters.blur > 0 ) filters.push( `blur(${cssFilters.blur}px)` );
		if ( cssFilters.brightness !== 100 ) filters.push( `brightness(${cssFilters.brightness}%)` );
		if ( cssFilters.contrast !== 100 ) filters.push( `contrast(${cssFilters.contrast}%)` );
		if ( cssFilters.saturation !== 100 ) filters.push( `saturate(${cssFilters.saturation}%)` );
		
		if ( filters.length > 0 ) {
			css += `filter: ${filters.join(' ')};`;
		}
	}

	// Transform
	if ( transform ) {
		const transforms = [];
		if ( transform.rotate !== 0 ) transforms.push( `rotate(${transform.rotate}deg)` );
		if ( transform.scaleX !== 1 ) transforms.push( `scaleX(${transform.scaleX})` );
		if ( transform.scaleY !== 1 ) transforms.push( `scaleY(${transform.scaleY})` );
		if ( transform.translateX !== '0px' ) transforms.push( `translateX(${transform.translateX})` );
		if ( transform.translateY !== '0px' ) transforms.push( `translateY(${transform.translateY})` );
		
		if ( transforms.length > 0 ) {
			css += `transform: ${transforms.join(' ')};`;
		}
	}

	// Transition
	if ( transitionDuration && transitionDuration !== '0s' ) {
		css += `transition: all ${transitionDuration} ease;`;
	}

	// Hide on desktop
	if ( hideOnDevices && hideOnDevices.desktop ) {
		css += `display: none !important;`;
	}

	css += `}`;

	// Background overlay
	if ( backgroundOverlay && backgroundOverlay.color ) {
		css += `${selector}::before {`;
		css += `content: '';`;
		css += `position: absolute;`;
		css += `top: 0;`;
		css += `left: 0;`;
		css += `right: 0;`;
		css += `bottom: 0;`;
		css += `background-color: ${backgroundOverlay.color};`;
		css += `opacity: ${backgroundOverlay.opacity || 0.5};`;
		css += `mix-blend-mode: ${backgroundOverlay.blendMode || 'normal'};`;
		css += `pointer-events: none;`;
		css += `}`;
	}

	// Tablet styles
	css += `@media (max-width: 1024px) {`;
	css += `${selector} {`;

	const tabletFlexDirection = getResponsiveValue( flexDirection, 'tablet' );
	const tabletJustifyContent = getResponsiveValue( justifyContent, 'tablet' );
	const tabletAlignItems = getResponsiveValue( alignItems, 'tablet' );
	const tabletFlexWrap = getResponsiveValue( flexWrap, 'tablet' );
	const tabletMinHeight = getResponsiveValue( minHeight, 'tablet' );
	const tabletPadding = generateSpacing( padding, 'tablet' );
	const tabletMargin = generateSpacing( margin, 'tablet' );
	const tabletGap = generateGap( gap, 'tablet' );

	if ( tabletFlexDirection ) {
		css += `flex-direction: ${tabletFlexDirection};`;
	}
	if ( tabletJustifyContent ) {
		css += `justify-content: ${tabletJustifyContent};`;
	}
	if ( tabletAlignItems ) {
		css += `align-items: ${tabletAlignItems};`;
	}
	if ( tabletFlexWrap ) {
		css += `flex-wrap: ${tabletFlexWrap};`;
	}
	if ( tabletMinHeight && tabletMinHeight !== 'auto' ) {
		css += `min-height: ${tabletMinHeight};`;
	}
	if ( tabletPadding ) {
		css += `padding: ${tabletPadding};`;
	}
	if ( tabletMargin ) {
		css += `margin: ${tabletMargin};`;
	}
	if ( tabletGap ) {
		css += `gap: ${tabletGap};`;
	}

	// Hide on tablet
	if ( hideOnDevices && hideOnDevices.tablet ) {
		css += `display: none !important;`;
	}

	css += `}`;
	css += `}`;

	// Mobile styles
	css += `@media (max-width: 768px) {`;
	css += `${selector} {`;

	const mobileFlexDirection = getResponsiveValue( flexDirection, 'mobile' );
	const mobileJustifyContent = getResponsiveValue( justifyContent, 'mobile' );
	const mobileAlignItems = getResponsiveValue( alignItems, 'mobile' );
	const mobileFlexWrap = getResponsiveValue( flexWrap, 'mobile' );
	const mobileMinHeight = getResponsiveValue( minHeight, 'mobile' );
	const mobilePadding = generateSpacing( padding, 'mobile' );
	const mobileMargin = generateSpacing( margin, 'mobile' );
	const mobileGap = generateGap( gap, 'mobile' );

	if ( mobileFlexDirection ) {
		css += `flex-direction: ${mobileFlexDirection};`;
	}
	if ( mobileJustifyContent ) {
		css += `justify-content: ${mobileJustifyContent};`;
	}
	if ( mobileAlignItems ) {
		css += `align-items: ${mobileAlignItems};`;
	}
	if ( mobileFlexWrap ) {
		css += `flex-wrap: ${mobileFlexWrap};`;
	}
	if ( mobileMinHeight && mobileMinHeight !== 'auto' ) {
		css += `min-height: ${mobileMinHeight};`;
	}
	if ( mobilePadding ) {
		css += `padding: ${mobilePadding};`;
	}
	if ( mobileMargin ) {
		css += `margin: ${mobileMargin};`;
	}
	if ( mobileGap ) {
		css += `gap: ${mobileGap};`;
	}

	// Hide on mobile
	if ( hideOnDevices && hideOnDevices.mobile ) {
		css += `display: none !important;`;
	}

	css += `}`;
	css += `}`;

	return css;
}

