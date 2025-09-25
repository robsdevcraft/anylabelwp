document.addEventListener('DOMContentLoaded', function() {
    const customImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const defaultImageUrl = anylabelwp.default_url; // Get the default URL
    const logoImage = document.querySelector('body.settings_page_fluent-mail .logo img');  // Target the img tag inside the .logo div

    // Use custom logo if available, otherwise use default
    const imageUrl = customImageUrl && customImageUrl.trim() !== '' ? customImageUrl : defaultImageUrl;

    if (logoImage && imageUrl) {
        // Create a new image object and set its src to the new image URL
        const newImage = new Image();
        
        newImage.onload = function() {
            // Once the new image has finished loading, update the src of the logo image
            logoImage.src = imageUrl;
            // Style using object properties to overide default css from fluent smtp
            Object.assign(logoImage.style, {
                display: 'block',
                width: 'auto',       
            });
        };

        newImage.onerror = function() {
            console.error('Failed to load the logo image:', imageUrl);
            // Optionally, you can show the original logo or a fallback image
            logoImage.style.display = 'block';
        };

        newImage.src = imageUrl;
    } else {
        console.warn('Logo image element not found or image URL is missing');
        if (logoImage) {
            logoImage.style.display = 'block'; // Make sure to display the original logo
        }
    }
    
    // Array of hashes for pages that should be blocked
    const blockedHashes = ['#/support', '#/documentation', '#/notification-settings'];

    // Listen for changes in the URL hash
    window.addEventListener('hashchange', function() {
        // Get the current hash from the URL
        const currentHash = new URL(window.location.href).hash;

        // Check if the current hash is in the list of blocked hashes
        if (blockedHashes.includes(currentHash)) {
            // If it is, redirect to the main Fluent SMTP settings page
            window.location.href = '/wp-admin/options-general.php?page=fluent-mail#/';
        }
    });
});
