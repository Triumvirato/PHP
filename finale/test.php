<?php


require_once __DIR__ . '/funzioni.php';

$con=connectToMongo(); //connessione al database mongo

$bugzilla_query = 'https://bugs.documentfoundation.org/'
					. 'buglist.cgi?bug_status=CLOSED'
				//	. '&classification=Client%20Software&query_format=advanced'
				//	. '&f2=flagtypes.name&v1=review%3F&v2=needinfo%3F'
				//	. '&product=LibreOffice';
				
				;

// cache in a local cache folder if possible
$csv = cacheUrl($bugzilla_query . '&ctype=csv');

$conta=0;
ob_start();

foreach (getBugsFromCSV($csv) as $bug_number => $bug_title) {

    if (!empty($bug_number)) {
        //echo '<p>'.$bug_number.'</p>';
        
        getBugs($bug_number,$con);
        
        
    }
	
	$conta++;
    //if ($conta == 1) break;
  

	

	
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

<?php echo '<p>Num di bug:   '.$conta.'</p>'; ?> 

<?=$content;?>



</body>
</html>

