<?php
	include_once 'myinclude/myfunctions.php';
	//$grant = $_SESSION['grant'];
	if(!isset($_POST['id'])||!isset($_POST['pass']))redirectPage('login.php');
	if(isLogin())redirectPage('index.php');
	$grant = getGrant($_POST['id'],$_POST['pass']);
	setSessionUser($_POST['id'],$grant);
	if(isEditUser(){
		$topPageMsg="ログインしました。"
		include 'index.php';
	}else{
		$loginPageMsg="IDまたはパスワードが違います。"
		include 'login.php';
	}
?>
