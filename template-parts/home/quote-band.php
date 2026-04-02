<?php
/**
 * Quote Band — reads from ACF options page (sk-settings).
 */
$eyebrow   = sk_option('quote_eyebrow',   'Our Vision');
$quote     = sk_option('quote_text',      "We envision a world where well-being and performance coexist harmoniously. By reconnecting people to their inner compass, we help them navigate life's complexities with purpose and alignment.");
$highlight = sk_option('quote_highlight', 'inner compass');
$attr      = sk_option('quote_attr',      'Sacred Kompass Collective, Vision Statement');

$rendered = esc_html($quote);
if ($highlight && str_contains($quote, $highlight)) {
    $rendered = str_replace(
        esc_html($highlight),
        '<span class="qa">' . esc_html($highlight) . '</span>',
        esc_html($quote)
    );
}
?>
<div class="quote-band" aria-label="<?php echo esc_attr($eyebrow); ?>">
  <span class="quote-band-large-q" aria-hidden="true">&ldquo;</span>
  <div class="wrap-narrow" style="position:relative;z-index:1;">
    <div class="eyebrow eyebrow-c eyebrow-light reveal"><?php echo esc_html($eyebrow); ?></div>
    <blockquote class="reveal d1"><?php echo $rendered; ?></blockquote>
    <?php if ($attr) : ?><p class="quote-by reveal d2"><?php echo esc_html($attr); ?></p><?php endif; ?>
  </div>
</div>
