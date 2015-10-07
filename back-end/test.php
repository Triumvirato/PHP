<?php

require_once __DIR__ . '/funzioni.php';

// MongoDB Connect
$con=connectToMongo(); 

//Select all CLOSED bug(s)
$bugzilla_query = $_POST['urlsend'];

// cache in a local cache folder if possible
$csv = cacheUrl($bugzilla_query . '&ctype=csv');

$conta=0;



foreach (getBugsFromCSV($csv) as $bug_number => $bug_title){

    if (!empty($bug_number)) { 
        getBugs($bug_number, $con);     
    }
	
	$conta++;

	if ($conta == 100)
		break;
	
}

 echo '<p>FINE. Prelevati: '.$conta.'</p>';
 flush();
 ob_flush();

?>