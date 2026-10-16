<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Yönetim Paneli') • Yalova Haremspor</title>
  <link rel="icon" href="{{ asset('assets/images/yalova-logo.jpg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-dark: #07100b;
      --bg-sidebar: #0b1712;
      --bg-card: #10211a;
      --bg-input: #07120d;
      --border-color: rgba(255, 255, 255, 0.08);
      --border-highlight: rgba(167, 1, 1, 0.4);
      --primary-red: #a70101;
      --primary-red-hover: #c90202;
      --primary-green: #003f20;
      --accent-gold: #e5a93c;
      --text-main: #f0f4f2;
      --text-muted: #8b9d94;
      --sidebar-width: 260px;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
      display: flex;
      min-height: 100vh;
    }
    /* Sidebar */
    .admin-sidebar {
      width: var(--sidebar-width);
      background: var(--bg-sidebar);
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
    }
    .sidebar-brand {
      padding: 1.5rem 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.85rem;
      border-bottom: 1px solid var(--border-color);
      text-decoration: none;
      color: #fff;
    }
    .sidebar-brand img {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      border: 2px solid var(--primary-red);
    }
    .sidebar-brand-text {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }
    .sidebar-brand-text span {
      font-size: 0.75rem;
      letter-spacing: 1.5px;
      color: var(--text-muted);
      font-weight: 700;
    }
    .sidebar-brand-text b {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.35rem;
      font-weight: 900;
      letter-spacing: 0.5px;
    }
    .sidebar-nav {
      flex: 1;
      padding: 1.25rem 0.75rem;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
    }
    .nav-label {
      font-size: 0.7rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--text-muted);
      padding: 0.6rem 0.75rem 0.3rem;
      text-transform: uppercase;
    }
    .nav-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.7rem 0.85rem;
      color: var(--text-muted);
      text-decoration: none;
      border-radius: 6px;
      font-size: 0.9rem;
      font-weight: 500;
      transition: background 0.2s, color 0.2s;
    }
    .nav-item:hover {
      background: rgba(255, 255, 255, 0.05);
      color: #fff;
    }
    .nav-item.active {
      background: var(--primary-red);
      color: #fff;
      font-weight: 600;
    }
    .nav-item svg {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
    }
    .sidebar-footer {
      padding: 1rem;
      border-top: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    .live-site-link {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      background: rgba(255, 255, 255, 0.06);
      color: #fff;
      padding: 0.6rem;
      border-radius: 6px;
      font-size: 0.82rem;
      font-weight: 600;
      text-decoration: none;
      transition: background 0.2s;
    }
    .live-site-link:hover {
      background: rgba(255, 255, 255, 0.12);
    }
    .logout-btn {
      width: 100%;
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #f56565;
      padding: 0.6rem;
      border-radius: 6px;
      font-size: 0.82rem;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: background 0.2s;
    }
    .logout-btn:hover {
      background: rgba(245, 101, 101, 0.1);
    }

    /* Main Container */
    .admin-main {
      margin-left: var(--sidebar-width);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }
    .admin-topbar {
      height: 64px;
      background: var(--bg-sidebar);
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
      position: sticky;
      top: 0;
      z-index: 90;
    }
    .topbar-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.4rem;
      font-weight: 800;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }
    .topbar-user {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 0.85rem;
    }
    .user-badge {
      background: rgba(229, 169, 60, 0.15);
      color: var(--accent-gold);
      padding: 3px 8px;
      border-radius: 4px;
      font-weight: 700;
      font-size: 0.75rem;
    }
    .admin-content {
      padding: 2rem;
      flex: 1;
    }

    /* Flash Messages */
    .alert {
      padding: 1rem 1.25rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .alert-success {
      background: rgba(72, 187, 120, 0.15);
      border: 1px solid rgba(72, 187, 120, 0.35);
      color: #68d391;
    }
    .alert-warning {
      background: rgba(237, 137, 54, 0.15);
      border: 1px solid rgba(237, 137, 54, 0.35);
      color: #fbd38d;
    }
    .alert-error {
      background: rgba(245, 101, 101, 0.15);
      border: 1px solid rgba(245, 101, 101, 0.35);
      color: #fc8181;
    }
    .alert-info {
      background: rgba(66, 153, 225, 0.15);
      border: 1px solid rgba(66, 153, 225, 0.35);
      color: #90cdf4;
    }

    /* Common Card & UI Components */
    .card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.25rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid var(--border-color);
    }
    .card-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.3rem;
      font-weight: 800;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* Forms & Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      border: none;
      transition: background 0.2s, transform 0.15s;
    }
    .btn-primary {
      background: var(--primary-red);
      color: #fff;
    }
    .btn-primary:hover {
      background: var(--primary-red-hover);
    }
    .btn-green {
      background: var(--primary-green);
      color: #fff;
      border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .btn-green:hover {
      background: #00562c;
    }
    .btn-gold {
      background: var(--accent-gold);
      color: #07100b;
      font-weight: 700;
    }
    .btn-gold:hover {
      background: #f0b74b;
    }
    .btn-secondary {
      background: rgba(255, 255, 255, 0.08);
      color: #fff;
    }
    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
    }
    .btn-danger {
      background: rgba(245, 101, 101, 0.15);
      color: #fc8181;
      border: 1px solid rgba(245, 101, 101, 0.3);
    }
    .btn-danger:hover {
      background: #f56565;
      color: #fff;
    }
    .btn-sm {
      padding: 0.35rem 0.75rem;
      font-size: 0.78rem;
    }

    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-label {
      display: block;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--text-muted);
      margin-bottom: 0.45rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .form-control {
      width: 100%;
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 0.65rem 0.85rem;
      color: #fff;
      font-size: 0.9rem;
      font-family: inherit;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
      outline: none;
      border-color: var(--primary-red);
      box-shadow: 0 0 0 2px rgba(167, 1, 1, 0.25);
    }
    textarea.form-control {
      resize: vertical;
      min-height: 90px;
    }

    /* Tables */
    .table-responsive {
      width: 100%;
      overflow-x: auto;
    }
    .table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.88rem;
    }
    .table th {
      background: rgba(0, 0, 0, 0.25);
      color: var(--text-muted);
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 0.85rem 1rem;
      border-bottom: 1px solid var(--border-color);
    }
    .table td {
      padding: 0.85rem 1rem;
      border-bottom: 1px solid var(--border-color);
      vertical-align: middle;
    }
    .table tbody tr:hover {
      background: rgba(255, 255, 255, 0.02);
    }
    .badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    .badge-success {
      background: rgba(72, 187, 120, 0.15);
      color: #68d391;
    }
    .badge-danger {
      background: rgba(245, 101, 101, 0.15);
      color: #fc8181;
    }
    .badge-gold {
      background: rgba(229, 169, 60, 0.15);
      color: var(--accent-gold);
    }

    /* Responsive */
    @media (max-width: 900px) {
      .admin-sidebar {
        transform: translateX(-100%);
        transition: transform 0.25s ease;
      }
      .admin-sidebar.open {
        transform: translateX(0);
      }
      .admin-main {
        margin-left: 0;
      }
    }
  </style>
  @yield('styles')
