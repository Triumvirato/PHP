<?php
	/*  
		INSTALLARE PRIMA LA LIBRERIA DLL DI MONGO DB DENTRO XAMPP
		http://devzone.co.in/configure-mongodb-php-windows-xampp-5-simple-steps/ 
	
		Tutorial qui: 
		http://www.tutorialspoint.com/mongodb/mongodb_php.htm   		*/
		
		

   // connect to mongodb
   $m = new MongoClient( "mongodb://tesi:tesi@ds059712.mongolab.com:59712/tesi_uniba" );
   echo "Connection to database successfully";
   
   // select a database
   $db = $m->tesi_uniba;
   echo "<br>Database mydb selected";
   
   //crea una collection
   $collection = $db->createCollection("mycol");
   echo "<br>Collection created succsessfully";
  
   //seleziono la collection mycol
   $collection = $db->mycol;
   echo "<br>Collection selected succsessfully";
   
   //inserisco un documento nella collection appena selezionata
   $document = array( 
      "title" => "MongoDB", 
      "description" => "database", 
      "likes" => 100,
      "url" => "http://www.tutorialspoint.com/mongodb/",
      "by", "tutorials point"
   );
   $collection->insert($document);
   echo "<br>Document inserted successfully";
   
   
   //Code snippets to select all documents: 
   $cursor = $collection->find();
   // iterate cursor to display title of documents
   foreach ($cursor as $document) {
      echo "<br>" . $document["title"] . "<br>";
   }
   
   
   
?>