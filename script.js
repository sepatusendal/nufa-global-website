// NUFA GLOBAL EDUCATION — script.js
document.addEventListener('DOMContentLoaded', function () {

  /* Apply assets/site-config.js overrides (lets you swap hero media via URL,
     without touching any HTML) before wiring up the fallback/lightbox logic below. */
  var CONFIG = window.SITE_CONFIG || {};
  var heroVideo = document.getElementById('hero-video');
  if (heroVideo) {
    if (CONFIG.heroVideoUrl) {
      var heroSource = heroVideo.querySelector('source');
      if (heroSource) heroSource.src = CONFIG.heroVideoUrl;
      else heroVideo.src = CONFIG.heroVideoUrl;
      heroVideo.load();
    }
    if (CONFIG.heroVideoPoster) heroVideo.setAttribute('poster', CONFIG.heroVideoPoster);
  }
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
    // If after a few seconds there's still no video data at all (e.g. source file missing),
    // hide the layer — generous delay so it doesn't race a legitimate slow load.
    setTimeout(function () {
      if (heroVideo.readyState === 0) heroVideoLayer.classList.add('no-video');
    }, 4000);
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
    document.querySelectorAll('.nav-links a').forEach(function (link) {
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
          lbMedia.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,0.5);font-size:14px;text-align:center;padding:20px;">Video belum tersedia.<br>Tambahkan file di ' + src + '</div>';
        });
        lbMedia.appendChild(vid);
      } else {
        lbMedia.style.backgroundImage = "url('" + src + "')";
        var img = new Image();
        img.onerror = function () {
          lbMedia.style.backgroundImage = 'none';
          lbMedia.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,0.5);font-size:14px;text-align:center;padding:20px;">Foto belum tersedia.<br>Tambahkan file di ' + src + '</div>';
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

  /* Contact form -> mailto fallback (static hosting friendly) */
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

      var subject = encodeURIComponent('Konsultasi Program — ' + (school || name));
      var body = encodeURIComponent(
        'Nama: ' + name + '\n' +
        'Sekolah/Institusi: ' + school + '\n' +
        'Email: ' + email + '\n' +
        'No. HP/WA: ' + phone + '\n' +
        'Program yang diminati: ' + interest + '\n\n' +
        'Pesan:\n' + message
      );
      window.location.href = 'mailto:info@nufaglobaledu.com?subject=' + subject + '&body=' + body;
    });
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
