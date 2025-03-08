// Replace Fluent CRM Logo with new logo saved from class-fluent-crm.php in wp-options
document.addEventListener('DOMContentLoaded', function() {
    const newImageUrl = anylabelwp.new_url;  // Get the saved URL from PHP
    const logoImage = document.querySelector('.fluentcrm_menu_logo_holder img');  // Target the img tag

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

// Hide the Pro links in "Contacts" and "Emails" subpage dropdowns
(function() {
    // Function to hide menu items based on text content
    function hideMenuItems() {
        // Target dropdown items with specific text
        document.querySelectorAll('.el-dropdown-menu__item').forEach(item => {
            const text = item.textContent.trim();
            if (text === 'Recurring Email Campaigns' || 
                text === 'Email Sequences' || 
                text === 'Dynamic Segments' ||
                text === 'Smart Links') {
                item.style.display = 'none';
            }
        });
        
        // Target settings menu items
        document.querySelectorAll('.el-menu .el-menu-item').forEach(item => {
            if (item.textContent.includes('Smart Links')) {
                item.style.display = 'none';
            }
        });
    }
    
    // Run immediately and on DOM changes
    function initObserver() {
        hideMenuItems();
        
        // Watch for DOM changes
        const observer = new MutationObserver(function(mutations) {
            hideMenuItems();
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Run when DOM is ready or when page has loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initObserver);
    } else {
        initObserver();
    }
})();

// Text replacement function on Settings page for General Settings
function replaceTextInElements() {
    // Get all text nodes in the document
    const walker = document.createTreeWalker(
        document.body,
        NodeFilter.SHOW_TEXT,
        null,
        false
    );
    
    const textReplacements = [
        { from: "Fluent CRM", to: "CRM" },
        { from: "FluentCRM", to: "CRM" },
        { from: "WP User", to: "User" },
    ];
    
    let node;
    while (node = walker.nextNode()) {
        if (node.nodeValue.trim() !== '') {
            let newText = node.nodeValue;
            textReplacements.forEach(replacement => {
                newText = newText.replace(new RegExp(replacement.from, 'g'), replacement.to);
            });
            
            if (newText !== node.nodeValue) {
                node.nodeValue = newText;
            }
        }
    }
}

// Run on page load and when DOM changes
function initTextReplacements() {
    replaceTextInElements();
    
    // Watch for DOM changes
    const observer = new MutationObserver(function(mutations) {
        replaceTextInElements();
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

// Run when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTextReplacements);
} else {
    initTextReplacements();
}
