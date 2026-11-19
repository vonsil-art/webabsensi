<?php
$page = $_GET['page'] ?? 'beranda';
?>

<div class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-brand">
            <img src="../gambar/LogoDarulFatah.png" alt="Logo Sekolah" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <h2>SDQu DARUL FATAH</h2>
            </div>
        </div>
        <div class="sidebar-menu">
            <a href="gr_index.php" class="menu-item <?php echo ($page == 'beranda') ? 'active' : ''; ?>">
                <i class="bi bi-house-door"></i>
                <span>Dashboard</span>
            </a>
            <a href="gr_index.php?page=riwayat" class="menu-item <?php echo ($page == 'riwayat' || $page == 'detail_riwayat') ? 'active' : ''; ?>">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Absensi</span>
            </a>
            <?php if ($_SESSION['role'] == "wali") { ?>
                <a href="gr_index.php?page=rekap" class="menu-item <?php echo ($page == 'rekap') ? 'active' : ''; ?>">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Laporan Absensi</span>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="sidebar-bottom">
        <a href="logout.php" class="menu-item logout-menu">
            <i class="bi bi-box-arrow-left"></i>
            <span>Keluar Aplikasi</span>
        </a>
    </div>
</div>
