var MB = (function (MB) {
    /**
     * Doc Ready
     * 
     * Use DOMContentLoaded for each function call
     */
    document.addEventListener('DOMContentLoaded', function() {
        MB.Statistics.init();
    });

    // ACF Block Editor initialization
    if (typeof acf !== 'undefined') {
        acf.addAction('render_block_preview', function($block) {
            // Convert jQuery object to DOM element if needed
            const blockElement = $block instanceof jQuery ? $block[0] : $block;
            
            // Only initialize if this block contains banner elements
            if (blockElement && blockElement.querySelector && blockElement.querySelector('.stats-numbers')) {
                MB.Statistics.init(blockElement);
            }
        });
    }

    // Fallback for block editor if ACF actions aren't available
    window.addEventListener('load', function() {
        if (isBlockEditor()) {
            // Delay to ensure blocks are rendered
            setTimeout(function() {
                MB.Statistics.init();
            }, 500);
        }
    });

    /**
     * Check if we're in the block editor
     */
    function isBlockEditor() {
        return typeof wp !== 'undefined' && wp.blocks && 
               (document.body.classList.contains('block-editor-page') || 
                document.body.classList.contains('post-php'));
    }

    MB.Statistics = {
        init(context = null) {
            const container = context || document;
            const columns = container.querySelectorAll('.stats-numbers'); // Select all columns with the class

            // Function to animate numbers
            function animateNumber(element) {
                const target = parseInt(element.dataset.currentNumber); // Get the target value from data-current-number
                
                // Store original content and set target number to measure width
                const originalText = element.textContent;
                element.textContent = target;
                const targetWidth = element.offsetWidth;
                
                // Reset to 0 and set fixed width to prevent jumping
                element.textContent = '0';
                element.style.width = targetWidth + 'px';
                
                // Animation using requestAnimationFrame
                const duration = 2000; // Animation duration in milliseconds
                const startTime = performance.now();
                
                function animate(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Easing function (similar to jQuery's 'swing')
                    const easeInOut = 0.5 - Math.cos(progress * Math.PI) / 2;
                    const current = Math.floor(easeInOut * target);
                    
                    element.textContent = current;
                    
                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        element.textContent = target; // Ensure exact final value
                    }
                }
                
                requestAnimationFrame(animate);
            }

            // Function to calculate the width needed for a number
            function getNumberWidth(number, element) {
                // Create a temporary element to measure the width
                const temp = element.cloneNode(true);
                temp.style.cssText = 'visibility: hidden; position: absolute; white-space: nowrap;';
                temp.textContent = number;
                document.body.appendChild(temp);
                const width = temp.offsetWidth;
                document.body.removeChild(temp);
                return width;
            }

            // Function to reset numbers
            function resetNumbers(container) {
                const currentNumbers = container.querySelectorAll('.current-number');
                currentNumbers.forEach(function(element) {
                    element.textContent = '0';
                    // Reset the width and display styling
                    element.style.width = '';
                    element.style.display = '';
                    element.style.textAlign = '';
                    element.style.boxSizing = '';
                });
            }

            // Set up IntersectionObserver
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        const target = entry.target;
                        const currentNumber = target.querySelectorAll('.current-number');
                        const maxNumber = target.querySelectorAll('.max-number');

                        // Check if both numbers exist before triggering the animation
                        if (entry.isIntersecting && currentNumber.length > 0 && maxNumber.length > 0) {
                            // Animate numbers
                            currentNumber.forEach(function (element) {
                                animateNumber(element);
                            });
                            // maxNumber.forEach(function (element) {
                            //     animateNumber(element);
                            // });
                        } else {
                            resetNumbers(target); // Reset numbers when out of view
                        }
                    });
                },
                { threshold: 0.5 } // Trigger when 50% of the section is visible
            );

            // Observe each column individually
            columns.forEach(function (column) {
                const currentNumbers = column.querySelectorAll('.current-number');
                const maxNumbers = column.querySelectorAll('.max-number');
                
                if (currentNumbers.length > 0 && maxNumbers.length > 0) {
                    observer.observe(column); // Observe only if numbers exist
                }
            });
        },
    };

    return MB;
})(MB || {});