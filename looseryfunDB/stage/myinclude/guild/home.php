<?php
	$memberData = GuildMember::getData();
?>
<div class="title-1"><h3>ギルドマネージャ</h3></div>
<?php if(isset($include_params['home_message']))echoGuildManagerMessages($include_params['home_message']); ?>
<table class="guildmanager_entryform"><tbody>
	<tr><th>ギルド</th><td>なし<span class="rightpos"><a class="edit" onclick="guildAjaxLoad({'com':'editguild'});">編集</a></span></td></tr>
	<tr><th>ユーザ</th><td><?php echo htmlspecialchars(@$memberData['name']) ?><span class="rightpos"><a class="edit" href="">編集</a></span></td></tr>
</tbody></table>