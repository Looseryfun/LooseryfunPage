<?php
	$error_message = '';
	$memberData = GuildMember::getData();
	$guildData = GuildGuild::getData();
	$name = trim_entryString(@$_REQUEST['name']);
	$keyword = trim_entryString(@$_REQUEST['keyword']);
try{
	if(isset($_REQUEST['job']))switch($_REQUEST['job']){
		case 'create':
		if(empty($name))throw new GuildErrorException('名前が設定されていません');
		if(GuildGuild::checkGuildName($name,$keyword))throw new GuildErrorException('同名同合言葉のギルドはすでに存在します。');
		if(GuildGuild::createNewGuild($name,$keyword,GuildMember::getMyMemberId())){
			throw new GoHomeException('ギルドを設立しました。');
		}else throw new GuildErrorException('同名同合言葉のギルドはすでに存在するか、設立できません。');
		break;
		case 'entry':
		if(empty($name))throw new GuildErrorException('名前が設定されていません');
		$guildId = GuildGuild::getGuildIdByName($name,$keyword);
		if($guildId!=0){
			GuildMember::updateGuild($guildId);
			throw new GoHomeException('ギルドに加入しました。');
		}else throw new GuildErrorException('ギルド名か合言葉が間違っています。');
		break;
		case 'update':
		$guildId = @$guildData['id'];
		if($guildId<=0)throw new GuildErrorException('不正な操作です');
		if(empty($name))throw new GuildErrorException('名前が設定されていません');
		$newguildId = GuildGuild::getGuildIdByName($name,$keyword);
		if($newguildId!=0&&$newguildId!=$guildId)throw new GuildErrorException('同名同合言葉のギルドはすでに存在するか、設立できません。');
		$icon_url = trim_entryString(@$_REQUEST['icon_url']);
		$publicinfo = trim_entryString(@$_REQUEST['publicinfo']);
		GuildGuild::updateData($guildId,$name,$keyword,$icon_url,$publicinfo);
		break;
	}
}catch(GuildErrorException $e){
	$error_message = $e->getMessage();
}
if(!empty($error_message))echoGuildManagerMessages($error_message);
?>
<div class="title-1"><h3>ギルド編集</h3></div>
<?php if(!$guildData){ ?>
<form action="." method="post">
	<table class="guildmanager_entryform"><tbody>
		<tr><th>ギルド名</th><td><input type="text" name="name" maxlength="50"/></td></tr>
		<tr><th>合言葉</th><td><input type="text" name="keyword" maxlength="50"/></td></tr>
		<tr><th>実行</th><td>
		<input type="button" name="submit" onclick="guildFromAjaxLoad(this,{'com':'editguild','job':'entry'});" value="参加する" />
			<span class="rightpos"><input type="button" name="submit" onclick="guildFromAjaxLoad(this,{'com':'editguild','job':'create'});" value="設立する" /></span>
		</td></tr>
	</tbody></table>
</form>
<?php } else if(!GuildMember::isGuildOwner()){ ?>
<table class="guildmanager_entryform"><tbody>
	<tr><th>ギルド</th><td><?php echo htmlspecialchars(@$guildData['name']) ?></td></tr>
</tbody></table>
<?php } else {
	$guildData = GuildGuild::getData();
	$name = @$guildData['icon_url'];
	$keyword = @$guildData['publicinfo'];
	$icon_url = @$guildData['icon_url'];
	$publicinfo = @$guildData['publicinfo'];
?>
<form action="." method="post">
	<table class="guildmanager_entryform"><tbody>
		<tr><th>ギルド名</th><td><input type="text" name="name" maxlength="50" value="<?php echo htmlspecialchars(@$guildData['name']) ?>"/></td></tr>
		<tr><th>合言葉</th><td><input type="text" name="keyword" maxlength="50"  value="<?php echo htmlspecialchars(@$guildData['keyword']) ?>"/></td></tr>
		<tr><th>アイコン</th><td><input type="text" name="icon_url" maxlength="50"  value="<?php echo htmlspecialchars(@$guildData['icon_url']) ?>"/></td></tr>
		<tr><th>公開情報</th><td><textarea name="publicinfo"maxlength="500" ><?php echo htmlspecialchars(@$guildData['publicinfo']) ?></textarea></td></tr>
		<tr><th>実行</th><td>
		<input type="button" name="submit" onclick="guildFromAjaxLoad(this,{'com':'editguild','job':'update'});" value="更新する" />
		</td></tr>
	</tbody></table>
</form>
<?php } ?>
<br/>
<br/>
<a href="javascript:guildAjaxLoad({'com':'home'});">ギルドマネージャホームへ戻る</a>
