document.addEventListener('DOMContentLoaded', function() {
    const newImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const logoImage = document.querySelector('body.settings_page_fluent-mail .logo img');  // Target the img tag inside the .logo div

    if (logoImage && newImageUrl) {
        // Create a new image object and set its src to the new image URL
        const newImage = new Image();
        
        newImage.onload = function() {
            // Once the new image has finished loading, update the src of the logo image
            logoImage.src = newImageUrl;
            // Show the logo image
            logoImage.style.display = 'block';
        };

        newImage.onerror = function() {
            console.error('Failed to load the new logo image:', newImageUrl);
            // Optionally, you can show the original logo or a fallback image
            logoImage.style.display = 'block';
        };

        newImage.src = newImageUrl;
    } else {
        console.warn('Logo image element not found or new image URL is missing');
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
