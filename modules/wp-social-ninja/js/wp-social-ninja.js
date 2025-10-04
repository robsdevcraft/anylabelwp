document.addEventListener('DOMContentLoaded', function() {
    console.log('AnylabelWP WP Social Ninja: Script loaded');
    const customImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const defaultImageUrl = anylabelwp.default_url; // Get the default URL
    console.log('AnylabelWP WP Social Ninja: Custom URL:', customImageUrl);
    console.log('AnylabelWP WP Social Ninja: Default URL:', defaultImageUrl);
    
    const logoContainer = document.querySelector('.wpsr-admin-logo');  // Target the logo container
    console.log('AnylabelWP WP Social Ninja: Logo element found:', logoContainer);

    // Use custom logo if available, otherwise use default
    const imageUrl = customImageUrl && customImageUrl.trim() !== '' ? customImageUrl : defaultImageUrl;
    console.log('AnylabelWP WP Social Ninja: Using URL:', imageUrl);

    if (logoContainer && imageUrl) {
        console.log('AnylabelWP WP Social Ninja: Forcing logo update');
        console.log('AnylabelWP WP Social Ninja: Target container:', logoContainer);
        console.log('AnylabelWP WP Social Ninja: New URL:', imageUrl);
        
        // Clear and create new image
        logoContainer.innerHTML = '';
        const newImage = document.createElement('img');
        newImage.src = imageUrl;
        newImage.style.cssText = 'width: 120px !important; height: auto !important; max-height: 45px !important; display: block !important;';
        logoContainer.appendChild(newImage);
        
        // Log final state
        setTimeout(() => {
            console.log('AnylabelWP WP Social Ninja: Final container HTML:', logoContainer.innerHTML);
            console.log('AnylabelWP WP Social Ninja: Final display:', window.getComputedStyle(newImage).display);
            console.log('AnylabelWP WP Social Ninja: Final visibility:', window.getComputedStyle(newImage).visibility);
        }, 100);
    } else {
        console.warn('Logo container element not found or image URL is missing');
    }
});
