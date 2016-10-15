<?php

/**
 * @author Grant Forrest (Lunaria Ltd)
 * jgrant@lunaria.co.uk
 * @copyright 2016
 */
  $nav = array(
    "intro"=>"Introduction",
	"criteria"=>"Criteria",
	"rec_and_analysis"=>"Recording &amp; Analysis",
	"scoring"=>"Scoring Tool",
	"report"=>"Reports");
  
  function GetTopNav() {
  	global $nav;
  	$s = "<p>";
  	foreach($nav as $url=>$label) {
  		$s .= "<a href=\"index.php?p=$url\">$label</a>";
		$s .= "&#160;|&#160;\r\n";	
  	}
  	$s .= "</p>\r\n";
  	return $s;
  }
  
  function GetBreadcrumbs($page) {
    global $nav;
    $s = "<p class=\"breadcrumbs\">You are here : " . "<a href=\"index.php?p=intro\">Home</a>\r\n";
    foreach ($nav as $n => $label) {
        if ($n == "intro")
            continue;
        if ($n == $page) {
            $s .= "&#160;&gt;&#160;" . "<a href=\"index.php?p=$n\">$label</a>\r\n";
        }
    }
    $s .= "</p>\r\n";
    return $s;
   }
   
?>