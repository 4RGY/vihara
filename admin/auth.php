<?php
session_start();

function requireLogin()
{
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ../admin/login.php');
        exit;
    }
}

function getAdminInfo()
{
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? null,
        'nama' => $_SESSION['admin_nama'] ?? null,
        'email' => $_SESSION['admin_email'] ?? null
    ];
}

function logout()
{
    session_destroy();
    header('Location: ../admin/login.php');
    exit;
}

// Auto logout handler
if (isset($_GET['logout'])) {
    logout();
}
