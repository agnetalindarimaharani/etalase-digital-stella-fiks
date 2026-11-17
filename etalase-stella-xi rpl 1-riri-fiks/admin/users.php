<?php
require_once '../config/kuis_db.php';
require_once '../middleware/auth_check.php';

// Memastikan hanya Super Admin yang bisa mengakses halaman ini
check_role(['super_admin']);

// Tambah Admin Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_admin'])) {
    $nama  = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :pass, 'admin')");
    $stmt->execute([':nama' => $nama, ':email' => $email, ':pass' => $pass]);
}

// Hapus User
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role != 'super_admin'");
    $stmt->execute([':id' => $_GET['hapus']]);
    header('Location: users.php');
    exit();
}

$users = $pdo->query("SELECT * FROM users ORDER BY role ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola User - Super Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin.css?v=2">
</head>

<body>
    <main class="user-page">
        <a class="user-back-link" href="../index.php">&larr; Kembali ke Etalase</a>
        <header class="user-page-heading">
            <p class="eyebrow">SUPER ADMIN</p>
            <h1>Manajemen User</h1>
            <p>Tambah admin dan kelola akun pengguna sistem.</p>
        </header>

        <section class="admin-card user-card">
            <h2>Tambah Admin Baru</h2>
            <form method="POST" class="user-form">
                <label>
                    Nama
                    <input type="text" name="nama" placeholder="Nama Admin" required>
                </label>
                <label>
                    Email
                    <input type="email" name="email" placeholder="nama@email.com" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" placeholder="Password" required>
                </label>
                <button class="btn btn-primary" type="submit" name="tambah_admin">Tambah Admin</button>
            </form>
        </section>

        <section class="admin-card user-card">
            <h2>Daftar Pengguna</h2>
            <div class="table-wrap">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['nama']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="role-pill"><?= htmlspecialchars($u['role']) ?></span></td>
                                <td>
                                    <?php if ($u['role'] !== 'super_admin'): ?>
                                        <a class="user-delete-link" href="users.php?hapus=<?= $u['id'] ?>" onclick="return confirm('Hapus user ini?')">Hapus</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>