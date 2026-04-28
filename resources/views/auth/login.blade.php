<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SmartPool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%; max-width: 400px;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
        }
        .brand-icon {
            width: 56px; height: 56px; border-radius: 14px;
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff;
            margin: 0 auto 1rem;
        }
        .login-card h4 { font-weight: 700; color: #0f172a; }
        .form-control {
            border-radius: 10px; border: 1.5px solid #e2e8f0;
            padding: .65rem 1rem; font-size: .9rem;
        }
        .form-control:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.15); }
        .btn-login {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: #fff; border: none; border-radius: 10px;
            padding: .7rem; font-weight: 600; font-size: .95rem;
            transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; color: #fff; }
        .input-group-text { border-radius: 0 10px 10px 0; background: #f8fafc; border: 1.5px solid #e2e8f0; border-left: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon"><i class="bi bi-water"></i></div>
            <h4>SmartPool</h4>
            <p class="text-muted small mb-0">Sistem Kolam Renang Pintar IoT</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 py-2 small">
                <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-600 small">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="email@contoh.com" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-600 small">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="passInput"
                        class="form-control" placeholder="••••••••" required
                        style="border-radius: 10px 0 0 10px;">
                    <span class="input-group-text" style="cursor:pointer" onclick="togglePass()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>

        <p class="text-center text-muted small mt-3 mb-0">
            &copy; {{ date('Y') }} SmartPool — IoT Pool Management
        </p>
    </div>
    <script>
        function togglePass() {
            const i = document.getElementById('passInput');
            const e = document.getElementById('eyeIcon');
            if (i.type === 'password') { i.type = 'text'; e.className = 'bi bi-eye-slash'; }
            else { i.type = 'password'; e.className = 'bi bi-eye'; }
        }
    </script>
</body>
</html>
