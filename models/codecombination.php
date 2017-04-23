<?php

class CodeCombination extends ObjectModel
{
	public $id;
	public $subreference;
	public $blockreference;
	public $id_object;
	public $type;
	public $order;
	public $id_shop;


	public function __construct($id = null, $id_lang = null, $id_shop = null) {
		$this -> id_shop = $id_shop;
		Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
		parent::__construct($id, $id_lang, $id_shop);
	}

	public static $definition = array(
		'table' => 'codecombinations',
		'primary' => 'id',
		'multishop' => true,
		//'multilang' => true,
		//'multilang_shop' => true,
		'fields' => array(
			'id' =>      				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
			'id_shop' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'shop' => true),
			'subreference' =>      		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'blockreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'id_object' =>      		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'type' =>					array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('cms', 'product', 'category'), 'required' => true),
			'order' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true)
		),
	);

	public static function createTables()
	{
		//main table for the files
		return (self::createContentTable());
	}

	public static function dropTables()
	{

		$sql = 'DROP TABLE
			`'._DB_PREFIX_.self::$definition['table'].'`
		';
		$sq2 = 'DROP TABLE
			`'._DB_PREFIX_.self::$definition['table'].'_shop`
		';

		$result = Db::getInstance()->execute($sql) && Db::getInstance()->execute($sq2) /*&& Db::getInstance()->execute($sq3)*/;
		return $result;
	}

	public static function createContentTable()
	{


		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`subreference` varchar(32) NOT NULL,
			`blockreference` varchar(32) NOT NULL,
			`id_object` int(10) NOT NULL,
			`type` enum("cms", "product", "category") NOT NULL,
			`order` int(3) NOT NULL,
			`id_shop` int(10) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$sq2 = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'_shop`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`id_shop` int(10) NOT NULL,
			PRIMARY KEY (`id`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
	
		$result1 = Db::getInstance()->execute($sql);
		$result2 = Db::getInstance()->execute($sq2);

		$result = $result1 && $result2;

		return $result;
	}

	public static function getBlockReferenceByObjectIdAndType($id_object, $type, $id_lang, $id_shop) {
		if (empty($id_object)) {
			error_log("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty id_object");
            return null;
        }
		if (empty($type)) {
            throw new PrestaShopException("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty type");
        }

		if (empty($id_shop)) {
			$id_shop = Context::getContext()->shop->id;
		}

		$ps_collection = new PrestashopCollection('CodeCombination', $id_lang);

       
        $ps_collection -> where ('type', '=', $type);
        
        $ps_collection -> where ('id_object', '=', $id_object);
        $ps_collection -> where ('id_shop', '=', $id_shop);

		if (!empty($ps_collection[0])){
			return $ps_collection[0] -> blockreference;
		}
		else {
			return null;
		}
	}

	public static function getSubReferenceByObjectIdTypeAndBlockreference( $id_object, $type, $id_lang, $id_shop, $blockreference) {
		if (empty($id_object)) {
			error_log("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty id_object");
            return null;
        }
		if (empty($type)) {
            throw new PrestaShopException("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty type");
        }
		if (empty($blockreference)) {
            throw new PrestaShopException("CodeCombination :: getSubReferenceByObjectIdTypeAndBlockreference:: Can't get codeCombination width an empty blockreferencetype");
        }

		if (empty($id_shop)) {
			$id_shop = Context::getContext()->shop->id;
		}

		$ps_collection = new PrestashopCollection('CodeCombination', $id_lang);

        $ps_collection -> where ('type', '=', $type);
        
        $ps_collection -> where ('id_object', '=', $id_object);
        $ps_collection -> where ('id_shop', '=', $id_shop);
		
		$ps_collection -> where ('blockreference', '=', $blockreference);

		$result = array();
		
		foreach ($ps_collection as $combination) {
			$result[] = $combination -> subreference;
		}

		return $result;
	}
}

?>