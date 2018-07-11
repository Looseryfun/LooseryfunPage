<?php
	include_once 'myinclude/pageheader.php';
?>
	<div class="title-1"><h3>Loosery funデータベース</h3></div>
	<div><?php if(isset($topPageMsg)){echo $topPageMsg;} ?></div>
	<div class="title-1"><h3>情報提供</h3></div>
	<ul class="list-1">
		<li>制作中-アイテム<!--<a href="entryitemtype.php">アイテム</a>--></li>
		<li><a href="armorimage.php">体防具一覧</a></li>
	</ul>
	<div>
	<div class="title-1"><h3>管理機能</h3></div>
	<?php
		$loginName="ログイン";
		$href="login.php";
		if (isLogin()) {
    		$loginName="ログアウト";
			$href="logout.php";
			echo '<div><a href="kanri.php">管理ページへ</a></div>';
		}
		echo "<div><a href=\"$href\">$loginName</a></div>";
	?>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
