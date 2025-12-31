/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 6942:
/***/ ((module, exports) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = '';

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (arg) {
				classes = appendClass(classes, parseValue(arg));
			}
		}

		return classes;
	}

	function parseValue (arg) {
		if (typeof arg === 'string' || typeof arg === 'number') {
			return arg;
		}

		if (typeof arg !== 'object') {
			return '';
		}

		if (Array.isArray(arg)) {
			return classNames.apply(null, arg);
		}

		if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
			return arg.toString();
		}

		var classes = '';

		for (var key in arg) {
			if (hasOwn.call(arg, key) && arg[key]) {
				classes = appendClass(classes, key);
			}
		}

		return classes;
	}

	function appendClass (value, newClass) {
		if (!newClass) {
			return value;
		}
	
		if (value) {
			return value + ' ' + newClass;
		}
	
		return value + newClass;
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else // removed by dead control flow
{}
}());


/***/ }),

/***/ 8720:
/***/ (() => {

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
var _blockonsEditorObj;
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var addFilter = wp.hooks.addFilter;
var _wp$element = wp.element,
  useState = _wp$element.useState,
  useEffect = _wp$element.useEffect;
var __ = wp.i18n.__;
var _wp$richText = wp.richText,
  registerFormatType = _wp$richText.registerFormatType,
  unregisterFormatType = _wp$richText.unregisterFormatType,
  applyFormat = _wp$richText.applyFormat,
  removeFormat = _wp$richText.removeFormat,
  getActiveFormat = _wp$richText.getActiveFormat;
var _wp$blockEditor = wp.blockEditor,
  BlockControls = _wp$blockEditor.BlockControls,
  MediaUpload = _wp$blockEditor.MediaUpload;
var _wp$components = wp.components,
  ToolbarGroup = _wp$components.ToolbarGroup,
  Dropdown = _wp$components.Dropdown,
  ToolbarButton = _wp$components.ToolbarButton,
  ToggleControl = _wp$components.ToggleControl,
  TextControl = _wp$components.TextControl,
  TextareaControl = _wp$components.TextareaControl,
  SelectControl = _wp$components.SelectControl,
  Button = _wp$components.Button,
  ColorPicker = _wp$components.ColorPicker,
  ColorIndicator = _wp$components.ColorIndicator;
var _wp$data = wp.data,
  useSelect = _wp$data.useSelect,
  dispatch = _wp$data.dispatch;
var isPremium = Boolean(blockonsEditorObj.isPremium);
var tooltipDefaults = ((_blockonsEditorObj = blockonsEditorObj) === null || _blockonsEditorObj === void 0 || (_blockonsEditorObj = _blockonsEditorObj.blockonsOptions) === null || _blockonsEditorObj === void 0 ? void 0 : _blockonsEditorObj.tooltips) || {
  enabled: false
};
var BlockonsInlineBlockTooltip = function BlockonsInlineBlockTooltip(_ref) {
  var isActive = _ref.isActive,
    onChange = _ref.onChange,
    value = _ref.value;
  var selectedBlock = useSelect(function (select) {
    return select('core/block-editor').getSelectedBlock();
  }, []);

  // console.log("selectedBlock", selectedBlock);

  var allowedBlocks = ['core/paragraph', 'core/heading', 'core/list-item', 'blockons/line-heading'];
  if (!tooltipDefaults.enabled || !selectedBlock || !allowedBlocks.includes(selectedBlock.name)) {
    return null;
  }
  var activeFormat = getActiveFormat(value, 'blockons/inline-tooltip');
  var _useState = useState({
      style: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.style) || 'underlined',
      theme: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.theme) || 'one',
      title: '',
      text: '',
      icon: '',
      image: '',
      color: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.color) || '#424242',
      fcolor: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.fcolor) || '#000',
      pcolor: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.pcolor) || '#424242',
      pfcolor: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.pfcolor) || '#FFF'
    }),
    _useState2 = _slicedToArray(_useState, 2),
    selectedTooltip = _useState2[0],
    setSelectedTooltip = _useState2[1];
  useEffect(function () {
    if (activeFormat) {
      var attributes = Object.keys(activeFormat.attributes).length > 0 ? activeFormat.attributes : Object.keys(activeFormat.unregisteredAttributes).length > 0 ? activeFormat.unregisteredAttributes : '';
      setSelectedTooltip({
        style: attributes['data-style'] || (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.style) || 'underlined',
        theme: attributes['data-theme'] || (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.theme) || 'one',
        title: attributes['data-title'] || '',
        text: attributes['data-text'] || '',
        icon: attributes['data-icon'] || '',
        image: attributes['data-image'] || '',
        color: attributes['data-color'] || (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.color) || '#424242',
        fcolor: attributes['data-fcolor'] || (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.fcolor) || '#000',
        pcolor: attributes['data-pcolor'] || (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.pcolor) || '#424242',
        pfcolor: attributes['data-pfcolor'] || (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.pfcolor) || '#FFF'
      });
    } else {
      setSelectedTooltip({
        style: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.style) || 'underlined',
        theme: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.theme) || 'one',
        title: '',
        text: '',
        icon: '',
        image: '',
        color: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.color) || '#424242',
        fcolor: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.fcolor) || '#000',
        pcolor: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.pcolor) || '#424242',
        pfcolor: (tooltipDefaults === null || tooltipDefaults === void 0 ? void 0 : tooltipDefaults.pfcolor) || '#FFF'
      });
    }
  }, [activeFormat]);
  var handleTooltipChange = function handleTooltipChange(property) {
    return function (newValue) {
      setSelectedTooltip(function (prevState) {
        return _objectSpread(_objectSpread({}, prevState), {}, _defineProperty({}, property, newValue));
      });
    };
  };
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(BlockControls, null, /*#__PURE__*/React.createElement(ToolbarGroup, null, /*#__PURE__*/React.createElement(Dropdown, {
    className: "blockons-tooltip-settings-dropdown",
    contentClassName: "blockons-tooltip-settings-popover",
    popoverProps: {
      placement: 'bottom-start'
    },
    renderToggle: function renderToggle(_ref2) {
      var isOpen = _ref2.isOpen,
        onToggle = _ref2.onToggle;
      return /*#__PURE__*/React.createElement(ToolbarButton, {
        icon: /*#__PURE__*/React.createElement("span", {
          className: "dashicons dashicons-format-status"
        }),
        title: "Blockons Tooltip",
        onClick: onToggle,
        isActive: isActive
      });
    },
    renderContent: function renderContent() {
      return /*#__PURE__*/React.createElement("div", {
        className: "blockons-tooltip-settings"
      }, /*#__PURE__*/React.createElement(TextControl, {
        label: __('Tooltip Title', 'blockons'),
        value: selectedTooltip.title,
        onChange: handleTooltipChange('title')
      }), /*#__PURE__*/React.createElement(TextareaControl, {
        label: __('Tooltip Text', 'blockons'),
        value: selectedTooltip.text,
        onChange: handleTooltipChange('text')
      }), isPremium && /*#__PURE__*/React.createElement("p", {
        className: "tooltip-link-hint"
      }, __('Add links', 'blockons'), ": (Blockons [*https://blockons.com/])"), /*#__PURE__*/React.createElement("div", {
        className: "blockons-divider"
      }), /*#__PURE__*/React.createElement("div", {
        className: "blockons-tooltip-extra"
      }, /*#__PURE__*/React.createElement("div", {
        className: "blockons-tooltip-color-wrap"
      }, !isPremium && /*#__PURE__*/React.createElement(Dropdown, {
        className: "blockons-tooltip-color disabled",
        contentClassName: "blockons-tooltip-color-picker",
        renderToggle: function renderToggle(_ref3) {
          var isOpen = _ref3.isOpen,
            onToggle = _ref3.onToggle;
          return /*#__PURE__*/React.createElement(ToolbarButton, {
            icon: /*#__PURE__*/React.createElement("span", {
              className: "dashicons dashicons-admin-appearance"
            }),
            title: __('Blockons Tooltip', 'blockons'),
            onClick: onToggle,
            isActive: isOpen,
            className: "btn-disabled"
          });
        },
        renderContent: function renderContent() {
          return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("p", null, __('Blockons Pro offers advanced Tooltips with more customization options.', 'blockons')));
        }
      }), isPremium && activeFormat && /*#__PURE__*/React.createElement(Dropdown, {
        className: "blockons-tooltip-color",
        contentClassName: "blockons-tooltip-color-picker",
        renderToggle: function renderToggle(_ref4) {
          var isOpen = _ref4.isOpen,
            onToggle = _ref4.onToggle;
          return /*#__PURE__*/React.createElement(ToolbarButton, {
            icon: /*#__PURE__*/React.createElement("span", {
              className: "dashicons dashicons-admin-appearance"
            }),
            title: __('Blockons Tooltip', 'blockons'),
            onClick: onToggle,
            isActive: isOpen
          });
        },
        renderContent: function renderContent() {
          return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Tooltip Link Style', 'blockons'),
            value: selectedTooltip.style,
            options: [{
              label: 'Underlined',
              value: 'underlined'
            }, {
              label: 'Underline Dashed',
              value: 'dashed'
            }, {
              label: 'Highlighted',
              value: 'highlight'
            }],
            onChange: handleTooltipChange('style')
          }), /*#__PURE__*/React.createElement("div", {
            className: "blockons-tooltip-clrs"
          }, /*#__PURE__*/React.createElement(Dropdown, {
            className: "blockons-tooltip-color",
            contentClassName: "blockons-tooltip-color-picker",
            renderToggle: function renderToggle(_ref5) {
              var isOpen = _ref5.isOpen,
                onToggle = _ref5.onToggle;
              return /*#__PURE__*/React.createElement(Button, {
                variant: "link",
                onClick: onToggle,
                className: "blockons-colorpicker-btn"
              }, /*#__PURE__*/React.createElement(ColorIndicator, {
                colorValue: selectedTooltip.color
              }), /*#__PURE__*/React.createElement("span", null, __('Tooltip Link Color', 'blockons')));
            },
            renderContent: function renderContent() {
              return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(ColorPicker, {
                color: selectedTooltip.color,
                onChange: handleTooltipChange('color'),
                defaultValue: "#f4f4f4"
              }));
            }
          }), (selectedTooltip.theme !== 'one' || selectedTooltip.theme !== 'two') && /*#__PURE__*/React.createElement(Dropdown, {
            className: "blockons-tooltip-color",
            contentClassName: "blockons-tooltip-color-picker",
            renderToggle: function renderToggle(_ref6) {
              var isOpen = _ref6.isOpen,
                onToggle = _ref6.onToggle;
              return /*#__PURE__*/React.createElement(Button, {
                variant: "link",
                onClick: onToggle,
                className: "blockons-colorpicker-btn"
              }, /*#__PURE__*/React.createElement(ColorIndicator, {
                colorValue: selectedTooltip.fcolor
              }), /*#__PURE__*/React.createElement("span", null, __('Font Color', 'blockons')));
            },
            renderContent: function renderContent() {
              return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(ColorPicker, {
                color: selectedTooltip.fcolor,
                onChange: handleTooltipChange('fcolor'),
                defaultValue: "#f4f4f4"
              }));
            }
          })), /*#__PURE__*/React.createElement("div", {
            className: "blockons-divider"
          }), /*#__PURE__*/React.createElement(TextControl, {
            label: __('Tooltip Icon', 'blockons'),
            value: selectedTooltip.icon,
            onChange: handleTooltipChange('icon'),
            help: __('Add the CSS class names of the Font Awesome icon to be displayed in the Tooltip', 'blockons')
          }), /*#__PURE__*/React.createElement("div", {
            className: "blockons-divider"
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Tooltip Popup Theme', 'blockons'),
            value: selectedTooltip.theme,
            options: [{
              label: 'Dark',
              value: 'one'
            }, {
              label: 'Light',
              value: 'two'
            }, {
              label: 'Custom Color',
              value: 'custom'
            }],
            onChange: handleTooltipChange('theme')
          }), /*#__PURE__*/React.createElement("div", {
            className: "blockons-divider"
          }), selectedTooltip.theme === 'custom' && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
            className: "blockons-tooltip-clrs"
          }, /*#__PURE__*/React.createElement(Dropdown, {
            className: "blockons-tooltip-color",
            contentClassName: "blockons-tooltip-color-picker",
            renderToggle: function renderToggle(_ref7) {
              var isOpen = _ref7.isOpen,
                onToggle = _ref7.onToggle;
              return /*#__PURE__*/React.createElement(Button, {
                variant: "link",
                onClick: onToggle,
                className: "blockons-colorpicker-btn"
              }, /*#__PURE__*/React.createElement(ColorIndicator, {
                colorValue: selectedTooltip.pcolor
              }), /*#__PURE__*/React.createElement("span", null, __('Popup Color', 'blockons')));
            },
            renderContent: function renderContent() {
              return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(ColorPicker, {
                color: selectedTooltip.pcolor,
                onChange: handleTooltipChange('pcolor'),
                defaultValue: "#d6c0ff"
              }));
            }
          }), (selectedTooltip.theme !== 'one' || selectedTooltip.theme !== 'two') && /*#__PURE__*/React.createElement(Dropdown, {
            className: "blockons-tooltip-color",
            contentClassName: "blockons-tooltip-color-picker",
            renderToggle: function renderToggle(_ref8) {
              var isOpen = _ref8.isOpen,
                onToggle = _ref8.onToggle;
              return /*#__PURE__*/React.createElement(Button, {
                variant: "link",
                onClick: onToggle,
                className: "blockons-colorpicker-btn"
              }, /*#__PURE__*/React.createElement(ColorIndicator, {
                colorValue: selectedTooltip.pfcolor
              }), /*#__PURE__*/React.createElement("span", null, __('Popup Font Color', 'blockons')));
            },
            renderContent: function renderContent() {
              return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(ColorPicker, {
                color: selectedTooltip.pfcolor,
                onChange: handleTooltipChange('pfcolor'),
                defaultValue: "#000"
              }));
            }
          })), /*#__PURE__*/React.createElement("div", {
            className: "blockons-divider"
          })), /*#__PURE__*/React.createElement(MediaUpload, {
            className: "components-icon-button components-toolbar__control",
            allowedTypes: ['image'],
            value: selectedTooltip.image,
            onSelect: function onSelect(media) {
              return handleTooltipChange('image')(media.url);
            },
            render: function render(_ref9) {
              var open = _ref9.open;
              return selectedTooltip !== null && selectedTooltip !== void 0 && selectedTooltip.image ? /*#__PURE__*/React.createElement("div", {
                className: "blockons-tooltip-imgpreview"
              }, /*#__PURE__*/React.createElement("div", {
                className: "blockons-tooltip-imgpreview-img"
              }, /*#__PURE__*/React.createElement("img", {
                src: selectedTooltip.image
              })), /*#__PURE__*/React.createElement(Button, {
                className: "blockons-tt-upload-button remove",
                onClick: function onClick() {
                  handleTooltipChange('image')('');
                }
              }, __('Remove Image', 'blockons'))) : /*#__PURE__*/React.createElement(Button, {
                className: "blockons-tt-upload-button",
                icon: "format-image",
                onClick: open
              }, __('Add a Tooltip Image', 'blockons'));
            }
          }), /*#__PURE__*/React.createElement("p", {
            className: "note-link"
          }, /*#__PURE__*/React.createElement("a", {
            href: "https://blockons.com/documentation/content-tooltips/#adding-tooltips",
            target: "_blank"
          }, __('Read more on adding Tooltips', 'blockons'))));
        }
      })), /*#__PURE__*/React.createElement("div", {
        className: "blockons-tooltip-preview  ".concat(selectedTooltip.theme)
      }, isPremium && selectedTooltip.style === 'highlight' ? /*#__PURE__*/React.createElement("mark", {
        className: "blockons-tooltip-style ".concat(selectedTooltip.style),
        style: _objectSpread({
          backgroundColor: selectedTooltip.color
        }, selectedTooltip.fcolor ? {
          color: selectedTooltip.fcolor
        } : {})
      }, "Tooltip Preview") : /*#__PURE__*/React.createElement("span", _extends({
        className: "blockons-tooltip-style ".concat(selectedTooltip.style)
      }, isPremium ? {
        style: _objectSpread(_objectSpread({}, selectedTooltip.color ? {
          borderBottomColor: selectedTooltip.color
        } : {}), selectedTooltip.fcolor ? {
          color: selectedTooltip.fcolor
        } : {})
      } : {}), "Tooltip Preview"), isPremium && selectedTooltip.icon && /*#__PURE__*/React.createElement("span", {
        className: "blockons-icon ".concat(selectedTooltip.icon),
        style: {
          color: selectedTooltip.color
        }
      }), /*#__PURE__*/React.createElement("div", _extends({
        className: "blockons-tooltip-preview-tooltip"
      }, isPremium && selectedTooltip.theme === 'custom' ? {
        style: {
          backgroundColor: selectedTooltip.pcolor,
          color: selectedTooltip.pfcolor
        }
      } : {}), isPremium && selectedTooltip.image && /*#__PURE__*/React.createElement("div", {
        className: "blockons-tooltip-img"
      }, /*#__PURE__*/React.createElement("div", {
        className: "blockons-tooltip-img-prvw"
      }, "Image")), /*#__PURE__*/React.createElement("h6", {
        className: "preview-title"
      }, "Tooltip Title"), /*#__PURE__*/React.createElement("p", {
        className: "preview-text"
      }, "Some example text."), /*#__PURE__*/React.createElement("span", _extends({
        className: "blockons-tooltip-arrow"
      }, isPremium && selectedTooltip.theme === 'custom' ? {
        style: {
          borderTopColor: selectedTooltip.pcolor
        }
      } : {}))))), /*#__PURE__*/React.createElement("div", {
        className: "blockons-tooltip-btns ".concat(activeFormat ? 'active' : '')
      }, /*#__PURE__*/React.createElement(Button, {
        variant: "secondary",
        className: "blockons-tooltip-button",
        onClick: function onClick() {
          onChange(applyFormat(value, {
            type: 'blockons/inline-tooltip',
            attributes: {
              'data-style': selectedTooltip.style,
              'data-theme': selectedTooltip.theme,
              'data-title': selectedTooltip.title,
              'data-text': selectedTooltip.text,
              'data-icon': selectedTooltip.icon,
              'data-image': selectedTooltip.image,
              'data-color': selectedTooltip.color,
              'data-fcolor': selectedTooltip.fcolor,
              'data-pcolor': selectedTooltip.pcolor,
              'data-pfcolor': selectedTooltip.pfcolor
            }
          }));
        },
        disabled: !selectedTooltip.title && !selectedTooltip.text
      }, activeFormat && (selectedTooltip.title || selectedTooltip.text) ? __('Update Tooltip', 'blockons') : __('Add Tooltip', 'blockons')), activeFormat && /*#__PURE__*/React.createElement(Button, {
        variant: "secondary",
        className: "blockons-tooltip-button remove",
        onClick: function onClick() {
          onChange(removeFormat(value, 'blockons/inline-tooltip'));
        },
        icon: "no-alt",
        title: __('Remove Tooltip', 'blockons')
      })));
    }
  }))));
};
registerFormatType('blockons/inline-tooltip', {
  title: 'Blockons Tooltip',
  tagName: 'span',
  className: 'blockons-inline-tooltip',
  edit: BlockonsInlineBlockTooltip
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";

// EXTERNAL MODULE: ./src/backend/editor/extensions/tooltips.js
var tooltips = __webpack_require__(8720);
// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(6942);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);
;// external "wp.i18n"
const external_wp_i18n_namespaceObject = wp.i18n;
;// ./src/backend/settings/components/UI/BlockonsNote.js

var BlockonsNote = function BlockonsNote(_ref) {
  var imageUrl = _ref.imageUrl,
    title = _ref.title,
    text = _ref.text,
    docLink = _ref.docLink,
    docText = _ref.docText,
    upgradeLink = _ref.upgradeLink,
    upgradeText = _ref.upgradeText,
    proFeatures = _ref.proFeatures,
    noline = _ref.noline;
  return /*#__PURE__*/React.createElement("div", {
    className: "blockons-editor-note ".concat(noline ? 'noline' : '')
  }, imageUrl && /*#__PURE__*/React.createElement("div", {
    className: "bl-note-img"
  }, /*#__PURE__*/React.createElement("img", {
    src: imageUrl,
    alt: title
  })), title && /*#__PURE__*/React.createElement("h4", {
    className: "bl-note-title"
  }, title), text && /*#__PURE__*/React.createElement("div", {
    className: "bl-note-txt"
  }, text), proFeatures && /*#__PURE__*/React.createElement("div", {
    className: "bl-note-features"
  }, proFeatures.map(function (feature, i) {
    return /*#__PURE__*/React.createElement("div", {
      key: i,
      className: "bl-note-feature"
    }, feature);
  })), (docLink || upgradeLink) && /*#__PURE__*/React.createElement("div", {
    className: "bl-note-links"
  }, docLink && /*#__PURE__*/React.createElement("a", {
    href: docLink,
    className: "bl-note-link",
    target: "_blank"
  }, docText ? docText : (0,external_wp_i18n_namespaceObject.__)('Read Documentation', 'blockons')), upgradeLink && /*#__PURE__*/React.createElement("a", {
    href: upgradeLink,
    className: "bl-note-link upgrade",
    target: "_blank"
  }, upgradeText ? upgradeText : (0,external_wp_i18n_namespaceObject.__)('Upgrade to Pro', 'blockons'))));
};
/* harmony default export */ const UI_BlockonsNote = (BlockonsNote);
;// ./src/backend/editor/extensions/imagepopups.js
var _blockonsEditorObj$bl;
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


var __ = wp.i18n.__;
var addFilter = wp.hooks.addFilter;
var _lodash = lodash,
  imagepopups_assign = _lodash.assign,
  merge = _lodash.merge;
var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
var isPremium = Boolean(blockonsEditorObj.isPremium);
var defaultOptions = (_blockonsEditorObj$bl = blockonsEditorObj.blockonsOptions) === null || _blockonsEditorObj$bl === void 0 ? void 0 : _blockonsEditorObj$bl.imagepopups;
var defaultOptionImgEnabled = Boolean(defaultOptions === null || defaultOptions === void 0 ? void 0 : defaultOptions.enabled);
var enabledAllBlocks = defaultOptions === null || defaultOptions === void 0 ? void 0 : defaultOptions.enable_all;
var allowedImgPopupBlockTypes = ['core/image'];

/**
 * Add New Attributes to all blocks
 */
function blockonsAddImgPopupAttributes(settings, name) {
  var showOnImageBlock = allowedImgPopupBlockTypes.includes(name);
  if (!showOnImageBlock) {
    return settings;
  }
  var blockImgPopupAtts = {
    blockonsPopupEnabled: {
      type: 'boolean',
      "default": false
    },
    blockonsGlobalDisable: {
      type: 'boolean',
      "default": false
    },
    blockonsPopupIcon: {
      type: 'string',
      "default": 'one'
    },
    blockonsPopupIconPos: {
      type: 'string',
      "default": 'topleft'
    },
    blockonsPopupIconColor: {
      type: 'string',
      "default": 'dark'
    },
    blockonsGalleryId: {
      type: 'string',
      "default": ''
    }
  };
  return imagepopups_assign({}, settings, {
    attributes: merge(settings.attributes, blockImgPopupAtts)
  });
}
/**
 * Add New Controls to all blocks
 */
var blockonsAddInspectorImgPopupControls = createHigherOrderComponent(function (BlockEdit) {
  return function (props) {
    var Fragment = wp.element.Fragment;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var _wp$components = wp.components,
      PanelBody = _wp$components.PanelBody,
      ToggleControl = _wp$components.ToggleControl,
      SelectControl = _wp$components.SelectControl,
      TextControl = _wp$components.TextControl;
    var _props$attributes = props.attributes,
      blockonsPopupEnabled = _props$attributes.blockonsPopupEnabled,
      blockonsGlobalDisable = _props$attributes.blockonsGlobalDisable,
      blockonsPopupIcon = _props$attributes.blockonsPopupIcon,
      blockonsPopupIconPos = _props$attributes.blockonsPopupIconPos,
      blockonsPopupIconColor = _props$attributes.blockonsPopupIconColor,
      blockonsGalleryId = _props$attributes.blockonsGalleryId,
      setAttributes = props.setAttributes,
      name = props.name;
    var showOnImageBlock = allowedImgPopupBlockTypes.includes(name);
    if (!showOnImageBlock || !defaultOptionImgEnabled) {
      return /*#__PURE__*/React.createElement(BlockEdit, props);
    }
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: __('Image Lightbox Settings', 'blockons'),
      initialOpen: false
    }, enabledAllBlocks ? /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(UI_BlockonsNote, {
      imageUrl: "",
      title: __('Lightbox Enabled Globally', 'blockons'),
      text: __('Global lightbox enabled. Please disable it in the settings to add lightboxes manually to images.', 'blockons'),
      docLink: "".concat(blockonsEditorObj.adminUrl, "options-general.php?page=blockons-settings&tab=extensions"),
      docText: __('View Settings', 'blockons'),
      noline: true
    }), /*#__PURE__*/React.createElement("div", {
      className: "blockons-divider"
    }), isPremium ? /*#__PURE__*/React.createElement(ToggleControl, {
      checked: blockonsGlobalDisable,
      label: __('Disable Lightbox', 'blockons'),
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsGlobalDisable: newValue
        });
      },
      help: __('Disable the global popup on this image', 'blockons')
    }) : /*#__PURE__*/React.createElement("p", null, __('Disable selected images from the global lightbox in Blockons Premium', 'blockons'))) : /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
      checked: blockonsPopupEnabled,
      label: __('Enable Image Lightbox', 'blockons'),
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsPopupEnabled: newValue
        });
      },
      help: __('This block popup extension will only work on the frontend', 'blockons')
    }), blockonsPopupEnabled && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "blockons-divider"
    }), /*#__PURE__*/React.createElement(SelectControl, {
      label: __('Icon', 'blockons'),
      value: blockonsPopupIcon,
      options: [{
        label: 'Magnifying Glass',
        value: 'one'
      }, {
        label: 'Expand',
        value: 'two'
      }, {
        label: 'Diagonal Arrows',
        value: 'three'
      }, {
        label: 'Maximize',
        value: 'four'
      }, {
        label: 'Plus',
        value: 'five'
      }, {
        label: 'Cross Arrows',
        value: 'six'
      }],
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsPopupIcon: newValue
        });
      }
    }), /*#__PURE__*/React.createElement(SelectControl, {
      label: __('Icon Position', 'blockons'),
      value: blockonsPopupIconPos,
      options: [{
        label: 'Top Left',
        value: 'topleft'
      }, {
        label: 'Top Right',
        value: 'topright'
      }, {
        label: 'Bottom Left',
        value: 'bottomleft'
      }, {
        label: 'Bottom Right',
        value: 'bottomright'
      }, {
        label: 'Center Center',
        value: 'center'
      }],
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsPopupIconPos: newValue
        });
      }
    }), /*#__PURE__*/React.createElement(SelectControl, {
      label: __('Icon Color', 'blockons'),
      value: blockonsPopupIconColor,
      options: [{
        label: 'Dark',
        value: 'dark'
      }, {
        label: 'Light',
        value: 'light'
      }],
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsPopupIconColor: newValue
        });
      }
    }), isPremium && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "blockons-divider"
    }), /*#__PURE__*/React.createElement(TextControl, {
      label: __('Gallery ID', 'blockons'),
      value: blockonsGalleryId,
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsGalleryId: newValue
        });
      },
      help: __('Enter a unique ID for the gallery. All images with the same ID will be grouped together.', 'blockons')
    })), /*#__PURE__*/React.createElement("div", {
      className: "blockons-divider"
    }), /*#__PURE__*/React.createElement(UI_BlockonsNote, {
      imageUrl: "",
      title: __('Using Image Popups', 'blockons'),
      text: __('Enable this option to show the image in a popup when clicked.', 'blockons'),
      docLink: "https://blockons.com/documentation/image-lightbox-popups/",
      noline: true
    }))))));
  };
}, 'blockonsAddInspectorImgPopupControls');
/**
 * Add CSS Classes to the blocks in the editor
 */
