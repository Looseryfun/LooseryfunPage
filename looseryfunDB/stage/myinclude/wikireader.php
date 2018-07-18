<?php

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
function getAllArmorImg(){
	$urls=[
		'https://www.dopr.net/toramonline-wiki/armor',
		'https://www.dopr.net/toramonline-wiki/armorEX',
	];
	$allData = array();
	foreach($urls as $url){
		$newData = getArmorImg($url);
		$allData = array_merge($allData,$newData);
	}
	return $allData;
}

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
function getEquipImgByName($typename){
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
	$urls = @$allurls[$typename];
	if(!$urls)return null;

	$allData = array();
	foreach($urls as $url){
		$newData = getEquipImg($url);
		$allData = array_merge($allData,$newData);
	}
	return $allData;
}
function getNormalItem($url){
	static $query = '//div[@id="main-content"]//table//th';
	static $queryInner = '../following-sibling::tr[1]//td';
	static $queryInner2 = '../following-sibling::tr[2]//td[1]';
	$result = array();
	$domDocument = new DOMDocument();
	if( !@$domDocument->loadHTMLFile($url) )return $result;
	
	$xpath = new DOMXPath($domDocument);
	// 入れ物を探す
	$result = array();
	$nodes = $xpath->query($query);
	foreach($nodes as $node) {
		$itemName = $node->textContent;
		if($itemName=='アイテム名')continue;
		// 分解ポイントと価格取得
		$breakAndPrice = $xpath->query($queryInner,$node);
		$break = @$breakAndPrice[0];
		$breakType = $breakPoint = null;
		if($break){
			$break = $break->textContent;
			$pregs = preg_split('/([0-9]+)/',$break,null,PREG_SPLIT_DELIM_CAPTURE);
			$breakType = @$pregs[0];
			$breakPoint = @$pregs[1];
		}
		$price = @$breakAndPrice[1];
		if($price){
			$price = $price->textContent;
			$pregs = preg_split('/([0-9]+)/',$price,null,PREG_SPLIT_DELIM_CAPTURE);
			$price = @$pregs[1];
		}
		// 説明取得
		$helptexts = $xpath->query($queryInner2,$node);
		$helpNode = @$helptexts[0];
		$helptext = '';
		if($helpNode){
			$helptext = $helpNode->textContent;
			//$helptext = str_replace(array("\t","\r\n","\n","\r"), "", $helptext);
			preg_match('/消費アイテム[\(（](.+?)[\)）]/',$helptext,$pregs);
			$helptext = trim(@$pregs[1]);
			$limited = $helpNode->textContent;
			$limited = (preg_match('/限定/',$limited,$pregs))?(1):(0);
		}
		array_push($result,array($itemName,$breakType,$breakPoint,$price,$helptext,$limited));
	}
	return $result;
}
function getAllNormalItem(){
	$urls=[
		'https://www.dopr.net/toramonline-wiki/item_material',
		'https://www.dopr.net/toramonline-wiki/item_material02',
		'https://www.dopr.net/toramonline-wiki/item_material03',
		'https://www.dopr.net/toramonline-wiki/item_material04',
		'https://www.dopr.net/toramonline-wiki/item_material05',
		'https://www.dopr.net/toramonline-wiki/item_material05',
		'https://www.dopr.net/toramonline-wiki/item_material06',
		'https://www.dopr.net/toramonline-wiki/item_material07',
		'https://www.dopr.net/toramonline-wiki/item_material08',
		'https://www.dopr.net/toramonline-wiki/item_material09',
		'https://www.dopr.net/toramonline-wiki/item_material10',
	];
	$allData = array();
	foreach($urls as $url){
		$newData = getNormalItem($url);
		$allData = array_merge($allData,$newData);
	}
	return $allData;
}
?>
