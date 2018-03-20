<?php
/* ======================================================= //
	ADMORPHIT DATABASE ABSTRACTION LAYER
	http://www.admorphit.com
	Licensed for use with APAG Website
	Copyright 2009 and beyond.
// ======================================================= */

class DatabaseAbstract extends masterCls {
	
	# Configurable Properties
	protected $Host = '';
	protected $User = '';
	protected $Pass = '';
	protected $Database = '';
	
	# Class Properties
	private static $_connection = NULL; // singleton
	private $_result;
	
	/* =========================================================================================== /
		CONSTRUCT
	/= ===========================================================================================*/
	public function __construct(){
		if(!isset(self::$_connection)){
			$this->Connect();
			//echo create;
		}else{
			//echo single;
			return self::$_connection;
		}//*/
	}
	/* =========================================================================================== /
		DESTRUCT
	/= ===========================================================================================*/
	public function __destruct(){
		//$this->ConnectionHandler("disconnect");
	}
	
	/* =========================================================================================== /
		METHOD[ps]: DESIGN PATTERN (SINGLETON) Connect
		@inp : [STR]
		@ret : NULL
	/= ===========================================================================================*/
	public function Connect(){
		// Specify Defaults
		$database_connection = array(
			"Host"=>DATABASE_HOST != '' ? DATABASE_HOST : '',
			"User"=>DATABASE_USER != '' ? DATABASE_USER : '',
			"Pass"=>DATABASE_PASS != '' ? DATABASE_PASS : '',
			"Database"=>DATABASE_DBNM != '' ? DATABASE_DBNM : ''
		);
		$this->config($database_connection);
		
		self::$_connection = new mysqli($this->Host, $this->User, $this->Pass, $this->Database);
		
		if(mysqli_connect_error()){
			$this->Output('Connect failed: '. mysqli_connect_error(), 'err');
			return 0;
		}
	}
	
	/* =========================================================================================== /
		METHOD[r]: Disconnect connection
		@inp : [STR]
		@ret : T||F
	/= ===========================================================================================*/
	private function Disconnect(){
		return self::$_connection->close();
	}
	
