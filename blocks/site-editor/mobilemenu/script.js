// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Replace 'your-burger-selector' with the actual selector (e.g., '.burger-menu', '#burger-btn', etc.)
    const burgerMenu = document.querySelector('.mobile-menu__button');
    
    // Check if element exists
    if (!burgerMenu) {
        console.error('Burger menu element not found. Check your selector.');
        return;
    }

    // Initialize isOpen variable
    let isOpen = false;
    
    burgerMenu.addEventListener('click', function(event) {
        event.stopPropagation();
        isOpen = !isOpen;
        
        if (isOpen) {
            document.querySelector('.mobile-menu__button .burger-icon').classList.add('hidden');
            document.querySelector('.mobile-menu__button .close-icon').classList.remove('hidden');
            document.querySelector('.mobile-menu').classList.remove('hidden');
        } else {
            document.querySelector('.mobile-menu__button .burger-icon').classList.remove('hidden');
            document.querySelector('.mobile-menu__button .close-icon').classList.add('hidden');
            document.querySelector('.mobile-menu').classList.add('hidden');
        }
    });

    initMultilevelMenu('.mobile-menu__list');
    
    function initMultilevelMenu(parentMenu) {
        // Check if menu exists
        const menuElement = document.querySelector(parentMenu);
        if (!menuElement) {
            return;
        }
    
        // Animation helper functions
        const animate = (element, property, from, to, duration, callback) => {
            const start = performance.now();
            const change = to - from;
            
            const step = (timestamp) => {
                const elapsed = timestamp - start;
                const progress = Math.min(elapsed / duration, 1);
                
                // Linear easing
                const currentValue = from + (change * progress);
                element.style[property] = currentValue + 'px';
                
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    if (callback) callback();
                }
            };
            
            requestAnimationFrame(step);
        };
    
        const slideUp = (element, duration = 500, callback) => {
            const currentHeight = element.offsetHeight;
            element.style.height = currentHeight + 'px';
            element.style.overflow = 'hidden';
            
            animate(element, 'height', currentHeight, 0, duration, () => {
                element.style.display = 'none';
                element.style.height = 'auto';
                element.style.overflow = '';
                if (callback) callback();
            });
        };
    
        const slideDown = (element, duration = 150, callback) => {
            element.style.display = 'block';
            element.style.height = 'auto';
            const targetHeight = element.offsetHeight;
            element.style.height = '0px';
            element.style.overflow = 'hidden';
            
            animate(element, 'height', 0, targetHeight, duration, () => {
                element.style.height = 'auto';
                element.style.overflow = '';
                if (callback) callback();
            });
        };
    
        // Handle menu toggle for all screen sizes
        function initMenu() {
            // Target the correct structure: li.has-submenu > div.menu-item
            const hasSubmenuItems = menuElement.querySelectorAll('li.has-submenu');
            
            hasSubmenuItems.forEach(parentLi => {
                const menuItemDiv = parentLi.querySelector('div.menu-item');
                const link = menuItemDiv?.querySelector('a');
                const arrow = menuItemDiv?.querySelector('.menu-arrow');
                const submenu = parentLi.querySelector('ul.sub-menu');
                
                // Only proceed if we have all required elements
                if (!link || !arrow || !submenu) {
                    return;
                }
                
                // Click handler for arrow only (toggles submenu)
                const arrowClickHandler = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Check if currently open
                    const isOpen = parentLi.classList.contains('open');
                    
                    // Close all other submenus at the same level (siblings)
                    const siblings = Array.from(parentLi.parentNode.children)
                        .filter(child => child !== parentLi && child.classList.contains('has-submenu'));
                    
                    siblings.forEach(siblingLi => {
                        const siblingDiv = siblingLi.querySelector('div.menu-item');
                        const siblingSubmenu = siblingLi.querySelector('ul.sub-menu');
                        const siblingArrow = siblingDiv?.querySelector('.menu-arrow');
                        const siblingLink = siblingDiv?.querySelector('a');
                        
                        if (siblingLi.classList.contains('open')) {
                            siblingLi.classList.remove('open');
                            siblingArrow?.classList.remove('arrow-active');
                            siblingLink?.classList.remove('active');
                            
                            // Also close all nested submenus in sibling
                            const nestedHasSubmenu = siblingSubmenu.querySelectorAll('li.has-submenu');
                            nestedHasSubmenu.forEach(nested => {
                                nested.classList.remove('open');
                            });
                            
                            siblingSubmenu.querySelectorAll('.menu-arrow').forEach(arrow => {
                                arrow.classList.remove('arrow-active');
                            });
                            
                            siblingSubmenu.querySelectorAll('a').forEach(link => {
                                link.classList.remove('active');
                            });
                            
                            siblingSubmenu.querySelectorAll('ul.sub-menu').forEach(nestedSub => {
                                slideUp(nestedSub, 500);
                            });
                            
                            // Smooth height animation for closing siblings
                            slideUp(siblingSubmenu, 500);
                        }
                    });
                    
                    // Toggle current submenu
                    if (!isOpen) {
                        // Open submenu
                        parentLi.classList.add('open');
                        arrow.classList.add('arrow-active');
                        link.classList.add('active');
                        
                        // Show and animate
                        slideDown(submenu, 150);
                        
                    } else {
                        // Close submenu
                        parentLi.classList.remove('open');
                        arrow.classList.remove('arrow-active');
                        link.classList.remove('active');
                        
                        // Close all nested submenus (if any exist in future)
                        submenu.querySelectorAll('li.has-submenu').forEach(nested => {
                            nested.classList.remove('open');
                        });
                        
                        submenu.querySelectorAll('.menu-arrow').forEach(nestedArrow => {
                            nestedArrow.classList.remove('arrow-active');
                        });
                        
                        submenu.querySelectorAll('a').forEach(nestedLink => {
                            nestedLink.classList.remove('active');
                        });
                        
                        submenu.querySelectorAll('ul.sub-menu').forEach(nestedSubmenu => {
                            slideUp(nestedSubmenu, 500);
                        });
                        
                        // Animate and hide
                        slideUp(submenu, 500);
                    }
                };
                
                // Click handler for link (allows navigation)
                const linkClickHandler = (e) => {
                    // Don't prevent default - let the link navigate normally
                    // Only prevent if the link is empty or has href="#"
                    const href = link.getAttribute('href');
                    if (!href || href === '#' || href === 'javascript:void(0)') {
                        e.preventDefault();
                        // If no valid href, treat it like arrow click
                        arrowClickHandler(e);
                    }
                    // Otherwise, let the link navigate normally
                };
                
                // Remove existing event listeners
                link.removeEventListener('click', linkClickHandler);
                arrow.removeEventListener('click', arrowClickHandler);
                
                // Add event listeners - separate handlers for link and arrow
                if (!link.__boundLink) {
                    link.addEventListener('click', linkClickHandler);
                    link.__boundLink = true;
                }

                if (!arrow.__boundArrow) {
                    arrow.addEventListener('click', arrowClickHandler);
                    arrow.__boundArrow = true;
                }
            });
        }
    
        // Clean up any existing handlers and initialize menu
        initMenu();
    
        // Reinitialize on window resize
        let resizeTimeout;
        const resizeHandler = () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                initMenu();
            }, 250);
        };
        
        window.removeEventListener('resize', resizeHandler);
        window.addEventListener('resize', resizeHandler);
    
        // Keyboard accessibility
        const arrowKeydownHandler = (e) => {
            if (e.which === 13 || e.which === 32) {
                e.preventDefault();
                e.target.click();
            }
        };
    
        const linkKeydownHandler = (e) => {
            // Allow Enter to follow the link naturally, but handle Space for accessibility
            if (e.which === 32) { // Space bar
                const href = e.target.getAttribute('href');
                if (!href || href === '#' || href === 'javascript:void(0)') {
                    e.preventDefault();
                    // Treat like arrow click if no valid href
                    const parentLi = e.target.closest('li.has-submenu');
                    const arrow = parentLi?.querySelector('.menu-arrow');
                    if (arrow) arrow.click();
                }
            }
            
            if (e.which === 38 || e.which === 40) { // Up/Down arrows
                e.preventDefault();
                const visibleLinks = Array.from(menuElement.querySelectorAll('div.menu-item a'))
                    .filter(link => link.offsetParent !== null); // Check if visible
                const currentIndex = visibleLinks.indexOf(e.target);
                
                if (e.which === 38 && currentIndex > 0) {
                    visibleLinks[currentIndex - 1].focus();
                } else if (e.which === 40 && currentIndex < visibleLinks.length - 1) {
                    visibleLinks[currentIndex + 1].focus();
                }
            }
            
            if (e.which === 27) { // Escape
                menuElement.querySelectorAll('li.has-submenu').forEach(item => {
                    item.classList.remove('open');
                });
                menuElement.querySelectorAll('.menu-arrow').forEach(arrow => {
                    arrow.classList.remove('arrow-active');
                });
                menuElement.querySelectorAll('div.menu-item a').forEach(link => {
                    link.classList.remove('active');
                });
                menuElement.querySelectorAll('ul.sub-menu').forEach(submenu => {
                    slideUp(submenu, 300);
                });
                e.target.blur();
            }
        };
    
        // Remove existing keyboard event listeners and add new ones
        menuElement.querySelectorAll('.menu-arrow').forEach(arrow => {
            if (!arrow.__kbdBound) {
                arrow.addEventListener('keydown', arrowKeydownHandler);
                arrow.__kbdBound = true;
            }
        });
    
        menuElement.querySelectorAll('div.menu-item a').forEach(link => {
            if (!link.__kbdBound) {
                link.addEventListener('keydown', linkKeydownHandler);
                link.__kbdBound = true;
            }
        });
    }
});
