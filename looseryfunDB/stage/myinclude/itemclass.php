<?php
define('MASTER_CACHETIME', 30*60);
include_once 'dbfunctions.php';

/**
 * アイテムプロパティ
 */
class ItemProperty{
	public $id;
	public $maintype;
	public $subtype ;
	public $percent ;
	public $proppower ;
	public function set($itemid, $maintype,$subtype,$percent,$proppower){
		$this->id=$itemid;
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
	public function save($itemID=null){
		$params=get();
		if(isset($itemID)){$params['ID']=$itemID;}
		return execSQL("INSERT INTO `itempropertys` (`id`, `maintype`, `subtype`, `percent`, `proppower`) VALUES (:id, :maintype, :subtype, :percent, :proppower) ",$params);
	}
	public static function getPropertyList($itemID){
		$rows = getSQLRecords("SELECT * FROM `itempropertys` WHERE id=:id",array('id'=>$itemID));
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
	public $id;
	public $maintype;
	public $subtype;
	public $status;
	public $name;
	public $gettype;
	public $power;
	public $stability;
	public $limited;
	public $help;
	//public $registdate;
}

/**
 * アイテムマスタデータ
 */
class ItemMaster{
	public static function getItemTypeList(){
		$result=apcu_entry('itemTypeMasterData','ItemMaster::generateData',MASTER_CACHETIME);
		return var_export($result,true);
	}
	protected static function generateData($key){
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
				}
				$newData['sub'][$subid] = $subdata;
			}
			$result[$id]=$newData;
		}
		return $result;
	}
}
?>
