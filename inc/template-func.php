<?php

/**
 * =====================================================================
 * Mega Menu Walker — renders "about" and "services" mega panels.
 *
 * Usage (WP admin → Appearance → Menus → Screen Options → CSS Classes):
 *   Add class  mega-about    to a top-level item for the About panel.
 *   Add class  mega-services to a top-level item for the Services panel.
 *
 * Structure expected in WP menu:
 *
 *   mega-about  (level 0)
 *     └─ Panel title  (level 1, link or #)
 *          └─ Col label  (level 2, e.g. "Điều Hướng")
 *               └─ Nav link 1, 2, …  (level 3)
 *
 *   mega-services  (level 0)
 *     └─ Panel title  (level 1)
 *          ├─ Col-1 label  (level 2, e.g. "NHÓM DỊCH VỤ")
 *          │    └─ Link 1, 2, …  (level 3)
 *          └─ Col-2 label  (level 2, e.g. "DỊCH VỤ ĐIỀU TRỊ")
 *               └─ Link 1, 2, …  (level 3)
 * =====================================================================
 */
if (!class_exists('Pi_Header_Walker')) {
    class Pi_Header_Walker extends Walker_Nav_Menu
    {
        /** Flat list of all items (stored so we can find children/grandchildren). */
        private $all_items = [];

        public function walk($elements, $max_depth, ...$args)
        {
            $this->all_items = $elements;
            return parent::walk($elements, $max_depth, ...$args);
        }

        /** Return direct children of $parent_id from the full items list. */
        private function get_children($parent_id)
        {
            $out = [];
            foreach ($this->all_items as $item) {
                if ((int) $item->menu_item_parent === (int) $parent_id) {
                    $out[] = $item;
                }
            }
            return $out;
        }

        /**
         * Return user-defined CSS classes on a menu item, stripping WP internals.
         * Pass $base_classes to always include certain classes on the element.
         */
        private function item_classes($item, array $base_classes = [])
        {
            $classes = empty($item->classes) ? [] : (array) $item->classes;
            $custom  = array_filter($classes, function ($c) {
                return !empty(trim($c))
                    && !preg_match(
                        '/^(menu-item|page[-_]item|current[-_]|has-children)/i',
                        $c
                    );
            });
            $merged = array_unique(array_merge($base_classes, array_values($custom)));
            return implode(' ', array_filter($merged));
        }

        /** Recursively remove an item and all its descendants from the Walker queue. */
        private function unset_descendants($id, &$children_elements)
        {
            if (!isset($children_elements[$id])) return;
            foreach ($children_elements[$id] as $child) {
                $this->unset_descendants($child->ID, $children_elements);
            }
            unset($children_elements[$id]);
        }

        /**
         * Intercept mega items before Walker processes their children.
         * For mega items: render the full panel inline, then remove children
         * from the queue so Walker does not double-render them.
         */
        public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
        {
            if ($depth === 0) {
                $classes     = empty($element->classes) ? [] : (array) $element->classes;
                $is_about    = in_array('mega-about', $classes);
                $is_services = in_array('mega-services', $classes);

                if ($is_about || $is_services) {
                    $type = $is_about ? 'about' : 'services';
                    $this->render_mega_li($output, $element, $type);
                    $this->unset_descendants($element->ID, $children_elements);
                    return;
                }
            }
            parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
        }

        /* ── Render the outer <li> wrapper ──────────────────────────── */
        private function render_mega_li(&$output, $item, $type)
        {
            $classes   = empty($item->classes) ? [] : (array) $item->classes;
            $classes   = array_unique(array_filter(array_merge(
                $classes,
                ['nav-item', 'dropdown', 'has-mega-menu', 'mega-type-' . $type]
            )));
            $title     = esc_html(apply_filters('the_title', $item->title, $item->ID));
            $panel_id  = 'mega-panel-' . esc_attr($item->ID);

            $output .= '<li class="' . esc_attr(implode(' ', $classes)) . '">';

            // Toggle button (works for desktop mega + mobile accordion via .dropdown-toggle)
            $output .= '<button class="nav-link mega-toggle dropdown-toggle" type="button"'
                     . ' aria-expanded="false" aria-controls="' . $panel_id . '">';
            $output .= '<span>' . $title . '</span>';
            $output .= $this->chevron_svg();
            $output .= '</button>';

            // ── Mobile fallback: show real navigable links (skips structural layers) ──
            $title_items    = $this->get_children($item->ID);
            $title_item     = $title_items[0] ?? null;
            $fallback_links = [];
            if ($type === 'services' && $title_item) {
                // New structure: level2 = NHÓM DỊCH VỤ wrapper → level3 = groups → level4 = services
                $col_groups = $this->get_children($title_item->ID);
                $nhom_wrapper = $col_groups[0] ?? null;
                $groups = $nhom_wrapper ? $this->get_children($nhom_wrapper->ID) : [];
                foreach ($groups as $group) {
                    $fallback_links = array_merge($fallback_links, $this->get_children($group->ID));
                }
            } elseif ($type === 'about' && $title_item) {
                $col_items      = $this->get_children($title_item->ID);
                $col_item       = $col_items[0] ?? null;
                $fallback_links = $col_item ? $this->get_children($col_item->ID) : [];
            }
            $output .= '<ul class="dropdown-menu mega-mobile-nav">';
            foreach ($fallback_links as $fl) {
                $output .= '<li>';
                $output .= '<a class="dropdown-item" href="' . esc_url($fl->url) . '">';
                $output .= esc_html(apply_filters('the_title', $fl->title, $fl->ID));
                $output .= '</a>';
                $output .= '</li>';
            }
            $output .= '</ul>';

            // ── Desktop mega panel ──
            $output .= '<div id="' . $panel_id . '" class="mega-menu mega-menu--' . esc_attr($type) . '" hidden>';
            $output .= '<div class="mega-menu__inner">';
            $output .= '<div class="container">';

            if ($type === 'services') {
                $output .= $this->render_services_content($item);
            } else {
                $output .= $this->render_about_content($item);
            }

            $output .= '</div>'; // /.container
            $output .= '</div>'; // /.mega-menu__inner
            $output .= $this->render_mega_footer();
            $output .= '</div>'; // /.mega-menu

            $output .= '</li>';
        }

        /* ── About mega content ──────────────────────────────────────── */
        private function render_about_content($item)
        {
            $blog_url = get_permalink(get_option('page_for_posts')) ?: home_url('/blog/');

            // Level 1: title item → panel heading
            $title_items = $this->get_children($item->ID);
            $title_item  = $title_items[0] ?? null;
            $panel_title = $title_item
                ? esc_html(apply_filters('the_title', $title_item->title, $title_item->ID))
                : '';

            // Level 2: col label (first child of title item)
            $col_items = $title_item ? $this->get_children($title_item->ID) : [];
            $col_item  = $col_items[0] ?? null;
            $col_label = $col_item
                ? esc_html(apply_filters('the_title', $col_item->title, $col_item->ID))
                : '';

            // Level 3: actual nav links (children of col label)
            $nav_links = $col_item ? $this->get_children($col_item->ID) : [];

            $html  = $this->mega_header($panel_title);
            $html .= '<div class="mega-menu__body">';

            // Col 1 — nav links
            $html .= '<div class="mega-menu__col mega-menu__col--nav">';
            if ($col_label) {
                $html .= '<p class="mega-menu__col-label">' . $col_label . '</p>';
            }
            $html .= '<ul class="mega-menu__nav-list">';
            foreach ($nav_links as $link) {
                $li_cls = $this->item_classes($link);
                $html .= '<li' . ($li_cls ? ' class="' . esc_attr($li_cls) . '"' : '') . '>';
                $html .= '<a href="' . esc_url($link->url) . '" class="mega-menu__nav-link">';
                $html .= esc_html(apply_filters('the_title', $link->title, $link->ID));
                $html .= '</a>';
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';

            // Col 2 — featured posts
            $html .= '<div class="mega-menu__col mega-menu__col--posts">';
            $html .= $this->posts_col($blog_url);
            $html .= '</div>';

            $html .= '</div>'; // .mega-menu__body
            return $html;
        }

        /* ── Services mega content ───────────────────────────────────── */
        private function render_services_content($item)
        {
            $blog_url = get_permalink(get_option('page_for_posts')) ?: home_url('/blog/');

            // Level 1: title item → panel heading
            $title_items = $this->get_children($item->ID);
            $title_item  = $title_items[0] ?? null;
            $panel_title = $title_item
                ? esc_html(apply_filters('the_title', $title_item->title, $title_item->ID))
                : '';

            // Level 2: "NHÓM DỊCH VỤ" wrapper (first child of title_item)
            $col_groups   = $title_item ? $this->get_children($title_item->ID) : [];
            $nhom_wrapper = $col_groups[0] ?? null;
            $col1_label   = $nhom_wrapper
                ? esc_html(apply_filters('the_title', $nhom_wrapper->title, $nhom_wrapper->ID))
                : esc_html__('NHÓM DỊCH VỤ', 'pi');

            // Level 3: actual groups (children of NHÓM DỊCH VỤ wrapper)
            // Level 4: services under each group (children of each level-3 item)
            $groups = $nhom_wrapper ? $this->get_children($nhom_wrapper->ID) : [];

            $html  = $this->mega_header($panel_title);
            $html .= '<div class="mega-menu__body">';

            // ── Col 1: category/group list ──────────────────────────────
            $html .= '<div class="mega-menu__col mega-menu__col--cats">';
            $html .= '<p class="mega-menu__col-label">' . $col1_label . '</p>';
            $html .= '<ul class="mega-menu__cat-list">';
            foreach ($groups as $i => $group) {
                $group_title = esc_html(apply_filters('the_title', $group->title, $group->ID));
                $group_url   = esc_url($group->url);
                $group_uid   = 'svgroup-' . esc_attr($group->ID);
                $active_cls  = $i === 0 ? ' is-active' : '';

                $html .= '<li class="mega-menu__cat-item' . $active_cls . '"'
                       . ' data-target="' . $group_uid . '">';
                $html .= '<a href="' . $group_url . '">' . $group_title . '</a>';
                $html .= $this->arrow_right_svg();
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';

            // ── Col 2: service groups (shown/hidden by JS on cat hover) ─
            $html .= '<div class="mega-menu__col mega-menu__col--services">';
            $html .= '<p class="mega-menu__col-label">' . esc_html__('DỊCH VỤ ĐIỀU TRỊ', 'pi') . '</p>';
            foreach ($groups as $i => $group) {
                $group_uid  = 'svgroup-' . esc_attr($group->ID);
                $active_cls = $i === 0 ? ' is-active' : '';
                $services   = $this->get_children($group->ID);

                $html .= '<ul id="' . $group_uid . '" class="mega-menu__service-group' . $active_cls . '">';
                foreach ($services as $service) {
                    $html .= '<li>';
                    $html .= '<a class="mega-menu__service-link"'
                           . ' href="' . esc_url($service->url) . '">';
                    $html .= esc_html(apply_filters('the_title', $service->title, $service->ID));
                    $html .= '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</div>';

            // ── Col 3: featured posts ────────────────────────────────────
            $html .= '<div class="mega-menu__col mega-menu__col--posts">';
            $html .= $this->posts_col($blog_url);
            $html .= '</div>';

            $html .= '</div>'; // .mega-menu__body
            return $html;
        }

        /* ── Shared: mega header row (title + close button) ─────────── */
        private function mega_header($title)
        {
            $html  = '<div class="mega-menu__header">';
            if ($title) {
                $html .= '<h2 class="mega-menu__title">' . esc_html($title) . '</h2>';
            }
            $html .= '<button class="mega-menu__close" type="button"'
                   . ' aria-label="' . esc_attr__('Đóng menu', 'pi') . '">'
                   . $this->close_svg()
                   . '</button>';
            $html .= '</div>';
            return $html;
        }

        /* ── Shared: offers column (ACF-driven) ─────────────────────── */
        private function posts_col($blog_url)
        {
            $col_label = get_field('mega_menu_col_label', 'option')
                         ?: __('ƯU ĐÃI ĐỘC QUYỀN THÁNG NÀY', 'pi');
            $see_more  = get_field('mega_menu_see_more', 'option'); // link field → array
            $offers    = get_field('mega_menu_offers', 'option');   // repeater → array[]

            $sm_url    = !empty($see_more['url'])    ? $see_more['url']    : $blog_url;
            $sm_label  = !empty($see_more['title'])  ? $see_more['title']  : __('Xem Thêm', 'pi');
            $sm_target = !empty($see_more['target']) ? $see_more['target'] : '_self';

            $html  = '<div class="mega-menu__col-header">';
            $html .= '<p class="mega-menu__col-label">' . esc_html($col_label) . '</p>';
            $html .= '<a href="' . esc_url($sm_url) . '" class="mega-menu__see-more"'
                   . ' target="' . esc_attr($sm_target) . '">'
                   . esc_html($sm_label) . '</a>';
            $html .= '</div>';

            if (!empty($offers)) {
                foreach ($offers as $offer) {
                    $img     = $offer['offer_image'] ?? null;
                    $ttl     = esc_html($offer['offer_title'] ?? '');
                    $exc     = esc_html($offer['offer_excerpt'] ?? '');
                    $link    = $offer['offer_link'] ?? [];

                    $lk_url    = !empty($link['url'])    ? esc_url($link['url'])    : '#';
                    $lk_target = !empty($link['target']) ? esc_attr($link['target']) : '_self';

                    $html .= '<a href="' . $lk_url . '" class="mega-menu__post"'
                           . ' target="' . $lk_target . '">';

                    if (!empty($img['url'])) {
                        $html .= '<div class="mega-menu__post-img">'
                               . '<img src="' . esc_url($img['url']) . '"'
                               . ' alt="' . esc_attr($img['alt'] ?: $ttl) . '"'
                               . ' loading="lazy">'
                               . '</div>';
                    }

                    $html .= '<div class="mega-menu__post-body">'
                           . '<p class="mega-menu__post-title">' . $ttl . '</p>'
                           . '<p class="mega-menu__post-excerpt">' . $exc . '</p>'
                           . '</div>';
                    $html .= '</a>';
                }
            }

            return $html;
        }

        /* ── Mega footer bar (social + copyright) ───────────────────── */
        private function render_mega_footer()
        {
            $social_fb  = get_field('footer_social_facebook', 'option');
            $social_ig  = get_field('footer_social_instagram', 'option');
            $social_tw  = get_field('footer_social_twitter', 'option');
            $social_yt  = get_field('footer_social_youtube', 'option');
            $copyright  = get_field('footer_copyright', 'option')
                          ?: '&copy; ' . date('Y') . ' ' . __('DD CLINIC. All Rights Reserved.', 'pi');

            $html  = '<div class="mega-menu__footer">';
            $html .= '<div class="container">';
            $html .= '<div class="mega-menu__footer-inner">';

            // Social
            if ($social_fb || $social_ig || $social_tw || $social_yt) {
                $html .= '<div class="mega-menu__footer-social">';
                $html .= '<span class="mega-menu__footer-social-label">'
                       . esc_html__('Theo Dõi DD CLINIC', 'pi') . '</span>';
                $html .= '<div class="mega-menu__footer-icons">';

                if ($social_fb) {
                    $html .= '<a href="' . esc_url($social_fb) . '" target="_blank" rel="noopener noreferrer" aria-label="Facebook">'
                           . '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">'
                           . '<path d="M7 10V14H10V21H14V14H17L18 10H14V8C14 7.73478 14.1054 7.48043 14.2929 7.29289C14.4804 7.10536 14.7348 7 15 7H18V3H15C13.6739 3 12.4021 3.52678 11.4645 4.46447C10.5268 5.40215 10 6.67392 10 8V10H7Z"/>'
                           . '</svg>'
                           . '</a>';
                }
                if ($social_ig) {
                    $html .= '<a href="' . esc_url($social_ig) . '" target="_blank" rel="noopener noreferrer" aria-label="Instagram">'
                           . '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">'
                           . '<path d="M16.5 7.5V7.51M4 8C4 6.93913 4.42143 5.92172 5.17157 5.17157C5.92172 4.42143 6.93913 4 8 4H16C17.0609 4 18.0783 4.42143 18.8284 5.17157C19.5786 5.92172 20 6.93913 20 8V16C20 17.0609 19.5786 18.0783 18.8284 18.8284C18.0783 19.5786 17.0609 20 16 20H8C6.93913 20 5.92172 19.5786 5.17157 18.8284C4.42143 18.0783 4 17.0609 4 16V8ZM9 12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12C15 11.2044 14.6839 10.4413 14.1213 9.87868C13.5587 9.31607 12.7956 9 12 9C11.2044 9 10.4413 9.31607 9.87868 9.87868C9.31607 10.4413 9 11.2044 9 12Z"/>'
                           . '</svg>'
                           . '</a>';
                }
                if ($social_tw) {
                    $html .= '<a href="' . esc_url($social_tw) . '" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">'
                           . '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">'
                           . '<path d="M4 20L10.768 13.232M13.228 10.772L20 4M4 4L15.733 20H20L8.267 4H4Z"/>'
                           . '</svg>'
                           . '</a>';
                }
                if ($social_yt) {
                    $html .= '<a href="' . esc_url($social_yt) . '" target="_blank" rel="noopener noreferrer" aria-label="YouTube">'
                           . '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">'
                           . '<path d="M2 8C2 6.93913 2.42143 5.92172 3.17157 5.17157C3.92172 4.42143 4.93913 4 6 4H18C19.0609 4 18.0783 4.42143 20.8284 5.17157C21.5786 5.92172 22 6.93913 22 8V16C22 17.0609 21.5786 18.0783 20.8284 18.8284C20.0783 19.5786 19.0609 20 18 20H6C4.93913 20 3.92172 19.5786 3.17157 18.8284C2.42143 18.0783 2 17.0609 2 16V8Z"/>'
                           . '<path d="M10 9L15 12L10 15V9Z"/>'
                           . '</svg>'
                           . '</a>';
                }

                $html .= '</div>'; // .mega-menu__footer-icons
                $html .= '</div>'; // .mega-menu__footer-social
            }

            // Copyright
            $html .= '<p class="mega-menu__footer-copyright">' . wp_kses_post($copyright) . '</p>';

            $html .= '</div>'; // .mega-menu__footer-inner
            $html .= '</div>'; // .container
            $html .= '</div>'; // .mega-menu__footer

            return $html;
        }

        /* ── SVG helpers ─────────────────────────────────────────────── */
        private function chevron_svg()
        {
            return '<svg class="nav-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none"'
                 . ' xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
                 . '<path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5"'
                 . ' stroke-linecap="round" stroke-linejoin="round"/>'
                 . '</svg>';
        }

        private function close_svg()
        {
            return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"'
                 . ' xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
                 . '<path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5"'
                 . ' stroke-linecap="round"/>'
                 . '</svg>';
        }

        private function arrow_right_svg()
        {
            return '<svg width="6" height="10" viewBox="0 0 6 10" fill="none"'
                 . ' xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
                 . '<path d="M1 1L5 5L1 9" stroke="currentColor" stroke-width="1.5"'
                 . ' stroke-linecap="round" stroke-linejoin="round"/>'
                 . '</svg>';
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
/**
 * Mobile Menu Walker — 3-level accordion for the mobile drawer.
 *
 * Level 0 → .mob-item    (button toggle or <a> link)
 * Level 1 → .mob-sub     (collapsible child list)
 * Level 2 → .mob-sub-sub (collapsible grandchild list)
 */
if (!class_exists('Pi_Mobile_Walker')) {
    class Pi_Mobile_Walker extends Walker_Nav_Menu
    {
        private $all_items = [];

        public function walk($elements, $max_depth, ...$args)
        {
            $this->all_items = $elements;
            return parent::walk($elements, $max_depth, ...$args);
        }

        private function get_children($parent_id)
        {
            $out = [];
            foreach ($this->all_items as $item) {
                if ((int) $item->menu_item_parent === (int) $parent_id) {
                    $out[] = $item;
                }
            }
            return $out;
        }

        private function unset_descendants($id, &$children_elements)
        {
            if (!isset($children_elements[$id])) return;
            foreach ($children_elements[$id] as $child) {
                $this->unset_descendants($child->ID, $children_elements);
            }
            unset($children_elements[$id]);
        }

        /**
         * Intercept mega items: skip structural layers (title item + col labels)
         * and render only the real navigable links directly inside the accordion.
         */
        public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
        {
            if ($depth === 0) {
                $classes     = empty($element->classes) ? [] : (array) $element->classes;
                $is_about    = in_array('mega-about', $classes);
                $is_services = in_array('mega-services', $classes);

                if ($is_about || $is_services) {
                    $this->render_mega_mobile($output, $element, $is_services ? 'services' : 'about');
                    $this->unset_descendants($element->ID, $children_elements);
                    return;
                }
            }
            parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
        }

        private function render_mega_mobile(&$output, $item, $type)
        {
            $title       = esc_html(apply_filters('the_title', $item->title, $item->ID));
            $title_items = $this->get_children($item->ID);
            $title_item  = $title_items[0] ?? null;

            $output .= '<li class="mob-item mob-item--has-children">';
            $output .= '<button class="mob-link mob-toggle" type="button" aria-expanded="false">';
            $output .= '<span>' . $title . '</span>';
            $output .= $this->chevron();
            $output .= '</button>';
            $output .= '<ul class="mob-sub">';

            if ($type === 'services' && $title_item) {
                // New structure: level2 = NHÓM DỊCH VỤ wrapper → level3 = groups → level4 = services
                $col_groups   = $this->get_children($title_item->ID);
                $nhom_wrapper = $col_groups[0] ?? null;
                $groups       = $nhom_wrapper ? $this->get_children($nhom_wrapper->ID) : [];

                foreach ($groups as $group) {
                    $group_title = esc_html(apply_filters('the_title', $group->title, $group->ID));
                    $services    = $this->get_children($group->ID);

                    // Group name as label
                    $output .= '<li class="mob-sub-item mob-sub-item--label">';
                    $output .= '<span class="mob-col-label">' . $group_title . '</span>';
                    $output .= '</li>';

                    // Services under this group
                    foreach ($services as $service) {
                        $output .= '<li class="mob-sub-item">';
                        $output .= '<a class="mob-sub-link" href="' . esc_url($service->url) . '">';
                        $output .= esc_html(apply_filters('the_title', $service->title, $service->ID));
                        $output .= '</a>';
                        $output .= '</li>';
                    }
                }
            } elseif ($type === 'about' && $title_item) {
                // Skip title item + col label → show nav links (level 3)
                $col_items = $this->get_children($title_item->ID);
                $col_item  = $col_items[0] ?? null;
                $nav_links = $col_item ? $this->get_children($col_item->ID) : [];
                foreach ($nav_links as $link) {
                    $output .= '<li class="mob-sub-item">';
                    $output .= '<a class="mob-sub-link" href="' . esc_url($link->url) . '">';
                    $output .= esc_html(apply_filters('the_title', $link->title, $link->ID));
                    $output .= '</a>';
                    $output .= '</li>';
                }
            }

            $output .= '</ul>';
            $output .= '</li>';
        }

        public function start_lvl(&$output, $depth = 0, $args = null)
        {
            $cls     = $depth === 0 ? 'mob-sub' : 'mob-sub-sub';
            $output .= '<ul class="' . $cls . '">';
        }

        public function end_lvl(&$output, $depth = 0, $args = null)
        {
            $output .= '</ul>';
        }

        public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
        {
            $classes      = empty($item->classes) ? [] : (array) $item->classes;
            $has_children = in_array('menu-item-has-children', $classes);
            $title        = esc_html(apply_filters('the_title', $item->title, $item->ID));
            $url          = esc_url($item->url);
            $target       = !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';

            if ($depth === 0) {
                $li_cls  = 'mob-item' . ($has_children ? ' mob-item--has-children' : '');
                $output .= '<li class="' . esc_attr($li_cls) . '">';

                if ($has_children) {
                    $output .= '<button class="mob-link mob-toggle" type="button" aria-expanded="false">';
                    $output .= '<span>' . $title . '</span>';
                    $output .= $this->chevron();
                    $output .= '</button>';
                } else {
                    $output .= '<a class="mob-link" href="' . $url . '"' . $target . '>' . $title . '</a>';
                }
            } elseif ($depth === 1) {
                $li_cls  = 'mob-sub-item' . ($has_children ? ' mob-sub-item--has-children' : '');
                $output .= '<li class="' . esc_attr($li_cls) . '">';

                if ($has_children) {
                    $output .= '<button class="mob-sub-toggle" type="button" aria-expanded="false">';
                    $output .= '<span>' . $title . '</span>';
                    $output .= $this->chevron();
                    $output .= '</button>';
                } else {
                    $output .= '<a class="mob-sub-link" href="' . $url . '"' . $target . '>' . $title . '</a>';
                }
            } else {
                $output .= '<li>';
                $output .= '<a class="mob-sub-sub-link" href="' . $url . '"' . $target . '>' . $title . '</a>';
            }
        }

        public function end_el(&$output, $item, $depth = 0, $args = null)
        {
            $output .= '</li>';
        }

        private function chevron()
        {
            return '<svg class="mob-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none"'
                 . ' aria-hidden="true"><path d="M1 1L6 6L11 1" stroke="currentColor"'
                 . ' stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
/**
 * Custom Walker for footer nav menus — renders each item with an arrow chevron.
 */
if (!class_exists('Pi_Footer_Walker')) {
    class Pi_Footer_Walker extends Walker_Nav_Menu
    {
        public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0)
        {
            $item    = $data_object;
            $classes = empty($item->classes) ? [] : (array) $item->classes;
            $classes[] = 'footer__nav-item';
            $is_cta    = in_array('menu-cta', $classes);
            $class_str = implode(' ', array_unique(array_filter($classes)));

            $output .= '<li class="' . esc_attr($class_str) . '">';

            $atts            = [];
            $atts['href']    = !empty($item->url) ? $item->url : '#';
            $atts['target']  = !empty($item->target) ? $item->target : '';
            $atts['rel']     = !empty($item->xfn) ? $item->xfn : '';
            $atts['title']   = !empty($item->attr_title) ? $item->attr_title : '';
            $atts['class']   = 'footer__nav-link' . ($is_cta ? ' footer__nav-link--cta' : '');

            $arrow = '<span class="footer__nav-arrow" aria-hidden="true">'
                . '<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">'
                . '<path d="M1 1L6 6L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>'
                . '</svg>'
                . '</span>';

            $attr_str = '';
            foreach ($atts as $attr => $value) {
                if ($value !== '') {
                    $attr_str .= ' ' . esc_attr($attr) . '="' . esc_attr($value) . '"';
                }
            }

            $output .= '<a' . $attr_str . '>';
            $output .= '<span>' . esc_html(apply_filters('the_title', $item->title, $item->ID)) . '</span>';
            $output .= $arrow;
            $output .= '</a>';
        }
    }
}

function pi_post_item()
{
   $categories = get_the_category(get_the_ID());
   $cate_color = __get_field('color_category', 'category_' . $categories[0]->term_id);
   $cate_bg_color = __get_field('bg_category', 'category_' . $categories[0]->term_id);
   $cate_color = !empty($cate_color) ? $cate_color : '#FFF';
   $cate_bg_color = !empty($cate_bg_color) ? $cate_bg_color : '#554943';
   ?>
<div class="pi-post-loop">
   <div class="pi-post-loop--content">
      <?php if (!empty($categories)): ?>
      <div class="pi-post-loop--category">
         <?php foreach ($categories as $key => $value): ?>
         <div class="item-cate" style="color:<?= $cate_color ?>; background-color:<?= $cate_bg_color ?>;">
            <?= $value->name ?>
         </div>
         <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <h3 class="pi-post-loop--title">
         <a href="<?= get_the_permalink(); ?>">
            <?php the_title(); ?>
         </a>
      </h3>
      <div class="pi-post-loop--excerpt">
         <?php
            echo wp_trim_words(get_the_excerpt(), 20, '...');
            ?>
      </div>
   </div>
   <div class="pi-post-loop--thumbnail">
      <a href="<?= get_the_permalink(); ?>">
         <div class="pi-cover-image">
            <?php the_post_thumbnail(''); ?>
         </div>
      </a>
   </div>
</div>
<?php
}



function pi_item_post()
{
   $categories = get_the_category();
   ?>
<div class="item-post swiper-slide">
   <div class="item-post__thumbnail">
      <a href="<?= esc_url(get_permalink()) ?>">
         <?php the_post_thumbnail('full'); ?>
      </a>
   </div>
   <div class="item-post-content">
      <div class="item-post-inner">
         <?php if (!empty($categories) && isset($categories)): ?>
         <div class="item-post__cate">
            <?php foreach ($categories as $category): ?>
            <div class="item-cate">
               <a href="/blog/?category=<?= $category->slug ?>">
                  <?= $category->name ?>
               </a>
            </div>
            <?php endforeach; ?>
         </div>
         <?php endif; ?>

         <h3>
            <a href="<?= esc_url(get_permalink()) ?>">
               <?php the_title(); ?>
            </a>
         </h3>

         <div class="item-post__excerpt"> <?php the_excerpt() ?> </div>
      </div>
   </div>

   <a href="<?= esc_url(get_permalink()) ?>" class="item-post__btn">
      <?php esc_html_e('Read more', 'pi'); ?>
   </a>
</div>
<?php }