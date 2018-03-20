<?php
	// Determine view permission
	$session->RedirectIfNotLoggedIn();
	
	// LOAD INCOMMING VARS
	$ListingID = $secure->Filter($_REQUEST['e'], '-n');
	
	// FILTER VARS
	$industry = $secure->Filter($industry, '-t -ee');
	$organisation = $secure->Filter($organisation, '-t -ee');
	$description = $secure->Filter($description, '-t -ee');
	$phone = $secure->Filter($phone, '-t -ee');
	$URL = $secure->Filter($URL, '-t -ee');
	$email = $secure->Filter($email, '-t -ee');
	
	// ADD EVENT
	if($submit == 'Add' || $submit == 'Save'){
		// Check Filled
		$FieldSelection = array($industry, $country, $organisation, $description);
		if($secure->EmptyCheck($FieldSelection, '-not-empty -aggregate')){
			// MANAGE LOGO UPLOAD
			$logo = $_FILES['logo']['name'];
			if($logo){
				$filename = stripslashes($logo); // grab filename
				// Check image extension
				$extension = substr($filename, -3);
				if(!in_array($extension, $AllowedUploadExtensions)){
					$Err = 'Logo image filetype not allowed.';
				}else{
					$SourceFilename = $_FILES['logo']['tmp_name'];
					// Check image filesize
					$filesize = filesize($SourceFilename);
					if($filesize > MAX_UPLOAD_SIZE*1024){
						$Err = 'Logo image filesize is larger then '.MAX_UPLOAD_SIZE.'kb.';
					}else{
						// Resize the image to aspect
						$RealType = exif_imagetype($SourceFilename);
						switch($RealType){
							case 1: // gif
								$im = imagecreatefromgif($SourceFilename);
							break;
							case 2 : // jpeg
								$im = imagecreatefromjpeg($SourceFilename);
							break;
							case 3 : // png
								$im = imagecreatefrompng($SourceFilename);
							break;
							default: // format not support
							break;
						}
						if(!isset($im)){
							$Err = 'Logo image is not of supported format.';
						}else{
							$w = imagesx($im);
							$h = imagesy($im);
							$mwh = DIRECTORY_LISTING_LOGO_MAXWIDTH_OR_HEIGHT;
							// Determine if aspect ratio resizing is actually necessary
							if($w != $mwh || $h > $mwh){
								$AspectRatio = $w < $h ? $mwh/$h : $mwh/$w ; // Calculate aspect ratio
								// Calculate final sizes
								$fW = round($w * $AspectRatio);
								$fH = round($h * $AspectRatio);
								
								// Create final image
								$FinalImage = imagecreatetruecolor($fW, $fH);
								imagecopyresampled($FinalImage, $im, 0, 0, 0, 0, $fW, $fH, $w, $h);
								switch($RealType){
									case 1: // gif
										$imNew = imagegif($FinalImage, $SourceFilename);
									break;
									case 2 : // jpeg
										$imNew = imagejpeg($FinalImage, $SourceFilename);
									break;
									case 3 : // png
										$imNew = imagepng($FinalImage, $SourceFilename);
									break;
									default: // should never happen; taking pragmatic approach
										$Err = 'Image not supported on second attempt.';
									break;
								}
								imagedestroy($FinalImage);
							}
							imagedestroy($im);
							
							// Move to final destination
							$FinalLogoName = md5(time()).'.'.$extension;
							$FinalDestination = DIRECTORY_LISTING_LOGO_STORE.'/'.$FinalLogoName;
							$res = move_uploaded_file($SourceFilename, $FinalDestination);
							// Check image successfully transferred to server
							if(empty($res)){
								$Err = 'Transfer error occurred.';
							}else{
								$Status = 'Image successfully uploaded.';
							}
						}
					}
				}
				$db->config('SystemMessage',
					!empty($Err)?$Err.'<br />Logo could not be uploaded.':''
				);
			}
			
			// MANAGE LISTING
			$_POST['user'] = $session->UserIn('uid');
			
			$_POST['industry'] = ucwords(strtolower($industry));
			$_POST['organisation'] = $organisation;
			$_POST['description'] = nl2br($description);
			$_POST['phone'] = $phone;
			$_POST['URL'] = $URL;
			$_POST['email'] = $email;
			
			$Listing = array('industry', 'country', 'organisation', 'description', 'phone', 'URL', 'email', 'user');
			
			// Determine logo addition
			if(!empty($FinalLogoName)){
				array_unshift($Listing, 'logo'); // add logo
				$_POST['logo'] = $FinalLogoName;
			}
			
			// Check for duplicate listing title
			$organsiation = $secure->Filter($_POST['organisation'], '-t -di -ee');
			$NameNotDuplicated = true;
			if($db->CountRecords('directories', "organisation = '".$organsiation."'")){
				$db->config('SystemMessage', 'A listing with the same name already exists.');
				$NameNotDuplicated = false;
			}
			
			// Determine action
			if($submit == 'Add'){
				if($NameNotDuplicated){
					$sql = $db->Array2QueryStr('directories', $Listing, 'insert', $_POST);
				}
			}else{
				// Check for title change
				$CanEdit = false;
				$OldOrganisation = $db->GetScalar('directories', 'organisation', "id = $ListingID");
				if($OldOrganisation == $_POST['organisation']){ // title unchanged
					$CanEdit = true;
				}else{
					if($NameNotDuplicated) $CanEdit = true;
				}
				// Check if can edit
				if($CanEdit){
					if(!empty($FinalLogoName)){
						$OldLogoFile = $db->GetScalar('directories', 'logo', "id = $ListingID");
						@unlink(DIRECTORY_LISTING_LOGO_STORE.'/'.$OldLogoFile);
					}
					$sql = $db->Array2QueryStr('directories', $Listing, 'update', $_POST, 'id = '.$ListingID);
				}
			}
			if(empty($Err)){			
				if(!empty($sql)){
					$db->config('SystemMessage', 
						$db->Query($sql) ? 'Directory listing has been saved.' : 'There was a problem adding the directory listing.' //.$db->GetLastError()
					);
				}
			}
		}else{
			$db->config('SystemMessage', 'You must fill out all fields marked with a *.');
		}
	}
	
	// EDIT EVENT
	if(!empty($ListingID) && $submit != 'Save'){
		$db->Query(str_replace('%id%', $ListingID, $SQLProc['ShowDirectory']));
		$Listing = $db->Fetch();
		// Set Fields
		$industry = $Listing->industry;
		$name = $Listing->country;
		$organisation = $Listing->name;
		$description = $Listing->description;
		$phone = $Listing->phone;
		$URL = $Listing->URL;
		$email = $Listing->email;
	}
	
	// DETERMINE ACTION WORDS
	if(!empty($ListingID)){
		$ActionTitle = 'Edit';
		$ActionWord = 'Save';
	}else{
		$ActionTitle = 'Add New';
		$ActionWord = 'Add';
	}
