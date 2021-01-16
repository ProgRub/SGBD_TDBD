<?php
require_once("custom/php/common.php");
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$filename = "resultado.xlsx";
header("Pragma: public");
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Length: ' . filesize($filename));
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
ob_clean();
flush();
$writer->save("php://output");
//echo basename($file)."<br>";
//            echo file_exists("./custom/php/$file")."<br>";
//echo "HERE1<br>";
//clearstatcache();
//Check the file exists or not
if (file_exists($filename)) {
//    echo basename($filename)."<br>HERE<br>";

//Define header information
//    header('Content-Description: File Transfer');
//    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//    header("Cache-Control: no-cache, must-revalidate");
//    header("Expires: 0");
//    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
//    header('Content-Length: ' . filesize($filename));
//    header('Pragma: public');
    ob_clean();
    flush();
    readfile($filename);
//Clear system output buffer
//    flush();
//
////Read the size of the file
//    echo readfile(basename($filename));

    die();
}
