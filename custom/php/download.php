<?php
require_once("custom/php/common.php");
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$filename = "resultado.xlsx";
$writer->save($filename);
//echo basename($file)."<br>";
//            echo file_exists("./custom/php/$file")."<br>";
//echo "HERE1<br>";
//clearstatcache();
//Check the file exists or not
if (file_exists($filename)) {

//Define header information
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: 0");
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filename));
    header('Pragma: public');

//Clear system output buffer
    flush();

//Read the size of the file
    readfile($filename,true);

    die();
    echo "HERE<br>";
}
