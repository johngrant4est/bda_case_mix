<?php

/**
 * @author Grant Forrest (Lunaria Ltd)
 * jgrant@lunaria.co.uk
 * @copyright 2016
 */

 function SQLDateToUKDate($SQLDate,$format) {
  	// takes a SQL date/datetime Y-m-d hh:mm:ss and convert to d-M Y
  	return date($format,strtotime($SQLDate));
  }

?>