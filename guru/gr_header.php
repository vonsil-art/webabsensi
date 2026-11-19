<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <link rel="stylesheet" href="../css/guru.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="dashboard-container">
    <?php require 'gr_sidebar.php'; ?>
    
    <!-- Overlay untuk menu di layar HP -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center; gap: 10px;">
                <!-- Tombol Toggle Menu di HP -->
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h3>Dashboard Guru</h3>
            </div>
            <span>
                <?php echo htmlspecialchars($_SESSION['nama_guru']); ?>
            </span>
        </div>
        <div class="content">