<?php

include '../config/koneksi.php';

$query_siswa = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total_siswa FROM tbl_siswa"
);

$data_siswa = mysqli_fetch_assoc($query_siswa);
$total_siswa = $data_siswa['total_siswa'];
$query_guru = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total_guru FROM tbl_guru"
);

$data_guru = mysqli_fetch_assoc($query_guru);

$total_guru = $data_guru['total_guru'];

?>

    <div class="admin-header">
        <div>
            <h1>Dashboard Admin</h1>
            <p>
                Ringkasan jumlah siswa dan guru.
            </p>
        </div>
        <div class="today-box">
            <i class="bi bi-calendar3"></i>
            <span>Hari Ini</span>
        </div>
    </div>
    <div class="admin-stat-container">
        <div class="admin-stat-card">
            <div class="stat-card-header">
                <div class="stat-icon">
                    <i class="bi bi-person"></i>
                </div>
                <div class="stat-name">
                    <span>Jumlah Siswa</span>
                    <small>
                        Total siswa terdaftar
                    </small>
                </div>
            </div>
            <div class="stat-number">
                <?php echo $total_siswa; ?>
            </div>
            <p class="stat-description">
                Jumlah siswa aktif yang terdaftar di sekolah.
            </p>
        </div>
        <div class="admin-stat-card">
            <div class="stat-card-header">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-name">
                    <span>Jumlah Guru</span>
                    <small>
                        Total guru aktif
                    </small>
                </div>
            </div>
            <div class="stat-number">
                <?php echo $total_guru; ?>
            </div>
            <p class="stat-description">
                Jumlah guru yang terdaftar dan aktif di sekolah.
            </p>
        </div>
    </div>
</div>
