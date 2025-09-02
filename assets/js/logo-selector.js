/**
 * AnylabelWP Logo Selection Component
 * Pure vanilla JavaScript implementation - no jQuery dependencies
 * 
 * @package AnylabelWP
 * @since 0.0.3
 * @author AnylabelWP Team
 */

(function(window, document) {
    'use strict';

    // Create namespace
    window.AnylabelWP = window.AnylabelWP || {};
    window.AnylabelWP.LogoSelector = window.AnylabelWP.LogoSelector || {};

    /**
     * Logo Selector Component
     */
    const LogoSelector = {
        /**
         * Initialize the logo selector system
         */
        init: function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', this.onDocumentReady.bind(this));
            } else {
                this.onDocumentReady();
            }
        },

        /**
         * Document ready handler
         */
        onDocumentReady: function() {
            this.initLogoSelectors();
        },

        /**
         * Initialize all logo selector components on the page
         */
        initLogoSelectors: function() {
            const containers = document.querySelectorAll('.anylabelwp-logo-selector');
            
            containers.forEach(function(container) {
                const input = container.querySelector('.logo-url-input');
                if (!input) return;
                
                const inputName = input.getAttribute('name');
                const currentValue = input.value;
                
                this.setupLogoSelector(container, inputName, currentValue);
            }.bind(this));
        },

        /**
         * Setup individual logo selector component
         * 
         * @param {HTMLElement} container - The container element
         * @param {string} inputName - The input field name
         * @param {string} currentValue - Current logo URL value
         */
        setupLogoSelector: function(container, inputName, currentValue) {
            const input = container.querySelector('.logo-url-input');
            const preview = container.querySelector('.logo-preview');
            const defaultGrid = container.querySelector('.default-images-grid');
            const mediaButton = container.querySelector('.media-upload-button');
            const clearButton = container.querySelector('.clear-logo-button');

            if (!input || !preview) return;

            // Update preview when input changes
            input.addEventListener('input', function() {
                this.updatePreview(input.value, preview);
            }.bind(this));

            // Media uploader button
            if (mediaButton) {
                mediaButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.openMediaUploader(input, preview);
                }.bind(this));
            }

            // Clear logo button
            if (clearButton) {
                clearButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    input.value = '';
                    this.updatePreview('', preview);
                    this.clearDefaultSelection(defaultGrid);
                }.bind(this));
            }

            // Default image selection
            if (defaultGrid) {
                this.setupDefaultImageSelection(defaultGrid, input, preview);
            }

            // Initial preview update
            this.updatePreview(currentValue, preview);
            
            // Mark current selection if it's a default image
            if (currentValue && defaultGrid) {
                this.markCurrentSelection(defaultGrid, currentValue);
            }
        },

        /**
         * Setup default image selection functionality
         * 
         * @param {HTMLElement} defaultGrid - Grid container for default images
         * @param {HTMLElement} input - Input field element
         * @param {HTMLElement} preview - Preview container element
         */
        setupDefaultImageSelection: function(defaultGrid, input, preview) {
            defaultGrid.addEventListener('click', function(e) {
                e.preventDefault();
                
                const target = e.target.closest('.default-image-option');
                if (!target) return;
                
                const imageUrl = target.getAttribute('data-url');
                if (!imageUrl) return;
                
                input.value = imageUrl;
                this.updatePreview(imageUrl, preview);
                
                // Visual feedback - clear all selections and mark current
                this.clearDefaultSelection(defaultGrid);
                target.classList.add('selected');
            }.bind(this));
        },

        /**
         * Update logo preview display
         * 
         * @param {string} url - Image URL to preview
         * @param {HTMLElement} preview - Preview container element
         */
        updatePreview: function(url, preview) {
            if (!preview) return;

            if (url && url.trim() !== '') {
                const img = document.createElement('img');
                img.src = this.sanitizeUrl(url);
                img.alt = 'Logo Preview';
                img.style.maxWidth = '200px';
                img.style.maxHeight = '50px';
                img.style.height = 'auto';
                img.style.objectFit = 'contain';
                
                // Handle image load errors
                img.addEventListener('error', function() {
                    preview.innerHTML = '<div class="logo-error">Invalid image URL</div>';
                });
                
                preview.innerHTML = '';
                preview.appendChild(img);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
                preview.innerHTML = '';
            }
        },

        /**
         * Open WordPress media uploader
         * 
         * @param {HTMLElement} input - Input field to populate
         * @param {HTMLElement} preview - Preview container to update
         */
        openMediaUploader: function(input, preview) {
            // Check if WordPress media library is available
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                console.error('WordPress media library not available');
                alert('Media library is not available. Please enter image URL manually.');
                return;
            }

            const mediaUploader = wp.media({
                title: window.anylabelwp_admin ? window.anylabelwp_admin.media_title : 'Choose Logo Image',
                button: {
                    text: window.anylabelwp_admin ? window.anylabelwp_admin.media_button : 'Use This Image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            mediaUploader.on('select', function() {
                try {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    if (attachment && attachment.url) {
                        input.value = attachment.url;
                        this.updatePreview(attachment.url, preview);
                        
                        // Clear default image selections since we're using custom
                        const container = input.closest('.anylabelwp-logo-selector');
                        const defaultGrid = container ? container.querySelector('.default-images-grid') : null;
                        if (defaultGrid) {
                            this.clearDefaultSelection(defaultGrid);
                        }
                    }
                } catch (error) {
                    console.error('Error selecting media:', error);
                }
            }.bind(this));

            mediaUploader.open();
        },

        /**
         * Clear all default image selections
         * 
         * @param {HTMLElement} defaultGrid - Grid container for default images
         */
        clearDefaultSelection: function(defaultGrid) {
            if (!defaultGrid) return;
            
            const selectedItems = defaultGrid.querySelectorAll('.default-image-option.selected');
            selectedItems.forEach(function(item) {
                item.classList.remove('selected');
            });
        },

        /**
         * Mark current selection in default images grid
         * 
         * @param {HTMLElement} defaultGrid - Grid container for default images
         * @param {string} currentValue - Current URL value to match
         */
        markCurrentSelection: function(defaultGrid, currentValue) {
            if (!defaultGrid || !currentValue) return;
            
            const matchingOption = defaultGrid.querySelector(`[data-url="${this.escapeSelector(currentValue)}"]`);
            if (matchingOption) {
                matchingOption.classList.add('selected');
            }
        },

        /**
         * Sanitize URL for security
         * 
         * @param {string} url - URL to sanitize
         * @returns {string} - Sanitized URL
         */
        sanitizeUrl: function(url) {
            if (!url || typeof url !== 'string') return '';
            
            // Basic URL validation - reject obviously malicious content
            if (url.includes('javascript:') || url.includes('data:text/html')) {
                console.warn('Potentially unsafe URL blocked:', url);
                return '';
            }
            
            return url.trim();
        },

        /**
         * Escape CSS selector string
         * 
         * @param {string} str - String to escape for CSS selector
         * @returns {string} - Escaped string
         */
        escapeSelector: function(str) {
            if (!str) return '';
            return str.replace(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, '\\$&');
        }
    };

    // Initialize the logo selector system
    LogoSelector.init();

    // Expose to global namespace for external access if needed
    window.AnylabelWP.LogoSelector = LogoSelector;

})(window, document);
