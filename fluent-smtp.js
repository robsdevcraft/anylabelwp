document.addEventListener('DOMContentLoaded', function() {
    const newImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    // console.log('newImageUrl:', newImageUrl);  // Log the new URL to the console

    const logoImage = document.querySelector('body.settings_page_fluent-mail .logo img');  // Target the img tag inside the .logo div

    if (logoImage) {
        // Create a new image object and set its src to the new image URL
        const newImage = new Image();
        newImage.src = newImageUrl;

        newImage.onload = function() {
            // Once the new image has finished loading, update the src of the logo image
            logoImage.src = newImageUrl;

            // Show the logo image
            logoImage.style.display = 'block';
        };
    }
    
    window.addEventListener('hashchange', function() {
        const blockedHashes = ['#/support', '#/documentation', '#/notification-settings'];
        if (blockedHashes.includes(window.location.hash)) {
            window.location.href = '/wp-admin/options-general.php?page=fluent-mail#/';
        }
    });
});
