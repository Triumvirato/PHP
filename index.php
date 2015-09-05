<?php
$json = file_get_contents('https://bugzilla.mozilla.org/rest/bug?id=529');
//echo $json;

// You can decode it to process it in PHP
$data = json_decode($json);
var_dump($data);

echo $data->bugs->{'op_sys'};
?>