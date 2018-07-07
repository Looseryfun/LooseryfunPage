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

function makeItemdataFromRequest($REQUEST){
	$maintype = $REQUEST['maintype'];
	$itemname = $REQUEST['name'];
	$result = new itemData($maintype,$itemname);
	$result->subtype = $REQUEST['subtype'];
	$result->breaktype = $REQUEST['breaktype'];
	$result->breakpoint = $REQUEST['breakpoint'];
	$result->name = $REQUEST['name'];
	$result->gettype = $REQUEST['gettype'];
	$result->power = $REQUEST['power'];
	$result->extra = isVariable($REQUEST['extra'])?($REQUEST['extra']):('');
	$result->notrade = isVariable($REQUEST['notrade'])?(1):(0);
	$result->stability = $REQUEST['stability'];
	$result->limited = isVariable($REQUEST['limited'])?(1):(0);
	$result->help = $REQUEST['help'];
	$result->makedate = isVariable($REQUEST['makedate'])?($REQUEST['makedate']):(null);

	for($i=0;$i<12;$i++){
		if(!isset($REQUEST['prop_maintype'.$i]))continue;
		if($REQUEST['prop_maintype'.$i]<=0)continue;
		$maintype = $REQUEST['prop_maintype'.$i];
		$subpercent = explode('_',$REQUEST['prop_subtype'.$i]);
		$power = $REQUEST['prop_power'.$i];
		$propertyData = new ItemProperty();
		$propertyData->set($maintype,$subpercent[0],$subpercent[1],$power);
		array_push($result->propertyList,$propertyData);
	}
	return $result;
}
	$itemdata = makeItemdataFromRequest($_POST);
	if(isset($_SESSION['registItemData'])){
		$oldData = $_SESSION['registItemData'];
		$itemdata->dupID($oldData);
	}
	$saveResult = $itemdata->save(false);
	if($saveResult){
		$_SESSION['registItemData'] = $itemdata;
	}
	$mainTypeData = ItemMaster::getItemTypeList()[$itemdata->maintype];
?>
<script type="text/javascript"> 
</script>
	<div class="title-1"><h3>登録完了</h3></div>
	<?php
		if(!$saveResult)
		{?>
	<div style="">
		<table class="itemedit"><tbody>
			<tr><td>エラーが発生しました。</td></tr>
			<tr><td><?php echo htmlspecialchars(getDBErrorString()); ?></td></tr>
		</tbody></table>
	</div>
	<?php	}else{	?>

	<div style="">
		<table class="itemedit"><tbody>
			<tr><th colspan="2"><?php echo htmlspecialchars($itemdata->name); ?>"/></th></tr>
			<tr><td><?php echo  $itemdata->subtype ?></td>
				<td>入手元<?php echo  $itemdata->gettype ?></td></tr>
		</tbody></table>
	</div>
	<?php	}	?>
<?php
	include 'myinclude/pagefooter.php';
?>
