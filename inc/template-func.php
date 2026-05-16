<?php

/**
 * Custom Walker for footer nav menus — renders each item with an arrow chevron.
 */
if (!class_exists('Pi_Footer_Walker')) {
    class Pi_Footer_Walker extends Walker_Nav_Menu
    {
        public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0)
        {
            $item    = $data_object;
            $classes = empty($item->classes) ? [] : (array) $item->classes;
            $classes[] = 'footer__nav-item';
            $is_cta    = in_array('menu-cta', $classes);
            $class_str = implode(' ', array_unique(array_filter($classes)));

            $output .= '<li class="' . esc_attr($class_str) . '">';

            $atts            = [];
            $atts['href']    = !empty($item->url) ? $item->url : '#';
            $atts['target']  = !empty($item->target) ? $item->target : '';
            $atts['rel']     = !empty($item->xfn) ? $item->xfn : '';
            $atts['title']   = !empty($item->attr_title) ? $item->attr_title : '';
            $atts['class']   = 'footer__nav-link' . ($is_cta ? ' footer__nav-link--cta' : '');

            $arrow = '<span class="footer__nav-arrow" aria-hidden="true">'
                . '<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">'
                . '<path d="M1 1L6 6L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'
                . '</svg>'
                . '</span>';

            $attr_str = '';
            foreach ($atts as $attr => $value) {
                if ($value !== '') {
                    $attr_str .= ' ' . esc_attr($attr) . '="' . esc_attr($value) . '"';
                }
            }

            $output .= '<a' . $attr_str . '>';
            $output .= '<span>' . esc_html(apply_filters('the_title', $item->title, $item->ID)) . '</span>';
            $output .= $arrow;
            $output .= '</a>';
        }
    }
}

function pi_post_item()
{
   $categories = get_the_category(get_the_ID());
   $cate_color = __get_field('color_category', 'category_' . $categories[0]->term_id);
   $cate_bg_color = __get_field('bg_category', 'category_' . $categories[0]->term_id);
   $cate_color = !empty($cate_color) ? $cate_color : '#FFF';
   $cate_bg_color = !empty($cate_bg_color) ? $cate_bg_color : '#554943';
   ?>
<div class="pi-post-loop">
   <div class="pi-post-loop--content">
      <?php if (!empty($categories)): ?>
      <div class="pi-post-loop--category">
         <?php foreach ($categories as $key => $value): ?>
         <div class="item-cate" style="color:<?= $cate_color ?>; background-color:<?= $cate_bg_color ?>;">
            <?= $value->name ?>
         </div>
         <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <h3 class="pi-post-loop--title">
         <a href="<?= get_the_permalink(); ?>">
            <?php the_title(); ?>
         </a>
      </h3>
      <div class="pi-post-loop--excerpt">
         <?php
            echo wp_trim_words(get_the_excerpt(), 20, '...');
            ?>
      </div>
   </div>
   <div class="pi-post-loop--thumbnail">
      <a href="<?= get_the_permalink(); ?>">
         <div class="pi-cover-image">
            <?php the_post_thumbnail(''); ?>
         </div>
      </a>
   </div>
</div>
<?php
}



function pi_item_post()
{
   $categories = get_the_category();
   ?>
<div class="item-post swiper-slide">
   <div class="item-post__thumbnail">
      <a href="<?= esc_url(get_permalink()) ?>">
         <?php the_post_thumbnail('full'); ?>
      </a>
   </div>
   <div class="item-post-content">
      <div class="item-post-inner">
         <?php if (!empty($categories) && isset($categories)): ?>
         <div class="item-post__cate">
            <?php foreach ($categories as $category): ?>
            <div class="item-cate">
               <a href="/blog/?category=<?= $category->slug ?>">
                  <?= $category->name ?>
               </a>
            </div>
            <?php endforeach; ?>
         </div>
         <?php endif; ?>

         <h3>
            <a href="<?= esc_url(get_permalink()) ?>">
               <?php the_title(); ?>
            </a>
         </h3>

         <div class="item-post__excerpt"> <?php the_excerpt() ?> </div>
      </div>
   </div>

   <a href="<?= esc_url(get_permalink()) ?>" class="item-post__btn">
      Read more
   </a>
</div>
<?php }