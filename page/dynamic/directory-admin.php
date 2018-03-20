<?php
	// Determine view permission
	$session->RedirectIfNotLoggedIn();
	
	// Listing Removal
	$RemoveID = $secure->Filter($_REQUEST['r'], '-n');
	if($RemoveID != ''){
		$LogoName = $db->GetScalar('directories', 'logo', "id = $RemoveID");
		$result = $db->Query(str_replace('%id%', $RemoveID, $SQLProc['DeleteListing']));
		$db->config('SystemMessage', !$result ? 'There was a problem removing the item.' : '');
		@unlink(DIRECTORY_LISTING_LOGO_STORE.'/'.$LogoName);
	}
?>
<script type="text/javascript">
	function Remove(id){
		if(confirm("Are you sure you'd like to remove this listing?")){
			window.location.href='/directory-admin/?t=1'+'&'+'r='+id;
		}
	}
</script>
<div style="float:right">
<a class="ActionButton" href="/admin/?t=1">
    <img src="/image/button/back.png" alt="Add a new event" border="0"/>
    <span>Administration Home</span>
</a>
</div>
<h1>Professional Directory Listing</h1>
<div style="clear:both;margin-top:10px">&nbsp;</div>
<a class="ActionButton" href="/directory-admin-action/?t=1">
    <img src="/image/button/add.png" alt="Add a new event" border="0"/>
    <span>Add New Listing</span>
</a>
<div style="clear:both;margin-top:10px">&nbsp;</div>
<?php if($db->SystemMessage != ''){?>
<div class="FormNotice"><?php echo $db->SystemMessage; ?></div>
<?php } ?>
<?php include_once('page/dynamic/directory-filter.php'); ?>
<div id="AllDirectories">
	<?php
        $sql = $SQLProc['ShowDirectoriesAdmin'];
        $FilterField = array(
        	'organisation' => $organisation,
            'industry' => $industry,
            'country' => $country
        );
		
		if(!empty($organisation) || !empty($industry) || !empty($country)){
			$cond = 'WHERE ';
            foreach($FilterField as $Field => $Value){
            	if($Value != ''){
	            	$cond .= $Field.' LIKE "%'.$Value.'%" AND  ';
				}
            }
            $cond = substr($cond, 0, -5);
		}else{
			$cond = '';
		}
        $sql = str_replace('%condition%', $cond, $sql);
        
    	// Grab all current events
		$db->Query($sql);
		$Listing = $db->FetchAll();
		$totListings = count($Listing['id']);
		if($totListings > 0){
			for($i=0;$i<$totListings;$i++){
				// Check for logo
				$Listing['logo'][$i] = $Listing['logo'][$i] == 0 ? '../../'.DIRECTORY_NO_LOGO_IMAGE : $Listing['logo'][$i];
    ?>
	<div class="AdminDirectoryList">
    	<div style="float:right;margin-right:2px;">
            <a class="ActionButton" href="/directory-admin-action/?t=1&amp;e=<?php echo $Listing['id'][$i]; ?>">
                <img src="/image/button/edit.png" alt="Edit this listing" border="0" />
                <span>Edit</span>
            </a>
            <a class="ActionButton" href="javascript:Remove(<?php echo $Listing['id'][$i]; ?>)">
                <img src="/image/button/delete.png" alt="Delete this listing" border="0" />
                <span>Remove</span>
            </a>
        </div>
        <h2><?php echo $secure->Filter($Listing['name'][$i], '-do'); ?></h2>
        <ul>
        	<li>
		        <img src="/image/user/directory/<?php echo $Listing['logo'][$i]; ?>" alt="<?php echo $secure->Filter($Listing['name'][$i], '-do'); ?>" border="0" />
            </li>
        </ul>
        <div class="description">
        	<?php echo $secure->Filter($Listing['description'][$i], '-do'); ?>
        </div>
        <div style="clear:both"></div>
        <h3>Total Hits: <?php echo $secure->Filter($Listing['clicks'][$i], '-do'); ?></h3>
	</div>
    <?php
    		}
    	}else{
    ?>
    <div class="event">
		<h2>No Directory Listings</h2>
	</div>
    <?php
    	}
    ?>
</div>