var blockonsAddEditorImgPopupAttributes = createHigherOrderComponent(function (BlockListBlock) {
  return function (props) {
    var _props$attributes2 = props.attributes,
      caption = _props$attributes2.caption,
      url = _props$attributes2.url,
      blockonsPopupEnabled = _props$attributes2.blockonsPopupEnabled,
      blockonsGlobalDisable = _props$attributes2.blockonsGlobalDisable,
      blockonsPopupIcon = _props$attributes2.blockonsPopupIcon,
      blockonsPopupIconPos = _props$attributes2.blockonsPopupIconPos,
      blockonsPopupIconColor = _props$attributes2.blockonsPopupIconColor,
      blockonsGalleryId = _props$attributes2.blockonsGalleryId,
      className = props.className,
      name = props.name;
    var showOnImageBlock = allowedImgPopupBlockTypes.includes(name);
    if (!showOnImageBlock) {
      return /*#__PURE__*/React.createElement(BlockListBlock, props);
    }
    var newClasses = blockonsPopupEnabled ? "blockons-img-popup blcks-".concat(blockonsPopupIcon, " blcks-").concat(blockonsPopupIconPos, " blcks-").concat(blockonsPopupIconColor, " ").concat(isPremium && blockonsGalleryId ? 'gallery' : 'single') : '';
    var globalClass = isPremium && blockonsGlobalDisable ? 'global-off' : '';
    var newClassnames = classnames_default()(className, newClasses, globalClass);
    var newProps = blockonsPopupEnabled && isPremium && blockonsGalleryId !== '' ? {
      'data-gallery': blockonsGalleryId
    } : {};
    return /*#__PURE__*/React.createElement(BlockListBlock, _extends({}, props, {
      className: newClassnames,
      wrapperProps: newProps
    }));
  };
}, 'blockonsAddEditorImgPopupAttributes');
/**
 * Add Classes to the blocks on the Frontend
 */
