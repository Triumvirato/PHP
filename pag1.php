<?php

$url = 'https://bugzilla.mozilla.org/rest/bug?id=529';
$content = file_get_contents($url);
$json = json_decode($content, true);

foreach($json['bugs'] as $item) {
    
    
    
    print $item['classification'];

    print '<br>';
    
}


//funziona
echo $json['bugs'][0]['assigned_to_detail']['id'];

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