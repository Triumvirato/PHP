import com.mongodb.*;

import java.net.UnknownHostException;
import java.util.Arrays;
import java.util.List;
import java.util.Set;

import static java.util.concurrent.TimeUnit.SECONDS;

public class MongoDBJDBC{
    public static void main( String args[] ) throws UnknownHostException {

        String userName="tesi";
        String password="tesi";
        String database="tesi_uniba";
        ServerAddress serverAddress = new ServerAddress("ds059712.mongolab.com", 59712);

        MongoCredential credential = MongoCredential.createCredential(userName, database, password.toCharArray());
        MongoClient mongoClient = new MongoClient(serverAddress, Arrays.asList(credential));

        DB db = mongoClient.getDB( "tesi_uniba" );

        DBCollection coll = db.getCollection("mongotesi");

        DBObject myDoc = coll.findOne();

        System.out.println(myDoc);


    }
}