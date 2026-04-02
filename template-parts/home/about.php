<?php
/**
 * About Section — reads from ACF options page (sk-settings).
 * Tradition tags stored as newline-separated text in sk_about_traditions.
 */
$eyebrow    = sk_option('about_eyebrow',    'Our Philosophy');
$heading    = sk_option('about_heading',    'Where the Sacred Meets the Everyday');
$quote      = sk_option('about_quote',      '"True transformation is not found. It is remembered. We walk with you back to what was always whole."');
$quote_attr = sk_option('about_quote_attr', 'Sacred Kompass, Our Philosophy');
$default_body = '<p>Sacred Kompass is a transformative wellness and consciousness-based consultancy. We weave together ancient wisdom, Vedic philosophy, Jyotish astrology, meditation, and self-awareness, with modern frameworks like Nonviolent Communication and emotional resilience practices.</p><p>We are not about temporary fixes. We are about in-depth transformation, supporting individuals, leaders, and organisations in cultivating inner clarity, compassionate engagement, and sustainable growth from the inside out.</p>';
$body = sk_option('about_body', '') ?: $default_body;

// Traditions: newline-separated string in ACF options
$traditions_raw = sk_option('about_traditions', '');
if ($traditions_raw) {
    $traditions = array_filter(array_map('trim', explode("\n", $traditions_raw)));
} else {
    $traditions = ['Vedic Philosophy','Jyotish Astrology','Meditation','NVC','Sacred Feminine','Breathwork','Energy Healing','Emotional Resilience'];
}
?>
<section class="about-section" id="about" aria-labelledby="about-heading">
  <div class="wrap">
    <div class="reveal"><div class="eyebrow"><?php echo esc_html($eyebrow); ?></div></div>
    <div class="about-layout">
      <div>
        <h2 class="display-h2 reveal d1" id="about-heading"><?php echo esc_html($heading); ?></h2>
        <div class="about-text reveal d2"><?php echo wp_kses_post($body); ?></div>
        <?php if ($traditions) : ?>
        <div class="tradition-tags reveal d3">
          <?php foreach ($traditions as $t) : ?>
            <span class="trad-tag"><?php echo esc_html($t); ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <div>
        <div class="about-right-large reveal" aria-hidden="true">Wisdom</div>
        <?php if ($quote) : ?>
        <div class="about-pull reveal d2">
          <blockquote><?php echo esc_html($quote); ?></blockquote>
          <?php if ($quote_attr) : ?><span class="attr"><?php echo esc_html($quote_attr); ?></span><?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
