<?php
// detail_berita.php
require_once 'config/database.php';
require_once 'includes/berita_functions.php';

// Security
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

$beritaManager = new BeritaManager();

// Get berita slug from URL
$slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: semua_berita.php');
    exit;
}

// Get berita detail
$berita = $beritaManager->getBeritaBySlug($slug);

if (!$berita) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Get related berita (same category, exclude current)
$related_berita = $beritaManager->getBerita(3, 0);
?>
<!DOCTYPE html>
<html lang="id">
<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
<link rel="manifest" href="site.webmanifest">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($berita['judul']) ?> - Vihara Watugong</title>
    <meta name="description" content="<?= htmlspecialchars($berita['excerpt']) ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($berita['judul']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($berita['excerpt']) ?>">
    <meta property="og:image" content="<?= htmlspecialchars('images/berita/' . $berita['gambar_utama']) ?>">
    <meta property="og:type" content="article">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-orange: #d3a84a;
            --light-orange: #FFA07A;
            --dark-gray: #2C2C2C;
            --black: #fff;
            --medium-gray: #666666;
            --light-gray: #F5F5F5;
            --white: #FFFFFF;
            --text-dark: #333333;
            --border-light: #E0E0E0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: var(--white);
        }

        /* Header Navbar */
        .navbar {
            background-color: black;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #F5F5F5;
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            margin-right: 0.75rem;
            border-radius: 8px;
        }

        .navbar-brand span {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: white;
        }

        .nav-link.active {
            color: white;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background-color: white;
        }

        .language-switcher {
            display: flex;
            gap: 0.5rem;
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
            border: none;
            cursor: pointer;
        }

        .lang-btn:hover,
        .lang-btn.active {
            background: var(--primary-orange);
            color: var(--white);
            border-color: var(--primary-orange);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            cursor: pointer;
            color: white;
            padding: 0.5rem;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            gap: 4px;
        }

        .mobile-menu-btn span {
            width: 25px;
            height: 3px;
            background-color: white;
            display: block;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .mobile-menu-btn.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .mobile-menu-btn.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-btn.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            color: var(--medium-gray);
        }

        .breadcrumb a {
            color: var(--primary-orange);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb .separator {
            color: var(--medium-gray);
        }

        /* Article */
        .article-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .article-header {
            margin-bottom: 2rem;
        }

        .article-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .article-category {
            background: var(--primary-orange);
            color: var(--white);
            padding: 0.25rem 0.75rem;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .article-date {
            color: var(--medium-gray);
            font-size: 0.9rem;
        }

        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        .article-image {
            margin: 2rem 0;
            border-radius: 12px;
            overflow: hidden;
        }

        .article-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }

        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-dark);
            margin: 2rem 0;
        }

        .article-content p {
            margin-bottom: 1.5rem;
        }

        .secondary-image {
            margin: 2rem 0;
            text-align: center;
            border-radius: 12px;
            overflow: hidden;
        }

        .secondary-image img {
            width: 100%;
            max-width: 600px;
            height: auto;
            border-radius: 12px;
        }

        /* Article Actions */
        .article-actions {
            display: flex;
            gap: 1rem;
            margin: 3rem 0;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-orange);
            color: var(--white);
        }

        .btn-primary:hover {
            background: #B88E2F;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--light-gray);
            color: var(--text-dark);
            border: 1px solid var(--border-light);
        }

        .btn-secondary:hover {
            background: var(--border-light);
            transform: translateY(-2px);
        }

        /* Related Articles */
        .related-section {
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid var(--border-light);
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 2rem;
            text-align: center;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .related-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
        }

        .related-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .related-image {
            height: 200px;
            overflow: hidden;
        }

        .related-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .related-card:hover .related-image img {
            transform: scale(1.05);
        }

        .related-content {
            padding: 1.5rem;
        }

        .related-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .related-category {
            background: var(--primary-orange);
            color: var(--white);
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .related-date {
            color: var(--medium-gray);
            font-size: 0.8rem;
        }

        .related-title a {
            color: var(--text-dark);
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.4;
            transition: color 0.3s ease;
        }

        .related-title a:hover {
            color: var(--primary-orange);
        }

        .related-excerpt {
            color: var(--medium-gray);
            line-height: 1.6;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* Footer */
        .footer {
            background: var(--dark-gray);
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
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            z-index: 1000;
            display: none;
        }

        .back-to-top:hover {
            transform: translateY(-2px);
            background: black;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: black;
                flex-direction: column;
                gap: 0;
                padding: 1rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                border-top: 1px solid #333;
            }

            .navbar-nav.mobile-active {
                display: flex;
            }

            .nav-link {
                padding: 1rem 0;
                border-bottom: 1px solid #333;
                width: 100%;
                color: rgba(255, 255, 255, 0.8);
            }

            .nav-link:hover {
                color: white;
            }

            .nav-link.active {
                color: var(--primary-orange);
            }

            .mobile-menu-btn {
                display: flex;
            }

            .main-content {
                padding: 1rem;
            }

            .article-title {
                font-size: 2rem;
            }

            .article-image img {
                height: 250px;
            }

            .related-grid {
                grid-template-columns: 1fr;
            }

            .article-actions {
                flex-direction: column;
                align-items: center;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .language-switcher {
                margin-left: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .article-title {
                font-size: 1.5rem;
            }

            .article-content {
                font-size: 1rem;
            }

            .breadcrumb {
                font-size: 0.8rem;
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
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

            <button class="mobile-menu-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="index.php">Beranda</a>
            <span class="separator">›</span>
            <a href="semua_berita.php">Berita</a>
            <span class="separator">›</span>
            <span><?= htmlspecialchars($berita['judul']) ?></span>
        </nav>

        <!-- Article -->
        <article class="article-container">
            <header class="article-header">
                <div class="article-meta">
                    <span class="article-category"><?= htmlspecialchars($berita['nama_kategori']) ?></span>
                    <span class="article-date"><?= date('d F Y', strtotime($berita['tanggal_publish'])) ?></span>
                </div>
                <h1 class="article-title"><?= htmlspecialchars($berita['judul']) ?></h1>
            </header>

            <div class="article-image">
                <img src="images/berita/<?= htmlspecialchars($berita['gambar_utama']) ?>"
                    alt="<?= htmlspecialchars($berita['judul']) ?>">
            </div>

            <div class="article-content">
                <?= $berita['konten'] ?>

                <?php if (!empty($berita['gambar_kedua'])): ?>
                    <div class="secondary-image">
                        <img src="images/berita/<?= htmlspecialchars($berita['gambar_kedua']) ?>"
                            alt="Gambar pendukung - <?= htmlspecialchars($berita['judul']) ?>">
                    </div>
                <?php endif; ?>
            </div>

            <div class="article-actions">
                <button class="btn btn-primary" onclick="shareArticle()">
                    Bagikan
                </button>
                <a href="semua_berita.php" class="btn btn-secondary">
                    ← Kembali ke Berita
                </a>
            </div>
        </article>

        <!-- Related Articles -->
        <?php if (!empty($related_berita)): ?>
            <section class="related-section">
                <h2 class="section-title">Berita Terkait</h2>
                <div class="related-grid">
                    <?php foreach ($related_berita as $related): ?>
                        <article class="related-card">
                            <div class="related-image">
                                <img src="images/berita/<?= htmlspecialchars($related['gambar_utama']) ?>"
                                    alt="<?= htmlspecialchars($related['judul']) ?>">
                            </div>
                            <div class="related-content">
                                <div class="related-meta">
                                    <span class="related-category"><?= htmlspecialchars($related['nama_kategori']) ?></span>
                                    <span class="related-date"><?= date('d M Y', strtotime($related['tanggal_publish'])) ?></span>
                                </div>
                                <h3 class="related-title">
                                    <a href="detail_berita.php?slug=<?= htmlspecialchars($related['slug']) ?>">
                                        <?= htmlspecialchars($related['judul']) ?>
                                    </a>
                                </h3>
                                <p class="related-excerpt">
                                    <?= htmlspecialchars(substr($related['excerpt'], 0, 120)) ?>...
                                </p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <!-- Footer -->
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

    <!-- Back to Top -->
    <button class="back-to-top" onclick="scrollToTop()">↑</button>

    <script>
        // Share function
        function shareArticle() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    text: 'Baca artikel menarik ini dari Vihara Watugong',
                    url: window.location.href
                }).catch(console.error);
            } else {
                const url = window.location.href;
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(() => {
                        alert('Link berhasil disalin ke clipboard!');
                    }).catch(() => {
                        const textArea = document.createElement('textarea');
                        textArea.value = url;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                        alert('Link berhasil disalin ke clipboard!');
                    });
                }
            }
        }

        // Back to top functionality
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Show/hide back to top button
        window.addEventListener('scroll', function() {
            const backToTopBtn = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const navbarNav = document.querySelector('.navbar-nav');

            mobileMenuBtn.addEventListener('click', function() {
                navbarNav.classList.toggle('mobile-active');
                mobileMenuBtn.classList.toggle('active');
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.navbar-container')) {
                    navbarNav.classList.remove('mobile-active');
                    mobileMenuBtn.classList.remove('active');
                }
            });

            // Close mobile menu when clicking on nav link
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navbarNav.classList.remove('mobile-active');
                    mobileMenuBtn.classList.remove('active');
                });
            });
        });

        // Language switcher functionality
        document.addEventListener('DOMContentLoaded', function() {
            const langButtons = document.querySelectorAll('.lang-btn');

            langButtons.forEach(button => {
                button.addEventListener('click', function() {
                    langButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const selectedLang = this.textContent;
                    console.log('Language switched to:', selectedLang);
                });
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>

</html>