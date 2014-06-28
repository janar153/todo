<?php
/**
* Main class
* 
* @author Janar Nagel
*/
class ToDoMain {
	protected $db;
	protected $db_host;
	protected $db_user;
	protected $db_pass;
	protected $db_name;
	protected $db_query;
	protected $lang;
	var $error;
	var $pageLimit;
	var $pageLimitStart;
	var $lastLocation;
	
	/**
	* Default constructor
	* 
	* @param string $host	Database hostname
	* @param string $user	Database username
	* @param string $pass	Database password
	* @param string $db		Database name
	* @return
	*/
	function __construct($host, $user, $pass, $db) {
		if(empty($host)) {
			throw new Exception("Database error: Database hostname missing!");
			exit();
		}
		
		if(empty($user)) {
			throw new Exception("Database error: Database username missing!");
			exit();
		}
		
		if(empty($db)) {
			throw new Exception("Database error: Database name missing!");
			exit();
		}
		$this->setupDatabaseSettings($host, $user, $pass, $db);
		
		$this->setupDatabase();
	}
	
	/**
	* Function to set language
	* 
	* @param string $lang		Language tag
	* @return string
	*/		
	public function setLang($lang) {
		$this->lang = $lang;
	}
	
	/**
	* Function to get language
	* 
	* @return string
	*/	
	private function getLang() {
		return $this->lang;
	}
	
	/**
	* Function to set page limit
	* 
	* @param string $limit		Limit
	* @return void
	*/	
	public function setPageLimit($limit) {
		$this->pageLimit = $limit;
	}
	
	/**
	* Function to get page limit
	* 
	* @return string
	*/
	private function getPageLimit() {
		return $this->pageLimit;
	}
	
	/**
	* Function to set page limit start
	* 
	* @param string $limitstart	Limitstart
	* @return void
	*/	
	public function setPageLimitStart($limitstart) {
		$this->pageLimitStart = $limitstart;
	}
	
	/**
	* Function to get page limit start
	* 
	* @return string
	*/	
	private function getPageLimitStart() {
		return $this->pageLimitStart;
	}
	
	/**
	* Function to set last location url
	* 
	* @param string $url	Last location URL
	* @return void
	*/	
	public function setLastLocation($url) {
		$this->lastLocation = $url;
	}
	
	/**
	* Function to get last location url
	* 
	* @return string
	*/	
	private function getLastLocation() {
		return $this->lastLocation;
	}
	
	/**
	* Function to get works array list
	* 
	* @param string $status		Work status
	* @param string $limitstart	Limitstart
	* @param string $limit		Limit
	* @param string $priority	Work priority
	* @param string $q			Search keyword
	* @return array
	*/		
	public function getWorksList($status, $limitstart = 0, $limit = 20, $priority = null, $q = null) {
		$this->setPageLimit($limit);
		$this->setPageLimitStart($limitstart);
		$rowLimit = " LIMIT ".$limitstart.", ".$limit;
		$where = "";
		if($status != "all") {
			$where .= " AND work_status = '".$status."' ";
		}
		if(!empty($priority) && $priority != "all") {
			$where .= " AND work_priority = '".$priority."' ";
		}
		
		if(!empty($q)) {
			$where .= " AND (work_name LIKE '%".$q."%' OR work_desc LIKE '%".$q."%') ";
		}
		
		$this->db->setQuery("SELECT *, DATE_FORMAT(work_deadline, '%d.%m.%Y') work_deadline, DATE_FORMAT(work_created, '%d.%m.%Y %H:%i:%s') work_created FROM works WHERE deletion_date = '0000-00-00 00:00:00' ".$where." ORDER BY work_deadline ASC ".$rowLimit);
		$data = $this->db->loadObjectList();
		
		return $data;
	}
	
