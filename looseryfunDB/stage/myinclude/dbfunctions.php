<?php
define('GRANT_GUEST', 0);
define('GRANT_ADMIN', 1);
define('GRANT_EDIT', 2);
define('STATUS_WAIT', 0);
define('STATUS_ACTIVE', 1);
define('STATUS_DISABLE', 1);
define('GETFROM_DROP', 0);
define('GETFROM_SMITHSHOP', 1);
define('GETFROM_QUEST', 2);
define('GETFROM_LIMITED', 3);

$pdo=null;
$lastStatement=null;
$lastSQL=null;
$transactionCount=0;
/**
 * DB接続
 */
function connectDB(){
	global $pdo,$lastStatement;
	if(isset($pdo))return;
try {
	$pdo = new PDO('mysql:host=localhost;dbname=looseryfun;charset=utf8','looseryfun','looseryJiro3fun',
		array(PDO::ATTR_EMULATE_PREPARES => false));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	exit('データベース接続失敗。'.$e->getMessage());
}
}
/**
 * トランザクション開始
 */
function beginTransaction(){
	global $pdo,$lastStatement,$transactionCount;
	connectDB();
	if($transactionCount==0){
		$pdo->beginTransaction();
	}
	$transactionCount++;
}
/**
 * トランザクションコミット
 */
function commitTransaction(){
	global $pdo,$lastStatement,$transactionCount;
	connectDB();
	$transactionCount--;
	if($transactionCount==0){
		$pdo->commit();
	}
}
/**
 * トランザクションロールバック
 */
function rollbackTransaction(){
	global $pdo,$lastStatement,$transactionCount;
	connectDB();
	$transactionCount--;
	if($transactionCount==0){
		$pdo->rollBack();
	}
}
/**
 * エラー文章取得
 */
function getDBErrorString(){
	global $pdo,$lastStatement,$lastSQL;
	if(!isset($lastStatement))return "";
	if($lastStatement===false)return "prepare error:".$lastSQL;
	$errInfo = $lastStatement->errorInfo();
	if(!isset($errInfo))return "";
	if(!isset($errInfo[1])||$errInfo[1]==0)return "";
	return implode(':',$errInfo);
}

function dumpPrepareParams(){
	global $pdo,$lastStatement,$lastSQL;
	if($lastStatement)$lastStatement->debugDumpParams();
}

/**
 * SQLの実行
 */
function execSQL($sql,$params){
	connectDB();
	global $pdo,$lastStatement,$lastSQL;
	$lastSQL = $sql.var_export($params,true);
	$lastStatement = $pdo->prepare($sql);
	if($lastStatement==false)return false;
	if( !$lastStatement->execute($params) )return false;
	return $lastStatement;
}

/**
 * IDだけのレコード取得
 * @return fase or array
 */
function getIDs($sql,$params){
	$records = getSQLRecords($sql,$params);
	if(!$records)return false;
	$result = array();
	foreach($records as $row){
		foreach($row as $value){
			array_push($result,$value);
			break;// 最初の一つだけ
		}
	}
	return $result;
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
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * data[id]=value or data[id]=array(values) の形にして取得
 * @return fase or array
 */
function getSQLKeyValueRecords($sql,$params){
	$stmt = execSQL($sql,$params);
	if($stmt==false)return false;
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if(count($result)<=0)return $result;
	$primaryKey = array_keys($result[0])[0];
	$returnValue = array();
	$valuesCounts = count($result[0]);
	foreach($result as $row){
		$myKey = $row[$primaryKey];
		unset($row[$primaryKey]);
		switch($valuesCounts){
			case 1:
				$returnValue[$myKey]=null;
				break;
			//case 2;
			//	$returnValue[$myKey]=array_values($row)[0];
			//	break;
			default:
				$returnValue[$myKey]=$row;
				break;
		}
	}
	return $returnValue;
}

/**
 * 最後のINSERTのID取得
 * @return
 */
function getInsertID(){
	global $pdo,$lastStatement;
	connectDB();
	return $pdo->lastInsertId();
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

/**
 * ユーザーリスト取得
 * @return array
 */
function getUserList(){
	return getSQLRecords("select userentry.grant, userentry.name from userentry",array());
}

/**
 * ユーザーリスト取得
 * @return
 */
function addUser($newID, $newPass){
	$params=array('name'=>$newID, 'pass'=>$newID);
	return execSQL("INSERT INTO `userentry` (`grant`, `name`, `pass`) VALUES ('2', :name, SHA1(:pass)) ",$params);
}

/**
 * ユーザー権限更新
 * @return
 */
function changeUserGrant($userID, $newState){
	$params=array('name'=>$userID, 'grant'=>$newState);
	return execSQL("UPDATE `userentry` SET `grant` = :grant WHERE `userentry`.`name` = :name ",$params);
}



?>
