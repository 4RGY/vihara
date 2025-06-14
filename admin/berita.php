<?php
require_once 'auth.php';
require_once '../config/database.php';
requireLogin();

$admin = getAdminInfo();

// Initialize variables
$error = '';
$success = '';
$berita_list = [];
$categories = [];
$edit_berita = null;

// Function to handle file upload
function handleFileUpload($file, $old_file = null)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $old_file; // Keep old file if no new file uploaded
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error uploading file');
    }

    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF');
    }

    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB');
    }

    $upload_dir = '../images/berita/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Gagal mengupload file');
    }

    // Delete old file if exists
    if ($old_file && file_exists($upload_dir . $old_file)) {
        unlink($upload_dir . $old_file);
    }

    return $filename;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Get file names before deleting
        $stmt = $pdo->prepare("SELECT gambar_utama, gambar_kedua FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        $berita_files = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete record
        $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
        $stmt->execute([$id]);

        // Delete files if they exist
        if ($berita_files) {
            $upload_dir = '../images/berita/';
            if ($berita_files['gambar_utama'] && file_exists('../images/berita/' . $berita_files['gambar_utama'])) {
                unlink($upload_dir . $berita_files['gambar_utama']);
            }
            if ($berita_files['gambar_kedua'] && file_exists('../images/berita/' . $berita_files['gambar_kedua'])) {
                unlink($upload_dir . $berita_files['gambar_kedua']);
            }
        }

        $success = "Berita berhasil dihapus";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle form submission
if ($_POST) {
    $judul = trim($_POST['judul']);
    $konten = trim($_POST['konten']);
    $excerpt = trim($_POST['excerpt']);
    $kategori_id = (int)$_POST['kategori_id'];
    $status = $_POST['status'];

    if (empty($judul) || empty($konten)) {
        $error = 'Judul dan konten harus diisi';
    } else {
        $slug = create_slug($judul);

        try {
            if (isset($_POST['edit_id'])) {
                // Update
                $id = (int)$_POST['edit_id'];

                // Get current file names
                $stmt = $pdo->prepare("SELECT gambar_utama, gambar_kedua FROM berita WHERE id = ?");
                $stmt->execute([$id]);
                $current_files = $stmt->fetch(PDO::FETCH_ASSOC);

                $gambar_utama = handleFileUpload($_FILES['gambar_utama'] ?? null, $current_files['gambar_utama']);
                $gambar_kedua = handleFileUpload($_FILES['gambar_kedua'] ?? null, $current_files['gambar_kedua']);

                $tanggal_publish = ($status == 'published') ? date('Y-m-d') : null;
                $stmt = $pdo->prepare("UPDATE berita SET judul = ?, slug = ?, konten = ?, excerpt = ?, gambar_utama = ?, gambar_kedua = ?, kategori_id = ?, status = ?, tanggal_publish = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$judul, $slug, $konten, $excerpt, $gambar_utama, $gambar_kedua, $kategori_id, $status, $tanggal_publish, $id]);
                $success = "Berita berhasil diupdate";
            } else {
                // Insert
                $gambar_utama = handleFileUpload($_FILES['gambar_utama'] ?? null);
                $gambar_kedua = handleFileUpload($_FILES['gambar_kedua'] ?? null);

                $tanggal_publish = ($status == 'published') ? date('Y-m-d') : null;
                $stmt = $pdo->prepare("INSERT INTO berita (judul, slug, konten, excerpt, gambar_utama, gambar_kedua, kategori_id, status, tanggal_publish, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$judul, $slug, $konten, $excerpt, $gambar_utama, $gambar_kedua, $kategori_id, $status, $tanggal_publish]);
                $success = "Berita berhasil ditambahkan";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Get edit data
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        $edit_berita = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get all berita with pagination
$page = validate_page($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Get categories
    $categories = $pdo->query("SELECT * FROM kategori_berita ORDER BY nama_kategori")->fetchAll(PDO::FETCH_ASSOC);

    // Get total count
    $total_berita = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
    $total_pages = ceil($total_berita / $limit);

    // Get berita list
    $stmt = $pdo->prepare("
        SELECT b.*, k.nama_kategori 
        FROM berita b 
        LEFT JOIN kategori_berita k ON b.kategori_id = k.id 
        ORDER BY b.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    $berita_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
    error_log("Berita error: " . $e->getMessage());
}

$show_form = isset($_GET['add']) || isset($_GET['edit']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Vihara Watugong</title>
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

        .form-control-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-control-large {
            min-height: 200px;
        }

        /* File Upload */
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: -9999px;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border: 2px dashed var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .file-upload-label:hover {
            border-color: var(--primary);
            background: #f0f4ff;
        }

        .file-upload-icon {
            width: 2rem;
            height: 2rem;
            background: var(--primary);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .file-upload-text {
            flex: 1;
        }

        .file-upload-text .main {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .file-upload-text .sub {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .file-preview {
            margin-top: 1rem;
            display: none;
        }

        .file-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .file-preview .file-info {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: var(--text-light);
        }

        /* Image Grid */
        .image-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .image-item {
            text-align: center;
        }

        .image-thumbnail {
            width: 100%;
            max-width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 0.5rem;
        }

        .image-placeholder {
            width: 100%;
            max-width: 200px;
            height: 150px;
            background: var(--bg-light);
            border: 2px dashed var(--border);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .image-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-dark);
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

        .table-image {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 6px;
        }

        /* Status Badge */
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

            .image-grid {
                grid-template-columns: 1fr;
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
                <a href="berita.php" class="nav-item active">
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
                    <h1>Kelola Berita</h1>
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
                            <h3><?php echo $edit_berita ? 'Edit' : 'Tambah'; ?> Berita</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($edit_berita): ?>
                                    <input type="hidden" name="edit_id" value="<?php echo $edit_berita['id']; ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="judul" class="form-label">Judul Berita *</label>
                                    <input type="text" id="judul" name="judul" class="form-control"
                                        value="<?php echo $edit_berita ? htmlspecialchars($edit_berita['judul']) : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="kategori_id" class="form-label">Kategori</label>
                                    <select id="kategori_id" name="kategori_id" class="form-control">
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"
                                                <?php echo ($edit_berita && $edit_berita['kategori_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="excerpt" class="form-label">Ringkasan</label>
                                    <textarea id="excerpt" name="excerpt" class="form-control form-control-textarea"><?php echo $edit_berita ? htmlspecialchars($edit_berita['excerpt']) : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="konten" class="form-label">Konten Berita *</label>
                                    <textarea id="konten" name="konten" class="form-control form-control-large" required><?php echo $edit_berita ? htmlspecialchars($edit_berita['konten']) : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Gambar</label>
                                    <div class="image-grid">
                                        <div class="image-item">
                                            <div class="file-upload">
                                                <input type="file" id="gambar_utama" name="gambar_utama" accept="image/*" onchange="previewImage(this, 'preview1')">
                                                <label for="gambar_utama" class="file-upload-label">
                                                    <div class="file-upload-icon">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                    <div class="file-upload-text">
                                                        <div class="main">Pilih Gambar Utama</div>
                                                        <div class="sub">JPG, PNG, GIF (Max 5MB)</div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="image-label">Gambar Utama</div>
                                            <div id="preview1" class="file-preview">
                                                <?php if ($edit_berita && $edit_berita['gambar_utama']): ?>
                                                    <img src="../images/berita/<?php echo htmlspecialchars($edit_berita['gambar_utama']); ?>" class="image-thumbnail">
                                                <?php else: ?>
                                                    <div class="image-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="image-item">
                                            <div class="file-upload">
                                                <input type="file" id="gambar_kedua" name="gambar_kedua" accept="image/*" onchange="previewImage(this, 'preview2')">
                                                <label for="gambar_kedua" class="file-upload-label">
                                                    <div class="file-upload-icon">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                    <div class="file-upload-text">
                                                        <div class="main">Pilih Gambar Kedua</div>
                                                        <div class="sub">JPG, PNG, GIF (Max 5MB)</div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="image-label">Gambar Kedua (Opsional)</div>
                                            <div id="preview2" class="file-preview">
                                                <?php if ($edit_berita && $edit_berita['gambar_kedua']): ?>
                                                    <img src="../images/berita/<?php echo htmlspecialchars($edit_berita['gambar_kedua']); ?>" class="image-thumbnail">
                                                <?php else: ?>
                                                    <div class="image-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="draft" <?php echo ($edit_berita && $edit_berita['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                        <option value="published" <?php echo ($edit_berita && $edit_berita['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                                    </select>
                                </div>

                                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                                    <a href="berita.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        <?php echo $edit_berita ? 'Update' : 'Simpan'; ?> Berita
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- List Section -->
                    <div class="action-bar">
                        <h2>Daftar Berita</h2>
                        <a href="berita.php?add=1" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Tambah Berita
                        </a>
                    </div>

                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($berita_list)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                            <i class="fas fa-newspaper" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                                            <div>Belum ada berita</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($berita_list as $berita): ?>
                                        <tr>
                                            <td>
                                                <?php if ($berita['gambar_utama']): ?>
                                                    <img src="../images/berita/<?php echo htmlspecialchars($berita['gambar_utama']); ?>"
                                                        class="table-image" alt="Gambar Berita">
                                                <?php else: ?>
                                                    <div class="table-image" style="background: var(--bg-light); display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.25rem;">
                                                    <?php echo htmlspecialchars(substr($berita['judul'], 0, 50)) . (strlen($berita['judul']) > 50 ? '...' : ''); ?>
                                                </div>
                                                <?php if ($berita['excerpt']): ?>
                                                    <div style="font-size: 0.8rem; color: var(--text-light);">
                                                        <?php echo htmlspecialchars(substr($berita['excerpt'], 0, 80)) . (strlen($berita['excerpt']) > 80 ? '...' : ''); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($berita['nama_kategori']): ?>
                                                    <span style="background: #e0e7ff; color: #3730a3; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                                        <?php echo htmlspecialchars($berita['nama_kategori']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: var(--text-light); font-size: 0.8rem;">Tanpa Kategori</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $berita['status']; ?>">
                                                    <?php echo ucfirst($berita['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.875rem; color: var(--text-dark);">
                                                    <?php echo date('d/m/Y', strtotime($berita['created_at'])); ?>
                                                </div>
                                                <div style="font-size: 0.75rem; color: var(--text-light);">
                                                    <?php echo date('H:i', strtotime($berita['created_at'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <a href="berita.php?edit=<?php echo $berita['id']; ?>"
                                                        class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="berita.php?delete=<?php echo $berita['id']; ?>"
                                                        class="btn btn-danger btn-sm" title="Hapus"
                                                        onclick="return confirm('Yakin ingin menghapus berita ini?')">
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
                                <a href="berita.php?page=1">&laquo; First</a>
                                <a href="berita.php?page=<?php echo $page - 1; ?>">&lsaquo; Prev</a>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <?php if ($i == $page): ?>
                                    <span class="current"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="berita.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="berita.php?page=<?php echo $page + 1; ?>">Next &rsaquo;</a>
                                <a href="berita.php?page=<?php echo $total_pages; ?>">Last &raquo;</a>
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

        // Image preview function
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="image-thumbnail" alt="Preview">
                        <div class="file-info">
                            <strong>${input.files[0].name}</strong><br>
                            Size: ${(input.files[0].size / 1024).toFixed(2)} KB
                        </div>
                    `;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        // Show existing image previews on page load
        document.addEventListener('DOMContentLoaded', function() {
            const preview1 = document.getElementById('preview1');
            const preview2 = document.getElementById('preview2');

            if (preview1.innerHTML.trim()) {
                preview1.style.display = 'block';
            }
            if (preview2.innerHTML.trim()) {
                preview2.style.display = 'block';
            }
        });

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

        // Confirm delete
        function confirmDelete(title) {
            return confirm(`Yakin ingin menghapus berita "${title}"?\n\nTindakan ini tidak dapat dibatalkan.`);
        }

        // Character counter for textarea
        document.addEventListener('DOMContentLoaded', function() {
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                // Add character counter
                const counter = document.createElement('div');
                counter.style.cssText = 'font-size: 0.8rem; color: var(--text-light); text-align: right; margin-top: 0.25rem;';
                textarea.parentNode.appendChild(counter);

                function updateCounter() {
                    const count = textarea.value.length;
                    const maxLength = textarea.getAttribute('maxlength');
                    if (maxLength) {
                        counter.textContent = `${count}/${maxLength} karakter`;
                    } else {
                        counter.textContent = `${count} karakter`;
                    }
                }

                textarea.addEventListener('input', updateCounter);
                updateCounter();
            });
        });

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const judul = document.getElementById('judul').value.trim();
                    const konten = document.getElementById('konten').value.trim();

                    if (!judul) {
                        alert('Judul berita harus diisi!');
                        e.preventDefault();
                        document.getElementById('judul').focus();
                        return;
                    }

                    if (!konten) {
                        alert('Konten berita harus diisi!');
                        e.preventDefault();
                        document.getElementById('konten').focus();
                        return;
                    }

                    // Check file sizes
                    const gambarUtama = document.getElementById('gambar_utama').files[0];
                    const gambarKedua = document.getElementById('gambar_kedua').files[0];

                    if (gambarUtama && gambarUtama.size > 5 * 1024 * 1024) {
                        alert('Ukuran gambar utama terlalu besar! Maksimal 5MB.');
                        e.preventDefault();
                        return;
                    }

                    if (gambarKedua && gambarKedua.size > 5 * 1024 * 1024) {
                        alert('Ukuran gambar kedua terlalu besar! Maksimal 5MB.');
                        e.preventDefault();
                        return;
                    }
                });
            }
        });
    </script>
</body>

</html>