<?php
  // PDO Style DB connections
  try {
  	$dbh = new PDO('mysql:host=localhost;dbname=bda_case_mix_model', 'bdacasem', '');
  }
  catch (PDOException $e)
  {
  	echo "<p>Unable to connect: " . $e->getMessage() ."<p>";
  }
?>
