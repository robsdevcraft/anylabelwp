document.addEventListener('DOMContentLoaded', function() {
    const newImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const logoContainer = document.querySelector('.wpsr-admin-logo');  // Target the logo container

    if (logoContainer && newImageUrl) {
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
            console.error('Failed to load the new logo image:', newImageUrl);
            // Keep the original SVG logo in case of error
        };

        // Set the src to trigger loading
        newImage.src = newImageUrl;
    } else {
        console.warn('Logo container element not found or new image URL is missing');
    }
});
