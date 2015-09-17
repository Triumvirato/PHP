<?php

class XmlToJson {

	public function Parse ($url) {

		$fileContents= file_get_contents($url);

		$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);

		$fileContents = trim(str_replace('"', "'", $fileContents));
		
	    //$fileContents = str_replace('\/', '/', $fileContents); //bob fix

		//echo $fileContents;

		$simpleXml = simplexml_load_string($fileContents);

		$json = json_encode($simpleXml, JSON_PRETTY_PRINT);
	

		return $json;

	}

}

$xmltojson = new XmlToJson();


$js = $xmltojson->Parse('https://bugs.documentfoundation.org/show_bug.cgi');


header('Content-Type: application/json');

echo $js;





?>