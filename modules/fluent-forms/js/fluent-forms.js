document.addEventListener('DOMContentLoaded', function() {
    console.log('AnylabelWP Fluent Forms: Script loaded');
    const customImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const defaultImageUrl = anylabelwp.default_url; // Get the default URL
    console.log('AnylabelWP Fluent Forms: Custom URL:', customImageUrl);
    console.log('AnylabelWP Fluent Forms: Default URL:', defaultImageUrl);
    
    const logoImage = document.querySelector('.ff_header .plugin-name img');  // Target the img tag
    console.log('AnylabelWP Fluent Forms: Logo element found:', logoImage);

    // Use custom logo if available, otherwise use default
    const imageUrl = customImageUrl && customImageUrl.trim() !== '' ? customImageUrl : defaultImageUrl;
    console.log('AnylabelWP Fluent Forms: Using URL:', imageUrl);

    if (logoImage && imageUrl) {
        console.log('AnylabelWP Fluent Forms: Forcing logo update');
        console.log('AnylabelWP Fluent Forms: Target element:', logoImage);
        console.log('AnylabelWP Fluent Forms: New URL:', imageUrl);
        
        // Force update immediately without preload
        logoImage.src = imageUrl;
        logoImage.setAttribute('src', imageUrl);
        logoImage.style.cssText = 'display: block !important; width: auto !important; max-width: 120px !important; height: auto !important;';
        
        // Log final state
        setTimeout(() => {
            console.log('AnylabelWP Fluent Forms: Final src:', logoImage.src);
            console.log('AnylabelWP Fluent Forms: Final display:', window.getComputedStyle(logoImage).display);
            console.log('AnylabelWP Fluent Forms: Final visibility:', window.getComputedStyle(logoImage).visibility);
            console.log('AnylabelWP Fluent Forms: Final opacity:', window.getComputedStyle(logoImage).opacity);
        }, 100);
    } else {
        console.warn('Logo image element not found or image URL is missing');
        if (logoImage) {
            logoImage.style.display = 'block'; // Make sure to display the original logo if imageUrl is missing
        }
    }
});
