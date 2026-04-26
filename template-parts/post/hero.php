<?php 
    $categories = get_the_category();
?>

<div class="post-hero-section"> 
    <div class="container"> 
        <?php if(!empty($categories) && isset($categories)): ?> 
            <div class="post-hero-section__cate d-flex align-items-center justify-content-center"> 
                <?php foreach( $categories as $category ): ?>
                    <div class="item-cate d-flex justify-content-center align-items-center">
                        <a href="/blog/?category=<?= $category->slug ?>" class="d-flex align-items-center">
                            <?= $category->name ?>
                        </a>    
                    </div>
                <?php endforeach;?>    
            </div>
        <?php endif;?> 

        <h1> <?php the_title() ?> </h1>

        <?php if(has_post_thumbnail()): ?>
            <div class="post-hero-section__thumbnail d-flex justify-content-center"> 
                <?php the_post_thumbnail('full'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>