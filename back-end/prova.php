<?php


$url = "https://bugzilla.gnome.org/buglist.cgi?bug_status=CLOSED";

$tokens = explode('/', $url);

echo $tokens[2] . '/' . $tokens[3];


?>