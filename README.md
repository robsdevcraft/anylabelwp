![Image of Anylabel WP logo](/assets/images/defaults/anylabelwp.logo.png "AnylabelWP logo")

# AnylabelWP WordPress Plugin

A free, GPL licensed, open-source plugin to customize the appearance of 3rd-Party Plugins for a more unified and product-agnostic user experience.

## Description

AnylabelWP helps agencies, freelancers, and WAAS providers create a consistent, branded experience by white-labeling popular WordPress plugins. Remove branding, customize logos, and create a unified admin interface.

## Supported Plugins

- **Fluent Forms** (Free version) - Form builder and management
- **Fluent CRM** (Free version) - Customer relationship management  
- **Fluent SMTP** - Email delivery service
- **WP Social Ninja** (Free version) - Social media management

## Features

✅ **Custom Branding**: Replace plugin logos with your own or generic icons  
✅ **Menu Customization**: Rename menu items for consistency  
✅ **Clean Interface**: Remove upgrade prompts and promotional content  
✅ **Modular Design**: Enable/disable features per plugin  
✅ **WordPress Cleanup**: Remove WordPress branding elements  
✅ **Dashboard Optimization**: Clean up admin dashboard widgets  

## Installation

### From GitHub (Development)
1. Download the latest release from GitHub
2. Upload to `/wp-content/plugins/anylabelwp/`
3. Activate through WordPress admin
4. Configure settings under **Settings → AnylabelWP**

### Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- At least one supported plugin installed

## Configuration

1. **General Settings**: Configure user roles and global options
2. **Per-Plugin Settings**: Customize each supported plugin individually
3. **Upload Custom Logos**: Replace default branding with your own

## Who Should Use This

- **Website as a Service (WAAS)** providers
- **Agencies** managing multiple client sites  
- **Freelancers** creating custom solutions
- **Developers** building white-label products

## Development

### File Structure
```
anylabelwp/
├── anylabel.php              # Main plugin file
├── includes/                 # Core classes
├── modules/                  # Plugin-specific modules
├── assets/                   # CSS/JS files
├── views/                    # Admin templates
├── languages/                # Translation files
└── uninstall.php            # Clean uninstall
```

### Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## Support

- **GitHub Issues**: [Report bugs or request features](https://github.com/wpoperator/anylabelwp/issues)
- **Documentation**: [Full documentation](https://github.com/wpoperator/anylabelwp#readme)

## Changelog

### Version 0.0.2
- Improved plugin architecture
- Enhanced security features
- Better error handling
- Added internationalization support

## License

AnylabelWP is licensed under the **GPLv2 or later**.

## Credits

Developed by [Rob](https://robsdevcraft.com)

## Changelog
