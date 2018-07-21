<?php

/*
 * Unlike the Database class in the horrible StarTutorial example, this is
 * an instantiable(?) object meant to represent a database.
 */
class Database 
{
	private $dbName;
	private $dbHost;
	private $dbUsername;
	private $dbUserPassword;

	// This is our database connection, which we do not want duplicated.
	private static $cont  = null;
	
	public function __construct(/* string */ $name, /* string */ $host,
	                            /* string */ $username, /* string */ $password)
	{
		$this->dbName = $name;
		$this->dbHost = $host;
		$this->dbUsername = $username;
		$this->dbUserPassword = $password;

		// Get a connection ready to go
		$this->connect();
	}

	public function __destruct()
	{
		$this->disconnect();
	}

	/*
	 * Returns a PDO referring to the database, creating a network
	 * connection if necessary.
	 */
	public function connect()
	{
		if (null == self::$cont)
		{
			try
			{
				self::$cont =  new PDO("mysql:host=".$this->dbHost.";"."dbname=".$this->dbName, $this->dbUsername, $this->dbUserPassword);
			}
			catch(PDOException $e) 
			{
				die($e->getMessage());
			}
		}
		return self::$cont;
	}
	
	public static function disconnect()
	{
		self::$cont = null;
	}
}
?>
