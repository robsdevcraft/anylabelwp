document.addEventListener('DOMContentLoaded', function() {
    console.log("Fluent Forms JS file loaded");

    const newImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const logoImage = document.querySelector('.ff_header .plugin-name img');  // Target the img tag

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
            logoImage.style.display = 'block'; // Make sure to display the original logo in case of error
        };

        newImage.src = newImageUrl;
    } else {
        console.warn('Logo image element not found or new image URL is missing');
        if (logoImage) {
            logoImage.style.display = 'block'; // Make sure to display the original logo if newImageUrl is missing
        }
    }
});
