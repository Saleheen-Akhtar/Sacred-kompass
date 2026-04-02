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

$section_eyebrow    = sk_option('founders_eyebrow',    'The Founders');
$section_heading    = sk_option('founders_heading',    'The Guides Behind');
$section_heading_em = sk_option('founders_heading_em', 'Sacred Kompass');
$section_sub        = sk_option('founders_sub',        'Two souls, one vision. Uniting Eastern wisdom and Western heart in service of conscious living.');

// Card 1 = group/team photo (left big), Card 2 = Kalai (top right), Card 3 = Christophe (bottom right)
$team_card  = [
  'label' => sk_option('founders_team_title', __('Our Team','sacred-kompass')),
  'subtitle' => sk_option('founders_team_subtitle', __('Sacred Kompass Collective','sacred-kompass')),
  'bio' => sk_option('founders_team_bio', __('Sacred Kompass brings together guides, teachers, and practitioners united by one vision: to help individuals, leaders, and organisations reconnect with their inner compass.','sacred-kompass')),
  'image' => sk_option('founders_team_image','')
];
$kalai      = $founders[0] ?? ['founder_name'=>'Kalai','founder_surname'=>'Somoo','founder_role'=>'Founder and Lead Guide','founder_bio'=>'','founder_tags'=>'','founder_image'=>'','founder_origin'=>'Singapore'];
$christophe = $founders[1] ?? ['founder_name'=>'Christophe','founder_surname'=>'Grigri','founder_role'=>'International Coordination & Communication','founder_bio'=>'','founder_tags'=>'','founder_image'=>'','founder_origin'=>'France'];
$other_members = array_slice($founders, 2);

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
          <div class="founder-strip-name"><?php echo esc_html($team_card['label']); ?></div>
          <span class="founder-strip-role"><?php echo esc_html($team_card['subtitle']); ?></span>
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

    <?php if (!empty($other_members)) : ?>
    <div style="margin-top:1.5rem;text-align:right">
      <button class="btn btn-outline sk-founder-trigger" data-modal="sk-modal-team-list" aria-haspopup="dialog">
        <?php esc_html_e('View All Team Members','sacred-kompass'); ?>
      </button>
    </div>
    <?php endif; ?>
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
        <h3 class="sk-founder-modal-name"><?php echo esc_html($team_card['label']); ?></h3>
        <span class="sk-founder-modal-role"><?php echo esc_html($team_card['subtitle']); ?></span>
        <p class="sk-founder-modal-bio"><?php echo esc_html($team_card['bio']); ?></p>
        <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-primary sk-founder-modal-cta" style="margin-top:1.8rem">
          <?php esc_html_e('Book a Discovery Call','sacred-kompass'); ?>
        </a>
      </div>
    </div>
  </div>
</div>

<?php sk_render_founder_modal($kalai,      'sk-modal-kalai'); ?>
<?php sk_render_founder_modal($christophe, 'sk-modal-christophe'); ?>
<?php if (!empty($other_members)): ?>
<div class="sk-founder-modal" id="sk-modal-team-list" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('All Team Members','sacred-kompass'); ?>" hidden>
  <div class="sk-founder-modal-backdrop"></div>
  <div class="sk-founder-modal-box">
    <button class="sk-founder-modal-close" aria-label="<?php esc_attr_e('Close','sacred-kompass'); ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <div class="sk-founder-modal-inner" style="display:block;padding:2rem 2rem 1rem">
      <h3 class="sk-founder-modal-name" style="margin-bottom:1.4rem"><?php esc_html_e('All Team Members','sacred-kompass'); ?></h3>
      <?php foreach ($other_members as $m): ?>
        <article style="display:grid;grid-template-columns:84px 1fr;gap:1rem;align-items:start;margin-bottom:1.2rem;padding-bottom:1.2rem;border-bottom:1px solid rgba(0,0,0,.08)">
          <div>
            <?php if (!empty($m['founder_image'])): ?>
              <img src="<?php echo esc_url($m['founder_image']); ?>" alt="<?php echo esc_attr(trim(($m['founder_name'] ?? '').' '.($m['founder_surname'] ?? ''))); ?>" style="width:84px;height:104px;object-fit:cover;border-radius:10px" />
            <?php endif; ?>
          </div>
          <div>
            <h4 style="margin:0 0 .2rem"><?php echo esc_html(trim(($m['founder_name'] ?? '').' '.($m['founder_surname'] ?? ''))); ?></h4>
            <?php if (!empty($m['founder_role'])): ?><p style="margin:0 0 .4rem;color:#8c6b2f"><?php echo esc_html($m['founder_role']); ?></p><?php endif; ?>
            <?php if (!empty($m['founder_bio'])): ?><p style="margin:0;color:#555;line-height:1.6"><?php echo esc_html($m['founder_bio']); ?></p><?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>
