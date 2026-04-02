<?php
/**
 * Offerings — Faithful port of 3D InfiniteGallery (Three.js CDN, cloth shader, depth fade/blur).
 */
$offerings_query = new WP_Query([
    'post_type'      => 'sk_offering',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);

$offerings_data = [];
$idx = 0;
if ($offerings_query->have_posts()) :
    while ($offerings_query->have_posts()) : $offerings_query->the_post();
        $pid = get_the_ID();
        $offerings_data[] = [
            'index' => $idx,
            'num'   => str_pad($idx + 1, 2, '0', STR_PAD_LEFT),
            'title' => get_the_title(),
            'tag'   => get_post_meta($pid, 'offering_tag',   true),
            'desc'  => get_post_meta($pid, 'offering_desc',  true),
            'price' => get_post_meta($pid, 'offering_price', true),
            'img'   => get_the_post_thumbnail_url(null, 'medium_large') ?: '',
            'link'  => home_url('/#contact'),
        ];
        $idx++;
    endwhile;
    wp_reset_postdata();
endif;

// Gallery images: atmospheric mood + offering images interleaved
$mood_images = [
    ['src'=>'https://images.unsplash.com/photo-1741332966416-414d8a5b8887?w=600&auto=format&fit=crop&q=60','offering_index'=>-1],
    ['src'=>'https://images.unsplash.com/photo-1754769440490-2eb64d715775?q=80&w=1113&auto=format&fit=crop','offering_index'=>-1],
    ['src'=>'https://images.unsplash.com/photo-1758640920659-0bb864175983?w=600&auto=format&fit=crop&q=60','offering_index'=>-1],
    ['src'=>'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&auto=format&fit=crop&q=60','offering_index'=>-1],
    ['src'=>'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=600&auto=format&fit=crop&q=60','offering_index'=>-1],
    ['src'=>'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?w=600&auto=format&fit=crop&q=60','offering_index'=>-1],
    ['src'=>'https://images.unsplash.com/photo-1470115636492-6d2b56f9146d?w=600&auto=format&fit=crop&q=60','offering_index'=>-1],
    ['src'=>'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=600&auto=format&fit=crop&q=60','offering_index'=>-1],
];

$gallery_images = $mood_images;
foreach ($offerings_data as $o) {
    if (!empty($o['img'])) {
        $gallery_images[] = ['src' => $o['img'], 'offering_index' => $o['index']];
    }
}
// Shuffle so offering images appear scattered among mood images
shuffle($gallery_images);
// Re-index
$gallery_images = array_values($gallery_images);
?>

<section class="offerings-section" id="offerings" aria-labelledby="offerings-heading">
  <div class="wrap">
    <div class="offerings-header">
      <div class="eyebrow eyebrow-c reveal"><?php esc_html_e('What We Offer','sacred-kompass'); ?></div>
      <h2 class="display-h2 reveal d1" id="offerings-heading">
        <?php esc_html_e('Pathways of','sacred-kompass'); ?> <em><?php esc_html_e('Guidance','sacred-kompass'); ?></em>
      </h2>
      <p class="body-serif reveal d2"><?php esc_html_e('Each pathway is an invitation, not a prescription. We meet you exactly where you are, and walk with you from there.','sacred-kompass'); ?></p>
    </div>
  </div>

  <!-- 3D Gallery -->
  <div class="offerings-gallery-wrap" id="sk-offerings-gallery">
    <div id="sk-three-mount" style="width:100%;height:100%"></div>

    <!-- Fallback -->
    <div class="offerings-gallery-fallback" id="sk-gallery-fallback" style="display:none">
      <div class="wrap">
        <div class="offerings-grid">
          <?php foreach ($offerings_data as $o) : ?>
          <div class="offering-card reveal">
            <?php if ($o['img']) : ?><div class="offering-img"><img src="<?php echo esc_url($o['img']); ?>" alt="<?php echo esc_attr($o['title']); ?>" loading="lazy" /></div><?php endif; ?>
            <span class="offering-num"><?php echo esc_html($o['num']); ?></span>
            <?php if ($o['tag'])   : ?><span class="offering-tag"><?php echo esc_html($o['tag']); ?></span><?php endif; ?>
            <h3 class="offering-title"><?php echo esc_html($o['title']); ?></h3>
            <?php if ($o['desc'])  : ?><p class="offering-desc"><?php echo esc_html($o['desc']); ?></p><?php endif; ?>
            <?php if ($o['price']) : ?><span class="offering-price"><?php echo esc_html($o['price']); ?></span><?php endif; ?>
            <a class="offering-link" href="<?php echo esc_url($o['link']); ?>"><?php esc_html_e('Enquire','sacred-kompass'); ?></a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Side drawer -->
    <aside class="offerings-drawer" id="sk-offerings-drawer" aria-hidden="true">
      <button class="offerings-drawer__close" id="sk-drawer-close" aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M14 4L4 14M4 4L14 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
      <div class="offerings-drawer__inner">
        <div class="offerings-drawer__media" id="sk-drawer-media" style="display:none">
          <img id="sk-drawer-img" src="" alt="" />
        </div>
        <span class="offerings-drawer__num"  id="sk-drawer-num"></span>
        <span class="offerings-drawer__tag"  id="sk-drawer-tag"></span>
        <h3  class="offerings-drawer__title" id="sk-drawer-title"></h3>
        <p   class="offerings-drawer__desc"  id="sk-drawer-desc"></p>
        <span class="offerings-drawer__price" id="sk-drawer-price"></span>
        <a class="btn btn-primary offerings-drawer__cta" id="sk-drawer-link" href="<?php echo esc_url(home_url('/#contact')); ?>">
          <?php esc_html_e('Enquire Now','sacred-kompass'); ?>
        </a>
      </div>
      <?php if (!empty($offerings_data)) : ?>
      <nav class="offerings-drawer__list">
        <p class="offerings-drawer__list-label"><?php esc_html_e('All Pathways','sacred-kompass'); ?></p>
        <?php foreach ($offerings_data as $o) : ?>
        <button class="offerings-drawer__list-item" data-offering-index="<?php echo esc_attr($o['index']); ?>">
          <span class="offerings-drawer__list-num"><?php echo esc_html($o['num']); ?></span>
          <span class="offerings-drawer__list-title"><?php echo esc_html($o['title']); ?></span>
          <?php if ($o['tag']) : ?><span class="offerings-drawer__list-tag"><?php echo esc_html($o['tag']); ?></span><?php endif; ?>
        </button>
        <?php endforeach; ?>
      </nav>
      <?php endif; ?>
    </aside>

    <div class="offerings-gallery-hint" id="sk-gallery-hint">
      <span><?php esc_html_e('Scroll to explore · Click an offering to discover','sacred-kompass'); ?></span>
    </div>
    <div class="offerings-end-cta" id="sk-offerings-end-cta" aria-hidden="true">
      <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-primary">
        <?php esc_html_e('Book a Free Discovery Call','sacred-kompass'); ?>
      </a>
    </div>
  </div>

  <div class="wrap">
    <div class="offerings-cta reveal">
      <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="btn btn-primary">
        <?php esc_html_e('Book a Free Discovery Call','sacred-kompass'); ?>
      </a>
    </div>
  </div>
</section>

<!-- Three.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
(function(){
  'use strict';

  var OFFERINGS      = <?php echo wp_json_encode($offerings_data); ?>;
  var GALLERY_IMAGES = <?php echo wp_json_encode($gallery_images); ?>;

  /* ── Drawer ── */
  var drawer    = document.getElementById('sk-offerings-drawer');
  var closeBtn  = document.getElementById('sk-drawer-close');
  var hint      = document.getElementById('sk-gallery-hint');
  var drawerOpen = false;

  function openDrawer(idx) {
    var o = OFFERINGS[idx];
    if (!o) return;
    var mediaEl = document.getElementById('sk-drawer-media');
    var imgEl = document.getElementById('sk-drawer-img');
    document.getElementById('sk-drawer-num').textContent   = o.num   || '';
    document.getElementById('sk-drawer-tag').textContent   = o.tag   || '';
    document.getElementById('sk-drawer-tag').style.display = o.tag ? '' : 'none';
    document.getElementById('sk-drawer-title').textContent = o.title || '';
    document.getElementById('sk-drawer-desc').textContent  = o.desc  || '';
    var priceEl = document.getElementById('sk-drawer-price');
    priceEl.textContent   = o.price || '';
    priceEl.style.display = o.price ? '' : 'none';
    if (imgEl && mediaEl) {
      if (o.img) {
        imgEl.src = o.img;
        imgEl.alt = o.title || '';
        mediaEl.style.display = '';
      } else {
        mediaEl.style.display = 'none';
      }
    }
    document.getElementById('sk-drawer-link').href = o.link || '#contact';
    document.querySelectorAll('.offerings-drawer__list-item').forEach(function(b){
      b.classList.toggle('is-active', parseInt(b.dataset.offeringIndex,10) === idx);
    });
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden','false');
    drawerOpen = true;
  }
  function closeDrawer() {
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden','true');
    drawerOpen = false;
  }
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  document.querySelectorAll('.offerings-drawer__list-item').forEach(function(b){
    b.addEventListener('click', function(){ openDrawer(parseInt(this.dataset.offeringIndex,10)); });
  });
  document.addEventListener('keydown', function(e){ if(e.key==='Escape'&&drawerOpen) closeDrawer(); });

  /* ── WebGL check ── */
  function hasWebGL(){
    try { var c=document.createElement('canvas'); return !!(c.getContext('webgl')||c.getContext('experimental-webgl')); }catch(e){return false;}
  }
  if (!hasWebGL() || typeof THREE === 'undefined') {
    document.getElementById('sk-three-mount').style.display='none';
    document.getElementById('sk-gallery-fallback').style.display='';
    if (hint) hint.style.display='none';
    return;
  }

  /* ── Three.js setup ── */
  var mount    = document.getElementById('sk-three-mount');
  var W = mount.offsetWidth, H = mount.offsetHeight;

  var renderer = new THREE.WebGLRenderer({ antialias:true, alpha:true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio,2));
  renderer.setSize(W, H);
  renderer.setClearColor(0xF6F0E7, 1); // --ivory-warm
  mount.appendChild(renderer.domElement);

  var scene  = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(55, W/H, 0.1, 200);
  camera.position.set(0, 0, 0);

  window.addEventListener('resize', function(){
    W = mount.offsetWidth; H = mount.offsetHeight;
    camera.aspect = W/H;
    camera.updateProjectionMatrix();
    renderer.setSize(W, H);
  });

  /* ── Cloth ShaderMaterial (faithful to original) ── */
  var clothVS = [
    'uniform float scrollForce;',
    'uniform float time;',
    'uniform float isHovered;',
    'varying vec2 vUv;',
    'void main() {',
    '  vUv = uv;',
    '  vec3 pos = position;',
    '  float curveIntensity = scrollForce * 0.3;',
    '  float distanceFromCenter = length(pos.xy);',
    '  float curve = distanceFromCenter * distanceFromCenter * curveIntensity;',
    '  float ripple1 = sin(pos.x * 2.0 + scrollForce * 3.0) * 0.02;',
    '  float ripple2 = sin(pos.y * 2.5 + scrollForce * 2.0) * 0.015;',
    '  float clothEffect = (ripple1 + ripple2) * abs(curveIntensity) * 2.0;',
    '  float flagWave = 0.0;',
    '  if (isHovered > 0.5) {',
    '    float wavePhase = pos.x * 3.0 + time * 8.0;',
    '    float dampening = smoothstep(-0.5, 0.5, pos.x);',
    '    flagWave = sin(wavePhase) * 0.1 * dampening;',
    '    flagWave += sin(pos.x * 5.0 + time * 12.0) * 0.03 * dampening;',
    '  }',
    '  pos.z -= (curve + clothEffect + flagWave);',
    '  gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);',
    '}'
  ].join('\n');

  var clothFS = [
    'uniform sampler2D map;',
    'uniform float opacity;',
    'uniform float blurAmount;',
    'uniform float scrollForce;',
    'varying vec2 vUv;',
    'void main() {',
    '  vec4 color = texture2D(map, vUv);',
    '  if (blurAmount > 0.0) {',
    '    vec4 blurred = vec4(0.0);',
    '    float total = 0.0;',
    '    for (float x = -2.0; x <= 2.0; x += 1.0) {',
    '      for (float y = -2.0; y <= 2.0; y += 1.0) {',
    '        vec2 offset = vec2(x, y) * (blurAmount * 0.004);',
    '        float weight = 1.0 / (1.0 + length(vec2(x, y)));',
    '        blurred += texture2D(map, vUv + offset) * weight;',
    '        total += weight;',
    '      }',
    '    }',
    '    color = blurred / total;',
    '  }',
    '  float curveHighlight = abs(scrollForce) * 0.05;',
    '  color.rgb += vec3(curveHighlight * 0.1);',
    '  gl_FragColor = vec4(color.rgb, color.a * opacity);',
    '}'
  ].join('\n');

  function createClothMaterial() {
    return new THREE.ShaderMaterial({
      transparent: true,
      uniforms: {
        map:         { value: null },
        opacity:     { value: 1.0 },
        blurAmount:  { value: 0.0 },
        scrollForce: { value: 0.0 },
        time:        { value: 0.0 },
        isHovered:   { value: 0.0 },
      },
      vertexShader:   clothVS,
      fragmentShader: clothFS,
    });
  }

  /* ── Gallery config (finite cinematic track) ── */
  var images       = GALLERY_IMAGES;
  var totalImages  = images.length;
  var itemSpacing  = 7.0;
  var leadOffset   = 2.1;
  var scrollPos    = 0;
  var maxScrollPos = Math.max(totalImages - 1, 0) + 1.25; // last stretch reserved for CTA reveal

  var fadeIn  = { start: 0.05, end: 0.25 };
  var fadeOut = { start: 0.66, end: 0.86 };
  var blurIn  = { start: 0.00, end: 0.10 };
  var blurOut = { start: 0.66, end: 0.86 };
  var maxBlur = 8.0;

  // Spatial offsets — golden angle distribution
  var MAX_H = 7.4, MAX_V = 6.8;
  var spatialX = [], spatialY = [];
  for (var si = 0; si < totalImages; si++) {
    var ha = (si * 2.618) % (Math.PI * 2);
    var va = (si * 1.618 + Math.PI / 3) % (Math.PI * 2);
    var hr = (si % 3) * 1.08;
    var vr = ((si + 1) % 4) * 0.72;
    spatialX.push(Math.sin(ha) * hr * MAX_H / 3);
    spatialY.push(Math.cos(va) * vr * MAX_V / 4);
  }

  /* ── Planes ── */
  var materials = [];
  var meshes    = [];
  var planeData = [];
  var loader    = new THREE.TextureLoader();
  var texCache  = {};

  function loadTex(src, cb) {
    if (texCache[src]) { if(cb) cb(texCache[src]); return texCache[src]; }
    loader.setCrossOrigin('anonymous');
    loader.load(src, function(t){
      t.minFilter = THREE.LinearMipMapLinearFilter;
      texCache[src] = t;
      if(cb) cb(t);
    });
    return null;
  }

  // Pre-load all textures
  images.forEach(function(img){ loadTex(img.src); });

  var geo = new THREE.PlaneGeometry(1, 1, 32, 32);

  for (var pi = 0; pi < totalImages; pi++) {
    var mat  = createClothMaterial();
    var mesh = new THREE.Mesh(geo, mat);
    scene.add(mesh);
    materials.push(mat);
    meshes.push(mesh);
    planeData.push({
      imageIndex: pi,
      x:          spatialX[pi],
      y:          spatialY[pi],
    });
  }

  /* ── Scroll / interaction ── */
  var scrollVelocity = 0;
  var autoPlay       = true;
  var lastInteract   = Date.now();
  var hoveredIndex   = -1; // plane index (not offering index)
  var endCta = document.getElementById('sk-offerings-end-cta');

  var mountEl = mount;

  mountEl.addEventListener('wheel', function(e){
    var tryingDown = e.deltaY > 0;
    var tryingUp = e.deltaY < 0;
    var atEnd = scrollPos >= maxScrollPos - 0.02;
    var atStart = scrollPos <= 0.02;
    if ((tryingDown && atEnd) || (tryingUp && atStart)) return; // allow normal page scroll
    e.preventDefault();
    scrollVelocity += e.deltaY * 0.012;
    autoPlay = false;
    lastInteract = Date.now();
    if (hint) hint.classList.add('is-hidden');
  }, { passive: false });

  document.addEventListener('keydown', function(e){
    var rect = mountEl.getBoundingClientRect();
    if (rect.bottom < 0 || rect.top > window.innerHeight) return;
    if (e.key==='ArrowUp'||e.key==='ArrowLeft')    { scrollVelocity -= 2; autoPlay=false; lastInteract=Date.now(); }
    if (e.key==='ArrowDown'||e.key==='ArrowRight') { scrollVelocity += 2; autoPlay=false; lastInteract=Date.now(); }
  });

  // Touch scroll
  var touchStartY = 0;
  mountEl.addEventListener('touchstart', function(e){ touchStartY = e.touches[0].clientY; }, {passive:true});
  mountEl.addEventListener('touchmove',  function(e){
    var dy = touchStartY - e.touches[0].clientY;
    scrollVelocity += dy * 0.008;
    touchStartY = e.touches[0].clientY;
    autoPlay = false; lastInteract = Date.now();
  }, {passive:true});

  /* ── Raycaster for hover/click on offering planes ── */
  var raycaster = new THREE.Raycaster();
  var mouse     = new THREE.Vector2();

  function updateMouse(clientX, clientY) {
    var rect = renderer.domElement.getBoundingClientRect();
    mouse.x =  ((clientX - rect.left)  / rect.width)  * 2 - 1;
    mouse.y = -((clientY - rect.top)   / rect.height)  * 2 + 1;
  }

  renderer.domElement.addEventListener('mousemove', function(e){
    updateMouse(e.clientX, e.clientY);
    raycaster.setFromCamera(mouse, camera);
    var hits = raycaster.intersectObjects(meshes);
    if (hits.length) {
      var hitMesh = hits[0].object;
      var idx = meshes.indexOf(hitMesh);
      hoveredIndex = idx;
      var gi = images[planeData[idx].imageIndex];
      renderer.domElement.style.cursor = (gi && gi.offering_index >= 0) ? 'pointer' : 'default';
    } else {
      hoveredIndex = -1;
      renderer.domElement.style.cursor = 'default';
    }
  });

  renderer.domElement.addEventListener('click', function(e){
    updateMouse(e.clientX, e.clientY);
    raycaster.setFromCamera(mouse, camera);
    var hits = raycaster.intersectObjects(meshes);
    if (hits.length) {
      var idx = meshes.indexOf(hits[0].object);
      var gi  = images[planeData[idx].imageIndex];
      if (gi && gi.offering_index >= 0) {
        openDrawer(gi.offering_index);
      }
    } else if (drawerOpen) {
      closeDrawer();
    }
  });

  /* ── Animate ── */
  var clock = new THREE.Clock();

  function animate() {
    requestAnimationFrame(animate);
    var delta = clock.getDelta();
    var elapsed = clock.getElapsedTime();

    // Auto-play resume
    if (Date.now() - lastInteract > 3000) autoPlay = true;
    if (autoPlay && scrollPos < maxScrollPos - 0.06) scrollVelocity += 0.28 * delta;

    // Damping
    scrollVelocity *= 0.92;
    scrollPos += scrollVelocity * delta * 7.2;
    if (scrollPos < 0) { scrollPos = 0; scrollVelocity = 0; }
    if (scrollPos > maxScrollPos) { scrollPos = maxScrollPos; scrollVelocity = 0; }

    planeData.forEach(function(p, i) {
      var rel = i - scrollPos;
      var worldZ = -((rel + leadOffset) * itemSpacing);
      var normPos = Math.max(0, Math.min(1, ((worldZ + 40) / 80)));

      // Opacity (matches original fadeSettings)
      var opacity = 1;
      if      (normPos < fadeIn.start)  opacity = 0;
      else if (normPos < fadeIn.end)    opacity = (normPos - fadeIn.start) / (fadeIn.end - fadeIn.start);
      else if (normPos > fadeOut.end)   opacity = 0;
      else if (normPos > fadeOut.start) opacity = 1 - (normPos - fadeOut.start) / (fadeOut.end - fadeOut.start);
      opacity = Math.max(0, Math.min(1, opacity));

      // Blur (matches original blurSettings)
      var blur = 0;
      if      (normPos < blurIn.start)  blur = maxBlur;
      else if (normPos < blurIn.end)    blur = maxBlur * (1 - (normPos - blurIn.start) / (blurIn.end - blurIn.start));
      else if (normPos > blurOut.end)   blur = maxBlur;
      else if (normPos > blurOut.start) blur = maxBlur * (normPos - blurOut.start) / (blurOut.end - blurOut.start);
      blur = Math.max(0, Math.min(maxBlur, blur));

      var mat = materials[i];
      mat.uniforms.time.value        = elapsed;
      mat.uniforms.scrollForce.value = scrollVelocity;
      mat.uniforms.opacity.value     = opacity;
      mat.uniforms.blurAmount.value  = blur;
      mat.uniforms.isHovered.value   = (hoveredIndex === i) ? 1.0 : 0.0;

      // Set texture
      var imgSrc = images[p.imageIndex] ? images[p.imageIndex].src : '';
      if (texCache[imgSrc]) {
        mat.uniforms.map.value = texCache[imgSrc];
        // Scale to maintain aspect ratio
        var img = texCache[imgSrc].image;
        if (img) {
          var aspect = img.width / img.height;
          meshes[i].scale.set(aspect > 1 ? 2 * aspect : 2, aspect > 1 ? 2 : 2 / aspect, 1);
        }
      } else {
        loadTex(imgSrc); // trigger load
      }

      meshes[i].position.set(p.x, p.y, worldZ);
    });

    if (endCta) {
      var endProgress = Math.max(0, Math.min(1, (scrollPos - (maxScrollPos - 0.9)) / 0.9));
      endCta.style.opacity = String(endProgress);
      endCta.style.transform = 'translate(-50%,' + (20 - endProgress * 20) + 'px)';
      endCta.setAttribute('aria-hidden', endProgress < 0.35 ? 'true' : 'false');
    }

    renderer.render(scene, camera);
  }
  animate();

})();
</script>
