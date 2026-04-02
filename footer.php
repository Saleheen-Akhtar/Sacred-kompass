<?php
$tagline   = sk_option('footer_tagline',   'Ancient wisdom for the modern soul. Transformative guidance for individuals, leaders, and organisations.');
$copyright = sk_option('footer_copyright', 'Sacred Kompass Collective &middot; Singapore');
$email     = sk_option('footer_email',     'collective@sacredkompass.org');
$phone     = sk_option('footer_phone',     '+65 84343915');
$phone_c   = preg_replace('/[^+0-9]/', '', $phone);
?>
<footer class="footer" role="contentinfo">
  <div class="wrap">
    <div class="footer-grid">

      <div>
        <a class="footer-brand" href="<?php echo esc_url(home_url('/')); ?>">
          <?php $logo = sk_logo_html('footer-logo-img'); ?>
          <?php if ($logo) : echo $logo; else : ?>
            <span class="footer-brand-text">Sacred <em>Kompass</em></span>
          <?php endif; ?>
        </a>
        <div class="footer-divider"></div>
        <p class="footer-tagline"><?php echo esc_html($tagline); ?></p>
      </div>

      <div class="footer-col">
        <h5><?php esc_html_e('Navigate','sacred-kompass'); ?></h5>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/#about'));     ?>"><?php esc_html_e('About',    'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/#offerings')); ?>"><?php esc_html_e('Offerings','sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/#founders'));  ?>"><?php esc_html_e('Founders', 'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/#faq'));       ?>"><?php esc_html_e('FAQ',       'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/#contact'));   ?>"><?php esc_html_e('Contact',   'sacred-kompass'); ?></a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5><?php esc_html_e('Offerings','sacred-kompass'); ?></h5>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/#offerings')); ?>"><?php esc_html_e('Meditation',           'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/#offerings')); ?>"><?php esc_html_e('Communication',        'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/#offerings')); ?>"><?php esc_html_e('Jyotish Astrology',    'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/#offerings')); ?>"><?php esc_html_e("Women's Wellness",     'sacred-kompass'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/#offerings')); ?>"><?php esc_html_e('Corporate Programmes', 'sacred-kompass'); ?></a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5><?php esc_html_e('Connect','sacred-kompass'); ?></h5>
        <ul>
          <li><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></li>
          <li><a href="tel:<?php echo esc_attr($phone_c); ?>"><?php echo esc_html($phone); ?></a></li>
          <?php if ($ig = sk_option('social_instagram')) : ?><li><a href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener">Instagram</a></li><?php endif; ?>
          <?php if ($fb = sk_option('social_facebook'))  : ?><li><a href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener">Facebook</a></li><?php endif; ?>
          <?php if ($wa = sk_option('social_whatsapp'))  : ?><li><a href="<?php echo esc_url($wa); ?>" target="_blank" rel="noopener">WhatsApp</a></li><?php endif; ?>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      <span class="footer-copy">
        &copy; <?php echo date('Y'); ?> <?php echo wp_kses($copyright, ['em'=>[],'strong'=>[],'br'=>[],'span'=>[]]); ?>
      </span>
      <nav class="footer-legal" aria-label="<?php esc_attr_e('Legal','sacred-kompass'); ?>">
        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy Policy','sacred-kompass'); ?></a>
        <a href="<?php echo esc_url(home_url('/terms'));           ?>"><?php esc_html_e('Terms of Use',  'sacred-kompass'); ?></a>
        <a href="<?php echo esc_url(home_url('/disclaimer'));      ?>"><?php esc_html_e('Disclaimer',    'sacred-kompass'); ?></a>
      </nav>
    </div>

  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
