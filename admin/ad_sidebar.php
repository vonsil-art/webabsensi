<?php

$page = $_GET['page'] ?? 'beranda';

?>
<div class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-brand">
            <img src="../gambar/LogoDarulFatah.png" alt="Logo Darul Fatah" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <h2>
                    SDQu DARUL FATAH
                </h2>
                <span>Admin</span>
            </div>
        </div>
        <div class="sidebar-menu">
            <a href="ad_index.php" class="menu-item <?php echo ($page == 'beranda') ? 'active' : ''; ?>">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
            <a href="ad_index.php?page=guru" class="menu-item <?php echo (
                    $page == 'guru' ||
                    $page == 'tambahGuru' ||
                    $page == 'ubahGuru'
                ) ? 'active' : ''; ?>"
                >

                <i class="bi bi-people"></i>
                <span>Data Guru</span>
            </a>
            <a href="ad_index.php?page=kelas" class="menu-item <?php echo (
                    $page == 'kelas' ||
                    $page == 'tambahKelas' ||
                    $page == 'ubahKelas'
                ) ? 'active' : ''; ?>"
            >

                <i class="bi bi-building"></i>
                <span>Data Kelas</span>
            </a>
            <a href="ad_index.php?page=siswa" class="menu-item <?php echo (
                    $page == 'siswa' ||
                    $page == 'tambahSiswa' ||
                    $page == 'ubahSiswa'
                ) ? 'active' : ''; ?>"
            >
                <i class="bi bi-person"></i>
                <span>Data Siswa</span>
            </a>
        </div>
    </div>
    <div class="sidebar-bottom">
        <a href="ad_index.php?page=logout" class="menu-item logout-menu">
            <i class="bi bi-box-arrow-left"></i>
            <span>Keluar</span>
        </a>
    </div>
</div>
