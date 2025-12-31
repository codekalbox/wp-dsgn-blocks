/**
 * Shared style utilities for WP DSGN Blocks
 */

/**
 * Convert hex color to rgba
 * 
 * @param {string} hex   Hex color code
 * @param {number} alpha Alpha value (0-1)
 * @return {string} RGBA color string
 */
export function hexToRgba( hex, alpha = 1 ) {
	if ( ! hex ) return '';
	
	// Remove # if present
	hex = hex.replace( '#', '' );
	
	// Parse hex values
	const r = parseInt( hex.substring( 0, 2 ), 16 );
	const g = parseInt( hex.substring( 2, 4 ), 16 );
	const b = parseInt( hex.substring( 4, 6 ), 16 );
	
	return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

/**
 * Generate responsive CSS for a property
 * 
 * @param {string} selector    CSS selector
 * @param {string} property    CSS property name
 * @param {Object} values      Responsive values object
 * @param {Object} breakpoints Breakpoint values
 * @return {string} Generated CSS
 */
export function generateResponsiveCSS( selector, property, values, breakpoints = {} ) {
	if ( ! values || typeof values !== 'object' ) {
		return '';
	}
	
	const defaultBreakpoints = {
		tablet: '1024px',
		mobile: '768px'
	};
	
	const bp = { ...defaultBreakpoints, ...breakpoints };
	let css = '';
	
	// Desktop (default)
	if ( values.desktop ) {
		css += `${selector} { ${property}: ${values.desktop}; }`;
	}
	
	// Tablet
	if ( values.tablet ) {
		css += `@media (max-width: ${bp.tablet}) {`;
		css += `${selector} { ${property}: ${values.tablet}; }`;
		css += `}`;
	}
	
	// Mobile
	if ( values.mobile ) {
		css += `@media (max-width: ${bp.mobile}) {`;
		css += `${selector} { ${property}: ${values.mobile}; }`;
		css += `}`;
	}
	
	return css;
}

/**
 * Generate spacing CSS (padding/margin)
 * 
 * @param {Object} spacing Spacing object with top, right, bottom, left
 * @return {string} CSS spacing value
 */
export function generateSpacingCSS( spacing ) {
	if ( ! spacing || typeof spacing !== 'object' ) {
		return '';
	}
	
	const { top, right, bottom, left, linked } = spacing;
	
	if ( linked && top ) {
		return top;
	}
	
	return `${top || '0'} ${right || '0'} ${bottom || '0'} ${left || '0'}`;
}

/**
 * Generate box shadow CSS
 * 
 * @param {Array} shadows Array of shadow objects
 * @return {string} CSS box-shadow value
 */
export function generateBoxShadowCSS( shadows ) {
	if ( ! Array.isArray( shadows ) || shadows.length === 0 ) {
		return '';
	}
	
	return shadows.map( shadow => {
		const { x, y, blur, spread, color, inset } = shadow;
		return `${inset ? 'inset ' : ''}${x || 0}px ${y || 0}px ${blur || 0}px ${spread || 0}px ${color || 'rgba(0,0,0,0.1)'}`;
	} ).join( ', ' );
}

/**
 * Generate border CSS
 * 
 * @param {Object} border Border object
 * @return {Object} CSS border properties
 */
export function generateBorderCSS( border ) {
	if ( ! border || typeof border !== 'object' ) {
		return {};
	}
	
	const css = {};
	
	// Border width and style
	if ( border.type && border.type !== 'none' ) {
		const { width, color, type } = border;
		
		if ( width && typeof width === 'object' ) {
			const { top, right, bottom, left, linked } = width;
			
			if ( linked && top ) {
				css.border = `${top} ${type} ${color || '#000000'}`;
			} else {
				if ( top ) css.borderTop = `${top} ${type} ${color || '#000000'}`;
				if ( right ) css.borderRight = `${right} ${type} ${color || '#000000'}`;
				if ( bottom ) css.borderBottom = `${bottom} ${type} ${color || '#000000'}`;
				if ( left ) css.borderLeft = `${left} ${type} ${color || '#000000'}`;
			}
		}
	}
	
	// Border radius
	if ( border.radius && typeof border.radius === 'object' ) {
		const { topLeft, topRight, bottomRight, bottomLeft, linked } = border.radius;
		
		if ( linked && topLeft ) {
			css.borderRadius = topLeft;
		} else {
			css.borderRadius = `${topLeft || '0'} ${topRight || '0'} ${bottomRight || '0'} ${bottomLeft || '0'}`;
		}
	}
	
	return css;
}

/**
 * Generate transform CSS
 * 
 * @param {Object} transform Transform object
 * @return {string} CSS transform value
 */
export function generateTransformCSS( transform ) {
	if ( ! transform || typeof transform !== 'object' ) {
		return '';
	}
	
	const transforms = [];
	
	if ( transform.rotate !== undefined && transform.rotate !== 0 ) {
		transforms.push( `rotate(${transform.rotate}deg)` );
	}
	
	if ( transform.scaleX !== undefined && transform.scaleX !== 1 ) {
		transforms.push( `scaleX(${transform.scaleX})` );
	}
	
	if ( transform.scaleY !== undefined && transform.scaleY !== 1 ) {
		transforms.push( `scaleY(${transform.scaleY})` );
	}
	
	if ( transform.translateX !== undefined && transform.translateX !== '0px' ) {
		transforms.push( `translateX(${transform.translateX})` );
	}
	
	if ( transform.translateY !== undefined && transform.translateY !== '0px' ) {
		transforms.push( `translateY(${transform.translateY})` );
	}
	
	return transforms.length > 0 ? transforms.join( ' ' ) : '';
}

/**
 * Generate filter CSS
 * 
 * @param {Object} filters Filter object
 * @return {string} CSS filter value
 */
export function generateFilterCSS( filters ) {
	if ( ! filters || typeof filters !== 'object' ) {
		return '';
	}
	
	const filterArray = [];
	
	if ( filters.blur !== undefined && filters.blur > 0 ) {
		filterArray.push( `blur(${filters.blur}px)` );
	}
	
	if ( filters.brightness !== undefined && filters.brightness !== 100 ) {
		filterArray.push( `brightness(${filters.brightness}%)` );
	}
	
	if ( filters.contrast !== undefined && filters.contrast !== 100 ) {
		filterArray.push( `contrast(${filters.contrast}%)` );
	}
	
	if ( filters.saturation !== undefined && filters.saturation !== 100 ) {
		filterArray.push( `saturate(${filters.saturation}%)` );
	}
	
	return filterArray.length > 0 ? filterArray.join( ' ' ) : '';
}

/**
 * Debounce function for performance optimization
 * 
 * @param {Function} func  Function to debounce
 * @param {number}   delay Delay in milliseconds
 * @return {Function} Debounced function
 */
export function debounce( func, delay ) {
	let timeoutId;
	return function( ...args ) {
		clearTimeout( timeoutId );
		timeoutId = setTimeout( () => func.apply( this, args ), delay );
	};
}

/**
 * Get breakpoint values
 * 
 * @return {Object} Breakpoint values
 */
export function getBreakpoints() {
	return {
		desktop: 1025,
		tablet: 1024,
		mobile: 768
	};
}

/**
 * Check if value is empty
 * 
 * @param {*} value Value to check
 * @return {boolean} True if empty
 */
export function isEmpty( value ) {
	return value === null || value === undefined || value === '' || 
		   ( Array.isArray( value ) && value.length === 0 ) ||
		   ( typeof value === 'object' && Object.keys( value ).length === 0 );
}

