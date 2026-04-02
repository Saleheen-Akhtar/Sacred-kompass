<?php
/**
 * FAQ — reads from sk_faq CPT. Answer stored as post_meta (no ACF needed).
 */
$faq_query = new WP_Query([
    'post_type'      => 'sk_faq',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);
?>
<section class="faq-section" id="faq" aria-labelledby="faq-heading">
  <div class="wrap"><div class="faq-layout">
    <div class="faq-left reveal">
      <div class="eyebrow"><?php esc_html_e('Questions','sacred-kompass'); ?></div>
      <h2 class="display-h2" id="faq-heading">
        <?php esc_html_e('Frequently','sacred-kompass'); ?><br><em><?php esc_html_e('Asked','sacred-kompass'); ?></em>
      </h2>
      <p class="body-serif"><?php esc_html_e('If you have more questions, we warmly invite you to reach out. Every journey begins with a conversation.','sacred-kompass'); ?></p>
      <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-outline"><?php esc_html_e('Book a Free Call','sacred-kompass'); ?></a>
    </div>
    <div class="faq-list reveal d2" role="list">
      <?php
      $idx = 0;
      if ($faq_query->have_posts()) :
        while ($faq_query->have_posts()) : $faq_query->the_post();
          $q = get_the_title();
          $a = get_post_meta(get_the_ID(), 'faq_answer', true);
          if (!$a) $a = get_the_content(); // fallback
          if (!$q) { $idx++; continue; }
      ?>
      <div class="faq-item" role="listitem">
        <button class="faq-trigger" aria-expanded="false" aria-controls="faq-body-<?php echo $idx; ?>">
          <span class="faq-q"><?php echo esc_html($q); ?></span>
          <span class="faq-toggle" aria-hidden="true"><span></span><span></span></span>
        </button>
        <div class="faq-body" id="faq-body-<?php echo $idx; ?>" role="region">
          <div class="faq-body-inner"><?php echo esc_html($a); ?></div>
        </div>
      </div>
      <?php
          $idx++;
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </div>
  </div></div>
</section>
