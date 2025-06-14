<?php
// semua_berita.php
require_once 'config/database.php';
require_once 'includes/berita_functions.php';

// Security
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

$beritaManager = new BeritaManager();

// Pagination settings
$page = validate_page($_GET['page'] ?? 1);
$limit = 6;
$offset = ($page - 1) * $limit;

// Get berita data
$berita_list = $beritaManager->getBerita($limit, $offset);
$total_berita = $beritaManager->getTotalBerita();
$total_pages = ceil($total_berita / $limit);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Berita - Vihara Watugong</title>
    <meta name="description" content="Kumpulan lengkap berita dan informasi dari Vihara Watugong.">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/berita.css">
    <link rel="stylesheet" href="css/berita-page.css">

    <style>
        :root {
            --primary-orange: #d3a84a;
            --primary-dark: #b8944a;
            --dark-bg: #1a1a1a;
            --medium-gray: #666666;
            --light-gray: #f8f9fa;
            --white: #ffffff;
            --text-dark: #2c3e50;
            --border-light: #e9ecef;
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-hover: rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--light-gray);
        }

        /* Header & Navigation */
        .navbar {
            background: var(--dark-bg);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--white);
            font-weight: 600;
            font-size: 1.25rem;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            margin-right: 0.75rem;
            border-radius: 8px;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--white);
        }

        .language-switcher {
            display: flex;
            border-radius: 8px;
            overflow: hidden;
            margin-left: 1rem;
        }

        .lang-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #f5f5f5;
            color: #333;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .lang-btn.active {
            background: var(--primary-orange);
            color: var(--white);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Mobile Navigation */
        .mobile-nav {
            display: none;
            background: var(--dark-bg);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
        }

        .mobile-nav.active {
            display: block;
        }

        .mobile-nav .nav-link {
            display: block;
            padding: 0.75rem 2rem;
            border-radius: 0;
        }

        .mobile-nav .nav-link:hover {
            background: rgba(211, 168, 74, 0.1);
        }

        .mobile-nav .language-switcher {
            margin: 1rem 2rem 0;
            width: fit-content;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 4rem 0;
            text-align: center;
        }

        .page-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* News Section */
        .news-section {
            padding: 4rem 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .news-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
        }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px var(--shadow-hover);
        }

        .news-card__image {
            position: relative;
            height: 240px;
            overflow: hidden;
        }

        .news-card__image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .news-card:hover .news-card__image img {
            transform: scale(1.05);
        }

        .news-card__category {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: var(--primary-orange);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .news-card__content {
            padding: 1.5rem;
        }

        .news-card__meta {
            margin-bottom: 1rem;
        }

        .news-card__meta time {
            color: var(--medium-gray);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .news-card__title {
            margin-bottom: 1rem;
        }

        .news-card__title a {
            color: var(--text-dark);
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.4;
            transition: color 0.3s ease;
        }

        .news-card__title a:hover {
            color: var(--primary-orange);
        }

        .news-card__excerpt {
            color: var(--medium-gray);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .news-card__link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-orange);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .news-card__link:hover {
            color: var(--primary-dark);
            transform: translateX(4px);
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 3rem;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--white);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px var(--shadow);
            border: 1px solid var(--border-light);
        }

        .pagination-link,
        .pagination-number {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            color: var(--text-dark);
            border: 1px solid transparent;
            min-width: 44px;
            text-align: center;
        }

        .pagination-link:hover,
        .pagination-number:hover {
            background: var(--primary-orange);
            color: var(--white);
            border-color: var(--primary-orange);
        }

        .pagination-number.active {
            background: var(--primary-orange);
            color: var(--white);
            border-color: var(--primary-orange);
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px var(--shadow);
            border: 1px solid var(--border-light);
        }

        .no-results-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .no-results h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .no-results p {
            color: var(--medium-gray);
            font-size: 1.1rem;
        }

        /* Footer */
        .footer {
            background: var(--dark-bg);
            color: var(--white);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: var(--primary-orange);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .footer-section p {
            line-height: 1.6;
            margin-bottom: 1rem;
            color: #CCCCCC;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: #CCCCCC;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--primary-orange);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #444444;
            color: #CCCCCC;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: black;
            color: var(--white);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 20px var(--shadow);
        }

        .back-to-top:hover {
            transform: translateY(-2px);
            background: var(--dark-bg);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .navbar-container {
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {

            .navbar-nav,
            .language-switcher {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .page-title {
                font-size: 2.5rem;
            }

            .page-subtitle {
                font-size: 1.1rem;
            }

            .news-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .pagination {
                flex-wrap: wrap;
                justify-content: center;
                padding: 1rem;
            }

            .pagination-link,
            .pagination-number {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 1rem;
            }

            .page-header {
                padding: 3rem 0;
            }

            .page-title {
                font-size: 2rem;
            }

            .news-section {
                padding: 2rem 0;
            }

            .news-card__content {
                padding: 1.25rem;
            }

            .news-card__title a {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <header class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <img src="favicon 1.png" alt="Vihara Watugong">
                <span>Vihara Watugong</span>
            </a>

            <div class="navbar-nav">
                <a href="index.php" class="nav-link">Beranda</a>
                <a href="index.php#sejarah" class="nav-link">Sejarah</a>
                <a href="index.php#fasilitas" class="nav-link">Fasilitas</a>
                <a href="index.php#kegiatan" class="nav-link">Kegiatan</a>
                <a href="semua_berita.php" class="nav-link active">Berita</a>
                <a href="index.php#kontak" class="nav-link">Kontak</a>
            </div>

            <div class="language-switcher">
                <button class="lang-btn active">ID</button>
                <button class="lang-btn">EN</button>
            </div>

            <button class="mobile-menu-btn">☰</button>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-nav" id="mobileNav">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="index.php#sejarah" class="nav-link">Sejarah</a>
            <a href="index.php#fasilitas" class="nav-link">Fasilitas</a>
            <a href="index.php#kegiatan" class="nav-link">Kegiatan</a>
            <a href="semua_berita.php" class="nav-link active">Berita</a>
            <a href="index.php#kontak" class="nav-link">Kontak</a>
            <div class="language-switcher">
                <button class="lang-btn active">ID</button>
                <button class="lang-btn">EN</button>
            </div>
        </div>
    </header>

    <main>
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <h1 class="page-title">Semua Berita</h1>
                <p class="page-subtitle">
                    Temukan informasi lengkap seputar kegiatan dan perkembangan Vihara Watugong
                </p>
            </div>
        </section>

        <!-- News Section -->
        <section class="news-section">
            <div class="container">
                <?php if (!empty($berita_list)): ?>
                    <div class="news-grid">
                        <?php foreach ($berita_list as $berita): ?>
                            <article class="news-card">
                                <div class="news-card__image">
                                    <img src="images/berita/<?= htmlspecialchars($berita['gambar_utama']) ?>"
                                        alt="<?= htmlspecialchars($berita['judul']) ?>"
                                        loading="lazy">
                                    <div class="news-card__category">
                                        <?= htmlspecialchars($berita['nama_kategori']) ?>
                                    </div>
                                </div>
                                <div class="news-card__content">
                                    <div class="news-card__meta">
                                        <time datetime="<?= $berita['tanggal_publish'] ?>">
                                            <?= format_tanggal($berita['tanggal_publish']) ?>
                                        </time>
                                    </div>
                                    <h3 class="news-card__title">
                                        <a href="detail_berita.php?slug=<?= urlencode($berita['slug']) ?>">
                                            <?= htmlspecialchars($berita['judul']) ?>
                                        </a>
                                    </h3>
                                    <p class="news-card__excerpt">
                                        <?= htmlspecialchars($berita['excerpt']) ?>
                                    </p>
                                    <a href="detail_berita.php?slug=<?= urlencode($berita['slug']) ?>"
                                        class="news-card__link">
                                        Baca Selengkapnya →
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination-wrapper">
                            <nav class="pagination" aria-label="Navigasi halaman">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?>" class="pagination-link">
                                        ← Sebelumnya
                                    </a>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);

                                for ($i = $start; $i <= $end; $i++): ?>
                                    <a href="?page=<?= $i ?>"
                                        class="pagination-number <?= ($i == $page) ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?= $page + 1 ?>" class="pagination-link">
                                        Selanjutnya →
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="no-results">
                        <div class="no-results-icon">📰</div>
                        <h3>Belum ada berita tersedia</h3>
                        <p>Silakan kembali lagi nanti untuk melihat berita terbaru.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>Vihara Watugong</h3>
                    <p>Vihara Watugong adalah pusat kegiatan spiritual Buddha yang berlokasi di Bekasi, West Java. Kami menyediakan tempat untuk meditasi, pembelajaran dharma, dan kegiatan keagamaan Buddha.</p>
                </div>

                <div class="footer-section">
                    <h3>Menu Utama</h3>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="index.php#sejarah">Sejarah Vihara</a></li>
                        <li><a href="index.php#fasilitas">Fasilitas</a></li>
                        <li><a href="index.php#kegiatan">Kegiatan</a></li>
                        <li><a href="semua_berita.php">Berita</a></li>
                        <li><a href="index.php#kontak">Kontak</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Kegiatan Dharma</h3>
                    <ul>
                        <li><a href="#">Meditasi Mingguan</a></li>
                        <li><a href="#">Kelas Dharma</a></li>
                        <li><a href="#">Perayaan Hari Raya</a></li>
                        <li><a href="#">Kebaktian Purnama</a></li>
                        <li><a href="#">Retreat Spiritual</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <p>📍 Jl. Watugong No. 123, Bekasi, West Java</p>
                    <p>📞 +62 21 1234 5678</p>
                    <p>✉️ info@viharawatugong.org</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2024 Vihara Watugong. Semua hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">↑</button>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const mobileNav = document.getElementById('mobileNav');

        mobileMenuBtn.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
        });

        // Language switcher
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons in the same group
                const parent = this.parentElement;
                parent.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
            });
        });

        // Back to top button
        const backToTopBtn = document.getElementById('backToTop');

        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });

        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Smooth scrolling for pagination
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function() {
                setTimeout(() => {
                    document.querySelector('.page-header').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            });
        });

        // Image loading animation
        document.querySelectorAll('.news-card__image img').forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.navbar-container') && mobileNav.classList.contains('active')) {
                mobileNav.classList.remove('active');
            }
        });
    </script>
</body>

</html>