<?php
	include_once '../myinclude/myfunctions.php';
	include_once '../myinclude/skilleditview.php';

	define('ICON_SCALE',2);
	define('ALL_SCALE',3);
	define('ICON_WIDTH',SKILL_ICON_X*ICON_SCALE*ALL_SCALE);
	define('ICON_HEIGHT',SKILL_ICON_Y*ICON_SCALE*ALL_SCALE);
	define('LAYOUT_X',SKILL_LAYOUT_X*ALL_SCALE);
	define('LAYOUT_Y',SKILL_LAYOUT_Y*ALL_SCALE);
	define('JP_FONT','./HigashiOme-Gothic-1.3i.ttf');
	define('NUM_FONT','/usr/share/fonts/dejavu/DejaVuSerif-Bold.ttf');
	ini_set( 'display_errors', 0 );

	// 画像読み込み
function loadImage($skillData){
	$imgPath = '../'.urldecode($skillData['img']);
	$image= imageCreateFromPNG($imgPath);
	return $image;
}
$textColor=null;
$textShadowColor=null;
// 中央揃えで文字描写
function drawCenterText($image,$text,$x,$y,$size,$font){
	global $textColor,$textShadowColor;
	$result = ImageTTFBBox($size, 0, $font, $text);
	$width = abs($result[6]-$result[2]);
	// 影
	ImageTTFText($image, $size, 0,$x-$width/2-2,$y,$textShadowColor,$font, $text);
	ImageTTFText($image, $size, 0,$x-$width/2+2,$y,$textShadowColor,$font, $text);
	ImageTTFText($image, $size, 0,$x-$width/2,$y-2,$textShadowColor,$font, $text);
	ImageTTFText($image, $size, 0,$x-$width/2,$y+2,$textShadowColor,$font, $text);
	ImageTTFText($image, $size, 0,$x-$width/2-1,$y-1,$textShadowColor,$font, $text);
	ImageTTFText($image, $size, 0,$x-$width/2+1,$y+1,$textShadowColor,$font, $text);
	ImageTTFText($image, $size, 0,$x-$width/2+1,$y-1,$textShadowColor,$font, $text);
	ImageTTFText($image, $size, 0,$x-$width/2-1,$y+1,$textShadowColor,$font, $text);
	// 本体
	ImageTTFText($image, $size, 0,
		$x-$width/2,$y,$textColor,
		$font, $text);
}
// あいこんびょうしゃ
function drawIcon($target,$image,$x,$y){
	$iconWidth = imagesx($image);
	$iconHeight = imagesy($image);
	return imagecopyresized($target,$image,
		$x*LAYOUT_X + ICON_WIDTH/2,
		$y*LAYOUT_Y + ICON_HEIGHT/2,
		0,0,
		ICON_WIDTH,
		ICON_HEIGHT,
		$iconWidth,$iconHeight);
}
// 線びょうしゃ
function drawBorderLine($image, $base,$next, $color){
	$x1 = $base['x']*LAYOUT_X+ICON_WIDTH;
	$y1 = $base['y']*LAYOUT_Y+ICON_WIDTH;
	$x2 = $next['x']*LAYOUT_X+ICON_WIDTH;
	$y2 = $next['y']*LAYOUT_Y+ICON_WIDTH;
	if(($x1-$x2)!=0&&($y1-$y2)!=0){
		$halfPos = ($x1+$x2)/2;
		imageline($image,$halfPos-(SKILL_LINE_WIDTH*ALL_SCALE)/2,$y2,$x2,$y2,$color);
		imageline($image,$halfPos,$y1,$halfPos,$y2,$color);
	}else imageline($image,$x1,$y1,$x2,$y2,$color);
}
	// GET取得
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
	// マスタデータ取得
	$skillMaster = SkillMaster::getSkillList();
	$keys = array_keys($skillValues);
	$drawData = null;
	$levelData = null;
	$skillValue = null;
	$treeID = 0;
	foreach($keys as $key){
		if(!isset($skillMaster[$key]))continue;
		$treeID = $key;
		$drawData = $skillMaster[$treeID]['sub'];
		$levelData = $skillMaster[$treeID]['level'];
		$skillValue = $skillValues[$treeID];
		break;	// 最初の一つ
	}
	if(!$drawData){
		header('Content-Type: image/png');
		readfile('../img/batu.png');
		exit();
	}
	// 使用可能ツリーレベル取得
	$enableLevels = array();
	foreach($levelData as $treelevel=>$data){
		if($userLevel<$data['need'])continue;
		array_push($enableLevels,$treelevel);
	}
	// 画像幅と高さ計算
	$imageWidth = 0;
	$imageHeight = 0;
	foreach($drawData as $skillID=>$data){
		if(isset($skillValue[$skillID]))$drawData[$skillID]['value'] = $skillValue[$skillID];
		else $drawData[$skillID]['value'] = 0;
		$enable = in_array($drawData[$skillID]['level'],$enableLevels);
		$used = $drawData[$skillID]['value']>0&&$enable;
		$drawData[$skillID]['enable']=$enable;
		$drawData[$skillID]['used']=$used;
		$imageWidth = max($imageWidth,$data['x']);
		$imageHeight = max($imageHeight,$data['y']);
	}
	
	foreach($drawData as $skillID=>$data){
		if(isset($skillValue[$skillID]))$drawData[$skillID]['value'] = $skillValue[$skillID];
		else $drawData[$skillID]['value'] = 0;
		$imageWidth = max($imageWidth,$data['x']);
		$imageHeight = max($imageHeight,$data['y']);
	}
	$imageWidth = (($imageWidth)*SKILL_LAYOUT_X+SKILL_ICON_X*ICON_SCALE*2)*ALL_SCALE;
	$imageHeight = (($imageHeight)*SKILL_LAYOUT_Y+SKILL_ICON_Y*ICON_SCALE*2)*ALL_SCALE;

