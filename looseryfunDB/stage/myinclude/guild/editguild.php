<?php
	$error_message = '';
	$memberData = GuildMember::getData();
	$guildData = GuildGuild::getData();
	$name = trim_entryString(@$_REQUEST['name']);
	$keyword = trim_entryString(@$_REQUEST['keyword']);
try{
	if(isset($_REQUEST['job']))switch($_REQUEST['job']){
		case 'create':
		if(empty($name)){
			$error_message = '名前が設定されていません';
			break;
		}
		if(GuildGuild::checkGuildName($name,$keyword)){
			$error_message = 'そのギルドはすでに存在します。';
			break;
		}
		if(GuildGuild::createNewGuild($name,$keyword,GuildMember::getMyMemberId())){
			throw new GoHomeException('ギルドを設立しました。');
		}else{
			$error_message = 'そのギルドはすでに存在するか、設立できません。';
		}
		break;
		case 'entry':
		var_dump($_REQUEST['job']);
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
<?php } else { ?>
<form action="." method="post">
	<table class="guildmanager_entryform"><tbody>
		<tr><th>ギルド名</th><td><input type="text" name="name" maxlength="50" value="<?php echo htmlspecialchars(@$guildData['name']) ?>"/></td></tr>
		<tr><th>合言葉</th><td><input type="text" name="keyword" maxlength="50"  value="<?php echo htmlspecialchars(@$guildData['keyword']) ?>"/></td></tr>
		<tr><th>アイコン</th><td><input type="text" name="icon_url" maxlength="50"  value="<?php echo htmlspecialchars(@$guildData['icon_url']) ?>"/></td></tr>
		<tr><th>公開情報</th><td><input type="text" name="publicinfo" maxlength="50"  value="<?php echo htmlspecialchars(@$guildData['publicinfo']) ?>"/></td></tr>
		<tr><th>実行</th><td>
		<input type="button" name="submit" onclick="guildFromAjaxLoad(this,{'com':'editguild','job':'update'});" value="更新する" />
		</td></tr>
	</tbody></table>
</form>
<?php } ?>
