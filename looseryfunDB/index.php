<?php
	include 'myinclude/pageheader.php';
?>
		<div class="title-1"><h3 id="content_2">Loosery funデータベース</h3></div>
		<?php
			echo "HELLO WORLD";
		?>
		<div>
		<?php
			$loginName="ログイン";
			$href="login.php";
			if (isLogin()) {
    			$loginName="ログアウト";
			}
			echo "<a href=\"$href\">$loginName</a>";
        ?>
        </div>
<?php
	include 'myinclude/pagefooter.php';
?>
