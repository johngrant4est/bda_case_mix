<?php

/**
 * @author Grant Forrest (Lunaria Ltd)
 * jgrant@lunaria.co.uk
 * @copyright 2016
 */
  require_once("utility_funcs.php");
  require_once("date_funcs.php");
  require_once("const.php"); 

  function GetTextContent($table,$id) {
  	// returns selected HTML-formatted textual content from the `documentation` table
    global $dbh;
    $s = "";
	$q = "SELECT `title` FROM $table WHERE `id`=$id";
	$stmt = $dbh->prepare($q);
	if ($stmt->execute()) {
		while ($row = $stmt->fetch()) {
			$s .= "<h4>" . $row["title"] . "</h4>\r\n";	
		}
	}
	$q = "SELECT `text` FROM $table WHERE `id`=$id";
	$stmt = $dbh->prepare($q);
	if ($stmt->execute()) {
		while ($row = $stmt->fetch()) {
			$s .= $row["text"] . "\r\n";	
		}
	}	
	return $s;
  }
  
  function GetScoringTool($table) {
  	// returns the tabular scoring tool/form for assessment
  	global $dbh;
  	$fields_to_skip = array("id","updated","weighting");
  	$input_fields = array();
  	// Start with some basic layouts
  	$s = "<h4>Case Mix Data Capture Form and Scoring Tool</h4>\r\n";
  	$s .= "<form name=\"scoring_form\" id=\"scoring_form\" method=\"post\" action=\"" . $_SERVER["PHP_SELF"] . "\">\r\n";
  	$s .= "<table class=\"t1\">\r\n";

  	$q = "SHOW FULL COLUMNS FROM `patient_data`";
  	$stmt = $dbh->prepare($q);
  	if ($stmt->execute()) {
  	  while ($row = $stmt->fetch()) {
  	  	if (in_array($row["Field"],$fields_to_skip)) continue;
  		// row labels
  		$s .= "<tr>\r\n";
  	  	// Row Label
  	  	$s .= "<th><p class=\"th\">" . $row["Comment"] . "</p></th>\r\n";
  	  	// Form Input
  	  	switch ($row["Field"]) {
  	  		case "patient_id" :		// VARCHAR
  	  			// text input
  	  			$s .= "<td><p class=\"th\"><input type=\"text\" size=\"20\" id=\"patient_id\" name=\"patient_id\"/></p></td>\r\n";
  	  			break;
  	  		case "age_range" :		// ENUMS
  	  		case "communication" :
  	  		case "cooperation" :
  	  		case "medical" :
  	  		case "oral_risk" :
  	  		case "access" :
  	  		case "legal_and_ethical" :
  	  			$s .= "<td>" . GetSelectFromEnumPDO($dbh,$table,$row["Field"],"") . "</td>\r\n";
				break;
			case "comments" : // TEXT
				$s .= "<td colspan=\"2\"><textarea id=\"comments\" name=\"comments\" rows=\"5\" cols=\"50\"></textarea></td>\r\n";
				break;					
  	  		default :
  	  			$s .= "<td><p class=\"th\">Next input</p></td>\r\n";
  	  			break;
  	  			
  	  	}
  	  	// Help Column
  	  	$s .= "<td><p class=\"help_text\">" . GetHelpText($row["Field"]) . "</p></td>\r\n";
  	  	$s .= "</tr>\r\n";
  	  }
  	  $s .= "<tr><td colspan=\"2\"><input type=\"submit\" name=\"posted\" value=\"submit\"/></td></tr>\r\n";
  	}
  	$s .= "</table>\r\n";
  	$s .= "</form>\r\n";
  	return $s;
  }
  
  function WriteCase($table) {
  	// writes a new case to the DB
  	global $dbh;
  	global $error_file;
  	$fields_to_skip = array("id","weighting","updated");
  	$q = "INSERT INTO `$table` (";
  	// build the query
  	$cols = GetFullColumnsPDO($dbh,"patient_data");
  	foreach ($cols as $fn=>$label) {
  		if (in_array($fn,$fields_to_skip)) continue;
  		$q .= "`$fn`,";	
  	}
  	$q = substr($q,0,-1);
  	$q .= ") VALUES (";
  	foreach ($cols as $fn=>$label) {
  		if (in_array($fn,$fields_to_skip)) continue;
  		if (isset($_POST[$fn])) {
  			$q .= "'" . $_POST[$fn] . "',";
  		}
  	}
  	$q = substr($q,0,-1);
	$q .= ")";
	// Debug
	// file_put_contents($error_file,$q,FILE_APPEND);
	$stmt = $dbh->prepare($q);
	if ($stmt->execute()) {
		$id = $dbh->lastInsertId("id");
		// file_put_contents($error_file,"Record ID: " . $id . "\r\n",FILE_APPEND);
	} else return 0;
	return $id; 	
  }
  
  function UpdateCase($table,$id) {
  	// writes a new case to the DB
  	global $dbh;
  	$fields_to_skip = array("id","weighting","updated");
  	$q = "UPDATE `$table` SET ";
  	// build the query
  	$cols = GetFullColumnsPDO($dbh,"patient_data");
  	foreach ($cols as $fn=>$label) {
  		if (in_array($fn,$fields_to_skip)) continue;
  		$q .= "`$fn`=";
        switch ($fn) {
            case "patient_id" :
                $q .= $_POST["patient_id"] . ",";
                break;
             default :
                $q .= "'" . $_POST[$fn] . "',";
                break; 
        }	
  	}
  	$q = substr($q,0,-1);
  	$q .= " WHERE `id`=$id";
 
	$stmt = $dbh->prepare($q);
	$stmt->execute();
    return;	
  }
  
  function DeleteCase($table,$id) {
    global $dbh;
    $q = "DELETE FROM `$table` WHERE `id`=$id";
	$stmt = $dbh->prepare($q);
	$stmt->execute();
    return;	    
  }
  
  function GetReports($table) {
  	// return a reporting table showing a summary of each scored/weighted case
  	global $dbh;
    global $weighted_fields;
    $fields_to_skip = array("id","comments","updated");
    $buttons = array(
       "b_search.png"=>"View",
	   "b_edit.png"=>"Edit",
       "b_drop.png"=>"Delete");
  	$toggle = true;
  	$s = "";
  	$s = "<table class=\"t1\">\r\n";
  	// Get Columns 
  	$cols = GetFullColumnsPDO($dbh,$table);
  	// Headers
  	$s .= "<tr>\r\n";
  	foreach ($cols as $fn=>$label) {
  	     if (in_array($fn,$fields_to_skip)) continue;
  		 $s .= "<th><p class=\"flatbold\">$label</p></th>\r\n";
  	}

  	// Weighting Category
  	$s .= "<th><p class=\"flatbold\">Composite Score</p></th>\r\n";
    // Control Buttons
    foreach ($buttons as $b) {
        $s .= "<th><p class=\"flatbold\">&#160;</p></th>\r\n"; 
    }
  	$s .= "</tr>\r\n";
  	$q = "SELECT * FROM `$table`";
  	$stmt = $dbh->prepare($q);
  	if ($stmt->execute()) {
  		while ($row = $stmt->fetch()) {
			// Data
			$s .= "<tr class=\"";
			$s .= ($toggle) ? "white" : "grey";
			$s .= "\">\r\n";
			foreach ($cols as $fn=>$label) {
			    if (in_array($fn,$fields_to_skip)) continue;
				$s .= "<td class=\"data\"><p class=\"flat\">";
                // Conditional display of different columns
				switch (TRUE) {
					case ($fn == "updated") : 	// TIMESTAMP
						$s .= ($row["updated"] == '0000-00-00 00:00:00') ? "Never" : SQLDateToUKDate($row["updated"],'d-M Y H:m');
						break;
                    case (in_array($fn,$weighted_fields)) :
                        // Display the value and the weighting in brackets
                        $s .= $row[$fn] . "<span class=\"ws\">(" . GetWeightedScore($fn,$row[$fn])  . ")</span>\r\n";
                        break;
					default : 
						$s .= $row[$fn];
						break;
				}
				$s .= "</p></th>\r\n";
			} 
			// Based on the score, peg on a composite category
			$wc = GetWeightingCategory($row["weighting"],true);
			$s .= "<td><p class=\"flat\">$wc</p></td>\r\n";
            // View, Edit, Delete
            foreach ($buttons as $img=>$action) {
                $s .= "<td><img src=\"img/$img\" alt=\"$action\" title=\"$action\" style=\"cursor:pointer\"";
                if ($action == "Delete") {
                    $s .= " onclick=\"if (confirm('Really Delete this item?')==true) { window.document.location.href='" . $_SERVER["PHP_SELF"] . "?a=Delete&amp;id=" . $row["id"] . "';} else { return false; }\" ";
                } else {
                    $s .= " onclick=\"window.document.location.href='" . $_SERVER["PHP_SELF"] . "?p=report&amp;a=$action&id=" . $row["id"] . "';\"";
                }
                $s .= "/></td>\r\n";

            }  
			$s .= "</tr>\r\n";
			$toggle = !$toggle;	
  		}
  	}
  	$s .= "</table>\r\n";
  	return $s;
  }
  
  function CalculateWeighting($table, $id) {	
  	// calculate the patient's weighting based on the data entered
  	global $dbh;
    global $weighted_fields;
  	$weighting = 0;
  
  	$q = "SELECT * FROM `$table` WHERE `id`=$id";
  	$stmt = $dbh->prepare($q);
  	if ($stmt->execute()) {
  		$row = $stmt->fetch();
  		// Do the ratings one at a time and add the score up
  		foreach ($weighted_fields as $wf) {
  			$weighting += GetWeightedScore($wf,$row[$wf]);
  		}
  	}
   	return $weighting;
  }
  
  function UpdateWeighting($table,$id,$weighting) {
  	// update the record with the calculated weighting
  	global $dbh;
  	$q = "UPDATE `$table` SET `weighting`=$weighting WHERE `id`=$id";
  	$stmt = $dbh->prepare($q);
  	if ($stmt->execute()) {
  		return;			
  	}
  }
  
  function RecalculateWeighting($table,$id) {
    // Look at a record, calculate the weighting based on its weighted fields and re-write the value
    $w = CalculateWeighting($table,$id);
    UpdateWeighting($table,$id,$w);
    return;
  }
  
  function GetWeightingCategory($weighting,$styled=false) {
  	// calculates the weighting based on the range criteria
  	$cat = "Standard Patient";
    $css_class = NULL;
  	switch (TRUE) {
  		case ($weighting == 0) : break;
  		case (($weighting > 0) && ($weighting < 10) ) : $cat = "Some Complexity"; break;
  		case (($weighting > 9) && ($weighting < 20) ) : $cat = "Moderate Complexity"; break;
  		case (($weighting > 19) && ($weighting < 31) ) : $cat = "Severe Complexity"; break;
  		case ($weighting > 30) : $cat = "Extreme Complexity"; break;
  	}
    if ($styled) {
        switch ($cat) {
            case "Standard Patient" :
                $css_class = "w_standard";
                break;
            case "Some Complexity" :
                $css_class = "w_some_complex";
                break;
            case "Moderate Complexity" :
                $css_class = "w_mod_complex";
                break;
            case "Severe Complexity" :
                $css_class = "w_sev_complex";
                break;
            case "Extreme Complexity" :
                $css_class = "w_ext_complex";
                break;
        }
        return "<span class=\"" . $css_class . "\" style=\"padding:3px;\">$cat</span>\r\n";
    } else return $cat;
  }
  
  function GetWeightedScore($field, $value) {
  	// takes a field name (comms etc) and looks up the numerical rating for that value - can be 0,A,B,C
	// Weighting arrays
    global $communication_arr;
    global $cooperation_arr;
    global $medical_arr;
    global $oral_risk_arr;
    global $access_arr;
    global $legal_and_ethical_arr;
	$score = 0;
    $scoring_array = array();
	
	// Assign the scoring array to the relevant array for calculating the weighted score
	switch ($field) {
		case "communication" :
            $scoring_array = $communication_arr;
            break;
		case "cooperation" :
            $scoring_array = $cooperation_arr;
            break;
		case "medical" :
            $scoring_array = $medical_arr;
            break;
		case "oral_risk" :
            $scoring_array = $oral_risk_arr;
            break;
		case "access" :
            $scoring_array = $access_arr;
            break;
		case "legal_and_ethical" :
            $scoring_array = $legal_and_ethical_arr;
            break;
		case "default" :
            file_put_contents($error_file,$field . " : " . $value . "\r\n",FILE_APPEND);
            break;		
	}
    switch ($value) {
        case "0" :
            $score += 0; break;     // bit obvious but remember the field is an ENUM
        case "A" :
            $score += $scoring_array[0]; break;
        case "B" :
            $score += $scoring_array[1]; break;
        case "C" :
            $score += $scoring_array[2]; break;
        default :
            $score = -1;
            break;
    }
	return $score;
	  	
  }
  
  function GetHelpText($field) {
  	// returns a small description of each field and its associated type
  	$s = "";
  	
  	switch ($field) {
  		case "patient_id" :
  			$s .= "Use Case Record Number (CRN), NHS Number or CHI Number";
  			break;
  		case "age_range" :
  			$s .= "Select the patient's Age Range";
  			break;
  		default :
  			$s .= "";
  			break;
  			
  	}
  	return $s;
  }
  
  function ViewCase($table,$id) {
    // show us the detail of a case
    global $dbh;
    global $weighted_fields;
    $fields = GetFullColumnsPDO($dbh,$table);
    $s = "<table class=\"t1\">\r\n";
    $q = "SELECT * FROM `$table` WHERE `id`=$id";
    $stmt = $dbh->prepare($q);
  	if ($stmt->execute()) {
  		$row = $stmt->fetch();
    }
    foreach ($fields as $fn=>$comment) {
        $s .= "<tr class=\"white\">\r\n";
        // Labels
        $s .= "<td class=\"label\"><p class=\"label\">$comment</p></td>\r\n";
        // Data
        $s .= "<td class=\"data\"><p class=\"flat\">";
        switch (TRUE) {
            case (in_array($fn,$weighted_fields)) :
                $s .= $row[$fn] . "<span class=\"ws\">(" . GetWeightedScore($fn,$row[$fn])  . ")</span>\r\n";
                break;
            case ($fn == "updated") :
                $s .= ($row["updated"] == '0000-00-00 00:00:00') ? "Never" : SQLDateToUKDate($row["updated"],'d-M Y H:m');
                break; 
            default :
                $s .= $row[$fn];
                break;
        }
        $s .= "</p></td>\r\n";
        $s .= "</tr/>\r\n";
        
    }
    $s .= "</table>\r\n";
    return $s;
  }
  
  function EditCase($table,$id) {
    // show us the detail of a case
    global $dbh;
    global $weighted_fields;
    $fields_to_skip = array("id","updated");
    $read_only_fields = array("weighting");     // not used currently
    $fields = GetFullColumnsPDO($dbh,$table);
    $q = "SELECT * FROM `$table` WHERE `id`=$id";
    $stmt = $dbh->prepare($q);
  	if ($stmt->execute()) {
  		$row = $stmt->fetch();
    }
    
    $s = "<form id=\"edit_case_form\" name=\"edit_case_form\" method=\"post\" action=\"";
    $s .= $_SERVER["PHP_SELF"] . "?p=report\">\r\n";
    $s .= "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"" . $row["id"] . "\"/>\r\n";
    $s .= "<table class=\"t1\">\r\n";

    
    foreach ($fields as $fn=>$comment) {
        if (in_array($fn,$fields_to_skip)) continue;
        $s .= "<tr class=\"white\">\r\n";
        // Labels
        $s .= "<td class=\"label\"><p class=\"label\">$comment</p></td>\r\n";
        // Data
        $s .= "<td class=\"data\"><p class=\"flat\">";
        switch (TRUE) {
            case (in_array($fn,$weighted_fields)) :
            case ($fn == "age_range") :
                // Selects like the input form
                $s .= GetSelectFromEnumPDO($dbh,$table,$fn,$row[$fn]) . "\r\n";
                break;
            case ($fn == "comments") :
                $s .= "<textarea id=\"comments\" name=\"comments\" rows=\"5\" cols=\"50\">" . $row["comments"] . "</textarea>\r\n";
				break;
            case ($fn == "weighting") :
                $s .= $row["weighting"] . "<span style=\"margin-left:10px;\">";
                $s .= GetWeightingCategory($row["weighting"],true) . "</span>\r\n";
                break;     	
           default :
                $s .= "<input type=\"text\" id=\"$fn\" name=\"$fn\" value=\"" . $row[$fn] . "\"/>\r\n";
                break;
        }
        $s .= "</p></td>\r\n";
        $s .= "</tr>\r\n";
       
    }
    // Submit Buttons
    $s .= "<tr><td colspan=\"2\"><p style=\"text-align:center\">\r\n";
    $s .= "<input type=\"submit\" name=\"posted\" value=\"update case\"/>\r\n";
    $s .= "<input type=\"button\" name=\"btn_cancel\" value=\"cancel\" onclick=\"window.document.location.href='index.php?p=report'\"/></p></td></tr>\r\n";
    $s .= "</table>\r\n";
    $s .= "</form>\r\n";
    return $s;
  }

?>