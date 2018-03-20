<?php
	// Determine view permission
	$session->RedirectIfNotLoggedIn();
	
	// Event Removal
	$Event2Remove = $secure->Filter($_REQUEST['r'], '-n');
	if($Event2Remove != ''){
		$result = $db->Query(str_replace('%id%', $Event2Remove, $SQLProc['DeleteEvent']));
		$db->config('SystemMessage', !$result ? 'There was a problem removing the item.' : '');
	}
?>
<script type="text/javascript">
	function Remove(id){
		if(confirm("Are you sure you'd like to remove this event?")){
			window.location.href='/events-admin/?t=1'+'&'+'r='+id;
		}
	}
</script>
<div style="float:right">
<a class="ActionButton" href="/admin/?t=1">
    <img src="/image/button/back.png" alt="Add a new event" border="0"/>
    <span>Administration Home</span>
</a>
</div>
<h1>Events Administration</h1>
<div style="clear:both;margin-top:10px">&nbsp;</div>
<a class="ActionButton" href="/events-admin-action/?t=1">
    <img src="/image/button/add.png" alt="Add a new event" border="0"/>
    <span>Add New Event</span>
</a>
<div style="clear:both;margin-top:10px">&nbsp;</div>
<?php if($db->SystemMessage != ''){?>
<div class="FormNotice"><?php echo $db->SystemMessage; ?></div>
<?php } ?>
<div id="AllEvents">
	<?php
		// Grab all current events
		$sql = $SQLProc['ShowEvents'];
		$db->Query($sql);
		$Event = $db->FetchAll();
		$totEvents = count($Event['id']);
		//print_r($Event);
		if($totEvents > 0){
			for($i=0;$i<$totEvents;$i++){
			?>
	<div class="event">
    	<div style="float:right;margin-right:2px;">
            <a class="ActionButton" href="/events-admin-action/?t=1&amp;e=<?php echo $Event['id'][$i]; ?>">
                <img src="/image/button/edit.png" alt="Edit this event" border="0" />
                <span>Edit</span>
            </a>
            <a class="ActionButton" href="javascript:Remove(<?php echo $Event['id'][$i]; ?>)">
                <img src="/image/button/delete.png" alt="Delete this event" border="0" />
                <span>Remove</span>
            </a>
        </div>
        <h2><?php echo $secure->Filter($Event['title'][$i], '-do'); ?></h2>
        <ul>
            <li>
                <span>Date: </span>
                <?php echo date('jS F Y', $Event['eventDate'][$i]); ?>
            </li>
            <li>
                <span>Time: </span>
                <?php echo $secure->Filter($Event['eventTime'][$i], '-do'); ?>
            </li>
            <li>
                <span>Location: </span>
                <?php echo $secure->Filter($Event['location'][$i], '-do'); ?>
            </li>
            <li>
                <span>Description: </span>
                <?php echo $secure->Filter($Event['description'][$i], '-do'); ?>
            </li>
        </ul>
	</div>
            <?php
			}
		}else{ // no events
		?>
	<div class="event">
		<h2>Currently No Events</h2>
	</div>
        <?php
		}
	?>
</div>