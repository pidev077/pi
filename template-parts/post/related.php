<?php 
    $current_post_id = get_the_ID();

    $query_args = [
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'desc',
        'post__not_in'   => [$current_post_id]
    ];

    $the_query = new WP_Query($query_args);

?>
<?php if ($the_query->have_posts()): ?>
    <div class="flip-post-related-section"> 
        <div class="container d-flex justify-content-between align-items-end">
            <h2>Related articles</h2>

            <div class="flip-post-related-section__navigation d-flex justify-content-end align-items-center">
                <div class="swiper-button-prev d-flex justify-content-center align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                        <path d="M13 5.99995L1 5.99995M1 5.99995L5.5 1.19995M1 5.99995L5.5 10.8" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <div class="swiper-button-next d-flex justify-content-center align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                        <path d="M1 5.99995L13 5.99995M13 5.99995L8.5 1.19995M13 5.99995L8.5 10.8" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="flip-post-related-section__carousel"> 
            <div class="swiper-wrapper">
                <?php 
                    while ($the_query->have_posts()):
                        $the_query->the_post();
                        $categories = get_the_category();
                        flip_item_post();
                    endwhile;
                ?>
            </div>
        </div>
    </div>
<?php endif;?>