/* Ruangsport: animasi scroll & interaksi ringan (tanpa dependensi). Menghormati prefers-reduced-motion. */
(function () {
  'use strict';

  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function ready(fn) {
    if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn);
  }

  function formatNumber(n) {
    try { return n.toLocaleString('id-ID'); } catch (e) { return String(n); }
  }

  ready(function () {
    var doc = document;

    // Gambar dimuat malas; tabel lebar bisa digeser di layar kecil.
    doc.querySelectorAll('img:not([loading])').forEach(function (img) { img.loading = 'lazy'; });
    doc.querySelectorAll('table.table').forEach(function (table) {
      if (table.parentElement.classList.contains('table-responsive')) return;
      var wrap = doc.createElement('div');
      wrap.className = 'table-responsive';
      table.parentNode.insertBefore(wrap, table);
      wrap.appendChild(table);
    });

    // ---- Reveal saat scroll: elemen dalam satu baris muncul bergantian
    var selector = '.site-section h3, .site-section h4, .rs-section h3, .rs-section h4, .card-block, .auth-card, .filter-form, .bracket-round, .step-item, .stat-item, .sport-chip, .cta-band .container';
    var counts = new Map();
    var targets = [];

    doc.querySelectorAll(selector).forEach(function (el) {
      if (el.closest('.page-hero')) return;
      var group = (el.closest('[class*="col-"]') || el).parentElement;
      var n = counts.has(group) ? counts.get(group) + 1 : 0;
      counts.set(group, n);
      el.style.setProperty('--rs-delay', Math.min(n, 5) * 70 + 'ms');
      el.classList.add('rs-reveal');
      targets.push(el);
    });

    function countUp(el) {
      var end = parseInt(el.getAttribute('data-count'), 10);
      if (isNaN(end) || reduce) { el.textContent = formatNumber(isNaN(end) ? 0 : end); return; }
      var start = null;
      function step(ts) {
        if (start === null) start = ts;
        var p = Math.min((ts - start) / 1200, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = formatNumber(Math.round(end * eased));
        if (p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }

    function show(el) {
      el.classList.add('is-visible');
      el.querySelectorAll('[data-count]').forEach(countUp);
    }

    if (!('IntersectionObserver' in window) || reduce) {
      targets.forEach(show);
    } else {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) { show(entry.target); io.unobserve(entry.target); }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
      targets.forEach(function (el) { io.observe(el); });
    }

    // ---- Scroll: progress bar, tombol ke atas, parallax hero
    var bar = doc.getElementById('scroll-progress');
    var toTop = doc.getElementById('back-to-top');
    var heroBg = doc.querySelector('[data-parallax]');
    var ticking = false;

    function onScroll() {
      var y = window.pageYOffset || doc.documentElement.scrollTop;
      var max = doc.documentElement.scrollHeight - window.innerHeight;
      if (bar) bar.style.transform = 'scaleX(' + (max > 0 ? Math.min(y / max, 1) : 0) + ')';
      if (toTop) toTop.classList.toggle('is-visible', y > 500);
      if (heroBg && !reduce && y < window.innerHeight * 1.2) {
        heroBg.style.transform = 'translate3d(0,' + Math.round(y * 0.25) + 'px,0)';
      }
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
    }, { passive: true });
    onScroll();

    if (toTop) {
      toTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
      });
    }
  });
})();
