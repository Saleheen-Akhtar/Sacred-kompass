<?php
/**
 * Values Section — reads from JSON-encoded wp_option (no ACF required).
 */
$values = sk_repeater('options_sk_values_json');
if (empty($values)) {
    $values = sk_default_values();
}
?>
<section class="values-section" id="values" aria-labelledby="values-heading">
  <div class="wrap">
    <div class="values-header">
      <div class="eyebrow eyebrow-c reveal"><?php esc_html_e('What We Stand For','sacred-kompass'); ?></div>
      <h2 class="display-h2 reveal d1" id="values-heading">
        <?php esc_html_e('Our Core','sacred-kompass'); ?> <em><?php esc_html_e('Values','sacred-kompass'); ?></em>
      </h2>
    </div>
    <div class="values-grid">
      <?php foreach ($values as $idx => $v) : ?>
      <div class="value-card reveal d<?php echo min($idx + 1, 5); ?>">
        <span class="value-num" aria-hidden="true">0<?php echo $idx + 1; ?></span>
        <h3><?php echo esc_html($v['value_title'] ?? ''); ?></h3>
        <p><?php echo esc_html($v['value_desc']  ?? ''); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
