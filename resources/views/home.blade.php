<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $settings['site_title'] ?? 'Yalova Haremspor • #aynıtutkuyeniheyecan' }}</title>
  <meta name="description" content="{{ $settings['meta_description'] ?? 'Yalova Haremspor Basketbol Kulübü. Bir şehir, bir arma, aynı tutku yeni heyecan.' }}">
  <link rel="icon" href="{{ asset('assets/images/yalova-logo.jpg') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css?v=20261015-laravel') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,600;0,700;0,800;0,900;1,800;1,900&family=Barlow:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Oyuncu İstatistik Modalı Stilleri */
    .player-entry {
      cursor: pointer;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .player-entry:hover {
      transform: translateY(-4px);
    }
    .player-modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(4, 15, 10, 0.85);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.25rem;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    /* OYUNCU İSTATİSTİK POPUP MODALI - EUROLEAGUE / NBA PRO EDITION */
    .player-modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(2, 8, 5, 0.88);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.25rem;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.28s ease, visibility 0.28s ease;
    }
    .player-modal-backdrop.active {
      opacity: 1;
      visibility: visible;
    }
    .player-modal-card {
      background: #06150e;
      border: 1px solid rgba(229, 169, 60, 0.35);
      border-radius: 0;
      width: 100%;
      max-width: 720px;
      max-height: 92vh;
      overflow-y: auto;
      box-shadow: 0 35px 70px -15px rgba(0, 0, 0, 0.95), 0 0 0 1px rgba(255, 255, 255, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.1);
      position: relative;
      color: #fff;
      transform: scale(0.94) translateY(10px);
      transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .player-modal-backdrop.active .player-modal-card {
      transform: scale(1) translateY(0);
    }
    .player-modal-close {
      position: absolute;
      top: 1.15rem;
      right: 1.15rem;
      background: rgba(0, 0, 0, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
      width: 38px;
      height: 38px;
      border-radius: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.25s ease;
      z-index: 15;
    }
    .player-modal-close:hover {
      background: #a70101;
      border-color: #a70101;
      transform: rotate(90deg) scale(1.08);
      box-shadow: 0 0 15px rgba(167, 1, 1, 0.6);
    }
    
    /* Header / Hero */
    .player-modal-header {
      position: relative;
      display: flex;
      gap: 1.5rem;
      padding: 2rem 2rem 1.6rem;
      background: radial-gradient(circle at 10% 20%, rgba(0, 168, 89, 0.22) 0%, transparent 55%), radial-gradient(circle at 90% 80%, rgba(167, 1, 1, 0.25) 0%, transparent 60%), linear-gradient(135deg, #092015 0%, #040d08 100%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      overflow: hidden;
    }
    .player-modal-watermark {
      position: absolute;
      right: 1.5rem;
      top: 50%;
      transform: translateY(-50%);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 11rem;
      font-weight: 900;
      color: #fff;
      opacity: 0.04;
      line-height: 0.8;
      pointer-events: none;
      user-select: none;
      z-index: 1;
    }
    .player-modal-avatar-box {
      width: 120px;
      height: 135px;
      border-radius: 0;
      overflow: hidden;
      background: #020704;
      border: 2px solid #e5a93c;
      box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.7), 0 0 20px rgba(229, 169, 60, 0.25);
      flex-shrink: 0;
      position: relative;
      z-index: 2;
    }
    .player-modal-avatar-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: top;
      display: block;
    }
    .player-modal-jersey-tag {
      position: absolute;
      top: 6px;
      left: 6px;
      background: #e5a93c;
      color: #000;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.85rem;
      font-weight: 900;
      padding: 2px 7px;
      border-radius: 0;
      box-shadow: 0 2px 6px rgba(0,0,0,0.4);
      z-index: 3;
    }
    .player-modal-header-info {
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      z-index: 2;
      flex: 1;
      min-width: 0;
    }
    .player-modal-meta-top {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin-bottom: 0.35rem;
      flex-wrap: wrap;
    }
    .player-modal-team-pill {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.72rem;
      font-weight: 800;
      letter-spacing: 0.8px;
      background: rgba(167, 1, 1, 0.25);
      border: 1px solid rgba(167, 1, 1, 0.5);
      color: #ff9999;
      padding: 2px 8px;
      border-radius: 0;
      text-transform: uppercase;
    }
    .player-modal-pos-pill {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.75rem;
      font-weight: 800;
      letter-spacing: 0.6px;
      background: rgba(229, 169, 60, 0.15);
      border: 1px solid rgba(229, 169, 60, 0.4);
      color: #e5a93c;
      padding: 2px 9px;
      border-radius: 0;
      text-transform: uppercase;
    }
    .player-modal-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 2.2rem;
      font-weight: 900;
      text-transform: uppercase;
      line-height: 1.05;
      margin: 0 0 0.65rem;
      letter-spacing: 0.5px;
      color: #fff;
      text-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }
    .player-modal-bio-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    .bio-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.09);
      border-left: 2px solid #e5a93c;
      padding: 4px 10px;
      border-radius: 0;
      font-size: 0.78rem;
      color: rgba(255, 255, 255, 0.7);
    }
    .bio-chip svg {
      width: 12px;
      height: 12px;
      color: #e5a93c;
      flex-shrink: 0;
    }
    .bio-chip strong {
      color: #fff;
      font-weight: 700;
    }

    /* Modal Body & Stats Sections */
    .player-modal-body {
      padding: 1.6rem 2rem 2rem;
    }
    .player-modal-stats-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      padding-bottom: 0.65rem;
      margin-bottom: 1.25rem;
    }
    .player-modal-stats-title h4 {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.15rem;
      font-weight: 900;
      letter-spacing: 0.8px;
      color: #fff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .player-modal-stats-title h4 span {
      color: #e5a93c;
    }
    .player-modal-source-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: #a0aec0;
      text-transform: uppercase;
    }
    .pulse-dot {
      width: 7px;
      height: 7px;
      background: #00A859;
      box-shadow: 0 0 8px #00A859;
      display: inline-block;
    }

    /* Section 1: Core Big 4 Stats */
    .player-stats-hero-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 0.85rem;
      margin-bottom: 1.25rem;
    }
    .stat-hero-card {
      background: rgba(0, 0, 0, 0.35);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 0;
      padding: 0.95rem 0.75rem;
      text-align: center;
      position: relative;
      overflow: hidden;
      transition: transform 0.2s, border-color 0.2s;
    }
    .stat-hero-card:hover {
      transform: translateY(-2px);
    }
    .stat-hero-card.gold-theme {
      background: linear-gradient(180deg, rgba(229, 169, 60, 0.12) 0%, rgba(0, 0, 0, 0.4) 100%);
      border-color: rgba(229, 169, 60, 0.4);
      border-left: 3px solid #e5a93c;
    }
    .stat-hero-card.gold-theme .stat-hero-val {
      color: #e5a93c;
    }
    .stat-hero-card.red-theme {
      background: linear-gradient(180deg, rgba(167, 1, 1, 0.16) 0%, rgba(0, 0, 0, 0.4) 100%);
      border-color: rgba(167, 1, 1, 0.45);
      border-left: 3px solid #a70101;
    }
    .stat-hero-card.red-theme .stat-hero-val {
      color: #fc8181;
    }
    .stat-hero-card.green-theme {
      background: linear-gradient(180deg, rgba(0, 168, 89, 0.14) 0%, rgba(0, 0, 0, 0.4) 100%);
      border-color: rgba(0, 168, 89, 0.4);
      border-left: 3px solid #00A859;
    }
    .stat-hero-card.green-theme .stat-hero-val {
      color: #68d391;
    }
    .stat-hero-card.blue-theme {
      background: linear-gradient(180deg, rgba(66, 153, 225, 0.14) 0%, rgba(0, 0, 0, 0.4) 100%);
      border-color: rgba(66, 153, 225, 0.4);
      border-left: 3px solid #4299e1;
    }
    .stat-hero-card.blue-theme .stat-hero-val {
      color: #90cdf4;
    }
    .stat-hero-val {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 2.3rem;
      font-weight: 900;
      line-height: 1;
      letter-spacing: -0.5px;
    }
    .stat-hero-lbl {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.78rem;
      font-weight: 800;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.75);
      margin-top: 0.35rem;
    }

    /* Section 2: Shooting Percentage Bars */
    .player-stats-shooting-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.85rem;
      margin-bottom: 1.25rem;
    }
    .shooting-card {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.07);
      border-radius: 0;
      padding: 0.75rem 0.9rem;
    }
    .shooting-header {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      margin-bottom: 0.45rem;
    }
    .shooting-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.8rem;
      font-weight: 800;
      letter-spacing: 0.5px;
      color: rgba(255, 255, 255, 0.7);
      text-transform: uppercase;
    }
    .shooting-pct {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.25rem;
      font-weight: 900;
      color: #fff;
    }
    .shooting-progress-track {
      width: 100%;
      height: 5px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 0;
      overflow: hidden;
    }
    .shooting-progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #00A859 0%, #e5a93c 100%);
      border-radius: 0;
      transition: width 0.4s ease;
    }

    /* Section 3: Secondary Stats 5-Grid */
    .player-stats-subgrid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 0.65rem;
    }
    .substat-card {
      background: rgba(0, 0, 0, 0.25);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 0;
      padding: 0.65rem 0.4rem;
      text-align: center;
      transition: background 0.2s;
    }
    .substat-card:hover {
      background: rgba(255, 255, 255, 0.04);
    }
    .substat-val {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.35rem;
      font-weight: 800;
      color: #fff;
      line-height: 1.1;
    }
    .substat-lbl {
      font-size: 0.7rem;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.55);
      text-transform: uppercase;
      letter-spacing: 0.4px;
      margin-top: 0.2rem;
    }

    @media (max-width: 680px) {
      .player-stats-hero-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .player-stats-shooting-row {
        grid-template-columns: 1fr;
      }
      .player-stats-subgrid {
        grid-template-columns: repeat(3, 1fr);
      }
      .player-modal-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 2.2rem 1.25rem 1.5rem;
      }
      .player-modal-meta-top,
      .player-modal-bio-grid {
        justify-content: center;
      }
      .player-modal-body {
        padding: 1.25rem;
      }
    }
    .admin-float-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #a70101;
      border: 1px solid #e5a93c;
      color: #fff;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 0.9rem;
      letter-spacing: 0.5px;
      padding: 8px 16px;
      border-radius: 0;
      text-decoration: none;
      box-shadow: 0 4px 14px rgba(0,0,0,0.4);
      z-index: 999;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: transform 0.2s, background 0.2s;
    }
    .admin-float-btn:hover {
      background: #c90202;
      transform: translateY(-2px);
    }

    /* Bilet Satış Noktaları Kompakt Bölümü (Zümrüt Yeşil & Kırmızı Tema) */
    .ticket-points-strip {
      padding: 3.5rem 0;
      background: radial-gradient(circle at 18% 30%, rgba(16, 185, 129, 0.22) 0%, transparent 45%),
                  radial-gradient(circle at 85% 70%, rgba(5, 150, 105, 0.18) 0%, transparent 40%),
                  linear-gradient(135deg, #011d13 0%, #032e1e 50%, #01140c 100%) !important;
      border-top: 3px solid #a70101 !important;
      border-bottom: 1px solid rgba(16, 185, 129, 0.25) !important;
      position: relative;
      overflow: hidden;
    }
    .ticket-points-strip .container {
      position: relative;
      z-index: 2;
    }
    .ticket-cinar-leaf {
      position: absolute;
      top: -60px;
      right: -60px;
      width: 420px;
      height: 420px;
      filter: invert(1) drop-shadow(0 0 14px rgba(16, 185, 129, 0.35));
      opacity: 0.065;
      pointer-events: none;
      animation: cinarLeafFloat1 20s ease-in-out infinite alternate;
      z-index: 1;
    }
    .ticket-points-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1.75rem;
    }
    .ticket-points-title-group {
      max-width: 650px;
    }
    .ticket-points-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(167, 1, 1, 0.18);
      border: 1px solid rgba(167, 1, 1, 0.45);
      border-left: 3px solid #a70101;
      color: #fca5a5;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 3px 10px;
      border-radius: 0;
      margin-bottom: 0.5rem;
    }
    .ticket-points-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 2.1rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
      color: #fff;
      line-height: 1;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
    }
    .ticket-points-title span {
      color: #a70101;
    }
    .ticket-points-desc {
      color: rgba(255, 255, 255, 0.75);
      font-size: 0.88rem;
      margin: 0.4rem 0 0;
      line-height: 1.4;
    }
    .ticket-points-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
      gap: 1rem;
    }
    .ticket-point-card {
      background: rgba(2, 24, 15, 0.88);
      border: 1px solid rgba(16, 185, 129, 0.22);
      border-left: 4px solid #a70101;
      border-radius: 0;
      padding: 1.15rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(10px);
    }
    .ticket-point-card:hover {
      transform: translateY(-3px);
      border-color: rgba(16, 185, 129, 0.45);
      border-left-color: #ff3b30;
      box-shadow: 0 14px 28px -6px rgba(0, 0, 0, 0.7), 0 0 20px rgba(5, 150, 105, 0.25);
    }
    .ticket-point-head {
      display: flex;
      gap: 0.85rem;
      align-items: center;
      margin-bottom: 0.85rem;
    }
    .ticket-point-logo-box {
      width: 54px;
      height: 54px;
      border-radius: 0;
      background: rgba(0, 0, 0, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 5px;
      flex-shrink: 0;
    }
    .ticket-point-logo-box img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    .ticket-point-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.15rem;
      font-weight: 800;
      color: #fff;
      text-transform: uppercase;
      line-height: 1.15;
      margin: 0;
      letter-spacing: 0.3px;
    }
    .ticket-point-hours {
      font-size: 0.72rem;
      color: #6ee7b7;
      font-weight: 700;
      margin-top: 3px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .ticket-point-address {
      font-size: 0.82rem;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.4;
      margin-bottom: 0.85rem;
      flex-grow: 1;
    }
    .ticket-point-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.5rem;
      padding-top: 0.75rem;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      font-size: 0.78rem;
    }
    .ticket-point-phone {
      color: rgba(255, 255, 255, 0.65);
      display: flex;
      align-items: center;
      gap: 4px;
      text-decoration: none;
      transition: color 0.2s;
    }
    .ticket-point-phone:hover {
      color: #6ee7b7;
    }
    .ticket-point-map-btn {
      background: linear-gradient(135deg, #a70101 0%, #c90202 100%);
      border: 1px solid #ff3b30;
      color: #fff;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 0.82rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      padding: 5px 12px;
      border-radius: 0;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
      margin-left: auto;
      box-shadow: 0 4px 10px rgba(167, 1, 1, 0.35);
    }
    .ticket-point-map-btn:hover {
      background: #e00303;
      border-color: #ff5555;
      transform: scale(1.03);
      box-shadow: 0 6px 14px rgba(167, 1, 1, 0.5);
    }
    .matchday-ticket-btn {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: #a7f3d0 !important;
      font-weight: 700;
      padding: 0.4rem 0.85rem;
      border-radius: 0;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: background 0.2s, color 0.2s, border-color 0.2s;
      font-size: 0.85rem;
    }
    .matchday-ticket-btn:hover {
      background: #10b981;
      border-color: #10b981;
      color: #011d13 !important;
    }

    /* ========================================================
       OYUNCULARIMIZ SECTION - KOYU ZÜMRÜT YEŞİL & ÇINAR TEMASI
       ======================================================== */
    section#oyuncular,
    .roster-section {
      background: radial-gradient(circle at 18% 30%, rgba(16, 185, 129, 0.22) 0%, transparent 45%),
                  radial-gradient(circle at 85% 70%, rgba(5, 150, 105, 0.18) 0%, transparent 40%),
                  linear-gradient(135deg, #011d13 0%, #032e1e 50%, #01140c 100%) !important;
      border-top: 3px solid #a70101 !important;
      border-bottom: 1px solid rgba(16, 185, 129, 0.25) !important;
      padding: 75px 0 !important;
      position: relative;
      overflow: hidden;
    }
    section#oyuncular::before,
    .roster-section::before {
      display: none !important;
    }
    section#oyuncular .container {
      position: relative;
      z-index: 2;
    }

    /* Çınar Ağacı ve Masaüstündeki cinar.png Opak Animasyonlu Sahne */
    .roster-cinar-scene {
      position: absolute;
      inset: 0;
      pointer-events: none;
      overflow: hidden;
      z-index: 1;
    }
    .cinar-leaf-img {
      position: absolute;
      pointer-events: none;
      user-select: none;
      filter: invert(1) drop-shadow(0 0 14px rgba(16, 185, 129, 0.35));
      transform-origin: center center;
    }
    .cinar-leaf-img-1 {
      top: -50px;
      left: -90px;
      width: 520px;
      height: 520px;
      opacity: 0.085;
      animation: cinarLeafFloat1 18s ease-in-out infinite alternate;
    }
    .cinar-leaf-img-2 {
      bottom: -110px;
      right: -80px;
      width: 580px;
      height: 580px;
      opacity: 0.075;
      animation: cinarLeafFloat2 22s ease-in-out infinite alternate;
    }
    .cinar-leaf-img-3 {
      top: 10%;
      left: 36%;
      width: 320px;
      height: 320px;
      opacity: 0.045;
      animation: cinarLeafFloat1 25s ease-in-out infinite alternate-reverse;
    }
    .cinar-ambient-aura {
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.09) 0%, transparent 65%);
      animation: emeraldPulse 9s ease-in-out infinite alternate;
    }

    @keyframes cinarTreeSway {
      0% { transform: rotate(0deg) scale(1); }
      50% { transform: rotate(1.2deg) scale(1.025); }
      100% { transform: rotate(-0.8deg) scale(1.01); }
    }
    @keyframes cinarLeafFloat1 {
      0% { transform: translateY(0) rotate(0deg); opacity: 0.06; }
      50% { transform: translateY(-24px) rotate(6deg); opacity: 0.095; }
      100% { transform: translateY(12px) rotate(-4deg); opacity: 0.07; }
    }
    @keyframes cinarLeafFloat2 {
      0% { transform: translateY(0) rotate(0deg); opacity: 0.04; }
      50% { transform: translateY(22px) rotate(-8deg); opacity: 0.075; }
      100% { transform: translateY(-16px) rotate(5deg); opacity: 0.05; }
    }
    @keyframes emeraldPulse {
      0% { opacity: 0.55; }
      100% { opacity: 1; }
    }

    .roster-section .players-heading {
      border-bottom: 1px solid rgba(16, 185, 129, 0.2) !important;
      padding-bottom: 24px !important;
      margin-bottom: 28px !important;
    }
    .roster-section .players-heading h2 {
      color: #ffffff !important;
      font-size: 2.5rem !important;
      letter-spacing: 0.5px !important;
      text-shadow: 0 2px 12px rgba(0, 0, 0, 0.6);
    }
    .roster-section .players-heading h2 span {
      color: #a70101 !important;
    }
    .roster-section .roster-count {
      color: #a70101 !important;
      font-size: 3rem !important;
      line-height: 1 !important;
    }
    .roster-section .roster-count small {
      color: rgba(255, 255, 255, 0.75) !important;
    }
    .roster-section .roster-heading-note p {
      color: rgba(255, 255, 255, 0.8) !important;
      border-left-color: #a70101 !important;
    }

    /* ========================================================
       OYUNCU VİTRİNİ & İSTATİSTİK ALANI (ZÜMRÜT ARENA)
       ======================================================== */
    .player-showcase-container {
      background: rgba(2, 22, 14, 0.95);
      border: 1px solid rgba(16, 185, 129, 0.28);
      border-radius: 0;
      box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.95), 0 0 45px rgba(5, 150, 105, 0.25);
      margin-top: 1rem;
      display: grid;
      grid-template-columns: 320px 1fr 310px;
      min-height: 500px;
      overflow: hidden;
      backdrop-filter: blur(12px);
    }

    /* 1. SOL: Oyuncu Fotoğraf Sahnesi */
    .showcase-stage-card {
      position: relative;
      height: 500px;
      background: #01120a;
      border-right: 1px solid rgba(16, 185, 129, 0.18);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }
    .showcase-stage-watermark {
      position: absolute;
      top: -10px;
      left: 10px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 15rem;
      font-weight: 900;
      color: #fff;
      opacity: 0.04;
      line-height: 0.8;
      pointer-events: none;
      user-select: none;
      z-index: 1;
    }
    .showcase-photo-frame {
      position: absolute;
      inset: 0;
      z-index: 2;
      background: #01120a;
    }
    .showcase-photo-frame img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: top center;
      display: block;
      transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
    }
    /* Fotoğraf üzerine binen koyu zümrüt gölge */
    .showcase-stage-card::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 65%;
      background: linear-gradient(to top, rgba(1, 22, 14, 0.98) 0%, rgba(1, 22, 14, 0.75) 45%, rgba(1, 22, 14, 0.2) 80%, transparent 100%);
      z-index: 3;
      pointer-events: none;
    }

    /* YENİ OYUNCU İSİM & KİMLİK ROZETİ (GELİŞMİŞ TİPOGRAFİ) */
    .showcase-name-plate {
      position: relative;
      z-index: 4;
      width: 100%;
      padding: 1.1rem 1.25rem 1.25rem;
      background: linear-gradient(180deg, rgba(2, 38, 23, 0) 0%, rgba(1, 28, 18, 0.94) 28%, #01180f 100%);
      border-top: 1px solid rgba(16, 185, 129, 0.25);
      backdrop-filter: blur(8px);
      transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .showcase-name-top-bar {
      display: flex;
      align-items: center;
      gap: 0.65rem;
      margin-bottom: 0.35rem;
    }
    .showcase-num-badge {
      background: linear-gradient(135deg, #a70101 0%, #d31212 100%);
      color: #ffffff;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.65rem;
      font-weight: 900;
      padding: 0.15rem 0.65rem;
      border-radius: 0;
      line-height: 1;
      letter-spacing: -0.5px;
      box-shadow: 0 4px 14px rgba(167, 1, 1, 0.5);
      border-top: 2px solid #ff4d4d;
    }
    .showcase-pos-chip {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.85rem;
      font-weight: 800;
      color: #6ee7b7;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    .showcase-pos-chip::before {
      content: '';
      display: inline-block;
      width: 6px;
      height: 6px;
      background: #10b981;
      border-radius: 50%;
      box-shadow: 0 0 8px #10b981;
    }
    .showcase-full-name-wrap {
      display: flex;
      flex-direction: column;
      line-height: 0.95;
      margin-top: 0.25rem;
    }
    .showcase-first-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.82);
      text-transform: uppercase;
      letter-spacing: 1.5px;
    }
    .showcase-last-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 2.2rem;
      font-weight: 900;
      color: #ffffff;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      line-height: 1;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.85);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .showcase-name-subline {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 0.5rem;
      padding-top: 0.45rem;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      font-size: 0.72rem;
      color: rgba(255, 255, 255, 0.55);
      font-weight: 700;
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }
    .showcase-name-subline strong {
      color: #34d399;
    }

    /* 2. ORTA: İstatistik ve Biyografi Alanı */
    .showcase-stats-panel {
      padding: 1.7rem 1.9rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      border-right: 1px solid rgba(16, 185, 129, 0.18);
      background: rgba(1, 24, 15, 0.85);
    }
    .showcase-stats-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.5rem;
      padding-bottom: 0.85rem;
      border-bottom: 1px solid rgba(16, 185, 129, 0.18);
      margin-bottom: 1.1rem;
    }
    .showcase-stats-header h3 {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.3rem;
      font-weight: 900;
      color: #fff;
      margin: 0;
      letter-spacing: 0.6px;
    }
    .showcase-stats-header h3 span {
      color: #ff4d4d;
    }
    .showcase-bio-strip {
      font-size: 0.82rem;
      color: rgba(255, 255, 255, 0.7);
      letter-spacing: 0.3px;
    }
    .showcase-bio-strip strong {
      color: #fff;
      font-weight: 700;
    }
    .showcase-bio-strip .sep {
      color: #a70101;
      margin: 0 0.4rem;
    }
    .showcase-stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 0.8rem;
    }
    .stat-box {
      background: rgba(1, 18, 12, 0.6);
      border: 1px solid rgba(16, 185, 129, 0.15);
      border-radius: 0;
      padding: 1rem 0.55rem;
      text-align: center;
      transition: border-color 0.25s, transform 0.25s;
    }
    .stat-box:hover {
      border-color: rgba(16, 185, 129, 0.45);
      transform: translateY(-2px);
    }
    .stat-box.red-stat,
    .stat-box.gold-stat {
      border-left: 3px solid #a70101;
      background: linear-gradient(180deg, rgba(167, 1, 1, 0.25) 0%, rgba(1, 22, 14, 0.7) 100%);
    }
    .stat-box.green-stat {
      border-left: 3px solid #10b981;
      background: linear-gradient(180deg, rgba(16, 185, 129, 0.22) 0%, rgba(1, 22, 14, 0.7) 100%);
    }
    .stat-box-val {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 2.25rem;
      font-weight: 900;
      line-height: 1;
      color: #fff;
      letter-spacing: -0.3px;
      transition: transform 0.2s;
    }
    .red-stat .stat-box-val,
    .gold-stat .stat-box-val {
      color: #ff4d4d;
    }
    .green-stat .stat-box-val {
      color: #34d399;
    }
    .stat-box-lbl {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.72rem;
      font-weight: 800;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.6);
      margin-top: 0.35rem;
    }

    /* 3. SAĞ: İnteraktif Kadro Listesi (Zümrüt Yeşil & Kırmızı Vurgu) */
    .showcase-roster-list {
      background: #01170e;
      display: flex;
      flex-direction: column;
      height: 500px;
      overflow-y: auto;
      border-radius: 0;
      scrollbar-width: thin;
      scrollbar-color: #a70101 rgba(0, 0, 0, 0.5);
    }
    .showcase-roster-list::-webkit-scrollbar {
      width: 5px;
    }
    .showcase-roster-list::-webkit-scrollbar-thumb {
      background: #a70101;
    }
    .showcase-roster-item {
      display: flex;
      align-items: center;
      padding: 0.95rem 1.25rem;
      background: #022014;
      border-bottom: 1px solid rgba(16, 185, 129, 0.1);
      cursor: pointer;
      position: relative;
      transition: background 0.2s, transform 0.2s, padding-left 0.2s;
      user-select: none;
    }
    .showcase-roster-item:hover {
      background: #033220;
      padding-left: 1.45rem;
    }
    .showcase-roster-num {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 2.15rem;
      font-weight: 900;
      color: #fff;
      min-width: 55px;
      letter-spacing: -0.5px;
      line-height: 1;
    }
    .showcase-roster-details {
      flex: 1;
      min-width: 0;
    }
    .showcase-roster-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.15rem;
      line-height: 1.15;
      letter-spacing: 0.4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      color: #fff;
    }
    .showcase-roster-name .r-first {
      font-weight: 600;
      opacity: 0.85;
      text-transform: uppercase;
      margin-right: 3px;
    }
    .showcase-roster-name .r-last {
      font-weight: 900;
      text-transform: uppercase;
    }
    .showcase-roster-pos {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 0.75rem;
      color: #6ee7b7;
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.5px;
      margin-top: 2px;
    }
    .showcase-roster-arrow {
      color: rgba(255, 255, 255, 0.35);
      display: flex;
      align-items: center;
      transition: transform 0.2s, color 0.2s;
      margin-left: 0.5rem;
    }
    .showcase-roster-item:hover .showcase-roster-arrow {
      color: #fff;
      transform: translateX(3px);
    }

    /* AKTİF OYUNCU (Kulüp Kırmızısı Vurgu, Sol Ok Kulakçığı ve Işıma) */
    .showcase-roster-item.active {
      background: linear-gradient(135deg, #a70101 0%, #c90202 100%) !important;
      color: #ffffff !important;
      box-shadow: 0 4px 18px rgba(167, 1, 1, 0.45);
    }
    .showcase-roster-item.active .showcase-roster-num,
    .showcase-roster-item.active .showcase-roster-name,
    .showcase-roster-item.active .showcase-roster-name .r-first,
    .showcase-roster-item.active .showcase-roster-name .r-last {
      color: #ffffff !important;
      opacity: 1 !important;
      font-weight: 900 !important;
    }
    .showcase-roster-item.active .showcase-roster-pos {
      color: rgba(255, 255, 255, 0.92) !important;
      font-weight: 800 !important;
    }
    .showcase-roster-item.active .showcase-roster-arrow {
      color: #ffffff !important;
      transform: translateX(4px);
    }
    .showcase-roster-item.active::before {
      content: '';
      position: absolute;
      left: -11px;
      top: 50%;
      transform: translateY(-50%);
      width: 0;
      height: 0;
      border-top: 11px solid transparent;
      border-bottom: 11px solid transparent;
      border-right: 11px solid #a70101;
      z-index: 10;
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
      .player-showcase-container {
        grid-template-columns: 280px 1fr 260px;
      }
    }
    @media (max-width: 950px) {
      .player-showcase-container {
        grid-template-columns: 1fr;
        min-height: auto;
      }
      .showcase-stage-card {
        height: 380px;
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      }
      .showcase-stats-panel {
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      }
      .showcase-roster-list {
        height: 350px;
      }
      .showcase-roster-item.active::before {
        display: none;
      }
    }
    @media (max-width: 580px) {
      .showcase-stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
  </style>
</head>
<body>
  <!-- Ana Header / Menü -->
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="{{ route('home') }}" aria-label="Ana sayfa">
        <img src="{{ asset('assets/images/yalova-logo.jpg') }}" alt="Yalova Haremspor Kulübü logosu">
        <div class="brand-text">
          <span class="city-name">{{ $settings['city_name'] ?? 'YALOVA' }}</span>
          <b class="club-name">{{ $settings['club_name'] ?? 'HAREMSPOR' }}</b>
        </div>
      </a>

      <nav id="nav" aria-label="Ana menü">
        <a href="#kulup">KULÜP</a>
        <a href="#maclar">MAÇ MERKEZİ</a>
        <a href="#bilet-noktalari">BİLET NOKTALARI</a>
        <a href="#sponsorlar">SPONSORLAR</a>
        <a href="#oyuncular">OYUNCULAR</a>
        <a href="#kimlik">KİMLİĞİMİZ</a>
        @auth
        <a href="{{ route('admin.dashboard') }}" style="color: #6ee7b7; font-weight: 700;">YÖNETİM</a>
        @endauth
      </nav>

      <a class="navcta" href="#maclar">{{ $settings['hero_cta_text'] ?? 'MAÇ MERKEZİ' }} <span aria-hidden="true">↗</span></a>
      <button class="menu" aria-label="Menüyü aç" aria-expanded="false">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </div>
  </header>

  <main>
    <!-- Hero Bölümü -->
    <section class="hero-arena" aria-labelledby="hero-title">
      <div class="arena-pattern" aria-hidden="true"></div>
      <div class="arena-photo">
        <img src="{{ asset($settings['hero_image'] ?? 'assets/images/saha.jpg') }}" alt="Yalova Haremspor mücadelesi" fetchpriority="high">
        <div class="arena-photo-reveal" aria-hidden="true">
          <span class="reveal-cloud reveal-cloud-left"></span>
          <span class="reveal-cloud reveal-cloud-center"></span>
          <span class="reveal-cloud reveal-cloud-right"></span>
        </div>
        <svg class="arena-photo-marks" viewBox="0 0 1000 800" fill="none" aria-hidden="true" focusable="false">
          <defs><pattern id="photo-dot-pattern" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="1" fill="#ffffff" fill-opacity=".55"/></pattern></defs>
          <path class="photo-corner" d="M250 100h-50v50M810 625v50h-50" stroke="#a70101" stroke-width="3"/>
          <path d="m760 135 16-24m0 24 16-24m0 24 16-24m0 24 16-24" stroke="#a70101" stroke-width="5" opacity=".8"/>
          <rect x="205" y="480" width="90" height="126" fill="url(#photo-dot-pattern)"/>
          <path d="M270 655h145" stroke="#ffffff" stroke-width="1" stroke-dasharray="4 7" opacity=".6"/>
          <path d="M825 250h18m-9-9v18M310 190h16m-8-8v16" stroke="#ffffff" stroke-width="1.5" opacity=".65"/>
          <path d="M765 665h20" stroke="#a70101" stroke-width="5"/>
        </svg>
      </div>
      <nav class="arena-social" aria-label="Kulüp sosyal medya hesapları">
        @if(!empty($settings['social_instagram']))
        <a href="{{ $settings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/></svg>
        </a>
        @endif
        @if(!empty($settings['social_x']))
        <a href="{{ $settings['social_x'] }}" target="_blank" rel="noopener noreferrer" aria-label="X" title="X">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 3h4l12 18h-4L4 3ZM20 3 4 21"/></svg>
        </a>
        @endif
        @if(!empty($settings['social_youtube']))
        <a href="{{ $settings['social_youtube'] }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" title="YouTube">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 5 3-5 3V9Z" fill="currentColor" stroke="none"/></svg>
        </a>
        @endif
      </nav>
      <div class="arena-content">
        <h1 id="hero-title">
          <span>{{ $settings['hero_title_1'] ?? 'AYNI TUTKU.' }}</span>
          <span class="arena-finale">{{ $settings['hero_title_2'] ?? 'YENİ HEYECAN.' }}</span>
        </h1>
        <p>{!! nl2br(e($settings['hero_description'] ?? "Sesimiz tribünde. Gücümüz sahada.\nSon düdüğe kadar, hep birlikte.")) !!}</p>
        <a class="arena-cta" href="{{ $settings['hero_cta_url'] ?? '#maclar' }}">{{ $settings['hero_cta_text'] ?? 'MAÇ MERKEZİ' }} <span aria-hidden="true">↗</span></a>
      </div>
    </section>
    <div class="arena-scroll-divider">
      <a class="arena-scroll" href="#maclar" aria-label="Aşağı kaydır, maç merkezine git">
        <svg viewBox="0 0 100 56" fill="none" aria-hidden="true">
          <path class="arena-scroll-notch" d="M18 28h64L50 54Z" fill="#fff"/>
          <path class="arena-scroll-edge" d="M0 28h18l32 26 32-26h18" stroke-width="1"/>
          <path class="arena-scroll-arrow" d="M50 14v22m-6-6 6 6 6-6" stroke="currentColor" stroke-width="1.7"/>
        </svg>
      </a>
    </div>

    <!-- Maç Merkezi -->
    <section class="matches section matchday" id="maclar" aria-label="Maç merkezi">
      <div class="container">
        <!-- Sıradaki Maç Posteri -->
        <article class="matchday-poster" aria-label="Sıradaki karşılaşma">
          <div class="matchday-duel">
            <div class="matchday-home">
              <img src="{{ asset('assets/images/yalova-logo.jpg') }}" alt="Yalova Haremspor arması">
              <h4>{{ $settings['city_name'] ?? 'YALOVA' }}<br>{{ $settings['club_name'] ?? 'HAREMSPOR' }}</h4>
            </div>
            <span class="matchday-vs" aria-label="karşı">VS</span>
            <div class="matchday-away">
              <span id="next-match-location">{{ $nextMatch ? ($nextMatch->is_home ? 'RAKİP · DEPLASMAN' : 'RAKİP · EV SAHİBİ') : 'RAKİP' }}</span>
              <h4 id="next-match-opponent">{{ $nextMatch ? $nextMatch->opponent : 'YAKINDA AÇIKLANACAK.' }}</h4>
            </div>
          </div>
          <div class="matchday-info">
            <p><span>TARİH / SAAT</span><b id="next-match-date">{{ $nextMatch ? $nextMatch->formatted_date : 'Açıklanacak' }}</b></p>
            <p><span>SALON</span><b id="next-match-venue">{{ $nextMatch->venue ?? 'Yalova 90. Yıl Spor Salonu' }}</b></p>
            <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;">
              <a href="#fixture-title">FİKSTÜRÜ GÖR <span aria-hidden="true">↗</span></a>
          
            </div>
          </div>
        </article>

        <!-- Sezon İstatistikleri / Puan Durumu Özeti -->
        <div class="matchday-record" id="season-record" aria-label="Sezon istatistikleri">
          <p><b>{{ $standing->rank ?? 2 }}.</b><span>LİG SIRASI</span></p>
          <p><b>{{ $standing->won ?? $winCount }}</b><span>GALİBİYET</span></p>
          <p><b>{{ $standing->lost ?? $lossCount }}</b><span>MAĞLUBİYET</span></p>
          <p><b>{{ $standing->points ?? (($standing->won ?? 0) * 2 + ($standing->lost ?? 0)) }}</b><span>PUAN</span></p>
          <p><b>{{ $standing->points_for ?? 353 }} / {{ $standing->points_against ?? 294 }}</b><span>ATILAN / YENİLEN</span></p>
        </div>

        <!-- Fikstür ve Sonuçlar -->
        <section class="matchday-schedule" aria-labelledby="fixture-title">
          <div class="schedule-top">
            <h3 id="fixture-title" class="section-title">FİKSTÜR <span>/</span></h3>
            <div class="fixture-tabs" role="tablist" aria-label="Maç programı görünümü">
              <button id="fixture-tab" role="tab" aria-selected="true" aria-controls="fixture-panel" tabindex="0" class="active" data-panel="fixture-panel">GELECEK MAÇLAR</button>
              <button id="results-tab" role="tab" aria-selected="false" aria-controls="results-panel" tabindex="-1" data-panel="results-panel">SONUÇLAR</button>
            </div>
          </div>

          <!-- Gelecek Maçlar Paneli -->
          <div id="fixture-panel" role="tabpanel" aria-labelledby="fixture-tab">
            <div class="schedule-table-wrap">
              <table class="schedule-table">
                <caption id="fixture-caption">2026–2027 TBL Sezonu Karşılaşmaları</caption>
                <thead>
                  <tr>
                    <th scope="col">TARİH</th>
                    <th scope="col">KARŞILAŞMA</th>
                    <th scope="col">KONUM</th>
                    <th scope="col">SAAT</th>
                  </tr>
                </thead>
                <tbody id="fixture-rows">
                  @forelse($upcomingMatches as $match)
                  <tr>
                    <td>{{ $match->match_date ? $match->match_date->format('d.m.Y') : 'Tarih Belirsiz' }}</td>
                    <td><strong>{{ $match->home_team }}</strong> - {{ $match->away_team }}</td>
                    <td>{{ $match->is_home ? 'İç Saha' : 'Deplasman' }}</td>
                    <td>{{ $match->match_date ? $match->match_date->format('H:i') : '—' }}</td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4">Yaklaşan yeni bir karşılaşma bulunmuyor.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <!-- Sonuçlar Paneli -->
          <div id="results-panel" role="tabpanel" aria-labelledby="results-tab" hidden>
            <div class="schedule-table-wrap">
              <table class="schedule-table">
                <caption>Tamamlanan lig karşılaşmaları.</caption>
                <thead>
                  <tr>
                    <th scope="col">TARİH</th>
                    <th scope="col">KARŞILAŞMA</th>
                    <th scope="col">SONUÇ</th>
                    <th scope="col">SKOR</th>
                  </tr>
                </thead>
                <tbody id="result-rows">
                  @forelse($pastMatches as $match)
                  <tr>
                    <td>{{ $match->match_date ? $match->match_date->format('d.m.Y') : '—' }}</td>
                    <td><strong>{{ $match->home_team }}</strong> - {{ $match->away_team }}</td>
                    <td>
                      @if($match->is_winner === true)
                        <span style="color:#48bb78;font-weight:700;">GALİBİYET</span>
                      @elseif($match->is_winner === false)
                        <span style="color:#f56565;font-weight:700;">MAĞLUBİYET</span>
                      @else
                        <span>TAMAMLANDI</span>
                      @endif
                    </td>
                    <td><b>{{ $match->home_score }} - {{ $match->away_score }}</b></td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4">Bu sezon için henüz maç sonucu bulunmuyor.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
          <p class="matchday-source">
            <a href="https://www.tbf.org.tr" target="_blank" rel="noopener noreferrer">VERİ KAYNAĞI: TBF ↗</a>
          </p>
        </section>
      </div>
    </section>

    <!-- Bilet Satış Noktaları (Kompakt Alan) -->
    <section class="ticket-points-strip section" id="bilet-noktalari" aria-labelledby="ticket-points-title">
      <img class="ticket-cinar-leaf" src="{{ asset('assets/images/cinar.png') }}" alt="" loading="lazy" aria-hidden="true">
      <div class="container">
        <div class="ticket-points-top">
          <div class="ticket-points-title-group">
            <span class="ticket-points-badge">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
              RESMİ GİŞE VE İŞLETMELER
            </span>
            <h2 id="ticket-points-title" class="ticket-points-title">BİLET SATIŞ NOKTALARIMIZ <span>/</span></h2>
            <p class="ticket-points-desc">
              Yalova Haremspor iç saha karşılaşmalarının maç biletlerini aşağıdaki yetkili satış noktalarımızdan ve gişelerden temin edebilirsiniz.
            </p>
          </div>
          @auth
            <a href="{{ route('admin.ticket-points.index') }}" class="matchday-ticket-btn" style="font-size: 0.78rem;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
              <span>Noktaları Yönet</span>
            </a>
          @endauth
        </div>

        <div class="ticket-points-grid">
          @forelse($ticketPoints as $point)
            <article class="ticket-point-card">
              <div>
                <div class="ticket-point-head">
                  <div class="ticket-point-logo-box">
                    <img src="{{ $point->logo }}" alt="{{ $point->name }}" loading="lazy">
                  </div>
                  <div>
                    <h3 class="ticket-point-name">{{ $point->name }}</h3>
                    @if($point->working_hours)
                      <div class="ticket-point-hours">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>{{ $point->working_hours }}</span>
                      </div>
                    @endif
                  </div>
                </div>

                @if($point->address)
                  <p class="ticket-point-address">{{ $point->address }}</p>
                @endif
              </div>

              <div class="ticket-point-footer">
                @if($point->phone)
                  <a href="tel:{{ preg_replace('/[^0-9+]/', '', $point->phone) }}" class="ticket-point-phone" title="Telefon ile ara">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $point->phone }}</span>
                  </a>
                @else
                  <span style="color: rgba(255,255,255,0.4); font-size: 0.75rem;">Yetkili Nokta</span>
                @endif

                @if($point->maps_url)
                  <a href="{{ $point->maps_url }}" target="_blank" rel="noopener noreferrer" class="ticket-point-map-btn" title="{{ $point->name }} konumunu haritada aç">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Konum ↗</span>
                  </a>
                @endif
              </div>
            </article>
          @empty
            <div style="grid-column: 1 / -1; background: rgba(0,0,0,0.25); border: 1px dashed rgba(255,255,255,0.15); border-radius: 0; padding: 2rem; text-align: center; color: rgba(255,255,255,0.7);">
              <p style="margin: 0; font-size: 0.95rem;">Resmi bilet satış noktaları yakında duyurulacaktır.</p>
              <small style="color: rgba(255,255,255,0.4); display: block; margin-top: 4px;">Maç günü salon gişelerinden biletinizi temin edebilirsiniz.</small>
            </div>
          @endforelse
        </div>
      </div>
    </section>

    <!-- Bizim Hikayemiz (.club) -->
    <section class="yalova-about section" id="kulup" aria-labelledby="yalova-title">
      <span class="yalova-about-decoration" aria-hidden="true"></span>
      <div class="container yalova-about-layout">
        <div class="yalova-about-photos">
          <figure class="yalova-about-main"><img src="{{ asset($settings['about_image_1'] ?? 'assets/images/birlik.jpg') }}" alt="Yalova oyuncusu taraftarlarıyla buluşuyor" loading="lazy"></figure>
          <figure class="yalova-about-inset"><img src="{{ asset($settings['about_image_2'] ?? 'assets/images/tribun.jpg') }}" alt="Yalova taraftarları tribünde tek yürek" loading="lazy"></figure>
        </div>
        <div class="yalova-about-copy">
          <h2 id="yalova-title" class="section-title">{!! nl2br(e($settings['about_title'] ?? "YALOVA İÇİN.\nSON DÜDÜĞE KADAR.")) !!}</h2>
          <div class="yalova-about-footer">
            <a class="yalova-about-cta" href="{{ $settings['about_cta_url'] ?? '#maclar' }}"><span>{{ $settings['about_cta_text'] ?? 'MAÇ TAKVİMİ' }}</span><i aria-hidden="true">↗</i></a>
          </div>
        </div>
      </div>
    </section>

    <!-- Sponsorlarımız -->
    <section class="sponsors section" id="sponsorlar" aria-labelledby="sponsors-title">
      <div class="container">
        <div class="section-head sponsors-head">
          <div class="sponsors-title-block">
            <h2 id="sponsors-title" class="section-title">SPONSORLARIMIZ<span class="title-slash" aria-hidden="true"> /</span></h2>
          </div>
          <div class="sponsors-head-right">
            <p class="section-sub">Yalova Haremspor armasını gururla taşıyan, parkede ve tribünde mücadelemize güç katan değerli sponsorlarımız.</p>
            <div class="sponsors-controls" aria-label="Sponsor slider kontrolleri">
              <button class="sponsor-nav-btn" id="sponsors-prev" aria-label="Önceki sponsorlar" title="Geri">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
              </button>
              <button class="sponsor-nav-btn" id="sponsors-toggle" aria-label="Kaydırmayı duraklat" aria-pressed="false" title="Durdur / Başlat">
                <svg class="icon-pause" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
                <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" hidden><polygon points="6 4 20 12 6 20 6 4"/></svg>
              </button>
              <button class="sponsor-nav-btn" id="sponsors-next" aria-label="Sonraki sponsorlar" title="İleri">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="sponsors-carousel" id="sponsors-carousel" role="region" aria-label="Sponsor logoları galerisi" tabindex="0">
        <div class="sponsors-fade sponsors-fade-left" aria-hidden="true"></div>
        <div class="sponsors-fade sponsors-fade-right" aria-hidden="true"></div>

        <div class="sponsors-track" id="sponsors-track">
          @for ($i = 0; $i < 4; $i++)
          <div class="sponsors-track-group" {{ $i > 0 ? 'aria-hidden=true' : '' }}>
            @foreach($sponsors as $sponsor)
            <article class="sponsor-card" tabindex="{{ $i === 0 ? '0' : '-1' }}" aria-label="{{ $sponsor->name }} - {{ $sponsor->category }}">
              <span class="sponsor-badge">{{ $sponsor->category }}</span>
              <div class="sponsor-logo-box">
                <img src="{{ $sponsor->logo }}" alt="{{ $sponsor->name }} logosu" width="175" height="52" loading="lazy">
              </div>
              <h3 class="sponsor-name">{{ $sponsor->name }}</h3>
            </article>
            @endforeach
          </div>
          @endfor
        </div>
      </div>

      <div class="container">
        <div class="sponsors-bottom-bar">
          <span class="roster-stripes" aria-hidden="true"></span>
          <span class="sponsors-tagline">BİRLİKTE DAHA GÜÇLÜ • {{ $settings['footer_hashtag'] ?? '#AYNITUTKUYENİHEYECAN' }}</span>
          <span class="roster-stripes" aria-hidden="true"></span>
        </div>
      </div>
    </section>

    <!-- OYUNCULARIMIZ (İNTERAKTİF PRO SHOWCASE - GÖRSEL VE İSTATİSTİK LİSTESİ) -->
    <section class="players roster-section" id="oyuncular" aria-labelledby="players-title">
      <!-- Çınar Ağacı ve Masaüstünden Yüklenen cinar.png Opak Dinamik Sahnesi -->
      <div class="roster-cinar-scene" aria-hidden="true">
        <!-- 1. Büyük Yüzen Çınar Yaprağı (Sol Üst) -->
        <img class="cinar-leaf-img cinar-leaf-img-1" src="{{ asset('assets/images/cinar.png') }}" alt="" loading="lazy">

        <!-- 2. Büyük Yüzen Çınar Yaprağı (Sağ Alt) -->
        <img class="cinar-leaf-img cinar-leaf-img-2" src="{{ asset('assets/images/cinar.png') }}" alt="" loading="lazy">

        <!-- 3. Arka Plan Süzülen Çınar Yaprağı (Orta) -->
        <img class="cinar-leaf-img cinar-leaf-img-3" src="{{ asset('assets/images/cinar.png') }}" alt="" loading="lazy">

        <!-- Zümrüt Işıma Katmanı -->
        <div class="cinar-ambient-aura"></div>
      </div>

      <div class="container">
        <div class="players-heading">
          <div><h2 id="players-title" class="section-title">OYUNCULARIMIZ</h2></div>
          <div class="roster-heading-note">
            <span class="roster-count">{{ $players->count() }} <small>OYUNCU</small></span>
            <p>Parkede mücadele.<br>Armanın altında tek yürek.<br><small style="color:#ff4d4d;">(Detaylar için listeden oyuncu seçin)</small></p>
          </div>
        </div>

        @php
          $firstPlayer = $players->first();
          $firstStats = $firstPlayer ? $firstPlayer->stats : null;
          $fullName = $firstPlayer ? $firstPlayer->full_name : 'OYUNCU ADI';
          $nameParts = explode(' ', trim($fullName));
          $lastName = count($nameParts) > 1 ? array_pop($nameParts) : $fullName;
          $firstName = count($nameParts) > 0 ? implode(' ', $nameParts) : '';
        @endphp

        <div class="player-showcase-container">
          <!-- 1. SOL: Oyuncu Fotoğraf Sahnesi (Kusursuz Oturan Kadraj) -->
          <div class="showcase-stage-card">
            <span class="showcase-stage-watermark" id="showcaseWatermark">#{{ $firstPlayer->jersey_number ?? '0' }}</span>
            <div class="showcase-photo-frame">
              <img id="showcasePhoto" src="{{ $firstPlayer ? $firstPlayer->image : asset('assets/images/yalova-logo.jpg') }}" alt="{{ $firstPlayer->full_name ?? 'Oyuncu' }}">
            </div>
            
            <!-- Yeni Güçlü Tipografili İsim & Kimlik Rozet Alanı -->
            <div class="showcase-name-plate">
              <div class="showcase-name-top-bar">
                <div class="showcase-num-badge" id="showcaseBadgeNum">
                  {{ $firstPlayer && $firstPlayer->jersey_number ? sprintf('%02d', (int)$firstPlayer->jersey_number) : '00' }}
                </div>
                <div class="showcase-pos-chip" id="showcaseBadgePos">
                  {{ $firstPlayer->position ?? 'OYUNCU' }}
                </div>
              </div>
              <div class="showcase-full-name-wrap">
                <div class="showcase-first-name" id="showcaseFirstName">{{ $firstName }}</div>
                <div class="showcase-last-name" id="showcaseLastName">{{ $lastName }}</div>
              </div>
              <div class="showcase-name-subline">
                <span>YALOVA HAREMSPOR</span>
                <strong id="showcaseBadgeNat">{{ strtoupper($firstPlayer->nationality ?? 'TÜRKİYE') }}</strong>
              </div>
            </div>
          </div>

          <!-- 2. ORTA: İstatistik Paneli & Biyografi -->
          <div class="showcase-stats-panel">
            <div>
              <div class="showcase-stats-header">
                <h3>2026–2027 <span>SEZON PERFORMANSI</span></h3>
                <div class="showcase-bio-strip">
                  Boy: <strong id="showcaseBioHeight">{{ $firstPlayer->height ?? '—' }}</strong>
                  <span class="sep">•</span>
                  Kilo: <strong id="showcaseBioWeight">{{ $firstPlayer->weight ?? '—' }}</strong>
                  <span class="sep">•</span>
                  D. Yılı: <strong id="showcaseBioBirth">{{ $firstPlayer->birth_date ?? '—' }}</strong>
                  <span class="sep">•</span>
                  Uyruk: <strong id="showcaseBioNat">{{ $firstPlayer->nationality ?? 'Türkiye' }}</strong>
                </div>
              </div>

              <!-- 8'li Temiz İstatistik Izgarası -->
              <div class="showcase-stats-grid">
                <div class="stat-box red-stat">
                  <div class="stat-box-val" id="showcaseStatPts">{{ $firstStats->points_avg ?? '0.0' }}</div>
                  <div class="stat-box-lbl">SAYI ORT. (PPG)</div>
                </div>
                <div class="stat-box">
                  <div class="stat-box-val" id="showcaseStatReb">{{ $firstStats->rebounds_avg ?? '0.0' }}</div>
                  <div class="stat-box-lbl">RİBAUND (RPG)</div>
                </div>
                <div class="stat-box">
                  <div class="stat-box-val" id="showcaseStatAst">{{ $firstStats->assists_avg ?? '0.0' }}</div>
                  <div class="stat-box-lbl">ASİST (APG)</div>
                </div>
                <div class="stat-box green-stat">
                  <div class="stat-box-val" id="showcaseStatEff">{{ $firstStats->efficiency_avg ?? '0.0' }}</div>
                  <div class="stat-box-lbl">VERİMLİLİK (EFF)</div>
                </div>

                <div class="stat-box">
                  <div class="stat-box-val" id="showcaseStatGp">{{ $firstStats->games_played ?? 0 }}</div>
                  <div class="stat-box-lbl">MAÇ SAYISI (GP)</div>
                </div>
                <div class="stat-box">
                  <div class="stat-box-val" id="showcaseStatMin">{{ $firstStats->minutes_avg ?? '0.0' }}</div>
                  <div class="stat-box-lbl">ORT. SÜRE (DK)</div>
                </div>
                <div class="stat-box">
                  <div class="stat-box-val" id="showcaseStatFg">{{ $firstStats->fg_percentage ?? '0%' }}</div>
                  <div class="stat-box-lbl">2 SAYI % (FG)</div>
                </div>
                <div class="stat-box">
                  <div class="stat-box-val" id="showcaseStatTp">{{ $firstStats->three_point_percentage ?? '0%' }}</div>
                  <div class="stat-box-lbl">3 SAYI % (3PT)</div>
                </div>
              </div>
            </div>

            <div style="padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: rgba(255,255,255,0.5);">
              <span>TBF Resmi İstatistik Verileri</span>
              <span style="color: #ff4d4d; font-weight: 700;">YALOVA HAREMSPOR • 2026-2027</span>
            </div>
          </div>

          <!-- 3. SAĞ: İnteraktif Oyuncu Listesi (Zümrüt Yeşil & Kırmızı Vurgu) -->
          <div class="showcase-roster-list" id="showcaseRosterList">
            @foreach($players as $index => $player)
            @php
              $pStats = $player->stats;
              $isActive = ($index === 0);
              $formattedNum = $player->jersey_number ? sprintf('%02d', (int)$player->jersey_number) : '—';
              $rParts = explode(' ', trim($player->full_name));
              $rLast = count($rParts) > 1 ? array_pop($rParts) : $player->full_name;
              $rFirst = count($rParts) > 0 ? implode(' ', $rParts) : '';
            @endphp
            <div class="showcase-roster-item {{ $isActive ? 'active' : '' }}"
                 tabindex="0"
                 role="button"
                 aria-label="{{ $player->full_name }}"
                 data-player-id="{{ $player->id }}"
                 data-name="{{ $player->full_name }}"
                 data-number="{{ $formattedNum }}"
                 data-raw-number="{{ $player->jersey_number ?? '0' }}"
                 data-position="{{ $player->position ?? 'Oyuncu' }}"
                 data-height="{{ $player->height ?? '—' }}"
                 data-weight="{{ $player->weight ?? '—' }}"
                 data-birth="{{ $player->birth_date ?? '—' }}"
                 data-nationality="{{ $player->nationality ?? 'Türkiye' }}"
                 data-image="{{ $player->image }}"
                 data-gp="{{ $pStats->games_played ?? 0 }}"
                 data-min="{{ $pStats->minutes_avg ?? '0.0' }}"
                 data-pts="{{ $pStats->points_avg ?? '0.0' }}"
                 data-reb="{{ $pStats->rebounds_avg ?? '0.0' }}"
                 data-ast="{{ $pStats->assists_avg ?? '0.0' }}"
                 data-stl="{{ $pStats->steals_avg ?? '0.0' }}"
                 data-blk="{{ $pStats->blocks_avg ?? '0.0' }}"
                 data-eff="{{ $pStats->efficiency_avg ?? '0.0' }}"
                 data-fg="{{ $pStats->fg_percentage ?? '0%' }}"
                 data-tp="{{ $pStats->three_point_percentage ?? '0%' }}"
                 data-ft="{{ $pStats->ft_percentage ?? '0%' }}"
                 data-total-pts="{{ $pStats->total_points ?? 0 }}">
              <div class="showcase-roster-num">{{ $formattedNum }}</div>
              <div class="showcase-roster-details">
                <div class="showcase-roster-name">
                  <span class="r-first">{{ $rFirst }}</span><span class="r-last">{{ $rLast }}</span>
                </div>
                <div class="showcase-roster-pos">{{ $player->position ?? 'Oyuncu' }}</div>
              </div>
              <div class="showcase-roster-arrow" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </section>

    <!-- Kimliğimiz / Renklerimiz -->
    <section class="identity section" id="kimlik">
      <div class="container">
        <div class="section-head">
          <div><h2 id="identity-title" class="section-title">BU RENKLER BİZİM KANITIMIZ.</h2></div>
          <p class="section-sub">Armanın taşıdığı ruh.<br>Parkeye yansıyan agresif karakter.</p>
        </div>
        <div class="identity-grid">
          <!-- 01: Koyu Yeşil -->
          <article class="identity-card green-card">
            <div class="card-head">
              <span class="card-num">{{ $settings['identity_green_num'] ?? '01 / KOYU YEŞİL' }}</span>
              <span class="swatch-badge">{{ $settings['identity_green_badge'] ?? '#003F20' }}</span>
            </div>
            <div class="card-body">
              <h3>{!! nl2br(e($settings['identity_green_title'] ?? "KÖKLERİMİZ\nSAĞLAM.")) !!}</h3>
              <p>{{ $settings['identity_green_text'] ?? 'Bağlılık, kararlılık ve kulübün sarsılmaz temeli. Parkede asla pes etmeyen takım disiplini.' }}</p>
            </div>
          </article>

          <!-- 02: Agresif Kırmızı -->
          <article class="identity-card red-card">
            <div class="card-head">
              <span class="card-num">{{ $settings['identity_red_num'] ?? '02 / AGRESİF KIRMIZI' }}</span>
              <span class="swatch-badge">{{ $settings['identity_red_badge'] ?? '#A70101' }}</span>
            </div>
            <div class="card-body">
              <h3>{!! nl2br(e($settings['identity_red_title'] ?? "ATEŞİMİZ\nDURMAZ.")) !!}</h3>
              <p>{{ $settings['identity_red_text'] ?? 'Mücadelenin ateşi, hücumun hızı ve tribünün bitmeyen tutkusu. Kazanma hırsımızın simgesi.' }}</p>
            </div>
          </article>

          <!-- 03: Maskot & Arma -->
          <article class="identity-card mascot-card">
            <div class="card-head">
              <span class="card-num">{{ $settings['identity_mascot_num'] ?? '03 / MASKOTUMUZ' }}</span>
            </div>
            <div class="card-body">
              <h3>{{ $settings['identity_mascot_title'] ?? 'TOSTOS’UN GÜCÜ.' }}</h3>
              <p>{{ $settings['identity_mascot_text'] ?? 'Cesur, çevik ve her saniye oyunda. Son saniyeye kadar sahadaki liderimiz.' }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Medya Odası (Basında Biz & Bloglarımız) -->
    <section class="media-room section" id="basinda-biz" aria-labelledby="media-title">
      <div class="container">
        <div class="media-heading">
          <div><h2 id="media-title" class="section-title">BASINDA <em>BİZ.</em><span aria-hidden="true"> /</span></h2></div>
          <p class="media-intro">Parkeden satırlara, aynı tutku.<br>Hikâyelerimiz, blog yazılarımız ve medyadaki yansımalarımız.</p>
        </div>
        <div class="media-toolbar">
          <div class="media-tabs" role="tablist" aria-label="Medya içerikleri">
            <button id="media-blog-tab" role="tab" aria-controls="media-blog-panel" aria-selected="true" tabindex="0">BLOGLARIMIZ <span>{{ sprintf('%02d', $blogs->count()) }}</span></button>
            <button id="media-press-tab" role="tab" aria-controls="media-press-panel" aria-selected="false" tabindex="-1">BASINDA BİZ <span>{{ sprintf('%02d', $press->count()) }}</span></button>
          </div>
          <span class="media-toolbar-note">BİR ŞEHİR. SAYISIZ HİKÂYE.</span>
        </div>

        <!-- Bloglar Paneli -->
        <div class="media-grid" id="media-blog-panel" role="tabpanel" aria-labelledby="media-blog-tab">
          @foreach($blogs as $index => $blog)
          <article class="media-card {{ $index === 0 ? 'media-feature' : 'media-small' }}">
            <div class="media-photo">
              <img src="{{ $blog->image }}" alt="" loading="lazy">
              <span class="media-category">{{ $blog->category }}</span>
            </div>
            <div class="media-card-copy">
              <div class="media-meta">
                <span>BLOG / {{ $blog->category }}</span>
                <span>{{ $blog->publish_date ? $blog->publish_date->format('d F Y') : '' }}</span>
              </div>
              <h3>{{ $blog->title }}</h3>
              <p>{{ $blog->summary }}</p>
              <button class="media-action" data-blog-open="blog-dialog-{{ $blog->id }}">YAZIYI OKU <span aria-hidden="true">↗</span></button>
            </div>
          </article>

          <!-- Dialog Popup for Blog -->
          <dialog class="media-dialog" id="blog-dialog-{{ $blog->id }}" aria-labelledby="blog-title-{{ $blog->id }}">
            <button class="media-dialog-close" aria-label="Yazıyı kapat" autofocus>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <h2 id="blog-title-{{ $blog->id }}">{{ $blog->title }}</h2>
            <p class="media-dialog-date">{{ $blog->publish_date ? $blog->publish_date->format('d F Y') : '' }} · {{ $blog->category }}</p>
            {!! nl2br(e($blog->content)) !!}
            @if($blog->source_url)
            <div style="margin-top: 1.5rem;">
              <a class="media-dialog-source" href="{{ $blog->source_url }}" target="_blank" rel="noopener noreferrer">{{ $blog->source_title ?: 'Kaynak Bağlantısı' }} <span aria-hidden="true">↗</span></a>
            </div>
            @endif
          </dialog>
          @endforeach
        </div>

        <!-- Basında Biz Paneli -->
        <div class="media-grid" id="media-press-panel" role="tabpanel" aria-labelledby="media-press-tab" hidden>
          @foreach($press as $index => $item)
          <article class="media-card {{ $index === 0 ? 'media-feature' : 'media-small' }}">
            <div class="media-photo">
              <img src="{{ $item->image }}" alt="" loading="lazy">
              <span class="media-category">{{ $item->category }}</span>
            </div>
            <div class="media-card-copy">
              <div class="media-meta">
                <span>{{ $item->category }}</span>
                <span>{{ $item->publish_date ? $item->publish_date->format('d F Y') : '' }}</span>
              </div>
              <h3>{{ $item->title }}</h3>
              <p>{{ $item->summary }}</p>
              @if($item->source_url)
              <a class="media-action" href="{{ $item->source_url }}" target="_blank" rel="noopener noreferrer">KAYNAĞINDA OKU <span aria-hidden="true">↗</span></a>
              @endif
            </div>
          </article>
          @endforeach
        </div>

        <div class="media-bottom">
          <span><i aria-hidden="true"></i> AYNI TUTKU, HER SATIRDA.</span>
          <span>YALOVA HAREMSPOR BASKETBOL</span>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="site-footer" id="footer">
    <div class="container">
      <section class="footer-newsletter" aria-labelledby="newsletter-title">
        <div class="newsletter-copy">
          <svg class="newsletter-mark" viewBox="0 0 48 48" fill="none" aria-hidden="true"><path d="M8 13h32v23H8zM8 13l16 14 16-14" stroke="currentColor" stroke-width="1.5"/><path d="M3 6h12M33 42h12" stroke="#a70101" stroke-width="2"/></svg>
          <h2 id="newsletter-title" class="section-title">BİZDEN <em>HABERİN OLSUN.</em></h2>
          <p>Maç günleri ve Yalova’dan haberler, doğrudan e-postanda.</p>
        </div>
        <form class="newsletter-form" id="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
          @csrf
          <label for="newsletter-email">BÜLTENE KAYIT OL</label>
          <div class="newsletter-field">
            <input type="email" id="newsletter-email" name="email" autocomplete="email" placeholder="E-posta adresin" required maxlength="254">
            <button type="submit">KAYIT OL <span aria-hidden="true">↗</span></button>
          </div>
          @if(session('newsletter_success'))
            <p class="newsletter-status" style="color: #48bb78; margin-top: 8px;">{{ session('newsletter_success') }}</p>
          @endif
        </form>
      </section>

      <div class="footer-top">
        <a class="brand" href="{{ route('home') }}">
          <img src="{{ asset('assets/images/yalova-logo.jpg') }}" alt="Yalova Haremspor Kulübü">
          <div class="brand-text">
            <span class="city-name">{{ $settings['city_name'] ?? 'YALOVA' }}</span>
            <b class="club-name">{{ $settings['club_name'] ?? 'HAREMSPOR' }}</b>
          </div>
        </a>
        <div class="footer-hashtag">{{ $settings['footer_hashtag'] ?? '#aynıtutkuyeniheyecan' }}</div>
        <a class="back-to-top" href="#">YUKARI DÖN ↑</a>
      </div>

      <div class="footer-bottom">
        <span>© {{ date('Y') }} {{ $settings['footer_copyright'] ?? 'Yalova Haremspor Basketbol Kulübü. Tüm Hakları Saklıdır.' }}</span>
        <div class="footer-credit">
          <span>Hazırlayan:</span>
          <a href="{{ $settings['footer_credit_url'] ?? 'https://alperengundogdu.com.tr' }}" target="_blank" rel="noopener noreferrer">{{ $settings['footer_credit_name'] ?? 'alperengundogdu' }} <span aria-hidden="true">↗</span></a>
        </div>
      </div>
    </div>
  </footer>

  <!-- OYUNCU İSTATİSTİK POPUP MODAL (EUROLEAGUE PRO EDITION) -->
  <div class="player-modal-backdrop" id="playerModalBackdrop" role="dialog" aria-modal="true" aria-labelledby="modalPlayerName">
    <div class="player-modal-card">
      <button class="player-modal-close" id="playerModalCloseBtn" aria-label="Kapat">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>

      <!-- Üst Hero / Oyuncu Kimlik Kartı -->
      <div class="player-modal-header">
        <!-- Arka Plan Su Damgası Forma Numarası -->
        <span class="player-modal-watermark" id="modalPlayerWatermark">#0</span>

        <div class="player-modal-avatar-box">
          <span class="player-modal-jersey-tag" id="modalPlayerJerseyTag">#0</span>
          <img id="modalPlayerImg" src="" alt="Oyuncu Fotoğrafı">
        </div>

        <div class="player-modal-header-info">
          <div class="player-modal-meta-top">
            <span class="player-modal-team-pill">YALOVA HAREMSPOR</span>
            <span class="player-modal-pos-pill" id="modalPlayerPos">MEVKİ</span>
          </div>
          <h3 class="player-modal-name" id="modalPlayerName">OYUNCU ADI</h3>
          <div class="player-modal-bio-grid">
            <div class="bio-chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 6H3M21 12H3M21 18H3"/></svg>
              <span>Boy: <strong id="modalPlayerHeight">—</strong></span>
            </div>
            <div class="bio-chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              <span>Kilo: <strong id="modalPlayerWeight">—</strong></span>
            </div>
            <div class="bio-chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <span>Doğum: <strong id="modalPlayerBirth">—</strong></span>
            </div>
            <div class="bio-chip">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              <span>Uyruk: <strong id="modalPlayerNat">—</strong></span>
            </div>
          </div>
        </div>
      </div>

      <!-- İstatistik Gövdesi -->
      <div class="player-modal-body">
        <div class="player-modal-stats-title">
          <h4>2026–2027 SEZONU <span>İSTATİSTİKLERİ</span></h4>
          <span class="player-modal-source-badge">
     
        </div>

        <!-- 1. Bölüm: Büyük 4 Ana İstatistik (Sayı, Ribaund, Asist, Verimlilik) -->
        <div class="player-stats-hero-grid">
          <div class="stat-hero-card gold-theme">
            <div class="stat-hero-val" id="modalStatPts">0.0</div>
            <div class="stat-hero-lbl">SAYI ORT. (PPG)</div>
          </div>
          <div class="stat-hero-card red-theme">
            <div class="stat-hero-val" id="modalStatReb">0.0</div>
            <div class="stat-hero-lbl">RİBAUND ORT. (RPG)</div>
          </div>
          <div class="stat-hero-card green-theme">
            <div class="stat-hero-val" id="modalStatAst">0.0</div>
            <div class="stat-hero-lbl">ASİST ORT. (APG)</div>
          </div>
          <div class="stat-hero-card blue-theme">
            <div class="stat-hero-val" id="modalStatEff">0.0</div>
            <div class="stat-hero-lbl">VERİMLİLİK (EFF)</div>
          </div>
        </div>

        <!-- 2. Bölüm: İsabet Yüzdeleri & İlerleme Çubukları -->
        <div class="player-stats-shooting-row">
          <div class="shooting-card">
            <div class="shooting-header">
              <span class="shooting-label">2 SAYI % (FG)</span>
              <span class="shooting-pct" id="modalStatFg">0%</span>
            </div>
            <div class="shooting-progress-track">
              <div class="shooting-progress-fill" id="modalProgressFg" style="width: 0%;"></div>
            </div>
          </div>
          <div class="shooting-card">
            <div class="shooting-header">
              <span class="shooting-label">3 SAYI % (3PT)</span>
              <span class="shooting-pct" id="modalStatTp">0%</span>
            </div>
            <div class="shooting-progress-track">
              <div class="shooting-progress-fill" id="modalProgressTp" style="width: 0%;"></div>
            </div>
          </div>
          <div class="shooting-card">
            <div class="shooting-header">
              <span class="shooting-label">SERBEST ATIŞ % (FT)</span>
              <span class="shooting-pct" id="modalStatFt">0%</span>
            </div>
            <div class="shooting-progress-track">
              <div class="shooting-progress-fill" id="modalProgressFt" style="width: 0%;"></div>
            </div>
          </div>
        </div>

        <!-- 3. Bölüm: Saha & Oyun Dağılımı (5'li kompakt ızgara) -->
        <div class="player-stats-subgrid">
          <div class="substat-card">
            <div class="substat-val" id="modalStatGp">0</div>
            <div class="substat-lbl">MAÇ SAYISI (GP)</div>
          </div>
          <div class="substat-card">
            <div class="substat-val" id="modalStatMin">0.0</div>
            <div class="substat-lbl">SÜRE ORT. (DK)</div>
          </div>
          <div class="substat-card">
            <div class="substat-val" id="modalStatStl">0.0</div>
            <div class="substat-lbl">TOP ÇALMA (SPG)</div>
          </div>
          <div class="substat-card">
            <div class="substat-val" id="modalStatBlk">0.0</div>
            <div class="substat-lbl">BLOK ORT. (BPG)</div>
          </div>
          <div class="substat-card">
            <div class="substat-val" id="modalStatTotalPts">0</div>
            <div class="substat-lbl">TOPLAM SAYI</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @auth
  <!-- Hızlı Yönetim Paneli Butonu (Yalnızca Giriş Yapılmışsa) -->
  <a href="{{ route('admin.dashboard') }}" class="admin-float-btn" title="Yönetim Paneli">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 14.93V18a1 1 0 0 1-2 0v-1.07A7 7 0 0 1 5.07 11H4a1 1 0 0 1 0-2h1.07A7 7 0 0 1 11 3.07V2a1 1 0 0 1 2 0v1.07A7 7 0 0 1 18.93 9H20a1 1 0 0 1 0 2h-1.07A7 7 0 0 1 13 16.93z"/></svg>
    <span>Admin</span>
  </a>
  @endauth

  <!-- Scriptler -->
  <script src="{{ asset('assets/js/script.js?v=20261015-laravel') }}"></script>
  <script>
    // Oyuncu İstatistik Modalı ve Vitrini Etkileşimi
    document.addEventListener('DOMContentLoaded', () => {
      // 1. İNTERAKTİF OYUNCU VİTRİNİ (Görseldeki Liste Seçimi)
      const showcaseItems = document.querySelectorAll('.showcase-roster-item');
      function selectShowcasePlayer(item) {
        showcaseItems.forEach(el => el.classList.remove('active'));
        item.classList.add('active');

        // Sol Görsel Sahnesi & Fotoğraf Geçişi
        const photo = document.getElementById('showcasePhoto');
        if (photo) {
          photo.style.opacity = '0.3';
          photo.style.transform = 'scale(0.96)';
          setTimeout(() => {
            photo.src = item.dataset.image;
            photo.alt = item.dataset.name;
            photo.style.opacity = '1';
            photo.style.transform = 'scale(1)';
          }, 150);
        }

        const wm = document.getElementById('showcaseWatermark');
        if (wm) wm.textContent = '#' + (item.dataset.rawNumber || '0');

        const bn = document.getElementById('showcaseBadgeNum');
        if (bn) bn.textContent = item.dataset.number;

        // İsim ayrıştırma (Ad & Soyad tipografisi)
        const rawName = (item.dataset.name || 'OYUNCU').trim();
        const parts = rawName.split(' ');
        const lastName = parts.length > 1 ? parts.pop() : rawName;
        const firstName = parts.join(' ');

        const fnEl = document.getElementById('showcaseFirstName');
        if (fnEl) fnEl.textContent = firstName;
        const lnEl = document.getElementById('showcaseLastName');
        if (lnEl) lnEl.textContent = lastName;

        const bName = document.getElementById('showcaseBadgeName');
        if (bName) bName.textContent = rawName;

        const bPos = document.getElementById('showcaseBadgePos');
        if (bPos) bPos.textContent = item.dataset.position;

        const bNat = document.getElementById('showcaseBadgeNat');
        if (bNat) bNat.textContent = (item.dataset.nationality || 'TÜRKİYE').toUpperCase();

        // İsimlik kartı geçiş efekti
        const namePlate = document.querySelector('.showcase-name-plate');
        if (namePlate) {
          namePlate.style.opacity = '0.5';
          namePlate.style.transform = 'translateY(5px)';
          setTimeout(() => {
            namePlate.style.opacity = '1';
            namePlate.style.transform = 'translateY(0)';
          }, 140);
        }

        // Orta İstatistikler
        const setTxt = (id, val) => {
          const el = document.getElementById(id);
          if (el) el.textContent = val;
        };

        setTxt('showcaseStatPts', item.dataset.pts);
        setTxt('showcaseStatReb', item.dataset.reb);
        setTxt('showcaseStatAst', item.dataset.ast);
        setTxt('showcaseStatEff', item.dataset.eff);

        setTxt('showcaseStatFg', item.dataset.fg);
        setTxt('showcaseStatTp', item.dataset.tp);
        setTxt('showcaseStatFt', item.dataset.ft);

        const parseNum = (val) => {
          if (!val) return 0;
          const n = parseFloat(val.toString().replace('%', ''));
          return isNaN(n) ? 0 : Math.min(Math.max(n, 0), 100);
        };

        const barFg = document.getElementById('showcaseBarFg');
        if (barFg) barFg.style.width = parseNum(item.dataset.fg) + '%';
        const barTp = document.getElementById('showcaseBarTp');
        if (barTp) barTp.style.width = parseNum(item.dataset.tp) + '%';
        const barFt = document.getElementById('showcaseBarFt');
        if (barFt) barFt.style.width = parseNum(item.dataset.ft) + '%';

        setTxt('showcaseStatGp', item.dataset.gp);
        setTxt('showcaseStatMin', item.dataset.min);
        setTxt('showcaseStatStl', item.dataset.stl);
        setTxt('showcaseStatBlk', item.dataset.blk);
        setTxt('showcaseStatTotalPts', item.dataset.totalPts);

        setTxt('showcaseBioHeight', item.dataset.height);
        setTxt('showcaseBioWeight', item.dataset.weight);
        setTxt('showcaseBioBirth', item.dataset.birth);
        setTxt('showcaseBioNat', item.dataset.nationality);
      }

      showcaseItems.forEach(item => {
        item.addEventListener('click', () => selectShowcasePlayer(item));
        item.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            selectShowcasePlayer(item);
          }
        });
      });

      // 2. Modalı açma (İsteğe bağlı tıklandığında)
      const modal = document.getElementById('playerModalBackdrop');
      const closeBtn = document.getElementById('playerModalCloseBtn');

      function openPlayerModal(item) {
        const num = item.dataset.number || '0';
        const numStr = '#' + num;

        document.getElementById('modalPlayerImg').src = item.dataset.image;
        document.getElementById('modalPlayerJerseyTag').textContent = numStr;
        document.getElementById('modalPlayerWatermark').textContent = numStr;
        document.getElementById('modalPlayerPos').textContent = item.dataset.position;
        document.getElementById('modalPlayerName').textContent = item.dataset.name;
        document.getElementById('modalPlayerHeight').textContent = item.dataset.height;
        document.getElementById('modalPlayerWeight').textContent = item.dataset.weight;
        document.getElementById('modalPlayerBirth').textContent = item.dataset.birth;
        document.getElementById('modalPlayerNat').textContent = item.dataset.nationality;

        document.getElementById('modalStatPts').textContent = item.dataset.pts;
        document.getElementById('modalStatReb').textContent = item.dataset.reb;
        document.getElementById('modalStatAst').textContent = item.dataset.ast;
        document.getElementById('modalStatGp').textContent = item.dataset.gp;
        document.getElementById('modalStatMin').textContent = item.dataset.min;
        document.getElementById('modalStatStl').textContent = item.dataset.stl;
        document.getElementById('modalStatBlk').textContent = item.dataset.blk;
        document.getElementById('modalStatEff').textContent = item.dataset.eff;
        document.getElementById('modalStatFg').textContent = item.dataset.fg;
        document.getElementById('modalStatTp').textContent = item.dataset.tp;
        document.getElementById('modalStatFt').textContent = item.dataset.ft;
        document.getElementById('modalStatTotalPts').textContent = item.dataset.totalPts;

        // Progress barların yüzdesini dinamik doldurma
        const parsePct = (val) => {
          if (!val) return 0;
          const num = parseFloat(val.toString().replace('%', ''));
          return isNaN(num) ? 0 : Math.min(Math.max(num, 0), 100);
        };
        document.getElementById('modalProgressFg').style.width = parsePct(item.dataset.fg) + '%';
        document.getElementById('modalProgressTp').style.width = parsePct(item.dataset.tp) + '%';
        document.getElementById('modalProgressFt').style.width = parsePct(item.dataset.ft) + '%';

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }

      function closePlayerModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
      }

      document.querySelectorAll('.player-entry').forEach(card => {
        card.addEventListener('click', () => openPlayerModal(card));
        card.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openPlayerModal(card);
          }
        });
      });

      if (closeBtn) closeBtn.addEventListener('click', closePlayerModal);
      if (modal) {
        modal.addEventListener('click', (e) => {
          if (e.target === modal) closePlayerModal();
        });
      }
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
          closePlayerModal();
        }
      });
    });
  </script>
</body>
</html>
