<?php

 // connect to mongodb
   $con = new MongoClient( "mongodb://tesi:tesi@ds059712.mongolab.com:59712/tesi_uniba" );
   echo "Connection to database successfully";
   
  // select a database
   $db = $con->tesi_uniba;
   echo "<br>Database mydb selected";
   
   //crea una collection
   $collection = $db->createCollection("mongotesi");
   echo "<br>Collection created succsessfully";
  
   //seleziono la collection 
   $collection = $db->mongotesi;
   echo "<br>Collection selected succsessfully";
   
   

$component= urlencode('Build Config');

store_db('https://bugzilla.mozilla.org/rest/bug?include_fields=id,summary,status&component='.$component.'&product=Firefox&resolution=---', $con, $collection);



function store_db($url, $con, $collection)
{
    
$content = file_get_contents($url);
$json = json_decode($content, true);
    
foreach($json['bugs'] as $item) {
 
    $id=$item['id'];
    getVariables($id, $con, $collection);
    
}
    
}

 
 
 

/* Prende l'oggetto json e lo salva in un file .json

$outfile= 'result.json';
$url='https://bugzilla.mozilla.org/rest/bug?id=415555';
$json = file_get_contents($url);
if($json) { 
    if(file_put_contents($outfile, $json, FILE_APPEND)) {
      echo "Saved JSON fetched from “{$url}” as “{$outfile}”.";
    }
    else {
      echo "Unable to save JSON to “{$outfile}”.";
    }
}
else {
   echo "Unable to fetch JSON from “{$url}”.";
}
*/

 
 
 


 

/*
foreach($json['bugs'] as $item) {
    
    
    
    print $item['classification'];

    print '<br>';
    
}
*/


//otteniamo le variabili necessarie formulando 3 API dei 3 Url
function getVariables($id,$con, $collection)
{

$url = 'https://bugzilla.mozilla.org/rest/bug?id='.$id.'';
$url_comment='https://bugzilla.mozilla.org/rest/bug/'.$id.'/comment';
$url_changes='https://bugzilla.mozilla.org/rest/bug/'.$id.'/history';

getVar($url, $url_comment, $url_changes, $id, $con, $collection);
       
}

function getComments($url)
{
$content = file_get_contents($url);
$json_c = json_decode($content, true);
    
    return $json_c;
}

function getChanges($url)
{
$content = file_get_contents($url);
$json_ch = json_decode($content, true);
    
    return $json_ch;
}

function getVar($url, $url_comment, $url_changes, $id, $con, $collection)
{
$content = file_get_contents($url);
$json = json_decode($content, true);    
    
     
    $creator_email=$json['bugs'][0]['creator_detail']['email'];
     
    $component= $json['bugs'][0]['component'];
     
    $assigned_email= $json['bugs'][0]['assigned_to_detail']['email'];
     
    $priority= $json['bugs'][0]['priority'];
     
    $severity= $json['bugs'][0]['severity'];
     
    $platform= $json['bugs'][0]['platform'];
     
    $op_sys= $json['bugs'][0]['op_sys'];
     
    $resolution= $json['bugs'][0]['resolution'];
     
    $status= $json['bugs'][0]['status'];
     
    $creation_time= $json['bugs'][0]['creation_time'];
     
    $last_change_time= $json['bugs'][0]['last_change_time'];
     
    $target_milestone= $json['bugs'][0]['target_milestone'];
     
    $nr_cc_detail= count($json['bugs'][0]['cc_detail']);
     
    

    $json_c=getComments($url_comment);
    
    $nr_comments=count($json_c['bugs'][$id]['comments']);
    
    
    $json_ch=getChanges($url_changes);
    $nr_history=count($json_ch['bugs'][0]['history']);
    
    
    /*insert into mysql table
    $sql = "INSERT INTO bug
    VALUES('$id', '$creator_email', '$creator_email', '$assigned_email', '$priority', '$severity', '$platform', '$op_sys', '$resolution', '$status',     '$creation_time', '$last_change_time', '$target_milestone', '$nr_cc_detail','$nr_comments','$nr_history')";
    if(!mysql_query($sql,$con))
    {
        die('Error : ' . mysql_error());
    }
    else
    {
        echo 'inserito';
        echo '<br>';
    }
	
	*/
	
	
	$collection->insert($json);
   
	
	
	/*
	//inserisco un documento nella collection appena selezionata
   $document = array( 
      "id" => $id, 
      "creator_email" => $creator_email, 
      "assigned_email" => $assigned_email,
      "priority" => $priority,
      "severity", $severity,
	  "platform" => $platform, 
      "op_sys" => $op_sys,
      "resolution" => $resolution,
	  "status" => $status, 
      "creation_time" => $creation_time,
      "last_change_time" => $last_change_time,
	  "target_milestone" => $target_milestone,
      "nr_cc_detail" => $nr_cc_detail,
	  "nr_comments" => $nr_comments, 
      "nr_history" => $nr_history
   );
   $collection->insert($document);
   echo "<br>Document inserted successfully";
   
   */
    
}







?>