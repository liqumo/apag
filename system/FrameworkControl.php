<?php
# INDUSTRY PROJECT - SWINBURNE UNIVERSITY
define('SUBDIRECTORY', '');
define('PAGE', 'page');
define('SYSTEM', 'system');
require_once('lib/Initialise.php');

# Manage FURL
$FURL = str_replace(SUBDIRECTORY, '', $_SERVER['REQUEST_URI']);
$RelCnt = count(explode('/', $FURL));
$RelCal = '';
for($i=0;$i<$RelCnt-1;$i++){ $RelCal .= '../'; }

# Grab POST Variables
foreach($_POST as $k => $v){ $$k = $v; }

# Determine Page Location
foreach($PageTypeDefinition as $ext => $type){ $PageType[] = $ext; }
$SelectedPage = $_REQUEST['p'];
$SelectedType = is_numeric($_REQUEST['t']) ? $_REQUEST['t'] : 0;
$p = empty($SelectedPage) ? 'home' : $SelectedPage;
$t = $PageType[$SelectedType] == "" ? $PageType[0] : $PageType[$SelectedType];

# User Control
if($SelectedPage == 'admin' && $t != 'php'){
	if($session->UserIn()){
		header("Location: /admin/?t=1");
	}
}
?>