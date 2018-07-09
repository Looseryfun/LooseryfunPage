<?php
	include_once 'myinclude/pageheader.php';
	include_once 'myinclude/itemclass.php';
	include_once 'myinclude/itemeditview.php';
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
	$itemData = new itemData($maintype,$itemname);
	if(isset($_SESSION['registItemData'])){
		$check = $_SESSION['registItemData'];
		if( $itemData->maintype==$check->maintype && $itemData->name==$check->name ){
			$itemData = $check;
		}
	}
	echoDatasetScript($itemData);
?>
	<div class="title-1"><h3>アイテム登録</h3></div>
	<ul class="list-1">
		<li><?php echo htmlspecialchars($itemData->name); ?>のデータを入力してください。</li>
	</ul>

	<?php
		echoEditTable($itemData);
	?>
	<!--<div>
		<form action="execentryitem.php" method="post">
			<div style="">
				<table class="itemedit"><tbody>
					<tr><th colspan="2"><?php echo htmlspecialchars($itemname); ?><input type="hidden" name="name" value="<?php echo htmlspecialchars($itemname); ?>"/></th></tr>
					<tr><td><select id="subItemType" name="subtype" imgid="subimg"></select><span><img id="subimg" src="" class="tableicon"/></span><input type="hidden" name="maintype" value="<?php echo htmlspecialchars($maintype); ?>"/></td>
						<td>入手元<select id="dropType" name="gettype"></select></td></tr>
					<tr><td></td><td>分解<select id="breakType" name="breaktype"></select>:<span class="rightpos"><input type="number" name="breakpoint" size="5" value="0"/>pt</span></td></tr>
					<tr><td><input type="checkbox" name="notrade" value="1" checked="checked">トレード不可</input></td>
						<td><input type="checkbox" name="limited" value="1">期間限定品</input></td></tr>
					<tr><td>ATK:<input type="number" name="power" size="10" value="0"/></td><td>安定率:<input type="number" name="stability" size="10" value="50"/>%</td></tr>
					<tr><td colspan="2"><?php echoPropertyEditor(0); ?>
							<?php echoPropertyEditor(1); ?>
							<?php echoPropertyEditor(2); ?>
							<?php echoPropertyEditor(3); ?>
							<?php echoPropertyEditor(4); ?>
							<?php echoPropertyEditor(5); ?>
							<?php echoPropertyEditor(6); ?>
							<?php echoPropertyEditor(7); ?></td></tr>
					<tr><td colspan="2">備考：<span class="rightpos"><input type="checkbox" id="usemakedate" name="usemakedate" target="makedate" value="1">実装日<input id="makedate" name="makedate" type="date" disabled="disabled"/></span><br/><textarea name="help" cols="40" rows="7" wrap="hard"></textarea></td></tr>
				</tbody></table>
				<input type="button" onclick="submit();" class="submit" value="登録する" />
			</div>
		</form>
	</div>-->
<?php
	include 'myinclude/pagefooter.php';
?>
