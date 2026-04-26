<?php
/**
 * Header template
 */

$logo = get_theme_mod('custom_logo');
$white_logo = get_theme_mod('white_logo');
$link_contact = get_field('link_contact_hd', 'option');
?>

<header id="header" class="header pi">
    <div class="container">
        <div class="header-inner d-flex align-items-center justify-content-between gap-3">
            <div class="header__logo">
                <a href="/" class="link-logo d-flex">
                    <?php
                    echo wp_get_attachment_image($logo, 'full', false, array('class' => 'logo-main', 'alt' => get_bloginfo('name')));
                    ?>
                </a>
            </div>

            <div class="header__menu">
                <?php
                if (has_nav_menu('primary-menu')) {
                    wp_nav_menu([
                        'theme_location' => 'primary-menu',
                        'menu_class' => 'primary-menu d-flex align-items-center p-0 m-0',
                        'container' => 'nav',
                        'container_class' => 'menu-container',
                        'bootstrap' => true,
                        'items_wrap' => '<ul id="%1$s" class="%2$s navbar-nav">%3$s</ul>'
                    ]);
                }
                ?>

                <?php if (!empty($link_contact)): ?>
                    <div class="header__button d-block d-lg-none">
                        <a class="flip-btn lg" href="<?= $link_contact['url'] ?>" target=" <?= $link_contact['target'] ?>">
                            <?= $link_contact['title'] ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($link_contact)): ?>
                <div class="header__button d-none d-lg-block">
                    <a class="flip-btn lg" href="<?= $link_contact['url'] ?>" target="<?= $link_contact['target'] ?>">
                        <?= $link_contact['title'] ?>
                    </a>
                </div>
            <?php endif; ?>

            <button id="btn-toggle-menu-mobile" class="header__hamberger d-flex d-lg-none flex-wrap"
                aria-label="Toggle menu" aria-expanded="false">
                <span class="header__hamberger--open">
                    <svg width="34" height="14" viewBox="0 0 34 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1H33" stroke="#FFF5D2" stroke-width="2" stroke-linecap="round" />
                        <path d="M1 13H33" stroke="#FFF5D2" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </span>

                <span class="header__hamberger--close">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.00497 24.995L25.005 1.2338" stroke="#FFF5D2" stroke-width="2"
                            stroke-linecap="round" />
                        <path d="M1.00497 0.994987L25.005 24.7562" stroke="#FFF5D2" stroke-width="2"
                            stroke-linecap="round" />
                    </svg>
                </span>
            </button>
        </div>
    </div>
</header>