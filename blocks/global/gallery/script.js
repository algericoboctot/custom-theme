/**
 * Masonry Gallery Frontend JavaScript - Simple Lightbox with Loading Spinner
 */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initMasonryGalleries();
    });

    function initMasonryGalleries() {
        const galleries = document.querySelectorAll('.masonry-gallery');
        
        galleries.forEach(function(gallery) {
            if (gallery.dataset.gallery) {
                try {
                    const galleryData = JSON.parse(gallery.dataset.gallery);
                    initLightboxGallery(gallery, galleryData);
                } catch (e) {
                    console.error('Failed to parse gallery data:', e);
                }
            }
        });
    }

    function initLightboxGallery(gallery, galleryData) {
        const images = galleryData.images || [];
        const showCounter = galleryData.options.showCounter;
        const showCaption = galleryData.options.showCaption;
        const loop = galleryData.options.loop;
        
        // Add click events to gallery items
        const galleryItems = gallery.querySelectorAll('.masonry-gallery__link');
        galleryItems.forEach(function(link, index) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                openLightbox(images, index, showCounter, showCaption, loop);
            });
        });
    }

    function openLightbox(images, startIndex, showCounter, showCaption, loop) {
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = lightbox.querySelector('.lightbox__image');
        const lightboxCaption = lightbox.querySelector('.lightbox__caption');
        const lightboxCounter = lightbox.querySelector('.lightbox__counter');
        const imageContainer = lightbox.querySelector('.lightbox__image-container');
        
        // Create or get spinner
        let spinner = lightbox.querySelector('.lightbox__spinner');
        if (!spinner) {
            spinner = createSpinner();
            imageContainer.appendChild(spinner);
        }
        
        let currentIndex = startIndex;
        let isLoading = false;
        
        // Show lightbox
        lightbox.style.display = 'block';
        
        // Add body class to prevent layout shift
        document.body.classList.add('lightbox-open');
        
        // Trigger animation after a small delay
        setTimeout(() => {
            lightbox.classList.add('lightbox--active');
        }, 10);
        
        // Load first image
        loadImage(currentIndex);
        
        // Navigation functions
        function loadImage(index) {
            if (isLoading) return; // Prevent multiple rapid clicks
            
            // Handle looping
            if (loop) {
                // Loop enabled: wrap around
                if (index < 0) index = images.length - 1;
                if (index >= images.length) index = 0;
            } else {
                // Loop disabled: stop at boundaries
                if (index < 0) index = 0;
                if (index >= images.length) index = images.length - 1;
            }
            
            currentIndex = index;
            const image = images[index];
            
            // Show spinner immediately and hide image
            isLoading = true;
            spinner.style.display = 'flex';
            lightboxImage.style.display = 'none';
            
            // Immediately update counter
            if (showCounter) {
                lightboxCounter.textContent = `${index + 1} / ${images.length}`;
                lightboxCounter.style.display = 'block';
            } else {
                lightboxCounter.style.display = 'none';
            }
            
            // Create new image element to preload
            const newImg = new Image();
            
            newImg.onload = function() {
                // Image loaded successfully
                lightboxImage.src = this.src;
                lightboxImage.alt = image.alt || image.title || '';
                
                // Handle caption - show only if exists and not empty
                if (showCaption && image.caption && image.caption.trim() !== '') {
                    lightboxCaption.textContent = image.caption;
                    lightboxCaption.style.display = 'block';
                } else {
                    lightboxCaption.style.display = 'none';
                }
                
                // Hide spinner and show new image
                spinner.style.display = 'none';
                lightboxImage.style.display = 'block';
                isLoading = false;
            };
            
            newImg.onerror = function() {
                // Handle image load error
                lightboxImage.src = '';
                lightboxImage.alt = 'Image failed to load';
                
                // Update caption to show error
                lightboxCaption.textContent = 'Image could not be loaded';
                lightboxCaption.style.display = 'block';
                
                // Hide spinner and show placeholder
                spinner.style.display = 'none';
                lightboxImage.style.display = 'block';
                isLoading = false;
            };
            
            // Start loading the image
            newImg.src = image.url;
        }
        
        function nextImage() {
            if (!isLoading) {
                // Check if we're at the last image and loop is disabled
                if (!loop && currentIndex >= images.length - 1) {
                    return; // Don't navigate
                }
                loadImage(currentIndex + 1);
            }
        }
        
        function prevImage() {
            if (!isLoading) {
                // Check if we're at the first image and loop is disabled
                if (!loop && currentIndex <= 0) {
                    return; // Don't navigate
                }
                loadImage(currentIndex - 1);
            }
        }
        
        // Event listeners
        const closeBtn = lightbox.querySelector('.lightbox__close');
        const prevBtn = lightbox.querySelector('.lightbox__prev');
        const nextBtn = lightbox.querySelector('.lightbox__next');
        const overlay = lightbox.querySelector('.lightbox__overlay');
        const content = lightbox.querySelector('.lightbox__content');
        
        // Remove any existing listeners to prevent duplicates
        const newCloseBtn = closeBtn.cloneNode(true);
        const newPrevBtn = prevBtn.cloneNode(true);
        const newNextBtn = nextBtn.cloneNode(true);
        
        closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn);
        prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
        nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);
        
        newCloseBtn.addEventListener('click', closeLightbox);
        newPrevBtn.addEventListener('click', prevImage);
        newNextBtn.addEventListener('click', nextImage);
        overlay.addEventListener('click', closeLightbox);
        
        // Content click closes lightbox (except navigation elements and image)
        content.addEventListener('click', function(e) {
            // Don't close if clicking on navigation elements or image
            if (e.target.closest('.lightbox__close') || 
                e.target.closest('.lightbox__prev') || 
                e.target.closest('.lightbox__next') ||
                e.target.closest('.lightbox__caption') ||
                e.target.closest('.lightbox__counter') ||
                e.target.closest('.lightbox__image') ||
                e.target.closest('.lightbox__spinner') ||
                e.target.closest('.lightbox__image-container')) {
                return;
            }
            closeLightbox();
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', handleKeydown);
        
        function handleKeydown(e) {
            switch(e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    prevImage();
                    break;
                case 'ArrowRight':
                    nextImage();
                    break;
            }
        }
        
        function closeLightbox() {
            // Remove active class to trigger exit animation
            lightbox.classList.remove('lightbox--active');
            
            // Wait for animation to complete, then hide
            setTimeout(() => {
                lightbox.style.display = 'none';
                document.body.classList.remove('lightbox-open');
                document.removeEventListener('keydown', handleKeydown);
                
                // Reset loading state
                isLoading = false;
                spinner.style.display = 'none';
                lightboxImage.style.display = 'block';
            }, 400); // Match CSS transition duration
        }
    }
    
    // Create spinner element
    function createSpinner() {
        const spinner = document.createElement('div');
        spinner.className = 'lightbox__spinner';
        spinner.style.display = 'none'; // Hidden by default
        spinner.innerHTML = `
            <svg class="lightbox__spinner-icon" width="40" height="40" viewBox="0 0 50 50">
                <circle class="lightbox__spinner-path" cx="25" cy="25" r="20" fill="none" 
                        stroke="currentColor" stroke-width="4" stroke-linecap="round" 
                        stroke-dasharray="31.416" stroke-dashoffset="31.416">
                </circle>
            </svg>
        `;
        return spinner;
    }
})();