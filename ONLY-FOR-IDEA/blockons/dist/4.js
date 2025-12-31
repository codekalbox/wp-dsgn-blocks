"use strict";
(self["webpackChunkblockons"] = self["webpackChunkblockons"] || []).push([[4],{

/***/ 1004:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createTooltipComponent: () => (/* binding */ createTooltipComponent),
/* harmony export */   initializeTooltips: () => (/* binding */ initializeTooltips)
/* harmony export */ });
/* harmony import */ var _backend_helpers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(3772);

var isPremium = Boolean(blockonsFrontendObj.isPremium);
function createTooltipComponent(theme, title, text, image, pcolor, pfcolor) {
  var tooltipComponent = document.createElement("div");
  tooltipComponent.classList.add("blockons-tooltip");
  if (isPremium && theme === "custom") {
    tooltipComponent.style.backgroundColor = pcolor;
    tooltipComponent.style.color = pfcolor;
  }
  if (isPremium && tooltipComponent) {
    tooltipComponent === null || tooltipComponent === void 0 || tooltipComponent.classList.add(theme);
  } else {
    tooltipComponent === null || tooltipComponent === void 0 || tooltipComponent.classList.add("one");
  }
  tooltipComponent.innerHTML = "\n\t\t<div class=\"blockons-tooltip-popup\">\n\t\t\t".concat(isPremium && image ? "<img src=\"".concat(image, "\" alt=\"").concat(title, "\" />") : "", "\n\n\t\t\t<h6 class=\"blockons-tooltip-title\">").concat(title, "</h6>\n\t\t\t<p class=\"blockons-tooltip-text\">").concat(isPremium ? (0,_backend_helpers__WEBPACK_IMPORTED_MODULE_0__/* .blockonsStringReplaceForLink */ .ar)(text) : text, "</p>\n\n\t\t\t<span class=\"blockons-tooltip-angle\" ").concat(isPremium && theme === "custom" ? "style='border-top-color: " + pcolor + "'" : "", "></span>\n\t\t</div>");
  return tooltipComponent;
}
function initializeTooltips() {
  var tooltipDefaults = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var blockonsTooltips = document.querySelectorAll(".blockons-inline-tooltip");
  if (blockonsTooltips.length > 0) {
    blockonsTooltips.forEach(function (tooltip) {
      var style = tooltip.getAttribute("data-style") || tooltipDefaults.style;
      var theme = tooltip.getAttribute("data-theme") || tooltipDefaults.theme;
      var title = tooltip.getAttribute("data-title");
      var text = tooltip.getAttribute("data-text");
      var icon = tooltip.getAttribute("data-icon") || "";
      var image = tooltip.getAttribute("data-image") || "";
      var color = tooltip.getAttribute("data-color") || tooltipDefaults.color;
      var fcolor = tooltip.getAttribute("data-fcolor") || tooltipDefaults.fcolor;
      var pcolor = tooltip.getAttribute("data-pcolor") || tooltipDefaults.pcolor;
      var pfcolor = tooltip.getAttribute("data-pfcolor") || tooltipDefaults.pfcolor;
      if (title || text) {
        var tooltipComponent = createTooltipComponent(theme, title, text, image, pcolor, pfcolor);
        tooltip.append(tooltipComponent);
        tooltip.classList.add(style);
        if (isPremium && fcolor) tooltip.style.color = fcolor;
        if (isPremium && style !== "highlight" && color) tooltip.style.borderBottomColor = color;
        if (isPremium && icon) {
          tooltip.classList.add("icon");
          var tooltipIcon = document.createElement("span");
          tooltipIcon.classList.add("tooltip-icon", icon.split(" ")[0], icon.split(" ")[1]);
          if (color) tooltipIcon.style.color = color;
          tooltip.appendChild(tooltipIcon);
        }
        tooltip.addEventListener("mouseover", function () {
          tooltip.classList.add("active");
        });
        tooltip.addEventListener("mouseout", function () {
          tooltip.classList.remove("active");
        });
      }
    });
  }
}

/***/ })

}]);