<?php require_once('system/FrameworkControl.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Asia Pacific Advisory Group</title>
<link href="<?php echo $RelCal; ?>script/layout.css?<?php echo rand()*9999; ?>" rel="stylesheet" type="text/css" media="screen" />
<link href="<?php echo $RelCal; ?>script/forms.css?<?php echo rand()*9999; ?>" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>
    <div id="head">
    	<?php if($session->UserIn()){ ?>
        <div class="tabs">
            <a class="TopTab" href="/logout/?t=1">Logout</a>
            <a class="TopTab" href="/admin">Admin</a>
            <div class="TopTab">Logged in as: <strong><?php echo $session->UserIn('user'); ?></strong></div>
        </div>
		<?php } ?>
    	<div id="innerHead">
			<a href="/home">
				<img src="<?php echo $RelCal; ?>image/interface/logo.png" alt="APAG" border="0" style="margin:10px 0 0 0" />
			</a>
		</div>
    </div>
    <div id="menuBg">
        <div id="menu">
            <ul>
                <li><a href="/home">Home</a></li> 
                <li><a href="/about">About APAG</a></li>
                <li><a href="/membership">Membership</a></li>
                <li><a href="/directory">Directory</a></li>
                <li><a href="/events">Events</a></li>
                <li><a href="/welcoming_committee">Welcoming Committee</a></li>
                <li><a class="last" href="/contact">Contact Us</a></li>
            </ul>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="middle">
    	<div id="innerMiddle">
        	<p>
            	"A journey of a thousand miles begins with the first step"
            	<span>-Ancient Scholar</span>
            </p>
        	<img src="<?php echo $RelCal; ?>image/interface/topBG.png" alt="workgroup" border="0" />
        </div>
    </div>
    <div id="middleGlow">
	    <div class="right">&nbsp;</div>
    	<div class="left">&nbsp;</div>
    </div>
    <div id="content">
    	<div id="main">
			<?php include_once('system/PageControl.php'); ?>
		</div>
    </div>
    <div id="footer">
    	<div id="innerFooter">&nbsp;</div>
    </div>
</body>
</html>