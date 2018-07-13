<?php
	include_once '../myinclude/myfunctions.php';
	include_once '../myinclude/skilleditview.php';

	define('ICON_SCALE',2);
	define('ALL_SCALE',3);

function loadImage($skillData){
	$imgPath = '../'.urldecode($skillData['img']);
	$image= imageCreateFromPNG($imgPath);
	return $image;
}

function drawIcon($target,$image,$x,$y){
	$iconWidth = imagesx($image);
	$iconHeight = imagesy($image);
	return imagecopyresized($target,$image
		,($x+SKILL_ICON_X*ICON_SCALE/2)*ALL_SCALE
		,($y+SKILL_ICON_Y*ICON_SCALE/2)*ALL_SCALE,0,0
		,SKILL_ICON_X*ICON_SCALE*ALL_SCALE
		,SKILL_ICON_Y*ICON_SCALE*ALL_SCALE
		,$iconWidth,$iconHeight);
}

	$skillRequest = @$_GET['s'];
	$skillValues = null;
	if($skillRequest)$skillValues = toSkillValues($skillRequest);
	$userLevel = 160;
	$levelRequest = @$_GET['lv'];
	if($levelRequest)$userLevel = intval($levelRequest);

	if(!$skillValues||count($skillValues)<=0){
		header('Content-Type: image/png');
		readfile('../img/batu.png');
		exit();
	}
	$skillMaster = SkillMaster::getSkillList();
	$keys = array_keys($skillValues);
	$masterData = null;
	$skillValue = null;
	$treeID = 0;
	foreach($keys as $key){
		if(!isset($skillMaster[$key]))continue;
		$treeID = $key;
		$masterData = $skillMaster[$treeID]['sub'];
		$skillValue = $skillValues[$treeID];
		break;	// 最初の一つ
	}
	if(!$masterData){
		header('Content-Type: image/png');
		readfile('../img/batu.png');
		exit();
	}
	$imageWidth = 0;
	$imageHeight = 0;
	foreach($masterData as $skillID=>&$data){
		if(isset($skillValue[$skillID]))$data['value'] = $skillValue[$skillID];
		else $data['value'] = 0;
		$imageWidth = max($imageWidth,$data['x']);
		$imageHeight = max($imageHeight,$data['y']);
	}
	$imageWidth = (($imageWidth)*SKILL_LAYOUT_X+SKILL_ICON_X*ICON_SCALE*2)*ALL_SCALE;
	$imageHeight = (($imageHeight)*SKILL_LAYOUT_Y+SKILL_ICON_Y*ICON_SCALE*2)*ALL_SCALE;
	
	//readfile('../img/心眼.png');
	
// 空の画像を作成し、テキストを追加します
$image = imagecreatetruecolor($imageWidth, $imageHeight);
// ピクセルブレンド停止 → PNGでα値を使用する
//imageLayerEffect($image, IMG_EFFECT_ALPHABLEND);
imageAlphaBlending($image, true);
imageSaveAlpha($image, true);

$transparent = imageColorAllocateAlpha($image, 0xFF, 0x00, 0xFF, 127);
imageFill($image, 0, 0, $transparent);

foreach($masterData as $skillID=>$data){
	$iconImage = loadImage($data);
	drawIcon($image,$iconImage,$data['x']*SKILL_LAYOUT_X,$data['y']*SKILL_LAYOUT_Y);
}

$text_color = imagecolorallocate($image, 233, 14, 91);
imagestring($image, 1, 5, 5,  "A Simple Text String", $text_color);

header('Content-Type: image/png');
header('Content-transfer-encoding: binary');

imagepng($image);

// メモリを開放します
imagedestroy($image);
?>