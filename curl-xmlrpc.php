<?php
$url = "https://bugs.documentfoundation.org/xmlrpc.cgi";

  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt( $ch, CURLOPT_POST, true );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_POSTFIELDS, "<?xml version='1.0' encoding='UTF-8'?><methodCall><methodName>Bug.history</methodName> <params> <param>
    <struct>      
        <member>
            <name>ids</name>
            <value>
                <array>
                    <data>
                        <value>
                            <int>89803</int>
                        </value>
                    </data>
                </array>
            </value>
        </member>   
    </struct>
</param> </params> </methodCall>" );

  $result = curl_exec($ch);

  if(!curl_exec($ch)){
    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
}

   //Stampa i risultati in XML
   header("Content-type: text/xml");
   print $result;

  curl_close($ch);
?>
