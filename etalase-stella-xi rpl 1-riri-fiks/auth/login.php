<?php
session_start();
require_once __DIR__ . '/../config/kuis_db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, nama, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        header('Location: ../index.php');
        exit;
    }
    $error = 'Email atau password tidak sesuai.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Etalase Digital Stella</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=2">
    <link rel="stylesheet" href="../assets/css/auth.css?v=3">
</head>
<body class="auth-wrapper">
    <main class="auth-card">
        <div class="auth-brand">
            <img src="../assets/telkom-schools-logo.png" alt="Logo Telkom Schools">
        </div>
        <h1>Etalase Stella</h1>
        <p class="muted">Pameran Proyek Digital Siswa</p>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="email">Email Sekolah</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <path d="m4 7 8 6 8-6"></path>
                </svg>
                <input
                    class="form-control"
                    type="email"
                    id="email"
                    name="email"
                    placeholder="example@ts.stella.sch.id"
                    autocomplete="email"
                    required
                >
            </div>

            <label for="password">Password</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <circle cx="8" cy="15" r="4"></circle>
                    <path d="m11 12 8-8 2 2-2 2 2 2-3 3-2-2-3 3"></path>
                </svg>
                <input
                    class="form-control"
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password Anda"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button class="btn btn-primary full" type="submit">
                Masuk
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <path d="M10 17l5-5-5-5M15 12H3"></path>
                    <path d="M13 4h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6"></path>
                </svg>
            </button>
        </form>

        <p class="auth-foot">
            Belum punya akun siswa? <a href="register.php">Daftar di sini</a>
        </p>
    </main>
</body>
</html>
