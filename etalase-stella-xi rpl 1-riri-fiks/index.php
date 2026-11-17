<?php
require_once __DIR__ . '/config/kuis_db.php';
require_once __DIR__ . '/middleware/auth_check.php';
check_login();

$stmt = $pdo->query(
    "SELECT p.*, u.nama AS pembuat
    FROM projects p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC"
);
$projects = $stmt->fetchAll();
$role = $_SESSION['role'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etalase Digital Stella</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <link rel="stylesheet" href="assets/css/main.css?v=2">
</head>
<body>
    <header class="navbar">
        <a class="logo dashboard-brand" href="index.php">
            <img src="assets/telkom-schools-logo.png" alt="">
            <span>Etalase Digital Stella</span>
        </a>
        <nav class="navbar-user">
            <span>
                Halo, <b><?= e($_SESSION['nama']) ?></b>
                <small class="role"><?= e(str_replace('_', ' ', $role)) ?></small>
            </span>
            <a class="btn btn-primary" href="project_tambah.php">+ Tambah Karya</a>
            <?php if ($role === 'super_admin'): ?>
                <a class="btn btn-light" href="admin/users.php">Kelola User</a>
            <?php endif; ?>
            <a class="btn btn-danger" href="auth/logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <section class="hero">
            <p class="eyebrow">KREATIVITAS • KOLABORASI • INOVASI</p>
            <h1>Etalase Karya Digital<br><span>Siswa Stella</span></h1>
            <p>Temukan ide, eksplorasi karya, dan inspirasi dari siswa.</p>
        </section>

        <div class="section-head">
            <div class="section-title">
                <h2>Karya Terbaru</h2>
                <p class="muted" id="project-count" aria-live="polite"><?= count($projects) ?> karya ditampilkan</p>
            </div>
            <label class="search-control">
                <span class="visually-hidden">Cari karya</span>
                <svg class="search-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                    <circle cx="10.8" cy="10.8" r="6.8"></circle>
                    <path d="m16 16 4.2 4.2"></path>
                </svg>
                <input
                    type="search"
                    id="project-search"
                    placeholder="Cari nama karya, deskripsi, atau pembuat..."
                    autocomplete="off"
                >
            </label>
        </div>

        <?php if (!$projects): ?>
            <div class="empty-state">
                <h3>Belum ada karya</h3>
                <p>Jadilah yang pertama menampilkan project di etalase.</p>
                <a class="btn btn-primary" href="project_tambah.php">Tambah karya pertama</a>
            </div>
        <?php else: ?>
            <section class="project-grid">
                <?php foreach ($projects as $p): ?>
                    <article class="project-card">
                        <img
                            class="project-image"
                            src="uploads/<?= e($p['gambar']) ?>"
                            alt="<?= e($p['nama_project']) ?>"
                        >
                        <div class="project-content">
                            <span class="tag">PROJECT</span>
                            <h3><?= e($p['nama_project']) ?></h3>
                            <p class="byline">Oleh <?= e($p['pembuat']) ?></p>
                            <p class="description"><?= nl2br(e($p['deskripsi'])) ?></p>

                            <div class="card-actions">
                                <a
                                    class="btn btn-primary"
                                    href="<?= e($p['link_project']) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >Lihat Project ↗</a>
                                <?php if (can_manage_project($p)): ?>
                                    <a class="btn btn-light" href="project_edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
                                    <form
                                        method="post"
                                        action="project_hapus.php"
                                        onsubmit="return confirm('Yakin ingin menghapus karya ini?')"
                                    >
                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e(csrf_token()) ?>"
                                        >
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
            <div class="empty-state search-empty" id="search-empty" hidden>
                <h3>Karya tidak ditemukan</h3>
                <p>Coba kata kunci lain.</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">© <?= date('Y') ?> Etalase Digital Stella</footer>
    <script>
        const searchInput = document.getElementById('project-search');
        const projectCards = Array.from(document.querySelectorAll('.project-card'));
        const projectCount = document.getElementById('project-count');
        const searchEmpty = document.getElementById('search-empty');
        const totalProjects = projectCards.length;

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLocaleLowerCase('id');
            let visibleProjects = 0;

            projectCards.forEach((card) => {
                const searchableText = [
                    card.querySelector('h3')?.textContent,
                    card.querySelector('.byline')?.textContent,
                    card.querySelector('.description')?.textContent,
                ].join(' ').toLocaleLowerCase('id');
                const matches = searchableText.includes(query);

                card.hidden = !matches;
                visibleProjects += Number(matches);
            });

            if (projectCount) {
                projectCount.textContent = query
                    ? `${visibleProjects} dari ${totalProjects} karya ditampilkan`
                    : `${totalProjects} karya ditampilkan`;
            }
            if (searchEmpty) {
                searchEmpty.hidden = visibleProjects !== 0;
            }
        });
    </script>
</body>
</html>
