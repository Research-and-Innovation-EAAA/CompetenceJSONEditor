<?php
$json="";
$row="";
$host = "ENTER DATABASE IP OR DOMAIN NAME";
$user = "ENTER DATABASE USER";
$password = "ENTER DATABASE PASSWORD";
$db = "ENTER DATABASE PASSWORD";
$port = "ENTER DATABASE PORT";
$language ="da";
#/*Connecting to the database and loading competences*/
$connect=new mysqli($host,$user,$password,$db,$port,$language) or die("failed" . mysqli_error());## for getting the JSON
#Connecting to the database and loading competences ------------------------------------------------------------
$connect = new mysqli($host, $user, $password, $db, $port, $language) or die("failed" . mysqli_error());  ###for updating the JSON
#changing character set to utf8
mysqli_character_set_name($connect);
if (!mysqli_set_charset($connect, "utf8mb4")) {
    printf("Error loading character set utf8: %s\n", mysqli_error($connect));
    exit();
}
$sql = mysqli_query($connect, "select shinyTreeJSON from global where _id = 1");
$row = mysqli_fetch_array($sql);


#Save competence and full json -----------------------------------------------------------------------------------
if (isset($_POST['service'])) {
    #JSON_UNESCAPED_UNICODE for danish letters
    $json = json_encode($_POST['service'], JSON_UNESCAPED_UNICODE);
    $connect = new mysqli($host, $user, $password, $db, $port, $language) or die("failed" . mysqli_error());
    #/*changing character set to utf8 */OK
    mysqli_character_set_name($connect);
    if (!mysqli_set_charset($connect, "utf8mb4")) {
        printf("Error loading character set utf8: %s\n", mysqli_error($connect));
        exit();
    }
    $sql = mysqli_query($connect, "update global set shinyTreeJSON= '$json'  where _id = 1");
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="album.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">

    <title>JSONEditor | Competence Administration</title>

    <link href="node_modules/jsoneditor/dist/jsoneditor.css" rel="stylesheet" type="text/css">
    <script src="node_modules/jsoneditor/dist/jsoneditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
    <script src="mdb.js"></script>
    <link href="mdb.css" rel="stylesheet" type="text/css">


</head>

<body>
<div class="jumbotron text-center title">
    <h4>Competence Administration tool</h4>
    <h5 class="text-muted">Research and innovation at Business Academy Aarhus maintain a database with competences. Below you will find a tool that allows you to administrate the competences. Save is needed after every competence change.</h5>
    <div>
        <button type="button" class="btn btn-primary" id="LoadJson">Load full json</button>
    </div>
</div>
<br>

<div>
    <div class="container">
        <div class="alert alert-success alert-dismissable" id="successalert"></div>
        <div class="alert alert-danger alert-dismissable" id="dangeralert"></div>
        <div class="row">
            <div class="col-sm-4">
                <h2 class="list-group-item list-group-item-action active">Select Competence to Edit</h2>
                <div class="right-menu">

                    <div class="treeview-animated">
                        <ul id="CompetenceList" class="treeview-animated-list mb-3">
                        </ul>
                    </div>


                </div>
            </div>
            <br>
            <div class="jsoneditor-div">
                <div id="jsoneditor"></div>
                <button type="button" class="btn btn-success" id="SaveCompetence">Save</button>
                <button type="button" class="btn btn-success" id="SaveJson">Save</button>

            </div>

        </div>
        <footer>
            <p>Copyright &copy; 2019 Research and Innovation, Business Academy Aarhus</p>
        </footer>

</body>
<script>
    // create the editor ------------------------------------------------------------------------------------------------
    $(document).ready(function () {
        $('.treeview-animated').mdbTreeview(); //initializing the list with the competences
    });
    const container = document.getElementById('jsoneditor');
    const options = {
        mode: 'tree',
        modes: ['code', 'form', 'text', 'tree', 'view', 'preview'], // allowed modes
        onError: function (err) {
            $('#dangeralert').html("<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>\n" + err.toString()).show();

        },
        onModeChange: function (newMode, oldMode) {
            if (newMode === "code")
                editor.aceEditor.setOptions({maxLines:50});

            console.log('Mode switched from', oldMode, 'to', newMode)
        }
    };
    const editor = new JSONEditor(container, options);

    //creating variables for making a list, loading and saving json ------------------------------------------------
    var myjson = {};
    var jsorbj = <?php echo $row[0] ?> ;
    //console.log(typeof jsorbj);
    var editorstate = 0;              // hiding the save buttons
   // console.log(typeof editorstate);
    var listEl = document.getElementById('CompetenceList');
    document.getElementById("SaveCompetence").style.display = "none";
    document.getElementById("SaveJson").style.display = "none";
    console.log(typeof listEl);


    //making a list function with the design -----------------------------------------------------------------------
    function makeList(jsonObject, listElement) {
        for (var i in jsonObject) {
          //  console.log('List the object',typeof jsonObject[i]);
            //console.log(typeof i);
            var newLi = listElement; // Creating a new variable "newLi" and assigning a value "listElement".
            if (typeof jsonObject[i] === 'string') {  //Checking if the object type is equal to string.

            }
            else if ((jsonObject[i] instanceof Object) && (jsonObject[i]["text"] !== undefined)) {         // Checking if it is an object and if it has the “text” property
                 if ((jsonObject[i]["children"] !== undefined) && (jsonObject[i]["children"].length !== 0)) {           //making the HTML if the competence has children (Checking if "children" is not equal to undefined and "children" length is not equal to 0
                    newLi = document.createElement('li');
                    newLi.className = "treeview-animated-items"; // creating new Li object with a property class name and assigning a value "treeview-animated-items"
                    listElement.appendChild(newLi);
                    var newa = document.createElement('a');
                    newdiv = document.createElement('div');
                    newdiv.className = "jsondiv";
                    newa.className = "closed";
                    json = jsonObject[i];
                    button = document.createElement('button');
                    button.innerText = "Edit";
                    button.type = "button";
                    button.className = "jsonbutton btn btn-outline-primary d-flex  btn-sm";
                    button.addEventListener('click', (function (inner_json) {
                        return function () {
                            loadjsonpart(inner_json);
                        }
                    }(json)), false);
                    newdiv.innerHTML = jsonObject[i]["text"];  // Accessing the text of the key of the object
                    newdiv.appendChild(button);
                    newa.appendChild(newdiv);+-
                    newLi.appendChild(newa);

                    var newUL = newLi.appendChild(document.createElement('ul'));
                    newUL.className = "nested";

                }
                else {                                                               //making the HTML if the competence does not have children
                    newLi = document.createElement('li');
                    listElement.appendChild(newLi);
                    var newdiv = document.createElement('div');
                    newdiv.className = "treeview-animated-element jsondiv";
                    var json = jsonObject[i];
                    var button = document.createElement('button');
                    button.innerText = "Edit";
                    button.type = "button";
                    button.className = "jsonbutton btn btn-outline-primary d-flex  btn-sm";
                    button.addEventListener('click', (function (inner_json) {
                        return function () {
                            loadjsonpart(inner_json);
                        }
                    }(json)), false);
                    newdiv.innerHTML = jsonObject[i]["text"];
                    newdiv.appendChild(button);
                    newLi.appendChild(newdiv);
                    newLi.appendChild(newdiv);
                    listElement.appendChild(newLi);

                }
                makeList(jsonObject[i], newUL);
            }
            else {
                makeList(jsonObject[i], newLi);
            }
        }

    }
    makeList(jsorbj, listEl);

    //Adding functionality to the buttons ------------------------------------------------------------------------
    //save part of json
    document.getElementById('SaveCompetence').onclick = function () {
        var editedjson = JSON.stringify(editor.get());               //converts the value to a JSON string
        var oldjson = JSON.stringify(myjson);
        var originaljson = JSON.stringify(jsorbj);
        var sendingjson = originaljson.replace(oldjson, editedjson);

        sendingjson = JSON.parse(sendingjson);
        $.ajax({                                                                    //Converting the object from Javascript to PHP
            type: 'post',
            url: 'index.php',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            data: {service: sendingjson},
            success: function (msh) {
                $('#successalert').html("<button type=\"button\" class=\"close\" onclick=\"$('.alert').hide()\" aria-hidden=\"true\">&times;</button>\n Competence successfully edited").show();
            },
            error: function (msg) {
                $('#dangeralert').html("<button type=\"button\" class=\"close\" onclick=\"$('.alert').hide()\" aria-hidden=\"true\">&times;</button>\n Competence unsuccessfully edited").show();

            }
        });

    };
    // load json
    document.getElementById('LoadJson').onclick = function () {
        //editor.set(json)
        editor.set(<?php echo $row[0] ?>);
        editorstate = 1;
        showbutton(editorstate);
    };

    // save json
    document.getElementById('SaveJson').onclick = function () {
        myjson = editor.get();
        //  function that update the sql json file on database over php function with the myjson.json
        $.ajax({
            type: 'post',
            url: 'index.php',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            data: {service: myjson},
            success: function (msh) {
                $('#successalert').html("<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>\n Json successfully edited").show();
            },
            error: function (msg) {
                $('#dangeralert').html("<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>\n Json unsuccessfully edited").show();
            }
        });
    };

    //Helper function ------------------------------------------------------------------------------------------------
    function loadjsonpart(json) {
        editor.set(json);
        myjson = json;
        editorstate = 2;
        showbutton(editorstate);
    }
    function showbutton(state){

        if (state === 1)       // If  the Load full JSON button is pressed it will display the SaveJson button and hide the SaveCompetence button
        {
            document.getElementById("SaveJson").style.display = "block";
            document.getElementById("SaveCompetence").style.display = "none";
        }
        else if (state === 2){      // If  the Edit button is pressed it will display the SaveCompetence button and hide the SaveJson button
            document.getElementById("SaveJson").style.display = "none";
            document.getElementById("SaveCompetence").style.display = "block";
        }
    }
</script>
</html>
