<?php
include_once 'rss/Item.php';
include_once 'rss/Feed.php';
include_once 'rss/RSS2.php';
include_once 'rss/InvalidOperationException.php';
use \FeedWriter\RSS2;

set_include_path(get_include_path().PATH_SEPARATOR.'..');
include_once 'myinclude/guild/guildclass.php';

date_default_timezone_set('Asia/Tokyo');
$token = @$_REQUEST['token'];
$type = @$_REQUEST['type'];

try{
	$memberid = GuildMember::getMemberIdByToken($token);
	$messageids = GuildMessage::getMessages($memberid);
	$Feed = new RSS2();
	$Feed->setTitle('Guild Manager Notify RSS');
	$Feed->setLink('http://looseryfun.netlify.com');
	$Feed->setDescription('ギルドマネージャの通知用RSSです。');

	$messageids = array_slice($messageids,0,20);
	foreach($messageids as $id){
		$message = GuildMessage::getData($id);
		if(!$message)continue;
		$item = $Feed->createNewItem();
		$item->setTitle($message['title']);
		$linkurl = $message['linkurl'];
		if(empty($linkurl))$linkurl=WEBPUSH_DEFAULTURL;
		$item->setLink($linkurl);
		$item->setDate(strtotime($message['sendtime']));
		$item->setDescription($message['message']);
		
		$Feed->addItem($item);
	}
	$Feed->printFeed();
}catch(FeedWriter\InvalidOperationException $e){
	throw $e;

}catch(GuildErrorException $e){
	$error_message = $e->getMessage();
}catch(PDOException $e){
	$error_message = $e->getMessage();
}
?>