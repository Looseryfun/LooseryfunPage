<?php
	include_once 'myfunctions.php';
	function searchPath($string){
		return "http://www.google.co.jp/search?q=%E3%83%88%E3%83%BC%E3%83%A9%E3%83%A0+".urlencode($string);
	}
	function echoProperty($propertyClass){
		global $propMaster;
		$maintypedata = @$propMaster[$propertyClass->maintype];
		$subtypedata = @$maintypedata[$propertyClass->subtype.'_'.$propertyClass->percent];
		$name = @$subtypedata['name'];
		$percent = (@$subtypedata['percent'])?('%'):('');
		echo htmlspecialchars($name)
			.'<span class="rightpos">'
			.sprintf('%5d%s',$propertyClass->power,htmlspecialchars($percent))
			.'</span>';
	}
	function echoPropertys($propertyList){
		$rowcount = floor((count($propertyList)+1)/2);
		if($rowcount<=0)return;
		for($i=0;$i<$rowcount;$i++){
			echo '<tr>';
			echoProperty($propertyList[$i*2]);
			if(isset($propertyList[$i*2+1])){
				echo '<td class="propertylist">';
				echoProperty($propertyList[$i*2+1]);
				echo '</td>';
			}else{
				echo '<td></td>';
			}
			echo '</tr>';
		}
	}
	function echoInfoIcon($img,$msg,$break=false){
		?>
		<span class="nohelp <?php echo ($break)?('break'):(''); ?>">
			<span class="icon"><img onClick="iconHElpText(this);" src="<?php echo $img; ?>"/></span>
			<span class="helptext"><?php echo htmlspecialchars($msg); ?></span>
		</span>
		<?php
	}
	function echoItemTable($data){
		$limitedImg = 'img/%e6%9c%9f%e9%96%93%e9%99%90%e5%ae%9a.png';
		$tradeImg = 'img/%e3%83%88%e3%83%ac%e3%83%bc%e3%83%89.png';
		$itemMaster = ItemMaster::getItemTypeList();
		$propMaster = ItemMaster::getItemPropertyList();
		$materialMaster = ItemMaster::getMaterialTypeList();
		$gettypeMaster = ItemMaster::getGetTypeList();
		$subtypeData = @$itemMaster[$data->maintype]['sub'][$data->subtype];
		$subtypeName = @$subtypeData['name'];
		$subtypeImagePath = @$subtypeData['img'];
		$materialName = @$materialMaster[$data->breaktype]['name'];
		$materialImg = @$materialMaster[$data->breaktype]['img'];
		$gettypeName = @$gettypeMaster[$data->gettype]['name'];
		$gettypeImg = @$gettypeMaster[$data->gettype]['img'];
?>
	<div style="">
	<table class="itemview"><tbody>
		<tr><th colspan="2"><?php echo htmlspecialchars($data->name); ?>
			<span class="rightpos"><a class="outlink" href="<?php echo searchPath($data->name); ?>" rel="nofollow"><img src="img/虫眼鏡.png" class="tableicon"/></a></span></th></tr>
		<tr><td><?php echo htmlspecialchars($subtypeName); ?><span><img src="<?php echo htmlspecialchars($subtypeImagePath); ?>" class="tableicon rightpos"/></span></td>
			<td>入手元:<span class="rightpos"><img src="<?php echo $gettypeImg; ?>" class="tableicon"/><?php echo htmlspecialchars($gettypeName); ?></span></td></tr>
		<tr><td>分解:<span class="rightpos"><img src="<?php echo $materialImg; ?>" class="tableicon"/>:<?php echo htmlspecialchars($data->breakpoint); ?>pt</span></td>
			<td><span class="rightpos"><?php echoInfoIcon($tradeImg,'トレード',$data->notrade); ?>
				<?php echo ($data->limited)?(echoInfoIcon($limitedImg,'期間限定',false)):(''); ?></span></td></tr>
		<tr><td>ATK:<span class="rightpos"><?php echo htmlspecialchars($data->power); ?></span></td><td>安定率:<span class="rightpos"><?php echo htmlspecialchars($data->stability); ?>%</span></td></tr>
		<?php echoPropertys($data->propertyList); ?>
		<tr><td colspan="2">備考：<span class="rightpos"><?php if($data->makedate)echo '実装日'.htmlspecialchars($data->makedate); ?></span><br/>
			<?php echo htmlspecialchars($data->help); ?></td></tr>
	</tbody></table>
	</div>
<?php
	}
?>
