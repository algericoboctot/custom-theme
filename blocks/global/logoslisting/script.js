// Enhanced script that works in both editor and frontend
(function() {
    'use strict';
    
    // Function to initialize Swiper
    function initializeSwiper() {
        // Check if Swiper is available
        if (typeof Swiper === 'undefined') {
            console.log('Swiper not loaded yet, retrying...');
            setTimeout(initializeSwiper, 100);
            return;
        }
        // Find all banner swipers
        const swiperElements = document.querySelectorAll('.logo-listings');
        
        swiperElements.forEach(function(element) {
            // Skip if already initialized
            if (element.swiper) {
                return;
            }
            
            // Initialize Swiper
            const swiper = new Swiper(element, {
                loop: true,
                slidesPerView: 1,
                spaceBetween: 20,
                navigation: {
                    nextEl: document.querySelector('.logo-listings__next'),
                    prevEl: document.querySelector('.logo-listings__prev'),
                },
                breakpoints: {
                    640: {
                      slidesPerView: 2,
                      spaceBetween: 20,
                    },
                    768: {
                      slidesPerView: 3,
                      spaceBetween: 40,
                    },
                    1024: {
                      slidesPerView: 5,
                      spaceBetween: 50,
                    },
                }
            });
        });
    }
    
    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeSwiper);
    } else {
        initializeSwiper();
    }
    
    // For Block Editor: Re-initialize when blocks are added/updated
    if (typeof wp !== 'undefined' && wp.data) {
        let timeoutId;
        
        wp.data.subscribe(function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function() {
                initializeSwiper();
            }, 100);
        });
    }
    
    // For Block Editor: Watch for DOM changes (fallback)
    if (window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            let shouldInit = false;
            
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && 
                        (node.classList.contains('swiper') || 
                         node.querySelector('.swiper'))) {
                        shouldInit = true;
                    }
                });
            });
            
            if (shouldInit) {
                setTimeout(initializeSwiper, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
  })();