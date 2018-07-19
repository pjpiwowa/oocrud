<?php
class Database 
{
	private static $dbName = 'pjpiwowa' ; 
	private static $dbHost = 'localhost' ;
	private static $dbUsername = 'pjpiwowa';
	private static $dbUserPassword = '92dementedlemurs';
	
	private static $cont  = null;
	
	public function __construct() {
		die('Init function is not allowed');
	}
	
	public static function connect()
	{
	   // One connection through whole application
       if ( null == self::$cont )
       {      
        try 
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword);  
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
