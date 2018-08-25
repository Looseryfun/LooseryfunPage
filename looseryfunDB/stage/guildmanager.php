<?php
	$html_title = 'ギルドマネージャ';
	$html_head = '<link rel="manifest" href="/manifest.json" />
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<link rel="stylesheet" type="text/css" href="guildmanager.css" />
<script type="text/javascript" src="guildmanager.js"></script>';
	include_once 'myinclude/pageheader.php';
?>
<script>
	function guidShowError(event) {
		if(guidShowError.enable)alert(event.message);
		return true;
	};
	if (!("Notification" in window)) {
        //alert("ブラウザが未対応です");
    }
	var OneSignal = window.OneSignal || [];
	OneSignal.push(function() {
		OneSignal.init({
		appId: "d419ca5c-c4ed-4053-a95a-59849c0c1ad7",
		});
	});
	//window.onerror = guidShowError;
	window.addEventListener('error', guidShowError);
</script>
	<div id="ajaxarea" class="guildmanager">
<?php
	include 'x/guildajax.php';
?>
	</div>
<?php
	include 'myinclude/pagefooter.php';
?>
