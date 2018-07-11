<?php
	include_once 'myinclude/pageheader.php';

	include_once 'myinclude/myfunctions.php';
	
	
function generateEquipImg($url){
	static $query = '//div[@id="main-content"]//h2';
	static $queryText = 'span/text()';
	static $queryInner = 'following-sibling::table[1]//img';
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
		if(strpos($itemName,'▼▼')!==false)continue;//期間限定表示
		// 画像取得
		$innerDatas = $xpath->query($queryInner,$node);
		$image = null;
		foreach($innerDatas as $innerNode) {
			$image = $innerNode->getAttribute('src');
			if($image)break;
		}
		$data = ['link'=>$url.'#'.$anker,'name'=>$itemName,'image'=>$image];
		array_push($result,$data);
	}
	return $result;
}
function getEquipImg($url){
	return apcu_entry($url, 'generateEquipImg', URL_CACHETIME);
}
	$allurls = array();
	$allurls['片手剣'] = ['https://www.dopr.net/toramonline-wiki/onehandsword'];
	$allurls['両手剣'] = ['https://www.dopr.net/toramonline-wiki/bothhandsword'];
	$allurls['手甲'] = ['https://www.dopr.net/toramonline-wiki/glove'];
	$allurls['弓'] = ['https://www.dopr.net/toramonline-wiki/bow'];
	$allurls['自動弓'] = ['https://www.dopr.net/toramonline-wiki/crossbow'];
	$allurls['杖'] = ['https://www.dopr.net/toramonline-wiki/rod'];
	$allurls['魔導具'] = ['https://www.dopr.net/toramonline-wiki/eviltool'];
	$allurls['旋風槍'] = ['https://www.dopr.net/toramonline-wiki/Halberd'];
	$allurls['抜刀剣'] = ['https://www.dopr.net/toramonline-wiki/katana'];
	$allurls['盾'] = ['https://www.dopr.net/toramonline-wiki/shield'];
	$allurls['追加装備'] =[
		'https://www.dopr.net/toramonline-wiki/selects',
		'https://www.dopr.net/toramonline-wiki/selects02',
		'https://www.dopr.net/toramonline-wiki/selects03',
		'https://www.dopr.net/toramonline-wiki/selects04',
		'https://www.dopr.net/toramonline-wiki/selects05',
		'https://www.dopr.net/toramonline-wiki/selects06',
		'https://www.dopr.net/toramonline-wiki/selectsEX',
		];
	$typename = @$_GET['name'];
	$urls = @$allurls[$typename];
	if(!$urls){
		echo htmlspecialchars('データがありません');
		include 'myinclude/pagefooter.php';
		exit();
	}

	$allData = array();
	foreach($urls as $url){
		$newData = getEquipImg($url);
		$allData = array_merge($allData,$newData);
	}
?>
<script type="text/javascript"> 
function changeshowimg(imgtag){
	var spanTag = imgtag.parentNode.parentNode;
	var target = spanTag.parentNode.parentNode.nextElementSibling ;
	if(spanTag.className=='break'){
		spanTag.className = '';
		target.style.display="inline";
	}else{
		spanTag.className = 'break';
		target.style.display="none";
	}
}
</script>
	<div class="title-1"><h3><?php echo htmlspecialchars($typename); ?>画像一覧</h3></div>
	<div>
		<ul class="list-1">
			<li>このデータは<a class="outlink" href="https://www.dopr.net/toramonline-wiki/" rel="nofollow">【アソビモ】トーラムオンライン攻略情報まとめ【wiki】</a>の情報を再構成したものです。</li>
			<li>データは自動的に作成されています。トラブルは<a class="outlink" href="http://looseryfun.game-info.wiki/d/%ca%d4%bd%b8%b0%cd%cd%ea" rel="nofollow">ぽよんと</a>までお願いします。</li>
		</ul>
	</div>
	<div id="showarea">
<?php
	$count = count($allData);
	$colcount = 5;
	$rowcount = floor(($count+$colcount-1)/$colcount);
	for($i=0;$i<$rowcount*$colcount;$i++){
		if(($i%$colcount)==0){echo "<div>";}
		if($i<$count){
			$imgData = $allData[$i];
			$fontSize = '14px';
			$strlen = mb_strlen(@$imgData['name']);
			if($strlen<5){$fontSize='14px';}
			else if($strlen<6){$fontSize='13px';}
			else if($strlen<7){$fontSize='12px';}
			else if($strlen<8){$fontSize='11px';}
			else if($strlen<9){$fontSize='10px';}
			else if($strlen<10){$fontSize='9px';}
			else if($strlen<11){$fontSize='8px';}
			else if($strlen<12){$fontSize='7px';}
			else {$fontSize='6px';}
?>
<table style="display: table-cell;"><tbody>
		<tr><th style="width:120px;font-size:<?php echo $fontSize; ?>;">
			<a class="outlink" href="<?php echo $imgData['link']; ?>" rel="nofollow"><?php echo htmlspecialchars($imgData['name']); ?></a>
			<span class=""><span class="rightpos icon"><img src="img/%e5%bf%83%e7%9c%bc.png" onclick="changeshowimg(this)"/></span></span>
		</th></tr>
		<tr><td style="text-align: center;">
<?php
	$imgPath = @$imgData['image'];
	if($imgPath){
		echo "<img style=\"height:100px;\" src=\"$imgPath\" />";
	}
?>
		</td></tr>
	</tbody></table>
<?php
		}
		if(($i%$colcount)==$colcount-1){echo "</div>";}
	}//for
?>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
