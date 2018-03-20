<?php
// SESSION MANAGEMENT - LOGIN - SESSION - LOGOUT
class SessionManagement extends masterCls {

	# Configurable Properties
	
	
	# Class Properties
	
	
	/* =========================================================================================== /
		CONSTRUCT
	/= ===========================================================================================*/
	function __construct(){
		session_start();
	}
	/* =========================================================================================== /
		DESTRUCT
	/= ===========================================================================================*/
	function __destruct(){
		
	}
	
	/* =========================================================================================== /
		METHOD[p]: Login
		@ret : T||F
		@svr : SET SESSION
	/= ===========================================================================================*/
	public function Login($user, $pass){
		$both = array($user, $pass);
		if(SecurityManagement::EmptyCheck($both, "-not-empty -aggregate")){
			$db = new DatabaseAbstract;
			
			// Clean incomming
			$clean = SecurityManagement::Filter($both,
				array("-a -n -t -y[_] -lg[".USERNAME_LENGTH."] -di", "-a -n -y[@#$%] -t -lg[".PASSWORD_LENGTH."] -di")
			); $user = $clean[0]; $pass = $clean[1];
			
			// Grab user
			$db->Query("SELECT * FROM users WHERE username = \"$user\"");
			$account = $db->Fetch();
			
			// Check existence
			if($account->username != ""){
				// Check password
				if($account->password == md5($pass)){
					// Check login permission
					if($account->active == 1){
						$_SESSION["uid"] = $account->id;
						$_SESSION["user"] = $account->username;
						$_SESSION["role"] = $account->group;
						$_SESSION["logTime"] = time();
						
						$this->Log("logged in");
						return true;
					}else{
						$this->Output("account is disabled", 'err');
						$this->Log("failed login: account was disabled");
					}
				}else{
					$this->Output("password is incorrect", 'err');
					$this->Log("failed login: password ($pass) incorrect");
				}
			}else{
				$this->Output("unable to find user", 'err');
			} //*/
		}else{
			$this->Output("enter both username and password", 'err');
		}
		
		return false;
	}

	/* =========================================================================================== /
		METHOD[p]: Logout
		@ret : NULL
		@svr : DESTROY SESSION
	/= ===========================================================================================*/
	public function Logout(){
		unset($_SESSION);
		session_destroy();
		$this->Log("logged out");
		return true;
	}
	
	/* =========================================================================================== /
		METHOD[p]: Checks if logged in
		@inp : [STR]
		@ret : T||F, STR
	/= ===========================================================================================*/
	public function UserIn($SessionVariable = ""){
		if(!empty($_SESSION)){
			if($SessionVariable != ""){
				return $_SESSION[$SessionVariable];
			}else{
				$all = array(
					$_SESSION["uid"],
					$_SESSION["user"],
					$_SESSION["role"],
					$_SESSION["logTime"]
				);
				$ret = SecurityManagement::EmptyCheck($all, "-not-empty -aggregate");
				if($ret){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	/* =========================================================================================== /
		METHOD[p]: Checks if logged in (redirects to homepage if not)
		@inp : NULL
		@ret : NULL
	/= ===========================================================================================*/
	public function RedirectIfNotLoggedIn(){
		if(!$this->UserIn()){
			echo "<script type='text/javascript'>window.location.href='/home'</script>";
		}
	}
}
?>