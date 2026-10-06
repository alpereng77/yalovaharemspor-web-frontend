// ==========================================
// Yalova Haremspor - Ana Etkileşim Scripti
// ==========================================

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobil Menü
  const menuBtn = document.querySelector('.menu');
  const nav = document.querySelector('#nav');

  if (menuBtn && nav) {
    menuBtn.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      menuBtn.setAttribute('aria-expanded', String(open));
      menuBtn.setAttribute('aria-label', open ? 'Menüyü kapat' : 'Menüyü aç');
      menuBtn.textContent = open ? '✕' : '☰';
    });

    nav.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        nav.classList.remove('open');
        menuBtn.setAttribute('aria-expanded', 'false');
        menuBtn.textContent = '☰';
      });
    });
  }

  // Açılış alanında küçük imleç hareketi; görünmeyen animasyonlar durur.
  const arena = document.querySelector('.hero-arena');
  const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
  const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
  if (arena) {
    const photo = arena.querySelector('.arena-photo');
    let pointerFrame = 0;
    arena.addEventListener('pointermove', event => {
      if (motionPreference.matches || !finePointer.matches) return;
      cancelAnimationFrame(pointerFrame);
      pointerFrame = requestAnimationFrame(() => {
        const bounds = arena.getBoundingClientRect();
        photo.style.setProperty('--arena-x', `${(event.clientX - bounds.left - bounds.width / 2) / bounds.width * 8}px`);
        photo.style.setProperty('--arena-y', `${(event.clientY - bounds.top - bounds.height / 2) / bounds.height * 6}px`);
      });
    }, { passive: true });
    const resetDepth = () => {
      cancelAnimationFrame(pointerFrame);
      photo.style.removeProperty('--arena-x');
      photo.style.removeProperty('--arena-y');
    };
    arena.addEventListener('pointerleave', resetDepth);
    motionPreference.addEventListener('change', resetDepth);
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(([entry]) => {
        arena.classList.toggle('arena-paused', !entry.isIntersecting);
      }).observe(arena);
    }
  }

  // Fanatik'in yerel veri kopyası. Saatler her cihazda Türkiye saatidir.
  const matchData = window.HAREM_MATCH_DATA;
  if (matchData) {
    const timeOptions = { timeZone: 'Europe/Istanbul' };
    const formatDate = (value, options) => new Intl.DateTimeFormat('tr-TR', { ...timeOptions, ...options }).format(new Date(value));
    const time = value => formatDate(value, { hour: '2-digit', minute: '2-digit' });
    const complete = match => ['closed', 'ended'].includes(match.status) && Number.isFinite(match.homeScore) && Number.isFinite(match.awayScore);
    const upcoming = matchData.matches.filter(match => !complete(match) && new Date(match.startTime) >= new Date() && match.status === 'not_started');
    const results = matchData.matches.filter(complete).slice().reverse();
    const next = upcoming[0];
    if (next) {
      document.querySelector('#next-match-opponent').textContent = next.isHome ? next.away : next.home;
      document.querySelector('#next-match-location').textContent = next.isHome ? 'RAKİP · DEPLASMAN' : 'RAKİP · EV SAHİBİ';
      document.querySelector('#next-match-date').textContent = `${formatDate(next.startTime, { day: 'numeric', month: 'long', weekday: 'long' })} · ${next.confirmed ? time(next.startTime) : 'Saat kesinleşmedi'}`;
      document.querySelector('#next-match-venue').textContent = next.venue || 'Kaynakta belirtilmemiş';
      document.querySelector('#next-match-venue').closest('p').hidden = !next.venue;
    }
    // Sonuçlar ve özet aynı tamamlanmış maçlardan hesaplanır.
    const record = results.reduce((total, match) => {
      const scored = match.isHome ? match.homeScore : match.awayScore;
      const conceded = match.isHome ? match.awayScore : match.homeScore;
      total.win += Number(scored > conceded);
      total.loss += Number(scored < conceded);
      total.pointsFor += scored;
      total.pointsAgainst += conceded;
      return total;
    }, { win: 0, loss: 0, pointsFor: 0, pointsAgainst: 0 });
    const missingResult = matchData.matches.some(match => !complete(match) && match.status === 'not_started' && Date.now() - Date.parse(match.startTime) > 6 * 60 * 60 * 1000);
    const stats = [['LİG SIRASI', !missingResult && matchData.standing.rank ? `${matchData.standing.rank}.` : '—'], ['GALİBİYET', record.win], ['MAĞLUBİYET', record.loss], ['PUAN', record.win * 2 + record.loss], ['ATILAN / YENİLEN', `${record.pointsFor} / ${record.pointsAgainst}`]];
    const statContainer = document.querySelector('#season-record');
    stats.forEach(([label, value]) => {
      const item = document.createElement('p');
      const number = document.createElement('b'); number.textContent = value;
      const text = document.createElement('span'); text.textContent = label;
      item.append(number, text); statContainer.append(item);
    });
    function addRows(container, matches, scores = false) {
      container.replaceChildren();
      if (!matches.length) {
        const row = document.createElement('tr');
        const cell = document.createElement('td'); cell.colSpan = 4;
        cell.textContent = scores ? 'Bu sezon için maç sonucu bulunmuyor.' : 'Kaynakta yeni bir karşılaşma bulunmuyor.';
        row.append(cell); container.append(row); return;
      }
      matches.forEach(match => {
        const row = document.createElement('tr');
        const dateCell = document.createElement('td');
        const date = document.createElement('span'); date.className = 'schedule-date';
        date.textContent = formatDate(match.startTime, { day: '2-digit', month: 'short' });
        const note = document.createElement('span'); note.className = 'schedule-date-note';
        note.textContent = `${match.round}. HAFTA`;
        dateCell.append(date, note);
        const teams = document.createElement('td');
        const home = document.createElement('b'); home.textContent = match.home;
        const away = document.createElement('span'); away.className = 'schedule-opponent'; away.textContent = match.away;
        teams.append(home, away);
        const location = document.createElement('td');
        const won = (match.isHome ? match.homeScore : match.awayScore) > (match.isHome ? match.awayScore : match.homeScore);
        location.textContent = scores ? (won ? 'GALİBİYET' : 'MAĞLUBİYET') : (match.isHome ? 'EVİMİZDE' : 'DEPLASMAN');
        const last = document.createElement('td');
        last.textContent = scores ? `${match.homeScore} – ${match.awayScore}` : (match.confirmed ? time(match.startTime) : 'Kesinleşmedi');
        if (scores) last.className = 'schedule-score';
        row.append(dateCell, teams, location, last); container.append(row);
      });
    }
    const more = document.querySelector('#fixture-more');
    addRows(document.querySelector('#fixture-rows'), upcoming.slice(0, 5));
    more.hidden = upcoming.length <= 5;
    more.addEventListener('click', () => {
      const expanded = more.getAttribute('aria-expanded') === 'true';
      addRows(document.querySelector('#fixture-rows'), expanded ? upcoming.slice(0, 5) : upcoming);
      more.setAttribute('aria-expanded', String(!expanded));
      more.textContent = expanded ? 'TÜM FİKSTÜRÜ GÖSTER +' : 'DAHA AZ GÖSTER −';
    });
    more.setAttribute('aria-expanded', 'false');
    addRows(document.querySelector('#result-rows'), results, true);
    document.querySelector('#fixture-caption').textContent = `${matchData.season} · Kaynakta yer alan ${upcoming.length} gelecek maç. Saatler Türkiye saatidir; program değişebilir.`;
    document.querySelector('#match-data-updated').textContent = `SON GÜNCELLEME: ${formatDate(matchData.updatedAt, { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}`;
    if (missingResult) {
      const notice = document.createElement('p');
      notice.className = 'match-data-notice';
      notice.setAttribute('role', 'status');
      notice.textContent = 'Son oynanan maçın sonucu henüz bu veri kopyasına eklenmedi. İstatistikler yalnızca kayıtlı sonuçları kapsıyor; maç verileri güncellenmeli.';
      statContainer.after(notice);
    }
    document.querySelector('#results-panel caption').textContent = `${matchData.season} · Tamamlanan lig karşılaşmaları. Skorlar ev sahibi – deplasman sırasındadır.`;
  }

  // Fikstür ve sonuç sekmeleri; klavye ile de gezinilebilir.
  const fixtureTabs = [...document.querySelectorAll('.fixture-tabs [role="tab"]')];
  function selectFixtureTab(button, focus = false) {
    fixtureTabs.forEach(tab => {
      const selected = tab === button;
      tab.classList.toggle('active', selected);
      tab.setAttribute('aria-selected', String(selected));
      tab.tabIndex = selected ? 0 : -1;
      document.getElementById(tab.dataset.panel).hidden = !selected;
    });
    if (focus) button.focus();
  }
  fixtureTabs.forEach((button, index) => {
    button.addEventListener('click', () => selectFixtureTab(button));
    button.addEventListener('keydown', event => {
      let target;
      if (event.key === 'ArrowRight') target = (index + 1) % fixtureTabs.length;
      if (event.key === 'ArrowLeft') target = (index - 1 + fixtureTabs.length) % fixtureTabs.length;
      if (event.key === 'Home') target = 0;
      if (event.key === 'End') target = fixtureTabs.length - 1;
      if (target !== undefined) { event.preventDefault(); selectFixtureTab(fixtureTabs[target], true); }
    });
  });

  // Kayıt servisi bağlanana kadar kişisel veri gönderilmez ve kayıt yapılmış gibi gösterilmez.
  document.querySelector('#newsletter-form')?.addEventListener('submit', event => {
    event.preventDefault();
    document.querySelector('#newsletter-status').textContent = 'Bülten kaydı yakında açılacak. E-posta adresin henüz kaydedilmedi.';
  });

  // Bileşenler ekrana her girişte yeniden açılır; düzen ve hover hareketleri korunur.
  if ('IntersectionObserver' in window) {
    const revealSelector = '.matchday-duel, .matchday-info, .matchday-record > p, .schedule-top, .schedule-table tbody > tr, .schedule-more, .matchday-source, .yalova-about-main, .yalova-about-inset, .yalova-about-copy > h2, .sponsors-head, .sponsors-bottom-bar, .players-heading, .player-entry, .yalova-about-footer, .identity .section-head, .identity-card, .newsletter-copy, .newsletter-form, .footer-top, .footer-bottom';
    const revealItems = new Set();
    const revealObserver = new IntersectionObserver(entries => {
      entries.forEach(({ target, isIntersecting }) => {
        target.classList.toggle('is-revealed', isIntersecting || motionPreference.matches || target.contains(document.activeElement));
      });
    }, { threshold: 0, rootMargin: '0px 0px -24px 0px' });
    const observeReveals = () => {
      document.querySelectorAll(revealSelector).forEach(element => {
        if (revealItems.has(element)) return;
        revealItems.add(element);
        const siblings = [...element.parentElement.children].filter(item => item.matches(revealSelector));
        element.style.setProperty('--reveal-delay', `${Math.min(siblings.indexOf(element), 3) * 70}ms`);
        element.classList.add('scroll-reveal');
        if (motionPreference.matches) element.classList.add('is-revealed');
        revealObserver.observe(element);
      });
    };
    observeReveals();
    // Fikstür genişletildiğinde yeni satırlar da aynı animasyonu kullanır.
    const rowObserver = new MutationObserver(() => {
      revealItems.forEach(element => {
        if (!element.isConnected) { revealObserver.unobserve(element); revealItems.delete(element); }
      });
      observeReveals();
    });
    document.querySelectorAll('.schedule-table tbody').forEach(body => rowObserver.observe(body, { childList: true }));
    document.addEventListener('focusin', event => {
      event.target.closest('.scroll-reveal')?.classList.add('is-revealed');
    });
    motionPreference.addEventListener('change', () => {
      revealItems.forEach(element => {
        if (motionPreference.matches) element.classList.add('is-revealed');
        else { revealObserver.unobserve(element); revealObserver.observe(element); }
      });
    });
  }

  // 5. Yıl Dinamik Güncelleme
  const yearElem = document.querySelector('#year');
  if (yearElem) {
    yearElem.textContent = new Date().getFullYear();
  }

  // 6. Sponsorlar Dinamik Slider Etkileşimi
  const sponsorsCarousel = document.querySelector('#sponsors-carousel');
  const sponsorsTrack = document.querySelector('#sponsors-track');
  const sponsorsToggle = document.querySelector('#sponsors-toggle');
  const sponsorsPrev = document.querySelector('#sponsors-prev');
  const sponsorsNext = document.querySelector('#sponsors-next');

  if (sponsorsCarousel && sponsorsTrack) {
    let isPaused = false;
    let dragStartX = 0;
    let isDragging = false;
    let baseOffset = 0;

    const iconPause = sponsorsToggle?.querySelector('.icon-pause');
    const iconPlay = sponsorsToggle?.querySelector('.icon-play');

    function updatePauseState(paused) {
      isPaused = paused;
      sponsorsCarousel.classList.toggle('is-paused', isPaused);
      if (sponsorsToggle) {
        sponsorsToggle.setAttribute('aria-pressed', String(isPaused));
        sponsorsToggle.setAttribute('aria-label', isPaused ? 'Kaydırmayı başlat' : 'Kaydırmayı duraklat');
        if (iconPause && iconPlay) {
          iconPause.hidden = isPaused;
          iconPlay.hidden = !isPaused;
        }
      }
    }

    sponsorsToggle?.addEventListener('click', () => {
      updatePauseState(!isPaused);
    });

    function shiftSlider(direction) {
      const step = 284 * direction;
      const computed = window.getComputedStyle(sponsorsTrack);
      const matrix = new DOMMatrixReadOnly(computed.transform);
      const currentX = matrix.m41;
      const targetX = currentX + step;

      sponsorsTrack.style.animationPlayState = 'paused';
      sponsorsTrack.style.transition = 'transform 0.45s cubic-bezier(0.16, 1, 0.3, 1)';
      sponsorsTrack.style.transform = `translate3d(${targetX}px, 0, 0)`;

      clearTimeout(sponsorsTrack._resumeTimer);
      sponsorsTrack._resumeTimer = setTimeout(() => {
        if (!isPaused && !sponsorsCarousel.matches(':hover')) {
          sponsorsTrack.style.transition = '';
          sponsorsTrack.style.animationPlayState = '';
        }
      }, 1200);
    }

    sponsorsPrev?.addEventListener('click', () => shiftSlider(1));
    sponsorsNext?.addEventListener('click', () => shiftSlider(-1));

    // Pointer (Fare & Dokunmatik) sürükleme
    sponsorsCarousel.addEventListener('pointerdown', (e) => {
      if (e.pointerType === 'mouse' && e.button !== 0) return;
      isDragging = true;
      dragStartX = e.clientX;
      sponsorsCarousel.classList.add('is-dragging');

      const computed = window.getComputedStyle(sponsorsTrack);
      const matrix = new DOMMatrixReadOnly(computed.transform);
      baseOffset = matrix.m41;

      sponsorsTrack.style.animation = 'none';
      sponsorsTrack.style.transition = 'none';
      sponsorsTrack.style.transform = `translate3d(${baseOffset}px, 0, 0)`;
      sponsorsCarousel.setPointerCapture?.(e.pointerId);
    });

    sponsorsCarousel.addEventListener('pointermove', (e) => {
      if (!isDragging) return;
      const dragOffset = e.clientX - dragStartX;
      sponsorsTrack.style.transform = `translate3d(${baseOffset + dragOffset}px, 0, 0)`;
    });

    const stopDragging = (e) => {
      if (!isDragging) return;
      isDragging = false;
      sponsorsCarousel.classList.remove('is-dragging');
      try { sponsorsCarousel.releasePointerCapture?.(e.pointerId); } catch (_) {}

      setTimeout(() => {
        if (!isPaused) {
          sponsorsTrack.style.animation = '';
          sponsorsTrack.style.transition = '';
          sponsorsTrack.style.transform = '';
        }
      }, 800);
    };

    sponsorsCarousel.addEventListener('pointerup', stopDragging);
    sponsorsCarousel.addEventListener('pointercancel', stopDragging);
  }
});

