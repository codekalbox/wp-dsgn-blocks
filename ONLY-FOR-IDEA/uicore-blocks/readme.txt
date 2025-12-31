=== UiCore Blocks - Free WordPress Gutenberg Blocks ===
Contributors: uicore
Tags: editor, gutenberg blocks, blocks 
Requires at least: 5.8
Requires PHP: 7.4
Tested up to: 6.9.0
Stable tag: 1.0.10
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gutenberg on Steroids! Extend your editor with powerful, flexible, and modular blocks to unlock limitless design possibilities.

== Description ==

UiCore Blocks extends the WordPress Gutenberg editor with **flexible** and **performant** blocks, empowering users to create modern and visually stunning websites effortlessly.

---

## Features
- **Flexible and Modular Design**  
  Each block includes common responsive settings along with specific customization options, such as:
  - Display and layout options
  - Backgrounds with layered effects
  - Shadows with layer controls
  - Filters and transforms
  - Custom attributes
  - Custom cursors
  - Animations and transitions
  - Advanced Typography controlls
  ...and more

- **Responsive Controls**  
  Easily customize any element of a block using an intuitive set of responsive controls for fine-tuned adjustments.

- **Animations Support**  
  Integrates seamlessly with **UiCore Animate** (free plugin) to add entrance and scroll animations.

- **Block Presets** *(Coming Early 2025)*  
  Pre-designed block presets will simplify the process for beginners, enabling quick setups while retaining flexibility for advanced users.
## 🚀 PRO Widgets

