<?php
include '../config/koneksi.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Pastikan session tersedia
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$preview = $_SESSION['preview_siswa'] ?? [];
$siapImpor = false;

// Ambil daftar kelas
$queryKelas = mysqli_query($koneksi, "SELECT id_kelas, nama_kelas FROM tbl_kelas");
$daftarKelas = [];

while ($kelas = mysqli_fetch_assoc($queryKelas)) {
    $daftarKelas[mb_strtolower(trim($kelas['nama_kelas']))] = $kelas['id_kelas'];
}

// ======================================
// PROSES UPLOAD DAN PREVIEW EXCEL
// ======================================
if (isset($_POST['preview_excel'])) {
    unset($_SESSION['preview_siswa']);
    $preview = [];
    $errors = [];

    if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File Excel gagal diunggah.";
    } else {
        $file = $_FILES['file_excel'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($extension !== 'xlsx') {
            $errors[] = "Format file harus Excel .xlsx.";
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal 5 MB.";
        } else {
            try {
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
                $barisTerakhir = $sheet->getHighestDataRow();

                // Periksa header
                $headerNis = strtolower(trim((string) $sheet->getCell('A1')->getValue()));
                $headerNama = strtolower(trim((string) $sheet->getCell('B1')->getValue()));
                $headerKelas = strtolower(trim((string) $sheet->getCell('C1')->getValue()));

                if ($headerNis !== 'nis' || $headerNama !== 'nama siswa' || $headerKelas !== 'kelas') {
                    $errors[] = "Format kolom Excel tidak sesuai template.";
                } else {
                    $nisDalamFile = [];

                    for ($i = 2; $i <= $barisTerakhir; $i++) {
                        $nis = trim((string) $sheet->getCell('A' . $i)->getFormattedValue());
                        $nama = trim((string) $sheet->getCell('B' . $i)->getValue());
                        $namaKelas = trim((string) $sheet->getCell('C' . $i)->getValue());

                        // Lewati baris yang benar-benar kosong
                        if ($nis === '' && $nama === '' && $namaKelas === '') {
                            continue;
                        }

                        $status = 'Valid';
                        $idKelas = null;

                        // Validasi NIS
                        if ($nis === '') {
                            $status = 'NIS kosong';
                        } elseif (isset($nisDalamFile[$nis])) {
                            $status = 'NIS ganda dalam Excel';
                        } else {
                            $nisDalamFile[$nis] = true;
                            // Periksa NIS di database
                            $stmt = mysqli_prepare($koneksi, "SELECT id_siswa FROM tbl_siswa WHERE nis = ? LIMIT 1");
                            mysqli_stmt_bind_param($stmt, "s", $nis);
                            mysqli_stmt_execute($stmt);
                            $hasil = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($hasil) > 0) {
                                $status = 'NIS sudah terdaftar';
                            }
                            mysqli_stmt_close($stmt);
                        }

                        // Validasi nama
                        if ($nama === '' && $status === 'Valid') {
                            $status = 'Nama siswa kosong';
                        }

                        // Validasi kelas
                        $keyKelas = mb_strtolower($namaKelas);
                        if ($namaKelas === '' && $status === 'Valid') {
                            $status = 'Kelas kosong';
                        } elseif ($namaKelas !== '' && !isset($daftarKelas[$keyKelas]) && $status === 'Valid') {
                            $status = 'Kelas tidak ditemukan';
                        } elseif (isset($daftarKelas[$keyKelas])) {
                            $idKelas = $daftarKelas[$keyKelas];
                        }

                        $preview[] = [
                            'baris' => $i,
                            'nis' => $nis,
                            'nama' => $nama,
                            'kelas' => $namaKelas,
                            'id_kelas' => $idKelas,
                            'status' => $status
                        ];

                        if ($status !== 'Valid') {
                            $errors[] = "Baris $i: $status ($nis)";
                        }
                    }

                    if (empty($preview)) {
                        $errors[] = "Tidak ada data siswa dalam Excel.";
                    }

                    if (empty($errors)) {
                        $_SESSION['preview_siswa'] = $preview;
                    }
                }

                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
            } catch (Throwable $e) {
                $errors[] = "File Excel tidak dapat dibaca. Periksa format file.";
            }
        }
    }
}

