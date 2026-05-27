<?php

/**
 * Template tags
 */

if ( ! function_exists( 'pi_page_breadcrumb' ) ) {
	/**
	 * Render breadcrumb nav below the page hero.
	 * Supports: pages (with ancestors), service CPT, single posts.
	 */
	function pi_page_breadcrumb() {
		if ( is_front_page() ) {
			return;
		}

		$crumbs   = [];
		$crumbs[] = [ 'label' => __( 'Trang Chủ', 'pi' ), 'url' => home_url( '/' ) ];

		if ( is_page() ) {
			$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
			foreach ( $ancestors as $pid ) {
				$crumbs[] = [ 'label' => get_the_title( $pid ), 'url' => get_permalink( $pid ) ];
			}
			$crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];

		} elseif ( is_singular( 'service_group' ) ) {
			// Trang Chủ → Dịch Vụ → [Tên nhóm]
			$dich_vu_page = get_page_by_path( 'dich-vu' );
			$dich_vu_url  = $dich_vu_page ? get_permalink( $dich_vu_page ) : home_url( '/dich-vu/' );
			$crumbs[] = [ 'label' => 'Dịch Vụ', 'url' => $dich_vu_url ];
			$crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];

		} elseif ( is_singular( 'service' ) ) {
			// Trang Chủ → Dịch Vụ → [Danh mục cấp 1] → [Tên dịch vụ]
			$dich_vu_page = get_page_by_path( 'dich-vu' );
			$dich_vu_url  = $dich_vu_page ? get_permalink( $dich_vu_page ) : home_url( '/dich-vu/' );
			$crumbs[] = [ 'label' => 'Dịch Vụ', 'url' => $dich_vu_url ];

			// Lấy taxonomy term cấp cao nhất của dịch vụ
			$terms = get_the_terms( get_the_ID(), 'service_category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				// Lấy term có depth cao nhất (term cha nhất trong danh sách)
				$top_term = null;
				foreach ( $terms as $term ) {
					if ( ! $top_term || $term->parent === 0 ) {
						$top_term = $term;
					}
				}
				if ( $top_term ) {
					// Tìm service_group post được link với term này
					$linked_group = get_posts( [
						'post_type'      => 'service_group',
						'posts_per_page' => 1,
						'meta_query'     => [ [
							'key'   => 'sg_linked_category',
							'value' => $top_term->term_id,
						] ],
					] );
					$group_url = $linked_group ? get_permalink( $linked_group[0]->ID ) : get_term_link( $top_term );
					$crumbs[]  = [ 'label' => $top_term->name, 'url' => $group_url ];
				}
			}
			$crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];

		} elseif ( is_singular( 'post' ) ) {
			$cats = get_the_category();
			if ( $cats ) {
				$crumbs[] = [ 'label' => $cats[0]->name, 'url' => get_category_link( $cats[0]->term_id ) ];
			}
			$crumbs[] = [ 'label' => get_the_title(), 'url' => '' ];
		}

		if ( count( $crumbs ) < 2 ) {
			return;
		}

		echo '<nav class="page-hero-breadcrumb" aria-label="breadcrumb">';
		echo '<div class="container">';
		echo '<ol class="breadcrumb">';
		$last = count( $crumbs ) - 1;
		foreach ( $crumbs as $i => $crumb ) {
			if ( $i === $last ) {
				echo '<li class="breadcrumb-item active" aria-current="page"><span>' . esc_html( $crumb['label'] ) . '</span></li>';
			} else {
				echo '<li class="breadcrumb-item"><a href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['label'] ) . '</a></li>';
			}
		}
		echo '</ol>';
		echo '</div>';
		echo '</nav>';
	}
}

if ( ! function_exists( 'pi_template_news_hero_header' ) ) {
	function pi_template_news_hero_header() {
		$page_for_posts_id = get_option( 'page_for_posts' );
		$blog_link         = get_permalink( $page_for_posts_id );
		ob_start(); ?>
        <div class="news-hero text-center text-white">
            <div class="container">
                <div class="hero-inner">
                    <h2 class="pi-title">Blog</h2>
                    <p class="pi-subtitle"><?= get_bloginfo( 'description' ); ?></p>
                </div>
            </div>
        </div>

        <div class="news-header">
            <div class="row">
                <div class="col-md-6">

                </div>
                <div class="col-md-6 text-md-right">
                    <div class="dropdown-alt">
                        <select onchange="window.location = this.value ? this.value  : '<?= esc_url( $blog_link ); ?>'">
                            <option value="">all</option>
	                        <?php wp_get_archives( [
		                        'type'   => 'yearly',
		                        'format' => 'option',
	                        ] ); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}
}