<?php

require 'vendor/autoload.php';
require 'credentials.php';

$language ="da";

use PhpOffice\PhpSpreadsheet\IOFactory;

// require __DIR__ . '/../Header.php';


$target_dir = "uploads/";
// $target_dir = "/tmp/";
$target_file = $target_dir . basename($_FILES["competenceCategoriesFile"]["name"]);
// $uploadOk = 1;
// $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// echo 'target_file=';
// // print_r(var_dump($target_file));
// echo '<br/>';

// Check if image file is a actual image or fake image
// if(isset($_POST["submit"])) {
//   $check = print_r($_FILES["competenceCategoriesFile"]["tmp_name"]."\n");
//   $check = getimagesize($_FILES["competenceCategoriesFile"]["tmp_name"]);
//   if($check !== false) {
//     echo "File is an image - " . $check["mime"] . ".";
//     // $uploadOk = 1;
//   } else {
//     echo "File is not an image.";
//     // $uploadOk = 0;
//   }
// }


// move_uploaded_file($_FILES["competenceCategoriesFile"]["tmp_name"], $target_file);


$inputFileType = 'Xlsx';

// $inputFileName = '/var/www/html/ResearchFrontWebsite/CompetenceJSONEditor/vendor/phpoffice/phpspreadsheet/samples/Reading_workbook_data/sampleData/example1.xls';
$inputFileName = $_FILES["competenceCategoriesFile"]["tmp_name"];
// echo 'inputFileName=';
// print_r(var_dump($inputFileName));
// echo '<br/>';

// Create a new Reader of the type defined in $inputFileType
$reader = IOFactory::createReader($inputFileType);
// echo 'creator=';
// print_r(var_dump($reader));
// echo '<br/>';
// Load $inputFileName to a PhpSpreadsheet Object
$spreadsheet = $reader->load($inputFileName);

// Read the document's creator property
$creator = $spreadsheet->getProperties()->getCreator();
// echo 'creator=';
// print_r(var_dump($creator));
// echo '<br/>';
// echo('<b>Document Creator: </b>' . $creator);

// Read the Date when the workbook was created (as a PHP timestamp value)
$creationDatestamp = $spreadsheet->getProperties()->getCreated();
// Format the date and time using the standard PHP date() function
$creationDate = date('l, d<\s\up>S</\s\up> F Y', $creationDatestamp);
$creationTime = date('g:i A', $creationDatestamp);
echo('<b>Created On: </b>' . $creationDate . ' at ' . $creationTime);

// Read the name of the last person to modify this workbook
$modifiedBy = $spreadsheet->getProperties()->getLastModifiedBy();
echo('<b>Last Modified By: </b>' . $modifiedBy);

// Read the Date when the workbook was last modified (as a PHP timestamp value)
$modifiedDatestamp = $spreadsheet->getProperties()->getModified();
// Format the date and time using the standard PHP date() function
$modifiedDate = date('l, d<\s\up>S</\s\up> F Y', $modifiedDatestamp);
$modifiedTime = date('g:i A', $modifiedDatestamp);
echo('<b>Last Modified On: </b>' . $modifiedDate . ' at ' . $modifiedTime);

// Read the workbook title property
$workbookTitle = $spreadsheet->getProperties()->getTitle();
echo('<b>Title: </b>' . $workbookTitle);

// Read the workbook description property
$description = $spreadsheet->getProperties()->getDescription();
echo('<b>Description: </b>' . $description);

// Read the workbook subject property
$subject = $spreadsheet->getProperties()->getSubject();
echo('<b>Subject: </b>' . $subject);

// Read the workbook keywords property
$keywords = $spreadsheet->getProperties()->getKeywords();
echo('<b>Keywords: </b>' . $keywords);

// Read the workbook category property
$category = $spreadsheet->getProperties()->getCategory();
echo('<b>Category: </b>' . $category);

// Read the workbook company property
$company = $spreadsheet->getProperties()->getCompany();
echo('<b>Company: </b>' . $company);

// Read the workbook manager property
$manager = $spreadsheet->getProperties()->getManager();
echo('<b>Manager: </b>' . $manager);
$s = new \PhpOffice\PhpSpreadsheet\Helper\Sample();

// Output sheet data
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
//var_dump($sheetData);

#Connecting to the database and loading competences ------------------------------------------------------------
$mysqli = new mysqli($host, $user, $password, $db, $port, $language) or die("failed" . mysqli_error());
#changing character set to utf8
$charset = $mysqli->character_set_name();#Returns the character set for the database connection
if (!$mysqli->set_charset("utf8mb4")) { //Checking if the character set is set to UTF-8 if it is false it will show printf
    printf("Error loading character set utf8: %s\n", $mysqli->error());
    exit();
}
$mysqli->autocommit(true);

// Database query
function dbExecuteDML($mysqli, $sqlQuery) {
    echo('<br/>'.$sqlQuery);
    $result = $mysqli->query($sqlQuery);
    // if ($mysqli->warning_count()) {
    //    $e = $mysqli->get_warnings();
    //    do {
    //    	  echo "Warning: $e->errno, $e->message\n";
    //    } while ($e->next());
    // }
    if (!$result) {
       printf("Result: %d, Error: %d, %s, %s", $result, $mysqli->errno(), $mysqli->error(), $mysqli->sqlstate());
       die("DB error '".$result."' while running '".$sqlQuery."'");
    }
}

// Store in database
foreach ($sheetData as $row) {
    $labels = $row["A"];
    list($prefferredLabel, $altLabels) = explode('/', $labels, 2);
    $parent = $row["B"];
    $subConceptUri = $parent.'-'.$prefferredLabel;

    dbExecuteDML($mysqli, 'insert ignore into kompetence (prefferredLabel, altLabels, conceptUri, grp) values ("'.$prefferredLabel.'","'.$altLabels.'","'.$subConceptUri.'","")');

    dbExecuteDML($mysqli, 'insert ignore into kompetence (prefferredLabel, altLabels, conceptUri, grp) values ("'.$parent.'","","'.$parent.'","")');

    dbExecuteDML($mysqli, 'insert ignore into kompetence_kategorisering (superkompetence, subkompetence) select sup.conceptUri superkompetence, sub.conceptUri subkompetence from kompetence sup JOIN kompetence sub ON sup.prefferredLabel="'.$parent.'" AND sub.prefferredLabel="'.$prefferredLabel.'" ');    		      	 

    // echo ($prefferredLabel." ".$altLabels." ".$parent."<br/>");
}

$mysqli->close();
?>
