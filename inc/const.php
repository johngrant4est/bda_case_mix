<?php

/**
 * @author Grant Forrest (Lunaria Ltd)
 * jgrant@lunaria.co.uk
 * @copyright 2016
 */

  $error_file = "error.txt";
  // Fields in the DB that have a weighting applied
  $weighted_fields = array(
    "communication",
    "cooperation",
    "medical",
    "oral_risk",
    "access",
    "legal_and_ethical");
    
	// Arrays to hold the actual weighting values
    $communication_arr = array(2,4,8);
	$cooperation_arr = array(3,6,12);
	$medical_arr = array(2,6,12);
	$oral_risk_arr = array(3,6,12);
	$access_arr = array(2,4,8);
	$legal_and_ethical_arr = array(2,4,8);
?>