	/* =========================================================================================== /
		METHOD[p]: Grab SQL Connection
		@ret : OBJ:SQL-Connection
	/= ===========================================================================================*/
	public function GetConnection(){
		return self::$_connection;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Standard Query
		@inp : STR
		@ret : T||F
	/= ===========================================================================================*/
	public function Query($sql, $action = 'single'){
		//echo $sql.'<hr />';
		$this->_result = $action=='single'?self::$_connection->query($sql):self::$_connection->multi_query($sql);
		if($this->_result){
			$ret = true;
		}else{
			$this->Output(self::$_connection->error, "error");
			$ret = false;
		}
		return $ret;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Check Value Existence
		@inp : STR,[STR]
		@ret : T||F
	/= ===========================================================================================*/
	public function ValueExists($table, $condition = ""){
		$existence = $this->CountRecords($table, $condition);
		if($existence <= 0){
			return false;
		}else{
			return true;
		}
	}
	
	/* =========================================================================================== /
		METHOD[p]: Count Records
		@inp : STR,[STR]
		@ret : INT
	/= ===========================================================================================*/
	public function CountRecords($table, $condition = ""){
		return $this->GetScalar($table, '1', $condition);
	}
	public function CountRecordsSQL($sql){
		$this->Query($sql);
		return $this->TotalRows();
	}
	
	/* =========================================================================================== /
		METHOD[p]: Return Single Value
		@inp : STR,STR,[STR]
		@ret : STR
	/= ===========================================================================================*/
	public function GetScalar($table, $item, $condition = ""){
		$sql = "SELECT $item result FROM $table";
		if($condition != ""){
			$sql .= " WHERE $condition";
		}
		$sql .= " LIMIT 1";
		//return $sql;
		$this->Query($sql);
		$record = $this->Fetch();
		return $record->result;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Fetch ROW of data
		@inp : [STR]
		@ret : OBJ||ASSARR||ARR
	/= ===========================================================================================*/
	public function Fetch($method = "obj"){
		$method = strtolower("-".$method);
		if(stripos($method, "obj")){ // Object
			return $this->_result->fetch_object();
		}elseif(stripos($method, "assoc")){ // Associative
			return $this->_result->fetch_assoc();
		}else{ // Normal
			return $this->_result->fetch_array();
		}
	}
	
	/* =========================================================================================== /
		METHOD[p]: Determine Total Number of Returned Rows
		@ret : INT
	/= ===========================================================================================*/
	public function TotalRows(){
		return $this->_result->num_rows;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Get All Results
		@inp : [STR]
		@ret : INT
	/= ===========================================================================================*/
	public function FetchAll($method = "assoc"){
		$method = strtolower("-".$method);
		$result = array("");
		if(stripos($method, "obj") || stripos($method, "assoc")){ // Object || Associative
			while( $row = $this->Fetch("associative") ){
				if($row === -1){
					return false;
				}else{
					foreach($row as $key => $value){
						$result[$key][] = $value;
					}
				}
			}
			
			if(stripos($method, "obj")){ // Object Portion
				return $this->Array2obj($result);
			}
		}else{
			while( ($row = $this->Fetch("indexed") ) != false ){
				if($row === -1){
					return false;
				}else{
					$result[] = $row;
				}
			}
		}
		return $result;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Database Preparation (Twin Methods)
		@inp : STR
		@ret : STR
	/= ===========================================================================================*/
	public function GetLastError(){
		return self::$_connection->error;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Database Preparation (Twin Methods)
		@inp : STR
		@ret : STR
	/= ===========================================================================================*/
	public function PrepareDBInput($input){
		$input = addslashes($input);
		return $input;
	}
	
	public function PrepareDBOutput($output){
		$input = stripslashes($output);
		return $input;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Generate SQL Query Based On Array of Table Column Names
		@inp : STR, ARR, STR, STR, STR
		@ret : STR||F
	/= ===========================================================================================*/
	public function Array2QueryStr($table, $columnArray, $method = 'insert', $source = '', $condition = ''){
		if(is_array($columnArray)){
			$method = strtolower($method); $s = false;
			
			if(!empty($source)){ // Post||Get||Request Source
				$s = true; // Source Not Empty
			}
			
			for($i=0;$i<count(
				($s)?$columnArray:$columnArray[column]
			);$i++){
				switch($method){
					case 'update': // Update
						if($s){
							$sql .= $columnArray[$i]." = \"".$this->PrepareDBInput($source[$columnArray[$i]])."\"";
						}else{
							$sql .= $columnArray[column][$i]." = \"".$this->PrepareDBInput($columnArray[set][$i])."\""; 
						}
					break;
					
					default: // Insert
						($s)?
							$val.=$this->PrepareDBInput($columnArray[$i]):
							$val.=$this->PrepareDBInput($columnArray[column][$i]);
						($s)?
							$sql.="\"".$this->PrepareDBInput($source[$columnArray[$i]])."\"":
							$sql.="\"".$this->PrepareDBInput($columnArray[set][$i])."\"";
				}
				// Add comma unless last item
				if($i<count(
					($s)?$columnArray:$columnArray[column]
				)-1){ $sql.=", "; $val.=", "; }
			}
			
			// Build query
			switch($method){
				case 'update': // Update
					$finalQuery = "UPDATE $table SET $sql";
					(!empty($condition))?$finalQuery.=" WHERE $condition":'';
				break;
				
				default: // Insert
					$finalQuery = "INSERT INTO $table ($val) VALUES ($sql)";
			}
			
			return $finalQuery;
		}else{
			return false;
		}
	}
	
	/* =========================================================================================== /
		METHOD[p]: New Transaction, DeferConstraints, Commit and Rollback (Quad Methods)
		@ret : NULL
	/= ===========================================================================================*/
	public function NewTransaction(){
		self::$_connection->autocommit(FALSE);
		$this->Output("start transaction", "err");
	}
	public function DeferConstraints($act = "on"){
		($act=="on")?$set="0":$set="1";
		$this->Query("SET @@foreign_key_checks = ".$set);
		$this->Output("deferred constraints set", "err");
	}
	public function Commit(){
		self::$_connection->commit();
		self::$_connection->autocommit(TRUE);
		$this->Output("commit", "err");
	}
	public function Rollback(){
		self::$_connection->rollback();
		self::$_connection->autocommit(TRUE);
		$this->Output("rollback", "err");
	}
}
?>