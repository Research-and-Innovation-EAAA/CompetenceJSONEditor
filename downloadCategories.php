<?php

require 'vendor/autoload.php';
require 'credentials.php';

$language ="da";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


#Connecting to the database and loading competences ------------------------------------------------------------
$mysqli = new mysqli($host, $user, $password, $db, $port, $language) or die("failed" . mysqli_error());
#changing character set to utf8
$charset = $mysqli->character_set_name();#Returns the character set for the database connection
if (!$mysqli->set_charset("utf8mb4")) { //Checking if the character set is set to UTF-8 if it is false it will show printf
    printf("Error loading character set utf8: %s\n", $mysqli->error());
    exit();
}
$mysqli->autocommit(true);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
//$sheet = $spreadsheet->setActiveSheetIndex(0);

// Set document properties
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setLastModifiedBy('Maarten Balliauw')
    ->setTitle('Office 2007 XLSX Test Document')
    ->setSubject('Office 2007 XLSX Test Document')
    ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
    ->setKeywords('office 2007 openxml php')
    ->setCategory('Test result file');

$sqlQuery = 'SELECT CASE sub.altLabels WHEN NULL THEN sub.prefferredLabel WHEN "" THEN sub.prefferredLabel ELSE CONCAT(sub.prefferredLabel, "/", sub.altLabels) END childLabels, "Added" connection, sup.prefferredLabel parentLabel, sup.grp parentGrp FROM `kompetence` sub LEFT JOIN kompetence_kategorisering kat ON sub.conceptUri=kat.subkompetence JOIN kompetence sup ON kat.superkompetence=sup.conceptUri where CASE sub.altLabels WHEN NULL THEN sub.prefferredLabel WHEN "" THEN sub.prefferredLabel ELSE CONCAT(sub.prefferredLabel, "/", sub.altLabels) END is not null';

/* Select queries return a resultset */
$sheet = $spreadsheet->setActiveSheetIndex(0);
$i = 1;
$sheet->setCellValueByColumnAndRow(1, $i, "childLabels");
$sheet->setCellValueByColumnAndRow(2, $i, "connection");
$sheet->setCellValueByColumnAndRow(3, $i, "parentLabel");
$sheet->setCellValueByColumnAndRow(4, $i, "parentGrp");
if ($result = $mysqli->query($sqlQuery)) {
    while($row = $result->fetch_object()) {
    	       $i++;
	       $sheet->setCellValueByColumnAndRow(1, $i, $row->childLabels);
	       $sheet->setCellValueByColumnAndRow(2, $i, $row->connection);
	       $sheet->setCellValueByColumnAndRow(3, $i, $row->parentLabel);
	       $sheet->setCellValueByColumnAndRow(4, $i, $row->parentGrp);
    }
}
$result->close();
$sheet->setAutoFilter('A1:D'.$i);
$sheet->getColumnDimension('A')->setWidth(40);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(15);

$mysqli->close();

// Redirect output to a client s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="competence_categories.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
