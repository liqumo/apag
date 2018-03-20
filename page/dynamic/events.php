<div id="AllEvents">
	<?php
		// Grab all current events
		$sql = $SQLProc['ShowEvents'];
		$db->Query($sql);
		$Event = $db->FetchAll();
		$totEvents = count($Event['id']);
		if($totEvents > 0){
			for($i=0;$i<$totEvents;$i++){
			?>
	<div class="event">
        <h2><?php echo $Event['title'][$i]; ?></h2>
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