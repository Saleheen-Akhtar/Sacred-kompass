/* Sacred Kompass — main.js v7.1 */
(function () {
  'use strict';

  /* ── Scroll progress bar ──────────────────── */
  const progressBar = document.createElement('div');
  progressBar.className = 'sk-progress';
  document.body.prepend(progressBar);

  /* ── NAV: hide on scroll down, show on scroll up ── */
  const nav = document.getElementById('sk-nav');
  let lastScrollY = window.scrollY;
  let scrollDir   = 'up';
  let ticking     = false;

  /* ── Hero parallax removed — image is position:absolute fixed ── */

  function onScrollTick() {
    const scrollY = window.scrollY;
    const docH    = document.documentElement.scrollHeight - window.innerHeight;

    // Progress bar
    progressBar.style.width = (docH > 0 ? (scrollY / docH) * 100 : 0) + '%';

    // Nav show/hide
    if (nav) {
      const dir = scrollY > lastScrollY ? 'down' : 'up';
      if (dir !== scrollDir || scrollY <= 80) {
        scrollDir = dir;
        if (scrollY <= 80) {
          nav.classList.remove('nav--hidden');
          nav.classList.add('nav--top');
        } else if (dir === 'down') {
          nav.classList.add('nav--hidden');
          nav.classList.remove('nav--top');
        } else {
          nav.classList.remove('nav--hidden');
          nav.classList.remove('nav--top');
        }
      }
      nav.classList.toggle('scrolled', scrollY > 60);
    }

    lastScrollY = scrollY;
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) { requestAnimationFrame(onScrollTick); ticking = true; }
  }, { passive: true });
  onScrollTick();

  /* ── Hero journey stage animations ───────── */
  document.querySelectorAll('.journey-stage').forEach((el, i) => {
    setTimeout(() => el.classList.add('visible'), i * 200 + 150);
  });

  /* ── Scroll reveal (IntersectionObserver) ── */
  const revealSelectors = '.reveal, .reveal-left, .reveal-right, .reveal-scale, .section-enter, .stagger-children';
  const revealEls = document.querySelectorAll(revealSelectors);

  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          const delay = parseFloat(e.target.dataset.delay || 0);
          if (delay) {
            setTimeout(() => e.target.classList.add('visible'), delay * 1000);
          } else {
            e.target.classList.add('visible');
          }
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.07, rootMargin: '0px 0px -48px 0px' });
    revealEls.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add('visible'));
  }

  /* ── Mobile hamburger ─────────────────────── */
  const hamburger = document.getElementById('sk-hamburger');
  const overlay   = document.getElementById('sk-mobile-menu');
  const closeBtn  = document.getElementById('sk-mobile-close');

  function closeMenu() {
    hamburger && hamburger.classList.remove('open');
    overlay   && overlay.classList.remove('open');
    if (hamburger) hamburger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  if (hamburger && overlay) {
    hamburger.addEventListener('click', () => {
      const isOpen = hamburger.classList.toggle('open');
      overlay.classList.toggle('open', isOpen);
      hamburger.setAttribute('aria-expanded', String(isOpen));
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    overlay.querySelectorAll('a').forEach((a) => a.addEventListener('click', closeMenu));
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeMenu();
        closeAllModals();
      }
    });
    // Tap backdrop to close
    overlay.addEventListener('click', (e) => { if (e.target === overlay) closeMenu(); });
  }

  /* ── FAQ accordion ────────────────────────── */
  document.querySelectorAll('.faq-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const isOpen    = trigger.getAttribute('aria-expanded') === 'true';
      const controlId = trigger.getAttribute('aria-controls');
      const body      = document.getElementById(controlId);
      document.querySelectorAll('.faq-trigger').forEach((t) => {
        t.setAttribute('aria-expanded', 'false');
        const b = document.getElementById(t.getAttribute('aria-controls'));
        if (b) b.classList.remove('open');
      });
      if (!isOpen && body) {
        trigger.setAttribute('aria-expanded', 'true');
        body.classList.add('open');
      }
    });
  });

  /* ── Smooth anchor scroll ─────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener('click', (e) => {
      const id = a.getAttribute('href').slice(1);
      if (!id) return;
      const el = document.getElementById(id);
      if (!el) return;
      e.preventDefault();
      const navH  = nav ? nav.offsetHeight + 20 : 80;
      const top   = el.getBoundingClientRect().top + window.scrollY - navH;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });

  /* ── Active nav link ──────────────────────── */
  const sections     = ['about','offerings','founders','faq','contact'];
  const navLinksAll  = document.querySelectorAll('.nav-links a, .nav-mobile-overlay a');

  window.addEventListener('scroll', () => {
    const scrollMid = window.scrollY + window.innerHeight / 2;
    let active = '';
    sections.forEach((id) => {
      const el = document.getElementById(id);
      if (el && el.offsetTop <= scrollMid) active = id;
    });
    navLinksAll.forEach((a) => {
      const href = a.getAttribute('href') || '';
      a.classList.toggle('active', href === `#${active}` || href.endsWith(`/#${active}`));
    });
  }, { passive: true });

  /* ── Founder Modals ───────────────────────── */
  const modals = document.querySelectorAll('.sk-founder-modal');

  function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.removeAttribute('hidden');
    document.body.style.overflow = 'hidden';
    // Focus the close button for a11y
    const closeEl = modal.querySelector('.sk-founder-modal-close');
    if (closeEl) closeEl.focus();
  }

  function closeModal(modal) {
    modal.setAttribute('hidden', '');
    document.body.style.overflow = '';
  }

  function closeAllModals() {
    modals.forEach(m => m.setAttribute('hidden', ''));
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.sk-founder-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const id = trigger.dataset.modal;
      if (id) openModal(id);
    });
  });

  modals.forEach((modal) => {
    // Close button
    const closeEl = modal.querySelector('.sk-founder-modal-close');
    if (closeEl) closeEl.addEventListener('click', () => closeModal(modal));

    // Backdrop click
    const backdrop = modal.querySelector('.sk-founder-modal-backdrop');
    if (backdrop) backdrop.addEventListener('click', () => closeModal(modal));
  });

})();

