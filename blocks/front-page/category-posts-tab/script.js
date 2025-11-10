/**
 * Category Posts Tab JavaScript
 * Handles tab switching for category posts
 * Works in both admin editor and frontend
 */

(function() {
    'use strict';

    // Function to initialize tab functionality
    function initCategoryTabs() {
        const categoryTabs = document.querySelectorAll('[data-category-tabs]');
        
        if (!categoryTabs.length) {
            return;
        }
        
        categoryTabs.forEach(function(tabContainer) {
            const tabButtons = tabContainer.querySelectorAll('.category-tab-btn');
            const tabContents = tabContainer.querySelectorAll('.category-tab-content');
            
            // Add click event listeners to tab buttons
            tabButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const categoryId = this.getAttribute('data-category-id');
                    
                    // Update active tab button
                    tabButtons.forEach(function(btn) {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // Update active tab content
                    tabContents.forEach(function(content) {
                        content.classList.remove('active');
                        if (content.getAttribute('data-category-content') === categoryId) {
                            content.classList.add('active');
                        }
                    });
                    
                    // Trigger custom event for editor compatibility
                    const event = new CustomEvent('categoryTabChanged', {
                        detail: {
                            categoryId: categoryId,
                            categoryName: this.getAttribute('data-category-name'),
                            categoryLink: this.getAttribute('data-category-link')
                        }
                    });
                    document.dispatchEvent(event);
                });
            });
        });
    }

    // Initialize on DOM ready
    function init() {
        initCategoryTabs();
    }

    // Check if we're in the WordPress admin/editor
    if (typeof wp !== 'undefined' && wp.domReady) {
        // WordPress admin/editor context
        wp.domReady(function() {
            init();
        });
    } else {
        // Frontend context
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            // DOM is already ready
            init();
        }
    }

    // Also initialize when content is dynamically loaded (for editor)
    if (typeof wp !== 'undefined' && wp.data && wp.data.subscribe) {
        wp.data.subscribe(function() {
            // Small delay to ensure DOM is updated
            setTimeout(init, 100);
        });
    }

    // Initialize on window load for any late-loading content
    window.addEventListener('load', init);

    // Make function globally available for manual initialization
    window.initCategoryPostsTabs = init;

})(); 