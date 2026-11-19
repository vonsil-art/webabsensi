<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['is_login'])) {
    header("Location: ../index.php");
    exit;
}

if (
    !isset($_SESSION['id_guru']) ||
    !isset($_SESSION['nama_guru']) ||
    !isset($_SESSION['role'])
) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit;
}

$page = $_GET['page'] ?? 'beranda';

switch ($page) {
    case 'beranda':
        $file = 'gr_beranda.php';
        break;
    case 'absensi':
        $file = 'modAbsensi/absensi.php';
        break;
    case 'riwayat':
        $file = 'modRiwayat/riwayat.php';
        break;
    case 'detail_riwayat':
        $file = 'modRiwayat/detail_riwayat.php';
        break;
    case 'rekap':
        if ($_SESSION['role'] == "wali") {
            $file = 'modRekap/rekap.php';
        } else {
            $file = 'gr_beranda.php';
        }
        break;
    default:
        $file = 'gr_beranda.php';
        break;
}

require 'gr_header.php';
require $file;
require 'gr_footer.php';
?>
