/**
 * AnylabelWP Admin JavaScript
 * 
 * This file contains general JavaScript functions for the AnylabelWP plugin admin area.
 */

(function(window, document, wp) {
    'use strict';

    window.AnylabelWP = window.AnylabelWP || {};

    var Admin = {
        init: function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', this.onReady.bind(this));
            } else {
                this.onReady();
            }
        },

        onReady: function() {
            this.setupLegacyButtons();
            this.initLogoControls();
        },

        setupLegacyButtons: function() {
            try {
                var buttons = document.querySelectorAll('.anylabelwp-button');
                buttons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('AnylabelWP button clicked!');
                    });
                });
            } catch (error) {
                console.error('Error setting up legacy button listeners:', error);
            }
        },

        initLogoControls: function() {
            if (!document.body.classList.contains('settings_page_anylabelwp-settings')) {
                return;
            }

            if (!wp || !wp.media) {
                console.warn('AnylabelWP: wp.media is not available. Media picker will be disabled.');
                return;
            }

            var controls = document.querySelectorAll('.anylabelwp-logo-control');
            if (!controls.length) {
                return;
            }

            controls.forEach(function(control) {
                var field = control.querySelector('.anylabelwp-logo-field');
                var mediaButton = control.querySelector('.anylabelwp-logo-media');
                var resetButton = control.querySelector('.anylabelwp-logo-reset');
                var preview = control.querySelector('.anylabelwp-logo-preview');
                var status = control.querySelector('.anylabelwp-logo-status');

                if (!field || !preview) {
                    return;
                }

                var defaultUrl = control.dataset.defaultUrl || '';
                var statusDefault = control.dataset.statusDefault || '';
                var statusCustom = control.dataset.statusCustom || '';
                var mediaTitle = control.dataset.mediaTitle || 'Select Image';
                var mediaButtonText = control.dataset.mediaButton || 'Use this image';
                var frame = null;

                var updatePreview = function(url) {
                    var trimmed = (url || '').trim();
                    var finalUrl = trimmed.length ? trimmed : defaultUrl;

                    preview.src = finalUrl || preview.src;

                    if (status) {
                        status.textContent = trimmed.length ? statusCustom : statusDefault;
                    }
                };

                updatePreview(field.value);

                var openMediaFrame = function() {
                    if (frame) {
                        frame.open();
                        return;
                    }

                    frame = wp.media({
                        title: mediaTitle,
                        button: {
                            text: mediaButtonText
                        },
                        library: {
                            type: 'image'
                        },
                        multiple: false
                    });

                    frame.on('select', function() {
                        var selection = frame.state().get('selection');
                        var attachment = selection && selection.first();

                        if (!attachment) {
                            return;
                        }

                        var data = attachment.toJSON();

                        if (!data || !data.url) {
                            return;
                        }

                        field.value = data.url;
                        updatePreview(data.url);
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                    });

                    frame.open();
                };

                if (mediaButton) {
                    mediaButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        openMediaFrame();
                    });
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        field.value = '';
                        updatePreview('');
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                }

                field.addEventListener('change', function() {
                    updatePreview(field.value);
                });

                field.addEventListener('input', function() {
                    updatePreview(field.value);
                });
            });
        }
    };

    Admin.init();

})(window, document, window.wp || undefined);
