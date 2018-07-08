<?php
define('MASTER_CACHETIME', 30*60);
include_once 'dbfunctions.php';

/**
 * アイテムプロパティ
 */
class ItemProperty{
	public $maintype=0;
	public $subtype=0;
	public $percent ;
	public $proppower ;
	protected static $inputValues=array(
		'maintype',
		'subtype',
		'percent',
		'proppower',
	);
	public function __construct(){
	}
	public function setFromArray($data){
		foreach(ItemProperty::$inputValues as $key){
			$this->$key = $data[$key];
		}
	}
	public function set($maintype,$subtype,$percent,$proppower){
		$this->maintype=$maintype;
		$this->subtype=$subtype;
		$this->percent=$percent;
		$this->proppower=$proppower;
	}
	public function get(){
		$result = array();
		foreach($this as $key=>$value){
			$result[$key]=$value;
		}
		return $result;
	}
	public function save($itemID){
		$params=get();
		$params['id']=$itemID;
		return execSQL("INSERT INTO `itempropertys` (`id`, `maintype`, `subtype`, `percent`, `proppower`) VALUES (:id, :maintype, :subtype, :percent, :proppower) ",$params);
	}
	public static function getPropertyList($itemID){
		$rows = getSQLRecords("SELECT maintype,subtype,percent,proppower FROM `itempropertys` WHERE id=:id ORDER BY showorder ASC",array('id'=>$itemID));
		$result = array();
		foreach($rows as $row){
			$prop = new ItemProperty();
			foreach($rows as $key=>$value){
				$prop->$key = $value;
			}
			array_push($result, $prop);
		}
		return $result;
	}
}

/**
 * アイテムのデータ
 */
class itemData{
	public $propertyList=array();
	public $id=0;
	public $maintype=0;
	public $subtype=0;
	public $breaktype=0;
	public $breakpoint=0;
	public $status=0;
	public $name=null;
	public $gettype=0;
	public $power=0;
	public $extra=null;
	public $notrade=0;
	public $stability=0;
	public $limited=0;
	public $help=0;
	public $makedate=0;
	//public $registdate;
	protected static $inputValues=array(
		'maintype',
		'subtype',
		'breaktype',
		'breakpoint',
		'name',
		'gettype',
		'power',
		'extra',
		'notrade',
		'stability',
		'limited',
		'help',
		'makedate',
	);
	public static function getMaintypeName($maintype){
		$masterData=ItemMaster::getItemTypeList();
		if(!isset($masterData[$maintype]))return '';
		return $masterData[$maintype];
	}
	public function __construct($newMaintype, $newName){
		$this->name = $newName;
		$this->maintype=$newMaintype;
		$maintypeName = itemData::getMaintypeName($this->maintype);
		switch($maintypeName){
			case '武器':case '体防具':
				$this->notrade=1;
				break;
		}
	}
	/**
	 * 同じ内容だったらIDパクる
	 */
	public function dupID($otherObject){
		if(!$otherObject)return;
		if($this->maintype!=$otherObject->maintype)return;
		if($this->gettype!=$otherObject->gettype)return;
		if($this->name!=$otherObject->name)return;
		$this->id = $otherObject->id;
	}
	/**
	 * DBに保存
	 */
	public function save($autoActive){
		$result = false;
		beginTransaction();
		if($this->id>0){
			$result = $this->saveUpdate();
		}else{
			$result = $this->saveInsert();
		}
		if($result){commitTransaction();}
		else {rollbackTransaction();}
		return $result;
	}
	protected function saveInsert(){
		$inputValues = itemData::$inputValues;
		$colums = implode(',','`'.$inputValues.'`');
		$values = ':'.implode(',:',$inputValues);
		//$sql = 'INSERT INTO `itemrecords` (`maintype`, `subtype`, `breaktype`, `breakpoint`, `name`, `gettype`, `power`, `extra`, `notrade`, `stability`, `limited`, `help`, `makedate`)
		//	VALUES (:maintype, :subtype, :breaktype, :breakpoint, :name, :gettype, :power, :extra, :notrade, :stability, :limited, :help, :makedate)';	
		$sql = 'INSERT INTO `itemrecords` ('.$colums.') VALUES ('.$values.')';
		$params = array();
		foreach($inputValues as $key){
			$params[$key]=$this->$key;
		}
		if(!execSQL($sql,$params))return false;
		$this->id = getInsertID();
		return $this->propertyInsert();
	}
	protected function saveUpdate(){
		$inputValues = itemData::$inputValues;
		$updateValues = array();
		foreach($inputValues as $key){
			array_push($updateValues,'`'.$key.'`=:'.$key);
		}
		$sets = implode(',',$updateValues);
		$sql = "UPDATE `itemrecords` SET $sets, registdate=now() WHERE `itemrecords`.`id` = :id ";
		$params = array();
		foreach($inputValues as $key){
			$params[$key]=$this->$key;
		}
		$params['id']=$this->id;
		if(!execSQL($sql,$params))return false;
		return $this->propertyInsert();
	}
	protected function propertyInsert(){
		if($this->id<=0)return false;
		if(!execSQL('DELETE FROM `itempropertys` WHERE `itempropertys`.`id`= ?',array($this->id)))return false;
		foreach($this->propertyList as $property){
			if(!$property->save($this->id))return false;
		}
		return true;
	}
	/**
	 * IDからデータ取得
	 */
	public static function getItemById($id){
		$key = implode('_',array('item',$id));
		$result=apcu_entry($key,'itemData::generateItemById',MASTER_CACHETIME);
		return $result;
	}
	protected static function generateItemById($key){
		$nameid = explode('_',$key);
		$id = $nameid[1];
		$record = getSQLRecord("SELECT * FROM `itemrecords` WHERE id=? ",array($id));
		if($record==false)return null;
		$result = new itemData($record['maintype'],$record['name']);
		foreach(itemData::$inputValues as $key){
			if($key=='makedate'){$result->$key = date('Y/m/d', strtotime($record[$key]));}
			else {$result->$key = $record[$key];}
		}
		$proprecords = getSQLRecords("SELECT maintype,subtype,percent,proppower FROM `propertyorderview` WHERE id=? ",array($id));
		if($proprecords!=false){
			foreach($proprecords as $row){
				$property = new ItemProperty();
				$property->setFromArray($row);
				array_push($result->propertyList,$property);
			}
		}
		return $result;
	}
}

