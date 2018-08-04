<?php
	set_include_path(get_include_path().PATH_SEPARATOR.'..');
	include_once 'myinclude/guild/guildclass.php';
	$command = urlencode(@$_REQUEST['com']);
	if(!$command)$command='home';
	if(!GuildMember::checkBrowser())$command='welcome';
	
	// コマンドでインクルード
	function includeCommand($command,$include_params=array()){
		$includefile = 'myinclude/guild/'.$command.'.php';
		///public_html/stage/myinclude/guild
		if(!@include_once($includefile)){
			var_dump($command);
			echo '<div>アクセスできません</div>';
		}
	}
	try{
		includeCommand($command);
	}catch(GoHomeException $e){
		$params['home_message'] = $e->getMessage();
		includeCommand('home',$params);
	}
?>