**All PRO widgets are available with [UiCore PRO](https://uicore.pro/) theme, unlocking powerful WooCommerce functionalities.**

[youtube https://www.youtube.com/watch?v=xlAEUTuZWAg]
---

## Who is it For?
- **Professionals and Designers** who want to leverage Gutenberg to its fullest potential.  
- **Beginners** who will benefit from ready-to-use block presets (*coming early 2025*).

---

## Requirements
- WordPress 5.8 or higher
- PHP 7.4 or higher
- Recommended: **UiCore Animate** for animation features

---

## Usage
1. After activation, go to the Gutenberg editor.
2. Add new blocks from the "UiCore Blocks" category.
3. Use the responsive controls and customization settings to style blocks as desired.

---

## Support
For any issues, questions, or feedback, visit our [support forum](https://wordpress.org/support/plugin/uicore-blocks/).

---

## Feedback
Love **UiCore Blocks**? Leave us a review in the [WordPress Plugin Repository](https://wordpress.org/plugins/uicore-blocks/)!


== Installation ==

1. Download the plugin or install it directly from the WordPress Plugin Repository.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. *(Optional)* Install **UiCore Animate** (free) if you plan to use animations.


== Changelog ==
= 1.0.10 =
* NEW - Added WooCommerce support
* NEW - Products Query Loop
* FIX - Custom fonts weight selection
* FIX - Video Block icon size
* FIX - Tabs content display grid issues
* FIX - Global Styles for buttons
 
= 1.0.9 =
* NEW - Added Animated Background in Container Block (via UiCore Animate)
* FIX - Blocks inputs unit mesure bug
* UPD - WordPress 6.9.0 compatibility

= 1.0.8 =
* FIX - Resolved issue with font loading in certain environments
* UPD - Enhanced error handling in page save process

= 1.0.7 =
* NEW - Added Adobe TypeKit and Custom Fonts support with dynamic font loading
* NEW - Implemented Shape Divider control for Container block
* NEW - Implemented Transform options (translate, rotate, scale) for text formatting (headings, paragraphs, etc.)
* NEW - Implemented Font Reset button for Global settings
* UPD - Updated desync functionality: desynced blocks now ignore global CSS from synced counterparts
* UPD - Optimized Image Controller for performance and cleaner code
* UPD - Improved other blocks plugin compatibility
* FIX - Fixed pattern saving issue in WordPress 6.8.3
* FIX - Fixed align-self bug on Buttons
* FIX - Fixed dynamic title rendering in Header and other sections
* FIX - Fixed container sticky position and grid column span behavior
* FIX - Fixed hover glow border issue and removed from unnecessary blocks
* FIX - Fixed newsletter active state handling
* FIX - Fixed “Save Changes” disable state in Global settings
* FIX - Fixed copy/paste attributes issue in composite blocks
* FIX - Input values now persist correctly when changing units
* FIX - Implemented hide video controller functionality
* FIX - Added missing “Add Item” button for Navigation Tabs

= 1.0.6 =
* FIX - Container presets issue
* FIX - Editor styles issues cause by compatibility older Gutenberg extensions

= 1.0.5 =
* NEW - Query Loop Block
* NEW - Implemented block categories: Basic, Composite, Dynamic, and Advanced
* NEW - Added Mask option applicable to all blocks (not only images)
* NEW - Implemented block transform feature (from Composite to Advanced and from Grids to Carousel)
* NEW - Added transform Scale 3D option
* NEW - Added error handling for Google Maps when API Key is missing
* NEW - Added placeholder for Button (fixed issue in Icon Card Grid)
* NEW - Added Edit Gallery button to simplify items edit
* NEW - Added video/image URL support for external sources
* UPD - Dynamic URL support for URL Controller
* UPD - Dynamic Content formatting updated so prefix/suffix appears outside links
* FIX - Fixed typography style inheritance on parent elements
* FIX - Fixed Nav Tab item duplication issue

= 1.0.4 =
* NEW - Align Self & Order controls for flex children
* NEW - Expanded controls for background images and gradients
* UPD - Improved video laoding perfromance
* UPD - Updated Design Cloud to the latest version (fixes connection cache handling)
* UPD - Performance improvements in editor
* FIX - Setting `border: none` now correctly overrides default CSS borders
* FIX - Resolved Swiper Slides conflicts with certain settings
* FIX - PHP 7.4 compatibility issues

= 1.0.3 =
* FIX - CustomCSS error without UiCore Framework
* FIX - Team Card social icons bug
* FIX - Max Width not working on some blocks

= 1.0.2 =
* NEW - Ai Generator Block (beta) (PRO)
* NEW - Google Maps Block with two modes: Location and Markers (markers require API KEY)
* NEW - Added Custom CSS support across blocks (PRO)
* NEW - Introduced Design Cloud integration (PRO)
* NEW - Added support for Dynamic Content and Dynamic Links
* UPD - Improved desync system to preserve synced values and perform cleaner style resets
* UPD - Enhanced Mask functionality with new controls for size, placement, and more
* UPD - Updated all block descriptions and added new preview images
* UPD - Removed display styles (gap, align, justify) from carousel blocks for a cleaner baseline layout
* FIX - Fixed text formatter font weight rendering issue  
* FIX - Fixed Custom Fonts rendering in editor view  
* FIX - Fixed Testimonial Card padding bug
* FIX - Fixed image animation delay issue
* FIX - Fixed Advanced Tabs navigation items not inheriting active state in frontend
* FIX - Fixed global buttons shadow rendering issue
* FIX - Fixed desynced styles missing in frontend
* FIX - Fixed paragraph global styles inside the editor
* FIX - Fixed quick actions sidebar bug in Modal block
* FIX - Fixed Input Group breaking when clearing unlinked values
* FIX - Other small bugs fixes

= 1.0.1 =
* NEW - Added Card Icon support for Card widgets: now cards can have both a main card icon and a normal icon, which can be synced
* NEW - Added Advanced Custom Carousel and Advanced Card Grid blocks (PRO)
* NEW - Inline Image in richtexts with more advanced controlls
* NEW - Added Copy/Paste on each section from Settings
* UPD - Improved block selection with structure outline on hover and colored highlight on active item
* UPD - Improved Swiper animations and fixed multiple related bugs
* UPD - Implemented FadeAmount and FadeOpacity controls for carousel fade edges
* UPD - Updated allowed blocks for Advanced Testimonial Card and Advanced Icon Card for better flexibility
* UPD - Changed placeholder text for multiple widgets based on updated UX guidelines
* UPD - Improved empty container outline behavior for unselected one-column layouts
* FIX - Fixed active color hover issue
* FIX - Fixed video cover image not spanning full width on low height settings
* FIX - Fixed carousel width to 100% and corrected grab cursor behavior
* FIX - Fixed typo in carousel settings
* FIX - Fixed copy/paste plugin bug causing unexpected style transfer behavior on synced blocks

= 1.0.0 = 
* Production Ready Release

= 0.0.2 =
* NEW - Testimonial Card Block
* NEW - Testimonial Card Grid Block
* NEW - Counter block
* NEW - Counter and Highlight effects in text blocks
* NEW - Container and Images Visual resize support
* UPD - Css Performance Improvements

= 0.0.1 =
* Initial release