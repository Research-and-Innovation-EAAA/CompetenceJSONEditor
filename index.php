<?php
$json="";
$row="";
require 'credentials.php';
$language ="da";

#Connecting to the database and loading competences ------------------------------------------------------------
$connect = new mysqli($host, $user, $password, $db, $port, $language) or die("failed" . mysqli_error());
#changing character set to utf8
mysqli_character_set_name($connect);#Returns the character set for the database connection
if (!mysqli_set_charset($connect, "utf8mb4")) { //Checking if the character set is set to UTF-8 if it is false it will show printf
    printf("Error loading character set utf8: %s\n", mysqli_error($connect));
    exit();
}
$sql = mysqli_query($connect, "select shinyTreeJSON from global where _id = 1");
$row = mysqli_fetch_array($sql);//Returns an array that corresponds to the fetched row in this case the result from the query


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
    <h1>Competence administration tool</h1>
    <h5 class="text-muted">Research and innovation at Business Academy Aarhus maintain a database with competences.<br/>Below you will find a tool that allows you to administrate the competences and their categorization.</h5>
</div>
<br>

<div class="text-center title container">
<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="tree-tab" data-toggle="tab" href="#tree" role="tab" aria-controls="tree"
      aria-selected="false">Tree</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="categories-tab" data-toggle="tab" href="#categories" role="tab" aria-controls="categories"
      aria-selected="false">Categories</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="competencies-tab" data-toggle="tab" href="#competencies" role="tab" aria-controls="competencies"
      aria-selected="true">Competencies</a>
  </li>
</ul>

<div class="tab-content container border-left border-bottom border-right" id="myTabContent">
  <br/>
  <form id="StoreJSONform" name="StoreJSON" id="StoreJSONform" method="post" action="StoreJSON.php" style="display:none;">
      <input id="newJSONinput" name="newJSON" />
      <button type="submit" id="StoreJSONformSubmit" name="StoreJSONformSubmit">Update JSON tree in database</button>
  </form>
  <div class="tab-pane fade show active" id="tree" role="tabpanel" aria-labelledby="tree-tab">

    <div class="row">
      <div class="col-sm-4">
          <button type="button" class="btn btn-block btn-lg btn-warning" id="LoadJson">Load from database</button>
	<br/>
	<div class="right-menu">

	  <div class="treeview-animated">
	    <ul id="CompetenceList" class="treeview-animated-list mb-3">
	    </ul>
	  </div>


	</div>
      </div>
      <br>
      <div class="jsoneditor-div">
	<div class="container">
	  <div id="jsoneditor"></div>
	  <br/>
	  <button type="button" class="btn btn-block btn-danger" id="SaveJson">Save to database</button>
	</div>
      </div>

    </div>
  </div>
  <div class="tab-pane fade" id="categories" role="tabpanel" aria-labelledby="categories-tab">
        <br/>
	<p> Download Excel file with database existing competences and categories:</p>
	      <form action="downloadCategories.php" method="get" enctype="multipart/form-data">
	      	    <button class="btn btn-primary btn-block my-4" type="submit" name="submit">Download current Excel file</button>
	      </form>
        <br/>
        <br/>
	      <hr/>

        <br/>
        <br/>
	<p>      Select Excel document to update competence category content:</p>


      <form action="uploadCategories.php" method="post" enctype="multipart/form-data">
	<div class="input-group">
	  <div class="input-group-prepend">
	    <span class="input-group-text" id="competenceCategoriesFile">Upload</span>
	  </div>
	  <div class="custom-file">
	    <input type="file" class="custom-file-input" id="competenceCategoriesFile" name="competenceCategoriesFile"
		   aria-describedby="competenceCategoriesFile">
	    <label class="custom-file-label" for="competenceCategoriesFile">Choose new file</label>
	  </div>
	  
	</div>
	<!-- Upload Excel file and update competences and categories -->
	<button class="btn btn-danger btn-block my-4" type="submit" name="submit">Update database by Excel file</button>
      </form>
  </div>
  <div class="tab-pane fade" id="competencies" role="tabpanel" aria-labelledby="competencies-tab">
        <br/>
	<h4>Load competence</h4>

	      <form id="loadcompetenceform" action="manipulatecompetence.php" method="post" enctype="multipart/form-data" style="text-align: left">
	      	    <div class="form-group row">
		    	 <label class="col-form-label col-sm-3" for="preferredLabel">Label</label>
			 <div class="col-sm-9">
			      <input type="text" class="form-control" name="preferredLabel">
			 </div>
		    </div>
	      	    <div class="form-group row">
		    <div class="btn-group container">
	      	       <button class="btn  btn-warning" type="submit" name="findcompetence" id="loadcompetenceformfindcompetence" style="margin: 0 6px 0 0;">Find existing</button>
    		       <button class="btn  btn-warning" type="submit" name="createcompetence" style="margin: 0 0 0 6px;">Create new</button>
                    </div>
		    </div>
	      </form>
	      
        <br/>



        <br/>

	      <hr/>

        <br/>
        <br/>
	<h4>Update competence</h4>

      <form id="updatecompetenceform" action="manipulatecompetence.php" method="post" enctype="multipart/form-data"  style="text-align: left">
      	<div class="form-group row">
	     <label class="col-form-label col-sm-3" for="preferredLabel">Label</label>
	     <div class="col-sm-9">
	     	  <input type="text" class="form-control" name="preferredLabel">
	     </div>
	</div>
      	<div class="form-group row">
	     <label class="col-form-label col-sm-3" for="altLabels">Alternatives ( split by / )</label>
	     <div class="col-sm-9">
	     	  <input type="text" class="form-control" id="altLabels" name="altLabels">
	     </div>
	</div>
      	<div class="form-group row">
	     <label class="col-form-label col-sm-3" for="grp">Group</label>
	     <div class="col-sm-9">
	     	  <input type="text" class="form-control" id="grp" name="grp">
	     </div>
	</div>
      	<div class="form-group row">
	     <label class="col-form-label col-sm-3" for="conceptUri">Concept URI</label>
	     <div class="col-sm-9">
	     	  <input type="text" class="form-control" id="conceptUri" name="conceptUri">
	     </div>
	</div>
      	<div class="form-group row">
	     <label class="col-form-label col-sm-3" for="overriddenSearchPatterns">Search pattern</label>
	     <div class="col-sm-9">
	     	  <input type="text" class="form-control" id="overriddenSearchPatterns" name="overriddenSearchPatterns">
	     </div>
	</div>
	<div class="form-group row">
	     <div class="btn-group container">
	     	  <button class="btn btn-danger" type="submit" name="updatecompetence" style="margin: 0 6px 0 0;">Update competence</button>
		  <button class="btn btn-danger" type="submit" name="deletecompetence" style="margin: 0 0 0 6px;">Delete competence</button>
             </div>
	</div>
      </form>

      <!-- 
          SET @user := 123456;
      	  SELECT @group := `group` FROM user WHERE user = @user;
	  SELECT * FROM user WHERE `group` = @group;
       -->
  </div>
