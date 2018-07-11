<?php

define('URL_CACHETIME',12*60*60);

include_once 'itemclass.php';
include_once 'dbfunctions.php';

function isVariable(&$check){
	if(!isset($check))return false;
	if($check==null)return false;
	if($check==0)return false;
	return true;
}

/**
 * 編集可能なユーザーか
 */
function startSession(){
	if(session_id()!="")return false;
	return session_start();
}

/**
 * 最後に登録したアイテム
 */
function getRegstItem(){
	if(isset($_SESSION['registItemData']))return $_SESSION['registItemData'];
	return null;
}
function setRegstItem($newItem){
	$_SESSION['registItemData'] = $newItem;
	return null;
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

/**
 * HTMLの一部取得
 */
function generateHtmlData($key){
	$urlquery = explode('|',$key);
	if(count($urlquery)!=2)return "";
	$url = urldecode($urlquery[0]);
	$query = urldecode($urlquery[1]);
	
	$domDocument = new DOMDocument();
	@$domDocument->loadHTMLFile($url);
	if(!$domDocument)return "";
	$xpath = new DOMXPath($domDocument);
	// 入れ物を探す
	$nodes = $xpath->query($query);
	foreach($nodes as $node) {
	    // DOMツリーを保存
	    $line = trim($domDocument->saveXML($node));
	    if (! empty($line)) {
	        $src[] = $line;
	    }
	}
	return implode('',$src);
}

/**
 * HTMLの一部取得
 */
function getHtmlData($url, $query){
	$key = urlencode($url).'|'.urlencode($query);
	return apcu_entry($key, 'generateHtmlData', URL_CACHETIME);
}



startSession();
?>
