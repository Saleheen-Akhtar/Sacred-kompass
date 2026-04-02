<?php
/**
 * Founders v7.1 — 3 cards: large left (team/group), top-right Kalai, bottom-right Christophe.
 * Click any card to open a full info modal popup.
 */

$team_posts = get_posts([
  'post_type'      => 'sk_team',
  'post_status'    => 'publish',
  'posts_per_page' => -1,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
]);

$founders = [];
if (!empty($team_posts)) {
  foreach ($team_posts as $tp) {
    $founders[] = [
      'founder_image'   => get_post_thumbnail_url($tp->ID,'large') ?: (get_post_meta($tp->ID,'team_image',true) ?: ''),
      'founder_name'    => get_post_meta($tp->ID,'team_first_name',true) ?: get_the_title($tp),
      'founder_surname' => get_post_meta($tp->ID,'team_last_name',true)  ?: '',
      'founder_origin'  => get_post_meta($tp->ID,'team_origin',true)     ?: '',
      'founder_role'    => get_post_meta($tp->ID,'team_role',true)        ?: '',
      'founder_bio'     => get_post_meta($tp->ID,'team_bio',true)         ?: '',
      'founder_tags'    => get_post_meta($tp->ID,'team_tags',true)        ?: '',
    ];
  }
} else {
  $founders = sk_repeater('options_sk_founders_json');
  if (empty($founders)) $founders = sk_default_founders();
}

$section_eyebrow    = sk_option('founders_eyebrow',    'The Founders');
$section_heading    = sk_option('founders_heading',    'The Guides Behind');
$section_heading_em = sk_option('founders_heading_em', 'Sacred Kompass');
$section_sub        = sk_option('founders_sub',        'Two souls, one vision. Uniting Eastern wisdom and Western heart in service of conscious living.');

// Card 1 = group/team photo (left big), Card 2 = Kalai (top right), Card 3 = Christophe (bottom right)
$team_card  = [ 'label' => __('Our Team','sacred-kompass'), 'image' => sk_option('founders_team_image','') ];
$kalai      = $founders[0] ?? ['founder_name'=>'Kalai','founder_surname'=>'Somoo','founder_role'=>'Founder and Lead Guide','founder_bio'=>'','founder_tags'=>'','founder_image'=>'','founder_origin'=>'Singapore'];
$christophe = $founders[1] ?? ['founder_name'=>'Christophe','founder_surname'=>'Grigri','founder_role'=>'International Coordination & Communication','founder_bio'=>'','founder_tags'=>'','founder_image'=>'','founder_origin'=>'France'];

