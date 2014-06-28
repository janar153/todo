<?php
/**
* Main database class for MySQLi inspired from Joomla 1.5
*/
class DB {
	protected $db;
	protected $db_host;
	protected $db_user;
	protected $db_pass;
	protected $db_name;
	
	var $_nameQuote		= null;
	var $_quoted		= null;
	var $_hasQuoted		= null;
	var $_sql = null;
	
	/**
	* Default constructor
	* 
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
	* Function to make database connection
	* 
	* @return void
	*/
	private function setupDatabase() {
		$dbSettings = $this->getDatabaseSettings();
		
		try {
			$this->db = new mysqli($dbSettings->db_host, $dbSettings->db_user, $dbSettings->db_pass, $dbSettings->db_name);	
			
			$this->db->set_charset("utf8");
		} catch(Exception $e) {
			return $e->getMessage();
		}
		
	}
	
	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return	boolean
	 */
	function connected() {
		return $this->db->ping();
	}
	
	/**
	* Execute the query
	*
	* @access public
	* @return mixed A database resource if successful, FALSE if not.
	*/
	function query() {
		if (!is_object($this->db)) {
			return false;
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->_sql;

		$this->_cursor = mysqli_query( $this->db, $sql );

		if (!$this->_cursor) {
			throw new Exception("SQL query error: ".mysqli_error( $this->db )." SQL=".$sql);
			return false;
		}
		return $this->_cursor;
	}
	
	/**
	 * Get a database escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 */
	function getEscaped( $text, $extra = false ) {
		$result = mysqli_real_escape_string( $this->db, $text );
		if ($extra) {
			$result = addcslashes( $result, '%_' );
		}
		return $result;
	}
	
	/**
	* This method loads the first field of the first row returned by the query.
	*
	* @return The value returned in the query or null if the query failed.
	*/
	function loadResult() {
		if (!($query = $this->query())) {
			return null;
		}
		$response = null;
		if ($row = mysqli_fetch_row( $query )) {
			$response = $row[0];
		}
		mysqli_free_result( $query );
		return $response;
	}
	
	/**
	* This global function loads the first row of a query into an object
	*
	* @return object
	*/
	function loadObject() {
		if (!($query = $this->query())) {
			return null;
		}
		$response = null;
		if ($object = mysqli_fetch_object( $query )) {
			$response = $object;
		}
		mysqli_free_result( $query );
		return $response;
	}
	
	/**
	* Load a list of database objects
	*
	* @return array list of returned records.
	*/
	function loadObjectList( $key='' ) {
		if (!($query = $this->query())) {
			return null;
		}
		$response = array();
		while ($row = mysqli_fetch_object( $query )) {
			if ($key) {
				$response[$row->$key] = $row;
			} else {
				$response[] = $row;
			}
		}
		mysqli_free_result( $query );
		return $response;
	}
	
	/**
	 * Inserts a row into a table based on an objects properties
	 *
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	function insertObject( $table, &$object, $keyName = NULL ) {
		$fmtsql = 'INSERT INTO '.$this->nameQuote($table).' ( %s ) VALUES ( %s ) ';
		$fields = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->nameQuote( $k );
			$values[] = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
		if (!$this->query()) {
			return false;
		}
		$id = $this->insertid();
		if ($keyName && $id) {
			$object->$keyName = $id;
		}
		return true;
	}
	
	/**
	 * Description
	 *
	 * @param [type] $updateNulls
	 */
	function updateObject( $table, &$object, $keyName, $updateNulls=true ) {
		$fmtsql = 'UPDATE '.$this->nameQuote($table).' SET %s WHERE %s';
		$tmp = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}
			if( $k == $keyName ) { // PK not to be updated
				$where = $keyName . '=' . $this->Quote( $v );
				continue;
			}
			if ($v === null)
			{
				if ($updateNulls) {
					$val = 'NULL';
				} else {
					continue;
				}
			} else {
				$val = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
			}
			$tmp[] = $this->nameQuote( $k ) . '=' . $val;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
		return $this->query();
	}
	
	/**
	 * Description
	 *
	 * @access public
	 */
	function insertid() {
		return mysqli_insert_id( $this->db );
	}
	
	/**
	 * Checks if field name needs to be quoted
	 *
	 * @access public
	 * @param string The field name
	 * @return bool
	 */
	function isQuoted( $fieldName ) {
		if ($this->_hasQuoted) {
			return in_array( $fieldName, $this->_quoted );
		} else {
			return true;
		}
	}
	
	/**
	 * Quote an identifier name (field, table, etc)
	 *
	 * @access	public
	 * @param	string	The name
	 * @return	string	The quoted name
	 */
	function nameQuote( $s ) {
		// Only quote if the name is not using dot-notation
		if (strpos( $s, '.' ) === false) {
			$q = $this->_nameQuote;
			if (strlen( $q ) == 1) {
				return $q . $s . $q;
			} else {
				return $q{0} . $s . $q{1};
			}
		} else {
			return $s;
		}
	}
	
	/**
	* Get a quoted database escaped string
	*
	* @param	string	A string
	* @param	boolean	Default true to escape string, false to leave the string unchanged
	* @return	string
	* @access public
	*/
	function Quote( $text, $escaped = true ) {
		return '\''.($escaped ? $this->getEscaped( $text ) : $text).'\'';
	}
	
	/**
	 * Sets the SQL query string for later execution.
	 *
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @access public
	 * @param string The SQL query
	 * @param string The offset to start selection
	 * @param string The number of results to return
	 * @param string The common table prefix
	 */
	function setQuery( $sql ) {
		$this->_sql		= $sql;
	}
	
	/**
	 * Get the active query
	 *
	 * @access public
	 * @return string The current value of the internal SQL vairable
	 */
	function getQuery() {
		return $this->_sql;
	}
	
	/**
	* Function fill return database settings in standard object
	* 
	* @return
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
	}
}