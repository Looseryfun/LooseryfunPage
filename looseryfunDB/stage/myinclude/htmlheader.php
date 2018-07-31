<!DOCTYPE html>
<?php
	include_once 'myfunctions.php';
	if(strpos($_SERVER["REQUEST_URI"],'myinclude')!==false){
		exit();
	}
?>
<html lang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<title>Loosery funnデータベースサービス</title>
	<link href="https://image02.seesaawiki.jp/l/o/looseryfun-game-info/4ec40051ee89d618b1b5.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
	<link href="https://image02.seesaawiki.jp/l/o/looseryfun-game-info/4ec40051ee89d618b1b5.png" rel="apple-touch-icon">
	<link rel="stylesheet" type="text/css" href="https://static.seesaawiki.jp/css/usr/common.css" />
	<link rel="stylesheet" type="text/css" href="https://image02.seesaawiki.jp/l/o/looseryfun-game-info/site.css" />
	<link rel="stylesheet" type="text/css" href="https://image02.seesaawiki.jp/l/o/looseryfun-game-info/overwrite.css" />
	<link rel="stylesheet" type="text/css" href="looseryfundbstyle.css" />
	<script type="text/javascript" src="https://static.seesaawiki.jp/js/jquery/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="https://static.seesaawiki.jp/js/usr/second/wikier.js"></script>
	<script type="text/javascript" src="looseryfundb.js"></script>
	<meta name="description" content="Loosery funデータベースサービス" />
<style type="text/css">  
<!-- 
	div#container
	{
		position: absolute;
		top: 100px;
	} 
-->  
</style>
</head>