<?php
	include_once 'myinclude/myfunctions.php';
	if(!isEditUser())redirectPage('index.php');
	include_once 'myinclude/pageheader.php';
?>
	<div class="title-1"><h3 id="content_2">管理ページ</h3></div>
	<ul class="list-1" id="356283_block_2">
		<li><a href="phpinfo.php">php情報</a></li>
		<li><a href="http://looseryfun.s1006.xrea.com/log/phpmyadmin/index.php">データベース管理</a></li>
		<li><a href="manageuser">ユーザー管理</a></li>
	</ul>
	
<?php
	include 'myinclude/pagefooter.php';
?>
