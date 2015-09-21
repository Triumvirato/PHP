<?php
include_once('simple_html_dom.php');
$service = 'https://bugs.documentfoundation.org/show_activity.cgi?id=30861';
$debug = $_GET["debug"];

$url = $service;
if($debug === "yes"){
echo "URL IS: " . $url . "\n";
}

$search = array('?', ' ', '.asp&');
$replace = array('&', '+', '.asp?');

$url2 = str_replace($search, $replace, $url);
if($debug === "yes"){
echo "REPLACEMENTS: ". $url2 . "\n";
}
$end = "http://tsy.acislive.com" . $url2 . '&showall=1';
if($debug === "yes"){
echo "FINAL URL: ". $end;
}

$html = file_get_html($end);

$ret = $html-> getElementsByTagName('table');

print($ret);
?>