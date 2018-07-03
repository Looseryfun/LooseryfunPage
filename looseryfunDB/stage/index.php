<?php
	include_once 'myinclude/pageheader.php';
?>
	<div class="title-1"><h3 id="content_2">Loosery funデータベース</h3></div>
	<div><?php if(isset($topPageMsg)){echo $topPageMsg;} ?></div>
	<?php
		echo "HELLO WORLD";
		if (isLogin()) {
			echo '<div><a href="kanri.php">管理ページへ</a></div>';
		}
	?>
	<div>
	<?php
		$loginName="ログイン";
		$href="login.php";
		if (isLogin()) {
    		$loginName="ログアウト";
			$href="logout.php";
		}
		echo "<a href=\"$href\">$loginName</a>";
	?>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