/**
 * アイテムマスタデータ
 */
class ItemMaster{
	protected static function makeImgPath($rows){
		foreach($rows as &$row){
			foreach($row as $key=>$value){
				if($key=='name'){$row['img']='img/'.urlencode($value).'.png';}
			}
		}
		return $rows;
	}
	// アイテム種別
	public static function getItemTypeList(){
		$result=apcu_entry('itemTypeMasterData','ItemMaster::generateItemTypeData',MASTER_CACHETIME);
		return $result;
	}
	protected static function generateItemTypeData($key){
		$result=array();
		$mainrows = getSQLRecords("SELECT id, name FROM `mainitemtypes` order by id asc",array());
		foreach($mainrows as $mainrow){
			$newData = array('name'=>$mainrow['name']);
			$newData['sub']=array();
			$id = $mainrow['id'];
			$subrows = ItemMaster::makeImgPath(getSQLKeyValueRecords("SELECT subtype, name FROM `subitemtype` WHERE maintype=? order by showorder asc",array($id)));

			$newData['sub'] = $subrows;
			$result[$id]=$newData;
		}
		return $result;
	}
	// プロパティ
	public static function getItemPropertyList(){
		$result=apcu_entry('itemPropertyMasterData','ItemMaster::generateItemPropertyData',MASTER_CACHETIME);
		return $result;
	}
	protected static function generateItemPropertyData($key){
		$result=array();
		$mainrows = getSQLRecords("SELECT id, name FROM `mainproptype` order by id asc",array());
		foreach($mainrows as $mainrow){
			$newData = array('name'=>$mainrow['name']);
			$newData['sub']=array();
			$id = $mainrow['id'];
			$subrows = getSQLRecords("SELECT subtype, percent, name FROM `subproptype` WHERE maintype=? order by showorder asc",array($id));
			foreach($subrows as $subrow){
				$subid = $subrow['subtype'].'_'.$subrow['percent'];
				$subdata = array();
				foreach($subrow as $key=>$value){
					$subdata[$key]=$value;
				}
				$newData['sub'][$subid] = $subdata;
			}
			$result[$id]=$newData;
		}
		return $result;
	}
	// 取得方法
	public static function getGetTypeList(){
		return apcu_entry('itemGetTypeMasterData','ItemMaster::generateGetTypeData',MASTER_CACHETIME);
	}
	protected static function generateGetTypeData($key){
		return ItemMaster::makeImgPath(getSQLKeyValueRecords("SELECT id, name FROM `itemgettype` order by id asc",array()));

	}
	// 素材タイプ
	public static function getMaterialTypeList(){
		return apcu_entry('itemMaterialTypeMasterData','ItemMaster::generateMaterialTypeData',MASTER_CACHETIME);
	}
	protected static function generateMaterialTypeData($key){
		return ItemMaster::makeImgPath(getSQLKeyValueRecords("SELECT id, name FROM `materialtype` order by id asc",array()));
	}
}
?>
