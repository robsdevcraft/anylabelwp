document.addEventListener('DOMContentLoaded', function() {
    console.log('AnylabelWP Fluent SMTP: Script loaded');
    const customImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const defaultImageUrl = anylabelwp.default_url; // Get the default URL
    console.log('AnylabelWP Fluent SMTP: Custom URL:', customImageUrl);
    console.log('AnylabelWP Fluent SMTP: Default URL:', defaultImageUrl);
    
    const logoImage = document.querySelector('body.settings_page_fluent-mail .logo img');  // Target the img tag inside the .logo div
    console.log('AnylabelWP Fluent SMTP: Logo element found:', logoImage);

    // Use custom logo if available, otherwise use default
    const imageUrl = customImageUrl && customImageUrl.trim() !== '' ? customImageUrl : defaultImageUrl;
    console.log('AnylabelWP Fluent SMTP: Using URL:', imageUrl);

    if (logoImage && imageUrl) {
        console.log('AnylabelWP Fluent SMTP: Forcing logo update');
        console.log('AnylabelWP Fluent SMTP: Target element:', logoImage);
        console.log('AnylabelWP Fluent SMTP: New URL:', imageUrl);
        
        // Force update immediately without preload
        logoImage.src = imageUrl;
        logoImage.setAttribute('src', imageUrl);
        logoImage.removeAttribute('style'); // Clear any existing inline styles first
        logoImage.style.cssText = 'display: block !important; width: 140px !important; height: auto !important; margin-top: 0.8rem !important; visibility: visible !important; opacity: 1 !important;';
        
        // Log final state
        setTimeout(() => {
            console.log('AnylabelWP Fluent SMTP: Final src:', logoImage.src);
            console.log('AnylabelWP Fluent SMTP: Final display:', window.getComputedStyle(logoImage).display);
            console.log('AnylabelWP Fluent SMTP: Final visibility:', window.getComputedStyle(logoImage).visibility);
            console.log('AnylabelWP Fluent SMTP: Final opacity:', window.getComputedStyle(logoImage).opacity);
            console.log('AnylabelWP Fluent SMTP: Image dimensions:', logoImage.width, 'x', logoImage.height);
            console.log('AnylabelWP Fluent SMTP: Computed width/height:', window.getComputedStyle(logoImage).width, window.getComputedStyle(logoImage).height);
            console.log('AnylabelWP Fluent SMTP: Parent element:', logoImage.parentElement);
            console.log('AnylabelWP Fluent SMTP: Parent display:', window.getComputedStyle(logoImage.parentElement).display);
        }, 100);
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
