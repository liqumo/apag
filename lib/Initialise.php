<?php
	// Initialiser
	function __autoload($class_name) {
 	   require_once $class_name . '.php';
	}
	
	// LOAD VARIABLES & CONSTANTS
	require_once("_config.vars.php");
	require_once("_config.procs.php");
	
	// Database Abstraction
	include_once("DatabaseAbstract.php"); //
	$db = new DatabaseAbstract;
	$db -> connect(); //*/
	
	// Security Control
	include_once("SecurityManagement.php");
	$secure = new SecurityManagement;
	
	// Session Control
	include_once("SessionManagement.php");
	$session = new SessionManagement;
	
	// Application
	include_once("Application.php");
	$app = new Application;
	
	// Components
	define('SELECTED', ' selected=\'selected\'');
?>