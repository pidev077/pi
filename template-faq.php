<?php
/**
 * Template Name: Trang FAQ
 * @package pi
 */

get_header();

// Recursively collect all blocks including those inside containers
$_flatten_blocks = function( array $blocks ) use ( &$_flatten_blocks ): array {
	$flat = [];
	foreach ( $blocks as $b ) {
		$flat[] = $b;
		if ( ! empty( $b['innerBlocks'] ) ) {
			$flat = array_merge( $flat, $_flatten_blocks( $b['innerBlocks'] ) );
		}
	}
	return $flat;
};

// Build sidebar navigation from faq-group block attributes.
// WP omits default attr values from the block comment, so we fall back to
// the same defaults defined in block-faq/index.js.
$faq_groups = [];
foreach ( $_flatten_blocks( parse_blocks( get_the_content() ) ) as $block ) {
	if ( $block['blockName'] !== 'pi-blocks/block-faq' ) {
		continue;
	}
	$attrs  = $block['attrs'];
	$letter = $attrs['groupLetter'] ?? 'A';
	$title  = $attrs['groupTitle']  ?? 'Tiêu đề nhóm câu hỏi';
	$faq_groups[] = [
		'letter' => $letter,
		'title'  => $title,
		'id'     => 'faq-group-' . strtolower( sanitize_title( $letter ) ),
	];
}

$content = apply_filters( 'the_content', get_the_content() );
?>

<main id="primary" class="site-main">

	<?php
	get_template_part( 'template-parts/page-hero', null, [
		'modifier' => 'page-hero--legal',
	] );
	?>

	<!-- ── Body: Sidebar + FAQ Content ──────────────────────────────────── -->
	<div class="legal-body-wrap">
		<div class="container legal-body-inner">

			<!-- Sidebar (TOC from faq-group labels) -->
			<?php if ( ! empty( $faq_groups ) ) : ?>
			<aside class="legal-sidebar" aria-label="Chủ đề câu hỏi">
				<div class="legal-sidebar__sticky">
					<div class="post-toc js-post-toc">
						<h4 class="post-toc__title">
							<span class="post-toc__title-text"><?= esc_html__( 'Chủ Đề Câu Hỏi', 'pi' ) ?></span>
							<button type="button" class="post-toc__toggle js-toc-toggle"
									aria-label="<?= esc_attr__( 'Chủ đề câu hỏi', 'pi' ) ?>">
								<svg class="post-toc__icon--plus" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M12 5V19M5 12H19" stroke="#978B7B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
								<svg class="post-toc__icon--minus" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M5 12H19" stroke="#978B7B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</button>
						</h4>
						<ol class="post-toc__list">
							<?php foreach ( $faq_groups as $group ) : ?>
							<li class="post-toc__item">
								<a href="#<?= esc_attr( $group['id'] ) ?>"
								   class="post-toc__link js-faq-toc-link">
									<span class="faq-toc__letter"><?= esc_html( $group['letter'] ) ?>.</span>
									<span class="faq-toc__label"><?= esc_html( $group['title'] ) ?></span>
								</a>
							</li>
							<?php endforeach; ?>
						</ol>
					</div>
				</div>
			</aside>
			<?php endif; ?>

			<!-- FAQ blocks output -->
			<div class="legal-content faq-content">
				<?= $content ?>
			</div>

		</div>
	</div>

	<?php
	$footer_cta = get_page_by_path( 'footer-blog', OBJECT, 'wp_block' );
	if ( $footer_cta ) {
		echo do_blocks( $footer_cta->post_content );
	}
	?>

</main>

<script>
(function () {
	// ── FAQ group toggle ──────────────────────────────────────────────────
	document.querySelectorAll('.js-faq-group-toggle').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var group  = this.closest('.block-faq');
			var body   = group.querySelector('.faq-group__body');
			var isOpen = group.classList.toggle('is-open');
			this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			if (isOpen) {
				body.removeAttribute('hidden');
			} else {
				body.setAttribute('hidden', '');
			}
		});
	});

	// ── Sidebar: active state + smooth scroll ─────────────────────────────
	var tocLinks = document.querySelectorAll('.js-faq-toc-link');
	if (!tocLinks.length) return;

	var setActive = function (id) {
		tocLinks.forEach(function (a) {
			a.classList.toggle('is-active', a.getAttribute('href') === '#' + id);
		});
	};

	var groups = [];
	tocLinks.forEach(function (a) {
		var id = a.getAttribute('href').slice(1);
		var el = document.getElementById(id);
		if (el) groups.push({ el: el, id: id });
	});

	if (groups.length) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) setActive(entry.target.id);
			});
		}, { rootMargin: '0px 0px -60% 0px', threshold: 0 });

		groups.forEach(function (g) { io.observe(g.el); });
		setActive(groups[0].id);
	}

	tocLinks.forEach(function (a) {
		a.addEventListener('click', function (e) {
			e.preventDefault();
			var id     = this.getAttribute('href').slice(1);
			var target = document.getElementById(id);
			if (!target) return;
			setActive(id);
			var offset = 100;
			if (window.lenis) {
				window.lenis.scrollTo(target, { offset: -offset });
			} else {
				var top = target.getBoundingClientRect().top + window.scrollY - offset;
				window.scrollTo({ top: top, behavior: 'smooth' });
			}
		});
	});
})();
</script>

<?php get_footer(); ?>
