﻿
// 定数とか
var useLooseryfunDBScript = 1;

// 関数とか

// セレクトリストにデータ設定
function iconHElpText(imgIconObject)
{
	var target = imgIconObject.parentNode.parentNode;
	$hasnohelp = target.className.indexOf('nohelp')>=0;
	if($hasnohelp)target.className = target.className.replace('nohelp','showhelp');
	else target.className = target.className.replace('showhelp','nohelp');
}

// セレクトリストにデータ設定
function setSelectboxData(targetID, masterData)
{
	var target = document.getElementById(targetID);
	if(!target)return null;
	
	target.innerHTML = "";	// option クリア
	for (key in masterData) {
		var name = masterData[key]['name'];
		let op = document.createElement("option");
		op.value = key;   //value
		op.text = name;   //テキスト
		target.appendChild(op);
	}
	target.masterData = masterData;
	return target; 
}

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
	var imgid = subTypeObj.getAttribute('imgid');
	if(!imgid)return;
	// 画像を更新
	var target = document.getElementById(imgid);
	if(!target)return;
	var imgpath = subTypeObj.masterData[subTypeObj.value]['img'];
	target.src = imgpath;
	return; 
}

// セレクトリストにメインアイテムタイプ設定
function setItemTypeList(targetID, itemMasterData)
{
	var target = setSelectboxData(targetID,itemMasterData);
	if(!target)return;
	
	target.addEventListener('change',onMainItemTypeChaned);
	return; 
}

// セレクトリストにサブアイテムタイプ設定
function setSubItemTypeList(targetID, itemMasterData)
{
	var target = setSelectboxData(targetID,itemMasterData);
	if(!target)return;
	
	target.addEventListener('change',onSubTypeChaned);
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
	var target = setSelectboxData(targetID,propertyMasterData);
	if(!target)return;
	
	target.addEventListener('change',onMainPropertyChaned);
	return; 
}

// セレクトリストにサブアイテム能力設定
function setSubPropertyTypeList(targetID, propertyMasterData)
{
	var target = document.getElementById(targetID);
	if(!target)return null;
	
	target.innerHTML = "";	// option クリア
	for (key in propertyMasterData) {
		var name = propertyMasterData[key]['name']+((propertyMasterData[key]['percent'])?('%'):(''));
		let op = document.createElement("option");
		op.value = key;   //value
		op.text = name;   //テキスト
		target.appendChild(op);
	}
	target.masterData = propertyMasterData;
	target.addEventListener('change',onSubPropertyChaned);
	return; 
}


// チェックボックスで有効無効切り替え
function onEnableChaned(event)
{
	var checkBox = event.target;
	var targetID = checkBox.getAttribute('target');
	if(!targetID)return;
	var target = document.getElementById(targetID);
	if(!target)return;
	
	target.disabled = !checkBox.checked;
	return; 
}

// チェックボックスで有効無効切り替え
function setEnableCheckbox(targetID)
{
	var target = document.getElementById(targetID);
	if(!target)return;
	
	target.addEventListener('change',onEnableChaned);
	return; 
}
function triggerEvent(target, eventName) {
	if (document.createEvent) {
		var event = document.createEvent("HTMLEvents");
		event.initEvent(eventName, true, true ); // event type, bubbling, cancelable
		return target.dispatchEvent(event);
	} else {
		// IE
		var event = document.createEventObject();
		return element.fireEvent("on"+eventName, event)
	}
 }
// フォームにデータを設定
function setFormData(formName, data)
{
	var targetList = document.getElementsByName(formName);
	var listSize = targetList.length;
	for(var i=0;i<listSize;i++){
		target = targetList.item(i);
		target.value = data;
		//target.trigger('change');
		triggerEvent(target,'change');
	}
}

// プロパティリストをフォームに設定
function setPropertyListforForm(propertyList)
{
	if(!propertyList)return;
	for(key in propertyList){
		property = propertyList[key];
		setFormData('prop_maintype'+key,property['maintype']);
		setFormData('prop_subtype'+key,property['subtype']+'_'+property['prop_percent']);
		//prop_percent //パーセントはサブタイプに吸収
		setFormData('prop_power'+key,property['proppower']);
	}
}

// アイテムデータをフォームに設定
function setItemDataforForm(itemData)
{
	if(!itemData)return;
	for(key in itemData){
		if(key=='propertyList')setPropertyListforForm(itemData[key]);
		else setFormData(key,itemData[key]);
	}
}

//ボタンで表示変更
function onChangeVisible(event)
{
	var source = event.target;
	var targetID = source.getAttribute('target');
	if(!targetID)return;
	var target = document.getElementById(targetID);
	if(!target)return;
	
	target.className = source.value;
	return; 
}