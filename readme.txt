=== FlexBlocks Layout Builder ===
Contributors: codekalbox
Tags: gutenberg, blocks, layout, flexbox, columns
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced Gutenberg blocks for flexible layout building with complete flexbox control.

== Description ==

FlexBlocks Layout Builder adds powerful, flexible layout blocks to the WordPress block editor (Gutenberg). Create sophisticated page layouts with complete control over flexbox properties, spacing, backgrounds, and responsive behavior.

= Features =

**Section Block**
* Complete flexbox control (direction, justify, align, wrap, gap)
* Full spacing controls (padding and margin with individual sides)
* Background options (color with opacity, images, gradients)
* Border and shadow controls
* Minimum height and content width settings
* Custom HTML tag selection
* Advanced options (CSS classes, ID, z-index, overflow)

**Columns Block**
* Flexible column layouts (1-6 columns)
* Preset layout options (50/50, 33/33/33, 25/75, etc.)
* Custom width control per column
* Vertical alignment options
* Individual column styling (background, padding)
* Responsive controls with mobile stacking
* Column gap customization

= Use Cases =

* Create hero sections with flexible content alignment
* Build responsive multi-column layouts
* Design feature sections with custom backgrounds
* Construct complex page layouts without code
* Build landing pages with precise spacing control

= Technical Features =

* Modern WordPress block development with @wordpress/scripts
* Mobile-first responsive design
* Full Site Editing (FSE) compatible
* Clean, semantic HTML output
* Optimized CSS generation
* Translation ready (i18n)

== Installation ==

= From WordPress Dashboard =

1. Navigate to Plugins → Add New
2. Search for "FlexBlocks Layout Builder"
3. Click "Install Now" and then "Activate"
4. Blocks will appear in the block inserter under "FlexBlocks" category

= Manual Installation =

1. Download the plugin ZIP file
2. Navigate to Plugins → Add New → Upload Plugin
3. Select the downloaded ZIP file and click "Install Now"
4. Click "Activate Plugin"

= From Source =

1. Clone or download the repository
2. Run `npm install` to install dependencies
3. Run `npm run build` to build the assets
4. Upload to your WordPress plugins directory
5. Activate through the WordPress plugins screen

== Frequently Asked Questions ==

= What WordPress version is required? =

WordPress 6.0 or higher is required to use this plugin.

= Does this work with my theme? =

Yes! FlexBlocks works with any WordPress theme that supports the block editor (Gutenberg). It's also fully compatible with Full Site Editing themes.

= Can I use these blocks with other block plugins? =

Absolutely! FlexBlocks blocks work seamlessly alongside core WordPress blocks and other block plugins.

= Are the blocks responsive? =

Yes, all blocks are built with mobile-first responsive design. The Columns block includes specific responsive controls for mobile stacking and breakpoint management.

= Can I nest blocks inside the Section block? =

Yes! The Section block accepts any WordPress blocks as children, including the Columns block and other nested Sections.

= Will this slow down my site? =

No. FlexBlocks is built with performance in mind, using optimized code and efficient CSS generation. Styles are only loaded when blocks are used on a page.

= Is the plugin translation ready? =

Yes, FlexBlocks is fully translation ready and includes proper internationalization functions throughout.

== Screenshots ==

1. Section Block with flexbox controls in the sidebar
2. Columns Block with multiple column layouts
3. Background and border controls panel
4. Responsive controls for mobile layouts
5. Example hero section built with Section block
6. Complex multi-column layout example

== Changelog ==

= 1.0.0 =
* Initial release
* Added Section Block with complete flexbox controls
* Added Columns Block with responsive features
* Background, border, and spacing controls
* Mobile-first responsive design
* Full Site Editing compatibility

== Upgrade Notice ==

= 1.0.0 =
Initial release of FlexBlocks Layout Builder.

== Development ==

= Building from Source =

```bash
# Install dependencies
npm install

# Start development mode (with watch)
npm run start

# Build for production
npm run build

# Lint JavaScript
npm run lint:js

# Lint CSS
npm run lint:css
```

= Contributing =

Contributions are welcome! Please visit our GitHub repository to submit issues or pull requests.

== Credits ==

* Built with @wordpress/scripts and @wordpress/block-editor
* Developed with modern WordPress block development best practices
* Inspired by popular page builders but focused on native Gutenberg integration

== Support ==

For support, feature requests, or bug reports, please visit our GitHub repository or contact us through the WordPress.org support forum.

