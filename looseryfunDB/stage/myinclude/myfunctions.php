<?php

include_once 'dbfunctions.php';
/**
 * 編集可能なユーザーか
 */
function startSession(){
	if(session_id()!="")return false;
	return session_start();
}

/**
 * 権限セット
 */
function setSessionUser($user,$grant){
	$_SESSION['user']=$user;
	$_SESSION['grant']=$grant;
}

/**
 * 権限削除
 */
function deleteSessionUser(){
	unset($_SESSION['user']);
	unset($_SESSION['grant']);
}

/**
 * 編集可能なユーザーか
 */
function isEditUser(){
	if(!isset($_SESSION['grant']))return false;
	$grant = $_SESSION['grant'];
	if($grant==GRANT_ADMIN)return true;
	if($grant==GRANT_EDIT)return true;
	return false;
}

/**
 * 管理者ユーザーか
 */
function isAdminUser(){
	if(!isEditUser())return false;
	return ($_SESSION['grant']==GRANT_ADMIN);
}
/**
 * ログイン中か
 */
function isLogin(){
	return isEditUser();
}
/**
 * ユーザー名取得
 */
function getUserName(){
	return $_SESSION['userName'];
}
/**
 * リダイレクト
 */
function redirectPage($url){
	header('Location: '.$url);
	exit();
}

/**
 * エスケープ
 */
function htmlEscape($string){
	return htmlspecialchars($string);
}

startSession();
?>
