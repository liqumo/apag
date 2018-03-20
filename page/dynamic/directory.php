<div id="AllDirectories">
	<?php
        $sql = $SQLProc['ShowDirectories'];
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
				$Listing['name'][$i] = $secure->Filter($Listing['name'][$i], '-do');
				$Listing['industry'][$i] = $secure->Filter($Listing['industry'][$i], '-do');
    ?>
	<div class="directoryList">
    	<a href="/directory-detail/?i=<?php echo $Listing['id'][$i]; ?>">
        	<img src="/image/user/directory/<?php echo $Listing['logo'][$i]; ?>" alt="<?php echo $Listing['name'][$i]; ?>" border="0" />
		</a>
        <div>
        	<ul>
            	<li><span><?php echo $Listing['name'][$i]; ?></span></li>
                <li><?php echo $Listing['country'][$i]; ?></li>
            	<li><?php echo $Listing['industry'][$i]; ?></li>
            </ul>
        </div>
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