// ======================================
// PROSES SIMPAN IMPOR
// ======================================
if (isset($_POST['simpan_impor'])) {
    $preview = $_SESSION['preview_siswa'] ?? [];

    if (empty($preview)) {
        $errors[] = "Tidak ada data untuk diimpor.";
    } else {
        mysqli_begin_transaction($koneksi);
        try {
            $stmtCek = mysqli_prepare($koneksi, "SELECT id_siswa FROM tbl_siswa WHERE nis = ? LIMIT 1");
            $stmtInsert = mysqli_prepare($koneksi, "INSERT INTO tbl_siswa (nis, nama_siswa, id_kelas) VALUES (?, ?, ?)");

            foreach ($preview as $data) {
                // Periksa ulang NIS sebelum menyimpan
                mysqli_stmt_bind_param($stmtCek, "s", $data['nis']);
                mysqli_stmt_execute($stmtCek);
                $hasil = mysqli_stmt_get_result($stmtCek);

                if (mysqli_num_rows($hasil) > 0) {
                    throw new Exception("NIS " . $data['nis'] . " sudah terdaftar. Seluruh impor dibatalkan.");
                }

                if ($data['status'] !== 'Valid' || empty($data['id_kelas'])) {
                    throw new Exception("Ditemukan data tidak valid. Impor dibatalkan.");
                }

                // Simpan siswa
                mysqli_stmt_bind_param($stmtInsert, "ssi", $data['nis'], $data['nama'], $data['id_kelas']);
                if (!mysqli_stmt_execute($stmtInsert)) {
                    throw new Exception("Gagal menyimpan data siswa.");
                }
            }

            mysqli_stmt_close($stmtCek);
            mysqli_stmt_close($stmtInsert);
            mysqli_commit($koneksi);
            unset($_SESSION['preview_siswa']);

            echo "<script>
                    alert('Berhasil mengimpor " . count($preview) . " data siswa!');
                    window.location.href='ad_index.php?page=siswa';
                  </script>";
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($koneksi);
            $errors[] = $e->getMessage();
        }
    }
}
?>

<div class="crud-header">
    <h2>Impor Data Siswa</h2>
    <p>Impor data siswa secara massal menggunakan file Excel.</p>
</div>

<div class="crud-section">
    <div class="impor-header">
        <h4>Upload File Excel</h4>
        <a href="modSiswa/templateSiswa.php" class="btn-download-template">
            <i class="bi bi-download"></i> Download Template
        </a>
    </div>

    <form method="POST" enctype="multipart/form-data" action="ad_index.php?page=imporSiswa">
        <div class="impor-upload">
            <i class="bi bi-file-earmark-excel"></i>
            <h4>Pilih File Excel</h4>
            <p>Format .xlsx, maksimal 5 MB</p>
            <input type="file" name="file_excel" accept=".xlsx" required>
        </div>
        <button type="submit" name="preview_excel" class="btn-proses-impor">
            <i class="bi bi-eye"></i> Preview Data
        </button>
    </form>

    <?php if (!empty($errors)) { ?>
        <div class="impor-error">
            <strong>Impor tidak dapat dilanjutkan:</strong>
            <?php foreach ($errors as $error) { ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (!empty($preview)) { ?>
        <div class="preview-impor">
            <h4>Preview Data Siswa</h4>
            <p>Total data: <?php echo count($preview); ?> siswa</p>
            <div class="table-wrapper">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($preview as $index => $data) { ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($data['nis']); ?></td>
                                <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                <td><?php echo htmlspecialchars($data['kelas']); ?></td>
                                <td><?php echo htmlspecialchars($data['status']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($errors)) { ?>
                <form method="POST" action="ad_index.php?page=imporSiswa" onsubmit="return confirm('Apakah kamu yakin ingin menyimpan seluruh data siswa?');">
                    <button type="submit" name="simpan_impor" class="btn-proses-impor">
                        <i class="bi bi-check-circle"></i> Simpan Semua Data
                    </button>
                </form>
            <?php } else { ?>
                <p class="impor-warning">Perbaiki seluruh kesalahan pada file Excel, kemudian unggah kembali file tersebut. Tidak ada data yang disimpan.</p>
            <?php } ?>
        </div>
    <?php } ?>
</div>