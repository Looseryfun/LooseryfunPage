<?php
define('GUILDDATA_CACHETIME',1*60*60);
define('ONEPUSH_APPKEY','d419ca5c-c4ed-4053-a95a-59849c0c1ad7');
define('ONEPUSH_RESTKEY','OTNmZTExNmYtYjFjYS00ZDdmLWEzZTctODVlMDg0MjgyN2Q2');
define('WEBPUSH_DEFAULTICON','https://looseryfun.netlify.com/tw_icon8.png');
define('WEBPUSH_DEFAULTURL','https://looseryfun.netlify.com/ssdb/stage/guildmanager.php');
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
/**
 * エラー例外
 */
class GuildErrorException extends Exception
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
	public static function getMyMemberId(){
		if( isset($_SESSION['guild']['id']) )return $_SESSION['guild']['id'];
		$playerid = @$_COOKIE['playerid'];
		if(!$playerid)return 0;
		$memberid = GuildMember::getMemberIdFromPlayerId($playerid);
		if($memberid<=0)return 0;
		$_SESSION['guild']['id']=$memberid;
		return $memberid;
	}
	public static function createNewMember($name,$playerid){
		beginTransaction();
		$result = GuildMember::_createNewMember($name,$playerid);
		if($result)commitTransaction();
		else rollbackTransaction();
		return $result;
	}
	public static function isGuildOwner(){
		$memberid = GuildMember::getMyMemberId();
		$guildid = GuildMember::getGuildId($memberid);
		return GuildGuild::isOwner($guildid,$memberid);
	}
	private static function _createNewMember($name,$playerid){
		$name = trim_entryString($name);
		$playerid = trim_entryString($playerid);
		if(!$playerid||strlen($playerid)<=0)$playerid = GuildMember::makeNewBrowserPlayerId();
		$sql = 'INSERT INTO `guild_member` (`name`,`token`,`lastactiontime`) VALUES (:name,uuid_short(),now())';
		if(!execSQL($sql,array('name'=>$name)))throw new GuildErrorException(getDBErrorString());
		$id = getInsertID();
		GuildMember::deleteApc($id);
		if($id<=0)throw new GuildErrorException(getDBErrorString());
		return GuildMember::saveBrowserPlayerId($id,$playerid);
	}
	public static function saveBrowserPlayerId($memberid,$playerid){
		$sql = 'REPLACE INTO `guild_browser` (`playerid`,`userid`,`useragent`,`entrytime`) VALUES (:playerid,:memberid,:useragent,now())';
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$params = array(
			'playerid'=>$playerid,
			'memberid'=>$memberid,
			'useragent'=>$useragent);
		if(!execSQL($sql,$params))throw new GuildErrorException(getDBErrorString());
		GuildMember::setBroeser($memberid,$playerid);
		return true;
	}
	protected static function setBroeser($memberid,$playerid){
		$_SESSION['guild']['id']=$memberid;
		setcookie('playerid',$playerid,time()+60*60*24*365,'/');
		return true;
	}
	public static function checkBrowser(){
		$memberid = GuildMember::getMyMemberId();
		if($memberid<=0)return false;
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
		GuildMember::deleteApc($memberid);
	}
	public static function updateGuild($guildid,$memberid=0,$useMessage=true){
		$name = GuildMember::getName($memberid);
		$oldGuildId = GuildMember::getGuildId($memberid);
		if($memberid<=0)$memberid = GuildMember::getMyMemberId();
		if($memberid<=0)throw new GuildErrorException('member id not found.');
		beginTransaction();
		$sql = "UPDATE `guild_member` SET `guild`=:guildid,`lastactiontime`=now() WHERE id=:memberid";
		$params=array('guildid'=>$guildid,'memberid'=>$memberid);
		execSQL($sql,$params);
		GuildMember::deleteApc($memberid);
		if($useMessage){
			if($guildid==0){
				if($oldGuildId!=0)GuildMessage::guildToOwner($name.'さんが退出しました',$name.'さんがギルドから退出しました。',$oldGuildId);
			}else{
				GuildMessage::guildToGuild($name.'さんが参加しました',$name.'さんがギルドへ加入しました。',$guildid);
			}
		}
		commitTransaction();
	}
	public static function getGuildId($memberid=0){
		$data = GuildMember::getData($memberid);
		if($data==null||!isset($data['guild']))return 0;
		return $data['guild'];
	}
	public static function getName($memberid=0){
		$data = GuildMember::getData($memberid);
		return @$data['name'];
	}
	public static function getIcon($memberid=0){
		$data = GuildMember::getData($memberid);
		if(!$data)return WEBPUSH_DEFAULTICON;
		if(!empty($data['icon']))return $data['icon'];
		return GuildGuild::getIcon($data['guild']);
	}
	protected static function deleteApc($memberid){
		apcu_delete(GuildMember::makeApcKey($memberid));
	}
	public static function getPlayers($memberid){
		if(!$memberid)return array();
		$sql='SELECT playerid FROM `guild_member` RIGHT JOIN `guild_browser` ON guild_member.id=guild_browser.userid WHERE guild_member.id=?';
		return getIDs($sql,array($memberid));
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
		if($memberid<=0)$memberid = GuildMember::getMyMemberId();
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

class GuildGuild{
	protected static $inputValues=array(
		'id',
		'name',
		'keyword',
		'publicinfo',
		'owner',
		'icon_url',
		'token',
	);
	public static function checkGuildName($name,$keyword){
		$sql = 'SELECT id FROM `guild_data` WHERE `name`=:name AND `keyword`=:keyword';
		$ids = getIDs($sql,array('name'=>$name,'keyword'=>$keyword));
		return !empty($ids);
	}
	public static function createNewGuild($name,$keyword,$ownerid){
		if(empty($name))throw new GuildErrorException('name not defined.');
		if(!$ownerid)throw new GuildErrorException('owner id not defined.');
		beginTransaction();
		$result = GuildGuild::_createNewGuild($name,$keyword,$ownerid);
		if($result)commitTransaction();
		else rollbackTransaction();
		return $result;
	}
	private static function _createNewGuild($name,$keyword,$ownerid){
		$name = trim_entryString($name);
		$keyword = trim_entryString($keyword);
		$sql = 'INSERT INTO `guild_data` (`name`,`keyword`,`owner`,`token`) VALUES (:name,:keyword,:ownerid,uuid_short())';
		$params = array('name'=>$name,'keyword'=>$keyword,'ownerid'=>$ownerid);
		if(!execSQL($sql,$params))throw new GuildErrorException(getDBErrorString());
		$id = getInsertID();
		GuildGuild::deleteApc($id);
		GuildMessage::guildToOwner('ギルドを設立しました','ギルドの「編集」で追加の設定を行えます。',$id);
		GuildMember::updateGuild($id,$ownerid,false);
		return $id;
	}
	public static function getPlayers($guilds){
		if(!$guilds)return array();
		if(!is_array($guilds))$guilds=array($guilds);
		$guilds = array_diff($guilds,array('0'));
		$inQuery = implode(',',$guilds);

		$sql='SELECT playerid FROM `guild_member` RIGHT JOIN `guild_browser` ON guild_member.id=guild_browser.userid WHERE guild_member.guild in ('.$inQuery.')';
		return getIDs($sql,array());
	}
	public static function getOwner($guildid){
		$guildData = GuildGuild::getData($guildid);
		if(!$guildData)return 0;
		return $guildData['owner'];
	}
	public static function isOwner($guildid,$memberid){
		$ownerid = GuildGuild::getOwner($guildid);
		return ($memberid==$ownerid);
	}
	public static function getName($guildid){
		$guildData = GuildGuild::getData($guildid);
		if(!$guildData)return 0;
		return $guildData['name'];
	}
	protected static function makeApcKey($guildid){
		return 'guild_guild_'.$guildid;
	}
	protected static function deleteApc($guildid){
		apcu_delete(GuildGuild::makeApcKey($guildid));
	}
	public static function getIcon($guildid){
		if($guildid<=0)return WEBPUSH_DEFAULTICON;
		$data = GuildGuild::getData($guildid);
		if(!$data)return WEBPUSH_DEFAULTICON;
		if(empty($data['icon']))return WEBPUSH_DEFAULTICON;
		return $data['icon'];
	}
	public static function getData($guildid=0){
		if($guildid<=0)$guildid = GuildMember::getGuildId();
		if($guildid<=0)return null;
		$result=apcu_entry(GuildGuild::makeApcKey($guildid),
			'GuildGuild::generateData',
			GUILDDATA_CACHETIME);
		return $result;
	}
	private static function generateData($key){
		if(!$key)return null;
		$split = explode('_',$key);
		$guildid = @$split[2];
		if($guildid<=0)return null;
		$result = getSQLRecord("SELECT * FROM `guild_data` WHERE id=?",array($guildid));
		return $result;
	}
}
class GuildMessage{
	protected static $inputValues=array(
		'id',
		'title',
		'message',
		'fromname',
		'touser',
		'icon_url',
		'token',
		'sendtime',
	);
	public static function sendTest(){
		return GuildMessage::send(5);
		//return GuildMessage::sendPushMessage('テストタイトル',
		//	'本文かもしれない',
		//	'https://looseryfun.netlify.com/ssdb/stage/guildmanager.php',
		//	'http://looseryfun.netlify.com/lficon.png',
		//	array('c3eb87fa-006a-4627-a693-ee1aed397410','7da96aef-4d37-442c-a4d2-0f5a397db808')
		//	);
	}
	public static function guildToGuild($title,$message,$guildid){
		$guildName = GuildGuild::getName($guildid);
		$icon = GuildGuild::getIcon($guildid);
		return GuildMessage::createData($title,$message,$guildName,$icon,$guildid);
	}
	public static function guildToOwner($title,$message,$guildid){
		$ownerid = GuildGuild::getOwner($guildid);
		if($ownerid<=0)throw new GuildErrorException('guild owner id not found.');
		$guildName = GuildGuild::getName($guildid);
		$icon = GuildGuild::getIcon($guildid);
		return GuildMessage::createData($title,$message,$guildName,$icon,array(),$ownerid);
	}
	protected static function createData($title,$message,$fromName,$icon_url,$toguilds,$touser=0){
		$sql="INSERT INTO `guild_message` (`title`, `message`, `fromname`, `touser`, `icon_url`, `token`, `sendtime`) VALUES (:title, :message, :fromname, :touser, :icon_url, uuid_short(), now())";
		$params=array('title'=>$title,'message'=>$message,'fromname'=>$fromName,'touser'=>$touser,'icon_url'=>$icon_url);
		if(!execSQL($sql,$params))throw new GuildErrorException(getDBErrorString());
		$insertID = getInsertID();
		GuildMessage::deleteApc($insertID);
		if($insertID<=0)throw new GuildErrorException(getDBErrorString());
		GuildMessage::createToGuildsData($insertID,$toguilds);
		return GuildMessage::send($insertID);
	}
	private static function createToGuildsData($messageId,$toGuilds){
		if(!$toGuilds)return;
		if(!is_array($toGuilds))$toGuilds = array($toGuilds);
		$sql="INSERT INTO `guild_messageto` (`messageid`, `guildid`) VALUES (:messageid,:guildid)";
		foreach($toGuilds as $guildid){
			execSQL($sql,array('messageid'=>$messageId,'guildid'=>$toGuilds));
		}
	}
	private static function getToGuilds($messageId){
		if(!$messageId)return;
		return getIDs("SELECT guildid FROM `guild_messageto` WHERE messageid=?",array($messageId));
	}
	protected static function makeApcKey($messageid){
		return 'guild_message_'.$messageid;
	}
	protected static function deleteApc($messageid){
		apcu_delete(GuildMessage::makeApcKey($messageid));
	}
	public static function getData($messageid){
		if($messageid<=0)return null;
		$result=apcu_entry(GuildMessage::makeApcKey($messageid),
			'GuildMessage::generateData',
			GUILDDATA_CACHETIME);
		return $result;
	}
	private static function generateData($key){
		if(!$key)return null;
		$split = explode('_',$key);
		$guildid = @$split[2];
		if($guildid<=0)return null;
		$result = getSQLRecord("SELECT * FROM `guild_message` WHERE id=?",array($guildid));
		return $result;
	}
	protected static function send($messageid){
		$data = GuildMessage::getData($messageid);
		if(!$data)return null;
		$toMemberPlayers = GuildMember::getPlayers($data['touser']);
		$guilds = GuildMessage::getToGuilds($messageid);
		$gildPlayers = GuildGuild::getPlayers($guilds);
		$players = array_unique(array_merge($toMemberPlayers,$gildPlayers));
		return GuildMessage::sendPushMessage(
			$data['title'],$data['message'],
			WEBPUSH_DEFAULTURL,$data['icon_url'],$players);
	}
	protected static function sendPushMessage($title,$body,$url,$icon,$toPlayers){
		$players = array();
		$result = array();
		foreach($toPlayers as $player){
			if(GuildMessage::isOnepushPlayerId($player)){
				if(!in_array($player,$players))$players[]=$player;
			}
			if(count($players)>=1500){	// send max 2000
				$result = GuildMessage::_sendPushMessage($title,$body,$url,$icon,$players);
				$players=array();
			}
		}
		if(count($players)<=0)return $result;
		return GuildMessage::_sendPushMessage($title,$body,$url,$icon,$players);
	}
	private static function _sendPushMessage($title,$body,$url,$icon,$toPlayers){
		if(empty($title))$title='ギルドマネージャ';
		if(empty($icon))$icon=WEBPUSH_DEFAULTICON;
		if(empty($url))$url=WEBPUSH_DEFAULTURL;

		$fields = array(
			'app_id' => ONEPUSH_APPKEY,
			'included_segments' => array('All'),
			'headings' => array('en' => $title, 'jp'=>$title),
			'contents' => array('en' => $body, 'jp'=>$body),
			//'small_icon' => 'https://looseryfun.netlify.com/small_icon.png',
			'chrome_web_icon' => $icon,
			'large_icon' => $icon,
			'url' => $url,
			"include_player_ids" => $toPlayers,
		  );
	  
		  $fields = json_encode($fields);
	  
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic '.ONEPUSH_RESTKEY));
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		  curl_setopt($ch, CURLOPT_HEADER, FALSE);
		  curl_setopt($ch, CURLOPT_POST, TRUE);
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	  
		  $response = curl_exec($ch);
		  curl_close($ch);
		  return $response;
	}
	protected static function isOnepushPlayerId($playerId){
		return (strlen($playerId)>20);
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
