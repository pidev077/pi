<?php
/**
 * The template for displaying 404 pages (not found)
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 * @package flip
 */

get_header();
?>
<main id="primary" class="site-main">
    <div class="entry-content">
        <div class="container">
            <h1 class="wp-block-heading-fadein-chars">404</h1>
            <h2 class="wp-block-heading-animation">Oops! Page not found.</h2>
            <p>Sorry, the page you are looking for does not exist.</p>
            <a class="go-home-btn" href="/" target=""> Go back to Home </a>
        </div>
    </div>
</main><!-- #main -->
<?php
get_footer();