
// 定数とか
var useLooseryfunDBScript = 1;

// 関数とか

// メインアイテムタイプ変更リスナー
function onMainItemTypeChaned(event)
{
	var mainTypeObj = event.target;
	if(!mainTypeObj.subid)return;
	// サブセレクトリストを更新
	var subTypeName = mainTypeObj.subid;
	var subTypeData = mainTypeObj.masterData[mainTypeObj.value]['sub'];
	
	setSubItemTypeList(subTypeName,subTypeData);
	return; 
}

// サブアイテムタイプ変更リスナー
function onSubTypeChaned(event)
{
	var subTypeObj = event.target;
	if(!subTypeObj.imgid)return;
	// 画像を更新
	var target = document.getElementById(targetID);
	if(!target)return;
	var imgpath = target.masterData[target.value]['img'];
	return; 
}

// セレクトリストにメインアイテムタイプ設定
function setItemTypeList(targetID, itemMasterData)
{
	var target = document.getElementById(targetID);
	if(!target)return;
	
	target.innerHTML = "";	// option クリア
	for (key in itemMasterData) {
		var name = itemMasterData[key]['name'];
		let op = document.createElement("option");
		op.value = key;   //value
		op.text = name;   //テキスト
		target.appendChild(op);
	}
	target.addEventListener('change',onMainItemTypeChaned);
	target.masterData = itemMasterData;
	return; 
}

// セレクトリストにサブアイテムタイプ設定
function setSubItemTypeList(targetID, itemMasterData)
{
	var target = document.getElementById(targetID);
	if(!target)return;
	
	target.innerHTML = "";	// option クリア
	for (key in itemMasterData) {
		var name = itemMasterData[key]['name'];
		let op = document.createElement("option");
		op.value = key;   //value
		op.text = name;   //テキスト
		target.appendChild(op);
	}
	target.addEventListener('change',onSubTypeChaned);
	target.masterData = itemMasterData;
	return; 
}

