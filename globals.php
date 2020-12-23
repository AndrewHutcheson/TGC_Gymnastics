<?php

	date_default_timezone_set("America/Chicago");
	$siteAdministrator = 'andrew.hutcheson@utexas.edu';
	
	include("database.php");

	//old, deprecate this in favor of PDO object.
		$con= new mysqli($sql_login_host, $sql_login_user, $sql_login_pass, $sql_login_db);
		
		if($con->connect_error){
			die("Connection Problem: ". $con->connect_error);
		}
	
	//F**k this BS when you don't have root control of php's ini file. Prevents us from having to use stripslashes on every input field.
	if (get_magic_quotes_gpc()) {
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}
	
	if(isset($_REQUEST['showErrors']))
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}
	
	function getCurrentSeason(){ 
		if (DATE('m') >= '08'){
			$currentSeason = DATE('Y')+1;
		}
		else{
			$currentSeason = DATE('Y');
		}
	
		return (int)$currentSeason;
	}
	
	//newest db stuff. Need to rewrite everything to use this.
	class Config
	{
		static $confArray;
		public static function read($name)
		{
			return self::$confArray[$name];
		}
		public static function write($name, $value)
		{
			self::$confArray[$name] = $value;
		}
	}

	// db
	Config::write('db.host', 'localhost');
	Config::write('db.port', '3306');
	Config::write('db.basename', 'texacpnq_TGC');
	Config::write('db.user', 'texacpnq');
	Config::write('db.password', 'eoikNcx2j18b');
	
	class Core
	{
		public $dbh; // handle of the db connection
		private static $instance;

		private function __construct()
		{
			try
			{
				// building data source name from config
				$dsn = 'mysql:host=' . Config::read('db.host') .
					   ';dbname='    . Config::read('db.basename') .
					   ';port='      . Config::read('db.port') .
					   ';connect_timeout=15';
				// getting DB user from config                
				$user = Config::read('db.user');
				// getting DB password from config                
				$password = Config::read('db.password');

				$this->dbh = new PDO($dsn, $user, $password);
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (PDOException $e)
			{
				echo "PDO object error: " . $e->getMessage();
			}
		}

		public static function getInstance()
		{
			if (!isset(self::$instance))
			{
				$object = __CLASS__;
				self::$instance = new $object;
			}
			return self::$instance;
		}

		// others global functions
		public function getCurrentSeason()
		{ 
			if (DATE('m') >= '08'){
				$currentSeason = DATE('Y')+1;
			}
			else{
				$currentSeason = DATE('Y');
			}
		
			return (int)$currentSeason;
		}
	}
	
	//stupid hacky way to instanciate this core object for compatibility with old code and I know I shouldn't be doing it lol. But its quicker than updating every other file. Eventually delete.
		$core = Core::getInstance();
		$conn = $core->dbh;
?>