	/**
	* Function to get works count
	* 
	* @param string $status		Work status
	* @param string $priority	Work priority
	* @param string $q			Search keyword
	* @return string
	*/	
	public function getWorksCount($status, $priority = null, $q = null) {		
		$where = "";
		if($status != "all") {
			$where .= " AND work_status = '".$status."' ";
		}
		
		if(!empty($priority) && $priority != "all") {
			$where .= " AND work_priority = '".$priority."' ";
		}
		
		if(!empty($q)) {
			$where .= " AND (work_name LIKE '%".$q."%' OR work_desc LIKE '%".$q."%') ";
		}
		
		$this->db->setQuery("SELECT COUNT(*) FROM works WHERE deletion_date = '0000-00-00 00:00:00' ".$where);
		$data = $this->db->loadResult();
		
		return $data;
	}

	/**
	* Function to get work data
	* 
	* @param string $workID		Work ID
	* @return object
	*/	
	public function getWorkData($workID) {
		$this->db->setQuery("SELECT *, DATE_FORMAT(work_deadline, '%d.%m.%Y') work_deadline, DATE_FORMAT(work_created, '%d.%m.%Y %H:%i:%s') work_created FROM works WHERE deletion_date = '0000-00-00 00:00:00' AND work_id = '".$workID."'");
		$data = $this->db->loadObject();
		
		return $data;
	}
	
	/**
	* Function to check if there are data on prev page
	* 
	* @return boolean
	*/	
	public function hasPrev() {
		$hasPrev = false;
		$pageLimitStart = $this->getPageLimitStart();
		
		if($pageLimitStart > 0) {
			$hasPrev = true;
		}
		
		return $hasPrev;
	}
	
	/**
	* Function to check if there are data on next page
	* 
	* @param string $status		Work status
	* @param string $priority	Work priority
	* @param string $q			Search keyword
	* @return boolean
	*/	
	public function hasNext($status, $priority = null, $q = null) {
		$hasMore = false;
		$totalRows = $this->getWorksCount($status, $priority, $q);
		$pageLimit = $this->getPageLimit();
		$pageLimitStart = $this->getPageLimitStart();
		
		$rowsLeft = $totalRows - $pageLimit - $pageLimitStart;
		if($rowsLeft > 0) {
			$hasMore = true;
		}
		
		return $hasMore;
	}
	
	/**
	* Function to get works priority list
	* 
	* @return array
	*/	
	public function getPriorityList() {
		$data = array("0" => $this->getTranslation("label.option.choose"),
						"1" => $this->getTranslation("label.option.normal"),
						"2" => $this->getTranslation("label.option.important"),
						"3" => $this->getTranslation("label.option.critical"));
		return $data;
	}
	
	/**
	* Function to get works statuses list
	* 
	* @return array
	*/	
	public function getStatusList() {
		$data = array("0" => $this->getTranslation("label.option.choose"),
						"1" => $this->getTranslation("label.option.new"),
						"2" => $this->getTranslation("label.option.open"),
						"3" => $this->getTranslation("label.option.completed"));
		return $data;
	}
	
	/**
	* Function to save new work into database
	* 
	* @return string
	*/		
	public function addWork() {
		$save = new stdClass();
		$save->work_id  = null;
		$save->work_name = $_REQUEST["nimetus"];
		$save->work_desc = $_REQUEST["kirjeldus"];
		$save->work_priority = $_REQUEST["priority"];
		$save->work_deadline = $this->fixDate($_REQUEST["deadline"]);
		$save->work_status = $_REQUEST["status"];
		$save->work_created = date("Y-m-d H:i:s");
		$save->deletion_date = "0000-00-00 00:00:00";
		
		$this->db->insertObject("works", $save, "work_id");
		
		return $save->work_id;
	}
	
	/**
	* Function to save new work into database
	* 
	* @return string
	*/		
	public function editWork() {
		$save = new stdClass();
		$save->work_id  = $_REQUEST["workID"];
		$save->work_name = $_REQUEST["nimetus"];
		$save->work_desc = $_REQUEST["kirjeldus"];
		$save->work_priority = $_REQUEST["priority"];
		$save->work_deadline = $this->fixDate($_REQUEST["deadline"]);
		$save->work_status = $_REQUEST["status"];
		$save->deletion_date = "0000-00-00 00:00:00";
		
		$this->db->updateObject("works", $save, "work_id");
		
		return $save->work_id;
	}
	
