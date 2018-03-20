<?php
	// Determine view permission
	$session->RedirectIfNotLoggedIn();
	
	// LOAD INCOMMING VARS
	$EventID = $secure->Filter($_REQUEST['e'], '-n');
	
	// FILTER VARS
	$title = $secure->Filter($title, '-t -ee');
	$eventTime = $secure->Filter($eventTime, '-t -ee');
	$location = $secure->Filter($location, '-t -ee');
	$description = $secure->Filter($description, '-t -ee');
	
	// ADD EVENT
	if($submit == 'Add' || $submit == 'Save'){
		// Check Filled
		$FieldSelection = array($title, $year, $month, $day, $eventTime, $location);
		if($secure->EmptyCheck($FieldSelection, '-not-empty -aggregate')){
			// Check Date
			if(!checkdate($month, $day, $year)){
				$db->config('SystemMessage', 'The date selected is invalid.');
			}else{
				$_POST['eventDate'] = strtotime($month.'/'.$day.'/'.$year);
				$_POST['user'] = $session->UserIn('uid');
				
				$_POST['title'] = $title;
				$_POST['eventTime'] = $eventTime;
				$_POST['location'] = nl2br($location);
				$_POST['description'] = nl2br($description);
	
				$Event = array('title', 'location', 'description', 'eventDate', 'eventTime', 'user');
				
				// Determine action
				if($submit == 'Add'){
					$sql = $db->Array2QueryStr('events', $Event, 'insert', $_POST);
				}else{
					$sql = $db->Array2QueryStr('events', $Event, 'update', $_POST, 'id = '.$EventID);
				}
				$db->config('SystemMessage', 
					$db->Query($sql) ? 'Event has been saved.' : 'There was a problem adding the event.' //.$db->GetLastError()
				);
			}
		}else{
			$db->config('SystemMessage', 'You must fill out all fields marked with a *.');
		}
	}
	
	// EDIT EVENT
	if(!empty($EventID) && $submit != 'Save'){
		$db->Query(str_replace('%id%', $EventID, $SQLProc['ShowEvent']));
		$Event = $db->Fetch();
		// Set Fields
		$title = $Event->title;
		$eventDate = $Event->eventDate;
		$eventTime = $Event->eventTime;
		$location = $Event->location;
		$description = $Event->description;
		
		$year = date('Y', $eventDate);
		$month = date('m', $eventDate);
		$day = date('d', $eventDate);	
	}
	
	// DETERMINE ACTION WORDS
	if(!empty($EventID)){
		$ActionTitle = 'Edit';
		$ActionWord = 'Save';
	}else{
		$ActionTitle = 'Add New';
		$ActionWord = 'Add';
	}
?>
<div style="float:right">
<a class="ActionButton" href="/events-admin/?t=1">
    <img src="/image/button/back.png" alt="Admin Home" border="0"/>
    <span>Events Administration Home</span>
</a>
</div>
<h1><?php echo $ActionTitle; ?> Event</h1>
<div style="clear:both;margin-top:10px">&nbsp;</div>
<div>
	<p>Please complete the form below:</p>
	<p><strong>Note:</strong> Required fields are marked with an asterisk (<em>*</em>)</p>
    <?php if($db->SystemMessage() != ''){ ?><div class='FormNotice'><?php echo $db->SystemMessage(); ?></div><?php } ?>
	<form id="newevent" action="" method="post">
		<ol>
			<li>
				<fieldset>
					<label for="title">Title:<em>*</em></label>
					<input name="title" id="title" type="text" title="Event Title" value="<?php echo $secure->Filter($title, '-do'); ?>" maxlength="150" />
				</fieldset>
			</li>
			<li>
                <fieldset>
					<label for="date">Date:<em>*</em></label>
                    <select id="date" name="day" style="width:50px"><?php $IS = false; for($i=1;$i<32;$i++){ ?>
                    	<option value="<?php echo $i; ?>"<?php
						if($day == $i){ echo SELECTED; $IS = true; }else{
                        	if(date('d',time())==$i && !$IS){ echo SELECTED; }
						}
					?>><?php echo strlen($i) < 2 ?'0'.$i:$i; ?></option>
					<?php } ?></select> /
                    <select name="month" style="width:50px"><?php $IS = false; for($i=1;$i<13;$i++){ ?>
                    	<option value="<?php echo $i; ?>"<?php
						if($month == $i){ echo SELECTED; $IS = true; }else{
	                        if(date('m',time())==$i && !$IS){ echo SELECTED; }
						}
					?>><?php echo strlen($i) < 2 ?'0'.$i:$i; ?></option>
					<?php } ?></select> /
                    <select name="year" style="width:60px"><?php $IS = false; $CY = date('Y', time()); for($i=$CY;$i<$CY+10;$i++){ ?>
                    	<option value="<?php echo $i; ?>"<?php
						if($year == $i){ echo SELECTED; $IS = true; }else{
							if(date('Y',time())==$i && !$IS){ echo SELECTED; }
						}
					?>><?php echo $i; ?></option>
					<?php } ?></select> (dd/mm/yyyy)
				</fieldset>
			</li>
			<li>
                <fieldset>
					<label for="eventTime">Time:<em>*</em></label>
                    <input name="eventTime" id="eventTime" type="text" title="Event Time" value="<?php echo $secure->Filter($eventTime, '-do'); ?>" maxlength="45" />
				</fieldset>
			</li>
			<li>
				<fieldset>
					<label for="location">Location:<em>*</em></label>
					<textarea cols="50" rows="4" name="location" id="location" title="Event Location"><?php echo $secure->Filter($location, '-do'); ?></textarea>
				</fieldset>
			</li>
			<li>
                <fieldset>
					<label for="description">Description:</label>
					<textarea cols="50" rows="4" name="description" id="description" title="Event Description"><?php echo $secure->Filter($description, '-do'); ?></textarea>
				</fieldset>
			</li>
			<li>
                <fieldset>
					<button id="submit" name="submit" type="submit" value="<?php echo $ActionWord; ?>"><?php echo $ActionWord; ?></button>
                    <button id="reset" name="reset" type="reset" value="Clear">Revert</button>
				</fieldset>
			</li>
		</ol>
	</form>
</div>