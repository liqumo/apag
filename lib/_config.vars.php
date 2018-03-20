<?php
	// GLOBAL SITE CONFIGURATION VARS
	// Database Connection Settings
	define("DATABASE_HOST", "localhost");
	define("DATABASE_USER", "root");
	define("DATABASE_PASS", "digitalage44");
	define("DATABASE_DBNM", "apag");
	
	// Constants
	define("USERNAME_LENGTH", 25);
	define("PASSWORD_LENGTH", 25);
	// Directory listing logo upload directory from root
	define("DIRECTORY_NO_LOGO_IMAGE", "nologo.png"); // from root
	define("DIRECTORY_LISTING_LOGO_STORE", "image/user/directory"); // no trailing slash
	define("DIRECTORY_LISTING_LOGO_MAXWIDTH_OR_HEIGHT", 128);
	define("MAX_UPLOAD_SIZE", 1024); // KB
	
	// Pagetype Control
	$PageTypeDefinition = array(
		'html' => 'static',
		'php' => 'dynamic'
	);
	
	// Upload Extension Control
	$AllowedUploadExtensions = array(
		'png', 'jpg', 'gif'
	);
?>