<?php
/**
 * Sacred Kompass v5.3 — Default Content Seeder
 * Uses post_meta directly — no ACF required.
 */
defined('ABSPATH') || exit;

function sk_get_post_by_title(string $title, string $post_type): ?WP_Post {
    $q = new WP_Query([
        'post_type'              => $post_type,
        'title'                  => $title,
        'posts_per_page'         => 1,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'post_status'            => ['publish','draft','pending'],
    ]);
    return $q->have_posts() ? $q->posts[0] : null;
}

function sk_insert_default_content(): void {

    /* ── Offerings ── */
    $offerings = [
        ['title'=>'Meditation & Mindfulness',              'tag'=>'Personal',    'desc'=>'Tailored practices for stress reduction, focus, and emotional balance, meeting you wherever you are on your inner journey.',                                        'price'=>'','order'=>1],
        ['title'=>'Compassionate Communication',           'tag'=>'Relational',  'desc'=>'Nonviolent Communication tools to foster empathy, resolve conflicts, and build stronger, more authentic relationships.',                                             'price'=>'','order'=>2],
        ['title'=>'Astrology & Strategic Insight',         'tag'=>'Guidance',    'desc'=>'Vedic Jyotish astrology as a living guidance system for clarity on aligned decision-making, timing, and sacred cycles.',                                             'price'=>'','order'=>3],
        ['title'=>"Women's Wellness & Empowerment",        'tag'=>'Empowerment', 'desc'=>"Programmes supporting women in reclaiming their sacred power, well-being, and intuitive wisdom through the sacred feminine.",                                        'price'=>'','order'=>4],
        ['title'=>'Leadership & Organisational Alignment', 'tag'=>'Corporate',   'desc'=>'Workshops to integrate conscious leadership, emotional resilience, and holistic growth — culture built from the inside out.',                                         'price'=>'','order'=>5],
    ];
    foreach ($offerings as $o) {
        if (sk_get_post_by_title($o['title'], 'sk_offering')) continue;
        $id = wp_insert_post(['post_title'=>$o['title'],'post_type'=>'sk_offering','post_status'=>'publish','menu_order'=>$o['order']]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'offering_tag',   $o['tag']);
            update_post_meta($id, 'offering_desc',  $o['desc']);
            update_post_meta($id, 'offering_price', $o['price']);
        }
    }

    /* ── FAQ ── */
    $faqs = [
        ['question'=>'What is Jyotish astrology?',                     'answer'=>"Jyotish is the ancient Vedic science of light — a system of astrology that predates Western traditions by thousands of years. It maps the soul's journey through planetary cycles, helping us understand our dharma, karmic patterns, and the auspicious timing of major life decisions.",'order'=>1],
        ['question'=>'Do I need prior experience to attend?',           'answer'=>'No experience is needed for any of our offerings. We welcome complete beginners alongside seasoned practitioners. Our guides meet you exactly where you are, with patience, warmth, and deep respect for your unique path.',                                                               'order'=>2],
        ['question'=>'Are sessions available online?',                  'answer'=>'Yes. Most private sessions are available online via video call. In-person sessions are held at our space in Bedok North, Singapore. Please contact us to confirm the format when booking.',                                                                                                   'order'=>3],
        ['question'=>'What is Nonviolent Communication (NVC)?',         'answer'=>"Developed by Marshall Rosenberg, NVC is a language of the heart — a framework for expressing ourselves honestly and listening to others with deep empathy. We use it as both a practical communication tool and a spiritual practice of compassion.",                                        'order'=>4],
        ['question'=>'How do I know which service is right for me?',    'answer'=>"We offer a free 20-minute discovery call to understand where you are and what you're seeking. From there, our team will lovingly suggest which offering, format, and guide feels most aligned with your current chapter of life.",                                                           'order'=>5],
        ['question'=>'Do you offer corporate or organisational programmes?','answer'=>'Yes. We design bespoke workshops and consulting engagements for teams and organisations seeking to integrate conscious leadership, emotional resilience, and compassionate culture. Please reach out directly to discuss your needs.',                                                    'order'=>6],
    ];
    foreach ($faqs as $f) {
        if (sk_get_post_by_title($f['question'], 'sk_faq')) continue;
        $id = wp_insert_post(['post_title'=>$f['question'],'post_type'=>'sk_faq','post_status'=>'publish','menu_order'=>$f['order']]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'faq_answer', $f['answer']);
        }
    }
}