?>
<div style="float:right">
<a class="ActionButton" href="/directory-admin/?t=1">
    <img src="/image/button/back.png" alt="Admin Home" border="0"/>
    <span>Directory Administration Home</span>
</a>
</div>
<h1><?php echo $ActionTitle; ?> Listing</h1>
<div style="clear:both;margin-top:10px">&nbsp;</div>
<div>
	<p>Please complete the form below:</p>
	<p><strong>Note:</strong> Required fields are marked with an asterisk (<em>*</em>)</p>
    <?php if($db->SystemMessage() != ''){ ?><div class='FormNotice'><?php echo $db->SystemMessage(); ?></div><?php } ?>
	<form id="new" action="" method="post" enctype="multipart/form-data">
		<ol>
	        <li>
        	    <fieldset>
					<label for="logo">Logo:</label>
					<input name="logo" id="logo" type="file" title="Logo" />
                    <span style="display:block;font-size:0.8em;">
                    	Supported file formats are: <?php foreach($AllowedUploadExtensions as $Format){ echo ' .'.$Format; } ?>
					</span>
                    <span style="display:block;font-size:0.8em;">
                    	Images larger then <?php echo DIRECTORY_LISTING_LOGO_MAXWIDTH_OR_HEIGHT; ?> x <?php echo DIRECTORY_LISTING_LOGO_MAXWIDTH_OR_HEIGHT; ?> pixels will be automatically scaled down.
					</span>
				</fieldset>
			</li>
	        <li>
				<fieldset>
					<label for="industry">Industry:<em>*</em></label>
					<input name="industry" id="industry" type="text" title="Industry" value="<?php echo $secure->Filter($industry, '-do'); ?>" maxlength="45" />
				</fieldset>
			</li>	
	        <li>
				<fieldset>
					<label for="country">Country:<em>*</em></label>
					<select name="country" id="country">
                    	<?php $Country = $app->GetListOf('Countries'); foreach($Country as $name => $id){ ?>
                        <option value="<?php echo $id; ?>"<?php if($id == $country){ echo SELECTED; } ?>><?php echo $name; ?></option>
                        <?php } ?>
					</select>
				</fieldset>
			</li>
	        <li>
             	<fieldset>
					<label for="organisation">Name:<em>*</em></label>
					<input name="organisation" id="organisation" type="text" title="Organisation Name" value="<?php echo $secure->Filter($organisation, '-do'); ?>" maxlength="150" />
				</fieldset>
			</li>
			<li>
                <fieldset>
					<label for="description">Description:<em>*</em></label>
					<textarea cols="50" rows="4" name="description" id="description" title="Organisation Description"><?php echo $secure->Filter($description, '-do'); ?></textarea>
				</fieldset>
			</li>
	        <li>
				<fieldset>
					<label for="phone">Phone:</label>
					<input name="phone" id="phone" type="text" title="Phone" value="<?php echo $secure->Filter($phone, '-do'); ?>" maxlength="20" />
				</fieldset>
			</li>
	        <li>
                <fieldset>
					<label for="url">Website:</label>
					<input name="URL" id="url" type="text" title="Website" value="<?php echo $secure->Filter($URL, '-do'); ?>" maxlength="250" />
				</fieldset>
			</li>
	        <li>
				<fieldset>
					<label for="email">Email:</label>
					<input name="email" id="email" type="text" title="Email" value="<?php echo $secure->Filter($email, '-do'); ?>" maxlength="250" />
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