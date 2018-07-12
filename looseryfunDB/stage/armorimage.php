<?php
	include_once 'myinclude/pageheader.php';

	include_once 'myinclude/myfunctions.php';
	
	
function generateArmorImg($url){
	static $query = '//div[@id="main-content"]//h2';
	static $queryText = 'span/text()';
	static $queryInner = 'following-sibling::table[2]/tr/td/img';
	$result = array();
	$domDocument = new DOMDocument();
	if( !@$domDocument->loadHTMLFile($url) )return $result;
	
	$xpath = new DOMXPath($domDocument);
	// 入れ物を探す
	$nodes = $xpath->query($query);
	foreach($nodes as $node) {
		$anker = $node->getAttribute('id');
		$texts = $xpath->query($queryText,$node);
		$itemName='';
		foreach($texts as $text) {$itemName.=$domDocument->saveXML($text);}
		// 画像取得
		$innerDatas = $xpath->query($queryInner,$node);
		$images = array();
		foreach($innerDatas as $innerNode) {
			array_push($images,$innerNode->getAttribute('src'));
		}
		// 順番を　軽　普通　重　に変える
		$images = [@$images[1],@$images[0],@$images[2],@$images[4],@$images[3],@$images[5]];
		$data = ['link'=>$url.'#'.$anker,'name'=>$itemName,'images'=>$images];
		array_push($result,$data);
	}
	return $result;
}
function getArmorImg($url){
	return apcu_entry($url, 'generateArmorImg', URL_CACHETIME);
}
	$urls=[
		'https://www.dopr.net/toramonline-wiki/armor',
		'https://www.dopr.net/toramonline-wiki/armorEX',
	];
	$allData = array();
	foreach($urls as $url){
		$newData = getArmorImg($url);
		$allData = array_merge($allData,$newData);
	}
?>
<script type="text/javascript"> 
window.addEventListener('DOMContentLoaded',function(){
	var targetList = document.getElementsByName('showChange');
	var listSize = targetList.length;
	for(var i=0;i<listSize;i++){
		target = targetList.item(i);
		target.addEventListener('change',onChangeVisible);
	}
});
</script>
	<div class="title-1"><h3>体防具画像一覧</h3></div>
	<div>
		<ul class="list-1">
			<li>このデータは<a class="outlink" href="https://www.dopr.net/toramonline-wiki/" rel="nofollow">【アソビモ】トーラムオンライン攻略情報まとめ【wiki】</a>の情報を再構成したものです。</li>
			<li>データは自動的に作成されています。トラブルは<a class="outlink" href="http://looseryfun.game-info.wiki/d/%ca%d4%bd%b8%b0%cd%cd%ea" rel="nofollow">ぽよんと</a>までお願いします。</li>
			<li>ボタンで表示を切り替えられます。</li>
		</ul>
	</div>
	<div>
	<input type="radio" name="showChange" value="showman" checked="checked" target="showarea" >男性用装備</input>
		<input type="radio" name="showChange" value="showwoman" target="showarea" >女性用装備</input>
	</div>
	<div id="showarea" class="showman">
<?php
	$count = count($allData);
	$rowcount = floor(($count+1)/2);
	for($i=0;$i<$rowcount*2;$i++){
		if(($i%2)==0){echo "<div>";}
		if($i<$count){
			$imgData = $allData[$i];
?>
<table style="display: inline-block;"><tbody>
		<tr><th>
			<a class="outlink" href="<?php echo $imgData['link']; ?>" rel="nofollow"><?php echo htmlspecialchars($imgData['name']); ?></a>
			<span class=""><span class="rightpos icon"><img src="img/%e5%bf%83%e7%9c%bc.png" onclick="changeShowImg(this)"/></span></span>
		</th></tr>
		<tr><td>
<?php
	foreach($imgData['images'] as $key=>$imgPath){
		$class = ($key<=2)?('man'):('woman');
		echo "<img style=\"width:90px;\" src=\"$imgPath\" class=\"$class\"/>";
	}
?>
		</td></tr>
	</tbody></table>
<?php
		}
		if(($i%2)==1){echo "</div>";}
	}//for
?>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
