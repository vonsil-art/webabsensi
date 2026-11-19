<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="dashboard-container">
    <?php require 'ad_sidebar.php'; ?>

    <!-- Overlay untuk menu di layar HP -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <!-- Letakkan tombol persis sebelum konten utama agar mudah diatur -->
        <button class="sidebar-toggle" id="sidebarToggle" style="margin-bottom: 20px;">
            <i class="bi bi-list"></i>
        </button>