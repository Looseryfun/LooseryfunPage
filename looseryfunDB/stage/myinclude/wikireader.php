<?php
function getAllEquipURLs(){
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
	return $allurls;
}
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
	$allurls = getAllEquipURLs();
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

function searchEquipData(&$result,$innerNode){
	if(!$innerNode)return;
	if($innerNode->firstChild->tagName=='th'){
		$thText = $innerNode->firstChild->textContent;
		if(strpos($thText,'基礎')!==false){
			$result['power']=preg_replace('/[^0-9]/', '', $innerNode->firstChild->nextSibling->textContent);
			return;
		}
		if(strpos($thText,'安定率')!==false){
			$result['stability']=preg_replace('/[^0-9]/', '', $innerNode->firstChild->nextSibling->textContent);
			return;
		}
		if(strpos($thText,'素材')!==false){
			$nextNode = $innerNode->nextSibling; //tr
			$materials = array();
			while($nextNode){
				$tdText = $nextNode->firstChild->textContent;
				if($tdText!='-'&&$tdText!='素材'){
					$pregs = preg_split('/([0-9]+)/',$tdText,null,PREG_SPLIT_DELIM_CAPTURE);
					array_push($materials,$pregs);
				}
				$nextNode = $nextNode->nextSibling;
			}
			if(count($materials)>0)$result['material']=$materials;
			return;
		}
		if(strpos($thText,'品')!==false&&$thText!='スミス品'){
			$trNode = $innerNode->nextSibling; //tr
			while($trNode&&$trNode->firstChild->tagName!='td'){
				$trNode = $trNode->nextSibling;
			}
			if(!$trNode)return;
			$textNode = $trNode->firstChild->firstChild;
			$propertys = array();
			while($textNode){
				if($textNode instanceof DOMText){
					if($textNode->textContent=='-')return;//存在しない
					if(strpos($textNode->textContent,'効果なし')===false){
						//array_push($propertys,$textNode->textContent);
						$preg = preg_split('/([-+][\d]*)(\%?)/',$textNode->textContent,null,PREG_SPLIT_DELIM_CAPTURE);
						array_push($propertys,$preg);
					}
				}
				$textNode = $textNode->nextSibling;
			}
			$result['property'][$thText]=$propertys;
			return;
		}
	}
	
}
function getEquipData($url){
	static $query = '//div[@id="main-content"]//h2';
	static $queryText = 'span/text()';
	static $queryInner = 'following-sibling::table[1]//table//tr';
	$result = array();
	$domDocument = new DOMDocument();
	if( !@$domDocument->loadHTMLFile($url) )return $result;
	
	$xpath = new DOMXPath($domDocument);
	// 入れ物を探す
	$nodes = $xpath->query($query);
	$limited = 0;
	foreach($nodes as $node) {
		$texts = $xpath->query($queryText,$node);
		$itemName='';
		foreach($texts as $text) {$itemName.=$domDocument->saveXML($text);}
		if(strpos($itemName,'▼▼')!==false){
			$limited = 1;//期間限定表示
			continue;
		}
		// データ取得
		$innerDatas = $xpath->query($queryInner,$node);
		$itemData = ['name'=>$itemName,'limited'=>$limited];
		foreach($innerDatas as $innerNode) {
			searchEquipData($itemData,$innerNode);
		}
		array_push($result,$itemData);
	}
	return $result;
}
function getAllEquipData(){
	$allurls = getAllEquipURLs();
	$allData = array();
	foreach($allurls as $urls){
		foreach($urls as $url){
			$newData = getEquipData($url);
			$allData = array_merge($allData,$newData);
		}
	}
	return $allData;
}
function searchCrystaData(&$result,$Alltext){
	if(!$Alltext || strlen($Alltext)<=0)return;
	if(strpos($Alltext,'トレード不可')!==false){
		$result['notrade']=1;
		return;
	}
	if(strpos($Alltext,'限定品')!==false){
		$result['limited']=1;
		return;
	}
	$base = preg_split('/：|；/',$Alltext,null,PREG_SPLIT_DELIM_CAPTURE);

	switch($base[0]){
		case '部位':
			if(strpos($base[1],'全')!==false){$result['subtype']='ノーマル';}
			else if(strpos($base[1],'武器')!==false){$result['subtype']='ウエポン';}
			else if(strpos($base[1],'体')!==false){$result['subtype']='アーマー';}
			else if(strpos($base[1],'追加')!==false){$result['subtype']='オプション';}
			else if(strpos($base[1],'特殊')!==false){$result['subtype']='アクセサリー';}
		break;
		case '強化するクリスタ':case '上書きするクリスタ':
			$result['extra']=str_replace('◇','',$base[1]);
		break;
		case '効果':
			$propertys = explode('、',$base[1]);
			foreach($propertys as $key=>$value){
				$preg = preg_split('/([-+][\d]*)(\%?)/',$value,null,PREG_SPLIT_DELIM_CAPTURE);
				if(count($preg)==1){
					$preg=preg_split('/\ |　/',$value);
					//$preg=explode('　',$preg[0]);
					if(strpos($preg[1],'%')!==false){
						$preg[1] = str_replace('%','',$preg[1]);
						$preg[2] = "%";
					}
				}
				$propertys[$key]=$preg;
			}
			$result['property']=$propertys;
		break;
	}
}
function getCrystaData($url){
	static $query = '//div[@id="main-content"]//h3';
	static $queryText = 'span/span/text()';
	$result = array();
	$domDocument = new DOMDocument();
	if( !@$domDocument->loadHTMLFile($url) )return $result;
	
	$xpath = new DOMXPath($domDocument);
	// 入れ物を探す
	$nodes = $xpath->query($query);
	foreach($nodes as $node) {
		$texts = $xpath->query($queryText,$node);
		$itemName='';
		foreach($texts as $text) {$itemName.=$domDocument->saveXML($text);}
		$itemName = str_replace('◇','',$itemName);
		// データ取得
		$itemData = ['name'=>$itemName,'limited'=>0,'notrade'=>0,'extra'=>null];
		$innnerText = "";
		$next = $node->nextSibling;
		while($next) {
			if($next instanceof DOMElement){
				if($next->tagName=='h3')break;
				if($next->tagName=='br'){
					searchCrystaData($itemData,$innnerText);
					$innnerText='';
				}else{
					$innnerText.=$next->textContent;
				}
			}else if($next instanceof DOMText){
				$innnerText.=$next->wholeText;
			}
			$next = $next->nextSibling;
		}
		array_push($result,$itemData);
	}
	return $result;
}
function getAllCrystaData(){
	$allData = getCrystaData('https://www.dopr.net/toramonline-wiki/item_crystal');
	return $allData;
}
?>
