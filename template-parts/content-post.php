<?php

/**
 * Template part for displaying posts
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package pi
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="single-resources-directory">
        <div class="header-hero-section">
            <div class="row">
                <div class="col-md-8">
                    <div class="ss-image" data-aos="fade-up" data-aos-duration="800">
                        <?php
                        $featured_img_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                        echo '<img src="' . $featured_img_url . '" alt="img">';
                        ?>

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="wrapper-resources-hero-text">
                        <?php the_title( '<h1 class="title-resources">', '</h1>' ); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-content-resources">
            <div class="col-md-8">
                <?php the_content(); ?>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</div>