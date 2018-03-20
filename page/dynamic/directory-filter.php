<?php
	// Determine Filter
    $organisation = $secure->Filter($_REQUEST['n'], '-t -s -a -n -lc -di -y[&:-_\']');
    $industry = htmlspecialchars($secure->Filter($_REQUEST['i'], '-t -s -a -n -lc -di -y[&:-_\']'));
    $country = $secure->Filter($_REQUEST['c'], '-t -n -di');
?>
<div class="filter">
	<h2>Directory Filter</h2>
	<form id="filter" action="" method="get">
		<ol>
			<li>
				<fieldset>
					<label for="filtername">Name:</label>
					<input name="n" id="filtername" type="text" value="<?php echo stripslashes($organisation); ?>" />
				</fieldset>
			</li>
			<li>
				<fieldset>
					<label for="filterindustry">Industry:</label>
                    <select name="i" id="filterindustry">
                    	<option value="">[Select Industry]</option>
                    	<?php $Industries = $app->GetListOf('Industries'); foreach($Industries as $name => $id){ ?>
                        <option value="<?php echo $name; ?>"<?php if(strtolower($name) == strtolower($industry)){ echo SELECTED; } ?>><?php echo $name; ?></option>
                        <?php } ?>
					</select>
				</fieldset>
			</li>
			<li>
				<fieldset>
					<label for="filtercountry">Country:</label>
					<select name="c" id="filtercountry">
                    	<option value="">[Select Country]</option>
                    	<?php $Country = $app->GetListOf('Countries'); foreach($Country as $name => $id){ ?>
                        <option value="<?php echo $id; ?>"<?php if($id == $country){ echo SELECTED; } ?>><?php echo $name; ?></option>
                        <?php } ?>
					</select>
				</fieldset>
			</li>
		</ol>
        <button name="do" type="submit" value="filter">Filter</button>
        <input type="hidden" name="t" value="<?php if(!empty($_REQUEST['t'])) echo $_REQUEST['t']; ?>" />
        <div style="clear:both"></div>
	</form>
</div>