// Medya merkezi: klavye destekli sekmeler ve kaynaklı blog yazıları.
(() => {
  const tabs = [...document.querySelectorAll('.media-tabs [role="tab"]')];
  const select = (tab, focus = false) => {
    tabs.forEach(item => {
      const active = item === tab;
      item.setAttribute('aria-selected', String(active));
      item.tabIndex = active ? 0 : -1;
      document.getElementById(item.getAttribute('aria-controls')).hidden = !active;
    });
    if (focus) tab.focus();
  };
  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => select(tab));
    tab.addEventListener('keydown', event => {
      const directions = { ArrowRight: (index + 1) % tabs.length, ArrowLeft: (index + tabs.length - 1) % tabs.length, Home: 0, End: tabs.length - 1 };
      if (event.key in directions) { event.preventDefault(); select(tabs[directions[event.key]], true); }
    });
  });
  document.querySelectorAll('[data-blog-open]').forEach(button => {
    const dialog = document.getElementById(button.dataset.blogOpen);
    button.addEventListener('click', () => { dialog.showModal(); document.body.classList.add('media-dialog-open'); });
    dialog.querySelector('.media-dialog-close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => { document.body.classList.remove('media-dialog-open'); button.focus(); });
    dialog.addEventListener('click', event => {
      const rect = dialog.getBoundingClientRect();
      if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) dialog.close();
    });
  });
})();
