<?php
	include_once 'myinclude/myfunctions.php';
	if(!isEditUser())redirectPage('index.php');
	phpinfo();
?>
