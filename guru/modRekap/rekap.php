<?php
date_default_timezone_set('Asia/Jakarta');

/* AMBIL ID GURU DARI SESSION */
$id_guru = isset($_SESSION['id_guru']) ? (int) $_SESSION['id_guru'] : 0;

/* CEK SESSION GURU */
if ($id_guru == 0) {
    echo "<script>
            alert('Session guru tidak ditemukan. Silakan login kembali.');
            window.location.href='../index.php';
          </script>";
    return;
}

/* AMBIL KELAS YANG DIWALIKAN GURU */
$query_kelas = mysqli_query(
    $koneksi,
    "SELECT *
     FROM tbl_kelas
     WHERE id_wali_guru='$id_guru'
     LIMIT 1"
);
$kelasWali = mysqli_fetch_assoc($query_kelas);

/* JIKA TIDAK MEMILIKI KELAS WALI */
if (!$kelasWali) {
    echo "<script>
            alert('Akun dengan ID Guru $id_guru tidak memiliki kelas wali.');
            window.location.href='gr_index.php';
          </script>";
    return;
}

/* DEFAULT KELAS SESUAI WALI KELAS */
$id_kelas = (int) $kelasWali['id_kelas'];
$nama_kelas = $kelasWali['nama_kelas'];

/* =========================================
   TAHUN AJARAN DEFAULT
========================================= */
$tahunSekarang = (int) date('Y');
$bulanSekarang = (int) date('m');

if ($bulanSekarang >= 7) {
    $awalTahunAjaran = $tahunSekarang;
    $semesterDefault = 1;
} else {
    $awalTahunAjaran = $tahunSekarang - 1;
    $semesterDefault = 2;
}

$daftarTahun = array(
    $awalTahunAjaran . "/" . ($awalTahunAjaran + 1),
    ($awalTahunAjaran - 1) . "/" . $awalTahunAjaran,
    ($awalTahunAjaran - 2) . "/" . ($awalTahunAjaran - 1)
);

/* =========================================
   AMBIL FILTER PERIODE
========================================= */
$semester = isset($_GET['semester']) ? (int) $_GET['semester'] : $semesterDefault;
$tahun_ajaran = isset($_GET['tahun']) ? $_GET['tahun'] : $daftarTahun[0];

/* CEK SEMESTER */
if ($semester != 1 && $semester != 2) {
    $semester = 1;
}

/* CEK TAHUN AJARAN */
if (!in_array($tahun_ajaran, $daftarTahun)) {
    $tahun_ajaran = $daftarTahun[0];
}

/* =========================================
   TENTUKAN TANGGAL SEMESTER
========================================= */
$bagianTahun = explode('/', $tahun_ajaran);
$tahunAwal = (int) $bagianTahun[0];
$tahunAkhir = (int) $bagianTahun[1];

if ($semester == 1) {
    $tanggal_awal = $tahunAwal . "-07-01";
    $tanggal_akhir = $tahunAwal . "-12-31";
} else {
    $tanggal_awal = $tahunAkhir . "-01-01";
    $tanggal_akhir = $tahunAkhir . "-06-30";
}

