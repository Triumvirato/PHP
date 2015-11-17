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


/*
 * connectToMongo()
 * Connect to mongodb and return the connection
 *
 * @return $con
 */

function connectToMongo()
{
    //Remote host
    $con = new MongoClient( "mongodb://188.166.121.194:27017/tesi_uniba" );

    //localhost
    //$con = new MongoClient( "mongodb://127.0.0.1/tesi_uniba" );
    
    return $con;  
}


/*
 * getBugs()
 * Get bugs and save them on db
 *
 * @var $id, id of the bugzilla bug
 * @var $con, db connection
 */

function getBugs($id,$con,$collname,$bugzilla_query)
{
 
// Include the library for scraping
include_once('simple_html_dom.php');

//Compongo l'url della history
$history_url = $bugzilla_query . '/show_activity.cgi?id='.$id.'';

// Start History Scraping 
$html = file_get_html($history_url);

//Search the table that contains data  
$es = $html->find('table tr td');

$arrlength = count($es);

//Count number of activity
$nr_activities=0;

$trovato = 0;
$duplicato = 0;  

//Keep data from History page
for($x = 0; $x < $arrlength; $x++) 
{
    //Keep date and time
    if(
        preg_match('/UTC/',$es[$x]) || 
        preg_match('/PDT/',$es[$x]) || 
        preg_match('/DST/',$es[$x]) || 
        preg_match('/PST/',$es[$x]) || 
        preg_match('/EST/',$es[$x]) || 
        preg_match('/EDT/',$es[$x])
        ){ 

        $ladata= $es[$x];
        $nr_activities++;
    }
    
    //Keep bug status
    if(preg_match('/Status/',$es[$x])){
        
        if(preg_match('/RESOLVED/',$es[$x+2])){
        
            $stato= $es[$x+2];
            $data_def=$ladata;

            $trovato = 1;
        }
    }  

    //Cerca duplicati
    if(preg_match('/Resolution/',$es[$x])){
        
        if(preg_match('/DUPLICATE/',$es[$x+2])){

            $duplicato = 1;
        }
    }  

    
    //Keep bug priority
    if(preg_match('/Priority/',$es[$x])){
        
        if(!isset($priorita)){
            $priorita= $es[$x+1];
            }
    }
    
    //Keep bug severity
    if(preg_match('/Severity/',$es[$x]))
    {
        if(!isset($gravita)){
            $gravita= $es[$x+1];
        }
    }

}



if($trovato == 1 || $duplicato == 1){  

    /*Echo TEST
    echo '<p>BUG id: '.$id.'</p>';
    echo '<p>Stato: '.$stato.'</p>';
    echo '<p>Data: '.$data_def.'</p>';
    echo '<p>Priorita: '.$priorita.'</p>';
    echo '<p>Gravita: '.$gravita.'</p>';
    */
 
 
//-------------------------------------------




// compongo l'url per aprire i dati del bug in formato XML
$url = $bugzilla_query . '/show_bug.cgi?ctype=xml&id='.$id.'';

$fileContents= file_get_contents($url);
$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
$fileContents = trim(str_replace('"', "'", $fileContents));
        
$simpleXml = simplexml_load_string($fileContents);
    
// Remove 'attachment' node (big size!)
unset($simpleXml->bug->attachment);

//Enter in the node <bug></bug>
$bugnode = $simpleXml->bug;

//From xml
$xmlseverity = $bugnode->bug_severity;
$xmlpriority = $bugnode->priority;

//Add new information (previus data) to XML
if(isset($priorita))
    $bugnode->addChild('first_priority', trim(strip_tags($priorita)));
else
    $bugnode->addChild('first_priority', $xmlpriority);
    
if(isset($gravita))
    $bugnode->addChild('first_severity', trim(strip_tags($gravita)));
else
    $bugnode->addChild('first_severity', $xmlseverity);

if(isset($data_def))    
    $bugnode->addChild('resolved_date', trim(strip_tags($data_def)));
    
$bugnode->addChild('nr_activities', trim(strip_tags($nr_activities)));


//aggiungiamo i giorni di risoluzione
$data_start = $bugnode->creation_ts;
$data_def = trim(strip_tags($data_def));
    
$interval = date_diff(date_create($data_start), date_create($data_def));
$giorni = $interval->format('%a');

$bugnode->addChild('days_resolution', $giorni);
    
//Debug    
//echo $simpleXml->asXML();

//XML to json conversion
$json = json_encode($simpleXml, JSON_PRETTY_PRINT); //JSON_PRETTY_PRINT

//Debug
//header("Content-type: text/json");
//var_dump($json);

$json = json_decode($json);
    
    
//------------ Save data on DB -------------

// select a database
$db = $con->tesi_uniba;

//$collname = "provacoll";
   
    if(!isset($collection)){
    //crea una collection
    $collection = $db->createCollection($collname);
    }

//Select collection
$collection = $db->$collname;
  

try{  
    $collection->insert($json);
    
    echo '<p>Bug: '.$id.' inserted</p>';
    }
    
    catch(MongoWriteConcernException $e){
        
        echo 'Database error';
        echo "error message: ".$e->getMessage()."\n";

    }

    
    flush();
    ob_flush(); 

}//chiusura if

}

?>