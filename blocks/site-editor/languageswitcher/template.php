<?php
    /**
     * Main Menu Block Template
     * Path: /blocks/site-editor/mainmenu/template.php
     */

    // Create class attribute allowing for custom "className" and "align" values
    $class_name = 'mainmenu';
    if (!empty($block['className'])) {
        $class_name .= ' ' . $block['className'];
    }
    if (!empty($block['align'])) {
        $class_name .= ' align' . $block['align'];
    }

?>

<?php if (function_exists('pll_the_languages')) : ?> 
    <nav class="lang-switcher <?php echo $class_name; ?>" aria-label="Language selector"> 
        <div class="lang-switcher__selector dropdown"> 
            <?php $langs_array = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0, 'hide_current' => 0 ) ); ?> 
            <?php if ($langs_array && is_array($langs_array) && count($langs_array) > 1) : ?> 
                <button
                    type="button"
                    role="button"
                    class="lang-switcher__current"
                    aria-label="<?php echo esc_attr(sprintf(__('Select language. Current: %s', 'ctheme'), pll_current_language('name'))); ?>"
                > 
                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg"> 
                        <path d="M12.5 22C18.0228 22 22.5 17.5228 22.5 12C22.5 6.47715 18.0228 2 12.5 2C6.97715 2 2.5 6.47715 2.5 12C2.5 17.5228 6.97715 22 12.5 22Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> 
                        <path d="M2.5 12H22.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> 
                        <path d="M12.5 2C15.0013 4.73835 16.4228 8.29203 16.5 12C16.4228 15.708 15.0013 19.2616 12.5 22C9.99872 19.2616 8.57725 15.708 8.5 12C8.57725 8.29203 9.99872 4.73835 12.5 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg> 
                    <span>
                        <?php echo pll_current_language('slug'); ?>
                    </span>
                    <svg class="arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" stroke="white" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false">
                        <path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path>
                    </svg> 
                </button> 
                <ul class="lang-switcher__dropdown"> 
                    <?php foreach ($langs_array as $lang) : ?> 
                        <?php if (!$lang['current_lang']) : ?> 
                            <li class="lang-switcher__item">
                                <a href="<?php echo $lang['url']; ?>" class="language"> 
                                    <?php echo $lang['slug']; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?> 
                </ul> 
            <?php endif; ?> 
        </div> 
    </nav> 
<?php endif; ?>