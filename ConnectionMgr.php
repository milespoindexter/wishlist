<?php

//manage the MySQL db connections
class ConnectionMgr {

	// Hold an instance of the class
	private static $instance;
    
    // DB _________________________
    private static $server =   "localhost";
    private static $db =       "your_db";
    private static $usr =      "your_usr";
    private static $pwd =      "your_pwd";
	
	// A private constructor; prevents direct creation of object
	private function __construct() {
		//echo 'I am constructed';
	}

	// The singleton method
	public static function singleton() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	//establish MySQL DB connection. Returns PDO object.
	public function getConnection() {
		
		$con = null;
		try {
			 $con = new PDO("mysql:host=".self::$server.";dbname=".self::$db, self::$usr, self::$pwd);
             //echo "Connected to database"; // check for connection
		}
		catch(PDOException $e){
            echo 'Error connecting to MySQL Server!: '.$e->getMessage();
            exit();
		}
		
		return $con;
		
	}

	
	public function returnConnection( $con ) {
		$con = NULL;
		
	}
	

	// Prevent users to clone the instance
	public function __clone() {
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

}


?>
