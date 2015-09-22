<?php

require_once __DIR__ . '/funzioni.php';

// MongoDB Connect
$con=connectToMongo(); 

//Select all CLOSED bug(s)
$bugzilla_query = 'https://bugs.documentfoundation.org/buglist.cgi?bug_status=CLOSED';

// cache in a local cache folder if possible
$csv = cacheUrl($bugzilla_query . '&ctype=csv');

$conta=0;

ob_start();

foreach (getBugsFromCSV($csv) as $bug_number => $bug_title){

    if (!empty($bug_number)) { 
        getBugs($bug_number, $con);     
    }
	
	$conta++;
    
	if ($conta == 10)
		break;
	
}

$content = ob_get_contents();
ob_end_clean();

?>

<!doctype html>
<html>
<head>
    <title>Show bug results</title>
    <meta charset="utf-8"></head>
<body>

<p>Results for <a href="<?=$bugzilla_query?>">this query</a>.</p>

<?php echo '<p>Numero di bug(s) presi attualmente (test):  '.$conta.'</p>'; ?> 

<?=$content;?>


</body>
</html>

