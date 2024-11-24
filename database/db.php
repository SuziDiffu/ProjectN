<?php
namespace Database;// should be the first statement in every php file
use \Exception as Exp;// Alias
use \mysqli as MYSQLi;

//default namespace \( root namespace has \)
/**
 *  database class 
 */
// define constants for our db connection(credentials) constants are in upper case
define('DB_HOST','localhost' );
define('DB_PORT','3306' );
define('DB_USER','root' );
define('DB_PASS','' );
define('DB_NAME','AfyaBora' );

class Database{
     //class properties
     private $host;
     private $port;
     private $user;
     private $password;
     private $db_name;

     public  $connection;
     // access modifiers (public[all],ptrotected[within &child classes],private[within])
     // constructor method called automatically when object is created. must have 2 underscores
     public function __construct() {
        //initialize variables
        $this->host=DB_HOST;
        $this->port=DB_PORT;
        $this->user=DB_USER;
        $this->password=DB_PASS;
        $this->db_name=DB_NAME;
     try{
        //create db connection
        $this->connection=new MYSQLi($this->host,$this->user,$this->password, $this->db_name,$this->port);
        
        //check whether coonection was established
        if($this->connection->connect_errno ){
            echo "Unable to connect to the Databse. <b>Please contact the administrator</b>";
            die();//or.... exit()
        };

        //check for errors
     }catch(Exp $e){
        echo "Something went wrong";//what  user sees
        //logging (for  developer)
        var_dump($e->getMessage());
    }
    }
} // end of Database  class

//creating an object of type of database
//$db=new Database();

//how to  access db connection
//var_dump($db->connection);