</div>
</div>


<footer>
	<div class="container">
	     <?php 
	     echo "Copyright &copy; ".date("Y").
		  "<br/>Research and Innovation, Business Academy Aarhus"; 
	     ?>
	</div>
</footer>

</body>
<script>
    // create the editor ------------------------------------------------------------------------------------------------
    $(document).ready(function () {
        $('.treeview-animated').mdbTreeview(); //initializing the treeview for the list with the competences
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
    var myjson = {}; //Creating an empty object which is used later on
    var jsorbj = <?php echo $row[0] ?> ; //creating a variable jsorobj and assigning a value the converted object from php to javascript
    //console.log(typeof jsorbj);
    var editorstate = 0;              // hiding the save buttons
   // console.log(typeof editorstate);
    var listEl = document.getElementById('CompetenceList');
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
            else if ((jsonObject[i] instanceof Object) && (jsonObject[i]["text"] !== undefined)) {  // Checking if it is an object and if it has the “text” property
                 if ((jsonObject[i]["children"] !== undefined) && (jsonObject[i]["children"].length !== 0)) { //making the HTML if the competence has children (Checking if "children" is not equal to undefined and "children" length is not equal to 0
                    newLi = document.createElement('li');
                    newLi.className = "treeview-animated-items"; // creating new Li object with a property class name and assigning a value "treeview-animated-items"
                    listElement.appendChild(newLi);
                    var newa = document.createElement('a');
                    newdiv = document.createElement('div');
                    newdiv.className = "jsondiv";
                    newa.className = "closed";
                    json = jsonObject[i];//Renaming the variable for readability
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
                    newa.appendChild(newdiv);
                    newLi.appendChild(newa);

                    var newUL = newLi.appendChild(document.createElement('ul'));//Creating a newUL for the children
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
                    newdiv.innerHTML = jsonObject[i]["text"]; // Setting the innerHTML property of the div element to the current element of the string of the jsonObject
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
    document.getElementById('SaveJson').onclick = function () {

        var editedjson = JSON.stringify(editor.get()); //Taking the edited json from the editor and converting it to a string(The one that is edited after pressing the edit button)
        var oldjson = JSON.stringify(myjson);//Taking the old JSON before any changes and making it string (The old json is the one that is loaded when we press the edit button)
        var originaljson = JSON.stringify(jsorbj);//Converting the value of the variable  jsorbj  to a string(Originaljson-the one that is passed from php to JS)
        var sendingjson = JSON.stringify(originaljson.replace(oldjson, editedjson));// Searching for the oldjson inside the originaljson and replacing it with the editedjson

	document.getElementById("newJSONinput").value = sendingjson;
	document.getElementById("StoreJSONformSubmit").click();
	// document.getElementById("StoreJSONform").submit();
        // $.ajax({                                                                    //Converting the object from Javascript to PHP
        //     type: 'post',
        //     url: 'StoreJSON.php',
        //     contentType: "application/x-www-form-urlencoded;charset=utf-8",
        //     data: {service: sendingjson},
        //     success: function (msh) {
        //         $('#successalert').html("<button type=\"button\" class=\"close\" onclick=\"$('.alert').hide()\" aria-hidden=\"true\">&times;</button>\n Competence successfully edited").show();
        //     },
        //     error: function (msg) {
        //         $('#dangeralert').html("<button type=\"button\" class=\"close\" onclick=\"$('.alert').hide()\" aria-hidden=\"true\">&times;</button>\n Competence unsuccessfully edited").show();

        //     }
        // });

    };
    // load json
    document.getElementById('LoadJson').onclick = function () {
        //editor.set(json)
        myjson = <?php echo $row[0] ?>;
        editor.set(myjson); //Setting the json in the editor to be the one that is send from php to javascript
        editorstate = 1;
        showbutton(editorstate);
    };

    //Helper function ------------------------------------------------------------------------------------------------
    function loadjsonpart(json) { //Creating a function loadjsonpart with parameter  json
        editor.set(json);//Setting the editor parameter to the value of the json(meaning that when the edit button is pressed it loads the part of the json)
        myjson = json;//Creating a myjson variable and assigning to it to the value of the json (the part loaded in the editor after pressing the edit button)
        editorstate = 2;
        showbutton(editorstate);//Calling the showbutton function and passing the editorstate parameter
    }
    function showbutton(state){
            document.getElementById("SaveJson").style.display = "block";
    }


// Ajax
window.addEventListener( "load", function () {
  function sendData(event, errorCallback, successCallback) {
    const XHR = new XMLHttpRequest();

    // Bind the FormData object and the form element
    FD = new FormData( event.currentTarget );
    FD.append(event.submitter.name, null);

    // Define what happens on successful data submission
    XHR.addEventListener( "load", function( e ) {
      if (XHR.status == 500)
         errorCallback("Error 500: Internal Server Error", event.submitter.name);
      else
         successCallback( e.target.responseText, event.submitter.name );
    } );

    // Define what happens in case of error
    XHR.addEventListener( "error", function( e ) {
      errorCallback( 'Oops! Something went wrong.', event.submitter.name );
    } );

    // Set up our request
    XHR.open( "POST", event.currentTarget.action );

    // The data sent is what the user provided in the form
    XHR.send( FD );
  }

  function setupAsyncFormSubmit(formId, successCallback, errorCallback) {
  	   form = document.getElementById(formId);
  	   form.addEventListener( "submit", function ( event ) {
    	   	event.preventDefault();
                sendData(event, errorCallback, successCallback);
  	   } );
  }

  setupAsyncFormSubmit("loadcompetenceform", function (responseJSON, submitter) {
     if (submitter=="findcompetence") {
        var value_else_empty = function (obj, attribute) {
            if (typeof obj != "object" || obj == null)
	       return "";
            if (!obj.hasOwnProperty(attribute))
	       return "";
	    return obj[attribute]!=null?obj[attribute]:"";
        }    
        var obj = null;
        try { 
           obj = JSON.parse(responseJSON); 
	   if (obj == null) {
	      throw "Competence not found"; 
	   }
        } catch (e) { 
    	     alert(JSON.stringify(e));
        } 
        form = document.getElementById("updatecompetenceform");
        form["preferredLabel"].value = value_else_empty(obj, "preferredLabel");
        form["altLabels"].value = value_else_empty(obj, "altLabels");
        form["overriddenSearchPatterns"].value = value_else_empty(obj, "overriddenSearchPatterns");
        form["overriddenSearchPatterns"].placeholder = value_else_empty(obj, "defaultSearchPatterns");
        form["grp"].value = value_else_empty(obj, "grp");
        form["conceptUri"].value = value_else_empty(obj, "conceptUri");
     } else if (submitter=="createcompetence") {
        if (responseJSON)
	   alert(responseJSON);
        else
	   document.getElementById('loadcompetenceformfindcompetence').click();
     }
  }, alert);

  setupAsyncFormSubmit("updatecompetenceform", function (responseJSON) {
        if (responseJSON)
	   alert(responseJSON);
        else
	{
	   alert ("Successful database update");
	   document.getElementById('loadcompetenceformfindcompetence').click();
	}
  }, alert);

  setupAsyncFormSubmit("StoreJSONform", alert, alert);

} );


</script>
</html>