var blockonsAddFrontendImgPopupAttributes = function blockonsAddFrontendImgPopupAttributes(extraProps, blockType, attributes) {
  var caption = attributes.caption,
    url = attributes.url,
    blockonsPopupEnabled = attributes.blockonsPopupEnabled,
    blockonsGlobalDisable = attributes.blockonsGlobalDisable,
    blockonsPopupIcon = attributes.blockonsPopupIcon,
    blockonsPopupIconPos = attributes.blockonsPopupIconPos,
    blockonsPopupIconColor = attributes.blockonsPopupIconColor,
    blockonsGalleryId = attributes.blockonsGalleryId;
  var name = blockType.name;
  var showOnImageBlock = allowedImgPopupBlockTypes.includes(name);
  if (!showOnImageBlock || !defaultOptionImgEnabled) {
    return extraProps;
  }
  var newClasses = blockonsPopupEnabled ? "blockons-img-popup blcks-".concat(blockonsPopupIcon, " blcks-").concat(blockonsPopupIconPos, " blcks-").concat(blockonsPopupIconColor, " ").concat(isPremium && blockonsGalleryId ? 'gallery' : 'single') : '';
  var globalClass = isPremium && blockonsGlobalDisable ? 'global-off' : '';
  var newClassnames = classnames_default()(extraProps.className, newClasses, globalClass);
  extraProps.className = newClassnames;
  if (blockonsPopupEnabled && isPremium && blockonsGalleryId !== '') {
    extraProps['data-gallery'] = blockonsGalleryId;
  }
  return extraProps;
};

