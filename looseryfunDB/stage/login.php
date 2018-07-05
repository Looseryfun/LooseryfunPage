<?php
	include_once 'myinclude/pageheader.php';
?>
	<div class="title-1"><h3>ログイン ページ</h3></div>

	<div>
		<ul class="list-1">
			<li>情報提供の場合、ログインの必要はありません。</li>
			<li>wikiページとは別のアカウントになります。</li>
			<li>新規登録希望者は<a class="outlink" href="http://looseryfun.game-info.wiki/d/%ca%d4%bd%b8%b0%cd%cd%ea" rel="nofollow">ぽよんと</a>までお願いします。</li>
		</ul>
		<form action="loginExec.php" method="post">
			<div style="text-align: center;">
				<table><tbody>
					<tr><td>ID</td><td><input type="text" name="id" size="40"></td></tr>
					<tr><td>パスワード</td><td><input type="password" name="pass" size="40"></td></tr>
<?php
	if(isset($isError)){
		echo '<tr><td></td><td>IDまたはパスワードが違います</td></tr>';
		echo '<tr><td>エラーコード</td><td>'.htmlEscape(getDBErrorString()).'</td></tr>';
	}
?>
				</tbody></table>
				<input type="submit" value="ログイン">
			</div>
		</form>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
