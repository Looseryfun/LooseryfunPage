<?php
	$html_title='シナリオEXP皮算用機';
	include_once 'myinclude/pageheader.php';
	include_once 'myinclude/wikireader.php';
	$result = getAllScenarioExpData();
?>
<script type="text/javascript"> 
	var expMaster = <?php echo json_encode($result); ?>;
	function getScenarioExpTable(chapter,section){
		var result = '<table><tbody>';
		for(var i=chapter;i<expMaster.length;i++){
			result += '<tr><th colspan="2">'+expMaster[i]['name']+'</th></tr>';
			var subData = expMaster[i]['sub'];
			for(var j=section;j<subData.length;j++){
				result += '<tr>';
				result += '<td>'+subData[j]['name']+'</td>';
				result += '<td>'+subData[j]['exp']+'</td>';
				result += '</tr>';
			}
			section=0;
		}
		result += '</tbody></table>';
		return result;
	}
	function getNeedNextLevelExp(nowlevel){
		return Math.floor(Math.pow(nowlevel,4)/40)+(nowlevel*2);
	}
	function getScenarioExp(chapter,section){
		var exp=0;
		for(var i=chapter;i<expMaster.length;i++){
			var subData = expMaster[i]['sub'];
			for(var j=section;j<subData.length;j++){
				exp += Number(subData[j]['exp']);
			}
			section=0;
		}
		return exp;
	}
	function jobExp(){
		var chapterTag = document.getElementById('chapter');
		var sectionTag = document.getElementById('section');
		var levelTag = document.getElementById('level');
		var getExp = getScenarioExp(chapterTag.value,sectionTag.value);

		var result = '獲得経験値：'+getExp+'<br/>';
		var level = Number(levelTag.value);
		while(getNeedNextLevelExp(level)<=getExp){
			level++;
			getExp-=getNeedNextLevelExp(level);
		}
		result += 'シナリオクリア後のレベル：'+level+'<br/>';
		var nextExp = getNeedNextLevelExp(level)-getExp;
		result += '次のレベルまで：'+nextExp+'<br/>';
		
		var resultTag = document.getElementById('result');
		resultTag.innerHTML = result;

		var scenarioexpTag = document.getElementById('scenarioexp');
		scenarioexpTag.innerHTML = getScenarioExpTable(chapterTag.value,sectionTag.value);
	}
	window.addEventListener('DOMContentLoaded',function(){
		setItemTypeList('chapter',expMaster);
	});
</script>
	<div class="title-1"><h3 id="content_2">シナリオEXP皮算用機</h3></div>
<div>
	<ul class="list-1">
		<li>シナリオを進めるとどのぐらいレベルが上がるか計算します。</li>
		<li>このデータは<a class="outlink" href="https://www.dopr.net/toramonline-wiki/" rel="nofollow">【アソビモ】トーラムオンライン攻略情報まとめ【wiki】</a>の情報を再構成したものです。</li>
		<li>データは自動的に作成されています。トラブルは<a class="outlink" href="http://looseryfun.game-info.wiki/d/%ca%d4%bd%b8%b0%cd%cd%ea" rel="nofollow">ぽよんと</a>までお願いします。</li>
		<li>シナリオ中に戦うボスの経験値などは含まれていません。</li>
	</ul>
</div>
	<div>
		現在のシナリオ<br/>
		<select id="chapter" subid="section"></select><br/>
		<select id="section"></select><br/>
		自分のレベル<br/>
		<input id="level" type="number" value="1" /><br/>
		<input type="button" value="計算する" onclick="jobExp();" /><br/>
	</div>
	<br/>
	<div id="result">
	</div>
	<div id="scenarioexp">
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
