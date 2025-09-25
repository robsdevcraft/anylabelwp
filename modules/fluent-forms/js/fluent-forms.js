document.addEventListener('DOMContentLoaded', function() {
    const customImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const defaultImageUrl = anylabelwp.default_url; // Get the default URL
    const logoImage = document.querySelector('.ff_header .plugin-name img');  // Target the img tag

    // Use custom logo if available, otherwise use default
    const imageUrl = customImageUrl && customImageUrl.trim() !== '' ? customImageUrl : defaultImageUrl;

    if (logoImage && imageUrl) {
        // Create a new image object and set its src to the new image URL
        const newImage = new Image();
        
        newImage.onload = function() {
            // Once the new image has finished loading, update the src of the logo image
            logoImage.src = imageUrl;
            // Show the logo image
            logoImage.style.display = 'block';
        };

        newImage.onerror = function() {
            console.error('Failed to load the logo image:', imageUrl);
            // Optionally, you can show the original logo or a fallback image
            logoImage.style.display = 'block'; // Make sure to display the original logo in case of error
        };

        newImage.src = imageUrl;
    } else {
        console.warn('Logo image element not found or image URL is missing');
        if (logoImage) {
            logoImage.style.display = 'block'; // Make sure to display the original logo if imageUrl is missing
        }
    }
});
