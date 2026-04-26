<?php
/**
 * Footer template
 */


// ===== INFO GROUP =====
$info = get_field('info_group', 'option');
$phone   = $info['phone_number'] ?? '';
$address_1 = $info['address_1'] ?? '';
$address_2 = $info['address_2'] ?? '';


// ===== BOTTOM LINKS =====
$background_image      = get_field('background_image', 'option');
$logo_footer      = get_field('logo_footer', 'option');
?>


<footer id="footer" class="footer" style="background-image: url('<?= $background_image; ?>');">
   <div class="container">
      <div class="footer-top">
         <div class="footer-top__left">
            <?php if ($address_1): ?>
            <p class="footer-top__address"><?= esc_html($address_1); ?></p>
            <?php endif; ?>
         </div>


         <div class="footer-top__center">
            <?php if ($phone): ?>
            <?php
                  $phone_url   = esc_url($phone['url']);
                  $phone_title = esc_html($phone['title']);
                  $phone_target = $phone['target'] ? ' target="_blank" rel="noopener"' : '';
              ?>
            <a class="footer-top__phone" href="<?= $phone_url; ?>" <?= $phone_target; ?>>
               <svg xmlns="http://www.w3.org/2000/svg" width="33" height="35" viewBox="0 0 33 35" fill="none">
                  <g clip-path="url(#clip0_121_45)">
                     <path
                        d="M21.9329 24.1937L23.835 27.3762C24.9082 29.1717 24.3695 31.4811 22.6064 32.5799L19.6158 34.4433C18.3627 35.2235 16.9737 35.1473 15.692 34.4412C10.7308 31.7067 6.56074 27.5831 3.78067 22.5413C1.29905 18.0395 0.0347359 12.981 -1.49767e-05 7.82329C-0.00921373 6.49887 0.574396 5.29456 1.667 4.61146L4.79867 2.65303C6.53927 1.56467 8.79501 2.12661 9.85491 3.90747L11.7621 7.11199C12.819 8.88867 12.2599 11.1772 10.5356 12.2603L8.95957 13.2494C8.48941 13.544 8.25331 14.1132 8.42502 14.6752C9.48287 18.1408 11.3369 21.2492 13.8707 23.7905C14.2591 24.179 14.8621 24.2104 15.3036 23.9388L16.9206 22.9444C18.6305 21.8937 20.8719 22.417 21.9339 24.1947L21.9329 24.1937Z"
                        fill="white" />
                     <path
                        d="M32.8089 19.9176C32.7005 20.656 32.1466 21.101 31.4516 21.0393C30.8465 20.9861 30.27 20.3468 30.3733 19.6784C31.6427 11.4331 26.0846 3.69758 18.0112 2.46612C17.3918 2.37212 17.0013 1.64724 17.079 1.08843C17.1761 0.388619 17.7996 -0.0866262 18.4466 0.0126009C27.7537 1.44565 34.2142 10.3855 32.8099 19.9165L32.8089 19.9176Z"
                        fill="white" />
                     <path
                        d="M25.5388 18.9534C26.3953 13.4217 22.7199 8.26189 17.3304 7.42212C16.6661 7.31872 16.2398 6.64815 16.339 5.98907C16.4381 5.33 17.0636 4.8558 17.73 4.96024C24.4032 5.99743 29.0108 12.4211 27.955 19.2375C27.8538 19.8903 27.2855 20.3269 26.6569 20.2809C26.0938 20.2391 25.4243 19.6929 25.5388 18.9534Z"
                        fill="white" />
                     <path
                        d="M23.1226 18.449C23.0132 19.1864 22.3775 19.5885 21.7581 19.5102C21.1387 19.4319 20.6031 18.8344 20.7064 18.1388C21.1183 15.3469 19.2795 12.7878 16.5577 12.3565C15.8781 12.2489 15.4835 11.525 15.6 10.9109C15.7298 10.2299 16.3421 9.79431 17.0391 9.91025C20.9997 10.5714 23.7277 14.3608 23.1236 18.449H23.1226Z"
                        fill="white" />
                  </g>
                  <defs>
                     <clipPath id="clip0_121_45">
                        <rect width="33" height="35" fill="white" />
                     </clipPath>
                  </defs>
               </svg> <span><?= $phone_title; ?></span>
            </a>
            <?php endif; ?>


         </div>


         <div class="footer-top__right">
            <?php if ($address_2): ?>
            <p class="footer-top__address"><?= esc_html($address_2); ?></p>
            <?php endif; ?>
         </div>
      </div>


      <div class="footer-middle">
         <div class="footer-middle__logo">
            <?php if( $logo_footer  ): ?>
            <img src="<?= $logo_footer; ?>" />
            <?php endif; ?>
         </div>


      </div>


      <div class="footer-bottom">
         <?php
                if (has_nav_menu('footer-menu')) {
                    wp_nav_menu([
                        'theme_location' => 'footer-menu',
                        'menu_class' => 'footer-menu d-flex align-items-center p-0 m-0',
                        'container' => 'nav',
                        'container_class' => 'menu-container',
                        'bootstrap' => true,
                        'items_wrap' => '<ul id="%1$s" class="%2$s navbar-nav">%3$s</ul>'
                    ]);
                }
                ?>
      </div>
   </div>
</footer>