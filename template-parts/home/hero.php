<?php
/**
 * Hero — v7.1: no features strip, no parallax bounce, fixed viewport height,
 * buttons fully visible on load.
 */
$stage1  = sk_option('hero_stage1',   'The world pulls at you from every direction.');
$stage2a = sk_option('hero_stage2a',  'Something inside you');
$stage2b = sk_option('hero_stage2b',  'is calling for stillness.');
$stage3a = sk_option('hero_stage3a',  'We walk you');
$stage3b = sk_option('hero_stage3b',  'back to yourself.');
$sub     = sk_option('hero_sub',      'Sacred Kompass is a transformative wellness consultancy weaving Vedic philosophy, Jyotish astrology, and compassionate practice into in-depth, inside-out transformation.');
$cta1_t  = sk_option('hero_cta1_text','Book a Free Discovery Call');
$cta1_u  = sk_option('hero_cta1_url', '/#contact');
$cta2_t  = sk_option('hero_cta2_text','Explore Offerings');
$cta2_u  = sk_option('hero_cta2_url', '#offerings');
$bg_img  = sk_option('hero_bg_image',   '');
$rt_img  = sk_option('hero_right_image','');

if ($cta1_u && !str_starts_with($cta1_u,'http') && !str_starts_with($cta1_u,'/') && !str_starts_with($cta1_u,'#')) $cta1_u = '/' . ltrim($cta1_u,'/');
if ($cta2_u && !str_starts_with($cta2_u,'http') && !str_starts_with($cta2_u,'#') && !str_starts_with($cta2_u,'/')) $cta2_u = '#' . ltrim($cta2_u,'#');
?>
<section class="hero hero--split" aria-label="<?php esc_attr_e('Welcome to Sacred Kompass','sacred-kompass'); ?>">

  <div class="hero-bg-layer" aria-hidden="true">
    <?php if ($bg_img): ?>
      <div class="hero-bg-image">
        <img src="<?php echo esc_url($bg_img); ?>" alt="" role="presentation" loading="eager" fetchpriority="high" />
      </div>
    <?php else: ?>
      <div class="hero-bg-gradient"></div>
    <?php endif; ?>
    <div class="hero-bg-overlay"></div>
  </div>

  <div class="hero-left">
    <div class="hero-journey">
      <p class="stage-chaos journey-stage"><?php echo esc_html($stage1); ?></p>
      <p class="stage-turning journey-stage"><?php echo esc_html($stage2a); ?><br><?php echo esc_html($stage2b); ?></p>
      <p class="stage-calm journey-stage"><?php echo esc_html($stage3a); ?><br><em><?php echo esc_html($stage3b); ?></em></p>
    </div>
    <p class="hero-sub"><?php echo esc_html($sub); ?></p>
    <div class="hero-actions">
      <a href="<?php echo esc_url($cta1_u ?: '/#contact'); ?>" class="btn btn-primary"><?php echo esc_html($cta1_t); ?></a>
      <a href="<?php echo esc_attr($cta2_u ?: '#offerings'); ?>" class="btn btn-outline btn-outline--light"><?php echo esc_html($cta2_t); ?></a>
    </div>
    <div class="hero-location">
      <?php esc_html_e('Bedok North, Singapore','sacred-kompass'); ?>
      &nbsp;·&nbsp;
      <?php esc_html_e('Online Worldwide','sacred-kompass'); ?>
    </div>
  </div>

  <div class="hero-right" aria-hidden="true">
    <?php if ($rt_img): ?>
      <div class="hero-right-img">
        <img src="<?php echo esc_url($rt_img); ?>" alt="" role="presentation" loading="eager" />
      </div>
    <?php else: ?>
      <div class="hero-image-placeholder">
        <div class="hero-halo"><span class="hero-halo-symbol">&#9644;</span></div>
      </div>
    <?php endif; ?>
  </div>

</section>
