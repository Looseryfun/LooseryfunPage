<?php
define('GRANT_GUEST', 0);
define('GRANT_ADMIN', 1);
define('GRANT_EDIT', 2);

$pdo=null;;
$lastStatement=null;;
/**
 * DB接続
 */
function connectDB(){
	global $pdo,$lastStatement;
	if(isset($pdo))return;
try {
	$pdo = new PDO('mysql:host=localhost;dbname=looseryfun;charset=utf8','looseryfun','looseryJiro3fun',
	array(PDO::ATTR_EMULATE_PREPARES => false));
} catch (PDOException $e) {
	exit('データベース接続失敗。'.$e->getMessage());
}
}

/**
 * SQLの実行
 */
function execSQL($sql,$params){
	connectDB();
	global $pdo,$lastStatement;
	$lastStatement = $pdo->prepare($sql);
	if($lastStatement==false)return false;
	$lastStatement->execute($params);
	return $lastStatement;
}

/**
 * 一行だけのレコード取得
 * @return fase or array
 */
function getSQLRecord($sql,$params){
	$stmt = execSQL($sql,$params);
	if($stmt==false)return false;
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * 全レコード取得
 * @return fase or array
 */
function getSQLRecords($sql,$params){
	$stmt = execSQL($sql,$params);
	if($stmt==false)return false;
	return $stmt->fetchAll();
}

/**
 * 権限取得
 * @return fase or GRANT_??
 */
function getGrant($id,$pass){
	$params=array('name'=>$id, 'pass'=>$pass);
	$row = getSQLRecord("select userentry.grant from userentry where name=:name and pass=SHA1(:pass)",$params);
	if(isset($row['grant']))return $row['grant'];
	return false;
}

?>
