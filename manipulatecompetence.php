<?php
$language ="da";

#
# Open database connection -----------------------------------------------------
#
require 'PrepareDatabaseOperation.php';

#
# Store in database ------------------------------------------------------------
#
if (isset($_POST['findcompetence'])) {

   $preferredLabel = $_POST['preferredLabel'];
   dbExecute("SELECT prefferredLabel as preferredLabel, altLabels, defaultSearchPatterns, overriddenSearchPatterns, grp, conceptUri, _id FROM kompetence WHERE prefferredLabel LIKE '".$preferredLabel."' limit 1");
   $row = $GLOBALS["mysqliresult"]->fetch_array(MYSQLI_ASSOC);
   echo(stripslashes(json_encode($row, JSON_UNESCAPED_UNICODE)));

} elseif (isset($_POST['createcompetence'])) {

   $preferredLabel = $_POST['preferredLabel'];
   dbExecute("INSERT INTO kompetence (prefferredLabel, grp, conceptUri) VALUES ('".$preferredLabel."','Misc','Misc-".$preferredLabel."')");

} elseif (isset($_POST['updatecompetence'])) {

   $preferredLabel = $_POST['preferredLabel'];
   $altLabels = $_POST['altLabels'];
   $grp = $_POST['grp'];
   $conceptUri = $_POST['conceptUri'];
   $overriddenSearchPatterns = $_POST['overriddenSearchPatterns'];

   $query = "";
   if (!empty($altLabels)) {
      if (!empty($query))
      	 $query .= ", ";
      $query .= "altLabels='".$altLabels."'";
   }
   if (!empty($grp)) {
      if (!empty($query))
      	 $query .= ", ";
      $query .= "grp='".$grp."'";
   }
   if (!empty($conceptUri)) {
      if (!empty($query))
      	 $query .= ", ";
      $query .= "conceptUri='".$conceptUri."'";
   }
   if (!empty($overriddenSearchPatterns)) {
      if (!empty($query))
      	 $query .= ", ";
      $query .= "overriddenSearchPatterns='".$overriddenSearchPatterns."'";
   }
   $query = "UPDATE kompetence SET ".$query." WHERE prefferredLabel = '".$preferredLabel."'";
   dbExecute($query);

} elseif (isset($_POST['deletecompetence'])) {

   $preferredLabel = $_POST['preferredLabel'];
   dbExecute("SELECT _id FROM kompetence WHERE prefferredLabel = '".$preferredLabel."' limit 1");
   $row = $GLOBALS["mysqliresult"]->fetch_array(MYSQLI_ASSOC);
   $id = $row['_id'];

   dbExecute("CALL deleteCompetence(".$id.")");

} elseif (isset($_POST['mergecompetencies'])) {

   $remain_label = $_POST['remain_label'];
   dbExecute("SELECT _id FROM kompetence WHERE prefferredLabel = '".$remain_label."' limit 1");
   $row = $GLOBALS["mysqliresult"]->fetch_array(MYSQLI_ASSOC);
   $remain_id = $row['_id'];
   if (empty($remain_id)) {
      echo("Unknown remain label: ".$remain_label);
      die();
   }

   $remove_label = $_POST['remove_label'];
   dbExecute("SELECT _id FROM kompetence WHERE prefferredLabel = '".$remove_label."' limit 1");
   $row = $GLOBALS["mysqliresult"]->fetch_array(MYSQLI_ASSOC);
   $remove_id = $row['_id'];
   if (empty($remove_id)) {
      echo("Unknown remove label: ".$remove_label);
      die();
   }
   
   dbExecute("CALL mergeCompetencies(".$remain_id.",".$remove_id.")");

} elseif (isset($_POST['addCompetenceToCategory']) ||
           isset($_POST['removeCompetenceFromCategory'])) {

   $childCompetenceLabel = $_POST['childCompetenceLabel'];
   $parentCompetenceLabel = $_POST['parentCompetenceLabel'];
   if (empty($childCompetenceLabel)) {
      echo("Child label is empty.");
      die();      
   }
   if (empty($parentCompetenceLabel)) {
      echo("Parent label is empty.");
      die();      
   }  
     
   if (isset($_POST['addCompetenceToCategory'])) {
      dbExecute('insert ignore into kompetence_kategorisering (superkompetence, subkompetence) select sup.conceptUri superkompetence, sub.conceptUri subkompetence from kompetence sup JOIN kompetence sub ON sup.prefferredLabel="'.$parentCompetenceLabel.'" AND sub.prefferredLabel="'.$childCompetenceLabel.'" ');    		      	  
   } elseif (isset($_POST['removeCompetenceFromCategory'])) {
      dbExecute('delete from kompetence_kategorisering where superkompetence in (select sup.conceptUri superkompetence from kompetence sup WHERE sup.prefferredLabel="'.$parentCompetenceLabel.'") AND subkompetence in (select sub.conceptUri subkompetence from kompetence sub where sub.prefferredLabel="'.$childCompetenceLabel.'")');    		      	 
   }

} else {

   echo("Unknown command to manipulate competence.");
   die();        

}


#
# Close database connection ----------------------------------------------------
#
require 'FinalizeDatabaseOperation.php';


?>
