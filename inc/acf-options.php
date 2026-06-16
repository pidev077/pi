<?php

add_action('acf/init', 'pi_acf_init');
function pi_acf_init()
{
	if (function_exists('acf_add_options_page')) {
		if (current_user_can('administrator')):
			acf_add_options_page(array(
				'page_title' => __('Theme Options', 'pi'),
				'menu_title' => __('Theme Options', 'pi'),
				'menu_slug' => 'theme-options',
			));

			// Add child page under the main options page
			acf_add_options_sub_page(array(
				'page_title' => 'General Settings',
				'menu_title' => 'General',
				'parent_slug' => 'theme-options',
			));

			// Add child page under the main options page
			acf_add_options_sub_page(array(
				'page_title' => 'Header Settings',
				'menu_title' => 'Header',
				'parent_slug' => 'theme-options',
			));

			// Add another child page
			acf_add_options_sub_page(array(
				'page_title' => 'Footer Settings',
				'menu_title' => 'Footer',
				'parent_slug' => 'theme-options',
			));

			// Mega Menu settings page
			acf_add_options_sub_page(array(
				'page_title' => 'Mega Menu',
				'menu_title' => 'Mega Menu',
				'menu_slug'  => 'theme-options-mega-menu',
				'parent_slug'=> 'theme-options',
			));
		endif;
	}

	if (function_exists('acf_add_local_field_group')) {

		// ── Mega Menu field group (code-defined, no JSON needed) ─────────
		acf_add_local_field_group([
			'key'    => 'group_pi_mega_menu',
			'title'  => 'Mega Menu — Ưu Đãi & Cấu Hình',
			'fields' => [

				// ── Label cột ưu đãi ───────────────────────────────
				[
					'key'           => 'field_pi_mega_col_label',
					'label'         => 'Nhãn cột ưu đãi',
					'name'          => 'mega_menu_col_label',
					'type'          => 'text',
					'default_value' => 'ƯU ĐÃI ĐỘC QUYỀN THÁNG NÀY',
					'instructions'  => 'Tiêu đề nhỏ phía trên các thẻ ưu đãi.',
				],

				// ── Link "Xem Thêm" ────────────────────────────────
				[
					'key'          => 'field_pi_mega_see_more',
					'label'        => 'Link "Xem Thêm"',
					'name'         => 'mega_menu_see_more',
					'type'         => 'link',
					'return_format'=> 'array',
					'instructions' => 'URL khi click "Xem Thêm" ở cột ưu đãi.',
				],

				// ── Danh sách ưu đãi (repeater, tối đa 2) ──────────
				[
					'key'        => 'field_pi_mega_offers',
					'label'      => 'Danh sách ưu đãi',
					'name'       => 'mega_menu_offers',
					'type'       => 'repeater',
					'max'        => 2,
					'min'        => 0,
					'layout'     => 'block',
					'button_label' => 'Thêm ưu đãi',
					'instructions' => 'Tối đa 2 ưu đãi. Hiện ở cột phải của mega menu.',
					'sub_fields' => [
						[
							'key'           => 'field_pi_offer_image',
							'label'         => 'Hình ảnh',
							'name'          => 'offer_image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'instructions'  => 'Khuyến nghị: 420×280 px trở lên.',
						],
						[
							'key'          => 'field_pi_offer_title',
							'label'        => 'Tiêu đề',
							'name'         => 'offer_title',
							'type'         => 'text',
						],
						[
							'key'          => 'field_pi_offer_excerpt',
							'label'        => 'Mô tả ngắn',
							'name'         => 'offer_excerpt',
							'type'         => 'textarea',
							'rows'         => 2,
							'new_lines'    => 'br',
						],
						[
							'key'           => 'field_pi_offer_link',
							'label'         => 'Đường dẫn',
							'name'          => 'offer_link',
							'type'          => 'link',
							'return_format' => 'array',
						],
					],
				],

			],
			'location' => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'theme-options-mega-menu',
					],
				],
			],
			'menu_order'  => 0,
			'style'       => 'default',
		]);

		// ── Page Hero field group (per-page title bar) ────────────────────
		acf_add_local_field_group([
			'key'    => 'group_pi_page_hero',
			'title'  => 'Page Hero — Title Bar',
			'fields' => [
				[
					'key'           => 'field_pi_hero_enable',
					'label'         => 'Show Page Hero',
					'name'          => 'page_hero_enable',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => 'On',
					'ui_off_text'   => 'Off',
					'instructions'  => 'Enable to display the title bar above the page content.',
				],
				[
					'key'               => 'field_pi_hero_style',
					'label'             => 'Layout Style',
					'name'              => 'page_hero_style',
					'type'              => 'select',
					'choices'           => [
						'style-1' => 'Style 1 — Centered (full-width background)',
						'style-2' => 'Style 2 — Split (text trái / ảnh phải)',
					],
					'default_value'     => 'style-1',
					'instructions'      => 'Style 1: text căn giữa trên nền ảnh. Style 2: text bên trái, ảnh bên phải.',
					'conditional_logic' => [[
						['field' => 'field_pi_hero_enable', 'operator' => '==', 'value' => '1'],
					]],
				],
				[
					'key'               => 'field_pi_hero_supertitle',
					'label'             => 'Supertitle',
					'name'              => 'page_hero_supertitle',
					'type'              => 'text',
					'instructions'      => 'Small text above the main title. E.g. DD CLINIC — PREMIUM KOREAN AESTHETIC ADVISORY CENTER',
					'conditional_logic' => [[
						['field' => 'field_pi_hero_enable', 'operator' => '==', 'value' => '1'],
					]],
				],
				[
					'key'               => 'field_pi_hero_title',
					'label'             => 'Title',
					'name'              => 'page_hero_title',
					'type'              => 'text',
					'instructions'      => 'Main heading. Leave blank to use the page name.',
					'conditional_logic' => [[
						['field' => 'field_pi_hero_enable', 'operator' => '==', 'value' => '1'],
					]],
				],
				[
					'key'               => 'field_pi_hero_description',
					'label'             => 'Description',
					'name'              => 'page_hero_description',
					'type'              => 'textarea',
					'rows'              => 3,
					'new_lines'         => 'br',
					'instructions'      => 'Short paragraph displayed below the title.',
					'conditional_logic' => [[
						['field' => 'field_pi_hero_enable', 'operator' => '==', 'value' => '1'],
					]],
				],
				[
					'key'               => 'field_pi_hero_cta',
					'label'             => 'CTA Button',
					'name'              => 'page_hero_cta',
					'type'              => 'link',
					'return_format'     => 'array',
					'instructions'      => 'Optional call-to-action button displayed below the description. E.g. "Đặt Lịch Tư Vấn" → booking page URL.',
					'conditional_logic' => [[
						['field' => 'field_pi_hero_enable', 'operator' => '==', 'value' => '1'],
					]],
				],
				[
					'key'               => 'field_pi_hero_bg_image',
					'label'             => 'Background Image',
					'name'              => 'page_hero_bg_image',
					'type'              => 'image',
					'return_format'     => 'url',
					'preview_size'      => 'medium',
					'instructions'      => 'Hero background image. Leave blank to use the default gradient.',
					'conditional_logic' => [[
						['field' => 'field_pi_hero_enable', 'operator' => '==', 'value' => '1'],
					]],
				],
			],
			'location' => [
				[[ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ]],
				[[ 'param' => 'post_type', 'operator' => '==', 'value' => 'teams' ]],
			],
			'menu_order' => 0,
			'position'   => 'normal',
			'style'      => 'default',
		]);

		// ── Service Hero: banner split-layout cho service & service_group ─
		acf_add_local_field_group([
			'key'    => 'group_pi_service_hero',
			'title'  => 'Service Hero — Banner',
			'fields' => [
				[
					'key'          => 'field_pi_sh_title',
					'label'        => 'Title',
					'name'         => 'service_hero_title',
					'type'         => 'text',
					'instructions' => 'Leave blank to use the post name.',
				],
				[
					'key'       => 'field_pi_sh_desc',
					'label'     => 'Description',
					'name'      => 'service_hero_desc',
					'type'      => 'textarea',
					'rows'      => 3,
					'new_lines' => 'br',
				],
				[
					'key'           => 'field_pi_sh_cta',
					'label'         => 'CTA Button',
					'name'          => 'service_hero_cta',
					'type'          => 'link',
					'return_format' => 'array',
					'instructions'  => 'Optional call-to-action button. E.g. "Đặt Lịch Tư Vấn" → booking page URL.',
				],
				[
					'key'           => 'field_pi_sh_image',
					'label'         => 'Banner Image (right side)',
					'name'          => 'service_hero_image',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'instructions'  => 'Portrait image, ~3:4 or ~2:3 ratio. Fills the right half of the banner.',
				],
			],
			'location' => [
				[[ 'param' => 'post_type', 'operator' => '==', 'value' => 'service' ]],
				[[ 'param' => 'post_type', 'operator' => '==', 'value' => 'service_group' ]],
			],
			'menu_order' => 1,
			'position'   => 'normal',
			'style'      => 'default',
		]);

		// ── Blog Category: thứ tự hiển thị ─────────────────────────────
		acf_add_local_field_group([
			'key'    => 'group_pi_blog_category',
			'title'  => 'Cài Đặt Danh Mục Blog',
			'fields' => [
				[
					'key'           => 'field_pi_cat_sort_order',
					'label'         => 'Thứ tự hiển thị',
					'name'          => 'cat_sort_order',
					'type'          => 'number',
					'default_value' => 10,
					'min'           => 0,
					'step'          => 1,
					'instructions'  => 'Số nhỏ hơn hiển thị trước. Mặc định: 10.',
				],
			],
			'location' => [
				[[ 'param' => 'taxonomy', 'operator' => '==', 'value' => 'category' ]],
			],
			'menu_order' => 0,
			'style'      => 'default',
		]);

		// ── Service Group: chọn danh mục liên kết ───────────────────────
		acf_add_local_field_group([
			'key'    => 'group_pi_service_group',
			'title'  => 'Cài Đặt Nhóm Dịch Vụ',
			'fields' => [
				[
					'key'           => 'field_pi_sg_linked_category',
					'label'         => 'Danh Mục Dịch Vụ Liên Kết',
					'name'          => 'sg_linked_category',
					'type'          => 'taxonomy',
					'taxonomy'      => 'service_category',
					'field_type'    => 'select',
					'return_format' => 'id',
					'allow_null'    => 1,
					'instructions'  => 'Chọn danh mục (cấp 1 hoặc cấp 2) để tự động liệt kê các dịch vụ thuộc nhóm này bên dưới nội dung Gutenberg.',
				],
			],
			'location' => [
				[[ 'param' => 'post_type', 'operator' => '==', 'value' => 'service_group' ]],
			],
			'menu_order' => 0,
			'position'   => 'side',
			'style'      => 'default',
		]);

	}
}

add_filter('acf/settings/save_json', 'pi_acf_json_save_point');
function pi_acf_json_save_point($path)
{
	// update path
	$path = get_stylesheet_directory() . '/inc/acf-options';

	// return
	return $path;
}

add_filter('acf/settings/load_json', 'pi_acf_json_load_point');
function pi_acf_json_load_point($paths)
{
	// remove original path (optional)
	unset($paths[0]);
	// append path
	$paths[] = get_stylesheet_directory() . '/inc/acf-options';

	// return
	return $paths;
}

function my_acf_google_map_api($api) {
    $api_key = get_field('google_map_api_key', 'option');
    if ($api_key) {
        $api['key'] = $api_key;
    }
    return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
