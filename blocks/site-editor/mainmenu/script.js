/* ---------------------------------------------------------------------
	Global Js
	Target Browsers: All

	HEADS UP! This script is for general functionality found on ALL pages and not tied to specific components, blocks, or
	plugins. 

	If you need to add JS for a specific block or component, create a script file in js/components or js/blocks and
	add your JS there. (Don't forget to enqueue it!)
------------------------------------------------------------------------ */
var MB = ( function( MB, $ ) {

	/**
	 * Doc Ready
	 * 
	 * Use a separate $(function() {}) block for each function call
	 */

    $(() => {
        MB.MenuWCAG.init();
    });

	/**
	 * Example Code Block - This should be removed
	 * @type {Object}
	 */
	MB.CodeBlock = {
		init() {
            
		},
	};

    MB.MenuWCAG = {
        init() {
            var $menu = $('#menu-header-menu, #menu-header'); // Adjust if your menu ID differs
            if (!$menu.length) return;

            // Roles and initial ARIA state
            $menu.attr('role', 'menubar');
            // Ensure every submenu has proper role and ARIA
            $menu.find('ul.sub-menu').each(function () {
                var $sub = $(this);
                $sub.attr({ 'role': 'menu', 'aria-hidden': $sub.is(':visible') ? 'false' : 'true' });
            });
            // Ensure triggers have ARIA
            $menu.find('li.has-submenu').each(function () {
                var $li = $(this);
                var $trigger = MB.MenuWCAG.getTrigger($li);
                if ($trigger.length) {
                    if (!$trigger.attr('aria-haspopup')) $trigger.attr('aria-haspopup', 'true');
                    if (!$trigger.attr('aria-expanded')) $trigger.attr('aria-expanded', 'false');
                }
            });

            // Delegated events for flexibility across any depth
            $menu.on('click', 'a[aria-haspopup="true"]', function (e) {
                var $link = $(this);
                var $li = $link.closest('li');
                var $submenu = MB.MenuWCAG.getSubmenu($li);
                if ($submenu.length) {
                    MB.MenuWCAG.toggleAtLevel($link);
                }
            });

            // Hover support for mouse users (ARIA only; let CSS control display)
            $menu.on('mouseenter', 'li.has-submenu', function () {
                var $li = $(this);
                var $trigger = MB.MenuWCAG.getTrigger($li);
                var $submenu = MB.MenuWCAG.getSubmenu($li);
                MB.MenuWCAG.setAria($trigger, $submenu, true);
            });
            $menu.on('mouseleave', 'li.has-submenu', function () {
                var $li = $(this);
                var $trigger = MB.MenuWCAG.getTrigger($li);
                var $submenu = MB.MenuWCAG.getSubmenu($li);
                MB.MenuWCAG.setAria($trigger, $submenu, false);
            });

            // Keyboard handling on any link inside the menu
            $menu.on('keydown', 'a', function (e) {
                var $link = $(this);
                var $li = $link.closest('li');
                var $submenu = MB.MenuWCAG.getSubmenu($li);
                var isTopLevel = $link.closest('ul').is($menu);

                switch (e.key) {
                    case 'Enter':
                    case ' ': // Space
                        if ($submenu.length) {
                            e.preventDefault();
                            MB.MenuWCAG.toggleAtLevel($link);
                        }
                        break;

                    case 'ArrowDown':
                        e.preventDefault();
                        if ($submenu.length) {
                            // Open and go to first child
                            MB.MenuWCAG.open($link, $submenu, { closeSiblings: true });
                            MB.MenuWCAG.focusFirstItem($submenu);
                        } else if (!isTopLevel) {
                            // Move to next within same submenu
                            MB.MenuWCAG.focusNextSibling($link);
                        }
                        break;

                    case 'ArrowUp':
                        if (!isTopLevel) {
                            e.preventDefault();
                            MB.MenuWCAG.focusPrevSibling($link);
                        }
                        break;

                    case 'ArrowRight':
                        e.preventDefault();
                        if (isTopLevel) {
                            MB.MenuWCAG.focusNextTop($menu, $link);
                        } else if ($submenu.length) {
                            MB.MenuWCAG.open($link, $submenu, { closeSiblings: true });
                            MB.MenuWCAG.focusFirstItem($submenu);
                        }
                        break;

                    case 'ArrowLeft':
                        e.preventDefault();
                        if (isTopLevel) {
                            MB.MenuWCAG.focusPrevTop($menu, $link);
                        } else {
                            // Close current submenu and focus parent trigger
                            var $parentTrigger = MB.MenuWCAG.getParentTrigger($link);
                            if ($parentTrigger.length) {
                                var $parentSub = MB.MenuWCAG.getSubmenu($parentTrigger.closest('li'));
                                MB.MenuWCAG.close($parentTrigger, $parentSub);
                                $parentTrigger.focus();
                            }
                        }
                        break;

                    case 'Escape':
                        e.preventDefault();
                        MB.MenuWCAG.closeAll($menu);
                        $link.blur();
                        break;
                }
            });

            // Click outside closes all
            $(document).on('click.menuwcag', function (e) {
                if (!$menu[0].contains(e.target)) {
                    MB.MenuWCAG.closeAll($menu);
                }
            });

            // Esc anywhere closes all
            $(document).on('keydown.menuwcag', function (e) {
                if (e.key === 'Escape') MB.MenuWCAG.closeAll($menu);
            });
        },

        // Helpers — generic and depth-agnostic
        getTrigger($li) {
            // Support markup with div.menu-item > a or direct a
            return $li.find('> .menu-item > a, > a').first();
        },
        getSubmenu($li) {
            return $li.children('ul.sub-menu').first();
        },
        getParentTrigger($link) {
            var $parentLi = $link.closest('ul').closest('li.has-submenu');
            return $parentLi.length ? this.getTrigger($parentLi) : $();
        },
        setAria($link, $submenu, expanded) {
            if ($link && $link.length) $link.attr('aria-expanded', expanded ? 'true' : 'false');
            if ($submenu && $submenu.length) $submenu.attr('aria-hidden', expanded ? 'false' : 'true');
        },
        focusFirstItem($submenu) {
            var $first = $submenu.find('> li:visible').find('> .menu-item > a, > a').first();
            if ($first.length) $first.focus();
        },
        focusNextSibling($link) {
            var $items = $link.closest('ul').children('li:visible');
            var $current = $link.closest('li');
            var idx = $items.index($current);
            var nextIdx = (idx + 1) % $items.length;
            var $nextLink = $items.eq(nextIdx).find('> .menu-item > a, > a').first();
            if ($nextLink.length) $nextLink.focus();
        },
        focusPrevSibling($link) {
            var $items = $link.closest('ul').children('li:visible');
            var $current = $link.closest('li');
            var idx = $items.index($current);
            var prevIdx = idx === 0 ? $items.length - 1 : idx - 1;
            var $prevLink = $items.eq(prevIdx).find('> .menu-item > a, > a').first();
            if ($prevLink.length) $prevLink.focus();
        },
        focusNextTop($menu, $link) {
            var $items = $menu.children('li:visible');
            var $current = $link.closest('li');
            var idx = $items.index($current);
            var nextIdx = (idx + 1) % $items.length;
            var $nextLink = $items.eq(nextIdx).find('> .menu-item > a, > a').first();
            if ($nextLink.length) $nextLink.focus();
        },
        focusPrevTop($menu, $link) {
            var $items = $menu.children('li:visible');
            var $current = $link.closest('li');
            var idx = $items.index($current);
            var prevIdx = idx === 0 ? $items.length - 1 : idx - 1;
            var $prevLink = $items.eq(prevIdx).find('> .menu-item > a, > a').first();
            if ($prevLink.length) $prevLink.focus();
        },
        toggleAtLevel($link) {
            var $li = $link.closest('li');
            var $submenu = this.getSubmenu($li);
            if (!$submenu.length) return;
            var expanded = $link.attr('aria-expanded') === 'true';
            if (expanded) {
                this.close($link, $submenu);
            } else {
                // Close open siblings at this level
                var $siblings = $li.siblings('.has-submenu');
                var self = this;
                $siblings.each(function () {
                    var $sli = $(this);
                    var $tr = self.getTrigger($sli);
                    var $sub = self.getSubmenu($sli);
                    self.close($tr, $sub);
                });
                this.open($link, $submenu);
            }
        },
        open($link, $submenu, opts) {
            if (!$submenu || !$submenu.length) return;
            var options = $.extend({ closeSiblings: false }, opts);

            if (options.closeSiblings) {
                var $li = $link.closest('li');
                var self = this;
                $li.siblings('.has-submenu').each(function () {
                    var $sli = $(this);
                    var $tr = self.getTrigger($sli);
                    var $sub = self.getSubmenu($sli);
                    self.close($tr, $sub);
                });
            }

            // ARIA
            this.setAria($link, $submenu, true);

            // Inline styles to match CSS (no new classes)
            // Detect if this submenu is nested inside another submenu (not itself)
            var isNested = $submenu.parents('.sub-menu').length > 0;
            $submenu.css('display', 'flex');
            if (isNested) {
                // Inner sub menus
                $submenu.css({ left: 'calc(100% - 2px)', top: '0px', right: '' });
                // Match nested hover border radii adjustments
                $submenu.css({ 'border-top-left-radius': '0', 'border-bottom-left-radius': '0' });
            } else {
                // First sub menu of the top level
                $submenu.css({ top: '80%', left: '0', right: '' });
            }
        },
        close($link, $submenu) {
            if (!$submenu || !$submenu.length) return;
            // Close all nested descendants too
            var self = this;
            $submenu.find('li.has-submenu').each(function () {
                var $nli = $(this);
                var $ntr = self.getTrigger($nli);
                var $nsub = self.getSubmenu($nli);
                self.setAria($ntr, $nsub, false);
                // Clear inline styles on descendants
                if ($nsub && $nsub.length) {
                    $nsub.css({ display: '', left: '', right: '', top: '', 'border-top-left-radius': '', 'border-bottom-left-radius': '' });
                }
            });
            // ARIA for current
            this.setAria($link, $submenu, false);
            // Clear inline styles for current
            $submenu.css({ display: '', left: '', right: '', top: '', 'border-top-left-radius': '', 'border-bottom-left-radius': '' });
        },
        closeAll($menu) {
            var self = this;
            $menu.find('li.has-submenu').each(function () {
                var $li = $(this);
                var $tr = self.getTrigger($li);
                var $sub = self.getSubmenu($li);
                self.close($tr, $sub);
            });
        }
    }
	
	return MB;

} ( MB || {}, jQuery ) );