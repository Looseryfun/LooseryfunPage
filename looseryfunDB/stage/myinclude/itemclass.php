<?php
define('MASTER_CACHETIME', 30*60);
include_once 'dbfunctions.php';

/**
 * アイテムプロパティ
 */
class ItemProperty{
	public $id=0;
	public $maintype=0;
	public $subtype=0;
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
		'status',
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
	public function __construct($newMaintype, $newName){
		$this->name = $newName;
		$this->maintype=$newMaintype;
		$masterData=ItemMaster::getItemTypeList();
		$maintypeName = $masterData[$this->maintype];
		switch($maintypeName){
			case '武器':case '体防具':
				$this->notrade=0;
				break;
		}
	}
	public function save(){
		if($this->id>0){
			//TBD
		}else{
			saveInsert();
		}
	}
	protected function saveInsert(){
		$sql = 'INSERT INTO `itemrecords` (`maintype`, `subtype`, `breaktype`, `breakpoint`, `status`, `name`, `gettype`, `power`, `extra`, `notrade`, `stability`, `limited`, `help`, `makedate`)
			VALUES (:maintype, :subtype, :breaktype, :breakpoint, :status, :name, :gettype, :power, :extra, :notrade, :stability, :limited, :help, :makedate)';
		$params = array();
		foreach($this->inputValues as $key){
			$params[$key]=$this->$key;
		}
		execSQL($sql,$params);
		$this->id = getInsertID();

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
