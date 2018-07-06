
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
	var target = document.getElementById(subTypeObj.imgid);
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

// メイン能力タイプ変更リスナー
function onMainPropertyChaned(event)
{
	var mainTypeObj = event.target;
	var subid = mainTypeObj.getAttribute('subid');
	if(!subid)return;
	// サブセレクトリストを更新
	var subTypeData = mainTypeObj.masterData[mainTypeObj.value]['sub'];
	
	setSubPropertyTypeList(subid,subTypeData);
	return; 
}

// サブ能力タイプ変更リスナー
function onSubPropertyChaned(event)
{
	var subTypeObj = event.target;
	var percentid = subTypeObj.getAttribute('percentid');
	if(!percentid)return;
	// 画像を更新
	var target = document.getElementById(percentid);
	if(!target)return;
	var hasPercent = subTypeObj.masterData[subTypeObj.value]['percent'];
	target.style.opacity = (hasPercent)?(1):(0);
	return; 
}

// セレクトリストにアイテム能力設定
function setPropertyList(targetID, propertyMasterData)
{
	var target = document.getElementById(targetID);
	if(!target)return;
	
	target.innerHTML = "";	// option クリア
	for (key in propertyMasterData) {
		var name = propertyMasterData[key]['name'];
		let op = document.createElement("option");
		op.value = key;   //value
		op.text = name;   //テキスト
		target.appendChild(op);
	}
	target.addEventListener('change',onMainPropertyChaned);
	target.masterData = propertyMasterData;
	return; 
}

// セレクトリストにサブアイテム能力設定
function setSubPropertyTypeList(targetID, propertyMasterData)
{
	var target = document.getElementById(targetID);
	if(!target)return;
	
	target.innerHTML = "";	// option クリア
	for (key in propertyMasterData) {
		var name = propertyMasterData[key]['name']+((propertyMasterData[key]['percent'])?('%'):(''));
		let op = document.createElement("option");
		op.value = key;   //value
		op.text = name;   //テキスト
		target.appendChild(op);
	}
	target.addEventListener('change',onSubPropertyChaned);
	target.masterData = propertyMasterData;
	return; 
}

