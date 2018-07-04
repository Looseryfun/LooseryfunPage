<?php
	include_once 'myinclude/pageheader.php';
	if(!isAdminUser()){
		echo '権限がありません<br/>';
		include 'myinclude/pagefooter.php';
		exit();
	}
	if(!isset($_POST['id'])||!isset($_POST['pass'])){
		pageRedirect('index.php');
	}
	$userID = $_POST['id'];
	$userPass = $_POST['pass'];
	beginTransaction();
	$result = addUser($userID,$userPass);
	commitTransaction();
?>
	<div class="title-1"><h3 id="content_2">ユーザー登録</h3></div>
	<div>
		<table><tbody>
	<?php
		if($result==false){
			echo '<tr><td>エラーが発生しました</td></tr>';
			echo '<tr><td>'.htmlEscape(getDBErrorString()).'</td></tr>';
		}else{
			echo '<tr><td>登録完了</td></tr>';
		}
	?>
		</tbody></table>
		<a href="manageuser.php">戻る</a>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
