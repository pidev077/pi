<?php
/**
 * Mobile menu drawer — full-screen overlay shown on < 992 px.
 * Loaded via wp_footer action (see inc/hooks.php).
 */

$logo         = get_theme_mod('custom_logo');
$logo_sticky  = get_field('logo_sticky', 'option');
$link_contact = get_field('link_contact_hd', 'option');
$current_lang = apply_filters('wpml_current_language', null);
$languages    = apply_filters('wpml_active_languages', null, ['skip_missing' => 0]);
$blog_url     = get_permalink(get_option('page_for_posts')) ?: home_url('/blog/');
$copyright    = get_field('footer_copyright', 'option')
                ?: '&copy; ' . date('Y') . ' ' . __('DD CLINIC. All Rights Reserved.', 'pi');

// Social
$social_fb = get_field('footer_social_facebook', 'option');
$social_ig = get_field('footer_social_instagram', 'option');
$social_tw = get_field('footer_social_twitter', 'option');
$social_yt = get_field('footer_social_youtube', 'option');

// Mega menu offers (ACF repeater — same data as desktop mega menu)
$col_label   = get_field('mega_menu_col_label', 'option') ?: __('ƯU ĐÃI ĐỘC QUYỀN THÁNG NÀY', 'pi');
$see_more    = get_field('mega_menu_see_more', 'option');
$offers      = get_field('mega_menu_offers', 'option');
$sm_url      = !empty($see_more['url'])    ? $see_more['url']    : $blog_url;
$sm_label    = !empty($see_more['title'])  ? $see_more['title']  : __('Xem Thêm', 'pi');
$sm_target   = !empty($see_more['target']) ? $see_more['target'] : '_self';
?>

