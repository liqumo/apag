<?php
// Integrity Checker Class
class SecurityManagement {
	/* =========================================================================================== /
		METHOD[p]: Check Page Existence
		@inp : STR, STR
		@ret : BOOL
	/= ===========================================================================================*/
	function CheckPage($Page, $Type){
		return file_exists($Page.'.'.$Type);
	}
	
	/* =========================================================================================== /
		METHOD[p]: String Filtering
		@inp : STR,STR || [ARR,ARR]
		@ret : STR (FILTERED) || ARR (FILTERED)
	/= ===========================================================================================*/
	public function Filter($String, $Options = "-t"){
		$filter = '';
		
		// Check Multivalues
		if(!is_array($String)){
			$str = array($String);
		}else{
			$str = $String;
		}
		if(!is_array($Options)){
			$Option = "-".strtolower($Options);
			$option = array($Options);
			$s = true; // Single Value
		}else{
			$option = $Options;
			$s = false;
		}
		
		for($x=0;$x<count($str);$x++){
			if(!empty($str[$x])){
				if(stripos( ($s)?$option[0]:$option[$x] , "t")){ // Trim
						$str[$x] = trim($str[$x]);
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "s")){ // Allow Space
					$filter .= " ";
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "a")){ // Allow Alpha
					$filter .= "a-zA-Z";
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "n")){ // Allow Numeric
					$filter .= "0-9";
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "y")){ // Allow Symbol
					preg_match_all("/y\[(.*?)\]/", ($s)?$option[0]:$option[$x] , $sym);
					for($i=0;$i<strlen($sym[1][0]);$i++){
						if($sym[1][0][$i] == "["){ // If [ is allowed, then allow ]
							$filter .= "\\]";
						}
						$filter .= "\\".$sym[1][0][$i];
					}
				}
			
				// Perform Filtering
				if($filter != ""){
					preg_match_all("/[$filter]+/", $str[$x], $matches);

					echo "=======";
					echo "<pre>";
					print_r ($matches);
					echo "</pre>";

					$str[$x] = implode("", $matches[0]);
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "lc")){ // Case Convert : Lower
					$str[$x] = strtolower($str[$x]);
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "uc")){ // Case Convert : Upper
					$str[$x] = strtoupper($str[$x]);
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "di")){ // Prepare : DB Input
					$str[$x] = DatabaseAbstract::PrepareDBInput($str[$x]);
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "do")){ // Prepare : DB Output
					$str[$x] = DatabaseAbstract::PrepareDBOutput($str[$x]);
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "ee")){ // HTML Entity : Encode
					$str[$x] = htmlentities($str[$x]);
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "ed")){ // HTML Entity : Decode
					$str[$x] = html_entity_decode($str[$x]);
				}
				if(stripos( ($s)?$option[0]:$option[$x] , "lg")){ // Length Truncate
					preg_match_all("/lg\[([0-9]+?)\]/", ($s)?$option[0]:$option[$x] , $len);
					$str[$x] = substr($str[$x], 0, $len[1][0]);
				}
				
				$filter = ""; // Reset Filter
			}
		}
		
		// Determine Return Type
		if($s){ // String
			return $str[0];
		}else{ // Array
			return $str;
		}
	}
	
	/* =========================================================================================== /
		METHOD[p]: Check variable or variable set is/isn't empty
		@inp : (STR, ARR), STR[-is-empty, -not-empty [-aggregate]]
		@ret : T||F (STR) || T||F (ARR)
	/= ===========================================================================================*/
	public function EmptyCheck($in, $method = "is"){
		$method = "-".strtolower($method);
		
		if(!is_array($in)){
			$proc = array($in);
			$s = true;
		}else{
			$proc = $in;
			$s = false;
		}
		
		// Perform check
		for($i=0;$i<count($proc);$i++){
			// Check input
			if(trim($proc[$i]) == "" || empty($proc[$i])){ // Empty
				(stripos($method, "is"))?$ret[] = true:$ret[] = false;
			}else{
				(stripos($method, "is"))?$ret[] = false:$ret[] = true;
			}
		}
		
		// Determine return
		if($s){
			return $ret[0]; // String
		}else{
			if(stripos($method, "aggregate")){ // Check all values are same
				$assume = true;
				for($i=0;$i<count($ret);$i++){
					if($ret[$i] == false){
						$assume = false;
					}
				}
				return $assume; // String
			}else{
				return $ret; // Array
			}
		}
	}
}
?>