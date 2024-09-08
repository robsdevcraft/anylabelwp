/**
 * AnylabelWP Admin JavaScript
 * 
 * This file contains general JavaScript functions for the AnylabelWP plugin admin area.
 */

(function(window, document) {
    'use strict';

    // Create a unique namespace for your plugin
    window.AnylabelWP = window.AnylabelWP || {};

    AnylabelWP.admin = {
        init: function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', this.onDocumentReady.bind(this));
            } else {
                this.onDocumentReady();
            }
        },

        onDocumentReady: function() {
            this.setupButtonListeners();
        },

        setupButtonListeners: function() {
            try {
                var buttons = document.querySelectorAll('.anylabelwp-button');
                buttons.forEach(function(button) {
                    button.addEventListener('click', this.onButtonClick.bind(this));
                }.bind(this));
            } catch (error) {
                console.error('Error setting up button listeners:', error);
            }
        },

        onButtonClick: function(e) {
            e.preventDefault();
            console.log('AnylabelWP button clicked!');
            // Add your button click logic here
        }
    };

    AnylabelWP.admin.init();

})(window, document);
