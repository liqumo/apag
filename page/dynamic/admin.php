<?php
	$LoggedIn = false;
	if($session->UserIn()){ // Logged In
		$LoggedIn = true;
	}else{ // Not Logged In
		if($submit == 'Login'){
			if(!$session->Login($username, $password)){
				$LoginMessage = "<div class='FormNotice'>".
					ucwords($session->GetLastError())
				.".</div>";
			}else{
				$LoggedIn = true;
				if($_REQUEST['v'] != '1'){
					?><script type="text/javascript">window.location.href = '?t=1&v=1';</script><?php
				}
			}
		}
	}
	if(!$LoggedIn){ // Attach Administration Login Form
		include_once("./page/admin.html");
	}else{ // Show Administration Options
?>
<h1>Administration Home</h1>
<p>Welcome to the site administration page. Please select an option below to begin.</p>
<div id="AdminMenu">
    <ol>
        <li>
        	<a class="ActionButton" href="/events-admin/?t=1">
                <img src="/image/button/event.png" alt="Events" border="0"/>
                <span>Manage Events</span>
            </a>
        </li>
		<li>
        	<a class="ActionButton" href="/directory-admin/?t=1">
                <img src="/image/button/directory.png" alt="Directory" border="0"/>
                <span>Manage Professional Directory</span>
            </a>
       	</li>
		<li>
	        <a class="ActionButton" href="/logout/?t=1">
                <img src="/image/button/logout.png" alt="Logout" border="0"/>
                <span>Logout</span>
            </a>
		</li>
    </ol>
</div>
<?php
	}
?>