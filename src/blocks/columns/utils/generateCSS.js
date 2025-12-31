/**
 * Generate CSS for Columns block.
 *
 * @param {Object} attributes Block attributes
 * @param {string} uniqueId   Unique block ID
 * @return {string} Generated CSS
 */
export function generateColumnsCSS( attributes, uniqueId ) {
	const {
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
		hideOnDevices
	} = attributes;

	if ( ! uniqueId ) {
		return '';
	}

	const selector = `.wpdsgn-columns-${uniqueId}`;
	let css = '';

	// Helper function to get responsive value
	const getResponsiveValue = ( responsiveObj, device, fallback = '' ) => {
		if ( ! responsiveObj || typeof responsiveObj !== 'object' ) {
			return fallback;
		}
		return responsiveObj[ device ] || responsiveObj.desktop || fallback;
	};

	// Base styles
	css += `${selector} {`;
	css += `display: flex;`;
	css += `flex-wrap: wrap;`;
	
	// Desktop styles
	const desktopVerticalAlign = getResponsiveValue( verticalAlignment, 'desktop' );
	const desktopHorizontalAlign = getResponsiveValue( horizontalAlignment, 'desktop' );
	const desktopColumnGap = getResponsiveValue( columnGap, 'desktop' );
	const desktopRowGap = getResponsiveValue( rowGap, 'desktop' );
	const desktopReverse = getResponsiveValue( reverseColumns, 'desktop' );

	if ( desktopVerticalAlign ) {
		css += `align-items: ${desktopVerticalAlign};`;
	}
	if ( desktopHorizontalAlign ) {
		css += `justify-content: ${desktopHorizontalAlign};`;
	}
	if ( desktopColumnGap || desktopRowGap ) {
		css += `gap: ${desktopRowGap || '20px'} ${desktopColumnGap || '20px'};`;
	}
	if ( desktopReverse ) {
		css += `flex-direction: row-reverse;`;
	}
	if ( equalHeight ) {
		css += `align-items: stretch;`;
	}

	// Hide on desktop
	if ( hideOnDevices && hideOnDevices.desktop ) {
		css += `display: none !important;`;
	}

	css += `}`;

	// Column styles
	css += `${selector} > .wp-block-column {`;
	css += `flex: 1;`;
	css += `min-width: 0;`;
	
	// Apply preset or custom widths
	if ( presetLayout !== 'equal' && customWidths && customWidths.length > 0 ) {
		// Custom widths will be applied per column below
	} else {
		// Equal width columns
		css += `flex: 1 1 ${100 / columnCount}%;`;
	}
	
	css += `}`;

	// Individual column styles
	if ( columnSettings && columnSettings.length > 0 ) {
		columnSettings.forEach( ( columnSetting, index ) => {
			if ( ! columnSetting ) return;

			const columnSelector = `${selector} > .wp-block-column:nth-child(${index + 1})`;
			css += `${columnSelector} {`;

			// Custom width
			if ( columnSetting.width ) {
				css += `flex: 0 0 ${columnSetting.width};`;
				css += `max-width: ${columnSetting.width};`;
			} else if ( customWidths && customWidths[ index ] ) {
				css += `flex: 0 0 ${customWidths[ index ]};`;
				css += `max-width: ${customWidths[ index ]};`;
			}

			// Background color
			if ( columnSetting.backgroundColor ) {
				css += `background-color: ${columnSetting.backgroundColor};`;
			}

			// Padding
			if ( columnSetting.padding && typeof columnSetting.padding === 'object' ) {
				const { top, right, bottom, left } = columnSetting.padding;
				if ( top || right || bottom || left ) {
					css += `padding: ${top || '0'} ${right || '0'} ${bottom || '0'} ${left || '0'};`;
				}
			}

			// Order
			if ( columnSetting.order !== undefined && columnSetting.order !== index + 1 ) {
				css += `order: ${columnSetting.order};`;
			}

			// Vertical self-alignment
			if ( columnSetting.verticalAlign && columnSetting.verticalAlign !== 'auto' ) {
				css += `align-self: ${columnSetting.verticalAlign};`;
			}

			css += `}`;
		} );
	}

	// Tablet styles
	css += `@media (max-width: 1024px) {`;
	css += `${selector} {`;

	const tabletVerticalAlign = getResponsiveValue( verticalAlignment, 'tablet' );
	const tabletHorizontalAlign = getResponsiveValue( horizontalAlignment, 'tablet' );
	const tabletColumnGap = getResponsiveValue( columnGap, 'tablet' );
	const tabletRowGap = getResponsiveValue( rowGap, 'tablet' );
	const tabletReverse = getResponsiveValue( reverseColumns, 'tablet' );

	if ( tabletVerticalAlign ) {
		css += `align-items: ${tabletVerticalAlign};`;
	}
	if ( tabletHorizontalAlign ) {
		css += `justify-content: ${tabletHorizontalAlign};`;
	}
	if ( tabletColumnGap || tabletRowGap ) {
		css += `gap: ${tabletRowGap || desktopRowGap || '20px'} ${tabletColumnGap || desktopColumnGap || '20px'};`;
	}
	if ( tabletReverse ) {
		css += `flex-direction: row-reverse;`;
	} else if ( tabletReverse === false ) {
		css += `flex-direction: row;`;
	}

	// Hide on tablet
	if ( hideOnDevices && hideOnDevices.tablet ) {
		css += `display: none !important;`;
	}

	css += `}`;
	css += `}`;

	// Mobile styles
	const mobileBreakpoint = stackBreakpoint?.mobile || '768px';
	css += `@media (max-width: ${mobileBreakpoint}) {`;
	css += `${selector} {`;

	const mobileVerticalAlign = getResponsiveValue( verticalAlignment, 'mobile' );
	const mobileHorizontalAlign = getResponsiveValue( horizontalAlignment, 'mobile' );
	const mobileColumnGap = getResponsiveValue( columnGap, 'mobile' );
	const mobileRowGap = getResponsiveValue( rowGap, 'mobile' );
	const mobileReverse = getResponsiveValue( reverseColumns, 'mobile' );

	// Mobile stacking
	if ( stackOnMobile ) {
		if ( stackDirection === 'reverse' ) {
			css += `flex-direction: column-reverse;`;
		} else {
			css += `flex-direction: column;`;
		}
	}

	if ( mobileVerticalAlign ) {
		css += `align-items: ${mobileVerticalAlign};`;
	}
	if ( mobileHorizontalAlign ) {
		css += `justify-content: ${mobileHorizontalAlign};`;
	}
	if ( mobileColumnGap || mobileRowGap ) {
		css += `gap: ${mobileRowGap || tabletRowGap || desktopRowGap || '20px'} ${mobileColumnGap || tabletColumnGap || desktopColumnGap || '20px'};`;
	}
	if ( ! stackOnMobile && mobileReverse ) {
		css += `flex-direction: row-reverse;`;
	} else if ( ! stackOnMobile && mobileReverse === false ) {
		css += `flex-direction: row;`;
	}

	// Hide on mobile
	if ( hideOnDevices && hideOnDevices.mobile ) {
		css += `display: none !important;`;
	}

	css += `}`;

	// Mobile column styles
	if ( stackOnMobile ) {
		css += `${selector} > .wp-block-column {`;
		css += `flex: 1 1 100%;`;
		css += `max-width: 100%;`;
		css += `}`;
	}

	css += `}`;

	return css;
}