/* =========================================
   HITUNG JUMLAH HARI SEKOLAH
========================================= */
$query_hari = mysqli_query($koneksi, "SELECT COUNT(DISTINCT tbl_absensi.tanggal) AS jumlah_hari
                                      FROM tbl_absensi
                                      JOIN tbl_siswa
                                      ON tbl_absensi.id_siswa = tbl_siswa.id_siswa
                                      WHERE tbl_siswa.id_kelas='$id_kelas'
                                      AND tbl_absensi.tanggal
                                      BETWEEN '$tanggal_awal' AND '$tanggal_akhir'");
$dataHari = mysqli_fetch_assoc($query_hari);
$jumlahHariSekolah = $dataHari['jumlah_hari'];

/* =========================================
   AMBIL REKAP SETIAP SISWA
========================================= */
$query_siswa = mysqli_query($koneksi, "SELECT
                                        tbl_siswa.id_siswa,
                                        tbl_siswa.nis,
                                        tbl_siswa.nama_siswa,
                                        COALESCE(SUM(
                                            CASE
                                                WHEN tbl_absensi.status='Hadir'
                                                THEN 1 ELSE 0
                                            END
                                        ), 0) AS hadir,
                                        COALESCE(SUM(
                                            CASE
                                                WHEN tbl_absensi.status='Sakit'
                                                THEN 1 ELSE 0
                                            END
                                        ), 0) AS sakit,
                                        COALESCE(SUM(
                                            CASE
                                                WHEN tbl_absensi.status='Izin'
                                                THEN 1 ELSE 0
                                            END
                                        ), 0) AS izin,
                                        COALESCE(SUM(
                                            CASE
                                                WHEN tbl_absensi.status='Alpa'
                                                THEN 1 ELSE 0
                                            END
                                        ), 0) AS alpa
                                      FROM tbl_siswa
                                      LEFT JOIN tbl_absensi
                                      ON tbl_siswa.id_siswa = tbl_absensi.id_siswa
                                      AND tbl_absensi.tanggal
                                      BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                                      WHERE tbl_siswa.id_kelas='$id_kelas'
                                      GROUP BY
                                        tbl_siswa.id_siswa,
                                        tbl_siswa.nis,
                                        tbl_siswa.nama_siswa
                                      ORDER BY tbl_siswa.nama_siswa ASC");
$dataSiswa = array();
while ($siswa = mysqli_fetch_assoc($query_siswa)) {
    $dataSiswa[] = $siswa;
}

/* =========================================
   HITUNG TOTAL
========================================= */
$totalHadir = 0;
$totalSakit = 0;
$totalIzin = 0;
$totalAlpa = 0;
foreach ($dataSiswa as $siswa) {
    $totalHadir += $siswa['hadir'];
    $totalSakit += $siswa['sakit'];
    $totalIzin += $siswa['izin'];
    $totalAlpa += $siswa['alpa'];
}
?>

<div class="rekap-heading">
    <div>
        <h1>Laporan Absensi Semester</h1>
        <p>Rekapitulasi absensi setiap siswa per kelas selama satu semester.</p>
    </div>
    <div class="semester-badge">
        <i class="bi bi-calendar3"></i>
        Semester <?php echo $semester; ?> • <?php echo htmlspecialchars($tahun_ajaran); ?>
    </div>
</div>

<!-- FILTER -->
<div class="rekap-filter">
    <div class="rekap-filter-title">
        <h3>Kelas & periode</h3>
        <p>Pilih semester dan tahun ajaran untuk menampilkan laporan.</p>
    </div>
    <div class="rekap-filter-row">
            <!-- KELAS (DIKUNCI SESUAI WALI KELAS) -->
    <div class="rekap-filter-group">
        <label>Kelas</label>
        <select id="rekapKelas" disabled>
            <option value="<?php echo $id_kelas; ?>" selected>
                Kelas <?php echo htmlspecialchars($nama_kelas); ?>
            </option>
        </select>
    </div>

        <!-- SEMESTER -->
        <div class="rekap-filter-group">
            <label>Semester</label>
            <select id="rekapSemester">
                <option value="1" <?php if ($semester == 1) echo "selected"; ?>>Semester 1</option>
                <option value="2" <?php if ($semester == 2) echo "selected"; ?>>Semester 2</option>
            </select>
        </div>

        <!-- TAHUN AJARAN -->
        <div class="rekap-filter-group">
            <label>Tahun Ajaran</label>
            <select id="rekapTahun">
                <?php foreach ($daftarTahun as $tahun) { ?>
                    <option value="<?php echo $tahun; ?>" <?php if ($tahun_ajaran == $tahun) echo "selected"; ?>>
                        <?php echo $tahun; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<!-- HASIL REKAP -->
<div class="rekap-card">
    <div class="rekap-card-header">
        <div>
            <h2>Rekapitulasi Absensi Kelas <?php echo htmlspecialchars($nama_kelas); ?></h2>
            <p>Semester <?php echo $semester; ?> • Tahun Ajaran <?php echo htmlspecialchars($tahun_ajaran); ?></p>
        </div>
        <div class="hari-sekolah">
            <?php echo $jumlahHariSekolah; ?> hari sekolah
        </div>
    </div>

    <!-- RINGKASAN -->
    <div class="rekap-summary">
        <div class="rekap-summary-card">
            <span>Total Hadir</span>
            <strong><?php echo number_format($totalHadir, 0, ',', '.'); ?></strong>
        </div>
        <div class="rekap-summary-card">
            <span>Total Sakit</span>
            <strong><?php echo number_format($totalSakit, 0, ',', '.'); ?></strong>
        </div>
        <div class="rekap-summary-card">
            <span>Total Izin</span>
            <strong><?php echo number_format($totalIzin, 0, ',', '.'); ?></strong>
        </div>
        <div class="rekap-summary-card">
            <span>Total Alpa</span>
            <strong><?php echo number_format($totalAlpa, 0, ',', '.'); ?></strong>
        </div>
    </div>

    <a href="modRekap/export_rekap.php?semester=<?php echo $semester; ?>&tahun_ajaran=<?php echo urlencode($tahun_ajaran); ?>" class="btn-export">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>

    <!-- TABEL -->
    <div class="rekap-table-container">
        <table class="rekap-table">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alpa</th>
                    <th>% Hadir</th>
                    <th>% Sakit</th>
                    <th>% Izin</th>
                    <th>% Alpa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataSiswa as $siswa) { ?>
                    <?php
                    if ($jumlahHariSekolah > 0) {
                        $persenHadir = ($siswa['hadir'] / $jumlahHariSekolah) * 100;
                        $persenSakit = ($siswa['sakit'] / $jumlahHariSekolah) * 100;
                        $persenIzin = ($siswa['izin'] / $jumlahHariSekolah) * 100;
                        $persenAlpa = ($siswa['alpa'] / $jumlahHariSekolah) * 100;
                    } else {
                        $persenHadir = 0;
                        $persenSakit = 0;
                        $persenIzin = 0;
                        $persenAlpa = 0;
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                        <td><?php echo $siswa['hadir']; ?></td>
                        <td><?php echo $siswa['sakit']; ?></td>
                        <td><?php echo $siswa['izin']; ?></td>
                        <td><?php echo $siswa['alpa']; ?></td>
                        <td><span class="persen-status persen-hadir"><?php echo number_format($persenHadir, 1, ',', '.'); ?>%</span></td>
                        <td><span class="persen-status persen-sakit"><?php echo number_format($persenSakit, 1, ',', '.'); ?>%</span></td>
                        <td><span class="persen-status persen-izin"><?php echo number_format($persenIzin, 1, ',', '.'); ?>%</span></td>
                        <td><span class="persen-status persen-alpa"><?php echo number_format($persenAlpa, 1, ',', '.'); ?>%</span></td>
                    </tr>
                <?php } ?>
                <?php if (count($dataSiswa) == 0) { ?>
                    <tr>
                        <td colspan="9">Belum ada data siswa pada kelas ini.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="rekap-footer">
        <span>Data berdasarkan absensi tersimpan • <?php echo $jumlahHariSekolah; ?> hari sekolah</span>
        <span><?php echo count($dataSiswa); ?> siswa ditampilkan</span>
    </div>
</div>

<script>
const semester = document.getElementById("rekapSemester");
const tahun = document.getElementById("rekapTahun");

semester.addEventListener("change", ubahFilter);
tahun.addEventListener("change", ubahFilter);

function ubahFilter() {
    window.location.href =
        "gr_index.php?page=rekap" +
        "&semester=" + semester.value +
        "&tahun=" + encodeURIComponent(tahun.value);
}
</script>