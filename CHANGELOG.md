# Changelog

All notable changes to AnylabelWP will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.0.3] - 2025-01-XX (Pending Release)

### Added
- **Enhanced Logo Selection Interface**: Replaced simple text inputs with comprehensive logo selection components
- **Default Logo Images**: Added 4 custom PNG logos for different plugin categories (Forms, CRM, SMTP, Social)
- **Media Library Integration**: Full WordPress Media Library integration for custom logo uploads
- **Visual Logo Preview**: Real-time preview of selected logos
- **Category-Specific Defaults**: Each module shows relevant default images (e.g., CRM module shows CRM-related logos)
- New `logo-selector.js` component with media uploader integration (pure vanilla JavaScript, no jQuery)
- New `assets/images/defaults/` directory with custom AnylabelWP PNG logos
- Centralized `render_logo_selector()` method in the Loader class

### Changed
- **Updated All Modules**: Fluent SMTP, Fluent CRM, Fluent Forms, and WP Social Ninja now use new logo selector
- **Enhanced User Experience**: Visual selection instead of manual URL entry
- **Improved CSS**: Professional styling for logo selection interfaces
- **Better Default Options**: Users can quickly select appropriate logos without searching
- **No jQuery Dependency**: Complete rewrite to vanilla JavaScript for better performance

### Technical Improvements
- Modular logo selector component architecture
- Enhanced JavaScript with proper WordPress media library integration
- Structured system for managing and serving default images
- Professional UI consistent with WordPress admin design patterns
- Pure vanilla JavaScript implementation without jQuery dependencies

### Backwards Compatibility
- ✅ Existing logo URLs remain functional
- ✅ Database settings maintain same option names
- ✅ No breaking changes to existing configurations

## [0.0.2] - 2025-09-02

### Added
- Internationalization (i18n) support with text domain
- Plugin action links (Settings link on plugins page)
- Plugin meta links (GitHub, Support, Documentation)
- System requirements checking (PHP/WordPress versions)
- Admin notices system for better user experience
- Activation notice with quick settings access
- Supported plugins detection and notifications
- Professional WordPress coding standards compliance
- Enhanced README with full documentation
- WordPress.org compatible readme.txt
- Languages directory structure
- Version checking and compatibility warnings

### Changed
- Improved plugin architecture for better maintainability
- Enhanced security with proper nonce verification
- Better error handling throughout the codebase
- Optimized asset loading (CSS/JS only on relevant pages)
- Cleaner activation and deactivation processes

### Fixed
- Plugin deletion issues resolved
- Proper namespace usage consistency
- Asset loading performance improvements
- Security vulnerabilities addressed

### Security
- Added ABSPATH protection to all files
- Proper input sanitization and validation
- Secure nonce implementation
- Safe file inclusion practices

## [0.0.1] - 2024-09-08

### Added
- Initial plugin release
- Basic white-labeling for Fluent Forms
- Basic white-labeling for Fluent CRM
- Basic white-labeling for Fluent SMTP
- Basic white-labeling for WP Social Ninja
- Modular architecture
- Admin settings interface
- Custom CSS/JS per module
- Basic WordPress cleanup features

[0.0.2]: https://github.com/wpoperator/anylabelwp/compare/v0.0.1...v0.0.2
[0.0.1]: https://github.com/wpoperator/anylabelwp/releases/tag/v0.0.1
