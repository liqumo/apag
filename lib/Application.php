<?php
// APPLICATION : Concrete Placeholder Class
class Application extends masterCls {
	# Configurable Properties
	
	# Class Properties
	
	/* =========================================================================================== /
		CONSTRUCT
	/= ===========================================================================================*/
	function __construct(){
		
	}
	/* =========================================================================================== /
		DESTRUCT
	/= ===========================================================================================*/
	function __destruct(){
		
	}
	
	/* =========================================================================================== /
		METHOD[p]: Get List Of Items
		@inp : STR
		@ret : ARR
	/= ===========================================================================================*/
	function GetListOf($Item){
		global $SQLProc;
		if(!empty($Item)){
			$Item = ucwords($Item);
			
			$db = new DatabaseAbstract;
			$db->Query($SQLProc['List'.$Item]);
			$ItemList = $db->FetchAll();
			for($i=0;$i<count($ItemList['id']);$i++){
				$Items[$ItemList['name'][$i]] = $ItemList['id'][$i];
			}
		}
		if(count($Items) < 1){
			$Items['No '.$Item] = '';
		}
		return $Items;
	}
}
?>