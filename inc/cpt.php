<?php
/**
 * Sacred Kompass v5.3 — Custom Post Types
 * Native meta boxes — no ACF required.
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_post_types', 10);
function sk_register_post_types(): void {
    register_post_type('sk_offering', [
        'labels' => ['name'=>'Offerings','singular_name'=>'Offering','add_new_item'=>'Add New Offering','edit_item'=>'Edit Offering'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => false,
        'supports'        => ['title','thumbnail','page-attributes'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => true,
    ]);
    register_post_type('sk_faq', [
        'labels' => ['name'=>'FAQ','singular_name'=>'FAQ Item','add_new_item'=>'Add New FAQ Item','edit_item'=>'Edit FAQ Item'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => false,
        'supports'        => ['title','page-attributes'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => true,
    ]);
}

/* ── Native meta boxes (no ACF needed) ── */
add_action('add_meta_boxes', 'sk_register_meta_boxes');
function sk_register_meta_boxes(): void {
    add_meta_box('sk_offering_details', 'Offering Details', 'sk_offering_meta_box', 'sk_offering', 'normal', 'high');
    add_meta_box('sk_faq_answer',       'FAQ Answer',       'sk_faq_meta_box',      'sk_faq',      'normal', 'high');
}

function sk_offering_meta_box(WP_Post $post): void {
    wp_nonce_field('sk_offering_save', 'sk_offering_nonce');
    $tag   = get_post_meta($post->ID, 'offering_tag',   true);
    $desc  = get_post_meta($post->ID, 'offering_desc',  true);
    $price = get_post_meta($post->ID, 'offering_price', true);
    echo '<table class="form-table" style="width:100%">';
    echo '<tr><th style="width:160px"><label>Category Tag</label></th><td><input type="text" name="offering_tag" value="'.esc_attr($tag).'" style="width:100%" placeholder="e.g. Personal · Guidance · Corporate" /></td></tr>';
    echo '<tr><th><label>Description</label></th><td><textarea name="offering_desc" rows="4" style="width:100%">'.esc_textarea($desc).'</textarea></td></tr>';
    echo '<tr><th><label>Price (optional)</label></th><td><input type="text" name="offering_price" value="'.esc_attr($price).'" style="width:100%" placeholder="e.g. From SGD 150 — leave blank to hide" /></td></tr>';
    echo '</table>';
}

function sk_faq_meta_box(WP_Post $post): void {
    wp_nonce_field('sk_faq_save', 'sk_faq_nonce');
    $answer = get_post_meta($post->ID, 'faq_answer', true);
    echo '<label style="display:block;font-weight:600;margin-bottom:6px">Answer</label>';
    echo '<textarea name="faq_answer" rows="6" style="width:100%">'.esc_textarea($answer).'</textarea>';
}

add_action('save_post', 'sk_save_meta_boxes');
function sk_save_meta_boxes(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (get_post_type($post_id) === 'sk_offering') {
        if (!isset($_POST['sk_offering_nonce']) || !wp_verify_nonce($_POST['sk_offering_nonce'], 'sk_offering_save')) return;
        update_post_meta($post_id, 'offering_tag',   sanitize_text_field($_POST['offering_tag']   ?? ''));
        update_post_meta($post_id, 'offering_desc',  sanitize_textarea_field($_POST['offering_desc']  ?? ''));
        update_post_meta($post_id, 'offering_price', sanitize_text_field($_POST['offering_price'] ?? ''));
    }

    if (get_post_type($post_id) === 'sk_faq') {
        if (!isset($_POST['sk_faq_nonce']) || !wp_verify_nonce($_POST['sk_faq_nonce'], 'sk_faq_save')) return;
        update_post_meta($post_id, 'faq_answer', sanitize_textarea_field($_POST['faq_answer'] ?? ''));
    }
}

/* ══════════════════════════════════════════════════════════
   TEAM / FOUNDERS — sk_team CPT
   Client can Add / Remove / Reorder via wp-admin › Team Members
   ══════════════════════════════════════════════════════════ */
