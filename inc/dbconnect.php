<?php
  // PDO Style DB connections
  try {
  	$dbh = new PDO('mysql:host=localhost;dbname=lunaria_bda_case_mix_model', 'lunaria_bdacasem', 'hNXlU9lQLpyqip7G');
  }
  catch (PDOException $e)
  {
  	echo "<p>Unable to connect: " . $e->getMessage() ."<p>";
  }
?>