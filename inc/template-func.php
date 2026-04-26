<?php
function flip_post_item()
{
   $categories = get_the_category(get_the_ID());
   $cate_color = __get_field('color_category', 'category_' . $categories[0]->term_id);
   $cate_bg_color = __get_field('bg_category', 'category_' . $categories[0]->term_id);
   $cate_color = !empty($cate_color) ? $cate_color : '#FFF';
   $cate_bg_color = !empty($cate_bg_color) ? $cate_bg_color : '#554943';
   ?>
<div class="flip-post-loop">
   <div class="flip-post-loop--content">
      <?php if (!empty($categories)): ?>
      <div class="flip-post-loop--category">
         <?php foreach ($categories as $key => $value): ?>
         <div class="item-cate" style="color:<?= $cate_color ?>; background-color:<?= $cate_bg_color ?>;">
            <?= $value->name ?>
         </div>
         <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <h3 class="flip-post-loop--title">
         <a href="<?= get_the_permalink(); ?>">
            <?php the_title(); ?>
         </a>
      </h3>
      <div class="flip-post-loop--excerpt">
         <?php
            echo wp_trim_words(get_the_excerpt(), 20, '...');
            ?>
      </div>
   </div>
   <div class="flip-post-loop--thumbnail">
      <a href="<?= get_the_permalink(); ?>">
         <div class="flip-cover-image">
            <?php the_post_thumbnail(''); ?>
         </div>
      </a>
   </div>
</div>
<?php
}



function flip_item_post()
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