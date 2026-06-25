<?php

/**
 * Hooks.
 */

function imageTagForJs($response, $attachment)
{
	foreach ($response['sizes'] as $size => $datas) {
		$response['sizes'][$size]['tag']    = wp_get_attachment_image($attachment->ID, $size);
		$response['sizes'][$size]['srcset'] = wp_get_attachment_image_srcset($attachment->ID, $size);
	}
	return $response;
}
add_filter('wp_prepare_attachment_for_js', 'imageTagForJs', 10, 2);


/**
 * Allow upload json file
 */
add_filter('upload_mimes', function ($mime_types) {
	$mime_types['json'] = 'application/json'; // Adding .json extension
	$mime_types['svg']  = 'image/svg+xml';
	$mime_types['svgz'] = 'image/svg+xml'; 
	$mime_types['ttf']  = 'application/x-font-ttf';
    $mime_types['otf']  = 'application/x-font-opentype';
    $mime_types['woff'] = 'application/font-woff';
    $mime_types['woff2'] = 'application/font-woff2';
	return $mime_types;
}, 1);

/**
 * Header template
 * @return void
 */
add_action('pi_hook_header', 'pi_header_template');
function pi_header_template()
{
	load_template(get_template_directory() . '/template-parts/header.php', false);
}

/**
 * Footer template
 * @return void
 */
add_action('pi_hook_footer', 'pi_footer_template');
function pi_footer_template()
{
	load_template(get_template_directory() . '/template-parts/footer.php', false);
}

/**
 * Mobile menu drawer — rendered once before </body> via wp_footer.
 */
add_action('wp_footer', 'pi_mobile_menu_template', 5);
function pi_mobile_menu_template()
{
	load_template(get_template_directory() . '/template-parts/mobile-menu.php', false);
}


/**
 * Post loop item template
 *
 * @param Int $post_id
 *
 * @return void
 */
add_action('pi_hook_post_loop_item', 'pi_post_loop_item_template', 20, 2);
function pi_post_loop_item_template($post_id, $index)
{
	set_query_var('post_id', $post_id);
	$v  = ($index) % 3;
	$vT = ceil($v);

	$anm = 'data-aos="fade-up" data-aos-duration="' . (($v !== 0 ? $vT : 3) * 400) . '"';
?>
	<article <?= $anm; ?> <?php post_class('col-md-4') ?>>
		<?php pi_post_item() ?>
	</article>
<?php
}

// ── CF7: tắt tự động chèn <br> / <p> trong form body ─────────────────────────
add_filter( 'wpcf7_autop_or_not', '__return_false' );

// ── CF7: custom thông báo lỗi "required" theo từng field name ────────────────
// CF7 6.x validate "required" qua SWV schema (wpcf7_swv_create_schema), không
// còn qua filter wpcf7_validate_*. Rule nào được add vào schema TRƯỚC sẽ thắng
// (WPCF7_Validation::invalidate() bỏ qua field đã invalid), nên hook ở priority
// thấp hơn module gốc (10) để rule custom của mình luôn chạy trước.
add_action( 'wpcf7_swv_create_schema', function ( $schema, $contact_form ) {
	$messages = array(
		'ho-ten'             => 'Vui lòng nhập họ và tên của bạn.',
		'so-dien-thoai'      => 'Vui lòng nhập số điện thoại liên hệ.',
		'your-email'         => 'Vui lòng nhập địa chỉ email của bạn.',
		'quoc-gia'           => 'Vui lòng nhập quốc gia / thành phố hiện tại.',
		'dich-vu'            => 'Vui lòng chọn dịch vụ bạn quan tâm.',
		'thoi-gian-tu-van'   => 'Vui lòng chọn thời gian tư vấn mong muốn.',
		'thoi-gian-dieu-tri' => 'Vui lòng chọn thời gian điều trị dự kiến.',
		'kenh-lien-he'       => 'Vui lòng chọn kênh liên hệ ưu tiên.',
		'quan-tam'           => 'Vui lòng chia sẻ điều bạn đang quan tâm nhất.',
		'chap-thuan'         => 'Vui lòng đồng ý với chính sách bảo mật để tiếp tục.',
	);

	$tags = $contact_form->scan_form_tags( array(
		'basetype' => array( 'text', 'email', 'url', 'tel', 'select', 'textarea', 'checkbox', 'radio', 'acceptance' ),
	) );

	foreach ( $tags as $tag ) {
		if ( ! isset( $messages[ $tag->name ] ) ) {
			continue;
		}

		$is_required = $tag->is_required()
			|| ( 'acceptance' === $tag->basetype && ! $tag->has_option( 'optional' ) );

		if ( ! $is_required ) {
			continue;
		}

		$schema->add_rule(
			wpcf7_swv_create_rule( 'required', array(
				'field' => $tag->name,
				'error' => $messages[ $tag->name ],
			) )
		);
	}
}, 5, 2 );

// Field acceptance vẫn validate qua filter riêng (không dùng SWV schema).
add_filter( 'wpcf7_validate_acceptance', function ( $result, $tag ) {
	$messages = array(
		'chap-thuan' => 'Vui lòng đồng ý với chính sách bảo mật để tiếp tục.',
	);

	if ( ! isset( $messages[ $tag->name ] ) || ! $result->is_valid( $tag->name ) ) {
		return $result;
	}

	if ( $tag->has_option( 'optional' ) ) {
		return $result;
	}

	if ( empty( $_POST[ $tag->name ] ) ) {
		$result->invalidate( $tag, $messages[ $tag->name ] );
	}

	return $result;
}, 5, 2 );

// ── Archive sort: register 'sort' query var and apply ordering ────────────────
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'sort';
	return $vars;
} );

add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( is_tag() || is_category() || is_author() ) ) {
		if ( get_query_var( 'sort' ) === 'oldest' ) {
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'ASC' );
		}
	}
} );

//remove comments
add_action('admin_init', function () {
	// Redirect any user trying to access comments page
	global $pagenow;

	if ($pagenow === 'edit-comments.php' || $pagenow === 'options-discussion.php') {
		wp_redirect(admin_url());
		exit;
	}

	// Remove comments metabox from dashboard
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

	// Disable support for comments and trackbacks in post types
	foreach (get_post_types() as $post_type) {
		if (post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page and option page in menu 
add_action('admin_menu', function () {
	remove_menu_page('edit-comments.php');
	remove_submenu_page('options-general.php', 'options-discussion.php');
});

// Remove comments links from admin bar
add_action('init', function () {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
});
