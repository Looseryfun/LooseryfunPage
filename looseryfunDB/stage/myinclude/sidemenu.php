
<div class="sidecolumn" id="sub">
<div class="column-inner">

<!-- menu_DB -->

<div class="free-box">
<div class="box-inner">
<div class="title"><h2>Database Menu</h2></div>
<div class="body">
<div class="inner">
<div class="user-area">
<div class="wiki-section-1"><div class="title-1"><h3>検索メニュー</h3></div>
<div class="wiki-section-body-1">
<ul class="list-1">
<li><a href="skilleditor.php">スキルエディター</a></li>
</ul>
<div id="356283_block_3">
<div class="toggle-title"><a class="toggle-link-open" id="region_plugin_DB_1" onclick="toggleRegion(this,'DB_block_1-inside');"><img width="17" height="17" alt="" src="http://static.seesaawiki.jp/formatter-storage/images/common/spacer.gif"></a><p>一覧</p></div>
<div class="toggle-display" id="DB_block_1-inside" style="display:none">

<ul class="list-2">
<li><a href="armorimage.php">体防具画像一覧</a></li>
<li><a href="equipimage.php?name=%E7%89%87%E6%89%8B%E5%89%A3">片手剣画像一覧</a></li>
<li><a href="equipimage.php?name=%E4%B8%A1%E6%89%8B%E5%89%A3">両手剣画像一覧</a></li>
<li><a href="equipimage.php?name=%E6%89%8B%E7%94%B2">手甲画像一覧</a></li>
<li><a href="equipimage.php?name=%E5%BC%93">弓画像一覧</a></li>
<li><a href="equipimage.php?name=%E8%87%AA%E5%8B%95%E5%BC%93">自動弓画像一覧</a></li>
<li><a href="equipimage.php?name=%E6%9D%96">杖画像一覧</a></li>
<li><a href="equipimage.php?name=%E9%AD%94%E5%B0%8E%E5%85%B7">魔導具画像一覧</a></li>
<li><a href="equipimage.php?name=%E6%97%8B%E9%A2%A8%E6%A7%8D">旋風槍画像一覧</a></li>
<li><a href="equipimage.php?name=%E6%8A%9C%E5%88%80%E5%89%A3">抜刀剣画像一覧</a></li>
<li><a href="equipimage.php?name=%E7%9B%BE">盾画像一覧</a></li>
<li><a href="equipimage.php?name=%E8%BF%BD%E5%8A%A0%E8%A3%85%E5%82%99">追加装備画像一覧</a></li>
</ul>
<!--
<ul class="list-1" id="356283_block_4">
<li><a href="http://looseryfun.game-info.wiki/d/%b6%e2%ba%f6%be%f0%ca%f3">金策情報</a></li>
<li><a href="http://looseryfun.game-info.wiki/d/%c9%d4%cd%d1%c9%ca%be%f0%ca%f3">不用品情報</a></li>
<li><a href="http://looseryfun.game-info.wiki/d/%c1%c7%ba%e0%bc%fd%bd%b8%a5%ac%a5%a4%a5%c9">素材収集ガイド</a></li>
<li><a href="http://looseryfun.game-info.wiki/d/%a5%ec%a5%d9%a5%eb%be%e5%a4%b2">レベル上げ</a></li>
</ul>
-->
</div><!-- toggle-display -->
</div><!-- 356283_block_3 -->
</div><!-- wiki-section-body-1 -->
</div><!-- wiki-section-1 -->
</div><!-- /userarea -->
</div><!-- /inner -->
</div><!-- /.body -->
</div><!-- /.box-inner -->
</div><!-- /free-box -->

<!-- /menu_DB -->

<?php
	// 本家サイトメニュー
	include_once 'myinclude/myfunctions.php';
	echo getHtmlData('http://looseryfun.game-info.wiki/','//div[@id="sub"]/div/*');
?>

</div><!-- /.column-inner -->
</div>
