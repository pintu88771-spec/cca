(function () {
  'use strict';

  var $ = function (selector, root) { return (root || document).querySelector(selector); };
  var $$ = function (selector, root) { return Array.prototype.slice.call((root || document).querySelectorAll(selector)); };

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var api = document.body.getAttribute('data-api') || '/api/home.php';
  var refreshMs = Math.max(4000, parseInt(document.body.getAttribute('data-refresh') || '8000', 10) || 8000);

  var state = {};
  try {
    state = JSON.parse($('#initialState').textContent || '{}');
  } catch (error) {
    state = { festival: {}, leaders: [] };
  }

  /* ------------------------------------------------------------------
   * Translations
   * ------------------------------------------------------------------ */
  var dict = {
    en: {
      brand: 'Chanda Collection',
      total: 'Total collected',
      target: 'Target',
      complete: 'complete',
      donorsWord: 'donors',
      daysToGo: 'days to go',
      daysShort: 'days',
      daysLeftLabel: 'Days to go',
      leaderLabel: 'Current leader',
      approvedShort: 'approved',
      remainingLabel: 'Remaining to target',
      ctaTitle: "Be part of this year's celebration",
      donateNow: 'Donate now',
      ctaNote: 'Every contribution is recorded and acknowledged.',
      topDonor: '#1 Top Donor',
      donate: 'Donate',
      staff: 'Staff',
      account: 'Account',
      donors: 'Donors',
      live: 'Live leaderboard',
      liveUpdates: 'Live updates',
      topDonors: 'Top donors',
      viewTop50: 'View top 50',
      autoRefresh: 'Updates every 8 seconds',
      approvedNote: 'Only admin-approved contributions are counted.',
      anonymousNote: 'Anonymous donors are counted in the total but never shown publicly.',
      top50: 'Top 50 Donors',
      top50Hint: 'Ranked by total approved contribution',
      search: 'Search donors',
      noDonors: 'No approved donations yet. Be the first!',
      noResults: 'No donor matches your search.',
      newLeader: 'New leader!',
      comingDonate: 'The donation flow is coming in the next build.',
      comingStaff: 'Staff login is coming in the next build.',
      comingAccount: 'Donor login and registration are coming in the next build.',
      firstDonor: 'Be the first donor',
      firstDonorSub: 'Your name belongs here',
      firstDonorMsg: 'Every contribution counts 🙏',
      badges: { platinum: 'Platinum', gold: 'Gold', silver: 'Silver', bronze: 'Bronze', supporter: 'Supporter' }
    },
    hi: {
      brand: 'चंदा संग्रह',
      total: 'कुल संग्रह',
      target: 'लक्ष्य',
      complete: 'पूर्ण',
      donorsWord: 'दानवीर',
      daysToGo: 'दिन बाकी',
      daysShort: 'दिन',
      daysLeftLabel: 'बाकी दिन',
      leaderLabel: 'वर्तमान शीर्ष',
      approvedShort: 'स्वीकृत',
      remainingLabel: 'लक्ष्य तक शेष',
      ctaTitle: 'इस साल के उत्सव का हिस्सा बनें',
      donateNow: 'अभी दान करें',
      ctaNote: 'हर योगदान दर्ज और स्वीकृत किया जाता है।',
      topDonor: '#1 शीर्ष दानवीर',
      donate: 'दान करें',
      staff: 'स्टाफ',
      account: 'खाता',
      donors: 'दानवीर',
      live: 'लाइव लीडरबोर्ड',
      liveUpdates: 'लाइव अपडेट',
      topDonors: 'शीर्ष दानवीर',
      viewTop50: 'शीर्ष 50 देखें',
      autoRefresh: 'हर 8 सेकंड में अपडेट',
      approvedNote: 'केवल एडमिन-स्वीकृत योगदान ही गिने जाते हैं।',
      anonymousNote: 'गुमनाम दानवीर कुल में जुड़ते हैं, पर सार्वजनिक रूप से नहीं दिखते।',
      top50: 'शीर्ष 50 दानवीर',
      top50Hint: 'स्वीकृत कुल योगदान के अनुसार',
      search: 'दानवीर खोजें',
      noDonors: 'अभी कोई स्वीकृत दान नहीं। पहले दानवीर बनें!',
      noResults: 'कोई दानवीर नहीं मिला।',
      newLeader: 'नया शीर्ष दानवीर!',
      comingDonate: 'दान की प्रक्रिया अगले चरण में जुड़ेगी।',
      comingStaff: 'स्टाफ लॉगिन अगले चरण में जुड़ेगा।',
      comingAccount: 'दानवीर लॉगिन और पंजीकरण अगले चरण में जुड़ेंगे।',
      firstDonor: 'पहले दानवीर बनें',
      firstDonorSub: 'आपका नाम यहाँ होना चाहिए',
      firstDonorMsg: 'हर योगदान मायने रखता है 🙏',
      badges: { platinum: 'प्लेटिनम', gold: 'गोल्ड', silver: 'सिल्वर', bronze: 'ब्रॉन्ज़', supporter: 'सहयोगी' }
    }
  };

  var lang = 'en';
  try {
    lang = localStorage.getItem('cca.lang') === 'hi' ? 'hi' : 'en';
  } catch (error) { /* private mode */ }

  function t(key) {
    var pack = dict[lang] || dict.en;
    return Object.prototype.hasOwnProperty.call(pack, key) ? pack[key] : key;
  }

  function badgeLabel(badge) {
    var labels = (dict[lang] || dict.en).badges;
    return labels[badge] || labels.supporter;
  }

  /* ------------------------------------------------------------------
   * Formatting helpers
   * ------------------------------------------------------------------ */
  var inrFormatter = new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    maximumFractionDigits: 0
  });

  function inr(value) {
    var number = Number(value);
    if (!isFinite(number)) { number = 0; }
    return inrFormatter.format(Math.round(number));
  }

  function percentText(value) {
    var number = Number(value) || 0;
    return Math.round(number * 10) / 10 === Math.round(number) ? String(Math.round(number)) : number.toFixed(1);
  }

  function initials(name) {
    var parts = String(name || '').trim().split(/\s+/).filter(Boolean);
    var out = '';
    for (var i = 0; i < parts.length && out.length < 2; i++) {
      var chars = Array.from(parts[i]);
      if (chars.length) { out += chars[0]; }
    }
    return out || '🙏';
  }

  function tone(name) {
    var hash = 0;
    var chars = Array.from(String(name || ''));
    for (var i = 0; i < chars.length; i++) {
      hash = (hash * 31 + chars[i].codePointAt(0)) >>> 0;
    }
    return hash % 5;
  }

  function el(tag, className, text) {
    var node = document.createElement(tag);
    if (className) { node.className = className; }
    if (text !== undefined && text !== null) { node.textContent = text; }
    return node;
  }

  function setAvatar(node, name) {
    if (!node) { return; }
    node.textContent = initials(name);
    node.setAttribute('data-tone', String(tone(name)));
  }

  function setBadge(node, badge) {
    if (!node) { return; }
    var clean = ['platinum', 'gold', 'silver', 'bronze', 'supporter'].indexOf(badge) !== -1 ? badge : 'supporter';
    node.className = 'badge badge--' + clean;
    node.textContent = badgeLabel(clean);
  }

  /* ------------------------------------------------------------------
   * Language
   * ------------------------------------------------------------------ */
  function applyLang(next) {
    lang = next === 'hi' ? 'hi' : 'en';
    document.documentElement.lang = lang;
    document.documentElement.setAttribute('data-lang', lang);

    try { localStorage.setItem('cca.lang', lang); } catch (error) { /* ignore */ }

    $$('[data-lang-btn]').forEach(function (button) {
      var active = button.getAttribute('data-lang-btn') === lang;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    $$('[data-i18n]').forEach(function (node) {
      var value = t(node.getAttribute('data-i18n'));
      if (value) { node.textContent = value; }
    });

    $$('[data-i18n-placeholder]').forEach(function (node) {
      var value = t(node.getAttribute('data-i18n-placeholder'));
      if (value) { node.setAttribute('placeholder', value); }
    });

    renderState(state, { animate: false, fromTotal: 0 });
  }

  $$('[data-lang-btn]').forEach(function (button) {
    button.addEventListener('click', function () {
      applyLang(button.getAttribute('data-lang-btn'));
    });
  });

  /* ------------------------------------------------------------------
   * Render: totals, hero, runners, leaderboard
   * ------------------------------------------------------------------ */
  function renderHero(leader) {
    var name = leader && leader.name ? leader.name : t('firstDonor');
    var locality = leader && leader.locality ? leader.locality : t('firstDonorSub');
    var amount = leader ? leader.amount : 0;
    var message = leader && leader.message ? leader.message : t('firstDonorMsg');

    var nameEl = $('#heroName');
    var localityEl = $('#heroLocality');
    var amountEl = $('#heroAmount');
    var messageEl = $('#heroMessage');

    if (nameEl) { nameEl.textContent = name; }
    if (localityEl) { localityEl.textContent = locality; }
    if (amountEl) { amountEl.textContent = inr(amount); }
    if (messageEl) { messageEl.textContent = message; }
    setAvatar($('#heroAvatar'), name);

    var leaderShort = $('#leaderShort');
    if (leaderShort) { leaderShort.textContent = name; }
  }

  function renderRunner(rank, leader) {
    setAvatar($('#r' + rank + 'Avatar'), leader ? leader.name : '—');
    var nameEl = $('#r' + rank + 'Name');
    var localityEl = $('#r' + rank + 'Locality');
    var amountEl = $('#r' + rank + 'Amount');
    var badgeEl = $('#r' + rank + 'Badge');

    if (nameEl) { nameEl.textContent = leader ? leader.name : '—'; }
    if (localityEl) { localityEl.textContent = leader ? (leader.locality || '') : ''; }
    if (amountEl) { amountEl.textContent = inr(leader ? leader.amount : 0); }
    if (badgeEl) { setBadge(badgeEl, leader ? leader.badge : (rank === 2 ? 'silver' : 'bronze')); }
  }

  function donorRow(donor) {
    var row = el('div', 'donor-row');
    row.setAttribute('data-donor-name', String(donor.name || '').toLocaleLowerCase());

    var rank = el('span', 'donor-rank' + (donor.rank <= 3 ? ' is-top' : ''), String(donor.rank));
    var avatar = el('div', 'avatar avatar--sm');
    avatar.setAttribute('aria-hidden', 'true');
    setAvatar(avatar, donor.name);

    var middle = el('div', 'min-w-0 flex-1');
    middle.appendChild(el('p', 'truncate text-[12.5px] font-semibold leading-tight', donor.name));
    middle.appendChild(el('p', 'truncate text-[10px] leading-tight text-cream/45', donor.locality || ''));

    var right = el('div', 'shrink-0 text-right');
    var badge = el('span', 'badge');
    setBadge(badge, donor.badge);
    right.appendChild(badge);
    right.appendChild(el('p', 'mt-0.5 text-[12px] font-bold leading-tight text-gold', inr(donor.amount)));

    row.appendChild(rank);
    row.appendChild(avatar);
    row.appendChild(middle);
    row.appendChild(right);
    return row;
  }

  function renderLeaderboard(leaders) {
    var list = $('#donorList');
    if (!list) { return; }

    list.replaceChildren();
    var top = (leaders || []).slice(0, 50);

    if (!top.length) {
      list.appendChild(el('p', 'px-4 py-10 text-center text-[12px] text-cream/45', t('noDonors')));
      return;
    }

    var fragment = document.createDocumentFragment();
    top.forEach(function (donor) { fragment.appendChild(donorRow(donor)); });
    list.appendChild(fragment);
    applySearch();
  }

  function renderPreview(leaders) {
    var preview = $('#topPreview');
    if (!preview) { return; }

    preview.replaceChildren();
    (leaders || []).slice(0, 5).forEach(function (donor) {
      var row = el('div', 'preview-row');

      var rank = el('span', 'donor-rank' + (donor.rank <= 3 ? ' is-top' : ''), String(donor.rank));
      var avatar = el('div', 'avatar avatar--sm');
      avatar.setAttribute('aria-hidden', 'true');
      setAvatar(avatar, donor.name);

      var middle = el('div', 'min-w-0 flex-1');
      middle.appendChild(el('p', 'truncate text-[12px] font-semibold leading-tight', donor.name));
      middle.appendChild(el('p', 'truncate text-[9.5px] leading-tight text-cream/45', donor.locality || ''));

      var amount = el('span', 'text-[12px] font-bold text-gold', inr(donor.amount));

      row.appendChild(rank);
      row.appendChild(avatar);
      row.appendChild(middle);
      row.appendChild(amount);
      preview.appendChild(row);
    });
  }

  function renderState(next, options) {
    var opts = options || {};
    var festival = next.festival || {};
    var leaders = next.leaders || [];

    var totalEl = $('#totalAmount');
    var targetEl = $('#targetAmount');
    var percentEl = $('#percentValue');
    var fillEl = $('#progressFill');
    var trackEl = $('#progressTrack');
    var donorCountEl = $('#donorCount');

    var total = Number(festival.total) || 0;
    if (totalEl) {
      if (opts.animate && !reduceMotion) {
        animateNumber(totalEl, Number(opts.fromTotal) || 0, total);
      } else {
        totalEl.textContent = inr(total);
      }
    }

    if (targetEl) { targetEl.textContent = inr(festival.target || 0); }
    if (percentEl) { percentEl.textContent = percentText(festival.percent) + '%'; }
    if (fillEl) { fillEl.style.width = Math.min(100, Number(festival.percent) || 0) + '%'; }
    if (trackEl) { trackEl.setAttribute('aria-valuenow', String(Number(festival.percent) || 0)); }
    if (donorCountEl) { donorCountEl.textContent = String(festival.donors_count || 0); }

    var remainingEl = $('#remainingAmount');
    if (remainingEl) {
      remainingEl.textContent = inr(Math.max(0, (Number(festival.target) || 0) - total));
    }

    ['#daysLeft', '#daysLeftStat', '#daysLeftMobile'].forEach(function (selector) {
      var node = $(selector);
      if (node) { node.textContent = String(festival.days_left || 0); }
    });

    renderHero(leaders[0] || null);
    renderRunner(2, leaders[1] || null);
    renderRunner(3, leaders[2] || null);
    renderLeaderboard(leaders);
    renderPreview(leaders);
  }

  function animateNumber(node, from, to) {
    var start = performance.now();
    var duration = 900;

    function frame(now) {
      var progress = Math.min(1, (now - start) / duration);
      var eased = 1 - Math.pow(1 - progress, 3);
      node.textContent = inr(from + (to - from) * eased);
      if (progress < 1) {
        window.requestAnimationFrame(frame);
      } else {
        node.textContent = inr(to);
      }
    }

    window.requestAnimationFrame(frame);
  }

  /* ------------------------------------------------------------------
   * Donor search
   * ------------------------------------------------------------------ */
  var searchInput = $('#donorSearch');

  function applySearch() {
    if (!searchInput) { return; }
    var query = searchInput.value.trim().toLocaleLowerCase();
    var rows = $$('#donorList .donor-row');
    var visible = 0;

    rows.forEach(function (row) {
      var matches = !query || (row.getAttribute('data-donor-name') || '').indexOf(query) !== -1;
      row.classList.toggle('hidden', !matches);
      if (matches) { visible++; }
    });

    var noResults = $('#noResults');
    if (noResults) { noResults.classList.toggle('hidden', !(query && visible === 0)); }
  }

  if (searchInput) {
    searchInput.addEventListener('input', applySearch);
  }

  /* ------------------------------------------------------------------
   * Bottom sheet
   * ------------------------------------------------------------------ */
  var sheet = $('#donorSheet');
  var donorsBtn = $('#donorsBtn');
  var lastFocused = null;

  function openSheet() {
    if (!sheet) { return; }
    lastFocused = document.activeElement;
    sheet.classList.add('is-open');
    sheet.setAttribute('aria-hidden', 'false');
    if (donorsBtn) { donorsBtn.classList.add('is-active'); }

    var closeBtn = $('[data-close-sheet].icon-btn', sheet);
    if (closeBtn) { closeBtn.focus({ preventScroll: true }); }

    refresh(true);
  }

  function closeSheet() {
    if (!sheet) { return; }
    sheet.classList.remove('is-open');
    sheet.setAttribute('aria-hidden', 'true');
    if (donorsBtn) { donorsBtn.classList.remove('is-active'); }
    if (lastFocused && typeof lastFocused.focus === 'function') {
      lastFocused.focus({ preventScroll: true });
    }
  }

  if (donorsBtn) { donorsBtn.addEventListener('click', openSheet); }
  var openTop50 = $('#openTop50');
  if (openTop50) { openTop50.addEventListener('click', openSheet); }
  $$('[data-close-sheet]').forEach(function (node) { node.addEventListener('click', closeSheet); });

  document.addEventListener('keydown', function (event) {
    if (!sheet || !sheet.classList.contains('is-open')) { return; }

    if (event.key === 'Escape') {
      event.preventDefault();
      closeSheet();
      return;
    }

    if (event.key === 'Tab') {
      var focusable = $$('button, input, [href], [tabindex]:not([tabindex="-1"])', sheet)
        .filter(function (node) { return !node.disabled && node.offsetParent !== null; });
      if (!focusable.length) { return; }

      var first = focusable[0];
      var last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });

  /* ------------------------------------------------------------------
   * Toast
   * ------------------------------------------------------------------ */
  var toastTimer = null;

  function showToast(text, icon) {
    var toast = $('#toast');
    var textEl = $('#toastText');
    var iconEl = $('#toastIcon');
    if (!toast || !textEl) { return; }

    textEl.textContent = text;
    if (iconEl) { iconEl.textContent = icon || '✨'; }
    toast.classList.add('is-show');

    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () {
      toast.classList.remove('is-show');
    }, 3200);
  }

  /* ------------------------------------------------------------------
   * Live polling + leader change celebration
   * ------------------------------------------------------------------ */
  function refresh(force) {
    if (document.hidden && !force) { return; }

    window.fetch(api, {
      headers: { Accept: 'application/json' },
      cache: 'no-store'
    })
      .then(function (response) {
        if (!response.ok) { throw new Error('HTTP ' + response.status); }
        return response.json();
      })
      .then(function (next) {
        if (!next || !next.festival) { return; }

        var previous = state || {};
        var previousLeaders = previous.leaders || [];
        var nextLeaders = next.leaders || [];
        var leaderChanged = previousLeaders.length > 0 &&
          nextLeaders.length > 0 &&
          Number(previousLeaders[0].donor_id) !== Number(nextLeaders[0].donor_id);

        var fromTotal = Number(previous.festival && previous.festival.total) || 0;
        state = next;
        renderState(next, { animate: true, fromTotal: fromTotal });

        if (leaderChanged && !reduceMotion) {
          burstConfetti();
          showToast(t('newLeader') + ' ' + nextLeaders[0].name, '🎉');

          var heroCard = $('#heroCard');
          if (heroCard) {
            heroCard.classList.remove('leader-pop');
            void heroCard.offsetWidth;
            heroCard.classList.add('leader-pop');
          }
        }
      })
      .catch(function () {
        /* Keep the last known board on screen — silent retry next tick. */
      });
  }

  var pollTimer = window.setInterval(function () { refresh(false); }, refreshMs);

  document.addEventListener('visibilitychange', function () {
    if (!document.hidden) { refresh(true); }
  });

  window.addEventListener('pagehide', function () {
    window.clearInterval(pollTimer);
  });

  /* ------------------------------------------------------------------
   * Confetti
   * ------------------------------------------------------------------ */
  var canvas = $('#confetti');
  var ctx = canvas ? canvas.getContext('2d') : null;
  var particles = [];
  var confettiFrame = null;

  var confettiColors = ['#ffd166', '#ffb020', '#ff7a18', '#ff2e63', '#fff3d0', '#3ddc97'];

  function sizeCanvas() {
    if (!canvas || !ctx) { return; }
    var ratio = window.devicePixelRatio || 1;
    canvas.width = Math.floor(window.innerWidth * ratio);
    canvas.height = Math.floor(window.innerHeight * ratio);
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
  }

  function burstConfetti() {
    if (!canvas || !ctx || reduceMotion) { return; }

    sizeCanvas();

    for (var i = 0; i < 110; i++) {
      particles.push({
        x: window.innerWidth / 2 + (Math.random() - 0.5) * 160,
        y: window.innerHeight * 0.24 + (Math.random() - 0.5) * 40,
        vx: (Math.random() - 0.5) * 9,
        vy: -4 - Math.random() * 7,
        size: 4 + Math.random() * 6,
        color: confettiColors[i % confettiColors.length],
        rotation: Math.random() * Math.PI,
        spin: (Math.random() - 0.5) * 0.35,
        life: 0
      });
    }

    if (!confettiFrame) {
      confettiFrame = window.requestAnimationFrame(drawConfetti);
    }
  }

  function drawConfetti() {
    if (!ctx || !canvas) { return; }
    ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);

    var alive = [];

    particles.forEach(function (particle) {
      particle.life += 1;
      particle.vy += 0.24;
      particle.vx *= 0.995;
      particle.x += particle.vx;
      particle.y += particle.vy;
      particle.rotation += particle.spin;

      if (particle.y < window.innerHeight + 40 && particle.life < 210) {
        alive.push(particle);

        ctx.save();
        ctx.translate(particle.x, particle.y);
        ctx.rotate(particle.rotation);
        ctx.globalAlpha = Math.max(0, 1 - particle.life / 210);
        ctx.fillStyle = particle.color;
        ctx.fillRect(-particle.size / 2, -particle.size / 2, particle.size, particle.size * 0.62);
        ctx.restore();
      }
    });

    particles = alive;

    if (particles.length) {
      confettiFrame = window.requestAnimationFrame(drawConfetti);
    } else {
      confettiFrame = null;
      ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);
    }
  }

  window.addEventListener('resize', function () {
    if (particles.length) { sizeCanvas(); }
  });

  /* ------------------------------------------------------------------
   * Boot
   * ------------------------------------------------------------------ */
  applyLang(lang);
})();
