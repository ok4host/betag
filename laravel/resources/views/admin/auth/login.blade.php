<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - BeTaj Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        body { background: linear-gradient(135deg, #1a1c23 0%, #2d3748 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); width: 100%; max-width: 400px; padding: 40px; }
        .login-card .logo { text-align: center; margin-bottom: 30px; }
        .login-card .logo h2 { color: #f59e0b; font-weight: 700; }
        .login-card .logo p { color: #666; margin: 0; }
        .form-control { border-radius: 8px; padding: 12px 15px; border: 2px solid #eee; }
        .form-control:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
        .btn-login { background: #f59e0b; border: none; padding: 12px; border-radius: 8px; font-weight: 600; width: 100%; }
        .btn-login:hover { background: #d97706; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <h2><i class="bi bi-building"></i> BeTaj</h2>
            <p>لوحة التحكم</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">تذكرني</label>
            </div>
            <button type="submit" class="btn btn-login btn-primary">تسجيل الدخول</button>
        </form>
    </div>
</body>
</html>