add_action('init', 'sk_register_team_cpt', 10);
function sk_register_team_cpt(): void {
    register_post_type('sk_team', [
        'labels' => [
            'name'          => 'Team Members',
            'singular_name' => 'Team Member',
            'add_new_item'  => 'Add New Team Member',
            'edit_item'     => 'Edit Team Member',
            'menu_name'     => 'Team Members',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'sacred-kompass-settings',   // appears under Sacred Kompass menu
        'supports'        => ['title', 'thumbnail', 'page-attributes'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => true,
        'menu_icon'       => 'dashicons-groups',
    ]);
}

/* ── Team member meta boxes ── */
add_action('add_meta_boxes', 'sk_register_team_meta_boxes');
function sk_register_team_meta_boxes(): void {
    add_meta_box(
        'sk_team_details',
        '★ Team Member Details',
        'sk_team_meta_box_cb',
        'sk_team',
        'normal',
        'high'
    );
    add_meta_box(
        'sk_team_image_url',
        '★ Portrait Photo (URL or Upload)',
        'sk_team_image_meta_box_cb',
        'sk_team',
        'side',
        'high'
    );
}

function sk_team_image_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_team_image_save', 'sk_team_image_nonce');
    $img = get_post_meta($post->ID, 'team_image', true);
    $thumb_id = get_post_thumbnail_id($post->ID);
    echo '<p style="font-size:12px;color:#666;margin-top:0">You can either paste a URL below <em>or</em> use the Featured Image box to upload directly.</p>';
    echo '<label style="font-weight:600;display:block;margin-bottom:4px">Image URL</label>';
    echo '<input type="text" name="team_image" id="sk_team_image_url" value="' . esc_attr($img) . '" placeholder="https://... or drag &amp; drop below" style="width:100%;box-sizing:border-box;margin-bottom:8px" />';
    echo '<div id="sk-team-dropzone" style="border:2px dashed #c49a2a;border-radius:8px;padding:20px;text-align:center;cursor:pointer;transition:background .2s;font-size:12px;color:#888" ';
    echo 'ondragover="event.preventDefault();this.style.background=\'rgba(196,154,42,.08)\'" ';
    echo 'ondragleave="this.style.background=\'\'" ';
    echo 'ondrop="(function(e){e.preventDefault();var f=e.dataTransfer.files[0];if(!f||!f.type.startsWith(\'image/\'))return;var r=new FileReader();r.onload=function(ev){document.getElementById(\'sk_team_image_url\').value=ev.target.result;document.getElementById(\'sk-team-dropzone-preview\').src=ev.target.result;document.getElementById(\'sk-team-dropzone-preview\').style.display=\'block\'};r.readAsDataURL(f)})(event)">';
    echo 'Drop image here to preview<br><small>(Upload via Featured Image for permanent storage)</small>';
    echo '</div>';
    if ($img || $thumb_id) {
        $preview_src = $img ?: wp_get_attachment_image_url($thumb_id, 'thumbnail');
        echo '<img id="sk-team-dropzone-preview" src="' . esc_url($preview_src) . '" style="width:100%;margin-top:8px;border-radius:6px;object-fit:cover;aspect-ratio:3/4" />';
    } else {
        echo '<img id="sk-team-dropzone-preview" src="" style="display:none;width:100%;margin-top:8px;border-radius:6px;object-fit:cover;aspect-ratio:3/4" />';
    }
    echo '<p style="font-size:11px;color:#888;margin-top:8px">Min 520×700px portrait recommended. Order members with the <strong>Order</strong> field (Page Attributes box).</p>';
}

function sk_team_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_team_save', 'sk_team_nonce');
    $first  = get_post_meta($post->ID, 'team_first_name', true);
    $last   = get_post_meta($post->ID, 'team_last_name',  true);
    $origin = get_post_meta($post->ID, 'team_origin',     true);
    $role   = get_post_meta($post->ID, 'team_role',       true);
    $bio    = get_post_meta($post->ID, 'team_bio',        true);
    $tags   = get_post_meta($post->ID, 'team_tags',       true);

    echo '<table class="form-table" style="width:100%">';
    echo '<tr><th style="width:160px"><label>First Name</label></th><td><input type="text" name="team_first_name" value="' . esc_attr($first) . '" style="width:100%" /></td></tr>';
    echo '<tr><th><label>Last Name</label></th><td><input type="text" name="team_last_name" value="' . esc_attr($last) . '" style="width:100%" /></td></tr>';
    echo '<tr><th><label>Origin / Country</label></th><td><input type="text" name="team_origin" value="' . esc_attr($origin) . '" placeholder="e.g. Singapore" style="width:100%" /></td></tr>';
    echo '<tr><th><label>Role / Title</label></th><td><input type="text" name="team_role" value="' . esc_attr($role) . '" placeholder="e.g. Founder and Lead Guide" style="width:100%" /></td></tr>';
    echo '<tr><th><label>Bio</label></th><td><textarea name="team_bio" rows="5" style="width:100%">' . esc_textarea($bio) . '</textarea></td></tr>';
    echo '<tr><th><label>Expertise Tags<br><small style="font-weight:300">(one per line)</small></label></th><td><textarea name="team_tags" rows="4" style="width:100%" placeholder="Vedic Philosophy&#10;Jyotish Astrology&#10;Coaching">' . esc_textarea($tags) . '</textarea></td></tr>';
    echo '</table>';
    echo '<p style="font-size:12px;color:#666;margin-top:12px">💡 <strong>Tip:</strong> Use <em>Page Attributes → Order</em> (right sidebar) to control display order. Lower number = displayed first (big card).</p>';
}

add_action('save_post_sk_team', 'sk_save_team_meta');
function sk_save_team_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_team_nonce']) || !wp_verify_nonce($_POST['sk_team_nonce'], 'sk_team_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['team_first_name','team_last_name','team_origin','team_role'];
    foreach ($fields as $f) {
        update_post_meta($post_id, $f, sanitize_text_field($_POST[$f] ?? ''));
    }
    update_post_meta($post_id, 'team_bio',  sanitize_textarea_field($_POST['team_bio']  ?? ''));
    update_post_meta($post_id, 'team_tags', sanitize_textarea_field($_POST['team_tags'] ?? ''));

    // Image URL (only if set via URL field, not drag-drop base64 — those are preview-only)
    if (!empty($_POST['team_image']) && str_starts_with($_POST['team_image'], 'http')) {
        update_post_meta($post_id, 'team_image', esc_url_raw($_POST['team_image']));
    }
}

/* Helper so founders.php can get thumbnail URL */
if (!function_exists('get_post_thumbnail_url')) {
    function get_post_thumbnail_url(int $post_id, string $size = 'large'): string {
        $id = get_post_thumbnail_id($post_id);
        return $id ? (wp_get_attachment_image_url($id, $size) ?: '') : '';
    }
}
