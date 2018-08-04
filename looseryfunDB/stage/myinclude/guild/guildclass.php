<?php
define('GUILDDATA_CACHETIME',1*60*60);
include_once 'myinclude/myfunctions.php';

/**
 * ホームに戻る例外
 */
class GoHomeException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class GuildMember{
	protected static $inputValues=array(
		'id', 
		'name',
		'guild',
		'icon_url', 
		'publicinfo', 
		'token',
		'lastactiontime',
	);
	public static function createNewMember($name,$playerid){
		beginTransaction();
		$result = GuildMember::_createNewMember($name,$playerid);
		if($result)commitTransaction();
		else rollbackTransaction();
		return $result;
	}
	private static function _createNewMember($name,$playerid){
		if(!$playerid||strlen($playerid)<=0)$playerid = GuildMember::makeNewBrowserPlayerId();
		$sql = 'INSERT INTO `guild_member` (`name`,`token`,`lastactiontime`) VALUES (:name,uuid_short(),now())';
		if(!execSQL($sql,array('name'=>$name)))return false;
		$id = getInsertID();
		if($id<0)return false;
		return GuildMember::saveBrowserPlayerId($id,$playerid);
	}
	public static function saveBrowserPlayerId($memberid,$playerid){
		$sql = 'REPLACE INTO `guild_browser` (`playerid`,`userid`,`useragent`,`entrytime`) VALUES (:playerid,:memberid,:useragent,now())';
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$params = array(
			'playerid'=>$playerid,
			'memberid'=>$memberid,
			'useragent'=>$useragent);
		if(!execSQL($sql,$params))return false;
		GuildMember::setBroeser($memberid,$playerid);
		return true;
	}
	protected static function setBroeser($memberid,$playerid){
		$_SESSION['guild']['id']=$memberid;
		setcookie('playerid',$playerid,time()+60*60*24*365,'/');
		return true;
	}
	public static function checkBrowser(){
		$playerid = @$_COOKIE['playerid'];
		if(!$playerid)return false;
		$memberid = GuildMember::getMemberIdFromPlayerId($playerid);
		if($memberid<=0)return false;
		$_SESSION['guild']['id']=$memberid;
		GuildMember::updateAccessTime($memberid);
		return true;
	}
	protected static function getMemberIdFromPlayerId($playerid){
		$sql = "SELECT userid FROM `guild_browser` WHERE playerid=?";
		$ids = getIDs($sql,array($playerid));
		if(!$ids||count($ids)<=0)return 0;
		return $ids[0];
	}
	protected static function updateAccessTime($memberid){
		$sql = "UPDATE `guild_member` SET `lastactiontime`=now() WHERE id=?";
		execSQL($sql,array($memberid));
		apcu_delete(GuildMember::makeApcKey($memberid));
	}
	/** IOS用ID */
	protected static function makeNewBrowserPlayerId(){
		$recordes = getIDs("select LPAD(hex(uuid_short()),16,'0') as id;",array());
		return @recordes[0];
	}
	protected static function makeApcKey($memberid){
		return 'guild_member_'.$memberid;
	}
	public static function getData($memberid=0){
		if($memberid<=0)$memberid = @$_SESSION['guild']['id'];
		if($memberid<=0)return null;
		$result=apcu_entry(GuildMember::makeApcKey($memberid),
			'GuildMember::generateData',
			GUILDDATA_CACHETIME);
		return $result;
	}
	private static function generateData($key){
		if(!$key)return null;
		$split = explode('_',$key);
		$memberid = @$split[2];
		if($memberid<=0)return null;
		$result = getSQLRecord("SELECT * FROM `guild_member` WHERE id=?",array($memberid));
		return $result;
	}
}

function echoGuildManagerMessages($message){
?>
<table class="manager_message"><tbody>
	<tr><td><?php echo htmlspecialchars($message); ?></td></tr>
</tbody></table>
<?php
}

function trim_entryString($str){
	// 半角全角スペース混在対応
	$str = trim($str);
	$str = preg_replace('/^[ 　]+/u', '', $str);
	$str = preg_replace('/[ 　]+$/u', '', $str);
	$str = trim($str);
	$str = preg_replace('/^[ 　]+/u', '', $str);
	$str = preg_replace('/[ 　]+$/u', '', $str);
	$str = trim($str);
	return $str;
}

?>