</head>
<body>
  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
      <img src="{{ asset('assets/images/yalova-logo.jpg') }}" alt="Yalova Haremspor">
      <div class="sidebar-brand-text">
        <span>YALOVA HAREMSPOR</span>
        <b>YÖNETİM PANELİ</b>
      </div>
    </a>

    <nav class="sidebar-nav">
      <div class="nav-label">Genel</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Gösterge Paneli</span>
      </a>

      <div class="nav-label">Otomasyon & Veri</div>
      <a href="{{ route('admin.bot.index') }}" class="nav-item {{ request()->routeIs('admin.bot.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg>
        <span>Veri Senkronizasyonu</span>
      </a>

      <div class="nav-label">Takım & Maçlar</div>
      <a href="{{ route('admin.players.index') }}" class="nav-item {{ request()->routeIs('admin.players.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Oyuncular & İstatistik</span>
      </a>
      <a href="{{ route('admin.matches.index') }}" class="nav-item {{ request()->routeIs('admin.matches.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
        <span>Fikstür & Sonuçlar</span>
      </a>
      <a href="{{ route('admin.staff.index') }}" class="nav-item {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        <span>Teknik Kadro</span>
      </a>

      <div class="nav-label">Site İçeriği</div>
      <a href="{{ route('admin.sponsors.index') }}" class="nav-item {{ request()->routeIs('admin.sponsors.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <span>Sponsorlar</span>
      </a>
      <a href="{{ route('admin.ticket-points.index') }}" class="nav-item {{ request()->routeIs('admin.ticket-points.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
        <span>Bilet Satış Noktaları</span>
      </a>
      <a href="{{ route('admin.media.index') }}" class="nav-item {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <span>Medya & Blog</span>
      </a>
      <a href="{{ route('admin.newsletters.index') }}" class="nav-item {{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <span>Bülten Aboneleri</span>
      </a>
      <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Site Ayarları</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="{{ route('home') }}" target="_blank" class="live-site-link">
        <span>Siteye Git</span>
        <span aria-hidden="true">↗</span>
      </a>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          <span>Çıkış Yap</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content Wrapper -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div class="topbar-title">
        @yield('topbar-title', 'Yönetim Paneli')
      </div>
      <div class="topbar-user">
        <span class="user-badge">ADMIN</span>
        <span>{{ auth()->user()->name ?? 'Yönetici' }}</span>
      </div>
    </header>

    <main class="admin-content">
      @if(session('success'))
        <div class="alert alert-success">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      @if(session('warning'))
        <div class="alert alert-warning">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <div>{{ session('warning') }}</div>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-error">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @if(session('info'))
        <div class="alert alert-info">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <div>{{ session('info') }}</div>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  @yield('scripts')
</body>
</html>
