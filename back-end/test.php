<?php

require_once __DIR__ . '/funzioni.php';

// MongoDB Connection
$con=connectToMongo(); 

//Select all CLOSED bug(s)
$bugzilla_query = $_POST['urlsend'];

// cache in a local cache folder if possible
$csv = cacheUrl($bugzilla_query . '&ctype=csv');

// count bug(s)
$conta=0;

//Recive name of collection from front-end
$collname = $_POST['collname'];

//Debug
echo '<p>Collection: '.$collname.'</p>';


foreach (getBugsFromCSV($csv) as $bug_number => $bug_title){

    if (!empty($bug_number)) { 
        getBugs($bug_number, $con, $collname);     
    }
	
	$conta++;

	//Limit bug to keep
	//if ($conta == 100)
	//break;
}


// Print the results on front-end
 echo '<p>FINE. Prelevati: '.$conta.' bug(s)</p>';
 flush();
 ob_flush();

?>