<?php
	include_once 'myinclude/pageheader.php';
	include_once 'myinclude/skilleditview.php';

	$skillRequest = @$_GET['s'];
	if($skillRequest){
	}
?>
<script type="text/javascript"> 
	var skillMaster = <?php echo json_encode($skillMaster); ?>;
</script>
	<div class="title-1"><h3>スキルエディター</h3></div>
	<div>
		<ul class="list-1">
			<li>スキルアイコンをタップするとレベルアップ、downをタップすると下げることができます。</li>
			<li>トラブルは<a class="outlink" href="http://looseryfun.game-info.wiki/d/%ca%d4%bd%b8%b0%cd%cd%ea" rel="nofollow">ぽよんと</a>までお願いします。</li>
		</ul>
		<div class="title-3"><h5>総使用ポイント：<span id="skill_all_total">0</span>pt</h5></div>
	</div>
	<div id="showarea">
<?php
foreach($skillMaster as $key=>$value){
	echoSkillTree($key,$value);
}
?>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
