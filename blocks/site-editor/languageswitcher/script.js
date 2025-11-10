(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const switchers = document.querySelectorAll('.lang-switcher');
        
        switchers.forEach(function(switcher) {
            const button = switcher.querySelector('.lang-switcher__current');
            const selector = switcher.querySelector('.lang-switcher__selector');
            const dropdown = switcher.querySelector('.lang-switcher__dropdown');
            const links = dropdown.querySelectorAll('.language');

            // Keyboard navigation for button
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    // Open dropdown by adding class
                    selector.classList.add('is-open');
                    button.setAttribute('aria-expanded', 'true');
                    // Focus first link
                    if (links.length > 0) {
                        links[0].focus();
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selector.classList.add('is-open');
                    button.setAttribute('aria-expanded', 'true');
                    if (links.length > 0) {
                        links[0].focus();
                    }
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    selector.classList.remove('is-open');
                    button.setAttribute('aria-expanded', 'false');
                }
            });

            // Keyboard navigation for menu items
            links.forEach(function(link, index) {
                link.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        const nextIndex = (index + 1) % links.length;
                        links[nextIndex].focus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        const prevIndex = index === 0 ? links.length - 1 : index - 1;
                        links[prevIndex].focus();
                    } else if (e.key === 'Home') {
                        e.preventDefault();
                        links[0].focus();
                    } else if (e.key === 'End') {
                        e.preventDefault();
                        links[links.length - 1].focus();
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        selector.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');
                        button.focus();
                    } else if (e.key === 'Tab') {
                        // Let tab work naturally, just close the dropdown
                        selector.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');
                    }
                });
            });

            // Track hover state
            selector.addEventListener('mouseenter', function() {
                button.setAttribute('aria-expanded', 'true');
            });

            selector.addEventListener('mouseleave', function() {
                selector.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!switcher.contains(e.target)) {
                    selector.classList.remove('is-open');
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        });
    });
})();