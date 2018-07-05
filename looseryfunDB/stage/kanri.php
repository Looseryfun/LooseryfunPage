<?php
	include_once 'myinclude/myfunctions.php';
	if(!isEditUser())redirectPage('index.php');
	include_once 'myinclude/pageheader.php';
?>
	<div class="title-1"><h3 id="content_2">管理ページ</h3></div>
	<ul class="list-1" id="356283_block_2">
		<li><a href="phpinfo.php">php情報</a></li>
		<li><a href="apc.php">キャッシュメモリ管理</a></li>
		<li><a href="http://looseryfun.s1006.xrea.com/log/phpmyadmin/index.php">データベース管理</a></li>
		<li><a href="https://cp.xrea.com/">サーバー管理</a></li>
		<li><a href="manageuser.php">ユーザー管理</a></li>
	</ul>
	
<?php
	include 'myinclude/pagefooter.php';
?>