/**
 * WP Editor Hooks
 */
addFilter('blocks.registerBlockType', 'blockons/block-imagepopups-attributes', blockonsAddImgPopupAttributes);
addFilter('editor.BlockEdit', 'blockons/block-imagepopups-controls', blockonsAddInspectorImgPopupControls);
addFilter('blocks.getSaveContent.extraProps', 'blockons/block-imagepopups-frontend-classes', blockonsAddFrontendImgPopupAttributes);
addFilter('editor.BlockListBlock', 'blockons/block-imagepopups-editor-classes', blockonsAddEditorImgPopupAttributes);
;// ./src/backend/editor/extensions/visibility.js
var visibility_blockonsEditorObj$bl;
function visibility_extends() { return visibility_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, visibility_extends.apply(null, arguments); }


var visibility_ = wp.i18n.__;
var visibility_addFilter = wp.hooks.addFilter;
var visibility_lodash = lodash,
  visibility_assign = visibility_lodash.assign,
  visibility_merge = visibility_lodash.merge;
var visibility_createHigherOrderComponent = wp.compose.createHigherOrderComponent;
var visibility_isPremium = Boolean(blockonsEditorObj.isPremium);
var visibilityEnabled = Boolean((visibility_blockonsEditorObj$bl = blockonsEditorObj.blockonsOptions) === null || visibility_blockonsEditorObj$bl === void 0 || (visibility_blockonsEditorObj$bl = visibility_blockonsEditorObj$bl.blockvisibility) === null || visibility_blockonsEditorObj$bl === void 0 ? void 0 : visibility_blockonsEditorObj$bl.enabled) || false;
var allowedVisibilityBlockTypes = ['core/group', 'core/columns', 'core/column'];

