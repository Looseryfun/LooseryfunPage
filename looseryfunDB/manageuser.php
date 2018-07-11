<?php
	include_once 'myinclude/pageheader.php';
	if(!isAdminUser()){
		echo '権限がありません<br/>';
		include 'myinclude/pagefooter.php';
		exit();
	}
	$userList = getUserList();
?>
	<div class="title-1"><h3 id="content_2">新規編集ユーザー登録</h3></div>
	<div>
		<table><tbody>
	<?php
		if(!is_array($userList)){
			echo '<tr><td>有効なユーザーではありません</td></tr>';
		}else{
			echo '<tr><th>ID</th><th>操作</th></tr>';
			foreach($userList as $row){
				switch($row['grant']){
					case GRANT_GUEST:
						$onoff = '<a href="userchangestate.php?name='.urlencode($row['name']).'&state=on">有効にする</a>';
					break;
					case GRANT_EDIT:
						$onoff = '<a href="userchangestate.php?name='.urlencode($row['name']).'&state=off">無効にする</a>';
					break;
					default:
						$onoff = '';
					break;
				}
				echo '<tr><td>'.htmlEscape($row['name']).'</td><td>'.$onoff.'</td></tr>';
			}
		}
	?>
		</tbody></table>
	</div>
	<div>
		<form action="adduserexec.php" method="post">
			<div style="text-align: center;">
				新規ユーザーの登録<br/>
				<table><tbody>
					<tr><td>ID</td><td><input type="text" name="id" size="40"></td></tr>
					<tr><td>パスワード</td><td><input type="password" name="pass" size="40"></td></tr>
				</tbody></table>
				<input type="submit" value="登録する">
			</div>
		</form>
		</tbody></table>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
