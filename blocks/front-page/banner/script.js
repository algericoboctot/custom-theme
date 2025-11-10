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
      const swiperElements = document.querySelectorAll('.hero-banner');
      
      swiperElements.forEach(function(element) {
          // Skip if already initialized
          if (element.swiper) {
              return;
          }
          
          // Read data attributes with sensible defaults
          const data = element.dataset || {};
          const toBool = (v, def) => {
              if (v === undefined || v === null) return def;
              if (typeof v === 'string') {
                  const val = v.toLowerCase().trim();
                  if (val === 'true' || val === '1') return true;
                  if (val === 'false' || val === '0' || val === '') return false;
              }
              return Boolean(v);
          };
          const toInt = (v, def) => {
              const n = parseInt(v, 10);
              return Number.isFinite(n) ? n : def;
          };

          const loop = toBool(data.loop, true);
          const autoplayEnabled = toBool(data.autoplay, true);
          const delay = toInt(data.delay, 5000);
          const speed = toInt(data.speed, 300);
          const effect = (data.effect || 'fade');
          const showPagination = toBool(data.pagination, true);
          const pauseOnHover = toBool(data.pauseOnHover, true);

          const options = {
              loop: loop,
              speed: speed,
              effect: effect,
              fadeEffect: { crossFade: true }
          };

          if (autoplayEnabled) {
              options.autoplay = {
                  delay: delay,
                  // Keep autoplay running after interactions; hover handling is custom below
                  disableOnInteraction: false
              };
          }

          if (showPagination) {
              options.pagination = {
                  el: element.querySelector('.swiper-pagination'),
                  clickable: true
              };
          }

          // Initialize Swiper
          const swiper = new Swiper(element, options);

          // Pause on hover if enabled
          if (autoplayEnabled && pauseOnHover) {
              element.addEventListener('mouseenter', function() {
                  if (swiper && swiper.autoplay && typeof swiper.autoplay.stop === 'function') {
                      swiper.autoplay.stop();
                  }
              });
              element.addEventListener('mouseleave', function() {
                  if (swiper && swiper.autoplay && typeof swiper.autoplay.start === 'function') {
                      swiper.autoplay.start();
                  }
              });
          }
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