<div class="mobile-menu" id="mobile-menu" aria-hidden="true">

    <div class="mobile-menu__overlay" aria-hidden="true"></div>

    <div class="mobile-menu__drawer" role="dialog"
         aria-modal="true"
         aria-label="<?php esc_attr_e('Menu', 'pi'); ?>">

        <!-- ── Header ─────────────────────────────────────────────── -->
        <div class="mobile-menu__header">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-menu__logo">
                <?php if (!empty($logo_sticky['url'])): ?>
                    <img class="mobile-menu__logo-img"
                         src="<?php echo esc_url($logo_sticky['url']); ?>"
                         alt="<?php echo esc_attr($logo_sticky['alt'] ?: get_bloginfo('name')); ?>">
                <?php else: ?>
                    <?php echo wp_get_attachment_image($logo, 'full', false, [
                        'alt'   => esc_attr(get_bloginfo('name')),
                        'class' => 'mobile-menu__logo-img',
                    ]); ?>
                <?php endif; ?>
            </a>
            <button class="mobile-menu__close"
                    aria-label="<?php esc_attr_e('Đóng menu', 'pi'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor"
                          stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <!-- ── Navigation accordion ───────────────────────────────── -->
        <nav class="mobile-menu__nav" aria-label="<?php esc_attr_e('Điều hướng chính', 'pi'); ?>">
            <?php
            if (has_nav_menu('primary-menu')) {
                wp_nav_menu([
                    'theme_location' => 'primary-menu',
                    'container'      => false,
                    'items_wrap'     => '<ul class="mob-nav-list">%3$s</ul>',
                    'walker'         => new Pi_Mobile_Walker(),
                    'depth'          => 3,
                ]);
            }
            ?>
        </nav>

        <!-- ── CTA ────────────────────────────────────────────────── -->
        <?php if (!empty($link_contact)): ?>
        <div class="mobile-menu__cta">
            <p class="mobile-menu__cta-label">
                <?php esc_html_e('KẾT NỐI VỚI CHUYÊN GIA CỦA CHÚNG TÔI', 'pi'); ?>
            </p>
            <a class="mobile-menu__cta-btn"
               href="<?php echo esc_url($link_contact['url']); ?>"
               target="<?php echo esc_attr($link_contact['target'] ?: '_self'); ?>">
                <?php echo esc_html($link_contact['title']); ?>
            </a>
        </div>
        <?php endif; ?>

        <!-- ── Offers (ACF repeater — same data as desktop mega menu) ── -->
        <?php if (!empty($offers)): ?>
        <div class="mobile-menu__posts">
            <div class="mobile-menu__posts-hd">
                <span class="mob-label"><?php echo esc_html($col_label); ?></span>
                <a href="<?php echo esc_url($sm_url); ?>"
                   target="<?php echo esc_attr($sm_target); ?>"
                   class="mob-see-more"><?php echo esc_html($sm_label); ?></a>
            </div>

            <?php foreach ($offers as $offer):
                $img     = $offer['offer_image'] ?? null;
                $ttl     = $offer['offer_title']   ?? '';
                $exc     = $offer['offer_excerpt']  ?? '';
                $link    = $offer['offer_link']     ?? [];
                $lk_url  = !empty($link['url'])    ? $link['url']    : '#';
                $lk_tgt  = !empty($link['target']) ? $link['target'] : '_self';
            ?>
            <a href="<?php echo esc_url($lk_url); ?>"
               target="<?php echo esc_attr($lk_tgt); ?>"
               class="mob-post">
                <?php if (!empty($img['url'])): ?>
                <div class="mob-post__img">
                    <img src="<?php echo esc_url($img['url']); ?>"
                         alt="<?php echo esc_attr($img['alt'] ?: $ttl); ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>
                <div class="mob-post__body">
                    <p class="mob-post__title"><?php echo esc_html($ttl); ?></p>
                    <p class="mob-post__excerpt"><?php echo esc_html($exc); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- ── Footer bar: social + lang + copyright ─────────────── -->
        <div class="mobile-menu__footer">

            <!-- Row 1: social left, language switcher right -->
            <div class="mobile-menu__footer-top">

                <?php if ($social_fb || $social_ig || $social_tw || $social_yt): ?>
                <div class="mobile-menu__social">
                    <span class="mobile-menu__social-label">
                        <?php esc_html_e('Theo Dõi DD CLINIC', 'pi'); ?>
                    </span>
                    <div class="mobile-menu__social-icons">
                        <?php if ($social_fb): ?>
                        <a href="<?php echo esc_url($social_fb); ?>" target="_blank"
                           rel="noopener noreferrer" aria-label="Facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 10V14H10V21H14V14H17L18 10H14V8C14 7.73478 14.1054 7.48043 14.2929 7.29289C14.4804 7.10536 14.7348 7 15 7H18V3H15C13.6739 3 12.4021 3.52678 11.4645 4.46447C10.5268 5.40215 10 6.67392 10 8V10H7Z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($social_ig): ?>
                        <a href="<?php echo esc_url($social_ig); ?>" target="_blank"
                           rel="noopener noreferrer" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16.5 7.5V7.51M4 8C4 6.93913 4.42143 5.92172 5.17157 5.17157C5.92172 4.42143 6.93913 4 8 4H16C17.0609 4 18.0783 4.42143 18.8284 5.17157C19.5786 5.92172 20 6.93913 20 8V16C20 17.0609 19.5786 18.0783 18.8284 18.8284C18.0783 19.5786 17.0609 20 16 20H8C6.93913 20 5.92172 19.5786 5.17157 18.8284C4.42143 18.0783 4 17.0609 4 16V8ZM9 12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12C15 11.2044 14.6839 10.4413 14.1213 9.87868C13.5587 9.31607 12.7956 9 12 9C11.2044 9 10.4413 9.31607 9.87868 9.87868C9.31607 10.4413 9 11.2044 9 12Z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($social_tw): ?>
                        <a href="<?php echo esc_url($social_tw); ?>" target="_blank"
                           rel="noopener noreferrer" aria-label="X (Twitter)">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 20L10.768 13.232M13.228 10.772L20 4M4 4L15.733 20H20L8.267 4H4Z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($social_yt): ?>
                        <a href="<?php echo esc_url($social_yt); ?>" target="_blank"
                           rel="noopener noreferrer" aria-label="YouTube">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 8C2 6.93913 2.42143 5.92172 3.17157 5.17157C3.92172 4.42143 4.93913 4 6 4H18C19.0609 4 18.0783 4.42143 20.8284 5.17157C21.5786 5.92172 22 6.93913 22 8V16C22 17.0609 21.5786 18.0783 20.8284 18.8284C20.0783 19.5786 19.0609 20 18 20H6C4.93913 20 3.92172 19.5786 3.17157 18.8284C2.42143 18.0783 2 17.0609 2 16V8Z"/>
                                <path d="M10 9L15 12L10 15V9Z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($languages)): ?>
                <div class="mob-lang">
                    <span class="mob-lang__label"><?php esc_html_e('Ngôn Ngữ:', 'pi'); ?></span>
                    <button class="mob-lang__toggle" aria-expanded="false">
                        <span><?php echo esc_html(strtoupper($current_lang)); ?></span>
                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" aria-hidden="true">
                            <path d="M1 1L5 5L9 1" stroke="currentColor"
                                  stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <ul class="mob-lang__dropdown">
                        <?php foreach ($languages as $lang): ?>
                        <li class="<?php echo $lang['active'] ? 'active' : ''; ?>">
                            <a href="<?php echo esc_url($lang['url']); ?>">
                                <?php echo esc_html(strtoupper($lang['language_code'])); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div><!-- /.mobile-menu__footer-top -->

            <!-- Row 2: copyright -->
            <p class="mobile-menu__copyright"><?php echo wp_kses_post($copyright); ?></p>

        </div><!-- /.mobile-menu__footer -->

    </div><!-- /.mobile-menu__drawer -->
</div><!-- /.mobile-menu -->
