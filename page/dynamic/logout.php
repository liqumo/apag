<?php
	$session->Logout();
	if($_REQUEST['v'] != '1'){
?>
<script type="text/javascript">
	window.location.href = '?t=1&v=1';
</script>
<?php
	}
?>
<h1>Logout Successful!</h1>
<p>You've been successfully logged off the system.</p>
<a class="ActionButton" href="/admin" style="margin:20px 0">
    <img src="/image/button/login.png" alt="Events" border="0"/>
    <span>Login Again</span>
</a>