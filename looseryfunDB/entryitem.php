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
	$oldItem = getRegstItem();
	if($oldItem){
		if( $itemData->maintype==$oldItem->maintype && $itemData->name==$oldItem->name ){
			$itemData = $oldItem;
		}
	}else{
		$items = itemData::createMainytypeName($maintype,$itemname);
		if(count($items)>0)$itemData=$items[0];
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
<?php
	include 'myinclude/pagefooter.php';
?>
