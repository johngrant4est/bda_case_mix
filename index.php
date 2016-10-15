<?php

/**
 * @author Lunaria (Scotland) Limited www.lunaria.co.uk jgrant@lunaria.co.uk
 * @copyright 2016
 * Demo for NHS Hackday Newcastle 1-2 October 2016
 * Written for PHP 7.0.11 and tested on MariaDB 10.1.18/Apache 2.4.23 (Win64)
 */

  require_once("inc/dbconnect.php");
  require_once("inc/funcs.php");
  require_once("inc/navigation.php");
  require_once("inc/const.php");
  // GET any variables passed to the page
  $page = isset($_REQUEST["p"]) ? $_REQUEST["p"] : "";
  $posted = isset($_POST["posted"]) ? $_POST["posted"] : "";
  $action = isset($_REQUEST["a"]) ? $_REQUEST["a"] : "";
  $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";
  
  // Handle Delete Requests
  if ($action == "Delete") {
    if (!empty($id)) {
        DeleteCase("patient_data",$id);
        $page = "report";
    }
  }
  
  if (!empty ($posted)) {
    // New Record or Update
    if (!empty($id)) {
        // Update
        UpdateCase("patient_data",$id);
        // Recalculate the Weighting
        RecalculateWeighting("patient_data",$id);
        $page = "report";
        $action = NULL;    
    } else {    
      	// write a record to the DB
    	$id = WriteCase("patient_data");
    	if (!empty($id)) {
    		RecalculateWeighting("patient_data",$id);
    		$page = "report";
    	} else $page = "error";
     } 	
  }

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>BDA Case Mix Model</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="css/style.css" type="text/css"/>
	</head>
	<body>
	<h3>BDA Case Mix Model</h3>
    <h5>This is <span class="red-em">demonstration</span> project used for illustrative purposes only and <span class="red-em">should not be used for live patient data</span>.<br />Please direct all questions to <a href="http://www.lunaria.co.uk/contact/">Grant Forrest</a></h5>
	
	<?php
	  echo GetTopNav();
      echo GetBreadCrumbs($page);
	  $s = "";
	  switch ($page) {
	  	case "intro" :
	  		$s = GetTextContent("documentation",1);
	  		break;
	  	case "criteria" :
	  		$s = GetTextContent("documentation",2);
	  		break;
	  	case "rec_and_analysis" :
	  		$s = GetTextContent("documentation",3);
	  		break;
	  	case "scoring" :
	  		$s = GetScoringTool("patient_data");
	  		break;
	  	case "report" :
            switch ($action) {
                case "View" : 
                    $s .= ViewCase("patient_data",$id);
                    break;
                case "Edit" :
                    $s .= EditCase("patient_data",$id); 
                    break;
                default :
                    $s .= GetReports("patient_data");
                    break;
            }
            break;
  	     case "error" :
	  		$s .= "<p>Sorry, there was an error writing the record to the DB</p>\r\n";
	  		break;
	  }
	  echo $s;
	?>
	</body>
</html>