// 空の画像を作成し
$image = imagecreatetruecolor($imageWidth, $imageHeight);
imageSaveAlpha($image, true);

$transparent = imageColorAllocateAlpha($image, 0xFF, 0x00, 0xFF, 127);
imageFill($image, 0, 0, $transparent);

$lineColor = imageColorAllocateAlpha($image, 255, 64, 64, (1.0-0.7)*127);

// 線を引く
//imageLayerEffect($image, IMG_EFFECT_ALPHABLEND);
imageAlphaBlending($image, false);
imagesetthickness($image,SKILL_LINE_WIDTH*ALL_SCALE);
foreach($drawData as $skillID=>$data){
	$base = @$drawData[$data['before']];
	if(!$base)continue;
	drawBorderLine($image,$base,$data,$lineColor);
}
imageAlphaBlending($image, true);

// あいこんびょうしゃ　
$batuImage = imagecreatefrompng('../img/batu.png');
foreach($drawData as $skillID=>$data){
	$iconImage = loadImage($data);
	if(!$data['used']){imagefilter($iconImage, IMG_FILTER_GRAYSCALE);}
	drawIcon($image,$iconImage,$data['x'],$data['y']);
	imagedestroy($iconImage);
	if(!$data['enable'])drawIcon($image,$batuImage,$data['x'],$data['y']);
}
imagedestroy($batuImage);
$textColor=imagecolorallocate($image, 255, 255, 255);
$textShadowColor=imagecolorallocate($image, 15, 15, 20);
// 文字描写
foreach($drawData as $skillID=>$data){
	$x = $data['x']*LAYOUT_X+ICON_WIDTH;
	$y = $data['y']*LAYOUT_Y+ICON_HEIGHT/2+ICON_HEIGHT/10;
	$level = $data['value'];
	if($level>0){
		$levelString = sprintf("Lv.%2d",$data['value']);
		drawCenterText($image,$levelString,$x,$y,32,NUM_FONT);
	}
	$name = $data['name'];
	$y = $y+ICON_HEIGHT-ICON_HEIGHT/10;
	drawCenterText($image,$name,$x,$y,18,JP_FONT);
}
drawCenterText($image,'(c)Toram Online',$imageWidth-100,$imageHeight-4,18,JP_FONT);

	//exec("fc-list", $output);
	//var_dump($output);
	//exit();
header('Content-Type: image/png');
header('Content-transfer-encoding: binary');
imagepng($image);
imagedestroy($image);
?>