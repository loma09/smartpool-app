<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Smart Swimming Pool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #060d1a 0%, #0c1929 50%, #060d1a 100%);
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse at 15% 85%, rgba(14,165,233,0.08) 0%, transparent 55%),
                radial-gradient(ellipse at 85% 15%, rgba(56,189,248,0.07) 0%, transparent 55%);
            pointer-events: none; z-index: 0;
        }

        .wrapper {
            display: flex;
            width: 960px; min-height: 590px;
            border-radius: 28px; overflow: hidden;
            box-shadow: 0 40px 120px rgba(0,0,0,.75), 0 0 0 1px rgba(255,255,255,.07);
            position: relative; z-index: 1;
        }

        /* ======= LEFT PANEL ======= */
        .left-panel {
            width: 52%;
            background: linear-gradient(180deg, #bae6fd 0%, #7dd3fc 30%, #38bdf8 55%, #0ea5e9 72%, #0284c7 85%, #0369a1 100%);
            position: relative; overflow: hidden;
        }

        /* ---- CLOUDS (3 uni-directional left→right) ---- */
        .cloud {
            position: absolute;
            background: rgba(255,255,255,.93);
            border-radius: 50px;
            z-index: 2;
        }
        .cloud::before, .cloud::after {
            content: ''; position: absolute;
            background: rgba(255,255,255,.93);
            border-radius: 50%;
        }

        .cloud-1 { width:90px; height:26px; top:8%; animation: driftLR 22s linear infinite; }
        .cloud-1::before { width:44px; height:44px; top:-22px; left:10px; }
        .cloud-1::after  { width:30px; height:30px; top:-15px; left:40px; }

        .cloud-2 { width:68px; height:20px; top:19%; animation: driftLR 30s linear infinite -10s; opacity:.85; }
        .cloud-2::before { width:35px; height:35px; top:-17px; left:8px; }
        .cloud-2::after  { width:24px; height:24px; top:-12px; left:30px; }

        .cloud-3 { width:110px; height:32px; top:4%; animation: driftLR 18s linear infinite -5s; }
        .cloud-3::before { width:54px; height:54px; top:-28px; left:12px; }
        .cloud-3::after  { width:38px; height:38px; top:-20px; left:52px; }

        @keyframes driftLR {
            0%   { transform: translateX(-180px); }
            100% { transform: translateX(560px); }
        }

        /* ---- CLOUDS (3 bolak-balik / ping-pong) ---- */
        .cloud-4 { width:80px; height:24px; top:13%; left:8%; opacity:.75; z-index:2; animation: pp1 14s ease-in-out infinite alternate; }
        .cloud-4::before { width:40px; height:40px; top:-20px; left:8px; }
        .cloud-4::after  { width:28px; height:28px; top:-14px; left:36px; }

        .cloud-5 { width:58px; height:18px; top:24%; left:28%; opacity:.7; z-index:2; animation: pp2 19s ease-in-out infinite alternate; }
        .cloud-5::before { width:30px; height:30px; top:-15px; left:6px; }
        .cloud-5::after  { width:20px; height:20px; top:-10px; left:24px; }

        .cloud-6 { width:95px; height:28px; top:5%; left:38%; opacity:.8; z-index:2; animation: pp3 22s ease-in-out infinite alternate; }
        .cloud-6::before { width:48px; height:48px; top:-24px; left:10px; }
        .cloud-6::after  { width:34px; height:34px; top:-18px; left:46px; }

        @keyframes pp1 { 0% { transform:translateX(0);   } 100% { transform:translateX(240px); } }
        @keyframes pp2 { 0% { transform:translateX(0);   } 100% { transform:translateX(170px); } }
        @keyframes pp3 { 0% { transform:translateX(0);   } 100% { transform:translateX(-130px); } }

        /* ---- BALLOON ---- */
        .balloon-wrap {
            position: absolute; top:5%; left:14%;
            animation: floatBalloon 6s ease-in-out infinite; z-index:3;
        }
        @keyframes floatBalloon {
            0%,100% { transform:translateY(0) rotate(-2deg); }
            50%     { transform:translateY(-14px) rotate(2deg); }
        }

        /* ---- TITLE — above water ---- */
        /* pool-surface height = 38%, so title bottom = ~42% */
        .panel-title {
            position: absolute;
            bottom: 40%; left: 0; right: 0;
            text-align: center; z-index: 10;
            padding: 0 1.2rem;
        }
        .panel-title h2 {
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: 1.6rem; font-weight: 900;
            text-shadow: 0 2px 16px rgba(0,0,0,.3);
            line-height: 1.2;
        }
        .panel-title p {
            color: rgba(255,255,255,.9);
            font-size: .8rem; margin-top:.2rem; font-weight:500;
            text-shadow: 0 1px 8px rgba(0,0,0,.2);
        }

        /* ---- WAVE border ---- */
        .wave-svg {
            position: absolute; bottom:36%; left:0; right:0; z-index:3;
        }

        /* ---- SWIMMER ---- */
        .swimmer-wrap {
            position: absolute; bottom:34%; left:50%;
            transform:translateX(-50%); z-index:4;
            animation: swimMove 4s ease-in-out infinite;
        }
        @keyframes swimMove {
            0%,100% { transform:translateX(-50%) translateY(0); }
            50%     { transform:translateX(-50%) translateY(-5px); }
        }

        /* ---- POOL ---- */
        .pool-surface {
            position: absolute; bottom:0; left:0; right:0;
            height:38%;
            background: linear-gradient(180deg,#0ea5e9 0%,#0284c7 40%,#075985 100%);
            overflow:hidden;
        }
        .pool-surface::before {
            content:''; position:absolute; top:0; left:-50%; right:-50%; height:30px;
            background:radial-gradient(ellipse 80px 20px at center,rgba(255,255,255,.3) 0%,transparent 70%);
            animation:shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%,100% { transform:translateX(-20px); opacity:.5; }
            50%     { transform:translateX(20px); opacity:1; }
        }
        .lane-line {
            position:absolute; bottom:0; width:3px; height:70%;
            background:linear-gradient(to bottom,rgba(255,255,255,.5),transparent);
            border-radius:2px;
        }
        .lane-line:nth-child(1){left:20%;} .lane-line:nth-child(2){left:40%;}
        .lane-line:nth-child(3){left:60%;} .lane-line:nth-child(4){left:80%;}

        .bubble-water {
            position:absolute; border-radius:50%;
            background:rgba(255,255,255,.3); border:1px solid rgba(255,255,255,.5);
            animation:riseUp linear infinite;
        }
        @keyframes riseUp {
            from { transform:translateY(0) scale(1); opacity:.7; }
            to   { transform:translateY(-80px) scale(.3); opacity:0; }
        }
        .ripple-edge {
            position:absolute; border-radius:50%;
            border:2px solid rgba(255,255,255,.25);
            animation:expandRipple 3s ease-out infinite;
        }
        @keyframes expandRipple {
            0%  { transform:scale(.5); opacity:.8; }
            100%{ transform:scale(2.5); opacity:0; }
        }

        .float-ring { position:absolute; z-index:5; animation:floatRing 5s ease-in-out infinite; }
        @keyframes floatRing {
            0%,100% { transform:rotate(-5deg) translateY(0); }
            50%     { transform:rotate(5deg) translateY(-8px); }
        }
        .deco-item { position:absolute; z-index:5; animation:floatDeco 4s ease-in-out infinite; }
        @keyframes floatDeco {
            0%,100% { transform:translateY(0) rotate(0deg); }
            50%     { transform:translateY(-6px) rotate(10deg); }
        }

        /* ======= RIGHT PANEL — DARK ======= */
        .right-panel {
            width:48%;
            background: #0b1628;
            display:flex; align-items:center; justify-content:center;
            padding:2.5rem 2.2rem;
        }
        .right-inner { width:100%; max-width:320px; }

        .brand-icon {
            width:54px; height:54px; border-radius:16px;
            background:linear-gradient(135deg,#38bdf8,#0284c7);
            display:flex; align-items:center; justify-content:center;
            font-size:1.5rem; color:#fff; margin:0 auto 1rem;
            box-shadow:0 6px 24px rgba(14,165,233,.45);
        }
        .right-inner h4 {
            font-family:'Nunito',sans-serif; font-weight:900;
            color:#e0f2fe; font-size:1.4rem;
        }
        .right-inner .sub { color:#4a6080; font-size:.82rem; margin-bottom:1.6rem; }

        .form-label { font-weight:600; font-size:.8rem; color:#7aa0c0; margin-bottom:.35rem; }

        .form-control {
            border-radius:12px; border:1.5px solid #1a2e4a;
            background:#101e32; color:#cbd5e1;
            padding:.65rem 1rem; font-size:.87rem; transition:all .2s;
            font-family:'Poppins',sans-serif;
        }
        .form-control::placeholder { color:#334d6e; }
        .form-control:focus {
            border-color:#38bdf8; background:#132035;
            box-shadow:0 0 0 3px rgba(56,189,248,.15);
            color:#e2e8f0; outline:none;
        }
        .input-group-text {
            border-radius:0 12px 12px 0; background:#101e32;
            border:1.5px solid #1a2e4a; border-left:none;
            cursor:pointer; color:#334d6e; transition:color .2s;
        }
        .input-group-text:hover { color:#38bdf8; }

        .btn-login {
            background:linear-gradient(135deg,#38bdf8,#0284c7);
            color:#fff; border:none; border-radius:12px;
            padding:.75rem; font-weight:700; font-size:.9rem;
            width:100%; transition:all .25s;
            font-family:'Nunito',sans-serif; letter-spacing:.3px;
            box-shadow:0 4px 18px rgba(14,165,233,.4);
        }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(14,165,233,.5); color:#fff; opacity:.95; }
        .btn-login:active { transform:translateY(0); }

        .form-check-input { background-color:#101e32; border-color:#1a2e4a; }
        .form-check-input:checked { background-color:#0ea5e9; border-color:#0ea5e9; }
        .form-check-label { font-size:.82rem; color:#4a6080; }

        .link-register { color:#38bdf8; font-weight:700; text-decoration:none; }
        .link-register:hover { color:#7dd3fc; }
        .footer-text { color:#1e3a5f; font-size:.74rem; }

        @media(max-width:768px){
            .wrapper{flex-direction:column;width:95%;min-height:auto;}
            .left-panel,.right-panel{width:100%;}
            .left-panel{min-height:320px;}
        }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- ===== LEFT ===== -->
    <div class="left-panel">

        <!-- 3 awan uni-directional -->
        <div class="cloud cloud-1"></div>
        <div class="cloud cloud-2"></div>
        <div class="cloud cloud-3"></div>
        <!-- 3 awan bolak-balik -->
        <div class="cloud cloud-4"></div>
        <div class="cloud cloud-5"></div>
        <div class="cloud cloud-6"></div>

        <!-- Balon udara -->
        <div class="balloon-wrap">
            <svg width="52" height="72" viewBox="0 0 52 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="26" cy="28" rx="22" ry="26" fill="white" opacity="0.95"/>
                <path d="M26 2 Q18 28 26 54 Q34 28 26 2Z" fill="#0ea5e9" opacity="0.75"/>
                <path d="M4 28 Q16 22 26 54 Q16 42 4 28Z" fill="#bae6fd" opacity="0.6"/>
                <path d="M48 28 Q36 22 26 54 Q36 42 48 28Z" fill="#bae6fd" opacity="0.6"/>
                <line x1="18" y1="52" x2="20" y2="63" stroke="#94a3b8" stroke-width="1.5"/>
                <line x1="34" y1="52" x2="32" y2="63" stroke="#94a3b8" stroke-width="1.5"/>
                <rect x="18" y="62" width="16" height="10" rx="3" fill="#f59e0b"/>
                <rect x="18" y="62" width="16" height="3" rx="1.5" fill="#d97706"/>
                <line x1="22" y1="62" x2="22" y2="72" stroke="#d97706" stroke-width="1" opacity="0.5"/>
                <line x1="26" y1="62" x2="26" y2="72" stroke="#d97706" stroke-width="1" opacity="0.5"/>
                <line x1="30" y1="62" x2="30" y2="72" stroke="#d97706" stroke-width="1" opacity="0.5"/>
            </svg>
        </div>

        <!-- Judul — di atas air -->
        <div class="panel-title">
            <h2>Smart Swimming Pool</h2>
            <p>Sistem IoT Kolam Renang Pintar</p>
        </div>

        <!-- Gelombang batas air -->
        <div class="wave-svg">
            <svg viewBox="0 0 494 65" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:65px;display:block;">
                <path d="M0,22 C60,6 120,42 180,22 C240,2 300,40 360,20 C420,2 460,36 494,16 L494,65 L0,65Z" fill="rgba(255,255,255,0.22)"/>
                <path d="M0,34 C50,20 110,52 170,30 C230,10 290,46 350,28 C410,10 455,44 494,26 L494,65 L0,65Z" fill="rgba(255,255,255,0.32)"/>
            </svg>
        </div>

        <!-- Perenang -->
        <div class="swimmer-wrap">
            <svg width="110" height="40" viewBox="0 0 110 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="88" cy="12" r="10" fill="#fcd34d"/>
                <circle cx="88" cy="12" r="10" fill="none" stroke="#f59e0b" stroke-width="1.5"/>
                <path d="M80 10 Q88 2 96 10" fill="#0ea5e9"/>
                <ellipse cx="84" cy="13" rx="4" ry="3" fill="white" opacity="0.8"/>
                <ellipse cx="92" cy="13" rx="4" ry="3" fill="white" opacity="0.8"/>
                <path d="M78 18 Q60 14 40 20 Q20 25 0 20" stroke="#fcd34d" stroke-width="8" stroke-linecap="round" fill="none"/>
                <path d="M70 15 Q55 5 38 8" stroke="#fcd34d" stroke-width="6" stroke-linecap="round" fill="none"/>
                <path d="M0 20 Q15 16 30 22 Q45 28 60 20" stroke="white" stroke-width="2" fill="none" opacity="0.6"/>
            </svg>
        </div>

        <!-- Kolam renang -->
        <div class="pool-surface">
            <div class="lane-line"></div><div class="lane-line"></div>
            <div class="lane-line"></div><div class="lane-line"></div>
            <div class="bubble-water" style="width:8px;height:8px;bottom:20%;left:15%;animation-duration:2.2s;"></div>
            <div class="bubble-water" style="width:5px;height:5px;bottom:15%;left:30%;animation-duration:1.8s;animation-delay:.5s;"></div>
            <div class="bubble-water" style="width:7px;height:7px;bottom:25%;left:55%;animation-duration:2.5s;animation-delay:1s;"></div>
            <div class="bubble-water" style="width:4px;height:4px;bottom:18%;left:72%;animation-duration:1.5s;animation-delay:.3s;"></div>
            <div class="bubble-water" style="width:6px;height:6px;bottom:30%;left:85%;animation-duration:2s;animation-delay:.8s;"></div>
            <div class="ripple-edge" style="width:40px;height:40px;bottom:28%;left:22%;animation-delay:0s;"></div>
            <div class="ripple-edge" style="width:30px;height:30px;bottom:22%;left:60%;animation-delay:1.2s;"></div>
            <div class="ripple-edge" style="width:35px;height:35px;bottom:35%;left:80%;animation-delay:.6s;"></div>
        </div>

        <!-- Pelampung -->
        <div class="float-ring" style="right:8%;bottom:42%;">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                <circle cx="24" cy="24" r="20" fill="none" stroke="#f97316" stroke-width="9"/>
                <path d="M4 24 A20 20 0 0 1 24 4" stroke="white" stroke-width="9" fill="none"/>
                <path d="M24 44 A20 20 0 0 1 44 24" stroke="white" stroke-width="9" fill="none"/>
            </svg>
        </div>

        <!-- Bintang laut -->
        <div class="deco-item" style="right:12%;bottom:28%;animation-delay:1s;">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                <path d="M16 3 L18.5 12 L27 10 L21 17 L27 24 L18.5 22 L16 31 L13.5 22 L5 24 L11 17 L5 10 L13.5 12 Z" fill="#f97316" opacity="0.9"/>
            </svg>
        </div>

        <!-- Kerang -->
        <div class="deco-item" style="right:22%;bottom:24%;animation-delay:.5s;">
            <svg width="22" height="20" viewBox="0 0 22 20" fill="none">
                <path d="M11 1 Q20 4 20 12 Q20 18 11 19 Q2 18 2 12 Q2 4 11 1Z" fill="#fb923c" opacity="0.85"/>
                <path d="M11 1 L11 19" stroke="white" stroke-width="1" opacity="0.5"/>
                <path d="M4 7 Q11 5 18 7" stroke="white" stroke-width="1" opacity="0.4" fill="none"/>
                <path d="M3 11 Q11 9 19 11" stroke="white" stroke-width="1" opacity="0.4" fill="none"/>
            </svg>
        </div>

        <div class="deco-item" style="right:5%;bottom:24%;animation-delay:1.5s;">
            <svg width="16" height="14" viewBox="0 0 16 14" fill="none">
                <path d="M8 1 Q14 3 14 8 Q14 13 8 13 Q2 13 2 8 Q2 3 8 1Z" fill="#fdba74" opacity="0.9"/>
                <path d="M8 1 L8 13" stroke="white" stroke-width="0.8" opacity="0.5"/>
            </svg>
        </div>
    </div>

    <!-- ===== RIGHT — FORM LOGIN ===== -->
    <div class="right-panel">
        <div class="right-inner">
            <div class="text-center mb-4">
                <div class="brand-icon"><i class="bi bi-water"></i></div>
                <h4>Selamat Datang!</h4>
                <p class="sub">Masuk ke akun Smart Swimming Pool Anda</p>
            </div>

            @if($errors->any())
                <div class="alert rounded-3 py-2 small mb-3" style="background:#12202e;border:1px solid #7f1d1d;color:#fca5a5;font-size:.82rem;">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="email@contoh.com" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passInput"
                            class="form-control" placeholder="••••••••" required
                            style="border-radius:12px 0 0 12px;">
                        <span class="input-group-text" onclick="togglePass()">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <p class="text-center mt-3 mb-1" style="font-size:.82rem;color:#334d6e;">
                Belum punya akun?
                <a href="{{ route('register') }}" class="link-register">Daftar di sini</a>
            </p>
            <p class="text-center footer-text mt-2 mb-0">
                &copy; {{ date('Y') }} Smart Swimming Pool — K2 Management
            </p>
        </div>
    </div>

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
