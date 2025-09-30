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

            var controls = document.querySelectorAll('.anylabelwp-logo-control');
            if (!controls.length) {
                return;
            }

            var mediaAvailable = !!(wp && wp.media);
            if (!mediaAvailable) {
                console.warn('AnylabelWP: wp.media is not available. Media picker button will be disabled.');
            }

            controls.forEach(function(control) {
                var field = control.querySelector('.anylabelwp-logo-field');
                var mediaButton = control.querySelector('.anylabelwp-logo-media');
                var resetButton = control.querySelector('.anylabelwp-logo-reset');
                var toggle = control.querySelector('.anylabelwp-logo-toggle-checkbox');
                var customFields = control.querySelector('.anylabelwp-logo-custom-fields');

                if (!field || !toggle || !customFields) {
                    return;
                }

                var mediaTitle = control.dataset.mediaTitle || 'Select Image';
                var mediaButtonText = control.dataset.mediaButton || 'Use this image';
                var frame = null;

                var setCustomActive = function(active) {
                    if (active) {
                        control.classList.add('anylabelwp-logo-active');
                        customFields.style.display = '';
                    } else {
                        control.classList.remove('anylabelwp-logo-active');
                        customFields.style.display = 'none';
                    }
                };

                var clearField = function() {
                    field.value = '';
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                };

                var initialActive = toggle.checked || (field.value && field.value.trim().length > 0);
                toggle.checked = initialActive;
                setCustomActive(initialActive);

                toggle.addEventListener('change', function() {
                    if (toggle.checked) {
                        setCustomActive(true);
                    } else {
                        setCustomActive(false);
                        clearField();
                    }
                });

                if (mediaButton) {
                    if (!mediaAvailable) {
                        mediaButton.classList.add('disabled');
                        mediaButton.setAttribute('disabled', 'disabled');
                        mediaButton.title = 'Media library unavailable';
                    } else {
                        mediaButton.addEventListener('click', function(event) {
                            event.preventDefault();

                            if (!toggle.checked) {
                                toggle.checked = true;
                                setCustomActive(true);
                            }

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
                                field.dispatchEvent(new Event('change', { bubbles: true }));
                                field.dispatchEvent(new Event('input', { bubbles: true }));
                            });

                            frame.open();
                        });
                    }
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        toggle.checked = false;
                        setCustomActive(false);
                        clearField();
                    });
                }

                field.addEventListener('input', function() {
                    if (field.value && field.value.trim().length > 0) {
                        if (!toggle.checked) {
                            toggle.checked = true;
                            setCustomActive(true);
                        }
                    }
                });
            });
        }
    };

    Admin.init();

})(window, document, window.wp || undefined);
