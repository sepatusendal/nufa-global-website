// NUFA GLOBAL EDUCATION — script.js
document.addEventListener('DOMContentLoaded', function () {

  var IS_EN = document.documentElement.lang === 'en';

  /* "Bumper" logo intro (gallery.html) — click/tap or Escape skips straight to the page. */
  var bumper = document.getElementById('nufa-bumper');
  if (bumper) {
    var skipBumper = function () { bumper.classList.add('skip-now'); };
    bumper.addEventListener('click', skipBumper);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') skipBumper();
    });
    bumper.addEventListener('animationend', function (e) {
      if (e.animationName === 'bumperFadeOut') bumper.style.display = 'none';
    });
  }

  /* Program highlight rail — horizontal scroll-snap, prev/next buttons scroll by one card width. */
  var phiGrid = document.querySelector('.phi-grid');
  if (phiGrid) {
    var phiPrev = document.querySelector('.phi-prev');
    var phiNext = document.querySelector('.phi-next');
    var scrollByCard = function (dir) {
      var card = phiGrid.querySelector('.phi-card');
      var step = card ? card.getBoundingClientRect().width + 20 : 320;
      phiGrid.scrollBy({ left: dir * step, behavior: 'smooth' });
    };
    if (phiPrev) phiPrev.addEventListener('click', function () { scrollByCard(-1); });
    if (phiNext) phiNext.addEventListener('click', function () { scrollByCard(1); });
  }

  /* Apply assets/site-config.js overrides (lets you swap hero media via URL,
     without touching any HTML) before wiring up the fallback/lightbox logic below. */
  var CONFIG = window.SITE_CONFIG || {};
  var heroVideo = document.getElementById('hero-video');
  if (heroVideo && CONFIG.heroVideoPoster) heroVideo.setAttribute('poster', CONFIG.heroVideoPoster);
  var heroVideoBtn = document.querySelector('.hero-video-btn');
  if (heroVideoBtn && CONFIG.companyProfileVideoUrl) {
    heroVideoBtn.dataset.src = CONFIG.companyProfileVideoUrl;
  }

  /* Hero stat numbers count up from 0 on load, instead of just appearing */
  document.querySelectorAll('[data-count-to]').forEach(function (el) {
    var target = parseInt(el.dataset.countTo, 10);
    var suffix = el.dataset.suffix || '';
    var duration = 1300;
    var start = null;
    function step(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target) + suffix;
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target + suffix;
    }
    setTimeout(function () { requestAnimationFrame(step); }, 300);
  });

  /* Hero video: gracefully fall back to gradient-only hero if no video file has been added yet.
     Self-healing — if the video does load/play after the initial check (e.g. it was still
     buffering, or SITE_CONFIG just triggered a fresh .load()), the fallback class is removed
     again so a slow-loading real video never gets stuck hidden. */
  var heroVideoLayer = document.getElementById('hero-video-layer');
  if (heroVideo && heroVideoLayer) {
    heroVideo.addEventListener('error', function () {
      heroVideoLayer.classList.add('no-video');
    }, true);
    ['loadeddata', 'canplay', 'playing'].forEach(function (evt) {
      heroVideo.addEventListener(evt, function () {
        heroVideoLayer.classList.remove('no-video');
      });
    });
  }

  /* Attach the hero clip only once the page itself has finished loading. The clip
     is ~4MB; if the <source> carried a real src, the browser would start pulling it
     while the render-blocking CSS is still in flight, and on a first visit that
     pushed the first paint back by seconds. The HTML keeps the path in data-src,
     and SITE_CONFIG.heroVideoUrl can override it. */
  if (heroVideo) {
    var heroVideoStarted = false;
    var startHeroVideo = function () {
      if (heroVideoStarted) return;
      var heroSource = heroVideo.querySelector('source');
      var heroUrl = CONFIG.heroVideoUrl || (heroSource && heroSource.getAttribute('data-src'));
      if (!heroUrl) {
        if (heroVideoLayer) heroVideoLayer.classList.add('no-video');
        return;
      }
      heroVideoStarted = true;
      if (heroSource) heroSource.src = heroUrl;
      else heroVideo.src = heroUrl;
      heroVideo.load();
      /* Safari/iOS sometimes won't resume autoplay on its own after a
         programmatic load() — nudge it once the new source is ready. */
      heroVideo.play().catch(function () {});
      // If after a few seconds there's still no video data at all (e.g. source file missing),
      // hide the layer — generous delay so it doesn't race a legitimate slow load.
      if (heroVideoLayer) {
        setTimeout(function () {
          if (heroVideo.readyState === 0) heroVideoLayer.classList.add('no-video');
        }, 4000);
      }
    };
    if (document.readyState === 'complete') {
      startHeroVideo();
    } else {
      window.addEventListener('load', startHeroVideo);
      // Don't wait forever if some third-party asset stalls the load event.
      setTimeout(startHeroVideo, 5000);
    }
  }

  /* Navbar scroll state */
  var navbar = document.getElementById('navbar');
  function onScroll() {
    if (window.scrollY > 12) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');

    var backTop = document.getElementById('back-top');
    if (backTop) {
      if (window.scrollY > 700) backTop.classList.add('show');
      else backTop.classList.remove('show');
    }
  }
  window.addEventListener('scroll', onScroll);
  onScroll();

  /* Mobile nav toggle */
  var toggle = document.getElementById('nav-toggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      navbar.classList.toggle('open');
    });
    var dropdownParentLinks = [];
    document.querySelectorAll('.nav-item.has-dropdown > a').forEach(function (parentLink) {
      dropdownParentLinks.push(parentLink);
      parentLink.addEventListener('click', function (e) {
        if (window.innerWidth <= 720) {
          e.preventDefault();
          parentLink.parentElement.classList.toggle('expanded');
        }
      });
    });
    document.querySelectorAll('.nav-links a').forEach(function (link) {
      if (dropdownParentLinks.indexOf(link) !== -1) return;
      link.addEventListener('click', function () { navbar.classList.remove('open'); });
    });
  }

  /* Back to top */
  var backTop = document.getElementById('back-top');
  if (backTop) {
    backTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* Program tabs */
  var tabs = document.querySelectorAll('.ptab');
  var panels = document.querySelectorAll('.ppanel');
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (t) { t.classList.remove('active'); });
      panels.forEach(function (p) { p.classList.remove('active'); });
      tab.classList.add('active');
      var target = document.getElementById(tab.dataset.target);
      if (target) target.classList.add('active');
    });
  });

  /* FAQ accordion */
  document.querySelectorAll('.faq-item').forEach(function (item) {
    var q = item.querySelector('.faq-q');
    var a = item.querySelector('.faq-a');
    q.addEventListener('click', function () {
      var isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item').forEach(function (i) {
        i.classList.remove('open');
        i.querySelector('.faq-a').style.maxHeight = null;
      });
      if (!isOpen) {
        item.classList.add('open');
        a.style.maxHeight = a.scrollHeight + 'px';
      }
    });
  });

  /* Reveal on scroll */
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in'); });
  }

  /* Show the real photo (or video poster frame) on a gallery/highlight card
     once it's confirmed to load, instead of leaving the gradient placeholder
     up forever. Video cards keep their play button/badge overlay since they
     still need a click to actually play in the lightbox.
     Images are fetched lazily, just before a card scrolls into view. Loading every
     card up front meant ~7MB of JPGs (including carousel cards nobody had scrolled
     to yet, and hidden ones) on the first visit. */
  function loadGalleryThumb(item) {
    var src = item.dataset.type === 'video' ? item.dataset.poster : item.dataset.src;
    var thumb = item.querySelector('.thumb');
    if (!src || !thumb) return;
    var img = new Image();
    img.onload = function () {
      thumb.style.backgroundImage = "url('" + src + "')";
      thumb.classList.remove('ph');
      var icon = thumb.querySelector('.ph-icon');
      if (icon) icon.remove();
    };
    img.src = src;
  }
  var galleryThumbItems = document.querySelectorAll('.gitem[data-type="photo"], .gitem[data-type="video"]');
  if ('IntersectionObserver' in window) {
    var thumbIO = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        thumbIO.unobserve(entry.target);
        loadGalleryThumb(entry.target);
      });
    }, { rootMargin: '300px' });
    galleryThumbItems.forEach(function (item) { thumbIO.observe(item); });
  } else {
    galleryThumbItems.forEach(loadGalleryThumb);
  }

  /* Gallery filter */
  var gfilters = document.querySelectorAll('.gfilter');
  var gitems = document.querySelectorAll('.gitem');
  gfilters.forEach(function (btn) {
    btn.addEventListener('click', function () {
      gfilters.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var filter = btn.dataset.filter;
      gitems.forEach(function (item) {
        if (filter === 'all' || item.dataset.cat === filter) item.classList.add('show');
        else item.classList.remove('show');
      });
    });
  });

  /* Lightbox for gallery items */
  var lightbox = document.getElementById('lightbox');
  var lbMedia = document.getElementById('lb-media');
  var lbTitle = document.getElementById('lb-title');
  var lbCat = document.getElementById('lb-cat');
  var lbClose = document.getElementById('lightbox-close');

  gitems.forEach(function (item) {
    item.addEventListener('click', function () {
      var type = item.dataset.type;
      var src = item.dataset.src;
      var title = item.dataset.title;
      var cat = item.dataset.catLabel;

      lbMedia.innerHTML = '';
      if (type === 'video') {
        var vid = document.createElement('video');
        vid.src = src;
        vid.controls = true;
        vid.autoplay = true;
        vid.style.width = '100%';
        vid.style.height = '100%';
        vid.addEventListener('error', function () {
          var msg = IS_EN ? 'Video not available yet.<br>Add the file at ' : 'Video belum tersedia.<br>Tambahkan file di ';
          lbMedia.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,0.5);font-size:14px;text-align:center;padding:20px;">' + msg + src + '</div>';
        });
        lbMedia.appendChild(vid);
      } else {
        lbMedia.style.backgroundImage = "url('" + src + "')";
        var img = new Image();
        img.onerror = function () {
          lbMedia.style.backgroundImage = 'none';
          var msg = IS_EN ? 'Photo not available yet.<br>Add the file at ' : 'Foto belum tersedia.<br>Tambahkan file di ';
          lbMedia.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,0.5);font-size:14px;text-align:center;padding:20px;">' + msg + src + '</div>';
        };
        img.src = src;
      }
      lbTitle.textContent = title;
      lbCat.textContent = cat;
      lightbox.classList.add('open');
    });
  });

  function closeLightbox() {
    lightbox.classList.remove('open');
    lbMedia.innerHTML = '';
    lbMedia.style.backgroundImage = 'none';
  }
  if (lbClose) lbClose.addEventListener('click', closeLightbox);
  if (lightbox) {
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox) closeLightbox();
    });
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeLightbox();
  });

  /* Contact form -> submits into a Google Form (responses land in a Google Sheet) */
  var GFORM_ACTION = 'https://docs.google.com/forms/d/e/1FAIpQLSeMpeguYKku3eV5gwkPiqJTHhLPUtqBGFvv7dIiLC0PnTb4hA/formResponse';
  var GFORM_ENTRIES = {
    name: 'entry.191453223',
    school: 'entry.1674040051',
    email: 'entry.838742538',
    phone: 'entry.1059957012',
    interest: 'entry.563354993',
    message: 'entry.1179613016'
  };

  var form = document.getElementById('contact-form');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var name = document.getElementById('f-name').value.trim();
      var school = document.getElementById('f-school').value.trim();
      var email = document.getElementById('f-email').value.trim();
      var phone = document.getElementById('f-phone').value.trim();
      var interest = document.getElementById('f-interest').value;
      var message = document.getElementById('f-message').value.trim();

      var submitBtn = form.querySelector('button[type="submit"]');
      var originalBtnText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = IS_EN ? 'Sending...' : 'Mengirim...';

      var data = new FormData();
      data.append(GFORM_ENTRIES.name, name);
      data.append(GFORM_ENTRIES.school, school);
      data.append(GFORM_ENTRIES.email, email);
      data.append(GFORM_ENTRIES.phone, phone);
      data.append(GFORM_ENTRIES.interest, interest);
      data.append(GFORM_ENTRIES.message, message);

      fetch(GFORM_ACTION, { method: 'POST', mode: 'no-cors', body: data })
        .then(function () {
          form.reset();
          submitBtn.disabled = false;
          submitBtn.textContent = originalBtnText;
          var successPanel = document.getElementById('form-success');
          if (successPanel) {
            form.style.display = 'none';
            successPanel.classList.remove('show');
            void successPanel.offsetWidth; /* restart animation on repeat submits */
            successPanel.classList.add('show');
          }
        })
        .catch(function () {
          submitBtn.disabled = false;
          submitBtn.textContent = originalBtnText;
          var subject = encodeURIComponent((IS_EN ? 'Program Consultation — ' : 'Konsultasi Program — ') + (school || name));
          var body = encodeURIComponent(
            IS_EN
              ? 'Name: ' + name + '\n' +
                'School/Institution: ' + school + '\n' +
                'Email: ' + email + '\n' +
                'Phone/WA: ' + phone + '\n' +
                'Program of interest: ' + interest + '\n\n' +
                'Message:\n' + message
              : 'Nama: ' + name + '\n' +
                'Sekolah/Institusi: ' + school + '\n' +
                'Email: ' + email + '\n' +
                'No. HP/WA: ' + phone + '\n' +
                'Program yang diminati: ' + interest + '\n\n' +
                'Pesan:\n' + message
          );
          window.location.href = 'mailto:info@nufaglobaledu.com?subject=' + subject + '&body=' + body;
        });
    });

    var successResetBtn = document.getElementById('form-success-reset');
    if (successResetBtn) {
      successResetBtn.addEventListener('click', function () {
        document.getElementById('form-success').classList.remove('show');
        form.style.display = '';
      });
    }
  }

  /* Program expand cards (programs page). One card is open at a time: on wide screens
     they are panels that open on hover/focus/tap, up to 1000px they become an accordion.
     Without this script the CSS leaves every card stacked and fully open. */
  var pxc = document.getElementById('program-cards');
  if (pxc) {
    var pxcCards = Array.prototype.slice.call(pxc.querySelectorAll('.pxc-card'));
    var pxcWide = window.matchMedia('(min-width: 1001px)');
    var pxcHover = window.matchMedia('(hover: hover) and (pointer: fine)');
    var pxcTimer = null;

    var pxcSet = function (card, open) {
      card.classList.toggle('is-active', open);
      card.querySelector('.pxc-head').setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    var pxcOpen = function (card) {
      pxcCards.forEach(function (c) { pxcSet(c, c === card); });
    };

    // Accordion only: opening a card collapses the one above it, which shifts the page.
    // Once the open/close transition has finished, scroll so the opened card is in view.
    var pxcKeepInView = function (card) {
      var body = card.querySelector('.pxc-body');
      var done = false;
      var run = function () {
        if (done) return;
        done = true;
        body.removeEventListener('transitionend', onEnd);
        var r = card.getBoundingClientRect();
        if (r.top < 84 || r.bottom > window.innerHeight) {
          window.scrollTo({ top: r.top + window.pageYOffset - 90, behavior: 'smooth' });
        }
      };
      var onEnd = function (e) {
        if (e.target === body && e.propertyName === 'grid-template-rows') run();
      };
      body.addEventListener('transitionend', onEnd);
      setTimeout(run, 900); // fallback when transitions are off (reduced motion)
    };

    pxcCards.forEach(function (card, i) {
      var head = card.querySelector('.pxc-head');

      head.addEventListener('click', function () {
        var isOpen = card.classList.contains('is-active');
        if (pxcWide.matches) { if (!isOpen) pxcOpen(card); }
        else if (isOpen) pxcSet(card, false);
        else {
          pxcOpen(card);
          pxcKeepInView(card);
        }
      });

      // Mouse users: open after a short hover so a pointer sweeping across the row doesn't flicker.
      card.addEventListener('mouseenter', function () {
        if (!pxcWide.matches || !pxcHover.matches) return;
        clearTimeout(pxcTimer);
        pxcTimer = setTimeout(function () { pxcOpen(card); }, 90);
      });
      card.addEventListener('mouseleave', function () { clearTimeout(pxcTimer); });

      // Keyboard: tabbing to a card opens it on wide screens; arrows/Home/End move between cards.
      head.addEventListener('focus', function () { if (pxcWide.matches) pxcOpen(card); });
      head.addEventListener('keydown', function (e) {
        var next = -1;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') next = (i + 1) % pxcCards.length;
        else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') next = (i - 1 + pxcCards.length) % pxcCards.length;
        else if (e.key === 'Home') next = 0;
        else if (e.key === 'End') next = pxcCards.length - 1;
        if (next < 0) return;
        e.preventDefault();
        pxcCards[next].querySelector('.pxc-head').focus();
      });
    });

    // The row layout always needs one open card; the accordion may have none.
    var pxcEnsureOne = function () {
      if (pxcWide.matches && !pxc.querySelector('.pxc-card.is-active')) pxcOpen(pxcCards[0]);
    };
    if (pxcWide.addEventListener) pxcWide.addEventListener('change', pxcEnsureOne);
    else if (pxcWide.addListener) pxcWide.addListener(pxcEnsureOne);

    pxcEnsureOne();
    pxc.classList.add('is-ready');
  }

  /* Smooth scroll for in-page anchors */
  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var id = link.getAttribute('href');
      if (id.length > 1) {
        var target = document.querySelector(id);
        if (target) {
          e.preventDefault();
          var offset = 78;
          var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
          window.scrollTo({ top: top, behavior: 'smooth' });
        }
      }
    });
  });

});
