<?php

/*
 * getBugsFromCSV()
 * fetches and a remote csv file generated by an advanced search in Bugzilla
 * @var string $csv, url of the csv file
 * @var string $full, default to false, return short or long results
 * @return array list of bugs and their description
 */

function getBugsFromCSV($csv, $full = false)
{
    $shortBugs = $fullBugs = $temp = [];

    if (($handle = fopen($csv, 'r')) !== false) {

        while (($data = fgetcsv($handle, 300, ',')) !== false) {

            if ($data[0] == 'bug_id') {
                $fields = $data;
                continue;
            }

            foreach ($fields as $key => $field) {
                $temp[$field] = $data[$key];
            }

            $fullBugs[] = $temp;
            $shortBugs[$temp['bug_id']] = $temp['short_desc'];
        }

        fclose($handle);
    }

    return ($full) ? $fullBugs : $shortBugs;
}

/*
 * cacheUrl()
 * Caches a remote resource in the cache folder for 120 seconds
 *
 * @var $url, remove content
 * @var $time = 120, seconds to cache
 * @return string
 */

function cacheUrl($url, $time = 120)
{
    $cache_dir  = __DIR__ . '/cache/';
    $cache_file = $cache_dir . sha1($url) . '.cache';

    if (is_file($cache_file)) {
        $age = $_SERVER['REQUEST_TIME'] - filemtime($cache_file);

        if ($age < $time) {

            return $cache_file;
        }
    }

    // Only fetch external data if we can write to Cache folder
    if (is_dir($cache_dir)) {
        $file = file_get_contents($url);
        file_put_contents($cache_file, $file);

        return $cache_file;
    }

    // No caching possible, return $url
    return $url;
}


function connectToMongo()
{
    // connect to mongodb
   $con = new MongoClient( "mongodb://tesi:tesi@ds059712.mongolab.com:59712/tesi_uniba" );
   
   //Debug
   //echo "Connection to database successfully";
    
    return $con;
   
}



function getBugs($id,$con)
{
 
// Include the library to use it.
include_once('simple_html_dom.php');

// Start History Scraping 
$html = file_get_html('https://bugs.documentfoundation.org/show_activity.cgi?id='.$id.'');

//Search the table (muhahahahah)    
$es = $html->find('table tr td');

$arrlength = count($es);

for($x = 0; $x < $arrlength; $x++) 
{
    if(preg_match('/UTC/',$es[$x]) || preg_match('/PDT/',$es[$x]) || 
	preg_match('/DST/',$es[$x]) || preg_match('/PST/',$es[$x]) || 
	preg_match('/EST/',$es[$x])){
	
		$ladata= $es[$x];
    }
	
    if(preg_match('/Status/',$es[$x])){
		
		if(preg_match('/RESOLVED/',$es[$x+2])){
		
			$stato= $es[$x+2];
			$data_def=$ladata;
        }       
    }  
    
    if(preg_match('/Priority/',$es[$x])){
		
		if(!isset($priorita)){
			$priorita= $es[$x+1];
            }
	}
    
    if(preg_match('/Severity/',$es[$x]))
	{
		if(!isset($gravita)){
            $gravita= $es[$x+1];
        }
    }

	
	//Echo TEST
    //echo $es[$x];
}

	/*Echo TEST
	echo '<p>BUG id: '.$id.'</p>';
	echo '<p>Stato: '.$stato.'</p>';
	echo '<p>Data: '.$data_def.'</p>';
	echo '<p>Priorita: '.$priorita.'</p>';
	echo '<p>Gravita: '.$gravita.'</p>';
	*/
 
 
//-------------------------------------------------------------------------------

// XML show bug URL
$url='https://bugs.documentfoundation.org/show_bug.cgi?ctype=xml&id='.$id.'';

$fileContents= file_get_contents($url);
$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
$fileContents = trim(str_replace('"', "'", $fileContents));
		
$simpleXml = simplexml_load_string($fileContents);
    
// Remove 'attachment' node (big size!)
unset($simpleXml->bug->attachment);

//Entro nel nodo <bug></bug>
$bugnode = $simpleXml->bug;

//Add new information to XML
$bugnode->addChild('last_status', strip_tags(trim($stato)));       //Toglie tag html e toglie spazi vuoti
$bugnode->addChild('first_priority', strip_tags(trim($priorita)));
$bugnode->addChild('first_gravity', strip_tags(trim($gravita)));
$bugnode->addChild('resolved_date', strip_tags(trim($data_def)));

//echo $simpleXml->asXML();

    
$json = json_encode($simpleXml, JSON_PRETTY_PRINT); //JSON_PRETTY_PRINT
    
//header("Content-type: text/json");
//print $json;
    
$json = json_decode($json);
    
    
// memorizziamo nel database
    
        // select a database
        $db = $con->tesi_uniba;
   
        if(!isset($collection))
        {
            //crea una collection
           $collection = $db->createCollection("mongotesi");
        }

           //seleziono la collection 
           $collection = $db->mongotesi;
    
            $collection->insert($json);
           
    
}


?>


