# FlexBlocks Layout Builder

Advanced Gutenberg blocks for flexible layout building with complete flexbox control.

## Description

FlexBlocks Layout Builder adds powerful, flexible layout blocks to the WordPress block editor (Gutenberg). Create sophisticated page layouts with complete control over flexbox properties, spacing, backgrounds, and responsive behavior.

## Features

### Section Block
- **Complete Flexbox Control**: Direction, justify-content, align-items, flex-wrap, and gap
- **Flexible Spacing**: Individual padding and margin controls for each side
- **Rich Backgrounds**: Color with opacity, images with position/size/repeat controls, and CSS gradients
- **Border & Effects**: Individual border controls, corner radius, and box shadow
- **Layout Options**: Content width (full/boxed), minimum height, HTML tag selection
- **Advanced Options**: Custom CSS classes, ID, z-index, and overflow control

### Columns Block
- **Flexible Layouts**: 1-6 columns with preset layouts (50/50, 33/33/33, 25/75, etc.)
- **Custom Widths**: Set individual column widths using percentages or pixels
- **Vertical Alignment**: Top, center, bottom, or stretch
- **Column Styling**: Individual background colors and padding for each column
- **Responsive Design**: Mobile stacking with optional reverse order
- **Gap Control**: Customizable spacing between columns

## Installation

### From WordPress Dashboard
1. Navigate to **Plugins → Add New**
2. Search for "FlexBlocks Layout Builder"
3. Click "Install Now" and then "Activate"
4. Blocks will appear in the block inserter under "FlexBlocks" category

### Manual Installation
1. Download the plugin ZIP file
2. Navigate to **Plugins → Add New → Upload Plugin**
3. Select the downloaded ZIP file and click "Install Now"
4. Click "Activate Plugin"

### Development Installation
```bash
# Clone the repository
git clone https://github.com/codekalbox/wp-dsgn-blocks.git
cd wp-dsgn-blocks

# Install dependencies
npm install

# Build for production
npm run build

# Or start development mode with watch
npm run start
```

## Requirements

- **WordPress**: 6.0 or higher
- **PHP**: 7.4 or higher
- **Node.js**: 16.0 or higher (for development)
- **npm**: 8.0 or higher (for development)

## Usage

### Section Block

1. Add a new block in the editor
2. Search for "Section" under the FlexBlocks category
3. Configure layout, flexbox, spacing, background, and border options in the sidebar
4. Add any other blocks inside the Section block

**Example Use Cases:**
- Hero sections with centered content
- Feature sections with custom backgrounds
- Call-to-action boxes with specific spacing
- Container sections for organizing page layout

### Columns Block

1. Add a new block in the editor
2. Search for "Columns" under the FlexBlocks category
3. Choose number of columns (1-6) or select a preset layout
4. Add blocks inside each column
5. Customize individual column styling in the sidebar

**Example Use Cases:**
- Multi-column layouts for content organization
- Sidebar layouts (25/75 or 75/25)
- Grid layouts for features or services
- Responsive layouts that stack on mobile

## Development

### Build Commands

```bash
# Install dependencies
npm install

# Start development mode (with file watching)
npm run start

# Build for production (minified)
npm run build

# Lint JavaScript files
npm run lint:js

# Lint CSS/SCSS files
npm run lint:css

# Format code
npm run format

# Update WordPress packages
npm run packages-update
```

### File Structure

```
flexblocks-layout-builder/
├── flexblocks-layout-builder.php    # Main plugin file
├── readme.txt                        # WordPress.org readme
├── package.json                      # npm dependencies and scripts
├── assets/
│   ├── css/
│   │   ├── editor.css               # Global editor styles
│   │   └── style.css                # Global frontend styles
│   └── js/                          # (Reserved for future use)
├── build/                           # Compiled assets (generated)
│   ├── section/
│   └── columns/
├── src/                             # Source files
│   ├── section/
│   │   ├── block.json              # Block metadata
│   │   ├── edit.js                 # Editor component
│   │   ├── save.js                 # Frontend component
│   │   ├── index.js                # Block registration
│   │   ├── style.scss              # Frontend styles
│   │   └── editor.scss             # Editor styles
│   ├── columns/
│   │   └── (same structure as section)
│   ├── components/                  # Shared React components
│   ├── utils/
│   │   └── styles.js               # Style generation utilities
│   └── index.js                    # Main entry point
└── includes/
    └── register-blocks.php          # Block registration (PHP)
```

## Browser Support

FlexBlocks supports all modern browsers:
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)

## Compatibility

- ✅ WordPress 6.0+
- ✅ PHP 7.4+
- ✅ Full Site Editing (FSE) themes
- ✅ Classic themes with Gutenberg support
- ✅ Multisite installations
- ✅ Translation ready (i18n)

## Frequently Asked Questions

### Can I use these blocks with my theme?
Yes! FlexBlocks works with any WordPress theme that supports the block editor (Gutenberg). It's also fully compatible with Full Site Editing themes.

### Will this slow down my site?
No. FlexBlocks is built with performance in mind, using optimized code and efficient CSS generation. Styles are only loaded when blocks are used on a page.

### Can I nest Section blocks inside other Section blocks?
Yes! You can nest Section blocks and combine them with Columns blocks to create complex layouts.

### Are the blocks responsive?
Yes, all blocks are built with mobile-first responsive design. The Columns block includes specific responsive controls for mobile stacking and breakpoint management.

### Can I export and reuse my layouts?
Yes! You can create reusable block patterns using WordPress's pattern system, allowing you to save and reuse entire layouts.

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Use ESLint and Prettier for code formatting
- Add inline documentation for all functions and components

## Changelog

### 1.0.0
- Initial release
- Added Section Block with complete flexbox controls
- Added Columns Block with responsive features
- Background, border, and spacing controls
- Mobile-first responsive design
- Full Site Editing compatibility

## Credits

- Built with [@wordpress/scripts](https://www.npmjs.com/package/@wordpress/scripts)
- Developed using [@wordpress/block-editor](https://www.npmjs.com/package/@wordpress/block-editor)
- Follows WordPress block development best practices

## License

This plugin is licensed under the GPL v2 or later.

```
FlexBlocks Layout Builder
Copyright (C) 2024 CodeKalbox

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## Support

For support, feature requests, or bug reports:
- GitHub Issues: [https://github.com/codekalbox/wp-dsgn-blocks/issues](https://github.com/codekalbox/wp-dsgn-blocks/issues)
- WordPress Support Forum: (coming soon)

## Author

**CodeKalbox**
- GitHub: [@codekalbox](https://github.com/codekalbox)

