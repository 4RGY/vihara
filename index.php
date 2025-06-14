<?php
// index.php
require_once 'config/database.php';
require_once 'includes/berita_functions.php';

// Security: Start session and set security headers
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Initialize BeritaManager
$beritaManager = new BeritaManager();

// Get pagination parameters
$page = validate_page($_GET['page'] ?? 1);
$limit = 3;
$offset = ($page - 1) * $limit;

// Get berita data
$berita_list = $beritaManager->getBerita($limit, $offset);
$total_berita = $beritaManager->getTotalBerita();
$total_pages = ceil($total_berita / $limit);
$show_view_all = $total_berita > $limit && $page == 1;
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<style>
    /* Footer */
    .footer {
        background: rgb(15, 15, 15);
        color: white;
        padding: 3rem 0 1rem;
        margin-top: 4rem;
        margin-bottom: -30px;
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
        color: #d3a84a;
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
        color: #d3a84a;
    }

    .footer-bottom {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid #444444;
        color: #CCCCCC;
    }
</style>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vihara Watugong</title>

    <script>
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('js');
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/styles.css">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">
</head>

<body id="top">
    <!-- Preloader -->
    <div id="preloader">
        <div id="loader"></div>
    </div>

    <!-- Page Wrapper -->
    <div id="page" class="s-pagewrap">

        <!-- Header -->
        <header class="s-header">
            <div class="row s-header__inner">
                <div class="s-header__block">
                    <div class="s-header__logo">
                        <a class="logo" href="index.html">
                            <img src="images/logo.png" alt="Vihara Watugong" style="width: 50px;">
                        </a>
                    </div>
                    <a class="s-header__menu-toggle" href="#0"><span>Beranda</span></a>
                </div>

                <nav class="s-header__nav">
                    <ul class="s-header__menu-links">
                        <li class="current"><a href="#intro" class="smoothscroll">Beranda</a></li>
                        <li><a href="#sejarah" class="smoothscroll">Sejarah</a></li>
                        <li><a href="#fasilitas" class="smoothscroll">Fasilitas</a></li>
                        <li><a href="#kegiatan" class="smoothscroll">Kegiatan</a></li>
                        <li><a href="#berita" class="smoothscroll">Berita</a></li>
                        <li><a href="#kontak" class="smoothscroll">Kontak</a></li>
                    </ul>

                    <ul class="s-header__social language-toggle">
                        <li>
                            <a href="javascript:void(0)" onclick="translateToIndonesian()" title="Bahasa Indonesia">
                                <span>ID</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="translateToEnglish()" title="English">
                                <span>EN</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <section id="content" class="s-content">
            <!-- Intro Section -->
            <section id="intro" class="s-intro target-section">
                <div class="s-intro__bg"></div>
                <div class="row s-intro__content">
                    <div class="s-intro__content-bg"></div>
                    <div class="column lg-12 s-intro__content-inner">
                        <h1 class="s-intro__content-title">
                            Temukan Kedamaian <br>
                            dan Keindahan <br>
                            di Vihara Watugong.
                        </h1>
                        <div class="s-intro__content-buttons">
                            <a href="#sejarah" class="btn btn--stroke s-intro__content-btn smoothscroll">Sejarah Vihara
                                Watugong</a>
                            <a href="https://player.vimeo.com/video/14592941?color=f26522&title=0&byline=0&portrait=0"
                                class="s-intro__content-video-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"style="fill: rgba(0, 0, 0, 1);transform:;msFilter:;">
                                    <path d="M7 6v12l10-6z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="s-intro__scroll-down">
                    <a href="#sejarah" class="smoothscroll">
                        <span>Gulir ke Bawah</span>
                    </a>
                </div>
            </section>

            <!-- Floating Religious Elements -->
            <div class="wg-floating-element">
                <i class="fas fa-dharmachakra" style="font-size: 3rem;"></i>
            </div>
            <div class="wg-floating-element">
                <i class="fas fa-peace" style="font-size: 2.5rem;"></i>
            </div>
            <div class="wg-floating-element">
                <i class="fas fa-lotus" style="font-size: 2rem;"></i>
            </div>
            <!-- History Section -->
            <section id="sejarah" class="wg-about-section">
                <div class="wg-container">
                    <!-- Enhanced Header Section -->
                    <div class="wg-header">
                        <div class="wg-section-number">01</div>
                        <h2 class="wg-pretitle" style="margin-top: -0.8px;">Sejarah Kami</h2>
                        <div class="wg-header-content">
                            <div class="wg-header-title">
                                <h2>Perjalanan Vihara Watugong sebagai pusat pengembangan Buddhadhamma di Indonesia</h2>
                            </div>
                            <div class="wg-header-desc">
                                <h6>Vihara Watugong yang awalnya bernama Vihara Buddha Gaya memiliki sejarah panjang sejak tahun 1955 dan telah menjadi simbol penting dalam
                                    perkembangan agama Buddha di Indonesia.</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Timeline Section -->
                    <div class="wg-timeline">
                        <div class="wg-timeline-item wg-reveal">
                            <div class="wg-timeline-content">
                                <h3>Awal Mula</h3>
                                <div class="wg-timeline-year">1955</div>
                                <p>Tahun 1955 seorang hartawan bernama Goei Thwan Ling (Sutopo) mempersembahkan tanah miliknya untuk digunakan sebagai pusat pengembangan
                                    Buddhadhamma. Tempat itu yang kemudian diberi nama Vihara Buddha Gaya dan pada 19 Oktober 1955 didirikan yayasan Buddha Gaya untuk menaungi
                                    aktivitas vihara.</p>
                            </div>
                            <div class="wg-timeline-image">
                                <img src="images/asoka.jpg" alt="Awal Mula Vihara">
                            </div>
                        </div>

                        <div class="wg-timeline-item wg-reveal">
                            <div class="wg-timeline-image">
                                <img src="images/watugong.JPG" alt="Batu Gong Antik">
                            </div>
                            <div class="wg-timeline-content">
                                <h3>Nama "Watugong"</h3>
                                <div class="wg-timeline-year">1960</div>
                                <p>Masyarakat mengkaitkan vihara dengan keberadaan Watu Gong yang ada di sekitar vihara. Pada kemudian hari, umat menyebut nama lain Vihara Buddha Gaya sebagai Vihara Watu Gong karena di dalamnya terdapat batu antik berbentuk gong. Dari sinilah seolah menjadi spirit tersendiri dimulainya pembabaran agama Buddha di tanah air.</p>
                            </div>
                        </div>

                        <div class="wg-timeline-item wg-reveal">
                            <div class="wg-timeline-content">
                                <h3>Perkembangan Modern</h3>
                                <div class="wg-timeline-year">2000</div>
                                <p>Sesudah mengalami pasang surut organisasi dan pembinaan, sejak tahun 2000 Vihara Buddha Gaya berkembang menjadi sebuah Buddhist Centre yang berfokus untuk menjadi tempat latihan umat dalam mempraktikan dhamma. Ditambah dengan berdirinya beberapa bangunan dan ornamen lain semakin mempercantik Vihara Buddha Gaya.</p>
                            </div>
                            <div class="wg-timeline-image">
                                <img src="images/sanchi.JPG" alt="Vihara Modern">
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Features Grid -->
                    <div class="wg-features-grid">
                        <div class="wg-feature-item wg-reveal">
                            <div class="wg-feature-icon">
                                <i class="fa fa-landmark"></i>
                            </div>
                            <h3>Warisan Budaya</h3>
                            <p>Vihara ini bukan hanya menjadi tempat ibadah umat Buddha, tetapi menjadi milik masyarakat dan pemerintah, sebagai aset berharga dalam pluralisme bangsa dan negara Republik Indonesia.</p>
                        </div>

                        <div class="wg-feature-item wg-reveal">
                            <div class="wg-feature-icon">
                                <i class="fa fa-pray"></i>
                            </div>
                            <h3>Tempat Spiritual</h3>
                            <p>Sebagai tempat praktik Dhamma, Vihara Watugong menyediakan berbagai kegiatan spiritual untuk umat Buddha dan terbuka bagi masyarakat umum yang ingin mempelajari ajaran Buddha serta mengalami kedamaian yang ditawarkan oleh tempat ini.</p>
                        </div>

                        <div class="wg-feature-item wg-reveal">
                            <div class="wg-feature-icon">
                                <i class="fa fa-building"></i>
                            </div>
                            <h3>Arsitektur Menawan</h3>
                            <p>Dengan arsitektur yang memadukan unsur Tiongkok dan lokal, Vihara Watugong memberikan pengalaman visual yang menarik. Ornamen dan struktur bangunan mencerminkan filosofi Buddha serta nilai-nilai budaya yang dijunjung tinggi.</p>
                        </div>

                        <div class="wg-feature-item wg-reveal">
                            <div class="wg-feature-icon">
                                <i class="fa fa-map-marked-alt"></i>
                            </div>
                            <h3>Destinasi Wisata</h3>
                            <p>Selain sebagai tempat ibadah, Vihara Watugong juga menjadi destinasi wisata religi yang menarik. Pengunjung dapat menikmati keindahan bangunan, mempelajari sejarah Buddha, dan merasakan kedamaian yang ditawarkan di lingkungan vihara.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Facilities Section -->
            <section id="fasilitas" class="vw-fasilitas target-section">
                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <div class="wg-section-number">02</div>
                    <h2 class="wg-pretitle" style="margin-top: -0.8px;">Fasilitas Vihara Watugong</h2>
                </div>
                <!-- Map and Facilities Container -->
                <div class="vw-map-facilities-container">
                    <!-- Interactive Map -->
                    <div class="vw-map-container">
                        <div class="vw-map-wrapper">
                            <img src="images/map.JPG" alt="Peta Vihara Watugong" class="vw-map-image">

                            <!-- Map Markers -->
                            <div class="vw-map-marker" style="top: 18%; left: 44.6%;" data-id="watu-gong">
                                <div class="vw-marker blue">1</div>
                                <div class="vw-tooltip">Watu Gong</div>
                            </div>

                            <div class="vw-map-marker" style="top: 27.6%; left: 47%;" data-id="gerbang-sanchi">
                                <div class="vw-marker yellow">2</div>
                                <div class="vw-tooltip">Gerbang Sanchi</div>
                            </div>

                            <div class="vw-map-marker" style="top: 45.7%; left: 52.4%;" data-id="plaza-borobudur">
                                <div class="vw-marker red">3</div>
                                <div class="vw-tooltip">Plaza Borobudur</div>
                            </div>

                            <div class="vw-map-marker" style="top: 43%; left: 31.1%;" data-id="asoka">
                                <div class="vw-marker white">4</div>
                                <div class="vw-tooltip">Tugu dan Prasasti Asoka</div>
                            </div>

                            <div class="vw-map-marker" style="top: 55.7%; left: 20.8%;" data-id="bodhi">
                                <div class="vw-marker blue">5</div>
                                <div class="vw-tooltip">Pohon Bodhi</div>
                            </div>

                            <div class="vw-map-marker" style="top: 75.4%; left: 31.8%;" data-id="avalokitesvara">
                                <div class="vw-marker blue">6</div>
                                <div class="vw-tooltip">Pagoda Avalokitesvara</div>
                            </div>

                            <div class="vw-map-marker" style="top: 78.6%; left: 50.2%;" data-id="b_parinibbana">
                                <div class="vw-marker yellow">7</div>
                                <div class="vw-tooltip">Buddha Parinibbana</div>
                            </div>

                            <div class="vw-map-marker" style="top: 76.2%; left: 61.8%;" data-id="b_sivali">
                                <div class="vw-marker red">8</div>
                                <div class="vw-tooltip">Bangunan Sivali</div>
                            </div>

                            <div class="vw-map-marker" style="top: 72.8%; left: 55%;" data-id="r_berdiri">
                                <div class="vw-marker white">9</div>
                                <div class="vw-tooltip">Rencana Buddha Berdiri</div>
                            </div>

                            <div class="vw-map-marker" style="top: 44.8%; left: 74.8%;" data-id="dhammasala">
                                <div class="vw-marker red">10</div>
                                <div class="vw-tooltip">Dhammasala</div>
                            </div>

                            <div class="vw-map-marker" style="top: 36.6%; left: 70.2%;" data-id="samupadda">
                                <div class="vw-marker blue">11</div>
                                <div class="vw-tooltip">Relief Patticasamupadda</div>
                            </div>

                            <div class="vw-map-marker" style="top: 61%; left: 71.2%;" data-id="tbm">
                                <div class="vw-marker red">12</div>
                                <div class="vw-tooltip">Taman Baca Masyarakat</div>
                            </div>

                            <div class="vw-map-marker" style="top: 61.8%; left: 81.6%;" data-id="meditasi">
                                <div class="vw-marker white">13</div>
                                <div class="vw-tooltip">Kuti Meditasi</div>
                            </div>

                            <div class="vw-map-marker" style="top: 82.8%; left: 74.6%;" data-id="bhikkhu">
                                <div class="vw-marker yellow">14</div>
                                <div class="vw-tooltip">Kuti Bhikkhu</div>
                            </div>
                        </div>
                    </div>
                    <!-- Facilities List -->
                    <div class="vw-facilities-list">
                        <h3>Fasilitas Vihara Watugong</h3>
                        <p>Tekan pada nomor di peta atau nama fasilitas untuk detail:</p>

                        <div class="vw-facility-items">
                            <ul class="vw-facility-items">
                                <li class="vw-facility-item" data-id="watu-gong">
                                    <span class="vw-facility-number blue">1</span>
                                    <span class="vw-facility-name">Watu Gong</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="gerbang-sanchi">
                                    <span class="vw-facility-number yellow">2</span>
                                    <span class="vw-facility-name">Gerbang Sanchi</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="plaza-borobudur">
                                    <span class="vw-facility-number red">3</span>
                                    <span class="vw-facility-name">Plaza Borobudur</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="asoka">
                                    <span class="vw-facility-number white">4</span>
                                    <span class="vw-facility-name">Tugu dan Prasasti Asoka</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="bodhi">
                                    <span class="vw-facility-number blue">5</span>
                                    <span class="vw-facility-name">Pohon Bodhi</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="avalokitesvara">
                                    <span class="vw-facility-number blue">6</span>
                                    <span class="vw-facility-name">Pagoda Avalokitesvara</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="b_parinibbana">
                                    <span class="vw-facility-number yellow">7</span>
                                    <span class="vw-facility-name">Buddha Parinibbana</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="b_sivali">
                                    <span class="vw-facility-number red">8</span>
                                    <span class="vw-facility-name">Bangunan Sivali</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="r_berdiri">
                                    <span class="vw-facility-number white">9</span>
                                    <span class="vw-facility-name">Rencana Buddha Berdiri</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="dhammasala">
                                    <span class="vw-facility-number red">10</span>
                                    <span class="vw-facility-name">Dhammasala</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="samupadda">
                                    <span class="vw-facility-number blue">11</span>
                                    <span class="vw-facility-name">Relief Patticasamupadda</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="tbm">
                                    <span class="vw-facility-number red">12</span>
                                    <span class="vw-facility-name">Taman Baca Masyarakat</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="meditasi">
                                    <span class="vw-facility-number white">13</span>
                                    <span class="vw-facility-name">Kuti Samadhi</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                                <li class="vw-facility-item" data-id="bhikkhu">
                                    <span class="vw-facility-number yellow">14</span>
                                    <span class="vw-facility-name">Kuti Bhikkhu</span>
                                    <span class="vw-facility-icon"><i class="fas fa-chevron-right"></i></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
    </div>

    <!-- Modal for Facility Details (Single Example) -->
    <div id="watu-gong-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('watu-gong')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number blue">1</span>
                    <h2>Watu Gong</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-watu-gong">
                                <div class="vw-carousel-slide">
                                    <img src="images/watugong.JPG" alt="Watu Gong - Tampak Depan" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('watu-gong')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('watu-gong')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-watu-gong">
                                    <span class="vw-indicator active" onclick="currentSlide('watu-gong', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('watu-gong', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('watu-gong', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>
                        Watugong, sebuah batu granit yang memiliki kemiripan dengan gong, alat musik tradisional Jawa. Batu ini awalnya ditemukan secara tidak sengaja oleh para pekerja proyek yang terlibat dalam pembangunan jalan di sepanjang rute antara Semarang dan Solo, di dekat vihara.
                    </p>

                    <p>
                        Bentuknya yang khas dan memiliki kesamaan budaya dengan cepat menarik perhatian, sehingga batu tersebut ditetapkan sebagai landmark setempat. Batu yang oleh penduduk setempat mulai disebut sebagai "Watu Gong" (bahasa Jawa yang berarti "batu berbentuk gong") ini awalnya diletakkan di dekat pohon beringin besar di depan kompleks wihara.
                    </p>

                    <blockquote>
                        "Seperti bunyi gong yang menggetarkan hati, Watu Gong mengingatkan kita akan pentingnya kebijaksanaan dalam menjalani kehidupan spiritual."
                    </blockquote>

                    <p>
                        Namun, untuk meningkatkan visibilitas dan kehadiran simbolisnya, batu ini kemudian dipindahkan ke posisi yang lebih dekat dengan gerbang masuk Wihara Watugong. Di lokasi ini, pohon beringin tersebut kini berfungsi sebagai simbol penyambutan bagi para pengunjung.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="gerbang-sanchi-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('gerbang-sanchi')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number yellow">2</span>
                    <h2>Gerbang Sanchi</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-gerbang-sanchi">
                                <div class="vw-carousel-slide">
                                    <img src="images/sanchi.JPG" alt="Gerbang Sanchi" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>
                        Gerbang Sanchi merupakan pintu masuk utama ke Vihara Buddhagaya Watugong. Gerbang ini terdiri dari tiga lorong, masing-masing dihiasi dengan relief bergaya Tiongkok yang melambangkan harmoni budaya.
                    </p>

                    <p>
                        Desain arsitektur gerbang ini terinspirasi dari Stupa Agung Sanchi yang terkenal di India, sebuah situs yang dianggap sebagai salah satu bangunan batu paling terhormat di negara tersebut dan merupakan Situs Warisan Dunia UNESCO.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="plaza-borobudur-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('plaza-borobudur')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number red">3</span>
                    <h2>Plaza Borobudur</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-plaza-borobudur">
                                <div class="vw-carousel-slide">
                                    <img src="images/plaza-borobudur.png" alt="Plaza Borobudur" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>
                        Plaza Borobudur merupakan area terbuka yang terletak di depan bangunan Dhammasala, yang didesain dengan bentuk mandala Candi Borobudur.
                    </p>

                    <p>
                        Tata letak simbolis ini mencerminkan perjalanan spiritual yang digambarkan dalam arsitektur Borobudur dari duniawi ke dunia pencerahan yang mewakili jalan bertahap menuju kebangkitan spiritual dalam agama Buddha. Struktur mandala di alun-alun ini tidak hanya bermakna secara estetika tetapi juga bermakna secara spiritual.
                    </p>
                    <p>
                        Struktur ini mencerminkan tiga tingkatan kosmologi Buddha: Kamadhatu (dunia keinginan), Rupadhatu (dunia bentuk), dan Arupadhatu (dunia tanpa bentuk). Pengunjung yang berjalan melalui ruang ini diajak untuk merenungkan perjalanan ini, baik secara fisik maupun metaforis. Plaza Borobudur berfungsi sebagai ruang multifungsi untuk kegiatan keagamaan dan budaya di luar ruangan, seperti perayaan Waisak dan Dhamma walk.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="asoka-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('asoka')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number white">4</span>
                    <h2>Tugu dan Prasasti Asoka</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-asoka">
                                <div class="vw-carousel-slide">
                                    <img src="images/asoka.JPG" alt="Asoka" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Monumen Asoka adalah replika prasasti dekrit Raja Asoka yang tersebar di 34 wilayah di pelosok India, Nepal, Pakistan, dan Afganistan untuk menginformasikan kepada masyarakat tentang program reformasi, dan mendorong masyarakat untuk lebih murah hati, bijaksana, dan bermoral. Salah satu yang paling terkenal adalah tentang toleransi beragama.
                    </p>

                    <p>
                        Relief batu Asoka yang terletak di dekat Gerbang Sanchi merupakan sebuah maklumat kepada masyarakat mengenai toleransi beragama. Bunyinya adalah 'Janganlah kita menghormati agama kita sendiri dengan mencela agama lain. Sebaliknya, agama orang lain juga harus dihormati. Dengan melakukan hal tersebut, kita membantu agama kita sendiri untuk berkembang dan juga memberi manfaat bagi agama-agama lain. Dengan melakukan hal yang sebaliknya, kita akan merugikan agama kita sendiri dan juga merugikan agama orang lain.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="bodhi-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('bodhi')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number blue">5</span>
                    <h2>Pohon Bodhi</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-bodhi">
                                <div class="vw-carousel-slide">
                                    <img src="images/bodhi.jpg" alt="Asoka" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Pohon Bodhi adalah simbol yang sangat sakral dalam agama Buddha, yang dipuja sebagai perwujudan hidup dari pencerahan. Pohon ini merupakan objek puja (persembahan kebaktian) dan penghormatan mendalam bagi umat Buddha di seluruh dunia.
                    </p>

                    <p>Pohon yang berdiri di dalam Vihara Buddhagaya Watugong bukanlah sembarang pohon, melainkan pohon Bodhi hasil cangkokan yang induknya berasal dari Anuradhapura, Sri Lanka, sebuah kota yang dikenal sebagai tempat penyimpanan salah satu pohon Bodhi tertua dan tersuci di dunia. Pohon di Anuradhapura ini merupakan keturunan langsung dari Pohon Bodhi asli di Bodhgaya, India, tempat Pangeran Siddharta Gautama bermeditasi dan mencapai pencerahan sempurna, menjadi Buddha lebih dari 2.500 tahun yang lalu.
                    </p>

                    <p>Melalui silsilah ini, Pohon Bodhi di Watugong menjadi penghubung yang hidup dengan momen sakral dalam sejarah manusia - sebuah penghubung yang nyata dengan tempat di mana perjalanan menuju pembebasan digenapi.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="avalokitesvara-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('avalokitesvara')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number blue">6</span>
                    <h2>Pagoda Avalokitesvara</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-avalokitesvara">
                                <div class="vw-carousel-slide">
                                    <img src="images/avalokitesvara.jpg" alt="Avalokitesvara" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Pagoda Avalokitesvara adalah bangunan bergaya stupa yang megah dengan ciri khas arsitektur Tiongkok yang kuat, berdiri sebagai salah satu landmark paling ikonik di Vihara Buddhagaya Watugong. Menjulang setinggi 45 meter, pagoda ini tidak hanya menjadi pagoda tertinggi di Indonesia, namun juga merupakan simbol welas asih, kedamaian, dan peningkatan spiritual.
                    </p>

                    <p>Di jantung pagoda terdapat Ruang Metta Karuna, yang dinamai sesuai dengan dua nilai utama ajaran Buddha: cinta kasih (metta) dan welas asih (karuṇā). Ruang suci ini menyimpan patung Bodhisattva Avalokitesvara, yang juga dihormati sebagai Guan Yin atau Kwan Im Po Sat, Dewi Welas Asih. Patung ini merupakan titik fokus spiritual yang kuat, yang dipercaya oleh para pemujanya untuk memberikan berkah, bimbingan, dan perlindungan kepada semua makhluk yang memujanya dengan niat tulus.
                    </p>

                    <p>Secara arsitektur, pagoda ini memadukan pengaruh Cina-Mahayana dengan estetika lokal. Struktur tujuh tingkatnya melambangkan tujuh tingkat pencapaian spiritual, yang secara bertahap naik menuju pembebasan. Setiap tingkat dihiasi dengan ukiran rumit dan patung-patung yang mewakili berbagai Bodhisattva, wali, dan motif teratai, yang semuanya memiliki makna simbolis yang dalam.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="b_parinibbana-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('b_parinibbana')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number yellow">7</span>
                    <h2>Buddha Parinibbana</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-b_parinibbana">
                                <div class="vw-carousel-slide">
                                    <img src="images/b_parinibbana.JPG" alt="Buddha Parinibbana" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Buddha Parinibbana adalah salah satu benda suci yang masih tersisa dari masa awal pembangunan Vihara Buddhagaya Watugong.
                    </p>

                    <p>Patung Buddharupam ini menggambarkan Buddha pada saat terakhirnya di dunia, berbaring miring ke kanan di bawah naungan dua pohon Sala, melambangkan peristiwa Parinibbana, atau Mangkatnya Sang Buddha, yaitu saat Buddha meninggalkan siklus kelahiran kembali dan memasuki pembebasan sempurna (Nibbana).
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="b_sivali-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('b_sivali')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number red">8</span>
                    <h2>Bangunan Sivalli</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-b_sivali">
                                <div class="vw-carousel-slide">
                                    <img src="images/b_sivali.jpg" alt="Bangunan Sivali" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Bangunan Sivali merupakan salah satu tempat untuk
                        beribadah bagi Umat Buddha. Nama Sivali sendiri diambil
                        dari nama seorang Bhikkhu yang terkenal sebagai
                        Seorang Bhikku yang selalu menerima pemberian
                        sumbangan makan berjumlah besar. Ia terkenal sebagai
                        Bhikkhu Murah Rezeki.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="r_berdiri-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('r_berdiri')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number white">9</span>
                    <h2>Rencana Buddha Berdiri</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-r_berdiri">
                                <div class="vw-carousel-slide">
                                    <img src="images/b_sivali.jpg" alt="Rencana Buddha Berdiri" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Bangunan Sivali merupakan salah satu tempat untuk
                        beribadah bagi Umat Buddha. Nama Sivali sendiri diambil
                        dari nama seorang Bhikkhu yang terkenal sebagai
                        Seorang Bhikku yang selalu menerima pemberian
                        sumbangan makan berjumlah besar. Ia terkenal sebagai
                        Bhikkhu Murah Rezeki.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="dhammasala-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('dhammasala')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number red">10</span>
                    <h2>Dhammasala</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-dhammasala">
                                <div class="vw-carousel-slide">
                                    <img src="images/dhammasala.jpg" alt="Rencana Buddha Berdiri" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Dhammasala, bangunan utama biara, adalah titik fokus dari praktik ini. Bangunan ini berfungsi sebagai pusat kegiatan keagamaan dan spiritual yang dilakukan di vihara. Kegiatan-kegiatan ini termasuk puja, upacara pengabdian, sesi meditasi, dan penahbisan hikku dan samanera. Selain itu, vihara ini juga berfungsi sebagai tempat untuk diskusi Dhamma Buddha. Vihara ini merupakan tempat suci dimana para bhikkhu dan umat awam berkumpul untuk meningkatkan pemahaman dan penerapan ajaran Buddha.
                    </p>
                    <p>Di dalam Dhammasala, terdapat Buddharupam (patung Buddha) dalam posisi Dhammacakkha Mudra, yang melambangkan momen ketika Buddha pertama kali membabarkan Dhamma. Postur ikonik ini, yang dikenal sebagai "Memutar Roda Dhamma", menandakan dimulainya perjalanan pengajaran Buddha dan merupakan simbol pencerahan dan penyebaran kebijaksanaan yang kuat.</p>
                    <p>Pada acara-acara penting seperti Waisak atau Kathina, aula ini memiliki peran penting, menjadi titik fokus kegiatan yang ditandai dengan nyanyian, persembahan, dan kontemplasi bersama. Bagi praktisi pemula dan mereka yang tidak memiliki pelatihan formal, aula ini berfungsi sebagai tempat untuk menerima bimbingan, menghadiri khotbah, dan mengajukan pertanyaan untuk menyempurnakan latihan mereka.</p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="samupadda-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('samupadda')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number blue">11</span>
                    <h2>Dhammasala</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-samupadda">
                                <div class="vw-carousel-slide">
                                    <img src="images/samupadda.jpg" alt="Rencana Buddha Berdiri" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Jika berkunjung ke Dhammasala, pengunjung akan menemukan ornamen Tirtana yang melambangkan Buddha, Dhamma, dan Sangha. Simbolisasi ini berfungsi untuk menginformasikan kepada pengunjung tentang Buddha, Dhamma, dan para siswa yang memperkenalkan Tirtana. Tiratana adalah gambar yang mewakili konsep pembelajaran untuk melihat realitas kehidupan yang melekat dengan memeriksa elemen-elemen mendasar yang memunculkan proses kehidupan, yang ditandai dengan pola berulang dan transisi. Relief di lantai pintu masuk Dhammasala, yang tersusun dari batu hijau dan berbentuk melingkar dengan diameter 120 sentimeter, sangat menarik. Relief ini menampilkan tiga serangkai hewan yang saling menggigit ekor satu sama lain, yang merupakan simbol dari sumber mendasar dari kekotoran batin manusia.
                    </p>
                    <p>Ciri khas lain dari Dhammasala adalah representasi dari Paticcasamuppāda, atau Asal Mula yang Saling Bergantung, melalui Dua Belas Nidāna (12 mata rantai keberadaan). Relief yang rumit ini tidak hanya berfungsi sebagai elemen dekoratif tetapi juga sebagai instrumen pedagogis, yang berfungsi untuk mengingatkan pengunjung dan praktisi tentang filosofi utama dari ajaran Buddha, bahwa semua fenomena muncul dengan ketergantungan pada fenomena lainnya.</p>
                    <p>12 Nidāna merupakan sebuah rantai berurutan yang menjelaskan proses samsara, atau dikenal sebagai siklus kelahiran, kematian, dan kelahiran kembali. Setiap mata rantai dalam rantai ini mewakili suatu kondisi yang memunculkan kondisi berikutnya, dengan demikian menggambarkan bagaimana penderitaan terus berlanjut dalam siklus yang terus menerus kecuali seseorang mencapai pandangan terang dan pembebasan sejati.
                        Dua belas mata rantai tersebut meliputi:
                    <blockquote>Avijjā (Ketidaktahuan), 2. Sankhāra (Bentukan-bentukan mental), 3. Viññāṇa (Kesadaran), 4. Nāma-rūpa (Nama dan bentuk), 5. Salāyatana (Enam landasan indera), 6. Phassa (Kontak), 7. Kundalini (Ketidaktahuan) Vedanā (Perasaan), 8. Tanhā (Nafsu keinginan), 9. Upādāna (Kemelekatan), 10. Bhava (Menjadi), 11. Jāti (Kelahiran), 12. Jarāmaraṇa (Penuaan dan kematian)</blockquote>
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="tbm-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('tbm')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number red">12</span>
                    <h2>Taman Baca Masyarakat</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-tbm">
                                <div class="vw-carousel-slide">
                                    <img src="images/tbm.png" alt="Rencana Buddha Berdiri" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p>Perpustakaan ini merupakan kantor sekretariat biara dan menyimpan koleksi buku-buku Buddhis dan buku bacaan umum. Perpustakaan ini terbuka bagi pengunjung untuk melihat atau membaca buku-buku yang berkaitan dengan sejarah Buddha.
                    </p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="meditasi-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('meditasi')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number white">13</span>
                    <h2>Kuti Samadhi</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-meditasi">
                                <div class="vw-carousel-slide">
                                    <img src="images/meditasi.png" alt="Rencana Buddha Berdiri" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p> Kuti adalah bangunan suci dan sederhana yang terbuat dari kayu ulin, yang dikenal karena kekuatan dan daya tahannya, yang berfungsi terutama sebagai tempat beristirahat dan tidur bagi para bhikkhu dan samanera (bhikkhu pemula). Penggunaan bahan alami yang kokoh seperti kayu ulin tidak hanya praktis untuk penggunaan jangka panjang, tetapi juga mencerminkan nilai-nilai Buddhis tentang kesederhanaan, keberlanjutan, dan keselarasan dengan alam.
                    </p>
                    <p>Selain fungsi utamanya sebagai tempat tinggal, kuti juga digunakan oleh umat awam dan peserta retret sebagai ruang untuk meditasi dan kontemplasi pribadi. Desain kuti yang tenang dan minimalis membantu menciptakan suasana yang tenang dan penuh perhatian, bebas dari gangguan. Dengan lingkungan yang tenang, memungkinkan para praktisi untuk fokus ke dalam, memperdalam konsentrasi, dan merenungkan Dhamma.</p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    <div id="bhikkhu-modal" class="vw-modal">
        <div class="vw-modal-content">
            <span class="vw-close" onclick="closeModal('bhikkhu')">&times;</span>
            <div class="vw-modal-header">
                <div class="vw-modal-title">
                    <span class="vw-modal-number yellow">14</span>
                    <h2>Kuti Bhikkhu</h2>
                </div>
            </div>
            <div class="vw-modal-body">
                <!-- Enhanced Image Gallery -->
                <div class="vw-modal-image-gallery">
                    <div class="vw-image-carousel">
                        <div class="vw-carousel-container">
                            <div class="vw-carousel-track" id="carousel-track-bhikkhu">
                                <div class="vw-carousel-slide">
                                    <img src="images/bhikku.png" alt="Rencana Buddha Berdiri" class="vw-modal-img">
                                </div>
                                <!-- <div class="vw-carousel-slide">
                                            <img src="images/watugong-detail.JPG" alt="Watu Gong - Detail Tekstur" class="vw-modal-img">
                                        </div>
                                        <div class="vw-carousel-slide">
                                            <img src="images/watugong-area.JPG" alt="Watu Gong - Area Sekitar" class="vw-modal-img">
                                        </div> -->
                            </div>
                            <!-- Navigation Arrows -->
                            <!-- <button class="vw-carousel-nav vw-carousel-prev" onclick="prevSlide('gerbang-sanchi')">❮</button>
                                    <button class="vw-carousel-nav vw-carousel-next" onclick="nextSlide('gerbang-sanchi')">❯</button> -->
                        </div>
                        <!-- Image Indicators -->
                        <!-- <div class="vw-carousel-indicators" id="indicators-gerbang-sanchi">
                                    <span class="vw-indicator active" onclick="currentSlide('gerbang-sanchi', 1)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 2)"></span>
                                    <span class="vw-indicator" onclick="currentSlide('gerbang-sanchi', 3)"></span>
                                </div> -->
                    </div>
                </div>

                <!-- Enhanced Description -->
                <div class="vw-modal-description">
                    <p> Kuti adalah bangunan suci dan sederhana yang terbuat dari kayu ulin, yang dikenal karena kekuatan dan daya tahannya, yang berfungsi terutama sebagai tempat beristirahat dan tidur bagi para bhikkhu dan samanera (bhikkhu pemula). Penggunaan bahan alami yang kokoh seperti kayu ulin tidak hanya praktis untuk penggunaan jangka panjang, tetapi juga mencerminkan nilai-nilai Buddhis tentang kesederhanaan, keberlanjutan, dan keselarasan dengan alam.
                    </p>
                    <p>Selain fungsi utamanya sebagai tempat tinggal, kuti juga digunakan oleh umat awam dan peserta retret sebagai ruang untuk meditasi dan kontemplasi pribadi. Desain kuti yang tenang dan minimalis membantu menciptakan suasana yang tenang dan penuh perhatian, bebas dari gangguan. Dengan lingkungan yang tenang, memungkinkan para praktisi untuk fokus ke dalam, memperdalam konsentrasi, dan merenungkan Dhamma.</p>
                </div>
            </div>
            <div class="vw-modal-footer">
                <button class="vw-nav-btn prev" onclick="previousFacility()">
                    <i>←</i> Sebelumnya
                </button>
                <button class="vw-nav-btn next" onclick="nextFacility()">
                    Selanjutnya <i>→</i>
                </button>
            </div>
        </div>
    </div>
    </section>
    <!-- Kegiatan Section -->
    <section id="kegiatan" class="s-events target-section">
        <div class="section-header">
            <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <div class="wg-section-number">03</div>
                <h2 class="wg-pretitle" style="margin-top: -0.8px;">Kegiatan</h2>
            </div>
            <div class="container">
                <div class="header-content">
                    <h2 class="title">Program Keagamaan dan Sosial di Vihara Watugong</h2>
                    <p class="description">
                        Tidak hanya untuk berwisata, vihara juga masih aktif dalam aktivitas keagamaan yang
                        dilakukan secara rutin
                        seperti puja bakti rutin yang dilakukan pada setiap hari Minggu sore dan juga puja bakti
                        perayaan 4 hari besar
                        agama Buddha.
                    </p>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="events-grid">
                <div class="event-card">
                    <div class="event-header">
                        <div class="event-icon">
                            <span class="fa fa-om"></span>
                        </div>
                        <h3>Meditasi Lintas Agama</h3>
                    </div>
                    <div class="event-details">
                        <p>
                            Meditasi lintas agama merupakan salah satu kegiatan yang rutin dilakukan di Vihara
                            Watugong.
                            Kegiatan ini biasanya dilakukan pada hari Jumat sore dan diikuti oleh para peserta
                            dari berbagai agama.
                        </p>
                    </div>
                    <div class="event-meta">
                        <span><span class="fa fa-calendar"></span> Setiap Jumat</span>
                        <span><span class="fa fa-clock-o"></span> 08.00 WIB</span>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-header">
                        <div class="event-icon">
                            <span class="fa fa-tree"></span>
                        </div>
                        <h3>Penggantian Kain Pohon Bodhi</h3>
                    </div>
                    <div class="event-details">
                        <p>
                            Kegiatan penggantian kain pohon Bodhi merupakan sebuah rangkaian acara dalam
                            menyambut Waisak.
                            Kegiatan diawali dengan puja bakti didalam Dhammasala yang dipimpin oleh Bhikkhu.
                        </p>
                    </div>
                    <div class="event-meta">
                        <span><span class="fa fa-calendar"></span> Penyambutan Waisak</span>
                        <span><span class="fa fa-clock-o"></span> 09.30 WIB</span>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-header">
                        <div class="event-icon">
                            <span class="fa fa-sun-o"></span>
                        </div>
                        <h3>Hari Tri Suci Waisak</h3>
                    </div>
                    <div class="event-details">
                        <p>
                            Hari Tri Suci Waisak merupakan puja bakti untuk memperingati kelahiran, penerangan
                            sempurna dan
                            wafatnya Sang Buddha. Peringatan ini diperingati pada bulan Mei setiap tahunnya.
                        </p>
                    </div>
                    <div class="event-meta">
                        <span><span class="fa fa-calendar"></span> Setiap Bulan Mei</span>
                        <span><span class="fa fa-clock-o"></span> 16.00 WIB</span>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-header">
                        <div class="event-icon">
                            <span class="fa fa-moon-o"></span>
                        </div>
                        <h3>Hari Asadha</h3>
                    </div>
                    <div class="event-details">
                        <p>
                            Hari Asadha merupakan puja bakti untuk memperingati pertama kalinya Sang Buddha
                            memutar roda dhamma
                            atau mengajarkan dhamma pada 5 murid pertamanya.
                        </p>
                    </div>
                    <div class="event-meta">
                        <span><span class="fa fa-calendar"></span> Setiap Bulan Juli</span>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-header">
                        <div class="event-icon">
                            <span class="fa fa-gift"></span>
                        </div>
                        <h3>Hari Raya Kathina</h3>
                    </div>
                    <div class="event-details">
                        <p>
                            Hari Raya Kathina merupakan hari raya umat Buddha untuk mempersembahkan dana berupa
                            4 kebutuhan
                            pokok kepada Sangha Bhikkhu.
                        </p>
                    </div>
                    <div class="event-meta">
                        <span><span class="fa fa-calendar"></span> Kegiatan Rutin</span>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-header">
                        <div class="event-icon">
                            <span class="fa fa-dharmachakra"></span>
                        </div>
                        <h3>Hari Magha Puja</h3>
                    </div>
                    <div class="event-details">
                        <p>
                            Hari Magha Puja merupakan puja bakti untuk memperingati 4 peristiwa agung dalam
                            pemutaran roda
                            dhamma.
                        </p>
                    </div>
                    <div class="event-meta">
                        <span><span class="fa fa-calendar"></span> Kegiatan Rutin</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Berita Section -->
    <section id="berita" class="s-news target-section">
        <div class="row section-header" data-num="04">
            <h3 class="column lg-12 section-header__pretitle text-pretitle">Berita Terkini</h3>
            <div class="column lg-6 stack-on-1100 section-header__primary">
                <h2 class="title text-display-1">
                    Kabar Terbaru dari Vihara Watugong
                </h2>
            </div>
            <div class="column lg-6 stack-on-1100 section-header__secondary">
                <p class="desc">
                    Temukan informasi terbaru seputar kegiatan, acara, dan perkembangan di Vihara Watugong.
                    Kami selalu berusaha memberikan informasi yang bermanfaat bagi umat Buddha dan masyarakat umum.
                </p>
            </div>
        </div>

        <?php if (!empty($berita_list)): ?>
            <div class="row news-list block-lg-one-third block-tab-whole">
                <?php foreach ($berita_list as $berita): ?>
                    <div class="column news-list__item">
                        <div class="news-list__img">
                            <img src="images/berita/<?= htmlspecialchars($berita['gambar_utama']) ?>"
                                alt="<?= htmlspecialchars($berita['judul']) ?>"
                                loading="lazy">
                        </div>
                        <div class="news-list__content">
                            <div class="news-list__meta">
                                <span><?= format_tanggal($berita['tanggal_publish']) ?></span>
                                <span><?= htmlspecialchars($berita['nama_kategori']) ?></span>
                            </div>
                            <h4><?= htmlspecialchars($berita['judul']) ?></h4>
                            <p><?= htmlspecialchars($berita['excerpt']) ?></p>
                            <a href="detail_berita.php?slug=<?= urlencode($berita['slug']) ?>"
                                class="news-list__more">Baca Selengkapnya</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($show_view_all): ?>
                <!-- View All Button (only on first page) -->
                <div class="view-all-container">
                    <a href="semua_berita.php" class="btn-view-all" style="color: white;">
                        Lihat Semua Berita
                    </a>
                </div>
            <?php elseif ($total_pages > 1): ?>
                <!-- Pagination (for other pages) -->
                <div class="pagination-container">
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>#berita" class="prev">« Sebelumnya</a>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);

                        if ($start > 1): ?>
                            <a href="?page=1#berita">1</a>
                            <?php if ($start > 2): ?>
                                <span class="dots">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="current"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?>#berita"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($end < $total_pages): ?>
                            <?php if ($end < $total_pages - 1): ?>
                                <span class="dots">...</span>
                            <?php endif; ?>
                            <a href="?page=<?= $total_pages ?>#berita"><?= $total_pages ?></a>
                        <?php endif; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>#berita" class="next">Selanjutnya »</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-data">
                <p>Belum ada berita yang tersedia.</p>
            </div>
        <?php endif; ?>
    </section>
    <section id="vihara-kontak" class="s-vihara-contact target-section">
        <div class="vihara-section-header" data-num="05">
            <h3 class="vihara-section-header__pretitle text-pretitle">Kontak</h3>
            <div class="vihara-section-header__primary">
                <h2 class="vihara-title text-display-1">
                    Hubungi Kami
                </h2>
            </div>
            <div class="vihara-section-header__secondary">
                <p class="vihara-desc">
                    Untuk informasi lebih lanjut tentang Vihara Watugong, kegiatan yang kami adakan,
                    atau jika Anda ingin berkunjung, silakan hubungi kami melalui kontak di bawah ini.
                </p>
            </div>
        </div>

        <div class="vihara-contact-main">
            <div class="vihara-contact-map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.654030856774!2d110.41660931477373!3d-7.063433694927306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708b37a738e03d%3A0x5027a76e356a830!2sVihara%20Buddhagaya%20Watugong!5e0!3m2!1sen!2sid!4v1651241518565!5m2!1sen!2sid"
                    width="20%" height="20%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="vihara-contact-form">
                <form name="contactForm" method="post" action="#">
                    <div class="vihara-form-field">
                        <div class="vihara-contact-block">
                            <h6>Alamat</h6>
                            <p>
                                Pudakpayung, Kec. Banyumanik,<br>
                                Kota Semarang, Jawa Tengah 50265
                            </p>
                        </div>
                    </div>
                    <div class="vihara-form-field">
                        <div class="vihara-contact-block">
                            <h6>Email & Telepon</h6>
                            <p>
                                info@viharawatugong.com<br>
                                +62 24 7471 5178
                            </p>
                        </div>
                    </div>
                    <div class="vihara-form-field">
                        <div class="vihara-contact-block">
                            <h6>Jam Kunjungan</h6>
                            <p>
                                Senin - Minggu: 06.00 - 20.00 WIB<br>
                                Hari Libur: 06.00 - 21.00 WIB
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- <div class="vihara-contact-infos">
            <div class="vihara-column">
                <div class="vihara-contact-block">
                    <h6>Alamat</h6>
                    <p>
                        Pudakpayung, Kec. Banyumanik,<br>
                        Kota Semarang, Jawa Tengah 50265
                    </p>
                </div>
            </div>
            <div class="vihara-column">
                <div class="vihara-contact-block">
                    <h6>Email & Telepon</h6>
                    <p>
                        info@viharawatugong.com<br>
                        +62 24 7471 5178
                    </p>
                </div>
            </div>
            <div class="vihara-column">
                <div class="vihara-contact-block">
                    <h6>Jam Kunjungan</h6>
                    <p>
                        Senin - Minggu: 06.00 - 20.00 WIB<br>
                        Hari Libur: 06.00 - 21.00 WIB
                    </p>
                </div>
            </div>
        </div> -->
    </section>

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
    </div>

    <!-- Javascript -->
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>
    <script src="js/lang.js"></script>

    <script>
        // Detail modal functionality
        function showDetail(id) {
            const modal = document.getElementById('detail-modal');
            const content = document.getElementById('detail-content');
            const details = {
                'watu-gong': {
                    title: 'Watu Gong',
                    description: 'Batu berbentuk gong yang menjadi ciri khas vihara. Batu ini telah ada sejak lama di area sekitar vihara dan dianggap memiliki nilai historis penting.',
                    image: 'images/watu-gong-detail.jpg'
                },
                'gerbang-sanchi': {
                    title: 'Gerbang Sanchi',
                    description: 'Replika dari gapura yang berada di depan Stupa Sanchi, India. Melambangkan pintu masuk menuju ajaran Buddha.',
                    image: 'images/gerbang-sanchi-detail.jpg'
                },
                'dhammasala': {
                    title: 'Dhammasala',
                    description: 'Bangunan utama tempat umat Buddha melakukan puja bakti, meditasi, dan mendengarkan khotbah Dhamma.',
                    image: 'images/dhammasala-detail.jpg'
                },
                // Tambahkan detail untuk fasilitas lain
            };

            if (details[id]) {
                const detail = details[id];
                content.innerHTML = `
                    <h2>${detail.title}</h2>
                    <img src="${detail.image}" alt="${detail.title}">
                    <p>${detail.description}</p>
                `;
            } else {
                content.innerHTML = `<p>Detail untuk ${id} belum tersedia.</p>`;
            }

            modal.style.display = 'block';

            // Close modal when clicking the X
            document.querySelector('.close').onclick = function() {
                modal.style.display = 'none';
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }
        // Function to open facility modal
        function openModal(facilityId) {
            const modal = document.getElementById(`${facilityId}-modal`);
            if (modal) {
                modal.style.display = "block";
                document.body.style.overflow = "hidden"; // Prevent scrolling when modal is open
            }
        }

        // Function to close facility modal
        function closeModal(facilityId) {
            const modal = document.getElementById(`${facilityId}-modal`);
            if (modal) {
                modal.style.display = "none";
                document.body.style.overflow = "auto"; // Re-enable scrolling
            }
        }

        // Function to navigate between modals
        function navigateModal(direction, currentId) {
            const facilityItems = document.querySelectorAll('.vw-facility-item');
            let currentIndex = -1;

            // Find current facility index
            for (let i = 0; i < facilityItems.length; i++) {
                if (facilityItems[i].dataset.id === currentId) {
                    currentIndex = i;
                    break;
                }
            }

            if (currentIndex !== -1) {
                // Close current modal
                closeModal(currentId);

                // Calculate next/prev index with wrapping
                let nextIndex;
                if (direction === 'next') {
                    nextIndex = (currentIndex + 1) % facilityItems.length;
                } else {
                    nextIndex = (currentIndex - 1 + facilityItems.length) % facilityItems.length;
                }

                // Open next modal
                const nextId = facilityItems[nextIndex].dataset.id;
                openModal(nextId);
            }
        }

        // Add event listeners when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Map markers click event
            const mapMarkers = document.querySelectorAll('.vw-map-marker');
            mapMarkers.forEach(marker => {
                marker.addEventListener('click', function() {
                    const facilityId = this.dataset.id;
                    openModal(facilityId);
                });
            });

            // Facility items click event
            const facilityItems = document.querySelectorAll('.vw-facility-item');
            facilityItems.forEach(item => {
                item.addEventListener('click', function() {
                    const facilityId = this.dataset.id;
                    openModal(facilityId);
                });
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modals = document.querySelectorAll('.vw-modal');
                modals.forEach(modal => {
                    if (event.target === modal) {
                        const facilityId = modal.id.replace('-modal', '');
                        closeModal(facilityId);
                    }
                });
            });

            // Close modal with Escape key
            window.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const openModal = document.querySelector('.vw-modal[style="display: block;"]');
                    if (openModal) {
                        const facilityId = openModal.id.replace('-modal', '');
                        closeModal(facilityId);
                    }
                }
            });
        });
    </script>
</body>

</html>