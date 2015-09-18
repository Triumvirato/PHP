<?php
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

$xml=setXML("92360");

$xml=getFromRpc("https://bugs.documentfoundation.org/xmlrpc.cgi", $xml);
        
        
    //$simpleXml = simplexml_load_string($xml);
    $xml = new SimpleXMLElement($xml);

//percorso per arrivare ai datatime
//'params/param/value/struct/member/value/array/data/value/struct/member/value/array/data/value/struct/member/value/dateTime.iso8601'

/* Search for <a><b><c> */
$result = $xml->xpath('params/param/value/struct/member/value/array/data/value/struct/member/value/array/data/value/struct/member/value/array/data/value/struct/member/value/string');

while(list( , $node) = each($result)) {
    echo $node,"<br>";
}
//header("Content-type: text/json");
//echo $json;

?>