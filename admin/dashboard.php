<?php
require_once 'auth.php';
require_once '../config/database.php';
requireLogin();

$admin = getAdminInfo();

// Initialize variables
$total_berita = 0;
$berita_published = 0;
$berita_draft = 0;
$total_kategori = 0;
$recent_news = [];
$error = '';

// Get statistics
try {
    $total_berita = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
    $berita_published = $pdo->query("SELECT COUNT(*) FROM berita WHERE status = 'published'")->fetchColumn();
    $berita_draft = $pdo->query("SELECT COUNT(*) FROM berita WHERE status = 'draft'")->fetchColumn();
    $total_kategori = $pdo->query("SELECT COUNT(*) FROM kategori_berita")->fetchColumn();

    // Recent news
    $recent_news = $pdo->query("SELECT id, judul, status, tanggal_publish, created_at FROM berita ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
    error_log("Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vihara Watugong</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --purple: #8b5cf6;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-white);
            border-right: 1px solid var(--border);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-header h2 {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .sidebar-header p {
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin: 0 0.75rem;
            border-radius: 8px;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #f1f5f9;
            color: var(--primary);
        }

        .nav-item i {
            width: 1.25rem;
            text-align: center;
            font-size: 1rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--bg-white);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: var(--bg-light);
            border-radius: 50px;
            border: 1px solid var(--border);
        }

        .user-avatar {
            width: 2rem;
            height: 2rem;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .logout-btn {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
        }

        .content {
            flex: 1;
            padding: 2rem;
        }

        /* Error Alert */
        .error-alert {
            background: #fef2f2;
            color: var(--danger);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border-left: 4px solid var(--danger);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-white);
            padding: 2rem 1.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            position: relative;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .stat-title {
            color: var(--text-light);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .stat-card.total .stat-icon {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .stat-card.published .stat-icon {
            background: linear-gradient(135deg, var(--success), #059669);
        }

        .stat-card.draft .stat-icon {
            background: linear-gradient(135deg, var(--warning), #d97706);
        }

        .stat-card.categories .stat-icon {
            background: linear-gradient(135deg, var(--purple), #7c3aed);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1;
        }

        /* Recent Section */
        .recent-section {
            background: var(--bg-white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .section-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        .section-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .recent-list {
            list-style: none;
            padding: 1rem 0;
        }

        .recent-item {
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .recent-item:hover {
            background: #f8fafc;
        }

        .recent-item h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .recent-item .meta {
            font-size: 0.8rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-published {
            background: #dcfce7;
            color: #166534;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .no-data {
            text-align: center;
            color: var(--text-light);
            padding: 4rem 2rem;
            font-style: italic;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-dark);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .mobile-menu-btn:hover {
            background: var(--bg-light);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 100;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .topbar {
                padding: 1rem;
            }

            .topbar h1 {
                font-size: 1.5rem;
            }

            .content {
                padding: 1rem;
            }

            .user-menu {
                padding: 0.375rem 0.75rem;
            }

            .user-name {
                display: none;
            }

            .stat-card {
                padding: 1.5rem 1rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .recent-item {
                padding: 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .section-header {
                padding: 1.5rem 1rem 1rem;
            }

            .recent-list {
                padding: 0.5rem 0;
            }
        }

        @media (max-width: 480px) {
            .topbar {
                padding: 0.75rem;
            }

            .content {
                padding: 0.75rem;
            }

            .stats-grid {
                gap: 0.75rem;
            }

            .stat-card {
                padding: 1.25rem 0.875rem;
            }

            .stat-number {
                font-size: 1.75rem;
            }

            .stat-icon {
                width: 2.5rem;
                height: 2.5rem;
                font-size: 1rem;
            }

            .recent-item h4 {
                font-size: 0.875rem;
            }

            .recent-item .meta {
                font-size: 0.75rem;
            }

            .status-badge {
                padding: 0.25rem 0.625rem;
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>
    <div class="layout">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" onclick="closeMobileMenu()"></div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Vihara Watugong</p>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>
                <a href="berita.php" class="nav-item">
                    <i class="fas fa-newspaper"></i>
                    Kelola Berita
                </a>
                <a href="kategori.php" class="nav-item">
                    <i class="fas fa-tags"></i>
                    Kelola Kategori
                </a>
                <a href="../index.php" class="nav-item" target="_blank">
                    <i class="fas fa-globe"></i>
                    Lihat Website
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Dashboard</h1>
                </div>
                <div class="topbar-actions">
                    <div class="user-menu">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($admin['nama'] ?? 'A', 0, 1)); ?>
                        </div>
                        <span class="user-name"><?php echo htmlspecialchars($admin['nama'] ?? 'Admin'); ?></span>
                    </div>
                    <form method="GET" style="margin: 0;">
                        <button type="submit" name="logout" value="1" class="logout-btn" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="content">
                <?php if ($error): ?>
                    <div class="error-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card total">
                        <div class="stat-header">
                            <div class="stat-title">Total Berita</div>
                            <div class="stat-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?php echo $total_berita; ?></div>
                    </div>

                    <div class="stat-card published">
                        <div class="stat-header">
                            <div class="stat-title">Published</div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?php echo $berita_published; ?></div>
                    </div>

                    <div class="stat-card draft">
                        <div class="stat-header">
                            <div class="stat-title">Draft</div>
                            <div class="stat-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?php echo $berita_draft; ?></div>
                    </div>

                    <div class="stat-card categories">
                        <div class="stat-header">
                            <div class="stat-title">Kategori</div>
                            <div class="stat-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?php echo $total_kategori; ?></div>
                    </div>
                </div>

                <!-- Recent News -->
                <div class="recent-section">
                    <div class="section-header">
                        <h2>
                            <i class="fas fa-clock"></i>
                            Berita Terbaru
                        </h2>
                    </div>

                    <?php if (empty($recent_news)): ?>
                        <div class="no-data">Belum ada berita</div>
                    <?php else: ?>
                        <ul class="recent-list">
                            <?php foreach ($recent_news as $news): ?>
                                <li class="recent-item">
                                    <div>
                                        <h4><?php echo htmlspecialchars($news['judul']); ?></h4>
                                        <div class="meta">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php
                                            $date = $news['tanggal_publish'] ? $news['tanggal_publish'] : date('Y-m-d', strtotime($news['created_at']));
                                            echo date('d/m/Y', strtotime($date));
                                            ?>
                                        </div>
                                    </div>
                                    <span class="status-badge status-<?php echo $news['status']; ?>">
                                        <?php echo ucfirst($news['status']); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        }

        // Close mobile menu when clicking nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeMobileMenu();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });
    </script>
</body>

</html>