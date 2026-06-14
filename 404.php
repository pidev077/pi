<?php
/**
 * The template for displaying 404 pages (not found)
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 * @package pi
 */

get_header();

do_action('wpml_register_single_string', 'pi', '404_heading',  'Trang Bạn Đang Tìm Kiếm Không Còn Khả Dụng');
do_action('wpml_register_single_string', 'pi', '404_body',     'Liên kết này có thể đã được thay đổi, di chuyển hoặc không còn tồn tại. Bạn có thể quay lại trang chủ hoặc tiếp tục khám phá các dịch vụ thẩm mỹ y khoa được DD CLINIC tư vấn tại Hàn Quốc.');
do_action('wpml_register_single_string', 'pi', '404_btn',      'Quay Về Trang Chủ');

$heading_404 = apply_filters('wpml_translate_single_string', 'Trang Bạn Đang Tìm Kiếm Không Còn Khả Dụng', 'pi', '404_heading');
$body_404    = apply_filters('wpml_translate_single_string', 'Liên kết này có thể đã được thay đổi, di chuyển hoặc không còn tồn tại. Bạn có thể quay lại trang chủ hoặc tiếp tục khám phá các dịch vụ thẩm mỹ y khoa được DD CLINIC tư vấn tại Hàn Quốc.', 'pi', '404_body');
$btn_404     = apply_filters('wpml_translate_single_string', 'Quay Về Trang Chủ', 'pi', '404_btn');
?>
<main id="primary" class="site-main">
    <div class="entry-content">
        <div class="container">
            <h2 class="wp-block-heading-fadein-chars">404</h2>
            <h4 class="wp-block-heading-animation"><?php echo esc_html($heading_404); ?></h4>
            <p><?php echo esc_html($body_404); ?></p>
            <a class="pi-btn lg" href="<?php echo esc_url(home_url('/')); ?>" target=""> <?php echo esc_html($btn_404); ?> </a>
        </div>
    </div>
</main><!-- #main -->
<?php
    $map = get_page_by_path( 'map', OBJECT, 'wp_block' );
    if ( $map ) {
        echo do_blocks( $map->post_content );
    }
    ?>
<?php
get_footer();