/**
 * Add New Attributes to all blocks
 */
function blockonsAddVisibilityAttributes(settings, name) {
  if (!visibility_isPremium || !visibilityEnabled) return settings;

  // console.log({ settings, name });
  var showVisibilitySettings = allowedVisibilityBlockTypes.includes(name);
  var blockVisibilityAtts = showVisibilitySettings ? {
    blockonsHideOnDesktop: {
      type: 'boolean',
      "default": false
    },
    blockonsHideOnTablet: {
      type: 'boolean',
      "default": false
    },
    blockonsHideOnMobile: {
      type: 'boolean',
      "default": false
    }
  } : {};
  return visibility_assign({}, settings, {
    attributes: visibility_merge(settings.attributes, blockVisibilityAtts)
  });
}
/**
 * Add New Controls to all blocks
 */
var blockonsAddInspectorVisibilityControls = visibility_createHigherOrderComponent(function (BlockEdit) {
  return function (props) {
    var Fragment = wp.element.Fragment;
    var _wp$blockEditor = wp.blockEditor,
      InspectorControls = _wp$blockEditor.InspectorControls,
      BlockControls = _wp$blockEditor.BlockControls;
    var _wp$components = wp.components,
      PanelBody = _wp$components.PanelBody,
      ToggleControl = _wp$components.ToggleControl,
      RangeControl = _wp$components.RangeControl,
      SelectControl = _wp$components.SelectControl,
      TextControl = _wp$components.TextControl,
      ToolbarGroup = _wp$components.ToolbarGroup,
      DropdownMenu = _wp$components.DropdownMenu;
    var _props$attributes = props.attributes,
      blockonsHideOnDesktop = _props$attributes.blockonsHideOnDesktop,
      blockonsHideOnTablet = _props$attributes.blockonsHideOnTablet,
      blockonsHideOnMobile = _props$attributes.blockonsHideOnMobile,
      setAttributes = props.setAttributes,
      name = props.name;
    var showVisibilitySettings = allowedVisibilityBlockTypes.includes(name);
    if (!visibility_isPremium || !visibilityEnabled) {
      return /*#__PURE__*/React.createElement(BlockEdit, props);
    }
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), showVisibilitySettings && /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: visibility_('Block Visibility', 'blockons'),
      initialOpen: false
    }, /*#__PURE__*/React.createElement(UI_BlockonsNote, {
      imageUrl: "",
      title: visibility_('Using Block Visibility', 'blockons'),
      text: visibility_('Hide certain blocks by device screen size. For more information, click the link below.', 'blockons'),
      docLink: "https://blockons.com/documentation/block-visibility-per-device/"
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      checked: blockonsHideOnDesktop,
      label: visibility_('Hide on desktop', 'blockons'),
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsHideOnDesktop: newValue
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      checked: blockonsHideOnTablet,
      label: visibility_('Hide on tablet', 'blockons'),
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsHideOnTablet: newValue
        });
      }
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      checked: blockonsHideOnMobile,
      label: visibility_('Hide on mobile', 'blockons'),
      onChange: function onChange(newValue) {
        return setAttributes({
          blockonsHideOnMobile: newValue
        });
      }
    }))));
  };
}, 'blockonsAddInspectorVisibilityControls');
/**
 * Add CSS Classes to the blocks in the editor
 */
