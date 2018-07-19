<?php
	include_once 'myinclude/pageheader.php';
	include_once 'myinclude/wikireader.php';
?>
	<div class="title-1"><h3 id="content_2">てす</h3></div>
	<div>
	<?php
		$result = getEquipData('https://www.dopr.net/toramonline-wiki/onehandsword');
	?>
<pre>
<code>
<?php
// echo item
/*foreach($result as $row){
	echo implode(',', $row)."\n";
}*/
// echo wepon
foreach($result as $row){
	if(!isset($row['property'])){
		// ウッドソード等
		$row['property']['鍛冶屋品']=array();
	}
	foreach($row['property'] as $droptype=>$propertys){
		$item = ['item',$droptype,@$row['name'],@$row['limited'],@$row['power'],@$row['stability']];
		echo implode(',', $item)."\n";
		if(!$propertys)continue;
		foreach($propertys as $property){
			echo 'property,'.$droptype.','.implode(',',$property)."\n";
		}
	}
	if(isset($row['material'])){
		foreach($row['material'] as $material){
			echo 'material,'.implode(',', $material)."\n";
		}
	}
}
	?>
</code>
</pre>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
