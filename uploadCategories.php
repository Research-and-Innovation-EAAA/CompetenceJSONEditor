<?php

require 'vendor/autoload.php';

$language ="da";

use PhpOffice\PhpSpreadsheet\IOFactory;
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["competenceCategoriesFile"]["name"]);
$inputFileType = 'Xlsx';
$inputFileName = $_FILES["competenceCategoriesFile"]["tmp_name"];

// Create a new Reader of the type defined in $inputFileType
$reader = IOFactory::createReader($inputFileType);

// Load $inputFileName to a PhpSpreadsheet Object
$spreadsheet = $reader->load($inputFileName);

// Read the document's creator property
$creator = $spreadsheet->getProperties()->getCreator();

// Read the Date when the workbook was created (as a PHP timestamp value)
$creationDatestamp = $spreadsheet->getProperties()->getCreated();

// Format the date and time using the standard PHP date() function
$creationDate = date('l, d<\s\up>S</\s\up> F Y', $creationDatestamp);
$creationTime = date('g:i A', $creationDatestamp);
echo('<p><b>Created On: </b>' . $creationDate . ' at ' . $creationTime.'</p>');

// Read the name of the last person to modify this workbook
$modifiedBy = $spreadsheet->getProperties()->getLastModifiedBy();
echo('<p><b>Last Modified By: </b>' . $modifiedBy.'</p>');

// Read the Date when the workbook was last modified (as a PHP timestamp value)
$modifiedDatestamp = $spreadsheet->getProperties()->getModified();

// Format the date and time using the standard PHP date() function
$modifiedDate = date('l, d<\s\up>S</\s\up> F Y', $modifiedDatestamp);
$modifiedTime = date('g:i A', $modifiedDatestamp);
echo('<p><b>Last Modified On: </b>' . $modifiedDate . ' at ' . $modifiedTime.'</p>');

// Read the workbook title property
$workbookTitle = $spreadsheet->getProperties()->getTitle();
echo('<p><b>Title: </b>' . $workbookTitle.'</p>');

// Read the workbook description property
$description = $spreadsheet->getProperties()->getDescription();
echo('<p><b>Description: </b>' . $description.'</p>');

// Read the workbook subject property
$subject = $spreadsheet->getProperties()->getSubject();
echo('<p><b>Subject: </b>' . $subject.'</p>');

// Read the workbook keywords property
$keywords = $spreadsheet->getProperties()->getKeywords();
echo('<p><b>Keywords: </b>' . $keywords.'</p>');

// Read the workbook category property
$category = $spreadsheet->getProperties()->getCategory();
echo('<p><b>Category: </b>' . $category.'</p>');

// Read the workbook company property
$company = $spreadsheet->getProperties()->getCompany();
echo('<p><b>Company: </b>' . $company.'</p>');

// Read the workbook manager property
$manager = $spreadsheet->getProperties()->getManager();
echo('<p><b>Manager: </b>' . $manager.'</p>');
$s = new \PhpOffice\PhpSpreadsheet\Helper\Sample();

// Output sheet data
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

#
# Open database connection -----------------------------------------------------
#
require 'PrepareDatabaseOperation.php';

#
# Store in database
#
$i = 0;
foreach ($sheetData as $row) {
    echo "<p>";
    print_r($row);
    echo "</p>";

    $childLabels = $row["A"];
    list($childLabel, $childAltLabels) = explode('/', $childLabels, 2);
    $connection = $row["B"];
    $parentLabel = $row["C"];
    $parentGrp = $row["D"];
    $childConceptUri = $parentLabel.'-'.$childLabel;

    $i++;
    if ($i==1) {
        if ($childLabels != "childLabels" ||
            $connection != "connection" ||
            $parentLabel != "parentLabel" ||
            $parentGrp != "parentGrp") {
            echo "<p>Bad header names in line 1</p>";
            exit;
        }
        continue;
    } 

    if (empty($childLabel) || empty($connection)) {
        continue;
    }

    if ($connection == "Deleted") {
	dbExecute("SELECT _id FROM kompetence WHERE prefferredLabel = '".$childLabel."' limit 1");
   	$row = $GLOBALS["mysqliresult"]->fetch_array(MYSQLI_ASSOC);
   	$id = $row['_id'];
   	dbExecute("CALL deleteCompetence(".$id.")");
    } else if ($connection == "Added") {
        dbExecute('insert ignore into kompetence (prefferredLabel, altLabels, conceptUri, grp) values ("'.$childLabel.'","'.$childAltLabels.'","'.$childConceptUri.'","")');
        if (empty($parentLabel)) {
          continue;
        }
        dbExecute('insert ignore into kompetence (prefferredLabel, altLabels, conceptUri, grp) values ("'.$parentLabel.'","","'.$parentLabel.'","'.$parentGrp.'")');
        dbExecute('insert ignore into kompetence_kategorisering (superkompetence, subkompetence) select sup.conceptUri superkompetence, sub.conceptUri subkompetence from kompetence sup JOIN kompetence sub ON sup.prefferredLabel="'.$parentLabel.'" AND sub.prefferredLabel="'.$childLabel.'" ');    		      	 
    } else if ($connection == "Removed") {
        if (empty($parentLabel)) {
          continue;
        }
        dbExecute('delete ignore from kompetence_kategorisering where superkompetence in (select sup.conceptUri superkompetence from kompetence sup WHERE sup.prefferredLabel="'.$parentLabel.'") AND subkompetence in (select sub.conceptUri subkompetence from kompetence sub where sub.prefferredLabel="'.$childLabel.'")');    		      	 
    }
}

#
# Close database connection ----------------------------------------------------
#
require 'FinalizeDatabaseOperation.php';

#
# End script                ----------------------------------------------------
#
echo "<p><b>Success, database updated</b></p>";


?>

<a href="index.php">Go back</a>
