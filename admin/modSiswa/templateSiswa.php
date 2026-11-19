<?php

// Pastikan TIDAK ADA spasi kosong atau baris baru di atas tag <?php ini

require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Data Siswa');

// Header
$sheet->setCellValue('A1', 'NIS');
$sheet->setCellValue('B1', 'Nama Siswa');
$sheet->setCellValue('C1', 'Kelas');

// Contoh data
$sheet->setCellValueExplicit(
    'A2',
    '123456',
    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
);

$sheet->setCellValue('B2', 'Contoh Nama Siswa');
$sheet->setCellValue('C2', 'Banin 1');

// Lebar kolom
$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(20);

// Format header
$sheet->getStyle('A1:C1')->getFont()->setBold(true);

$sheet->getStyle('A1:C1')->getFill()
    ->setFillType(
        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
    )
    ->getStartColor()->setRGB('D9EAF7');

// Download
$filename = 'Template_Data_Siswa.xlsx';

// --- TAMBAHKAN BARIS INI UNTUK MEMBERSIHKAN OUTPUT TERSEMBUNYI ---
if (ob_get_length()) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
