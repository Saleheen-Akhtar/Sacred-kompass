<?php
/**
 * CTA / Contact — reads from ACF options page (sk-settings).
 */
$email       = sk_option('footer_email',        'collective@sacredkompass.org');
$phone       = sk_option('footer_phone',        '+65 84343915');
$phone_clean = preg_replace('/[^+0-9]/', '', $phone);
$whatsapp    = sk_option('social_whatsapp',     '');
$eyebrow     = sk_option('cta_eyebrow',         'Begin Your Journey');
$heading_raw = sk_option('cta_heading',         '');
$sub         = sk_option('cta_sub',             'A unique fusion of sacred traditions and practical application. Not temporary fixes, but in-depth transformation that helps you thrive from the inside out.');
$form_id     = sk_option('forminator_form_id',  '');
?>
<section class="cta-section" id="contact" aria-labelledby="cta-heading-el">
  <div class="wrap"><div class="cta-layout">
    <div class="cta-text-col reveal">
      <div class="eyebrow eyebrow-light"><?php echo esc_html($eyebrow); ?></div>
      <h2 class="cta-h2" id="cta-heading-el">
        <?php if ($heading_raw) : echo wp_kses_post($heading_raw);
        else : ?>
          <?php esc_html_e('Ready to Reconnect With','sacred-kompass'); ?><br>
          <?php esc_html_e('Your','sacred-kompass'); ?> <em><?php esc_html_e('Inner Compass?','sacred-kompass'); ?></em>
        <?php endif; ?>
      </h2>
      <p class="cta-sub"><?php echo esc_html($sub); ?></p>
      <div class="cta-contact-info">
        <div class="cta-contact-row"><span class="cta-contact-dot"></span><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></div>
        <div class="cta-contact-row"><span class="cta-contact-dot"></span><a href="tel:<?php echo esc_attr($phone_clean); ?>"><?php echo esc_html($phone); ?></a></div>
        <?php if ($whatsapp) : ?>
        <div class="cta-contact-row"><span class="cta-contact-dot"></span><a href="<?php echo esc_url($whatsapp); ?>" target="_blank" rel="noopener"><?php esc_html_e('WhatsApp Us','sacred-kompass'); ?></a></div>
        <?php endif; ?>
        <div class="cta-contact-row"><span class="cta-contact-dot"></span><?php esc_html_e('Bedok North, Singapore · Online Worldwide','sacred-kompass'); ?></div>
      </div>
    </div>
    <div class="cta-form-col reveal d2">
      <?php if ($form_id && shortcode_exists('forminator_form')) :
        echo do_shortcode('[forminator_form id="' . absint($form_id) . '"]');
      else : ?>
        <div class="sk-contact-fallback-form" data-ajax-action="sk_contact_submit">
          <span class="sk-form-title"><?php esc_html_e('Get in Touch','sacred-kompass'); ?></span>

          <div class="sk-form-row-double">
            <div>
              <label for="sk-fname"><?php esc_html_e('First Name','sacred-kompass'); ?></label>
              <input type="text" id="sk-fname" name="fname" placeholder="<?php esc_attr_e('Your first name','sacred-kompass'); ?>" />
            </div>
            <div>
              <label for="sk-lname"><?php esc_html_e('Last Name','sacred-kompass'); ?></label>
              <input type="text" id="sk-lname" name="lname" placeholder="<?php esc_attr_e('Your last name','sacred-kompass'); ?>" />
            </div>
          </div>

          <div class="sk-form-row">
            <label for="sk-email"><?php esc_html_e('Email Address','sacred-kompass'); ?></label>
            <input type="email" id="sk-email" name="email" placeholder="<?php esc_attr_e('your@email.com','sacred-kompass'); ?>" />
          </div>

          <div class="sk-form-row">
            <label for="sk-service"><?php esc_html_e('Area of Interest','sacred-kompass'); ?></label>
            <div class="sk-form-select-wrap">
              <select id="sk-service" name="service">
                <option value="" disabled selected><?php esc_html_e('Choose a service...','sacred-kompass'); ?></option>
                <option value="meditation"><?php esc_html_e('Meditation &amp; Mindfulness','sacred-kompass'); ?></option>
                <option value="nvc"><?php esc_html_e('Compassionate Communication (NVC)','sacred-kompass'); ?></option>
                <option value="astrology"><?php esc_html_e('Astrology &amp; Strategic Insight','sacred-kompass'); ?></option>
                <option value="womens"><?php esc_html_e("Women's Wellness &amp; Empowerment",'sacred-kompass'); ?></option>
                <option value="corporate"><?php esc_html_e('Leadership &amp; Corporate','sacred-kompass'); ?></option>
                <option value="other"><?php esc_html_e('Other / Discovery Call','sacred-kompass'); ?></option>
              </select>
            </div>
          </div>

          <div class="sk-form-row">
            <label for="sk-message"><?php esc_html_e('Your Message','sacred-kompass'); ?></label>
            <textarea id="sk-message" name="message" placeholder="<?php esc_attr_e('Share what brings you here — how can we support your journey?','sacred-kompass'); ?>"></textarea>
          </div>

          <!-- Honeypot: hidden from humans, bots fill it -->
          <input type="text" name="website" id="sk-hp" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0" tabindex="-1" autocomplete="off" aria-hidden="true" />

          <div class="sk-form-submit">
            <p class="sk-form-note"><?php esc_html_e('We respond within 24 hours.','sacred-kompass'); ?></p>
            <button type="submit" class="btn-primary"><?php esc_html_e('Send Message','sacred-kompass'); ?></button>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div></div>
</section>
