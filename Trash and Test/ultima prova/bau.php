<?php

    // Include the library to use it.
    include_once('simple_html_dom.php');

    // Get the HTML from the Yahoo! website.
    $html = file_get_html('https://bugs.documentfoundation.org/show_activity.cgi?id=30861');

    // Put all of the <a> tags into an array named $result
    $result = $html -> find('td');

    // Run through the array using a foreach loop and print each link out using echo
    foreach($result as $link) {
        echo $link."<br/>";
    }

    //seconda prova
    $es = $html->find('table tr td');
    foreach($es as $link) {
            echo $link."<br/>";
    }

    //terza prova
    $es = $html-> getElementsByTagName('table');
    foreach($es as $link) {
            echo $link."<br/>";
    }



?>