	public function deleteWork() {
		$save = new stdClass();
		$save->work_id  = $_REQUEST["workID"];
		$save->deletion_date = date("Y-m-d H:i:s");

		$this->db->updateObject("works", $save, "work_id");
		
		return $save->work_id;
	}
	
	/**
	* Function to fix dateformat
	* 
	* @param string $date		Date in Estonian format (dd.mm.YYYY)
	* @return string
	*/		
	private function fixDate($date) {
		$time = strtotime($date);
		$fixed = date("Y-m-d H:i:s", $time);
		
		return $fixed;
	}
	
	/**
	* Function to get translation string
	* 
	* @param string $key		Translation key string
	* @return string
	*/		
	public function getTranslation($key) {
		$locales = $this->loadLocales($this->getLang());
		$data = $key;
		
		$mis = array("&Auml;","&auml;","&Ouml;", "&ouml;","&Uuml;", "&uuml;", "&otilde;");
		$millega = array("\\u00c4", "\\u00e4", "\\u00d6", "\\u00f6", "\\u00dc", "\\u00fc", "\u00f5");
		
		if(isset($locales[$key])) {
			$data = str_replace($millega, $mis,$locales[$key]);	
		}
		
		return $data;
	}
	
	/**
	* Function to load locales file
	* 
	* @param string $lang		Language tag
	* @return string
	*/	
	private function loadLocales($lang = null) {
		$langString = "_en";
		if(!empty($lang)) {
			$langString = "_".$lang;
		} 
		$localesFile = file_get_contents("locales/todo".$langString.".properties");
		$locales = $this->parse_properties($localesFile);
		
		return $locales;
	}
	
	/**
	* Function to parse properties file content
	* 
	* @param string $txtProperties		Transaltion properties file content
	* @return array
	*/	
	private function parse_properties($txtProperties) {
		$result = array();

		$lines = split("\n", $txtProperties);
		$key = "";

		$isWaitingOtherLine = false;
		foreach($lines as $i=>$line) {

			if(empty($line) || (!$isWaitingOtherLine && strpos($line,"#") === 0)) continue;

			if(!$isWaitingOtherLine) {
				$key = substr($line,0,strpos($line,'='));
				$value = substr($line,strpos($line,'=') + 1, strlen($line));
			} else {
				$value .= $line;
			}

			/* Check if ends with single '\' */
			if(strrpos($value,"\\") === strlen($value)-strlen("\\")) {
				$value = substr($value, 0, strlen($value)-1)."\n";
				$isWaitingOtherLine = true;
			} else {
				$isWaitingOtherLine = false;
			}

			$result[$key] = $value;
			unset($lines[$i]);
		}

		return $result;
	}
	
	/**
	* Function to make database connection
	* 
	* @return void
	*/
	private function setupDatabase() {
		
		$dbSettings = $this->getDatabaseSettings();
		
		require_once("libraries\db.php");
		$this->db = new DB($dbSettings->db_host, $dbSettings->db_user, $dbSettings->db_pass, $dbSettings->db_name);
		
	}
	
	/**
	* Function fill return database settings in standard object
	* 
	* @return object
	*/
	private function getDatabaseSettings() {
		$settings = new stdClass;
		$settings->db_host = $this->db_host;
		$settings->db_user = $this->db_user;
		$settings->db_pass = $this->db_pass;
		$settings->db_name = $this->db_name;
		
		return $settings;
	}
	
	/**
	* Function to setup database settings
	* 
	* @param string $host	Database hostname
	* @param string $user	Database username
	* @param string $pass	Database password
	* @param string $db		Database name
	* 
	* @return void
	*/
	private function setupDatabaseSettings($host, $user, $pass, $db) {
		$this->db_host = $host;
		$this->db_user = $user;
		$this->db_pass = $pass;
		$this->db_name = $db;
		
		$this->setupDatabase();
	}
}