/* ── v7.2: Contact form validation ──────────────────────── */
(function () {
  const form = document.querySelector('.sk-contact-fallback-form');
  if (!form) return;

  function showError(field, msg) {
    field.classList.add('sk-invalid');
    field.classList.remove('sk-valid');
    let err = field.parentElement.querySelector('.sk-field-error');
    if (!err) { err = document.createElement('span'); err.className = 'sk-field-error'; field.parentElement.appendChild(err); }
    err.textContent = msg;
  }
  function clearError(field) {
    field.classList.remove('sk-invalid');
    const err = field.parentElement.querySelector('.sk-field-error');
    if (err) err.textContent = '';
  }
  function markValid(field) {
    field.classList.remove('sk-invalid');
    field.classList.add('sk-valid');
    const err = field.parentElement.querySelector('.sk-field-error');
    if (err) err.textContent = '';
  }

  function validateField(field) {
    const val = field.value.trim();
    const name = field.name || field.id;

    if (field.required && !val) {
      showError(field, 'This field is required.'); return false;
    }
    if (field.type === 'email' && val) {
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
        showError(field, 'Please enter a valid email address.'); return false;
      }
    }
    if ((name === 'fname' || name === 'lname') && val && val.length < 2) {
      showError(field, 'Must be at least 2 characters.'); return false;
    }
    if (name === 'message' && val && val.length < 10) {
      showError(field, 'Please write at least 10 characters.'); return false;
    }
    if (field.tagName === 'SELECT' && !val) {
      showError(field, 'Please select an option.'); return false;
    }
    markValid(field); return true;
  }

  // Mark required fields
  ['sk-fname','sk-email','sk-service','sk-message'].forEach(id => {
    const f = document.getElementById(id);
    if (f) f.required = true;
  });

  // Live validation on blur
  form.querySelectorAll('input, textarea, select').forEach(field => {
    field.addEventListener('blur', () => validateField(field));
    field.addEventListener('input', () => {
      if (field.classList.contains('sk-invalid')) validateField(field);
    });
  });

  // Submit — validate then POST to WordPress AJAX
  const submitBtn = form.querySelector('button[type="submit"]');
  if (submitBtn) {
    submitBtn.removeEventListener('click', submitBtn._clickHandler);
    submitBtn._clickHandler = function (e) {
      e.preventDefault();

      // Client-side validation
      const fields = form.querySelectorAll('input:not([name="website"]), textarea, select');
      let allValid = true;
      fields.forEach(f => { if (!validateField(f)) allValid = false; });
      if (!allValid) {
        const firstErr = form.querySelector('.sk-invalid');
        if (firstErr) firstErr.focus();
        return;
      }

      const original = submitBtn.textContent;
      submitBtn.textContent = 'Sending…';
      submitBtn.disabled = true;

      // Build FormData for AJAX
      const data = new FormData();
      data.append('action', 'sk_contact_submit');
      data.append('nonce',  (window.skData && window.skData.nonce) || '');
      data.append('fname',   form.querySelector('#sk-fname')   ? form.querySelector('#sk-fname').value   : '');
      data.append('lname',   form.querySelector('#sk-lname')   ? form.querySelector('#sk-lname').value   : '');
      data.append('email',   form.querySelector('#sk-email')   ? form.querySelector('#sk-email').value   : '');
      data.append('service', form.querySelector('#sk-service') ? form.querySelector('#sk-service').value : '');
      data.append('message', form.querySelector('#sk-message') ? form.querySelector('#sk-message').value : '');
      data.append('website', form.querySelector('#sk-hp')      ? form.querySelector('#sk-hp').value      : ''); // honeypot

      const ajaxUrl = (window.skData && window.skData.ajaxurl) || '/wp-admin/admin-ajax.php';

      fetch(ajaxUrl, { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            const msg = (res.data && res.data.msg) ? res.data.msg : '✓ Message Sent';
            submitBtn.textContent = msg;
            submitBtn.style.background = 'var(--sage)';
            form.querySelectorAll('input:not([name="website"]), textarea, select').forEach(f => {
              f.value = ''; f.classList.remove('sk-valid', 'sk-invalid');
            });
            // Show inline success message
            let successEl = form.querySelector('.sk-form-success');
            if (!successEl) {
              successEl = document.createElement('p');
              successEl.className = 'sk-form-success';
              successEl.style.cssText = 'color:var(--sage);font-family:var(--font-ui);font-size:.85rem;margin-top:1rem;';
              form.appendChild(successEl);
            }
            successEl.textContent = msg;
            setTimeout(() => {
              submitBtn.textContent = original;
              submitBtn.disabled = false;
              submitBtn.style.background = '';
              if (successEl) successEl.textContent = '';
            }, 5000);
          } else {
            const errMsg = (res.data && res.data.msg) ? res.data.msg : 'Something went wrong. Please try again.';
            submitBtn.textContent = original;
            submitBtn.disabled = false;
            // Show error below button
            let errEl = form.querySelector('.sk-form-server-error');
            if (!errEl) {
              errEl = document.createElement('p');
              errEl.className = 'sk-form-server-error';
              errEl.style.cssText = 'color:var(--terra);font-family:var(--font-ui);font-size:.85rem;margin-top:.75rem;';
              submitBtn.parentElement.appendChild(errEl);
            }
            errEl.textContent = errMsg;
            setTimeout(() => { if (errEl) errEl.textContent = ''; }, 6000);
          }
        })
        .catch(() => {
          submitBtn.textContent = original;
          submitBtn.disabled = false;
        });
    };
    submitBtn.addEventListener('click', submitBtn._clickHandler);
  }
})();
