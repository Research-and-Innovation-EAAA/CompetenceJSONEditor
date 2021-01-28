<?php

require_once 'vendor/autoload.php';
require_once 'credentials.php';

$language ="da";

#Connecting to the database and loading competences ------------------------------------------------------------
$GLOBALS["mysqli"] = new mysqli($host, $user, $password, $db, $port, $language) or die("failed" . mysqli_error());
#changing character set to utf8
$charset = $GLOBALS["mysqli"]->character_set_name();#Returns the character set for the database connection
if (!$GLOBALS["mysqli"]->set_charset("utf8mb4")) { //Checking if the character set is set to UTF-8 if it is false it will show printf
    printf("<p>Error loading character set utf8: %s </p>\n", $GLOBALS["mysqli"]->error());
    exit();
}
$GLOBALS["mysqli"]->autocommit(true);

// Free result
function dbFreeResult() {
    if (isset($GLOBALS["mysqliresult"])) {
       if (method_exists($GLOBALS["mysqliresult"], "free")) {
          $GLOBALS["mysqliresult"]->free();
       }
       unset($GLOBALS["mysqliresult"]);
    }
}

// Close connection
function dbCloseConnection() {
    if (isset($GLOBALS["mysqli"])) {
       $GLOBALS["mysqli"]->close();	
       unset($GLOBALS["mysqli"]);
    }
}

// Database query or update
function dbExecute($sqlQuery) {
    dbFreeResult();
    $GLOBALS["mysqliresult"] = $GLOBALS["mysqli"]->query($sqlQuery);
    if (!$GLOBALS["mysqliresult"]) {
       echo("DB error '".$GLOBALS["mysqliresult"]."' while running '".$sqlQuery."' \n");
       echo("Result: ".$GLOBALS["mysqliresult"].", Error: ".$GLOBALS["mysqli"]->errno.", ".$GLOBALS["mysqli"]->error.", ".$GLOBALS["mysqli"]->sqlstate);
       die();
    }
}


?>
