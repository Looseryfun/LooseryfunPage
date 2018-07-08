<?php
	include_once 'myinclude/pageheader.php';
	if(!isset($_REQUEST['id'])){
		echo 'IDが指定されていません。';
		include 'myinclude/pagefooter.php';
		exit();
	}
	$itemData = itemData::getItemById($_REQUEST['id']);
	if(!$itemData){
		echo 'データがありません。';
		include 'myinclude/pagefooter.php';
		exit();
	}
	include 'myinclude/itemview.php';
	echoItemTable($itemData);
?>
<?php
	include 'myinclude/pagefooter.php';
?>