var blockonsAddEditorVisibilityAttributes = visibility_createHigherOrderComponent(function (BlockListBlock) {
  return function (props) {
    var _props$attributes2 = props.attributes,
      blockonsHideOnDesktop = _props$attributes2.blockonsHideOnDesktop,
      blockonsHideOnTablet = _props$attributes2.blockonsHideOnTablet,
      blockonsHideOnMobile = _props$attributes2.blockonsHideOnMobile,
      className = props.className,
      name = props.name;
    var showVisibilitySettings = allowedVisibilityBlockTypes.includes(name);
    if (!visibility_isPremium || !visibilityEnabled) {
      return /*#__PURE__*/React.createElement(BlockListBlock, props);
    }
    var newClassnames = showVisibilitySettings ? classnames_default()(className, "".concat(blockonsHideOnDesktop ? 'hide-on-desktop' : '', " ").concat(blockonsHideOnTablet ? 'hide-on-tablet' : '', " ").concat(blockonsHideOnMobile ? 'hide-on-mobile' : '')) : className;
    return /*#__PURE__*/React.createElement(BlockListBlock, visibility_extends({}, props, {
      className: newClassnames
    }));
  };
}, 'blockonsAddEditorVisibilityAttributes');
/**
 * Add Classes to the blocks on the Frontend
 */
var blockonsAddFrontendVisibilityAttributes = function blockonsAddFrontendVisibilityAttributes(extraProps, blockType, attributes) {
  var blockonsHideOnDesktop = attributes.blockonsHideOnDesktop,
    blockonsHideOnTablet = attributes.blockonsHideOnTablet,
    blockonsHideOnMobile = attributes.blockonsHideOnMobile;
  var name = blockType.name;
  var showVisibilitySettings = allowedVisibilityBlockTypes.includes(name);
  if (!visibility_isPremium || !visibilityEnabled) {
    return extraProps;
  }
  if (showVisibilitySettings) {
    extraProps.className = classnames_default()(extraProps.className, {
      'hide-on-desktop': blockonsHideOnDesktop,
      'hide-on-tablet': blockonsHideOnTablet,
      'hide-on-mobile': blockonsHideOnMobile
    });
  }
  return extraProps;
};

/**
 * WP Editor Hooks
 */
visibility_addFilter('blocks.registerBlockType', 'blockons/block-visibility-attributes', blockonsAddVisibilityAttributes);
visibility_addFilter('editor.BlockEdit', 'blockons/block-visibility-controls', blockonsAddInspectorVisibilityControls);
visibility_addFilter('blocks.getSaveContent.extraProps', 'blockons/block-visibility-frontend-clases', blockonsAddFrontendVisibilityAttributes);
visibility_addFilter('editor.BlockListBlock', 'blockons/block-visibility-editor-clases', blockonsAddEditorVisibilityAttributes);
;// ./src/backend/editor/extensions/animations.js
var animations_blockonsEditorObj$bl, _blockonsEditorObj$bl2;
function animations_extends() { return animations_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, animations_extends.apply(null, arguments); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }

var animations_ = wp.i18n.__;
var animations_addFilter = wp.hooks.addFilter;
var animations_lodash = lodash,
  animations_assign = animations_lodash.assign,
  animations_merge = animations_lodash.merge;
var animations_createHigherOrderComponent = wp.compose.createHigherOrderComponent;
var animations_isPremium = Boolean(blockonsEditorObj.isPremium);
var animationsEnabled = Boolean((animations_blockonsEditorObj$bl = blockonsEditorObj.blockonsOptions) === null || animations_blockonsEditorObj$bl === void 0 || (animations_blockonsEditorObj$bl = animations_blockonsEditorObj$bl.blockanimation) === null || animations_blockonsEditorObj$bl === void 0 ? void 0 : animations_blockonsEditorObj$bl.enabled) || false;
var animationsSettings = (_blockonsEditorObj$bl2 = blockonsEditorObj.blockonsOptions) === null || _blockonsEditorObj$bl2 === void 0 ? void 0 : _blockonsEditorObj$bl2.blockanimation;
var allowedAnimationBlockTypes = ['core/group', 'core/columns', 'core/column'];

/**
 * Add New Attributes to all blocks
 */
