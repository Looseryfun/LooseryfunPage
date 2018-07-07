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
	public function __construct(){
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
}

/**
 * アイテムマスタデータ
 */
class ItemMaster{
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
			$subrows = getSQLRecords("SELECT subtype, name FROM `subitemtype` WHERE maintype=? order by showorder asc",array($id));
			foreach($subrows as $subrow){
				$subid = $subrow['subtype'];
				$subdata = array();
				foreach($subrow as $key=>$value){
					$subdata[$key]=$value;
					if($key=='name'){$subdata['img']='img/'.urlencode($value).'.png';}
				}
				$newData['sub'][$subid] = $subdata;
			}
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
		$result=apcu_entry('itemGetTypeMasterData','ItemMaster::generateGetTypeData',MASTER_CACHETIME);
		return $result;
	}
	protected static function generateGetTypeData($key){
		return getSQLKeyValueRecords("SELECT id, name FROM `itemgettype` order by id asc",array());
	}
	// 素材タイプ
	public static function getMaterialTypeList(){
		$result=apcu_entry('itemMaterialTypeMasterData','ItemMaster::generateMaterialTypeData',MASTER_CACHETIME);
		return $result;
	}
	protected static function generateMaterialTypeData($key){
		return getSQLKeyValueRecords("SELECT id, name FROM `materialtype` order by id asc",array());
	}
}
?>
