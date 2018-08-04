<?php
	if(isset($_POST['name'])){
		$name = trim_entryString($_POST['name']);
		if(strlen($name)<=0){
			$error_message='名前が入力されていません';
		}else if(GuildMember::createNewMember(@$_POST['name'],@$_POST['playerid'])){
			throw new GoHomeException('登録しました。');
		}else{
			$error_message = getDBErrorString();
		}
	}
?>
<div class="title-1"><h3>ようこそギルドマネージャへ！</h3></div>
<div>
	<ul class="list-2">
		<li>ギルドマネージャはギルドメンバーとのやり取りができるツールです。</li>
		<li>今使っているブラウザを登録します。メールアドレスなどは不要です。
			<br/>※ネットカフェ等の<b>誰でも使えるブラウザで登録しない</b>でください！</li>
		<li>詳しい機能などは<a href="" target="_blank">こちら</a>をご確認ください。</li>
	</ul>
	<?php if(isset($error_message))echoGuildManagerMessages($error_message); ?>
	<br/>
	<div id="welcome">
		<div class="welcome_default">
			<?php echoGuildManagerMessages('上部に出る通知の許可をしてください。'); ?>
		</div>
		<div class="welcome_granted">
	<form action="." method="post">
		<table class="guildmanager_entryform"><tbody>
			<tr><th>ユーザー名</th><td><input type="text" name="name" maxlength="50"/></td></tr>
			<tr><th><input type="hidden" name="playerid" value=""/></th><td><input type="button" name="submit" onclick="guildFromAjaxLoad(this,{'com':'welcome'},loadFinishCall);" value="登録して開始" /></td></tr>
	</tbody></table>
	</form>
	<form action="." method="post">
	<table class="guildmanager_entryform"><tbody>
			<tr><th>秘密番号</th><td><input type="text" name="token" maxlength="50"/></td></tr>
			<tr><th><input type="hidden" name="playerid" value=""/></th><td><input type="button" name="submit" onclick="guildFromAjaxLoad(this,{'com':'welcome'},loadFinishCall);" value="引き継いで開始" /></td></tr>
	</tbody></table>
	</form>
		</div>
		<div class="welcome_denied">
			<?php echoGuildManagerMessages('ギルドマネージャの使用は拒否されています。'); ?>
		</div>
	</div>
	<br/>
<script>
	function updateStatus(permission){
		var newStatus = '';
		switch(permission){
			default:
			case 'default':newStatus = 'welcome_default';break;
			case 'granted':newStatus = 'welcome_granted';break;
			case 'denied':newStatus = 'welcome_denied';break;
		}
		var showareaTag = document.getElementById('welcome');
		if(showareaTag)showareaTag.className = newStatus;
		OneSignal.getUserId((userId)=>{
			updatePlayerId(userId);
		});
	}
	function updatePlayerId(playerId){
		var isSupport = OneSignal.isPushNotificationsSupported();
		var enable = (!isSupport)||(!!playerId);
		var elements = document.getElementsByName('playerid');
		for(var i=0;i<elements.length;i++){
			elements[i].value = playerId;
		}
		elements = document.getElementsByName('submit');
		for(var i=0;i<elements.length;i++){
			elements[i].disabled = (enable)?(''):('true');
		}
	}
	OneSignal.push(function() {
		OneSignal.on('notificationPermissionChange', function(permissionChange) {
			updateStatus(permissionChange.to);
		});
	});
	function loadFinishCall(){
		var isSupport = OneSignal.isPushNotificationsSupported();
		if(isSupport){
			OneSignal.getNotificationPermission((permission)=>{
				updateStatus(permission);
			});
		}else{
			updateStatus('granted');
		}
	}
	OneSignal.push(function() {
		loadFinishCall();
	});
	//guidShowError.enable=true;
</script>
</div>