function blockonsAddAnimationAttributes(settings, name) {
  if (!animations_isPremium || !animationsEnabled) return settings;

  // console.log({ settings, name });
  var showAnimationSettings = allowedAnimationBlockTypes.includes(name);
  var blockAnimationAtts = showAnimationSettings ? {
    blockonsEnableAnimation: {
      type: 'boolean',
      "default": false
    },
    blockonsAnimation: {
      type: 'string',
      "default": (animationsSettings === null || animationsSettings === void 0 ? void 0 : animationsSettings.default_style) || 'fade'
    },
    blockonsAnimationDirection: {
      type: 'string',
      "default": (animationsSettings === null || animationsSettings === void 0 ? void 0 : animationsSettings.default_direction) || '-up'
    },
    blockonsAnimationDuration: {
      type: 'number',
      "default": (animationsSettings === null || animationsSettings === void 0 ? void 0 : animationsSettings.default_duration) || 850
    },
    blockonsAnimationDelay: {
      type: 'number',
      "default": (animationsSettings === null || animationsSettings === void 0 ? void 0 : animationsSettings.default_delay) || 50
    },
    blockonsAnimationOffset: {
      type: 'number',
      "default": (animationsSettings === null || animationsSettings === void 0 ? void 0 : animationsSettings.default_offset) || 80
    },
    blockonsAnimationOnce: {
      type: 'boolean',
      "default": (animationsSettings === null || animationsSettings === void 0 ? void 0 : animationsSettings.default_animate_once) || false
    },
    blockonsAnimationMirror: {
      type: 'boolean',
      "default": (animationsSettings === null || animationsSettings === void 0 ? void 0 : animationsSettings.default_mirror) || false
    }
  } : {};
  return animations_assign({}, settings, {
    attributes: animations_merge(settings.attributes, blockAnimationAtts)
  });
}
/**
 * Add New Controls to all blocks
 */
var blockonsAddAnimationInspectorControls = animations_createHigherOrderComponent(function (BlockEdit) {
  return function (props) {
    var Fragment = wp.element.Fragment;
    var _wp$blockEditor = wp.blockEditor,
      InspectorControls = _wp$blockEditor.InspectorControls,
      BlockControls = _wp$blockEditor.BlockControls;
    var _wp$components = wp.components,
      PanelBody = _wp$components.PanelBody,
      ToggleControl = _wp$components.ToggleControl,
      RangeControl = _wp$components.RangeControl,
      SelectControl = _wp$components.SelectControl;
    var _props$attributes = props.attributes,
      blockonsEnableAnimation = _props$attributes.blockonsEnableAnimation,
      blockonsAnimation = _props$attributes.blockonsAnimation,
      blockonsAnimationDirection = _props$attributes.blockonsAnimationDirection,
      blockonsAnimationDuration = _props$attributes.blockonsAnimationDuration,
      blockonsAnimationDelay = _props$attributes.blockonsAnimationDelay,
      blockonsAnimationOffset = _props$attributes.blockonsAnimationOffset,
      blockonsAnimationOnce = _props$attributes.blockonsAnimationOnce,
      blockonsAnimationMirror = _props$attributes.blockonsAnimationMirror,
      setAttributes = props.setAttributes,
      name = props.name;
    var showAnimationSettings = allowedAnimationBlockTypes.includes(name);
    if (!animations_isPremium || !animationsEnabled) {
      return /*#__PURE__*/React.createElement(BlockEdit, props);
    }
    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), showAnimationSettings && /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: animations_('Block Animations', 'blockons'),
      initialOpen: false
    }, /*#__PURE__*/React.createElement(UI_BlockonsNote, {
      imageUrl: "",
      title: animations_('Using Block Animations', 'blockons'),
      text: animations_('Add scroll animations for layout blocks in your WordPress editor. For more information, click the link below.', 'blockons'),
      docLink: "https://blockons.com/documentation/block-animations-on-scroll/"
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      checked: blockonsEnableAnimation,
      label: animations_('Enable Block Animations', 'blockons'),
      onChange: function onChange(newValue) {
        setAttributes({
          blockonsEnableAnimation: newValue
        });
      }
    }), blockonsEnableAnimation && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(SelectControl, {
      label: "Style",
      value: blockonsAnimation,
      options: [{
        label: 'Fade',
        value: 'fade'
      }, {
        label: 'Slide',
        value: 'slide'
      }, {
        label: 'Flip',
        value: 'flip'
      }, {
        label: 'Zoom In',
        value: 'zoom-in'
      }, {
        label: 'Zoom Out',
        value: 'zoom-out'
      }],
      onChange: function onChange(newAnimationStyle) {
        setAttributes({
          blockonsAnimation: newAnimationStyle
        });
      }
    }), /*#__PURE__*/React.createElement(SelectControl, {
      label: "Direction",
      value: blockonsAnimationDirection,
      options: [{
        label: 'Up',
        value: '-up'
      }, {
        label: 'Down',
        value: '-down'
      }, {
        label: 'Left',
        value: '-left'
      }, {
        label: 'Right',
        value: '-right'
      }].concat(_toConsumableArray(blockonsAnimation === 'zoom-in' || blockonsAnimation === 'zoom-out' ? [{
        label: 'None',
        value: ''
      }] : []), _toConsumableArray(blockonsAnimation === 'fade' ? [{
        label: 'Up Right',
        value: '-up-right'
      }, {
        label: 'Up Left',
        value: '-up-left'
      }, {
        label: 'Down Right',
        value: '-down-right'
      }, {
        label: 'Down Left',
        value: '-down-left'
      }] : [])),
      onChange: function onChange(newAnimationDirection) {
        setAttributes({
          blockonsAnimationDirection: newAnimationDirection
        });
      }
    }), /*#__PURE__*/React.createElement(RangeControl, {
      label: animations_('Duration', 'blockons'),
      value: parseInt(blockonsAnimationDuration),
      onChange: function onChange(newDurationValue) {
        return setAttributes({
          blockonsAnimationDuration: parseInt(newDurationValue)
        });
      },
      min: 50,
      max: 3000,
      step: 50,
      help: animations_('How long the animation takes - 1000 = 1 second', 'blockons')
    }), /*#__PURE__*/React.createElement(RangeControl, {
      label: animations_('Delay', 'blockons'),
      value: parseInt(blockonsAnimationDelay),
      onChange: function onChange(newDelayValue) {
        return setAttributes({
          blockonsAnimationDelay: parseInt(newDelayValue)
        });
      },
      min: 0,
      max: 3000,
      step: 50,
      help: animations_('The milliseconds before the animation starts - 1000 = 1 second', 'blockons')
    }), /*#__PURE__*/React.createElement(RangeControl, {
      label: animations_('Offset', 'blockons'),
      value: parseInt(blockonsAnimationOffset),
      onChange: function onChange(newOffsetValue) {
        return setAttributes({
          blockonsAnimationOffset: parseInt(newOffsetValue)
        });
      },
      min: 0,
      max: 600,
      step: 5,
      help: animations_('The offset in pixels from the bottom of the window', 'blockons')
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      checked: blockonsAnimationOnce,
      label: animations_('Animate Once', 'blockons'),
      onChange: function onChange(newValue) {
        setAttributes({
          blockonsAnimationOnce: newValue
        });
      },
      help: animations_('Whether animation should happen only once - while scrolling down', 'blockons')
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      checked: blockonsAnimationMirror,
      label: animations_('Mirror Animations', 'blockons'),
      onChange: function onChange(newValue) {
        setAttributes({
          blockonsAnimationMirror: newValue
        });
      },
      help: animations_('Whether elements should animate out while scrolling past them', 'blockons')
    })))));
  };
}, 'blockonsAddAnimationInspectorControls');
/**
 * Add CSS Classes to the blocks in the editor
 */
