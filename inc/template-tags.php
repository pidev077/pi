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

		} elseif ( is_singular( 'service' ) ) {
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