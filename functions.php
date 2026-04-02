<?php
/**
 * Sacred Kompass v5.3 — functions.php
 *
 * KEY CHANGES IN v5.3:
 *
 * 1. ZERO ACF DEPENDENCY — the settings page is now a native WordPress
 *    admin page using wp_options directly. No ACF Free, No ACF Pro needed.
 *    acf_add_options_page() (Pro-only) is completely removed.
 *
 * 2. LOGO SUPPORT — header and footer now show a logo image if one is
 *    set via Appearance → Customize → Site Identity, with text fallback.
 *
 * 3. FOUNDERS & VALUES & PILLARS — repeater data stored as JSON in
 *    wp_options so no ACF repeater field reading is needed.
 *
 * 4. NATIVE SETTINGS PAGE — all fields rendered with plain HTML form,
 *    saved via wp_options. Works on every WordPress install, zero plugins.
 */
defined('ABSPATH') || exit;

require_once __DIR__ . '/inc/cpt.php';

/* ════════════════════════════════════════════════════════════
   SACRED KOMPASS ADMIN MENU — pure WP, no ACF required
   ════════════════════════════════════════════════════════════ */
add_action('admin_menu', 'sk_register_admin_menu', 9);
function sk_register_admin_menu(): void {
    add_menu_page(
        'Sacred Kompass — Site Settings',
        '★ Sacred Kompass',
        'edit_posts',
        'sk-settings',
        'sk_settings_page',
        'dashicons-star-filled',
        25
    );
}

add_action('admin_menu', 'sk_nest_cpt_menus', 99);
function sk_nest_cpt_menus(): void {
    remove_menu_page('edit.php?post_type=sk_offering');
    remove_menu_page('edit.php?post_type=sk_faq');
    add_submenu_page('sk-settings', 'Offerings', '✦ Offerings', 'edit_posts', 'edit.php?post_type=sk_offering');
    add_submenu_page('sk-settings', 'FAQ',       '✦ FAQ',       'edit_posts', 'edit.php?post_type=sk_faq');
}

/* ════════════════════════════════════════════════════════════
   SETTINGS PAGE — native HTML form, saved to wp_options
   ════════════════════════════════════════════════════════════ */
