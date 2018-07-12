<?php

define('SKILL_CACHETIME',6*60*60);

define('SKILL_ICON_X',20);
define('SKILL_ICON_Y',20);
define('SKILL_LAYOUT_X',100);
define('SKILL_LAYOUT_Y',50);
define('SKILL_LINE_WIDTH',5*2);
$skillMaster = SkillMaster::getSkillList();
function echoSkill($treeid, $skillid, $skillData){
if(!$skillData)return;
$x = ($skillData['x']*SKILL_LAYOUT_X).'px';
$y = ($skillData['y']*SKILL_LAYOUT_Y).'px';
$name = htmlspecialchars($skillData['name']);
$imgPath = $skillData['img'];
$id='skill_'.$treeid.'_'.$skillid;
$before='';
if($skillData['before']>0){
	$before='skill_'.$treeid.'_'.$skillData['before'];
	$before='before="'.$before.'"';
}

$style = "left: $x;top: $y;";
echo "<div class=\"skillpanel\" id=\"skill$skillid\" style=\"$style\">";
echo '<table class="clearbox">';
echo "<tr><td rowspan=\"2\"><img class=\"skillicon\" target=\"$id\" src=\"$imgPath\" alt=\"$name\"></td>";
echo '<td><span id="'.$id.'" '.$before.' class="skilllevel">'.'0'.'</span></td></tr>';
echo '<tr><td>'.'<img class="downicon" target="'.$id.'" src="img/down.png" alt="下げる">'.'</td>';
echo '</table>';
echo '</div>';
}
function echoBorderLine($base,$next){
$left = min($base['x'],$next['x'])*SKILL_LAYOUT_X-(SKILL_LINE_WIDTH/2)+(SKILL_ICON_X);
$right = max($base['x'],$next['x'])*SKILL_LAYOUT_X+(SKILL_LINE_WIDTH/2)+(SKILL_ICON_X);
$top = min($base['y'],$next['y'])*SKILL_LAYOUT_Y-(SKILL_LINE_WIDTH/2)+(SKILL_ICON_Y);
$bottom = max($base['y'],$next['y'])*SKILL_LAYOUT_Y+(SKILL_LINE_WIDTH/2)+(SKILL_ICON_Y);

$style = 'left:'.$left.'px;top:'.$top.'px;width:'.($right-$left).'px;height:'.($bottom-$top).'px;';
echo "<div class=\"skillborder\" style=\"$style\" >";
echo '</div>';
}
function echoSubBorderLine($base,$next){
$xpos = ($base['x']+$next['x'])*SKILL_LAYOUT_X/2;
//$ypos = max($base['y'],$next['y'])*SKILL_LAYOUT_Y/2;

$left = $xpos-(SKILL_LINE_WIDTH/2)+(SKILL_ICON_X);
$right = $xpos+(SKILL_LINE_WIDTH/2)+(SKILL_ICON_X);
$top = min($base['y'],$next['y'])*SKILL_LAYOUT_Y-(SKILL_LINE_WIDTH/2)+(SKILL_ICON_Y);
$bottom = max($base['y'],$next['y'])*SKILL_LAYOUT_Y+(SKILL_LINE_WIDTH/2)+(SKILL_ICON_Y);
$style = 'left:'.$left.'px;top:'.$top.'px;width:'.($right-$left).'px;height:'.($bottom-$top).'px;';
echo "<div class=\"skillborder\" style=\"$style\" >";
echo '</div>';

$left = $xpos-(SKILL_LINE_WIDTH/2)+(SKILL_ICON_X);
$right = max($base['x'],$next['x'])*SKILL_LAYOUT_X+(SKILL_LINE_WIDTH/2)+(SKILL_ICON_X);
$top = max($base['y'],$next['y'])*SKILL_LAYOUT_Y-(SKILL_LINE_WIDTH/2)+(SKILL_ICON_Y);
$bottom = max($base['y'],$next['y'])*SKILL_LAYOUT_Y+(SKILL_LINE_WIDTH/2)+(SKILL_ICON_Y);

$style = 'left:'.$left.'px;top:'.$top.'px;width:'.($right-$left).'px;height:'.($bottom-$top).'px;';
echo "<div class=\"skillborder\" style=\"$style\" >";
echo '</div>';
}
function echoBorderLines($skillData){
foreach($skillData['sub'] as $key=>$value){
	$before = $value['before'];
	if($before<=0)continue;

	$base = $skillData['sub'][$before];
	$next = $value;
	$dx = abs($base['x']-$next['x']);
	$dy = abs($base['y']-$next['y']);
	if($dx!=0&&$dy!=0){
		echoSubBorderLine($base,$next);
	}else{
		echoBorderLine($base,$next);
	}
}
}
function echoSkillTree($skillid, $skillData){
if(!$skillData)return;
$maxy = 0;
foreach($skillData['sub'] as $sub){
	$maxy = max($sub['y'],$maxy);
}
$height = SKILL_LAYOUT_Y*($maxy+1).'px';

echo '<div class="title-1"><h3>'.htmlspecialchars($skillData['name']).'</h3></div>';
echo "<div class=\"skilltree\" id=\"tree$skillid\" style=\"height:$height;\">";
echo '<div class="skillicons">';
echoBorderLines($skillData);
foreach($skillData['sub'] as $key=>$value){
	echoSkill($skillid,$key,$value);
}
echo '</div>';
echo '</div>';
}

/**
 * スキルマスタデータ
 */
class SkillMaster{
	protected static function makeImgPath($rows){
		foreach($rows as &$row){
			foreach($row as $key=>$value){
				if($key=='name'){$row['img']='img/skill/'.urlencode($value).'.png';}
			}
		}
		return $rows;
	}
	// アイテム種別
	public static function getSkillList(){
		return SkillMaster::generateSkillData('skillMasterData');
		$result=apcu_entry('skillMasterData','SkillMaster::generateSkillData',MASTER_CACHETIME);
		return $result;
	}
	protected static function generateSkillData($key){
		$result = getSQLKeyValueRecords("SELECT id, name FROM `skilltrees` order by id asc",array());
		foreach($result as $mainkey=>&$newData){
			$subrows = SkillMaster::makeImgPath(
				getSQLKeyValueRecords("SELECT subtype, name, level, `before`, `x`, `y` FROM `skilldata` WHERE maintype=? order by subtype asc",array($mainkey))
			);

			$newData['sub'] = $subrows;
		}
		return $result;
	}
}
?>
