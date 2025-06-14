<?php
require_once 'auth.php';
require_once '../config/database.php';
requireLogin();

$admin = getAdminInfo();

// Initialize variables
$error = '';
$success = '';
$kategori_list = [];
$edit_kategori = null;

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Check if category is being used by any news
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM berita WHERE kategori_id = ?");
        $stmt->execute([$id]);
        $berita_count = $stmt->fetchColumn();

        if ($berita_count > 0) {
            $error = "Kategori tidak dapat dihapus karena masih digunakan oleh {$berita_count} berita";
        } else {
            // Delete category
            $stmt = $pdo->prepare("DELETE FROM kategori_berita WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Kategori berhasil dihapus";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle form submission
if ($_POST) {
    $nama_kategori = trim($_POST['nama_kategori']);

    if (empty($nama_kategori)) {
        $error = 'Nama kategori harus diisi';
    } else {
        try {
            if (isset($_POST['edit_id'])) {
                // Update
                $id = (int)$_POST['edit_id'];

                // Check if name already exists (excluding current record)
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM kategori_berita WHERE nama_kategori = ? AND id != ?");
                $stmt->execute([$nama_kategori, $id]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "Nama kategori sudah ada";
                } else {
                    $stmt = $pdo->prepare("UPDATE kategori_berita SET nama_kategori = ? WHERE id = ?");
                    $stmt->execute([$nama_kategori, $id]);
                    $success = "Kategori berhasil diupdate";
                }
            } else {
                // Insert
                // Check if name already exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM kategori_berita WHERE nama_kategori = ?");
                $stmt->execute([$nama_kategori]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "Nama kategori sudah ada";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO kategori_berita (nama_kategori, created_at) VALUES (?, NOW())");
                    $stmt->execute([$nama_kategori]);
                    $success = "Kategori berhasil ditambahkan";
                }
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Get edit data
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM kategori_berita WHERE id = ?");
        $stmt->execute([$id]);
        $edit_kategori = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get all categories with pagination
$page = validate_page($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Get total count
    $total_kategori = $pdo->query("SELECT COUNT(*) FROM kategori_berita")->fetchColumn();
    $total_pages = ceil($total_kategori / $limit);

    // Get categories list with news count
    $stmt = $pdo->prepare("
        SELECT k.*, COUNT(b.id) as berita_count
        FROM kategori_berita k 
        LEFT JOIN berita b ON k.id = b.kategori_id 
        GROUP BY k.id
        ORDER BY k.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    $kategori_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
    error_log("Kategori error: " . $e->getMessage());
}

$show_form = isset($_GET['add']) || isset($_GET['edit']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Vihara Watugong</title>
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

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: #ecfdf5;
            color: var(--success);
            border-color: var(--success);
        }

        .alert-danger {
            background: #fef2f2;
            color: var(--danger);
            border-color: var(--danger);
        }

        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .action-bar h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: var(--text-light);
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        /* Card */
        .card {
            background: var(--bg-white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
            background: #f8fafc;
        }

        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .card-body {
            padding: 2rem;
        }

        /* Form */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Table */
        .table-container {
            background: var(--bg-white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            background: #f8fafc;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            color: var(--text-dark);
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        /* Status Badge */
        .count-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e0e7ff;
            color: #3730a3;
        }

        .count-badge.zero {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 0.875rem;
        }

        .pagination a:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination .current {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
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

            .action-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 600px;
            }

            .card-body {
                padding: 1rem;
            }

            .card-header {
                padding: 1rem;
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
                <a href="dashboard.php" class="nav-item">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>
                <a href="berita.php" class="nav-item">
                    <i class="fas fa-newspaper"></i>
                    Kelola Berita
                </a>
                <a href="kategori.php" class="nav-item active">
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
                    <h1>Kelola Kategori</h1>
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
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if ($show_form): ?>
                    <!-- Form Section -->
                    <div class="card">
                        <div class="card-header">
                            <h3><?php echo $edit_kategori ? 'Edit' : 'Tambah'; ?> Kategori</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <?php if ($edit_kategori): ?>
                                    <input type="hidden" name="edit_id" value="<?php echo $edit_kategori['id']; ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="nama_kategori" class="form-label">Nama Kategori *</label>
                                    <input type="text" id="nama_kategori" name="nama_kategori" class="form-control"
                                        value="<?php echo $edit_kategori ? htmlspecialchars($edit_kategori['nama_kategori']) : ''; ?>"
                                        required maxlength="50"
                                        placeholder="Masukkan nama kategori">
                                    <div style="font-size: 0.8rem; color: var(--text-light); margin-top: 0.25rem;">
                                        Maksimal 50 karakter
                                    </div>
                                </div>

                                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                                    <a href="kategori.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        <?php echo $edit_kategori ? 'Update' : 'Simpan'; ?> Kategori
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- List Section -->
                    <div class="action-bar">
                        <h2>Daftar Kategori</h2>
                        <a href="kategori.php?add=1" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Tambah Kategori
                        </a>
                    </div>

                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Jumlah Berita</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($kategori_list)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                            <i class="fas fa-tags" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                                            <div>Belum ada kategori</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($kategori_list as $kategori): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: var(--text-dark);">
                                                    #<?php echo $kategori['id']; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.25rem;">
                                                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="count-badge <?php echo $kategori['berita_count'] == 0 ? 'zero' : ''; ?>">
                                                    <?php echo $kategori['berita_count']; ?> berita
                                                </span>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.875rem; color: var(--text-dark);">
                                                    <?php echo date('d/m/Y', strtotime($kategori['created_at'])); ?>
                                                </div>
                                                <div style="font-size: 0.75rem; color: var(--text-light);">
                                                    <?php echo date('H:i', strtotime($kategori['created_at'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <a href="kategori.php?edit=<?php echo $kategori['id']; ?>"
                                                        class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="kategori.php?delete=<?php echo $kategori['id']; ?>"
                                                        class="btn btn-danger btn-sm" title="Hapus"
                                                        onclick="return confirmDelete('<?php echo htmlspecialchars($kategori['nama_kategori']); ?>', <?php echo $kategori['berita_count']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="kategori.php?page=1">&laquo; First</a>
                                <a href="kategori.php?page=<?php echo $page - 1; ?>">&lsaquo; Prev</a>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <?php if ($i == $page): ?>
                                    <span class="current"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="kategori.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="kategori.php?page=<?php echo $page + 1; ?>">Next &rsaquo;</a>
                                <a href="kategori.php?page=<?php echo $total_pages; ?>">Last &raquo;</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
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

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });

        // Confirm delete with special handling for categories with news
        function confirmDelete(kategoriName, beritaCount) {
            if (beritaCount > 0) {
                alert(`Kategori "${kategoriName}" tidak dapat dihapus karena masih digunakan oleh ${beritaCount} berita.\n\nSilakan hapus atau pindahkan berita tersebut terlebih dahulu.`);
                return false;
            }

            return confirm(`Yakin ingin menghapus kategori "${kategoriName}"?\n\nTindakan ini tidak dapat dibatalkan.`);
        }

        // Character counter for input
        // Character counter for input
        document.addEventListener('DOMContentLoaded', function() {
            const namaKategoriInput = document.getElementById('nama_kategori');

            if (namaKategoriInput) {
                // Create character counter element
                const counterDiv = document.createElement('div');
                counterDiv.style.cssText = 'font-size: 0.75rem; color: var(--text-light); margin-top: 0.25rem; text-align: right;';
                counterDiv.innerHTML = `<span id="char-count">0</span>/50 karakter`;

                // Insert after the existing help text
                const helpText = namaKategoriInput.parentNode.querySelector('div');
                if (helpText) {
                    helpText.parentNode.insertBefore(counterDiv, helpText.nextSibling);
                }

                // Update counter on input
                const charCountSpan = document.getElementById('char-count');
                namaKategoriInput.addEventListener('input', function() {
                    const length = this.value.length;
                    charCountSpan.textContent = length;

                    // Change color based on character count
                    if (length > 45) {
                        charCountSpan.style.color = 'var(--danger)';
                    } else if (length > 35) {
                        charCountSpan.style.color = 'var(--warning)';
                    } else {
                        charCountSpan.style.color = 'var(--text-light)';
                    }
                });

                // Initialize counter
                namaKategoriInput.dispatchEvent(new Event('input'));
            }
        });

        // Form validation enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const namaKategori = document.getElementById('nama_kategori');
                    if (namaKategori) {
                        const value = namaKategori.value.trim();

                        // Check if empty
                        if (!value) {
                            e.preventDefault();
                            alert('Nama kategori harus diisi!');
                            namaKategori.focus();
                            return false;
                        }

                        // Check length
                        if (value.length > 50) {
                            e.preventDefault();
                            alert('Nama kategori maksimal 50 karakter!');
                            namaKategori.focus();
                            return false;
                        }

                        // Check for special characters (optional - adjust as needed)
                        const invalidChars = /[<>]/;
                        if (invalidChars.test(value)) {
                            e.preventDefault();
                            alert('Nama kategori tidak boleh mengandung karakter < atau >');
                            namaKategori.focus();
                            return false;
                        }
                    }
                });
            }
        });

        // Enhanced table interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects for table rows
            const tableRows = document.querySelectorAll('.table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8fafc';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // Add keyboard navigation for action buttons
            const actionButtons = document.querySelectorAll('.btn');
            actionButtons.forEach(btn => {
                btn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        });

        // Loading state for form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            const submitBtn = document.querySelector('button[type="submit"]');

            if (form && submitBtn) {
                const originalText = submitBtn.innerHTML;

                form.addEventListener('submit', function() {
                    // Disable button and show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                    // Re-enable after 3 seconds as fallback
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 3000);
                });
            }
        });

        // Search functionality (if needed for future enhancement)
        function filterTable() {
            const searchInput = document.getElementById('search');
            if (!searchInput) return;

            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('.table tbody tr');

            rows.forEach(row => {
                const kategoriName = row.querySelector('td:nth-child(2)');
                if (kategoriName) {
                    const text = kategoriName.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + N = New category
            if ((e.ctrlKey || e.metaKey) && e.key === 'n' && !document.querySelector('form[method="POST"]')) {
                e.preventDefault();
                window.location.href = 'kategori.php?add=1';
            }

            // Escape key = Cancel/Back
            if (e.key === 'Escape') {
                const backBtn = document.querySelector('a[href="kategori.php"]');
                if (backBtn) {
                    window.location.href = 'kategori.php';
                }
            }
        });

        // Smooth scrolling for mobile menu
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-item');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeMobileMenu();
                    }
                });
            });
        });

        // Auto-focus on form inputs
        document.addEventListener('DOMContentLoaded', function() {
            const namaKategoriInput = document.getElementById('nama_kategori');
            if (namaKategoriInput && window.innerWidth > 768) {
                // Only auto-focus on desktop
                setTimeout(() => {
                    namaKategoriInput.focus();
                }, 100);
            }
        });

        // Enhanced confirmation dialogs
        function showDeleteConfirmation(kategoriName, beritaCount, kategoriId) {
            if (beritaCount > 0) {
                // Create custom modal for categories with news
                const modal = document.createElement('div');
                modal.style.cssText = `
                    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                    background: rgba(0,0,0,0.5); z-index: 1000;
                    display: flex; align-items: center; justify-content: center;
                `;

                modal.innerHTML = `
                    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 400px; text-align: center;">
                        <i class="fas fa-exclamation-triangle" style="color: var(--warning); font-size: 3rem; margin-bottom: 1rem;"></i>
                        <h3 style="margin-bottom: 1rem;">Tidak Dapat Menghapus</h3>
                        <p style="margin-bottom: 1.5rem; color: var(--text-light);">
                            Kategori "${kategoriName}" masih digunakan oleh ${beritaCount} berita.<br>
                            Silakan hapus atau pindahkan berita tersebut terlebih dahulu.
                        </p>
                        <button onclick="this.closest('div').parentNode.remove()" 
                                style="background: var(--primary); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; cursor: pointer;">
                            Mengerti
                        </button>
                    </div>
                `;

                document.body.appendChild(modal);
                return false;
            }

            return confirm(`Yakin ingin menghapus kategori "${kategoriName}"?\n\nTindakan ini tidak dapat dibatalkan.`);
        }
    </script>
</body>

</html>