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
   echo "Connection to database successfully";
    
    return $con;
   
}



function getBugs($id,$con)
{
    $url='https://bugs.documentfoundation.org/show_bug.cgi?ctype=xml&id='.$id.'';

        $fileContents= file_get_contents($url);

		$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);

		$fileContents = trim(str_replace('"', "'", $fileContents));
		

		$simpleXml = simplexml_load_string($fileContents);
    
        unset($simpleXml->bug->attachment);
    

		$json = json_encode($simpleXml);//JSON_PRETTY_PRINT
        
    
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

function getFromRpc($url, $xml)
{

  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt( $ch, CURLOPT_POST, true );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );

  $result = curl_exec($ch);

  if(!curl_exec($ch)){
    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
}

   //Stampa i risultati in XML
   //header("Content-type: text/xml");
   //print $result;

  curl_close($ch);
    
    return $result;
}

function setXML($id)
{
    return '<?xml version=\'1.0\' encoding=\'UTF-8\'?><methodCall><methodName>Bug.history</methodName> <params> <param>
    <struct>      
        <member>
            <name>ids</name>
            <value>
                <array>
                    <data>
                        <value>
                            <int>'.$id.'</int>
                        </value>
                    </data>
                </array>
            </value>
        </member>   
    </struct>
</param> </params> </methodCall>';
        
}



?>


