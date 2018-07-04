<?php
	include_once 'myinclude/pageheader.php';
	if(!isAdminUser()){
		echo '権限がありません<br/>';
		include 'myinclude/pagefooter.php';
		exit();
	}
	if(!isset($_GET['name'])||!isset($_GET['state'])){
		redirectPage('index.php');
	}
	$userid = $_GET['name'];
	$state = ($_GET['state']=="on")?(GRANT_EDIT):(GRANT_GUEST);
	$result = changeUserGrant($userid,$state);
?>
	<div class="title-1"><h3 id="content_2">ユーザー状態変更</h3></div>
	<div>
		<table><tbody>
	<?php
		if($result==false){
			echo '<tr><td>エラーが発生しました</td></tr>';
			echo '<tr><td>'.htmlEscape(getDBErrorString()).'</td></tr>';
		}else{
			echo '<tr><td>'.htmlEscape($userid).'</td><td>'.htmlEscape($_GET['state']).'</td><td>変更完了</td></tr>';
		}
	?>
		</tbody></table>
		<a href="manageuser.php">戻る</a>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
