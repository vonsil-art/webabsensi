
<?php
date_default_timezone_set('Asia/Jakarta');

$tanggal_sekarang = date('d-m-Y');
$tanggal_database = date('Y-m-d');

$id_guru = (int) $_SESSION['id_guru'];

/* CEK APAKAH AKUN INI WALI KELAS */
$query_kelas_wali = mysqli_query(
    $koneksi,
    "SELECT id_kelas, nama_kelas
     FROM tbl_kelas
     WHERE id_wali_guru='$id_guru'
     LIMIT 1"
);

$kelas_wali = mysqli_fetch_assoc($query_kelas_wali);
$is_wali = ($kelas_wali !== null);

/* AMBIL SEMUA KELAS UNTUK GURU BIASA */
$query_kelas = mysqli_query(
    $koneksi,
    "SELECT * FROM tbl_kelas ORDER BY nama_kelas ASC"
);

/* STATISTIK ABSENSI BERDASARKAN AKUN YANG LOGIN */
$query_stat = mysqli_query($koneksi, "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) AS hadir,
        SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END) AS sakit,
        SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END) AS izin,
        SUM(CASE WHEN status='Alpa' THEN 1 ELSE 0 END) AS alpa
     FROM tbl_absensi
     WHERE tanggal='$tanggal_database'
     AND id_guru='$id_guru'"
);

$data_stat = mysqli_fetch_assoc($query_stat);

$total_absensi = $data_stat['total'];

$jumlah_hadir = $data_stat['hadir'] ?? 0;
$jumlah_sakit = $data_stat['sakit'] ?? 0;
$jumlah_izin = $data_stat['izin'] ?? 0;
$jumlah_alpa = $data_stat['alpa'] ?? 0;

if ($total_absensi > 0) {
    $persen_hadir = round(($jumlah_hadir / $total_absensi) * 100, 1);
    $persen_sakit = round(($jumlah_sakit / $total_absensi) * 100, 1);
    $persen_izin = round(($jumlah_izin / $total_absensi) * 100, 1);
    $persen_alpa = round(($jumlah_alpa / $total_absensi) * 100, 1);
} else {
    $persen_hadir = 0;
    $persen_sakit = 0;
    $persen_izin = 0;
    $persen_alpa = 0;
}
?>

<div class="welcome">
    <div>
        <h1>Selamat Pagi, <?php echo htmlspecialchars($_SESSION['nama_guru']); ?></h1>

        <p>
            <?php if ($is_wali) { ?>
                Silakan lakukan absensi siswa pada kelas wali Anda.
            <?php } else { ?>
                Silakan lakukan absensi siswa pada kelas yang dipilih.
            <?php } ?>
        </p>
    </div>

    <div class="date-box">
        <i class="bi bi-calendar3"></i>
        <?php echo $tanggal_sekarang; ?>
    </div>
</div>

<div class="stat-container">

    <div class="stat-card">
        <div class="stat-title">
            Hadir
            <span class="dot hadir"></span>
        </div>
        <div class="stat-row">
            <div class="stat-number">
                <?php echo $jumlah_hadir; ?>
                <span>Siswa</span>
            </div>
            <div class="stat-percent percent-hadir">
                <?php echo $persen_hadir; ?>%
            </div>
        </div>
        <p>Hadir di kelas</p>
    </div>

    <div class="stat-card">
        <div class="stat-title">
            Sakit
            <span class="dot sakit"></span>
        </div>
        <div class="stat-row">
            <div class="stat-number">
                <?php echo $jumlah_sakit; ?>
                <span>Siswa</span>
            </div>
            <div class="stat-percent percent-sakit">
                <?php echo $persen_sakit; ?>%
            </div>
        </div>
        <p>Surat keterangan sakit</p>
    </div>

    <div class="stat-card">
        <div class="stat-title">
            Izin
            <span class="dot izin"></span>
        </div>
        <div class="stat-row">
            <div class="stat-number">
                <?php echo $jumlah_izin; ?>
                <span>Siswa</span>
            </div>
            <div class="stat-percent percent-izin">
                <?php echo $persen_izin; ?>%
            </div>
        </div>
        <p>Izin orang tua</p>
    </div>

    <div class="stat-card">
        <div class="stat-title">
            Alpa
            <span class="dot alpa"></span>
        </div>
        <div class="stat-row">
            <div class="stat-number">
                <?php echo $jumlah_alpa; ?>
                <span>Siswa</span>
            </div>
            <div class="stat-percent percent-alpa">
                <?php echo $persen_alpa; ?>%
            </div>
        </div>
        <p>Belum ada kabar</p>
    </div>

</div>

<div class="attendance-card">
    <div class="attendance-header">
        <div class="attendance-title">
            <div class="attendance-icon">
                <i class="bi bi-clock"></i>
            </div>
            <strong>Absensi Pagi</strong>
        </div>

        <?php if ($is_wali) { ?>

            <div class="class-select">
                Kelas <?php echo htmlspecialchars($kelas_wali['nama_kelas']); ?>
            </div>

        <?php } else { ?>

            <select id="pilihKelas" class="class-select">
                <option value="" data-nama="">
                    Pilih Kelas
                </option>

                <?php while ($kelas = mysqli_fetch_assoc($query_kelas)) { ?>
                    <option
                        value="<?php echo $kelas['id_kelas']; ?>"
                        data-nama="<?php echo htmlspecialchars($kelas['nama_kelas']); ?>"
                    >
                        Kelas <?php echo htmlspecialchars($kelas['nama_kelas']); ?>
                    </option>
                <?php } ?>
            </select>

        <?php } ?>
    </div>

    <hr>

    <?php if ($is_wali) { ?>

        <button type="button" class="btn-absensi" onclick="mulaiAbsensiWali()">
            Mulai Absensi
            <span>
                Catat kehadiran siswa kelas
                <?php echo htmlspecialchars($kelas_wali['nama_kelas']); ?>
            </span>
        </button>

    <?php } else { ?>

        <button type="button" class="btn-absensi" onclick="mulaiAbsensi()">
            Mulai Absensi
            <span id="keteranganKelas">
                Pilih kelas terlebih dahulu
            </span>
        </button>

    <?php } ?>
</div>

<script>
<?php if ($is_wali) { ?>

function mulaiAbsensiWali() {
    window.location.href =
        "gr_index.php?page=absensi&id_kelas=<?php echo (int) $kelas_wali['id_kelas']; ?>";
}

<?php } else { ?>

const pilihKelas =
    document.getElementById("pilihKelas");

const keteranganKelas =
    document.getElementById("keteranganKelas");

pilihKelas.addEventListener("change", function() {
    const pilihan =
        pilihKelas.options[pilihKelas.selectedIndex];

    const namaKelas =
        pilihan.getAttribute("data-nama");

    if (pilihKelas.value != "") {
        keteranganKelas.innerHTML =
            "Catat kehadiran siswa kelas " + namaKelas;
    } else {
        keteranganKelas.innerHTML =
            "Pilih kelas terlebih dahulu";
    }
});

function mulaiAbsensi() {
    const idKelas = pilihKelas.value;

    if (idKelas == "") {
        alert("Silakan pilih kelas terlebih dahulu!");
        return;
    }

    window.location.href =
        "gr_index.php?page=absensi&id_kelas=" + idKelas;
}

<?php } ?>
</script>
