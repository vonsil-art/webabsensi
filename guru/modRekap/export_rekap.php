<?php
session_start();
include '../../config/koneksi.php';

/* CEK LOGIN */
if (!isset($_SESSION['is_login']) || !isset($_SESSION['id_guru'])) {
    header("Location: ../../index.php");
    exit;
}

/* ID WALI KELAS YANG LOGIN */
$id_guru = (int) $_SESSION['id_guru'];

/* AMBIL FILTER */
$semester = isset($_GET['semester'])
    ? (int) $_GET['semester']
    : 1;

$tahun_ajaran = isset($_GET['tahun_ajaran'])
    ? $_GET['tahun_ajaran']
    : '2026/2027';

/* PECAH TAHUN AJARAN */
$bagian_tahun = explode('/', $tahun_ajaran);
$tahun_awal = (int) $bagian_tahun[0];
$tahun_akhir = (int) $bagian_tahun[1];

/* TENTUKAN PERIODE SEMESTER */
if ($semester == 1) {
    $tanggal_awal = $tahun_awal . '-07-01';
    $tanggal_akhir = $tahun_awal . '-12-31';
} else {
    $tanggal_awal = $tahun_akhir . '-01-01';
    $tanggal_akhir = $tahun_akhir . '-06-30';
}

/* AMBIL KELAS YANG DIWALIKAN */
$query_kelas = mysqli_query(
    $koneksi,
    "SELECT
        tbl_kelas.id_kelas,
        tbl_kelas.nama_kelas,
        tbl_guru.nama_guru
     FROM tbl_kelas
     JOIN tbl_guru
        ON tbl_kelas.id_wali_guru = tbl_guru.id_guru
     WHERE tbl_kelas.id_wali_guru='$id_guru'
     LIMIT 1"
);
$kelas = mysqli_fetch_assoc($query_kelas);

if (!$kelas) {
    die("Anda belum terdaftar sebagai wali kelas.");
}

$id_kelas = $kelas['id_kelas'];
$nama_kelas = $kelas['nama_kelas'];
$nama_wali = $kelas['nama_guru'];

/* HITUNG JUMLAH HARI ABSENSI */
$query_hari = mysqli_query(
    $koneksi,
    "SELECT
        COUNT(DISTINCT tbl_absensi.tanggal) AS jumlah_hari
     FROM tbl_absensi
     JOIN tbl_siswa
        ON tbl_absensi.id_siswa = tbl_siswa.id_siswa
     WHERE tbl_siswa.id_kelas='$id_kelas'
        AND tbl_absensi.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'"
);
$data_hari = mysqli_fetch_assoc($query_hari);
$jumlah_hari = (int) $data_hari['jumlah_hari'];

/* AMBIL DATA REKAP SISWA */
$query_rekap = mysqli_query(
    $koneksi,
    "SELECT
        tbl_siswa.id_siswa,
        tbl_siswa.nis,
        tbl_siswa.nama_siswa,
        SUM(
            CASE
                WHEN tbl_absensi.status='Hadir' THEN 1
                ELSE 0
            END
        ) AS hadir,
        SUM(
            CASE
                WHEN tbl_absensi.status='Sakit' THEN 1
                ELSE 0
            END
        ) AS sakit,
        SUM(
            CASE
                WHEN tbl_absensi.status='Izin' THEN 1
                ELSE 0
            END
        ) AS izin,
        SUM(
            CASE
                WHEN tbl_absensi.status='Alpa' THEN 1
                ELSE 0
            END
        ) AS alpa
     FROM tbl_siswa
     LEFT JOIN tbl_absensi
        ON tbl_siswa.id_siswa = tbl_absensi.id_siswa
        AND tbl_absensi.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
     WHERE tbl_siswa.id_kelas='$id_kelas'
     GROUP BY
        tbl_siswa.id_siswa,
        tbl_siswa.nis,
        tbl_siswa.nama_siswa
     ORDER BY tbl_siswa.nama_siswa ASC"
);

/* NAMA FILE */
$nama_file = "Rekap_Absensi_"
    . str_replace(' ', '_', $nama_kelas)
    . "_Semester_"
    . $semester
    . ".xls";

/* HEADER EXCEL */
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_file\"");
header("Pragma: no-cache");
header("Expires: 0");
?>

<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>

        <table>
            <tr>
                <th colspan="8">LAPORAN REKAP ABSENSI SISWA</th>
            </tr>
            <tr>
                <td>Kelas</td>
                <td colspan="7">: <?php echo htmlspecialchars($nama_kelas); ?></td>
            </tr>
            <tr>
                <td>Wali Kelas</td>
                <td colspan="7">: <?php echo htmlspecialchars($nama_wali); ?></td>
            </tr>
            <tr>
                <td>Semester</td>
                <td colspan="7">: Semester <?php echo $semester; ?></td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td colspan="7">: <?php echo htmlspecialchars($tahun_ajaran); ?></td>
            </tr>
            <tr>
                <td>Jumlah Hari</td>
                <td colspan="7">: <?php echo $jumlah_hari; ?> Hari</td>
            </tr>
        </table>

        <br>

        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alpa</th>
                    <th>Persentase Hadir</th>
                    <th>Persentase Sakit</th>
                    <th>Persentase Izin</th>
                    <th>Persentase Alpa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($data = mysqli_fetch_assoc($query_rekap)) {
                    $hadir = (int) $data['hadir'];
                    $sakit = (int) $data['sakit'];
                    $izin = (int) $data['izin'];
                    $alpa = (int) $data['alpa'];

                    /* HITUNG PERSENTASE */
                    if ($jumlah_hari > 0) {
                        $persen_hadir = ($hadir / $jumlah_hari) * 100;
                        $persen_sakit = ($sakit / $jumlah_hari) * 100;
                        $persen_izin = ($izin / $jumlah_hari) * 100;
                        $persen_alpa = ($alpa / $jumlah_hari) * 100;
                    } else {
                        $persen_hadir = 0;
                        $persen_sakit = 0;
                        $persen_izin = 0;
                        $persen_alpa = 0;
                    }
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($data['nis']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama_siswa']); ?></td>
                        <td><?php echo $hadir; ?></td>
                        <td><?php echo $sakit; ?></td>
                        <td><?php echo $izin; ?></td>
                        <td><?php echo $alpa; ?></td>
                        <td><?php echo number_format($persen_hadir, 1, ',', '.'); ?>%</td>
                        <td><?php echo number_format($persen_sakit, 1, ',', '.'); ?>%</td>
                        <td><?php echo number_format($persen_izin, 1, ',', '.'); ?>%</td>
                        <td><?php echo number_format($persen_alpa, 1, ',', '.'); ?>%</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </body>
</html>
```[cite: 34]