function sk_settings_page(): void {
    if (!current_user_can('edit_posts')) wp_die('Access denied.');

    /* ── Handle save ── */
    if (isset($_POST['sk_settings_nonce']) && wp_verify_nonce($_POST['sk_settings_nonce'], 'sk_save_settings')) {
        $text_fields = [
            'sk_hero_stage1','sk_hero_stage2a','sk_hero_stage2b','sk_hero_stage3a','sk_hero_stage3b',
            'sk_hero_sub','sk_hero_cta1_text','sk_hero_cta1_url','sk_hero_cta2_text','sk_hero_cta2_url',
            'sk_hero_bg_image','sk_hero_right_image',
            'sk_about_eyebrow','sk_about_heading','sk_about_body','sk_about_quote','sk_about_quote_attr','sk_about_traditions',
            'sk_quote_eyebrow','sk_quote_text','sk_quote_highlight','sk_quote_attr',
            'sk_founders_eyebrow','sk_founders_heading','sk_founders_heading_em','sk_founders_sub',
            'sk_cta_eyebrow','sk_cta_sub','sk_forminator_form_id',
            'sk_footer_email','sk_footer_phone','sk_footer_tagline','sk_footer_copyright',
            'sk_social_instagram','sk_social_facebook','sk_social_whatsapp',
        ];
        foreach ($text_fields as $k) {
            $val = isset($_POST[$k]) ? wp_kses_post(stripslashes($_POST[$k])) : '';
            update_option('options_' . $k, $val, false);
        }

        /* Pillars */
        $pillars = [];
        if (!empty($_POST['pillar_num']) && is_array($_POST['pillar_num'])) {
            foreach ($_POST['pillar_num'] as $i => $num) {
                $pillars[] = [
                    'pillar_num'   => sanitize_text_field($num),
                    'pillar_title' => sanitize_text_field($_POST['pillar_title'][$i] ?? ''),
                    'pillar_desc'  => sanitize_textarea_field($_POST['pillar_desc'][$i] ?? ''),
                ];
            }
        }
        update_option('options_sk_philosophy_pillars_json', wp_json_encode($pillars), false);

        /* Founders */
        $founders = [];
        if (!empty($_POST['founder_name']) && is_array($_POST['founder_name'])) {
            foreach ($_POST['founder_name'] as $i => $name) {
                $founders[] = [
                    'founder_image'   => sanitize_text_field($_POST['founder_image'][$i] ?? ''),
                    'founder_name'    => sanitize_text_field($name),
                    'founder_surname' => sanitize_text_field($_POST['founder_surname'][$i] ?? ''),
                    'founder_origin'  => sanitize_text_field($_POST['founder_origin'][$i] ?? ''),
                    'founder_role'    => sanitize_text_field($_POST['founder_role'][$i] ?? ''),
                    'founder_bio'     => sanitize_textarea_field($_POST['founder_bio'][$i] ?? ''),
                    'founder_tags'    => sanitize_textarea_field($_POST['founder_tags'][$i] ?? ''),
                ];
            }
        }
        update_option('options_sk_founders_json', wp_json_encode($founders), false);

        /* Values */
        $values = [];
        if (!empty($_POST['value_title']) && is_array($_POST['value_title'])) {
            foreach ($_POST['value_title'] as $i => $title) {
                $values[] = [
                    'value_title' => sanitize_text_field($title),
                    'value_desc'  => sanitize_textarea_field($_POST['value_desc'][$i] ?? ''),
                ];
            }
        }
        update_option('options_sk_values_json', wp_json_encode($values), false);

        echo '<div class="notice notice-success is-dismissible" style="margin:10px 0 20px"><p><strong>Sacred Kompass:</strong> Settings saved successfully.</p></div>';
    }

    /* ── Load values for display ── */
    $o = function(string $k, string $fb = ''): string {
        return esc_attr((string) get_option('options_' . $k, $fb));
    };
    $t = function(string $k, string $fb = ''): string {
        return esc_textarea((string) get_option('options_' . $k, $fb));
    };

    $pillars  = sk_repeater('options_sk_philosophy_pillars_json');
    $founders = sk_repeater('options_sk_founders_json');
    $values   = sk_repeater('options_sk_values_json');

    if (empty($pillars))  $pillars  = sk_default_pillars();
    if (empty($founders)) $founders = sk_default_founders();
    if (empty($values))   $values   = sk_default_values();

    ?>
    <div class="wrap">
    <h1>★ Sacred Kompass — Site Settings</h1>
    <form method="post" action="" id="sk-settings-form">
    <?php wp_nonce_field('sk_save_settings', 'sk_settings_nonce'); ?>

    <style>
    #sk-settings-form{max-width:900px}
    .sk-save-bar{position:sticky;top:32px;z-index:99;background:#f0f0f1;padding:10px 0;margin:0 0 20px;border-bottom:1px solid #ddd}
    .sk-section{background:#fff;border:1px solid #c3c4c7;border-radius:4px;margin:0 0 20px;padding:20px 24px}
    .sk-section > h2{font-size:14px;font-weight:700;margin:0 0 16px;padding:0 0 10px;border-bottom:1px solid #f0f0f1;color:#1d2327;text-transform:uppercase;letter-spacing:.05em}
    .sk-row{display:grid;grid-template-columns:200px 1fr;gap:6px 16px;align-items:start;margin:0 0 12px}
    .sk-row label{font-size:13px;font-weight:500;padding-top:7px;color:#3c434a}
    .sk-row input[type=text],.sk-row input[type=url],.sk-row input[type=email],.sk-row input[type=number]{width:100%;box-sizing:border-box}
    .sk-row textarea{width:100%;box-sizing:border-box}
    .sk-hint{color:#646970;font-size:11px;margin:3px 0 0}
    .sk-rep-row{background:#f9f9f9;border:1px solid #dcdcde;border-radius:3px;padding:14px 16px;margin:0 0 10px;position:relative}
    .sk-rep-row h4{margin:0 0 12px;font-size:13px;color:#1d2327;font-weight:600}
    .sk-btn-del{position:absolute;top:10px;right:10px;background:#dc3232;color:#fff;border:none;border-radius:3px;padding:3px 10px;font-size:11px;cursor:pointer;line-height:1.6}
    .sk-btn-del:hover{background:#b32d2e}
    .sk-btn-add{background:#2271b1;color:#fff;border:none;border-radius:3px;padding:7px 16px;font-size:13px;cursor:pointer;margin-top:4px}
    .sk-btn-add:hover{background:#135e96}
    .sk-img-preview{max-width:80px;max-height:80px;border-radius:4px;margin:6px 0 0;display:block;border:1px solid #ddd}
    </style>

    <div class="sk-save-bar">
        <input type="submit" class="button button-primary button-large" value="Save All Changes" />
        <span style="margin-left:12px;color:#646970;font-size:13px">Changes apply immediately after saving.</span>
    </div>

    <!-- HERO -->
    <div class="sk-section">
    <h2>✦ Hero Section</h2>
    <?php sk_row('Stage 1 — Opening line','sk_hero_stage1',$o('sk_hero_stage1','The world pulls at you from every direction.')); ?>
    <?php sk_row('Stage 2 — Line 1','sk_hero_stage2a',$o('sk_hero_stage2a','Something inside you')); ?>
    <?php sk_row('Stage 2 — Line 2','sk_hero_stage2b',$o('sk_hero_stage2b','is calling for stillness.')); ?>
    <?php sk_row('Stage 3 — Line 1','sk_hero_stage3a',$o('sk_hero_stage3a','We walk you')); ?>
    <?php sk_row('Stage 3 — Line 2','sk_hero_stage3b',$o('sk_hero_stage3b','back to yourself.')); ?>
    <?php sk_row_ta('Sub-description','sk_hero_sub',$t('sk_hero_sub'),3); ?>
    <?php sk_row('Primary Button Text','sk_hero_cta1_text',$o('sk_hero_cta1_text','Book a Free Discovery Call')); ?>
    <?php sk_row('Primary Button URL','sk_hero_cta1_url',$o('sk_hero_cta1_url','/#contact')); ?>
    <?php sk_row('Secondary Button Text','sk_hero_cta2_text',$o('sk_hero_cta2_text','Explore Offerings')); ?>
    <?php sk_row('Secondary Button URL','sk_hero_cta2_url',$o('sk_hero_cta2_url','#offerings')); ?>

    <!-- Hero Image Fields -->
    <div class="sk-row">
      <label><strong>Background Image</strong></label>
      <div>
        <input type="text" name="sk_hero_bg_image" id="sk_hero_bg_image"
               value="<?php echo $o('sk_hero_bg_image',''); ?>"
               placeholder="https://… paste URL or drag &amp; drop below"
               style="width:100%;box-sizing:border-box;margin-bottom:8px" />
        <div id="sk-hero-bg-dropzone"
             style="border:2px dashed #c49a2a;border-radius:8px;padding:16px;text-align:center;cursor:pointer;font-size:12px;color:#888;transition:background .2s"
             ondragover="event.preventDefault();this.style.background='rgba(196,154,42,.08)'"
             ondragleave="this.style.background=''"
             ondrop="(function(e){e.preventDefault();var f=e.dataTransfer.files[0];if(!f||!f.type.startsWith('image/'))return;var r=new FileReader();r.onload=function(ev){document.getElementById('sk_hero_bg_image').value=ev.target.result;document.getElementById('sk-hero-bg-preview').src=ev.target.result;document.getElementById('sk-hero-bg-preview').style.display='block'};r.readAsDataURL(f)})(event)">
          Drop image here to preview &nbsp;·&nbsp; <small>Upload via <a href="<?php echo admin_url('media-new.php'); ?>" target="_blank">Media → Add New</a> for permanent URL</small>
        </div>
        <?php $bg_v = get_option('options_sk_hero_bg_image',''); ?>
        <img id="sk-hero-bg-preview" src="<?php echo esc_url($bg_v); ?>"
             style="<?php echo $bg_v ? '' : 'display:none;'; ?>width:100%;max-height:180px;object-fit:cover;border-radius:6px;margin-top:8px" />
        <p class="sk-hint">Full-bleed background for the hero. Recommended: 1920×1080px landscape, dark/atmospheric.</p>
      </div>
    </div>

    <div class="sk-row">
      <label><strong>Right Panel Image</strong></label>
      <div>
        <input type="text" name="sk_hero_right_image" id="sk_hero_right_image"
               value="<?php echo $o('sk_hero_right_image',''); ?>"
               placeholder="https://… paste URL or drag &amp; drop below"
               style="width:100%;box-sizing:border-box;margin-bottom:8px" />
        <div id="sk-hero-rt-dropzone"
             style="border:2px dashed #c49a2a;border-radius:8px;padding:16px;text-align:center;cursor:pointer;font-size:12px;color:#888;transition:background .2s"
             ondragover="event.preventDefault();this.style.background='rgba(196,154,42,.08)'"
             ondragleave="this.style.background=''"
             ondrop="(function(e){e.preventDefault();var f=e.dataTransfer.files[0];if(!f||!f.type.startsWith('image/'))return;var r=new FileReader();r.onload=function(ev){document.getElementById('sk_hero_right_image').value=ev.target.result;document.getElementById('sk-hero-rt-preview').src=ev.target.result;document.getElementById('sk-hero-rt-preview').style.display='block'};r.readAsDataURL(f)})(event)">
          Drop image here to preview &nbsp;·&nbsp; <small>Upload via <a href="<?php echo admin_url('media-new.php'); ?>" target="_blank">Media → Add New</a> for permanent URL</small>
        </div>
        <?php $rt_v = get_option('options_sk_hero_right_image',''); ?>
        <img id="sk-hero-rt-preview" src="<?php echo esc_url($rt_v); ?>"
             style="<?php echo $rt_v ? '' : 'display:none;'; ?>width:100%;max-height:220px;object-fit:cover;border-radius:6px;margin-top:8px" />
        <p class="sk-hint">Shown in the right column of the hero. Recommended: 800×1000px portrait, subject facing left.</p>
      </div>
    </div>
    </div>

    <!-- ABOUT -->
    <div class="sk-section">
    <h2>✦ About Section</h2>
    <?php sk_row('Eyebrow','sk_about_eyebrow',$o('sk_about_eyebrow','Our Philosophy')); ?>
    <?php sk_row('Heading','sk_about_heading',$o('sk_about_heading','Where the Sacred Meets the Everyday')); ?>
    <?php sk_row_ta('Body Text (HTML allowed)','sk_about_body',$t('sk_about_body'),6); ?>
    <?php sk_row_ta('Pull Quote','sk_about_quote',$t('sk_about_quote'),2); ?>
    <?php sk_row('Quote Attribution','sk_about_quote_attr',$o('sk_about_quote_attr','Sacred Kompass, Our Philosophy')); ?>
    <?php sk_row_ta('Tradition Tags (one per line)','sk_about_traditions',$t('sk_about_traditions'),5,'Shown as pill tags under the About text.'); ?>
    </div>

    <!-- PHILOSOPHY STRIP -->
    <div class="sk-section">
    <h2>✦ Philosophy Strip</h2>
    <div id="pillars-wrap">
    <?php foreach ($pillars as $pi => $p): ?>
    <div class="sk-rep-row" data-type="pillar">
        <h4>Pillar <?php echo $pi+1; ?></h4>
        <button type="button" class="sk-btn-del" onclick="this.closest('.sk-rep-row').remove()">Remove</button>
        <?php sk_sub_row('No.','pillar_num[]',esc_attr($p['pillar_num']??'')); ?>
        <?php sk_sub_row('Title','pillar_title[]',esc_attr($p['pillar_title']??'')); ?>
        <?php sk_sub_row_ta('Description','pillar_desc[]',esc_textarea($p['pillar_desc']??''),2); ?>
    </div>
    <?php endforeach; ?>
    </div>
    <button type="button" class="sk-btn-add" onclick="skAdd('pillar','pillars-wrap')">+ Add Pillar</button>
    </div>

    <!-- QUOTE BAND -->
    <div class="sk-section">
    <h2>✦ Quote Band</h2>
    <?php sk_row('Eyebrow','sk_quote_eyebrow',$o('sk_quote_eyebrow','Our Vision')); ?>
    <?php sk_row_ta('Quote Text','sk_quote_text',$t('sk_quote_text'),4); ?>
    <?php sk_row('Highlight Phrase','sk_quote_highlight',$o('sk_quote_highlight','inner compass'),'Exact phrase to be coloured in blush'); ?>
    <?php sk_row('Attribution','sk_quote_attr',$o('sk_quote_attr','Sacred Kompass Collective, Vision Statement')); ?>
    </div>

    <!-- FOUNDERS -->
    <div class="sk-section">
    <h2>✦ Founders</h2>
    <?php sk_row('Section Eyebrow','sk_founders_eyebrow',$o('sk_founders_eyebrow','The Founders')); ?>
    <?php sk_row('Section Heading','sk_founders_heading',$o('sk_founders_heading','The Guides Behind')); ?>
    <?php sk_row('Heading (italic part)','sk_founders_heading_em',$o('sk_founders_heading_em','Sacred Kompass')); ?>
    <?php sk_row_ta('Section Sub-text','sk_founders_sub',$t('sk_founders_sub'),2); ?>
    <div id="founders-wrap">
    <?php foreach ($founders as $fi => $f): ?>
    <div class="sk-rep-row" data-type="founder">
        <h4>Founder <?php echo $fi+1; ?></h4>
        <button type="button" class="sk-btn-del" onclick="this.closest('.sk-rep-row').remove()">Remove</button>
        <div class="sk-row">
            <label>Portrait Photo URL</label>
            <div>
                <input type="text" name="founder_image[]" value="<?php echo esc_attr($f['founder_image']??''); ?>" placeholder="https://... (paste URL from Media Library)" style="width:100%;box-sizing:border-box" />
                <?php if (!empty($f['founder_image'])): ?><img src="<?php echo esc_url($f['founder_image']); ?>" class="sk-img-preview" /><?php endif; ?>
                <p class="sk-hint">Upload via <a href="<?php echo admin_url('media-new.php'); ?>" target="_blank">Media → Add New</a>, then copy the file URL here. Min 520×700px portrait.</p>
            </div>
        </div>
        <?php sk_sub_row('First Name','founder_name[]',esc_attr($f['founder_name']??'')); ?>
        <?php sk_sub_row('Last Name','founder_surname[]',esc_attr($f['founder_surname']??'')); ?>
        <?php sk_sub_row('Origin / Country','founder_origin[]',esc_attr($f['founder_origin']??'')); ?>
        <?php sk_sub_row('Role / Title','founder_role[]',esc_attr($f['founder_role']??'')); ?>
        <?php sk_sub_row_ta('Bio','founder_bio[]',esc_textarea($f['founder_bio']??''),4); ?>
        <?php sk_sub_row_ta('Expertise Tags (one per line)','founder_tags[]',esc_textarea($f['founder_tags']??''),3); ?>
    </div>
    <?php endforeach; ?>
    </div>
    <button type="button" class="sk-btn-add" onclick="skAdd('founder','founders-wrap')">+ Add Founder</button>
    </div>

    <!-- CORE VALUES -->
    <div class="sk-section">
    <h2>✦ Core Values</h2>
    <div id="values-wrap">
    <?php foreach ($values as $vi => $v): ?>
    <div class="sk-rep-row" data-type="value">
        <h4>Value <?php echo $vi+1; ?></h4>
        <button type="button" class="sk-btn-del" onclick="this.closest('.sk-rep-row').remove()">Remove</button>
        <?php sk_sub_row('Title','value_title[]',esc_attr($v['value_title']??'')); ?>
        <?php sk_sub_row_ta('Description','value_desc[]',esc_textarea($v['value_desc']??''),3); ?>
    </div>
    <?php endforeach; ?>
    </div>
    <button type="button" class="sk-btn-add" onclick="skAdd('value','values-wrap')">+ Add Value</button>
    </div>

    <!-- CONTACT -->
    <div class="sk-section">
    <h2>✦ Contact Section</h2>
    <?php sk_row('Eyebrow','sk_cta_eyebrow',$o('sk_cta_eyebrow','Begin Your Journey')); ?>
    <?php sk_row_ta('Sub-text','sk_cta_sub',$t('sk_cta_sub'),3); ?>
    <?php sk_row('Forminator Form ID','sk_forminator_form_id',$o('sk_forminator_form_id'),'Forminator → Forms → hover form → ID in URL'); ?>
    </div>

    <!-- FOOTER & SOCIAL -->
    <div class="sk-section">
    <h2>✦ Footer &amp; Social</h2>
    <?php sk_row('Contact Email','sk_footer_email',$o('sk_footer_email','collective@sacredkompass.org')); ?>
    <?php sk_row('Contact Phone','sk_footer_phone',$o('sk_footer_phone','+65 84343915')); ?>
    <?php sk_row_ta('Footer Tagline','sk_footer_tagline',$t('sk_footer_tagline'),2); ?>
    <?php sk_row('Copyright','sk_footer_copyright',$o('sk_footer_copyright','Sacred Kompass Collective · Singapore'),'Year is auto-prepended'); ?>
    <?php sk_row('Instagram URL','sk_social_instagram',$o('sk_social_instagram')); ?>
    <?php sk_row('Facebook URL','sk_social_facebook',$o('sk_social_facebook')); ?>
    <?php sk_row('WhatsApp Link','sk_social_whatsapp',$o('sk_social_whatsapp'),'Format: https://wa.me/6584343915'); ?>
    </div>

    <div class="sk-save-bar" style="position:static;margin-top:0">
        <input type="submit" class="button button-primary button-large" value="Save All Changes" />
    </div>
    </form>
    </div>

    <script>
    const skT = {
        pillar:`<div class="sk-rep-row" data-type="pillar"><h4>Pillar</h4><button type="button" class="sk-btn-del" onclick="this.closest('.sk-rep-row').remove()">Remove</button><div class="sk-row"><label>No.</label><input type="text" name="pillar_num[]" value="" /></div><div class="sk-row"><label>Title</label><input type="text" name="pillar_title[]" value="" /></div><div class="sk-row"><label>Description</label><textarea name="pillar_desc[]" rows="2" style="width:100%;box-sizing:border-box"></textarea></div></div>`,
        founder:`<div class="sk-rep-row" data-type="founder"><h4>Founder</h4><button type="button" class="sk-btn-del" onclick="this.closest('.sk-rep-row').remove()">Remove</button><div class="sk-row"><label>Portrait Photo URL</label><div><input type="text" name="founder_image[]" value="" placeholder="https://..." style="width:100%;box-sizing:border-box"/><p class="sk-hint">Upload via Media Library, copy file URL here.</p></div></div><div class="sk-row"><label>First Name</label><input type="text" name="founder_name[]" value="" /></div><div class="sk-row"><label>Last Name</label><input type="text" name="founder_surname[]" value="" /></div><div class="sk-row"><label>Origin / Country</label><input type="text" name="founder_origin[]" value="" /></div><div class="sk-row"><label>Role / Title</label><input type="text" name="founder_role[]" value="" /></div><div class="sk-row"><label>Bio</label><textarea name="founder_bio[]" rows="4" style="width:100%;box-sizing:border-box"></textarea></div><div class="sk-row"><label>Expertise Tags</label><textarea name="founder_tags[]" rows="3" style="width:100%;box-sizing:border-box"></textarea></div></div>`,
        value:`<div class="sk-rep-row" data-type="value"><h4>Value</h4><button type="button" class="sk-btn-del" onclick="this.closest('.sk-rep-row').remove()">Remove</button><div class="sk-row"><label>Title</label><input type="text" name="value_title[]" value="" /></div><div class="sk-row"><label>Description</label><textarea name="value_desc[]" rows="3" style="width:100%;box-sizing:border-box"></textarea></div></div>`
    };
    function skAdd(type,wrapId){
        const wrap=document.getElementById(wrapId);
        const div=document.createElement('div');
        div.innerHTML=skT[type];
        wrap.appendChild(div.firstElementChild);
    }
    </script>
    <?php
}

/* Form field helpers */
function sk_row(string $label, string $name, string $val, string $hint=''): void {
    echo '<div class="sk-row"><label for="'.esc_attr($name).'">'.esc_html($label).'</label><div>';
    echo '<input type="text" id="'.esc_attr($name).'" name="'.esc_attr($name).'" value="'.$val.'" />';
    if ($hint) echo '<p class="sk-hint">'.esc_html($hint).'</p>';
    echo '</div></div>';
}
function sk_row_ta(string $label, string $name, string $val, int $rows=3, string $hint=''): void {
    echo '<div class="sk-row"><label for="'.esc_attr($name).'">'.esc_html($label).'</label><div>';
    echo '<textarea id="'.esc_attr($name).'" name="'.esc_attr($name).'" rows="'.$rows.'">'.$val.'</textarea>';
    if ($hint) echo '<p class="sk-hint">'.esc_html($hint).'</p>';
    echo '</div></div>';
}
function sk_sub_row(string $label, string $name, string $val): void {
    echo '<div class="sk-row"><label>'.esc_html($label).'</label>';
    echo '<input type="text" name="'.esc_attr($name).'" value="'.$val.'" /></div>';
}
function sk_sub_row_ta(string $label, string $name, string $val, int $rows=3): void {
    echo '<div class="sk-row"><label>'.esc_html($label).'</label>';
    echo '<textarea name="'.esc_attr($name).'" rows="'.$rows.'" style="width:100%;box-sizing:border-box">'.$val.'</textarea></div>';
}

/* ════════════════════════════════════════════════════════════
   HELPERS — read settings
   ════════════════════════════════════════════════════════════ */
function sk_acf(string $key, mixed $fallback = ''): mixed {
    $val = get_option('options_' . $key, null);
    if ($val !== null && $val !== '' && $val !== [] && $val !== false) return $val;
    return $fallback;
}
function sk_option(string $key, mixed $fallback = ''): mixed {
    return sk_acf('sk_' . $key, $fallback);
}
function sk_repeater(string $option_key): array {
    $json = get_option($option_key, '');
    if (!$json) return [];
    $data = json_decode($json, true);
    return (is_array($data) && !empty($data)) ? $data : [];
}

/* ════════════════════════════════════════════════════════════
   DEFAULT DATA
   ════════════════════════════════════════════════════════════ */
function sk_acf_defaults(): array {
    return [
        'sk_hero_stage1'         => 'The world pulls at you from every direction.',
        'sk_hero_stage2a'        => 'Something inside you',
        'sk_hero_stage2b'        => 'is calling for stillness.',
        'sk_hero_stage3a'        => 'We walk you',
        'sk_hero_stage3b'        => 'back to yourself.',
        'sk_hero_sub'            => 'Sacred Kompass is a transformative wellness consultancy weaving Vedic philosophy, Jyotish astrology, and compassionate practice into in-depth, inside-out transformation.',
        'sk_hero_cta1_text'      => 'Book a Free Discovery Call',
        'sk_hero_cta1_url'       => '/#contact',
        'sk_hero_cta2_text'      => 'Explore Offerings',
        'sk_hero_cta2_url'       => '#offerings',
        'sk_about_eyebrow'       => 'Our Philosophy',
        'sk_about_heading'       => 'Where the Sacred Meets the Everyday',
        'sk_about_body'          => '<p>Sacred Kompass is a transformative wellness and consciousness-based consultancy. We weave together ancient wisdom, Vedic philosophy, Jyotish astrology, meditation, and self-awareness, with modern frameworks like Nonviolent Communication and emotional resilience practices.</p><p>We are not about temporary fixes. We are about in-depth transformation, supporting individuals, leaders, and organisations in cultivating inner clarity, compassionate engagement, and sustainable growth from the inside out.</p>',
        'sk_about_quote'         => '"True transformation is not found. It is remembered. We walk with you back to what was always whole."',
        'sk_about_quote_attr'    => 'Sacred Kompass, Our Philosophy',
        'sk_about_traditions'    => "Vedic Philosophy\nJyotish Astrology\nMeditation\nNVC\nSacred Feminine\nBreathwork\nEnergy Healing\nEmotional Resilience",
        'sk_quote_eyebrow'       => 'Our Vision',
        'sk_quote_text'          => "We envision a world where well-being and performance coexist harmoniously. By reconnecting people to their inner compass, we help them navigate life's complexities with purpose and alignment.",
        'sk_quote_highlight'     => 'inner compass',
        'sk_quote_attr'          => 'Sacred Kompass Collective, Vision Statement',
        'sk_founders_eyebrow'    => 'The Founders',
        'sk_founders_heading'    => 'The Guides Behind',
        'sk_founders_heading_em' => 'Sacred Kompass',
        'sk_founders_sub'        => 'Two souls, one vision. Uniting Eastern wisdom and Western heart in service of conscious living.',
        'sk_cta_eyebrow'         => 'Begin Your Journey',
        'sk_cta_sub'             => 'A unique fusion of sacred traditions and practical application. Not temporary fixes, but in-depth transformation that helps you thrive from the inside out.',
        'sk_forminator_form_id'  => '',
        'sk_footer_email'        => 'collective@sacredkompass.org',
        'sk_footer_phone'        => '+65 84343915',
        'sk_footer_tagline'      => 'Ancient wisdom for the modern soul. Transformative guidance for individuals, leaders, and organisations.',
        'sk_footer_copyright'    => 'Sacred Kompass Collective · Singapore',
        'sk_social_instagram'    => '',
        'sk_social_facebook'     => '',
        'sk_social_whatsapp'     => '',
    ];
}
function sk_default_pillars(): array {
    return [
        ['pillar_num'=>'01','pillar_title'=>'Ancient Wisdom',         'pillar_desc'=>'Rooted in the deep soil of Vedic philosophy and centuries of sacred contemplative tradition — we offer not a system to follow, but a living river to return to.'],
        ['pillar_num'=>'02','pillar_title'=>'Compassionate Practice', 'pillar_desc'=>'Nonviolent Communication and emotional resilience woven into the fabric of how we meet the world — not as techniques, but as a way of being that transforms every conversation, every relationship, every moment of conflict into an opening.'],
        ['pillar_num'=>'03','pillar_title'=>'Inner Stillness',        'pillar_desc'=>'Meditation, breathwork, and the art of presence — practices that do not silence the noise of life, but teach you to rest so deeply within yourself that the noise loses its grip.'],
        ['pillar_num'=>'04','pillar_title'=>'Jyotish Astrology',      'pillar_desc'=>'The luminous science of light and time — Jyotish astrology as a sacred map of your soul\'s journey, offering clarity on your dharma, your gifts, and the seasons of transformation already written in the stars.'],
        ['pillar_num'=>'05','pillar_title'=>'Sacred Feminine',        'pillar_desc'=>'Honouring the intelligence of the feminine — cyclical, intuitive, embodied. A remembering of what has been suppressed, and a reclaiming of wholeness for every woman, every leader, every soul willing to bow to the deeper wisdom within.'],
    ];
}
function sk_default_founders(): array {
    return [
        ['founder_image'=>'','founder_name'=>'Kalai','founder_surname'=>'Somoo','founder_origin'=>'Singapore','founder_role'=>'Founder and Lead Guide','founder_bio'=>"Kalai founded Sacred Kompass with a vision to reconnect people to their inner wisdom. With deep roots in Vedic philosophy, sacred feminine practices, Jyotish astrology, and women's empowerment, she guides individuals and organisations through transformative, inside-out growth.",'founder_tags'=>"Women's Wellness\nVedic Philosophy\nJyotish Astrology\nSacred Feminine\nCoaching"],
        ['founder_image'=>'','founder_name'=>'Christophe','founder_surname'=>'Grigri','founder_origin'=>'France','founder_role'=>'International Coordination and Communication','founder_bio'=>"Christophe brings decades of international experience bridging cultures through compassionate dialogue and conscious leadership. Trained in Gandhian non-violence and NVC, he coordinates Sacred Kompass's global outreach and shapes the communicative heart of the collective.",'founder_tags'=>"NVC\nGandhian Non-Violence\nInternational Coordination\nConscious Leadership"],
    ];
}
function sk_default_values(): array {
    return [
        ['value_title'=>'Sacred Presence',    'value_desc'=>'We believe transformation begins with stillness. Every session, every conversation, every offering is held in a space of deep, unhurried presence.'],
        ['value_title'=>'Compassionate Truth', 'value_desc'=>'We speak from the heart and listen with the same depth. Nonviolent Communication is not just a tool — it is a way of being that runs through everything we do.'],
        ['value_title'=>'Ancient Wisdom',      'value_desc'=>'We honour the timeless traditions that have guided human flourishing for millennia, as living, breathing guides rather than museum pieces.'],
        ['value_title'=>'Conscious Growth',    'value_desc'=>'We believe sustainable change comes from within. Our work is about remembering what was always whole, and living outward from that place.'],
    ];
}

/* ════════════════════════════════════════════════════════════
   AUTO-SETUP — runs on every init, skips if already done
   ════════════════════════════════════════════════════════════ */
add_action('init', 'sk_auto_setup', 20);
function sk_auto_setup(): void {
    if (get_option('sk_setup_done_v53')) return;

    $pages = ['home'=>'Home','about'=>'About','offerings'=>'Offerings','founders'=>'Founders','faq'=>'FAQ','contact'=>'Contact','privacy-policy'=>'Privacy Policy','terms'=>'Terms of Use','disclaimer'=>'Disclaimer'];
    $home_id = 0;
    foreach ($pages as $slug => $title) {
        $existing = get_page_by_path($slug);
        if ($existing) { if ($slug==='home') $home_id=$existing->ID; continue; }
        $id = wp_insert_post(['post_title'=>$title,'post_name'=>$slug,'post_status'=>'publish','post_type'=>'page','post_content'=>'']);
        if (!is_wp_error($id) && $slug==='home') $home_id = $id;
    }
    if ($home_id) { update_option('show_on_front','page'); update_option('page_on_front',$home_id); }

    foreach (sk_acf_defaults() as $key => $val) {
        if (get_option('options_'.$key)===false || get_option('options_'.$key)==='') {
            update_option('options_'.$key, $val, false);
        }
    }
    if (!get_option('options_sk_philosophy_pillars_json')) update_option('options_sk_philosophy_pillars_json', wp_json_encode(sk_default_pillars()), false);
    if (!get_option('options_sk_founders_json'))           update_option('options_sk_founders_json',           wp_json_encode(sk_default_founders()), false);
    if (!get_option('options_sk_values_json'))             update_option('options_sk_values_json',             wp_json_encode(sk_default_values()),   false);

    require_once __DIR__ . '/inc/content.php';
    sk_insert_default_content();

    update_option('sk_setup_done_v53', true, false);
    flush_rewrite_rules();
}

/* ════════════════════════════════════════════════════════════
   RE-SEED TOOL — /wp-admin/?sk_reseed=1
   ════════════════════════════════════════════════════════════ */
add_action('admin_init', 'sk_maybe_reseed');
function sk_maybe_reseed(): void {
    if (empty($_GET['sk_reseed']) || !current_user_can('manage_options')) return;

    delete_option('sk_setup_done_v53');
    delete_option('sk_setup_done_v52');
    delete_option('options_sk_philosophy_pillars_json');
    delete_option('options_sk_founders_json');
    delete_option('options_sk_values_json');

    foreach (sk_acf_defaults() as $key => $val) {
        update_option('options_'.$key, $val, false);
    }
    update_option('options_sk_philosophy_pillars_json', wp_json_encode(sk_default_pillars()), false);
    update_option('options_sk_founders_json',           wp_json_encode(sk_default_founders()), false);
    update_option('options_sk_values_json',             wp_json_encode(sk_default_values()),   false);

    require_once __DIR__ . '/inc/content.php';
    sk_insert_default_content();

    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Sacred Kompass:</strong> Re-seeded successfully. <a href="'.admin_url().'">Dashboard →</a></p></div>';
    });
}

/* ════════════════════════════════════════════════════════════
   LOGO HELPER
   ════════════════════════════════════════════════════════════ */
function sk_logo_html(string $class = 'sk-logo-img'): string {
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $img = wp_get_attachment_image($logo_id, 'full', false, ['class'=>$class,'alt'=>get_bloginfo('name')]);
        if ($img) return $img;
    }
    return '';
}

/* ════════════════════════════════════════════════════════════
   THEME SUPPORT + MENUS
   ════════════════════════════════════════════════════════════ */
add_action('after_setup_theme', function(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', ['height'=>80,'width'=>240,'flex-width'=>true,'flex-height'=>true]);
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
    register_nav_menus(['primary'=>'Primary Navigation','footer'=>'Footer Menu']);
});

/* ════════════════════════════════════════════════════════════
   ENQUEUE
   ════════════════════════════════════════════════════════════ */
add_action('wp_enqueue_scripts', function(): void {
    wp_enqueue_style('sacred-kompass-style', get_stylesheet_uri(), [], '5.3.0');
    wp_enqueue_script('sacred-kompass-main', get_template_directory_uri().'/assets/js/main.js', [], '5.3.0', true);
    wp_localize_script('sacred-kompass-main', 'skData', [
        'ajaxurl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('sk_contact_nonce'),
        'whatsapp' => sk_option('social_whatsapp', ''),
    ]);
});

/* ════════════════════════════════════════════════════════════
   FORMINATOR — returning visitor
   ════════════════════════════════════════════════════════════ */
if (class_exists('Forminator')) {
    define('SK_EMAIL_FIELD', 'email-1');
    add_filter('forminator_custom_form_success_message', 'sk_returning_visitor_message', 10, 4);
    function sk_returning_visitor_message(string $msg, mixed $form, mixed $form_id, mixed $fields): string {
        global $wpdb;
        $email = '';
        if (is_array($fields)) {
            foreach ($fields as $f) {
                $name = $f['name'] ?? '';
                if ($name === SK_EMAIL_FIELD || stripos($name,'email') !== false) {
                    $email = sanitize_email($f['value'] ?? '');
                    if ($email) break;
                }
            }
        }
        if (empty($email)) return esc_html__('Your message has been received. We will connect with you soon.','sacred-kompass');
        $count = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT entry_id) FROM {$wpdb->prefix}frmt_form_entry_meta WHERE meta_key=%s AND meta_value=%s", SK_EMAIL_FIELD, $email));
        return $count > 1
            ? esc_html__("Welcome back — it's good to hear from you again. We'll reconnect shortly.",'sacred-kompass')
            : esc_html__('Your message has been received. We will connect with you soon.','sacred-kompass');
    }
}

/* ════════════════════════════════════════════════════════════
   FALLBACK CONTACT FORM — AJAX handler, rate limiting, honeypot
   Handles sk_contact_fallback_form (shown when Forminator is absent).
   ════════════════════════════════════════════════════════════ */
add_action('wp_ajax_nopriv_sk_contact_submit', 'sk_handle_contact_submit');
add_action('wp_ajax_sk_contact_submit',        'sk_handle_contact_submit');
function sk_handle_contact_submit(): void {
    /* 1 ── Nonce verification */
    check_ajax_referer('sk_contact_nonce', 'nonce');

    /* 2 ── Honeypot: bots fill hidden "website" field, humans never see it */
    if ( ! empty( $_POST['website'] ) ) {
        wp_send_json_success( [ 'msg' => __( 'Your message has been received. We will connect with you soon.', 'sacred-kompass' ) ] );
    }

    /* 3 ── Per-IP rate limiting: max 3 submissions per hour */
    $ip     = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
    $rl_key = 'sk_rl_' . md5( $ip );
    $hits   = (int) get_transient( $rl_key );
    if ( $hits >= 3 ) {
        wp_send_json_error( [ 'msg' => __( "You've sent several messages recently. Please wait a little while before trying again.", 'sacred-kompass' ) ] );
    }
    set_transient( $rl_key, $hits + 1, HOUR_IN_SECONDS );

    /* 4 ── Sanitise inputs */
    $fname   = sanitize_text_field( wp_unslash( $_POST['fname']   ?? '' ) );
    $lname   = sanitize_text_field( wp_unslash( $_POST['lname']   ?? '' ) );
    $email   = sanitize_email(      wp_unslash( $_POST['email']   ?? '' ) );
    $service = sanitize_text_field( wp_unslash( $_POST['service'] ?? '' ) );
    $message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

    /* 5 ── Server-side validation */
    if ( empty( $fname ) || empty( $email ) || empty( $service ) || empty( $message ) ) {
        wp_send_json_error( [ 'msg' => __( 'Please fill in all required fields.', 'sacred-kompass' ) ] );
    }
    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'msg' => __( 'Please enter a valid email address.', 'sacred-kompass' ) ] );
    }

    /* 6 ── Check for returning visitor */
    global $wpdb;
    $prior = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}sk_contact_leads WHERE email = %s",
        $email
    ) );

    /* 7 ── Store lead */
    $wpdb->insert(
        $wpdb->prefix . 'sk_contact_leads',
        [
            'fname'      => $fname,
            'lname'      => $lname,
            'email'      => $email,
            'service'    => $service,
            'message'    => $message,
            'ip_hash'    => md5( $ip ),
            'created_at' => current_time( 'mysql' ),
        ],
        [ '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
    );

    /* 8 ── Email notification to site owner */
    $admin_email = sk_option( 'footer_email', get_option( 'admin_email' ) );
    $subject     = sprintf( '[Sacred Kompass] New enquiry from %s %s', $fname, $lname );
    $body        = sprintf(
        "Name: %s %s\nEmail: %s\nService: %s\n\nMessage:\n%s\n\n---\nSubmitted: %s",
        $fname, $lname, $email, $service, $message, current_time( 'mysql' )
    );
    wp_mail( $admin_email, $subject, $body, [ 'Reply-To: ' . $email ] );

    /* 9 ── Personalised success message */
    $success_msg = $prior > 0
        ? __( "Welcome back \u2014 thank you for reaching out again. We'll be in touch shortly.", 'sacred-kompass' )
        : __( 'Your message has been received. We will connect with you soon.', 'sacred-kompass' );

    wp_send_json_success( [ 'msg' => $success_msg ] );
}

/* ── Create leads table on first theme activation ── */
add_action( 'after_switch_theme', 'sk_create_leads_table' );
function sk_create_leads_table(): void {
    global $wpdb;
    $table   = $wpdb->prefix . 'sk_contact_leads';
    $charset = $wpdb->get_charset_collate();
    $sql     = "CREATE TABLE IF NOT EXISTS {$table} (
        id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        fname       VARCHAR(100)    NOT NULL DEFAULT '',
        lname       VARCHAR(100)    NOT NULL DEFAULT '',
        email       VARCHAR(200)    NOT NULL DEFAULT '',
        service     VARCHAR(100)    NOT NULL DEFAULT '',
        message     TEXT            NOT NULL,
        ip_hash     VARCHAR(64)     NOT NULL DEFAULT '',
        created_at  DATETIME        NOT NULL,
        PRIMARY KEY  (id),
        KEY email      (email),
        KEY created_at (created_at)
    ) {$charset};";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
