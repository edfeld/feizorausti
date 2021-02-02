<?php

// include_once "env.php";

// used to get mysql database connection
class Database{
  function __construct($currentEnv, $databaseName="")
  {
    // These are the DB parms for the GRO database
    if ($databaseName === 'GRO') {
      switch ($currentEnv) {
        // ERE20200422 - update  the webServer case with the production GRO database values
        case "WebServer":
          $this->host     = "96.93.106.82";
          $this->db_name  = "gro";
          $this->username = "core";
          $this->password = "6VzYKFN7QYb71gKW"; 
        break;
        case "Sandbox":
          $this->host     = "localhost";
          $this->db_name  = "isa2islam";
          $this->username = "isa2islam";
          $this->password = "EKkZGGGoknh2vYTo"; 
        break;
        default:
        // Default to localhost
        $this->host = "localhost";
        $this->db_name = "GRO";
        $this->username = "root";
        $this->password = "root";  
      }
    } else {
      // Included here are the DB parms for this property's database
      require_once("../config/connectionParms.php");
      $this->host     = $hostname;
      $this->db_name  = $database;
      $this->username = $username;
      $this->password = $password;
    }
  }
    // specify your own database credentials
    
    private $host;
    private $db_name;
    // private $db_name = "php_login_system";
    private $username;
    private $password;  
    public $conn;
 
    // get the database connection
    public function getConnection(){
      
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>