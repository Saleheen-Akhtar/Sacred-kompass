<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Mobile full-screen overlay -->
<div class="nav-mobile-overlay" id="sk-mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Mobile navigation','sacred-kompass'); ?>">

  <!-- Close button -->
  <button class="nav-mobile-close" id="sk-mobile-close" aria-label="<?php esc_attr_e('Close menu','sacred-kompass'); ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
  </button>

  <div class="nav-mobile-logo">
    <a href="<?php echo esc_url(home_url('/')); ?>">
      <?php $logo = sk_logo_html('nav-mobile-logo-img'); ?>
      <?php if ($logo) : echo $logo; else : ?>
        Sacred <em>Kompass</em>
      <?php endif; ?>
    </a>
  </div>
  <ul>
    <li><a href="<?php echo esc_url(home_url('/#about'));     ?>"><?php esc_html_e('About',     'sacred-kompass'); ?></a></li>
    <li><a href="<?php echo esc_url(home_url('/#offerings')); ?>"><?php esc_html_e('Offerings', 'sacred-kompass'); ?></a></li>
    <li><a href="<?php echo esc_url(home_url('/#founders'));  ?>"><?php esc_html_e('Founders',  'sacred-kompass'); ?></a></li>
    <li><a href="<?php echo esc_url(home_url('/#faq'));       ?>"><?php esc_html_e('FAQ',        'sacred-kompass'); ?></a></li>
    <li><a href="<?php echo esc_url(home_url('/#contact'));   ?>"><?php esc_html_e('Contact',   'sacred-kompass'); ?></a></li>
  </ul>
  <div class="nav-mobile-divider"></div>
  <div class="nav-mobile-cta">
    <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-primary">
      <?php esc_html_e('Book a Free Discovery Call', 'sacred-kompass'); ?>
    </a>
  </div>
</div>

<nav class="nav" id="sk-nav" role="navigation" aria-label="<?php esc_attr_e('Main navigation','sacred-kompass'); ?>">
  <a class="nav-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Sacred Kompass home','sacred-kompass'); ?>">
    <?php $logo = sk_logo_html('nav-logo-img'); ?>
    <?php if ($logo) : echo $logo; else : ?>
      Sacred <em>Kompass</em>
    <?php endif; ?>
  </a>

  <ul class="nav-links">
    <li><a href="<?php echo esc_url(home_url('/#about'));     ?>"><?php esc_html_e('About',    'sacred-kompass'); ?></a></li>
    <li><a href="<?php echo esc_url(home_url('/#offerings')); ?>"><?php esc_html_e('Offerings','sacred-kompass'); ?></a></li>
    <li><a href="<?php echo esc_url(home_url('/#founders'));  ?>"><?php esc_html_e('Founders', 'sacred-kompass'); ?></a></li>
    <li><a href="<?php echo esc_url(home_url('/#faq'));       ?>"><?php esc_html_e('FAQ',       'sacred-kompass'); ?></a></li>
  </ul>

  <div class="nav-cta">
    <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-primary">
      <?php esc_html_e('Book a Call', 'sacred-kompass'); ?>
    </a>
  </div>

  <button class="nav-hamburger" id="sk-hamburger"
          aria-label="<?php esc_attr_e('Toggle menu','sacred-kompass'); ?>"
          aria-expanded="false"
          aria-controls="sk-mobile-menu">
    <span></span>
    <span></span>
    <span></span>
  </button>
</nav>
