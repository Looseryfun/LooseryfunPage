<?php
	include_once 'myinclude/pageheader.php';
?>
	<div class="title-1"><h3 id="content_2">Loosery funデータベース</h3></div>
	<div><?php if(isset($topPageMsg)){echo $topPageMsg;} ?></div>
	<?php
		echo "HELLO WORLD";
	?>
	<div>
	<div class="title-1"><h3 id="content_2">管理機能</h3></div>
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
