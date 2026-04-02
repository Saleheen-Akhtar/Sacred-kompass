<?php
/**
 * Philosophy Strip — Faithful port of CircularTestimonials component.
 * 3 stacked images (left/centre/right) with 3D perspective + word-blur animated text.
 */
$pillars = sk_repeater('options_sk_philosophy_pillars_json');
if (empty($pillars)) {
    $pillars = sk_default_pillars();
}

// Assign images to each pillar
$pillar_images = [
    'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&auto=format&fit=crop&q=80',
    'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&auto=format&fit=crop&q=80',
    'https://images.unsplash.com/photo-1470115636492-6d2b56f9146d?w=800&auto=format&fit=crop&q=80',
    'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?w=800&auto=format&fit=crop&q=80',
    'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=800&auto=format&fit=crop&q=80',
];

$pillars_js = [];
foreach ($pillars as $i => $p) {
    $pillars_js[] = [
        'num'   => $p['pillar_num']   ?? '0'.($i+1),
        'title' => $p['pillar_title'] ?? '',
        'desc'  => $p['pillar_desc']  ?? '',
        'src'   => $pillar_images[$i % count($pillar_images)],
    ];
}
?>
<div class="strip strip--circular" aria-label="<?php esc_attr_e('Core Pillars','sacred-kompass'); ?>" id="sk-philosophy-strip">
  <div class="wrap">
    <div class="circular-testimonials" id="sk-circular-testimonials">

      <!-- Images container (left) -->
      <div class="ct-images" id="ct-images">
        <?php foreach ($pillars_js as $i => $p) : ?>
        <img
          class="ct-img"
          src="<?php echo esc_url($p['src']); ?>"
          alt="<?php echo esc_attr($p['title']); ?>"
          data-index="<?php echo $i; ?>"
          loading="lazy"
        />
        <?php endforeach; ?>
      </div>

      <!-- Content (right) -->
      <div class="ct-content">
        <div class="ct-text" id="ct-text">
          <div class="ct-meta">
            <h3 class="ct-name"  id="ct-name"></h3>
            <p  class="ct-desig" id="ct-desig"></p>
          </div>
          <p class="ct-quote" id="ct-quote"></p>
        </div>
        <div class="ct-arrows">
          <button class="ct-btn ct-btn--prev" id="ct-prev" aria-label="<?php esc_attr_e('Previous','sacred-kompass'); ?>">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M12 15L7 10L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <button class="ct-btn ct-btn--next" id="ct-next" aria-label="<?php esc_attr_e('Next','sacred-kompass'); ?>">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M8 5L13 10L8 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
