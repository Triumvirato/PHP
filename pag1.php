<?php

$con = mysql_connect("localhost", "root","") or die('Could not connect: ' . mysql_error());
    mysql_select_db("bugs", $con);

getVariables('415570',$con); 

/*
foreach($json['bugs'] as $item) {
    
    
    
    print $item['classification'];

    print '<br>';
    
}
*/


//otteniamo le variabili necessarie formulando 3 API dei 3 Url
function getVariables($id,$con)
{

$url = 'https://bugzilla.mozilla.org/rest/bug?id='.$id.'';
$url_comment='https://bugzilla.mozilla.org/rest/bug/'.$id.'/comment';
$url_changes='https://bugzilla.mozilla.org/rest/bug/'.$id.'/history';

getVar($url, $url_comment, $url_changes, $id, $con);
       
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

function getVar($url, $url_comment, $url_changes,$id,$con)
{
$content = file_get_contents($url);
$json = json_decode($content, true);    
    
    echo '<br>';
    $creator_email=$json['bugs'][0]['creator_detail']['email'];
    echo '<br>';
    $component= $json['bugs'][0]['component'];
    echo '<br>';
    $assigned_email= $json['bugs'][0]['assigned_to_detail']['email'];
    echo '<br>';
    $priority= $json['bugs'][0]['priority'];
    echo '<br>';
    $severity= $json['bugs'][0]['severity'];
    echo '<br>';
    $platform= $json['bugs'][0]['platform'];
    echo '<br>';
    $op_sys= $json['bugs'][0]['op_sys'];
    echo '<br>';
    $resolution= $json['bugs'][0]['resolution'];
    echo '<br>';
    $status= $json['bugs'][0]['status'];
    echo '<br>';
    $creation_time= $json['bugs'][0]['creation_time'];
    echo '<br>';
    $last_change_time= $json['bugs'][0]['last_change_time'];
    echo '<br>';
    $target_milestone= $json['bugs'][0]['target_milestone'];
    echo '<br>';
    $nr_cc_detail= count($json['bugs'][0]['cc_detail']);
    echo '<br>';
    

    $json_c=getComments($url_comment);
    echo '<br>';
    $nr_comments=count($json_c['bugs'][$id]['comments']);
    echo '<br>';
    
    $json_ch=getChanges($url_changes);
    $nr_history=count($json_ch['bugs'][0]['history']);
    echo '<br>';
    
    //insert into mysql table
    $sql = "INSERT INTO bug
    VALUES('$id', '$creator_email', '$component', '$assigned_email', '$priority', '$severity', '$platform', '$op_sys', '$resolution', '$status',     '$creation_time', '$last_change_time', '$target_milestone', '$nr_cc_detail','$nr_comments','$nr_history')";
    if(!mysql_query($sql,$con))
    {
        die('Error : ' . mysql_error());
    }
    
   /* (id, creator_email, component, assigned_email, priority, platform, op_sys, resolution, status, creation_time, last_change_time, target_milestone, nr_cc_detail, nr_comments, nr_history)*/
    
    
}

echo '<br>';
echo '<br>';
echo '<br>';

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

echo '<br>';
echo '<br>';
echo '<br>';

getId('https://bugzilla.mozilla.org/rest/bug?include_fields=id,summary,status&component=Bookmarks%20%26%20History&product=Firefox&resolution=---');



function getId($url)
{
    
$content = file_get_contents($url);
$json = json_decode($content, true);
    
foreach($json['bugs'] as $item) {
 
    print $item['id'] . ' - ' . $item['status'];
    
    print '<br>';
}
    
}




?>