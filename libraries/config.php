<?php
/**
* Config main class
* 
* @author Janar Nagel
*/
class configLoader {
	var	$config;
	
	/**
	* Default constructor
	* 
	* @return
	*/
	function __construct() {
		$this->loadConfig();
		
		return $this->getConfig();
	}
	
	/**
	* Function to load config
	* 
	* @return
	*/
	private function loadConfig() {
		$this->config = parse_ini_file("config/config.ini", true);
		
	}
	
	/**
	* Function to get config
	* 
	* @return array
	*/
	private function getConfig() {
		return $this->config;
	}
}