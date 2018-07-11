<?php
include_once 'myfunctions.php';
include_once 'itemclass.php';
function echoDatasetScript($itemdata){
	$maintype = $itemdata->maintype;
	$itemname = $itemdata->name;
	$mainTypeData = ItemMaster::getItemTypeList()[$maintype];
	?>
	<script type="text/javascript"> 
	// アイテム種別
	var ItemTypeData = <?php echo json_encode(ItemMaster::getItemTypeList()); ?>;
	var maintype = <?php echo htmlspecialchars($maintype); ?>;
	var subTypeData = ItemTypeData[maintype]['sub'];
	var mainTypeName = ItemTypeData[maintype]['name'];
	window.addEventListener('DOMContentLoaded',function(){
		setSubItemTypeList('subItemType',subTypeData);
	});
	
	// プロパティ種別
	var PropertyData = <?php echo json_encode(ItemMaster::getItemPropertyList()); ?>;
	window.addEventListener('DOMContentLoaded',function(){
		for(var i=0;i<10;i++){
			setPropertyList('prop_maintype'+i,PropertyData);
		}
	});
	
	// 素材種別
	var materialTypeData = <?php echo json_encode(ItemMaster::getMaterialTypeList()); ?>;
	window.addEventListener('DOMContentLoaded',function(){
		setSubItemTypeList('breakType',materialTypeData);
	});
	
	// 取得方法(ドロップ・鍛冶など)
	var getTypeData = <?php echo json_encode(ItemMaster::getGetTypeList()); ?>;
	window.addEventListener('DOMContentLoaded',function(){
		setSelectboxData('dropType',getTypeData);
	});
	
	// 実装日
	window.addEventListener('DOMContentLoaded',function(){
		setEnableCheckbox('usemakedate');
	});
	// データ設定
	var oldData = <?php echo json_encode($itemdata); ?>;
	window.addEventListener('DOMContentLoaded',function(){
		setItemDataforForm(oldData);
	});
	</script>
	<?php
}
function echoPropertyEditor($num){
	echo "<div><select id=\"prop_maintype$num\" name=\"prop_maintype$num\" subid=\"prop_subtype$num\"></select><select id=\"prop_subtype$num\" name=\"prop_subtype$num\" percentid=\"prop_percent$num\"></select><span class=\"rightpos\">数値:<input type=\"number\" name=\"prop_power$num\" size=\"10\" value=\"0\"/><span id=\"prop_percent$num\" style=\"opacity:0;\">%</span></span></div>";
}	
function echoEditTable($itemdata){
	if(!$itemdata)return;
	$maintype = $itemdata->maintype;
	$itemname = $itemdata->name;
	$mainTypeData = ItemMaster::getItemTypeList()[$maintype];
	$mainTypeName = $mainTypeData['name'];
	switch($mainTypeName){
		case '武器':
?>
<div>
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
</div>
<?php
	break;
	case 'アイテム':
?>
<div>
	<form action="execentryitem.php" method="post">
		<div style="">
			<table class="itemedit"><tbody>
				<tr><th colspan="2"><?php echo htmlspecialchars($itemname); ?><input type="hidden" name="name" value="<?php echo htmlspecialchars($itemname); ?>"/></th></tr>
				<tr><td>使用方法<select id="subItemType" name="subtype"></select>
						<input type="hidden" name="maintype" value="<?php echo htmlspecialchars($maintype); ?>"/><input type="hidden" name="gettype" value="0"></td>
					<td style="width: 170px;"></td></tr>
				<tr><td>分解<select id="breakType" name="breaktype" imgid="subimg"></select><img id="subimg" src="" class="tableicon"/>:<span class="rightpos"><input type="number" name="breakpoint" size="5" value="0"/>pt</span></td>
					<td><input type="checkbox" name="notrade" value="1">トレード不可</input></td></tr>
				<tr><td>売値:<span class="rightpos"><input type="number" name="power" size="5" value="0"/>スピナ</span></td>
					<td><input type="checkbox" name="limited" value="1">期間限定品</input><input type="hidden" name="power" value="0"/><input type="hidden" name="stability" value="0"/></td></tr>
				<tr><td colspan="2">備考：<span class="rightpos"><input type="checkbox" id="usemakedate" name="usemakedate" target="makedate" value="1">実装日<input id="makedate" name="makedate" type="date" disabled="disabled"/></span><br/>
					<textarea name="help" cols="47" rows="7" wrap="hard"></textarea></td></tr>
			</tbody></table>
			<input type="button" onclick="submit();" class="submit" value="登録する" />
		</div>
	</form>
</div>
<?php
	break;
?>
<?php
	}//switch
}
?>