function sk_render_founder_modal(array $f, string $modal_id): void {
  $name    = esc_html(($f['founder_name'] ?? '') . ' ' . ($f['founder_surname'] ?? ''));
  $role    = esc_html($f['founder_role']    ?? '');
  $origin  = esc_html($f['founder_origin']  ?? '');
  $bio     = esc_html($f['founder_bio']     ?? '');
  $img     = esc_url($f['founder_image']    ?? '');
  $tags    = array_filter(array_map('trim', explode("\n", $f['founder_tags'] ?? '')));
  ?>
  <div class="sk-founder-modal" id="<?php echo esc_attr($modal_id); ?>" role="dialog" aria-modal="true" aria-label="<?php echo $name; ?>" hidden>
    <div class="sk-founder-modal-backdrop"></div>
    <div class="sk-founder-modal-box">
      <button class="sk-founder-modal-close" aria-label="<?php esc_attr_e('Close','sacred-kompass'); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
      <div class="sk-founder-modal-inner">
        <?php if ($img): ?>
          <div class="sk-founder-modal-img">
            <img src="<?php echo $img; ?>" alt="<?php echo $name; ?>" />
          </div>
        <?php endif; ?>
        <div class="sk-founder-modal-content">
          <h3 class="sk-founder-modal-name"><?php echo $name; ?></h3>
          <?php if ($role):   ?><span class="sk-founder-modal-role"><?php echo $role; ?></span><?php endif; ?>
          <?php if ($origin): ?><span class="sk-founder-modal-origin">&#9670; <?php echo $origin; ?></span><?php endif; ?>
          <?php if ($bio):    ?><p class="sk-founder-modal-bio"><?php echo $bio; ?></p><?php endif; ?>
          <?php if ($tags): ?>
            <div class="sk-founder-modal-tags">
              <?php foreach ($tags as $tag): ?><span class="trad-tag"><?php echo esc_html($tag); ?></span><?php endforeach; ?>
            </div>
          <?php endif; ?>
          <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-primary sk-founder-modal-cta" style="margin-top:1.8rem">
            <?php esc_html_e('Book a Session','sacred-kompass'); ?>
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<section class="founders-section" id="founders" aria-labelledby="founders-heading">
  <div class="wrap">
    <div class="founders-header">
      <div class="founders-header-left">
        <div class="eyebrow reveal"><?php echo esc_html($section_eyebrow); ?></div>
        <h2 class="display-h2 reveal d1" id="founders-heading">
          <?php echo esc_html($section_heading); ?><br><em><?php echo esc_html($section_heading_em); ?></em>
        </h2>
      </div>
      <p class="founders-header-right reveal d2"><?php echo esc_html($section_sub); ?></p>
    </div>

    <!-- 3-card asymmetric grid -->
    <div class="founders-asymgrid">

      <!-- LEFT: large team/group card -->
      <button class="founder-card founder-card--primary reveal sk-founder-trigger" data-modal="sk-modal-team"
              aria-haspopup="dialog" aria-label="<?php esc_attr_e('View team info','sacred-kompass'); ?>">
        <?php if (!empty($team_card['image'])): ?>
          <div class="founder-card-image">
            <img src="<?php echo esc_url($team_card['image']); ?>" alt="<?php esc_attr_e('Sacred Kompass Team','sacred-kompass'); ?>" loading="lazy" />
          </div>
        <?php else: ?>
          <div class="founder-card-placeholder" aria-hidden="true">
            <div class="founder-placeholder-halo">
              <span class="founder-placeholder-initial">SK</span>
            </div>
          </div>
        <?php endif; ?>
        <div class="founder-card-name-strip" aria-hidden="true">
          <div class="founder-strip-name"><?php esc_html_e('Our','sacred-kompass'); ?> <em><?php esc_html_e('Team','sacred-kompass'); ?></em></div>
          <span class="founder-strip-role"><?php esc_html_e('Sacred Kompass Collective','sacred-kompass'); ?></span>
        </div>
        <div class="founder-card-hover-hint" aria-hidden="true">
          <span><?php esc_html_e('View Info','sacred-kompass'); ?> &#8599;</span>
        </div>
      </button>

      <!-- RIGHT column: Kalai (top) + Christophe (bottom) -->
      <div class="founders-secondary-col">

        <!-- Kalai -->
        <?php $k = $kalai; $k_initial = strtoupper(substr($k['founder_name'],0,1)); ?>
        <button class="founder-card founder-card--secondary reveal d2 sk-founder-trigger" data-modal="sk-modal-kalai"
                aria-haspopup="dialog" aria-label="<?php echo esc_attr(($k['founder_name'].' '.$k['founder_surname']).' — view profile'); ?>">
          <?php if (!empty($k['founder_image'])): ?>
            <div class="founder-card-image">
              <img src="<?php echo esc_url($k['founder_image']); ?>" alt="<?php echo esc_attr($k['founder_name'].' '.$k['founder_surname']); ?>" loading="lazy" />
            </div>
          <?php else: ?>
            <div class="founder-card-placeholder" aria-hidden="true">
              <div class="founder-placeholder-halo"><span class="founder-placeholder-initial"><?php echo esc_html($k_initial); ?></span></div>
            </div>
          <?php endif; ?>
          <div class="founder-card-name-strip" aria-hidden="true">
            <div class="founder-strip-name"><?php echo esc_html($k['founder_name']); ?> <em><?php echo esc_html($k['founder_surname']); ?></em></div>
            <?php if ($k['founder_role']): ?><span class="founder-strip-role"><?php echo esc_html($k['founder_role']); ?></span><?php endif; ?>
          </div>
          <div class="founder-card-hover-hint" aria-hidden="true">
            <span><?php esc_html_e('View Profile','sacred-kompass'); ?> &#8599;</span>
          </div>
        </button>

        <!-- Christophe -->
        <?php $c = $christophe; $c_initial = strtoupper(substr($c['founder_name'],0,1)); ?>
        <button class="founder-card founder-card--secondary reveal d3 sk-founder-trigger" data-modal="sk-modal-christophe"
                aria-haspopup="dialog" aria-label="<?php echo esc_attr(($c['founder_name'].' '.$c['founder_surname']).' — view profile'); ?>">
          <?php if (!empty($c['founder_image'])): ?>
            <div class="founder-card-image">
              <img src="<?php echo esc_url($c['founder_image']); ?>" alt="<?php echo esc_attr($c['founder_name'].' '.$c['founder_surname']); ?>" loading="lazy" />
            </div>
          <?php else: ?>
            <div class="founder-card-placeholder" aria-hidden="true">
              <div class="founder-placeholder-halo"><span class="founder-placeholder-initial"><?php echo esc_html($c_initial); ?></span></div>
            </div>
          <?php endif; ?>
          <div class="founder-card-name-strip" aria-hidden="true">
            <div class="founder-strip-name"><?php echo esc_html($c['founder_name']); ?> <em><?php echo esc_html($c['founder_surname']); ?></em></div>
            <?php if ($c['founder_role']): ?><span class="founder-strip-role"><?php echo esc_html($c['founder_role']); ?></span><?php endif; ?>
          </div>
          <div class="founder-card-hover-hint" aria-hidden="true">
            <span><?php esc_html_e('View Profile','sacred-kompass'); ?> &#8599;</span>
          </div>
        </button>

      </div>
    </div>
  </div>
</section>

<!-- ── Modals ──────────────────────────────────────────────── -->
<div class="sk-founder-modal" id="sk-modal-team" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Sacred Kompass Team','sacred-kompass'); ?>" hidden>
  <div class="sk-founder-modal-backdrop"></div>
  <div class="sk-founder-modal-box">
    <button class="sk-founder-modal-close" aria-label="<?php esc_attr_e('Close','sacred-kompass'); ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <div class="sk-founder-modal-inner">
      <?php if (!empty($team_card['image'])): ?>
        <div class="sk-founder-modal-img">
          <img src="<?php echo esc_url($team_card['image']); ?>" alt="<?php esc_attr_e('Sacred Kompass Team','sacred-kompass'); ?>" />
        </div>
      <?php endif; ?>
      <div class="sk-founder-modal-content">
        <h3 class="sk-founder-modal-name"><?php esc_html_e('Sacred Kompass','sacred-kompass'); ?> <em><?php esc_html_e('Collective','sacred-kompass'); ?></em></h3>
        <span class="sk-founder-modal-role"><?php esc_html_e('A Community of Conscious Practice','sacred-kompass'); ?></span>
        <p class="sk-founder-modal-bio"><?php esc_html_e('Sacred Kompass brings together guides, teachers, and practitioners united by one vision: to help individuals, leaders, and organisations reconnect with their inner compass. Through ancient wisdom and modern frameworks, we walk with you toward lasting transformation.','sacred-kompass'); ?></p>
        <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-primary sk-founder-modal-cta" style="margin-top:1.8rem">
          <?php esc_html_e('Book a Discovery Call','sacred-kompass'); ?>
        </a>
      </div>
    </div>
  </div>
</div>

<?php sk_render_founder_modal($kalai,      'sk-modal-kalai'); ?>
<?php sk_render_founder_modal($christophe, 'sk-modal-christophe'); ?>
