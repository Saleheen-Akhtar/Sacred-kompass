<?php
/**
 * Sacred Kompass v5.2 — Homepage template
 *
 * Single scrollable page. Each section is a separate WP Page
 * (About, Offerings, Founders, FAQ, Contact) included here as
 * one seamless scroll. Anchor links like /#about or /about work.
 *
 * ELEMENTOR: the_content() is called unconditionally — always.
 * This is required so Elementor can intercept it on every load,
 * including the very first time you open the page in the editor.
 * Never wrap this in a condition.
 *
 * HOW THE TWO MODES WORK:
 * - First visit / no Elementor: the_content() returns empty string
 *   (the Home page has no post_content). The theme sections below
 *   render the full page.
 * - After you publish with Elementor: _elementor_edit_mode = 'builder'
 *   is set. The theme sections are hidden; Elementor's output comes
 *   through the_content() filter.
 */
get_header();

// ALWAYS call the_content() — Elementor requires this unconditionally.
if (have_posts()) {
    while (have_posts()) {
        the_post();
        the_content();
    }
}

// Show coded theme sections only when Elementor has NOT taken over.
$home_id           = (int) get_option('page_on_front');
$elementor_active  = $home_id
    && get_post_meta($home_id, '_elementor_edit_mode', true) === 'builder';

if (!$elementor_active) {
    get_template_part('template-parts/home/hero');
    get_template_part('template-parts/home/philosophy-strip');
    get_template_part('template-parts/home/about');
    get_template_part('template-parts/home/offerings');
    get_template_part('template-parts/home/quote-band');
    get_template_part('template-parts/home/founders');
    get_template_part('template-parts/home/testimonials');
    get_template_part('template-parts/home/faq');
    get_template_part('template-parts/home/cta');
}

get_footer();
