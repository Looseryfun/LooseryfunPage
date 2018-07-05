<?php
	include_once 'myinclude/pageheader.php';
	include_once 'myinclude/itemclass.php';
	if(!isset($_REQUEST['maintype'])){
		echo 'タイプが不正です。';
		include 'myinclude/pagefooter.php';
	}
	if(!isset($_REQUEST['name'])||strlen($_REQUEST['name'])<=0){
		echo 'アイテム名が入力されていません。';
		include 'myinclude/pagefooter.php';
	}
	$maintype = $_REQUEST['maintype'];
	$itemname = $_REQUEST['name'];
	$mainTypeData = ItemMaster::getItemTypeList()[$maintype];
?>
<script type="text/javascript"> 
var ItemTypeData = <?php echo ItemMaster::getJsonItemType(); ?>;
var maintype = <?php echo htmlspecialchars($maintype); ?>;
var subTypeData = ItemTypeData[maintype]['sub'];
var mainTypeName = ItemTypeData[maintype]['name'];
window.addEventListener('DOMContentLoaded',function(){
	setItemTypeList('subItemType',subTypeData);
});

</script>
	<div class="title-1"><h3>アイテム登録</h3></div>

	<div>
		<ul class="list-1">
			<li><?php echo htmlspecialchars($itemname); ?>のデータを入力してください。</li>
		</ul>
		<form action="execentryitem.php" method="get">
			<div style="">
				<table style="padding-left: 10px;"><tbody>
					<tr><th colspan="2"><?php echo htmlspecialchars($itemname); ?></th></tr>
					<tr><td><?php echo htmlspecialchars($mainTypeData['name']); ?><input type="hidden" name="maintype" value="<?php echo htmlspecialchars($maintype); ?>"/></td>
						<td><select id="subItemType" name="subtype" imgid="subimg"></select><img id="subimg" src=""/></td></tr>
					<tr><td><input type="checkbox" name="notrade" value="1" checked="checked">トレード不可</input></td><td><input type="checkbox" name="limited" value="1">期間限定品</input></td></tr>
					<tr><td>ATK:<input type="text" name="power" size="10" value="0"/></td><td>安定率:<input type="text" name="stability" size="10" value="50"/>%</td></tr>
					<tr><td>プロパティ1-5</td><td>プロパティ6-10</td></tr>
					<tr><td colspan="2">備考：<br/><textarea name="help" cols="40" rows="7" wrap="hard"></textarea></td></tr>
				</tbody></table>
				<input type="submit" value="登録する" style="position: relative;left: 300px;"/>
			</div>
		</form>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
