/**
 * AnylabelWP Logo Selection Component
 * Pure vanilla JavaScript implementation - no jQuery dependencies
 * Updated for new checkbox-based logo selection UI
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
                this.setupLogoSelector(container);
            }.bind(this));
        },

        /**
         * Setup individual logo selector component
         * 
         * @param {HTMLElement} container - The container element
         */
        setupLogoSelector: function(container) {
            const checkbox = container.querySelector('.use-custom-logo-checkbox');
            const customOptions = container.querySelector('.custom-logo-options');
            const hiddenInput = container.querySelector('.logo-url-input');
            const customInput = container.querySelector('.custom-logo-url-input');
            const preview = container.querySelector('.custom-logo-preview');
            const mediaButton = container.querySelector('.media-upload-button');
            const clearButton = container.querySelector('.clear-custom-logo-button');

            if (!checkbox || !customOptions || !hiddenInput) {
                console.warn('Logo selector elements not found');
                return;
            }

            // Handle checkbox toggle
            checkbox.addEventListener('change', function() {
                this.toggleCustomOptions(checkbox, customOptions, hiddenInput, customInput);
            }.bind(this));

            // Handle custom URL input changes
            if (customInput) {
                customInput.addEventListener('input', function() {
                    this.updateCustomLogo(customInput.value, hiddenInput, preview);
                }.bind(this));
            }

            // Handle media library button
            if (mediaButton) {
                mediaButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.openMediaUploader(hiddenInput, customInput, preview);
                }.bind(this));
            }

            // Handle clear button
            if (clearButton) {
                clearButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.clearCustomLogo(checkbox, customOptions, hiddenInput, customInput, preview);
                }.bind(this));
            }
        },

        /**
         * Toggle custom logo options visibility
         * 
         * @param {HTMLElement} checkbox - The checkbox element
         * @param {HTMLElement} customOptions - The custom options container
         * @param {HTMLElement} hiddenInput - The hidden input field
         * @param {HTMLElement} customInput - The custom URL input field
         */
        toggleCustomOptions: function(checkbox, customOptions, hiddenInput, customInput) {
            if (checkbox.checked) {
                // Show custom options
                customOptions.style.display = 'block';
                
                // If there's a current value that's not the default, use it
                const currentValue = hiddenInput.value;
                const defaultUrl = hiddenInput.getAttribute('data-default-url');
                
                if (currentValue && currentValue !== defaultUrl) {
                    if (customInput) {
                        customInput.value = currentValue;
                    }
                }
            } else {
                // Hide custom options and revert to default
                customOptions.style.display = 'none';
                
                const defaultUrl = hiddenInput.getAttribute('data-default-url');
                hiddenInput.value = defaultUrl || '';
                
                if (customInput) {
                    customInput.value = '';
                }
                
                // Hide preview
                const preview = customOptions.querySelector('.custom-logo-preview');
                if (preview) {
                    preview.style.display = 'none';
                }
            }
        },

        /**
         * Update custom logo value and preview
         * 
         * @param {string} url - The new logo URL
         * @param {HTMLElement} hiddenInput - The hidden input field
         * @param {HTMLElement} preview - The preview container
         */
        updateCustomLogo: function(url, hiddenInput, preview) {
            if (hiddenInput) {
                hiddenInput.value = url;
            }
            
            this.updatePreview(url, preview);
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
                img.alt = 'Custom Logo Preview';
                img.style.maxHeight = '40px';
                img.style.height = 'auto';
                img.style.border = '1px solid #ddd';
                img.style.borderRadius = '2px';
                
                // Handle image load errors
                img.addEventListener('error', function() {
                    preview.innerHTML = '<div class="logo-error" style="color: #d63638;">Invalid image URL</div>';
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
         * @param {HTMLElement} hiddenInput - Hidden input to populate
         * @param {HTMLElement} customInput - Custom URL input to populate
         * @param {HTMLElement} preview - Preview container to update
         */
        openMediaUploader: function(hiddenInput, customInput, preview) {
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
                        // Update both inputs
                        if (hiddenInput) {
                            hiddenInput.value = attachment.url;
                        }
                        if (customInput) {
                            customInput.value = attachment.url;
                        }
                        
                        // Update preview
                        this.updatePreview(attachment.url, preview);
                    }
                } catch (error) {
                    console.error('Error selecting media:', error);
                }
            }.bind(this));

            mediaUploader.open();
        },

        /**
         * Clear custom logo and revert to default
         * 
         * @param {HTMLElement} checkbox - The checkbox element
         * @param {HTMLElement} customOptions - The custom options container
         * @param {HTMLElement} hiddenInput - The hidden input field
         * @param {HTMLElement} customInput - The custom URL input field
         * @param {HTMLElement} preview - The preview container
         */
        clearCustomLogo: function(checkbox, customOptions, hiddenInput, customInput, preview) {
            // Uncheck the checkbox
            checkbox.checked = false;
            
            // Hide custom options
            customOptions.style.display = 'none';
            
            // Clear custom input
            if (customInput) {
                customInput.value = '';
            }
            
            // Revert to default URL
            const defaultUrl = hiddenInput.getAttribute('data-default-url');
            hiddenInput.value = defaultUrl || '';
            
            // Hide preview
            if (preview) {
                preview.style.display = 'none';
                preview.innerHTML = '';
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
        }
    };

    // Initialize the logo selector system
    LogoSelector.init();

    // Expose to global namespace for external access if needed
    window.AnylabelWP.LogoSelector = LogoSelector;

})(window, document);
