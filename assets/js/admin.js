/**
 * AnylabelWP Admin JavaScript
 * 
 * This file contains general JavaScript functions for the AnylabelWP plugin admin area.
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Example: Add a click event to all buttons with class 'anylabelwp-button'
        var buttons = document.querySelectorAll('.anylabelwp-button');
        buttons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('AnylabelWP button clicked!');
            });
        });

        // You can add more general admin JavaScript functionality here
    });

})();
