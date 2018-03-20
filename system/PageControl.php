<?php
// Check requested page is valid
$SubDirectory = ($PageTypeDefinition[$t] == 'dynamic')?'dynamic/':'';
if($secure->CheckPage(PAGE.'/'.$SubDirectory.$p, $t)){
	include(PAGE.'/'.$SubDirectory.$p.'.'.$t);
}else{ // Error 404: Logical page doesn't exist
	include(SYSTEM.'/Error404.php');
}
?>