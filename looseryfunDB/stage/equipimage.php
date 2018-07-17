<?php
	include_once 'myinclude/pageheader.php';
	include_once 'myinclude/myfunctions.php';
	include_once 'myinclude/wikireader.php';
	
	$typename = @$_GET['name'];
	$allData = getEquipImgByName($typename);
	if(!$allData){
		echo htmlspecialchars('データがありません');
		include 'myinclude/pagefooter.php';
		exit();
	}
?>
<script type="text/javascript"> 
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
			<span class=""><span class="rightpos icon"><img src="img/%e5%bf%83%e7%9c%bc.png" onclick="changeShowImg(this)"/></span></span>
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
