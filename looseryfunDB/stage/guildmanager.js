
// 定数とか
var useLooseryfunGuildManagerScript = 1;

// 関数とか

// 内部読み込み
function guildAjaxLoad(getparam,postparam,myButton,callback)
{
	return ajaxload('ajaxarea','x/guildajax.php',getparam,postparam,myButton,callback);
}

// 内部読み込み
function guildFromAjaxLoad(myButton,getparam,callback)
{
	var form = myButton.form;
	var postparam = {};
	for( var i=0;i<form.elements.length;i++){
		var element = form.elements[i];
		element = form.elements[element.name];
		postparam[element.name] = element.value;
	}
	return guildAjaxLoad(getparam,postparam,myButton,callback);
}
