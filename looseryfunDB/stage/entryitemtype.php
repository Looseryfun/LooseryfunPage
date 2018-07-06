<?php
	include_once 'myinclude/pageheader.php';
	include_once 'myinclude/itemclass.php';
?>
<script type="text/javascript"> 
var ItemTypeData = <?php echo json_encode(ItemMaster::getItemTypeList()); ?>;
delete ItemTypeData[1];	// アイテムはまだ未対応
window.addEventListener('DOMContentLoaded',function(){
	setItemTypeList('mainItemType',ItemTypeData);
});

</script>
	<div class="title-1"><h3>アイテム登録</h3></div>

	<div>
		<ul class="list-1">
			<li>まずアイテムの種類と名前を入力してください。</li>
			<li>種類が足りないなどの修正は<a class="outlink" href="http://looseryfun.game-info.wiki/d/%ca%d4%bd%b8%b0%cd%cd%ea" rel="nofollow">ぽよんと</a>までお願いします。</li>
			<li>現在は装備品のみ対応しています。</li>
			<li>まだ通常アイテム、敵情報などは未対応です。</li>
		</ul>
		<form action="entryitem.php" method="get">
			<div style="">
				<table><tbody>
					<tr><th>種類</th><td><select id="mainItemType" name="maintype"></select></td></tr>
					<tr><th>アイテム名</th><td><input type="text" name="name" size="40"/></td></tr>
				</tbody></table>
				<input type="submit" value="進む" style="position: relative;left: 300px;"/>
			</div>
		</form>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