(function(){
  var PILLARS = <?php echo wp_json_encode($pillars_js); ?>;
  var total   = PILLARS.length;
  var active  = 0;
  var animating = false;
  var autoTimer = null;

  var imgEls   = document.querySelectorAll('#ct-images .ct-img');
  var nameEl   = document.getElementById('ct-name');
  var desigEl  = document.getElementById('ct-desig');
  var quoteEl  = document.getElementById('ct-quote');
  var prevBtn  = document.getElementById('ct-prev');
  var nextBtn  = document.getElementById('ct-next');
  var imagesWrap = document.getElementById('ct-images');

  /* ── Calculate gap (matches original calculateGap()) ── */
  function calcGap() {
    var w = imagesWrap ? imagesWrap.offsetWidth : 400;
    var minW=1024, maxW=1456, minG=60, maxG=86;
    if (w <= minW) return minG;
    if (w >= maxW) return Math.max(minG, maxG + 0.06018*(w-maxW));
    return minG + (maxG-minG)*((w-minW)/(maxW-minW));
  }

  /* ── Apply image styles (left/centre/right) ── */
  function applyStyles() {
    var gap      = calcGap();
    var maxStick = gap * 0.8;
    var leftIdx  = (active - 1 + total) % total;
    var rightIdx = (active + 1) % total;

    imgEls.forEach(function(el, idx) {
      var isActive = (idx === active);
      var isLeft   = (idx === leftIdx);
      var isRight  = (idx === rightIdx);

      if (isActive) {
        el.style.cssText = 'z-index:3;opacity:1;pointer-events:auto;transform:translateX(0px) translateY(0px) scale(1) rotateY(0deg);transition:all 0.8s cubic-bezier(.4,2,.3,1);';
      } else if (isLeft) {
        el.style.cssText = 'z-index:2;opacity:1;pointer-events:auto;transform:translateX(-'+gap+'px) translateY(-'+maxStick+'px) scale(0.85) rotateY(15deg);transition:all 0.8s cubic-bezier(.4,2,.3,1);';
      } else if (isRight) {
        el.style.cssText = 'z-index:2;opacity:1;pointer-events:auto;transform:translateX('+gap+'px) translateY(-'+maxStick+'px) scale(0.85) rotateY(-15deg);transition:all 0.8s cubic-bezier(.4,2,.3,1);';
      } else {
        el.style.cssText = 'z-index:1;opacity:0;pointer-events:none;transition:all 0.8s cubic-bezier(.4,2,.3,1);';
      }
    });
  }

  /* ── Word-by-word blur animation (matches original) ── */
  function animateWords(text) {
    quoteEl.innerHTML = '';
    var words = text.split(' ');
    words.forEach(function(word, i) {
      var span = document.createElement('span');
      span.className = 'ct-word';
      span.textContent = word + '\u00A0';
      span.style.cssText = 'display:inline-block;filter:blur(10px);opacity:0;transform:translateY(5px);transition:filter 0.22s ease '+( i*0.025 )+'s, opacity 0.22s ease '+(i*0.025)+'s, transform 0.22s ease '+(i*0.025)+'s;';
      quoteEl.appendChild(span);
      // Trigger animation next frame
      requestAnimationFrame(function(){
        requestAnimationFrame(function(){
          span.style.filter    = 'blur(0px)';
          span.style.opacity   = '1';
          span.style.transform = 'translateY(0)';
        });
      });
    });
  }

  /* ── Update text content with fade ── */
  function updateText(p) {
    var textEl = document.getElementById('ct-text');
    textEl.style.opacity   = '0';
    textEl.style.transform = 'translateY(20px)';
    textEl.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

    setTimeout(function(){
      nameEl.textContent  = p.num + ' — ' + p.title;
      desigEl.textContent = '';
      animateWords(p.desc);

      textEl.style.opacity   = '1';
      textEl.style.transform = 'translateY(0)';
    }, 200);
  }

  /* ── Go to index ── */
  function goTo(idx) {
    if (animating) return;
    idx = ((idx % total) + total) % total;
    if (idx === active) return;
    animating = true;
    active = idx;
    applyStyles();
    updateText(PILLARS[active]);
    setTimeout(function(){ animating = false; }, 850);
  }

  /* ── Init ── */
  function init() {
    applyStyles();
    updateText(PILLARS[active]);
  }

  /* ── Controls ── */
  function startAuto() {
    stopAuto();
    autoTimer = setInterval(function(){ goTo(active+1); }, 5000);
  }
  function stopAuto() {
    if (autoTimer) clearInterval(autoTimer);
  }

  nextBtn.addEventListener('click', function(){ stopAuto(); goTo(active+1); startAuto(); });
  prevBtn.addEventListener('click', function(){ stopAuto(); goTo(active-1); startAuto(); });

  // Click on side images to navigate
  imgEls.forEach(function(el, idx){
    el.addEventListener('click', function(){
      if (idx !== active) { stopAuto(); goTo(idx); startAuto(); }
    });
  });

  // Keyboard
  document.addEventListener('keydown', function(e){
    var strip = document.getElementById('sk-philosophy-strip');
    if (!strip) return;
    var rect = strip.getBoundingClientRect();
    if (rect.bottom < 0 || rect.top > window.innerHeight) return;
    if (e.key==='ArrowLeft')  { stopAuto(); goTo(active-1); startAuto(); }
    if (e.key==='ArrowRight') { stopAuto(); goTo(active+1); startAuto(); }
  });

  window.addEventListener('resize', applyStyles);

  init();
  startAuto();
})();
</script>
