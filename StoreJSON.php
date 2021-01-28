<?php

$language ="da";

#
# Open database connection -----------------------------------------------------
#
require 'PrepareDatabaseOperation.php';


#
# Store json -------------------------------------------------------------------
#
if (isset($_POST['newJSON'])) {

    #JSON_UNESCAPED_UNICODE for danish letters
    $json = json_decode($_POST['newJSON'], JSON_UNESCAPED_UNICODE);

    dbExecute("insert into global (shinyTreeJSON) select shinyTreeJSON from global where _id = 1");
    dbExecute("update global set shinyTreeJSON='".$json."' where _id = 1");
}


#
# Close database connection ----------------------------------------------------
#
require 'FinalizeDatabaseOperation.php';


?>
