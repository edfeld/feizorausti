<?php
/**
 * Salvations.php
 * Salvations include Discipleship Requests.
 * Salvation Requests are type=0
 * Discipleship Requests are type=1
 * 
 **/ 
//

class Salvation {
  private $conn;
  private $table_name = "salvations";

  // object properties
  public $id;
  public $companyStatsId;
  public $simpleAnalyticsVisitorId;
  public $type;
  public $name;
  public $email;
  public $ipAddress;
  public $location;
  public $timetag;
  public $contacted;

  // constructor with $db as database connection
  public function __construct($db){
    $this->conn = $db;
  }
  // create a salvation record for stats
  function createForStats(){

	$query = "INSERT INTO 
			salvations 
		  SET 
			companyStatsId = :companyStatsId, 
			simpleAnalyticsVisitorId = :simpleAnalyticsVisitorId,
			type = :type,
			name = :name,
			email = :email, 
			ipAddress = :ipAddress, 
			location = :location, 
			timetag = :timetag, 
			contacted = :contacted";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
	
        // sanitize
        // Name and email are the only fields the user enters
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->email=htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':companyStatsId', $this->companyStatsId);
        $stmt->bindParam(':simpleAnalyticsVisitorId', $this->simpleAnalyticsVisitorId);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':ipAddress', $this->ipAddress);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':timetag', $this->timetag);
        $stmt->bindParam(':contacted', $this->contacted);
        // execute query
        if($stmt->execute()){
          return true;
      }
      // Otherwise Execution failed
      return false;

  }
}
?>