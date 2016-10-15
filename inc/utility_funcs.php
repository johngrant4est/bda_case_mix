<?php

/**
 * @author Grant Forrest (Lunaria Ltd)
 * jgrant@lunaria.co.uk
 * @copyright 2016
 */

  function GetSelectFromEnumPDO(
    $dbh,
    $table,
    $field,
    $value,
    $extra_items=NULL,
    $enclose_select=true,
    $enclose_options=true,
    $js_action='') {
	// returns a <select> control with the (optional) current value selected
	// the source data is the enum('') list
    // can optionally be enclosed as a <select>, or <option> or just returns an array from the Enum
    // can also (optionally) have JS action
	
	$sql = "SHOW COLUMNS FROM `$table` WHERE `Field`='$field'";
	$stmt = $dbh->prepare($sql);
	if ($stmt->execute()) {
		$row = $stmt->fetch();
		// Check that the supplied field is an Enum
		if (strpos($row["Type"],"enum") === false && (strpos($row["Type"],"set") === false))
			return "The field $field is not an enumeration or a set";
		$trimmed_options = array();
		$trimmed_options = GetEnumsPDO($row["Type"]);
	    if (!$enclose_options) return $trimmed_options;
	
		// posted values override passed value
		$curr_value = (isset($_POST[$field])) ? $_POST[$field] : $value;
	    $s = "";
	    if ($enclose_select) {
	        $s .= "<select name=\"$field\" id=\"$field\"";
	        if (!empty($js_action)) {
	            $s .= " onchange=\"javascript:$js_action;\"";
	        }
	        $s .= ">\r\n";
	    }
		// any extras as in, additional <options> to add to the select ?
		if (!empty($extra_items)) {
			foreach($extra_items as $value=>$label) {
				$s .= "<option value=\"$value\">$label</option>\r\n";	
			}
		} 
		foreach ($trimmed_options as $option) {
			$s .= "<option value=\"$option\"";
			if ($option == $curr_value)
			  $s .= " selected=\"selected\"";
			$s .= ">$option</option>\r\n";
		}
	}	
	$s .= ($enclose_select) ? "</select>\r\n" : "";
	return $s;
  }
  
  function GetEnumsPDO($row_type,$set=false) {
  	// takes a comma limited list of enums from a table structure def and returns the enums as an array
  	$enums = array();
  	$trimmed_options = array();
	// strip the 'enum(' and ')' or 'set(' and ')'
	
	$str = (!$set) ? substr($row_type,5) : substr($row_type,4);
	$str = substr($str,0,-1);
	$options = explode(',',$str);
	// trim the single quotes
	foreach ($options as $option) {
		$trimmed_options[] = str_replace("'","",$option);
	} 
	return $trimmed_options;
  }
  
  function GetFullColumnsPDO($dbh,$table) {
  	// returns array of $fieldname=>$label/comment
  	$fields = array();
  	$sql = "SHOW FULL COLUMNS FROM `$table`";
  	$stmt = $dbh->prepare($sql);
  	if ($stmt->execute()) {
  		while ($row = $stmt->fetch()) {
  			$fields[$row["Field"]] = $row["Comment"];
  		}
  	}
  	return $fields;
  }

?>