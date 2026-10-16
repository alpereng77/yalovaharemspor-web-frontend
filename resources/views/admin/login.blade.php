<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Giriş Yap • Yalova Haremspor Yönetim Paneli</title>
  <link rel="icon" href="{{ asset('assets/images/yalova-logo.jpg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: radial-gradient(circle at center, #0f2b1d 0%, #07100b 100%);
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .login-card {
      background: #0d1e16;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      width: 100%;
      max-width: 420px;
      padding: 2.5rem 2rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
      text-align: center;
    }
    .login-brand {
      margin-bottom: 2rem;
    }
    .login-brand img {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      border: 3px solid #a70101;
      margin-bottom: 0.75rem;
    }
    .login-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.8rem;
      font-weight: 900;
      letter-spacing: 0.5px;
      line-height: 1.1;
    }
    .login-sub {
      font-size: 0.85rem;
      color: #8b9d94;
      margin-top: 0.25rem;
    }
    .form-group {
      text-align: left;
      margin-bottom: 1.25rem;
    }
    .form-label {
      display: block;
      font-size: 0.8rem;
      font-weight: 600;
      color: #8b9d94;
      margin-bottom: 0.4rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .form-control {
      width: 100%;
      background: #050e09;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      padding: 0.75rem 1rem;
      color: #fff;
      font-size: 0.95rem;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
      outline: none;
      border-color: #a70101;
      box-shadow: 0 0 0 3px rgba(167, 1, 1, 0.25);
    }
    .login-btn {
      width: 100%;
      background: #a70101;
      color: #fff;
      border: none;
      padding: 0.85rem;
      border-radius: 6px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.15rem;
      font-weight: 800;
      letter-spacing: 0.5px;
      cursor: pointer;
      margin-top: 0.5rem;
      transition: background 0.2s, transform 0.15s;
    }
    .login-btn:hover {
      background: #c90202;
      transform: translateY(-1px);
    }
    .alert-error {
      background: rgba(245, 101, 101, 0.15);
      border: 1px solid rgba(245, 101, 101, 0.35);
      color: #fc8181;
      padding: 0.75rem 1rem;
      border-radius: 6px;
      font-size: 0.85rem;
      margin-bottom: 1.25rem;
      text-align: left;
    }
    .alert-success {
      background: rgba(72, 187, 120, 0.15);
      border: 1px solid rgba(72, 187, 120, 0.35);
      color: #68d391;
      padding: 0.75rem 1rem;
      border-radius: 6px;
      font-size: 0.85rem;
      margin-bottom: 1.25rem;
    }
    .back-home {
      display: inline-block;
      margin-top: 1.5rem;
      color: #8b9d94;
      font-size: 0.85rem;
      text-decoration: none;
      transition: color 0.2s;
    }
    .back-home:hover {
      color: #fff;
    }
    .cred-hint {
      margin-top: 1rem;
      padding: 0.6rem;
      background: rgba(255, 255, 255, 0.04);
      border-radius: 6px;
      font-size: 0.75rem;
      color: #8b9d94;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-brand">
      <img src="{{ asset('assets/images/yalova-logo.jpg') }}" alt="Yalova Haremspor">
      <h1 class="login-title">YALOVA HAREMSPOR</h1>
      <p class="login-sub">Yönetim Paneli Girişi</p>
    </div>

    @if($errors->any())
      <div class="alert-error">
        {{ $errors->first() }}
      </div>
    @endif

    @if(session('success'))
      <div class="alert-success">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
      @csrf
      <div class="form-group">
        <label class="form-label" for="email">E-Posta Adresi</label>
        <input class="form-control" type="email" id="email" name="email" value="{{ old('email', 'admin@yalovaharemspor.com') }}" required autofocus>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Şifre</label>
        <input class="form-control" type="password" id="password" name="password" value="admin123" required>
      </div>

      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; font-size: 0.85rem; color: #8b9d94;">
        <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer;">
          <input type="checkbox" name="remember" checked> Beni Hatırla
        </label>
      </div>

      <button type="submit" class="login-btn">GİRİŞ YAP ↗</button>
    </form>

    <div class="cred-hint">
      Varsayılan Giriş: <strong>admin@yalovaharemspor.com</strong> / <strong>admin123</strong>
    </div>

    <a href="{{ route('home') }}" class="back-home">← Ana Sayfaya Dön</a>
  </div>
</body>
</html>
