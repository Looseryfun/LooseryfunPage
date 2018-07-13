<?php
	include_once 'myinclude/pageheader.php';
	include_once 'myinclude/skilleditview.php';

	$skillMaster = SkillMaster::getSkillList();
	$skillRequest = @$_GET['s'];
	$skillValues = null;
	if($skillRequest)$skillValues = toSkillValues($skillRequest);
	$userLevel = 160;
	$levelRequest = @$_GET['lv'];
	if($levelRequest)$userLevel = intval($levelRequest);
?>
<script type="text/javascript"> 
	var skillMaster = <?php echo json_encode($skillMaster); ?>;
	var skillValues = <?php echo json_encode($skillValues); ?>;
	window.addEventListener('DOMContentLoaded',function(){
		var target = document.getElementById('mylevel');
		if(target){
			target.value = <?php echo json_encode($userLevel); ?>;
			updateUserLevel(Number(target.value),skillMaster);
		}
		if(skillValues){
			setAllSkillValues(skillValues,skillMaster);
			updateLinks.updated = true;
		}
	});
	window.addEventListener('DOMContentLoaded',function(){
		var links=['alllink'];
		for( var treeid in skillMaster ){
			links.push('makeimage_'+String(treeid));
		}
		setInterval(updateLinks,500,links,skillMaster,'mylevel');
	});
	var t = 0;
	document.documentElement.addEventListener('touchend', function (e) {
		var now = new Date().getTime();
		if ((now - t) < 300){
			e.preventDefault();
		}
		t = now;
	}, false);
</script>
	<div class="title-1"><h3>スキルエディター</h3></div>
	<div>
		<ul class="list-1">
			<li>スキルアイコンをタップするとレベルアップ、downをタップすると下げることができます。</li>
			<li>トラブルは<a class="outlink" href="http://looseryfun.game-info.wiki/d/%ca%d4%bd%b8%b0%cd%cd%ea" rel="nofollow">ぽよんと</a>までお願いします。</li>
		</ul>
		<div><a id="alllink" treeid="all" href="skilleditor.php?">保存用URL</a>※長押しでURLをコピーすると現在の内容を保管できます。</div>
		<div>最大のキャラクターレベル<input id="mylevel" type="number" size="4" value="160" onkeyup="updateUserLevel(this.value,skillMaster)"></input>※スキルツリーの開放判定に使用されます</div>
		<div>　</div>
		<div class="title-3"><h5>総使用ポイント：<span id="skill_all_total">0</span>pt</h5></div>
	</div>
	<div id="showarea">
<?php
foreach($skillMaster as $key=>$value){
	echoSkillTree($key,$value,'skillMaster');
}
?>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
