<?php
require_once __DIR__ . '/../config/kuis_db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasiPassword = $_POST['konfirmasi_password'] ?? '';

    if (
        $nama === ''
        || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 8
    ) {
        $error = 'Lengkapi data dengan benar. Password minimal 8 karakter.';
    } elseif ($password !== $konfirmasiPassword) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (nama, email, password, role)
                VALUES (?, ?, ?, 'siswa')"
            );
            $stmt->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Email sudah digunakan. Gunakan email lain.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | Etalase Digital Stella</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=2">
    <link rel="stylesheet" href="../assets/css/auth.css?v=3">
</head>
<body class="auth-wrapper">
    <main class="auth-card">
        <div class="auth-brand">
            <img src="../assets/telkom-schools-logo.png" alt="Logo Telkom Schools">
        </div>
        <h1>Registrasi Siswa</h1>
        <p class="muted">Buat akun siswa untuk mengunggah karya Anda</p>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <label for="nama">Nama Lengkap Siswa</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="3.5"></circle>
                    <path d="M5 20a7 7 0 0 1 14 0"></path>
                </svg>
                <input
                    class="form-control"
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Nama lengkap sesuai absen"
                    autocomplete="name"
                    required
                    maxlength="100"
                >
            </div>

            <label for="email">Email Sekolah (ts.stella.sch.id)</label>
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

            <label for="password">Password (minimal 8 karakter)</label>
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
                    placeholder="Buat password (min. 8 karakter)"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
                <p class="auth-field-error" id="password-mismatch" aria-live="polite" hidden>
                    Konfirmasi password tidak cocok.
                </p>
            </div>

            <label for="konfirmasi_password">Konfirmasi Password</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3 20 6v5c0 5-3.4 8.3-8 10-4.6-1.7-8-5-8-10V6l8-3Z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
                <input
                    class="form-control"
                    type="password"
                    id="konfirmasi_password"
                    name="konfirmasi_password"
                    placeholder="Ulangi password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </div>

            <button class="btn btn-primary full" type="submit">
                Daftar Sekarang
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <path d="M5 12h14M13 6l6 6-6 6"></path>
                </svg>
            </button>
        </form>

        <p class="auth-foot">
            Sudah punya akun? <a href="login.php">Login</a>
        </p>
    </main>
    <script>
        const passwordInput = document.getElementById('password');
        const confirmationInput = document.getElementById('konfirmasi_password');
        const mismatchMessage = document.getElementById('password-mismatch');

        function validatePasswordConfirmation() {
            const hasBothValues = passwordInput.value !== '' && confirmationInput.value !== '';
            const passwordsMatch = passwordInput.value === confirmationInput.value;
            const isMismatch = hasBothValues && !passwordsMatch;

            mismatchMessage.hidden = !isMismatch;
            confirmationInput.setCustomValidity(
                isMismatch ? 'Konfirmasi password tidak cocok.' : ''
            );
        }

        passwordInput.addEventListener('input', validatePasswordConfirmation);
        confirmationInput.addEventListener('input', validatePasswordConfirmation);
    </script>
</body>
</html>
