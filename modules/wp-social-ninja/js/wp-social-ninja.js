document.addEventListener('DOMContentLoaded', function() {
    const customImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const defaultImageUrl = anylabelwp.default_url; // Get the default URL
    const logoContainer = document.querySelector('.wpsr-admin-logo');  // Target the logo container

    // Use custom logo if available, otherwise use default
    const imageUrl = customImageUrl && customImageUrl.trim() !== '' ? customImageUrl : defaultImageUrl;

    if (logoContainer && imageUrl) {
        // Create a new image element
        const newImage = new Image();
        
        newImage.onload = function() {
            // Once the image has loaded successfully, replace the SVG with the new image
            logoContainer.innerHTML = ''; // Clear the SVG
            
            // Style the new image appropriately
            newImage.style.maxWidth = '100%';
            newImage.style.maxHeight = '45px'; // Adjust this value as needed
            
            // Add the new image to the container
            logoContainer.appendChild(newImage);
        };

        newImage.onerror = function() {
            console.error('Failed to load the new logo image:', imageUrl);
            // Keep the original SVG logo in case of error
        };

        // Set the src to trigger loading
        newImage.src = imageUrl;
    } else {
        console.warn('Logo container element not found or image URL is missing');
    }
});
