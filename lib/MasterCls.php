<?php
	// MASTER CLASS
	// This CLASS is the master initialiser class which is extended to all classes. The methods in this class hold true for all classes.

class MasterCls {
	private $output;
	private $SystemMessage;
	private $LastError;
	
	public function call(){
		$this->output = get_class($this);
		echo $this->output;
		return $this->output;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Configuration GET/SET Properties [FOR ALL CLASSES]
		Access to all protected properties of inheriting classes
		@ret : SPECIAL
		@use : $a = array("tableName" => "tbl_user", ... ...);
	/= ===========================================================================================*/
	public function config($option, $setting = ""){
		if(is_array($option) === true){
			foreach($option as $key => $value){
				$this->$key = $value;
			}
			return true;
		}else{
			if(isset($setting) === true){
				$this->$option = $setting;
				return true;
			}else{
				return false;
			}
		}
	}
	
	/* =========================================================================================== /
		METHOD[p]: Error Handing [FOR ALL CLASSES]
		@ret : NULL||PRINT
	/= ===========================================================================================*/
	public function Output($Data, $Options = "print"){
		$Options = strtolower("-".$Options);
		
		// Output as last error
		if(stripos($Options, "err")){
			$this->LastError = $Data;
		}
		
		// Print to page
		if(stripos($Options, "print")){
			print_r($Data);
		}
	}
	
	/* =========================================================================================== /
		METHOD[p]: Get Last Error
		@ret : STR
	/= ===========================================================================================*/
	public function GetLastError(){
		return $this->LastError;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Get System Message
		@ret : STR
	/= ===========================================================================================*/
	public function SystemMessage(){
		return $this->SystemMessage;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Logs error in database [FOR ALL CLASSES]
		@ret : NULL
	/= ===========================================================================================*/
	public function Log($logAction, $user = ""){
		$logAction = SecurityManagement::Filter($logAction, "-a -n -s -y[\"'!:_;*|.-,/\[] -di -t");
		$user = SecurityManagement::Filter($user, "-n -di");
		
		if(SecurityManagement::EmptyCheck($logAction, "-not-empty")){
			// Determine who to log under
			if(SecurityManagement::EmptyCheck($user, "-is-empty")){ // No user specified
				if(SessionManagement::UserIn()){ // User logged in
					$user = SessionManagement::UserIn("uid");
				}else{
					$user = 1; // SYSTEM (Correlates/DB)
				}
			}
			
			$db = new DatabaseAbstract;
			$db->Query("INSERT INTO log (action, userID) VALUES ('$logAction', $user)");
		}
	}
}
?>