var blockonsAddEditorAnimationAttributes = animations_createHigherOrderComponent(function (BlockListBlock) {
  return function (props) {
    var _props$attributes2 = props.attributes,
      blockonsEnableAnimation = _props$attributes2.blockonsEnableAnimation,
      blockonsAnimation = _props$attributes2.blockonsAnimation,
      blockonsAnimationDirection = _props$attributes2.blockonsAnimationDirection,
      blockonsAnimationDuration = _props$attributes2.blockonsAnimationDuration,
      blockonsAnimationDelay = _props$attributes2.blockonsAnimationDelay,
      blockonsAnimationOffset = _props$attributes2.blockonsAnimationOffset,
      blockonsAnimationOnce = _props$attributes2.blockonsAnimationOnce,
      blockonsAnimationMirror = _props$attributes2.blockonsAnimationMirror,
      className = props.className,
      name = props.name;
    var showAnimationSettings = allowedAnimationBlockTypes.includes(name);
    if (!animations_isPremium || !animationsEnabled || !showAnimationSettings) {
      return /*#__PURE__*/React.createElement(BlockListBlock, props);
    }
    var newWrapperProps = blockonsEnableAnimation ? {
      'data-aos': "".concat(blockonsAnimation).concat(blockonsAnimationDirection),
      'data-aos-easing': 'ease-in-out',
      'data-aos-duration': blockonsAnimationDuration,
      'data-aos-delay': blockonsAnimationDelay,
      'data-aos-offset': blockonsAnimationOffset,
      'data-aos-once': blockonsAnimationOnce,
      'data-aos-mirror': blockonsAnimationMirror
    } : {};
    return /*#__PURE__*/React.createElement(BlockListBlock, animations_extends({}, props, {
      wrapperProps: newWrapperProps // ANIMATIONS
    }));
  };
}, 'blockonsAddEditorAnimationAttributes');
/**
 * Add Classes to the blocks on the Frontend
 */
var blockonsAddFrontendAnimationAttributes = function blockonsAddFrontendAnimationAttributes(extraProps, blockType, attributes) {
  var blockonsEnableAnimation = attributes.blockonsEnableAnimation,
    blockonsAnimation = attributes.blockonsAnimation,
    blockonsAnimationDirection = attributes.blockonsAnimationDirection,
    blockonsAnimationDuration = attributes.blockonsAnimationDuration,
    blockonsAnimationDelay = attributes.blockonsAnimationDelay,
    blockonsAnimationOffset = attributes.blockonsAnimationOffset,
    blockonsAnimationOnce = attributes.blockonsAnimationOnce,
    blockonsAnimationMirror = attributes.blockonsAnimationMirror;
  var name = blockType.name;
  var showAnimationSettings = allowedAnimationBlockTypes.includes(name);
  if (!animations_isPremium || !animationsEnabled || !showAnimationSettings) {
    return extraProps;
  }
  if (blockonsEnableAnimation) {
    extraProps['data-aos'] = "".concat(blockonsAnimation).concat(blockonsAnimationDirection);
    extraProps['data-aos-easing'] = 'ease-in-out';
    extraProps['data-aos-duration'] = blockonsAnimationDuration;
    extraProps['data-aos-delay'] = blockonsAnimationDelay;
    extraProps['data-aos-offset'] = blockonsAnimationOffset;
    extraProps['data-aos-once'] = blockonsAnimationOnce;
    extraProps['data-aos-mirror'] = blockonsAnimationMirror;
  }
  return extraProps;
};

/**
 * WP Editor Hooks
 */
animations_addFilter('blocks.registerBlockType', 'blockons/block-animations-attributes', blockonsAddAnimationAttributes);
animations_addFilter('editor.BlockEdit', 'blockons/block-animations-controls', blockonsAddAnimationInspectorControls);
animations_addFilter('blocks.getSaveContent.extraProps', 'blockons/block-animations-frontend-classes', blockonsAddFrontendAnimationAttributes);
animations_addFilter('editor.BlockListBlock', 'blockons/block-animations-editor-classes', blockonsAddEditorAnimationAttributes);
;// ./src/backend/editor/extensions/quickview.js
var _blockonsEditorObj;
var quickview_addFilter = wp.hooks.addFilter;
var Fragment = wp.element.Fragment;
var quickview_ = wp.i18n.__;
var InspectorControls = wp.blockEditor.InspectorControls;
var PanelBody = wp.components.PanelBody;
var quickview_createHigherOrderComponent = wp.compose.createHigherOrderComponent;

var wcAllowedProducts = ['woocommerce/product-collection', 'woocommerce/handpicked-products', 'woocommerce/product-best-sellers', 'woocommerce/product-category', 'woocommerce/product-new', 'woocommerce/product-on-sale', 'woocommerce/product-tag', 'woocommerce/product-top-rated', 'woocommerce/products'];
var quickview_isPremium = Boolean(blockonsEditorObj.isPremium);
var qvEnabled = ((_blockonsEditorObj = blockonsEditorObj) === null || _blockonsEditorObj === void 0 || (_blockonsEditorObj = _blockonsEditorObj.blockonsOptions) === null || _blockonsEditorObj === void 0 || (_blockonsEditorObj = _blockonsEditorObj.quickview) === null || _blockonsEditorObj === void 0 ? void 0 : _blockonsEditorObj.enabled) || false;

// 1. Create a Higher Order Component to add the controls in the Inspector Sidebar
var blockonsQuickviewSettings = quickview_createHigherOrderComponent(function (BlockEdit) {
  return function (props) {
    var showOnBlock = wcAllowedProducts.includes(props.name);
    if (!quickview_isPremium || !qvEnabled || !showOnBlock) return /*#__PURE__*/React.createElement(BlockEdit, props); // Return if not the desired block

    return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: "Product Quick View",
      initialOpen: true
    }, /*#__PURE__*/React.createElement(UI_BlockonsNote, {
      text: quickview_('Configure Quick View for these products.', 'blockons'),
      upgradeLink: "".concat(blockonsEditorObj.adminUrl, "options-general.php?page=blockons-settings"),
      upgradeText: quickview_('Go to Settings', 'blockons'),
      noline: true
    }))));
  };
}, 'blockonsQuickviewSettings');

/**
 * WP Editor Hooks
 */
quickview_addFilter('editor.BlockEdit', 'blockons/quickview-settings', blockonsQuickviewSettings);
;// ./src/backend/editor/editor.js






})();

/******/ })()
;