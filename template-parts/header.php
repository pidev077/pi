<?php
/**
 * Header template
 */

$logo        = get_theme_mod('custom_logo');
$logo_sticky  = get_field('logo_sticky', 'option');
$link_contact = get_field('link_contact_hd', 'option');

// WPML language switcher
$current_lang = apply_filters('wpml_current_language', null);
$languages    = apply_filters('wpml_active_languages', null, array('skip_missing' => 0));
?>

<header id="header" class="header pi">
    <div class="container">
        <div class="header-inner d-flex align-items-center justify-content-between gap-3">

            <!-- Logo -->
            <div class="header__logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="link-logo d-flex">
                    <?php echo wp_get_attachment_image($logo, 'full', false, array('class' => 'logo-main', 'alt' => get_bloginfo('name'))); ?>
                    <?php if (!empty($logo_sticky)): ?>
                        <img class="logo-sticky"
                             src="<?php echo esc_url($logo_sticky['url']); ?>"
                             alt="<?php echo esc_attr($logo_sticky['alt'] ?: get_bloginfo('name')); ?>"
                             width="<?php echo esc_attr($logo_sticky['width']); ?>"
                             height="<?php echo esc_attr($logo_sticky['height']); ?>"
                        />
                    <?php endif; ?>
                </a>
            </div>

            <!-- Navigation -->
            <div class="header__menu">
                <?php
                if (has_nav_menu('primary-menu')) {
                    wp_nav_menu([
                        'theme_location' => 'primary-menu',
                        'menu_class'     => 'primary-menu d-flex align-items-center p-0 m-0',
                        'container'      => 'nav',
                        'container_class' => 'menu-container',
                        'bootstrap'      => true,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s navbar-nav">%3$s</ul>',
                    ]);
                }
                ?>

                <?php if (!empty($link_contact)): ?>
                    <div class="header__button d-block d-lg-none">
                        <a class="pi-btn lg" href="<?php echo esc_url($link_contact['url']); ?>" target="<?php echo esc_attr($link_contact['target']); ?>">
                            <?php echo esc_html($link_contact['title']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: CTA + Language -->
            <div class="header__right d-none d-lg-flex align-items-center gap-3">

                <?php if (!empty($link_contact)): ?>
                    <div class="header__button">
                        <a class="pi-btn lg" href="<?php echo esc_url($link_contact['url']); ?>" target="<?php echo esc_attr($link_contact['target']); ?>">
                            <?php echo esc_html($link_contact['title']); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($languages)): ?>
                    <div class="header__lang">
                        <div class="lang-switcher">
                            <button class="lang-switcher__toggle" aria-expanded="false" aria-label="<?php esc_attr_e('Language switcher', 'pi'); ?>">
                                <svg class="lang-switcher__icon" width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M12 2C12 2 8.5 6.5 8.5 12C8.5 17.5 12 22 12 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M12 2C12 2 15.5 6.5 15.5 12C15.5 17.5 12 22 12 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M2.5 12H21.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M3.5 7.5H20.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M3.5 16.5H20.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <span class="lang-switcher__current"><?php echo esc_html(strtoupper($current_lang)); ?></span>
                                <svg class="lang-switcher__chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <ul class="lang-switcher__dropdown">
                                <?php foreach ($languages as $lang): ?>
                                    <li class="<?php echo $lang['active'] ? 'active' : ''; ?>">
                                        <a href="<?php echo esc_url($lang['url']); ?>">
                                            <?php echo esc_html(strtoupper($lang['language_code'])); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Hamburger (mobile) -->
            <button id="btn-toggle-menu-mobile" class="header__hamberger d-flex d-lg-none flex-wrap"
                aria-label="Toggle menu" aria-expanded="false">
                <span class="header__hamberger--open">
                    <svg width="30" height="14" viewBox="0 0 30 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1H29" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M1 13H29" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="header__hamberger--close">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 21L21 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M1 1L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
            </button>

        </div>
    </div>
</header>
