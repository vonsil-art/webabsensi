<?php

include "cek_sesi.php";

$page = $_GET['page'] ?? 'beranda';
switch ($page) {
    case 'beranda':
        $file = 'ad_beranda.php';
        break;
    case 'guru':
        $file = 'modGuru/dt_guru.php';
        break;
    case 'tambahGuru':
        $file = 'modGuru/tambahGuru.php';
        break;
    case 'ubahGuru':
        $file = 'modGuru/ubahGuru.php';
        break;
    case 'hapusGuru':
        $file = 'modGuru/hapusGuru.php';
        break;
    case 'kelas':
        $file = 'modKelas/dt_kelas.php';
        break;
    case 'tambahKelas':
        $file = 'modKelas/tambahKelas.php';
        break;
    case 'ubahKelas':
        $file = 'modKelas/ubahKelas.php';
        break;
    case 'hapusKelas':
        $file = 'modKelas/hapusKelas.php';
        break;
    case 'siswa':
        $file = 'modSiswa/dt_siswa.php';
        break;
    case 'tambahSiswa':
        $file = 'modSiswa/tambahSiswa.php';
        break;
    case 'ubahSiswa':
        $file = 'modSiswa/ubahSiswa.php';
        break;
    case 'hapusSiswa':
        $file = 'modSiswa/hapusSiswa.php';
        break;
    case 'imporSiswa':
        $file = 'modSiswa/imporSiswa.php';
        break;
    case 'logout':

    session_unset();
    session_destroy();
    header("Location: ../login_admin.php");
        
    exit;

    default:
    
        $file = 'ad_beranda.php';
        break;

}
require 'ad_header.php';
require $file;
require 'ad_footer.php';

?>
