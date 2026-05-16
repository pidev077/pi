<?php
/**
 * Footer template
 */

$logo_footer   = get_field('logo_footer', 'option');
$footer_desc   = get_field('footer_description', 'option');
$social_fb     = get_field('footer_social_facebook', 'option');
$social_ig     = get_field('footer_social_instagram', 'option');
$social_tw     = get_field('footer_social_twitter', 'option');
$social_yt     = get_field('footer_social_youtube', 'option');
$copyright     = get_field('footer_copyright', 'option');
$copyright     = $copyright ?: '&copy; ' . date('Y') . ' ' . __('DD CLINIC. All Rights Reserved.', 'pi');
?>

<footer id="footer" class="footer">
    <div class="container">

        <div class="footer__grid">

            <!-- ── Column 1: Brand ─────────────────────────────────── -->
            <div class="footer__brand">

                <?php if ($logo_footer): ?>
                    <div class="footer__logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <img src="<?php echo esc_url($logo_footer); ?>" alt="<?php bloginfo('name'); ?>">
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($footer_desc): ?>
                    <p class="footer__desc"><?php echo wp_kses_post($footer_desc); ?></p>
                <?php endif; ?>

                <?php if ($social_fb || $social_ig || $social_tw || $social_yt): ?>
                    <div class="footer__social">
                        <span class="footer__social-label"><?php esc_html_e('THEO DÕI DD CLINIC', 'pi'); ?></span>
                        <div class="footer__social-icons">

                            <?php if ($social_fb): ?>
                                <a href="<?php echo esc_url($social_fb); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_ig): ?>
                                <a href="<?php echo esc_url($social_ig); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                        <circle cx="12" cy="12" r="4"/>
                                        <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_tw): ?>
                                <a href="<?php echo esc_url($social_tw); ?>" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ($social_yt): ?>
                                <a href="<?php echo esc_url($social_yt); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                                    <svg width="20" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/>
                                        <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#120a00"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- ── Column 2: Điều Hướng ────────────────────────────── -->
            <div class="footer__nav-col">
                <h4 class="footer__col-title"><?php esc_html_e('ĐIỀU HƯỚNG', 'pi'); ?></h4>
                <?php
                if (has_nav_menu('footer-navigation')) {
                    wp_nav_menu([
                        'theme_location' => 'footer-navigation',
                        'container'      => false,
                        'menu_class'     => 'footer__nav-list',
                        'depth'          => 1,
                        'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
                        'walker'         => new Pi_Footer_Walker(),
                    ]);
                }
                ?>
            </div>

            <!-- ── Column 3: Dịch Vụ ───────────────────────────────── -->
            <div class="footer__nav-col">
                <h4 class="footer__col-title"><?php esc_html_e('DỊCH VỤ', 'pi'); ?></h4>
                <?php
                if (has_nav_menu('footer-services')) {
                    wp_nav_menu([
                        'theme_location' => 'footer-services',
                        'container'      => false,
                        'menu_class'     => 'footer__nav-list',
                        'depth'          => 1,
                        'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
                        'walker'         => new Pi_Footer_Walker(),
                    ]);
                }
                ?>
            </div>

            <!-- ── Column 4: Hỗ Trợ ────────────────────────────────── -->
            <div class="footer__nav-col">
                <h4 class="footer__col-title"><?php esc_html_e('HỖ TRỢ', 'pi'); ?></h4>
                <?php
                if (has_nav_menu('footer-support')) {
                    wp_nav_menu([
                        'theme_location' => 'footer-support',
                        'container'      => false,
                        'menu_class'     => 'footer__nav-list',
                        'depth'          => 1,
                        'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
                        'walker'         => new Pi_Footer_Walker(),
                    ]);
                }
                ?>
            </div>

        </div><!-- /.footer__grid -->

        <!-- ── Bottom bar ──────────────────────────────────────────── -->
        <div class="footer__bottom">
            <div class="footer__badges">
                <a href="https://www.dmca.com/Protection/Status.aspx" title="DMCA.com Protection Status" class="dmca-badge" target="_blank">
                    <img src="https://images.dmca.com/Badges/dmca-badge-w100-5x1-09.png" alt="DMCA.com Protection Status" width="100">
                </a>
            </div>
            <p class="footer__copyright"><?php echo wp_kses_post($copyright); ?></p>
        </div>

    </div><!-- /.container -->
</footer>

