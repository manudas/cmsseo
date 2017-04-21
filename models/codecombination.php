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
/*
	public static $_COMBINATION_TYPE_OPTIONS = array(
													array(
														'id_option' => 'cms',            // The value of the 'value' attribute of the <option> tag.
														'name' => 'cms'              // The value of the text content of the  <option> tag.
													),
													array(
														'id_option' => 'product',
														'name' => 'product'
													),
													array(
														'id_option' => 'category',
														'name' => 'category'
													)
												);
*/

	public function __construct($id = null, $id_lang = null, $id_shop = null) {
		$this -> id_shop = $id_shop;
		Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
		parent::__construct($id, $id_lang, $id_shop);
	}

	public static $definition = array(
		'table' => 'codecombinations',
		'primary' => 'id',
		// 'multishop' => true,
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			'id' =>      				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
			'id_shop' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'shop' => true),
			'subreference' =>      		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => TRUE),
			'blockreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => TRUE),
			'id_object' =>      		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'lang' => TRUE),
			'type' =>					array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('cms', 'product', 'category'), 'required' => true, 'lang' => TRUE),
			'order' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'lang' => TRUE)
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
		$sq3 = 'DROP TABLE
			`'._DB_PREFIX_.self::$definition['table'].'_lang`
		';
		$result = Db::getInstance()->execute($sql) && Db::getInstance()->execute($sq2) && Db::getInstance()->execute($sq3);
		return $result;
	}

	public static function createContentTable()
	{

		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`id_shop` int(10) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$sq2 = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'_shop`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`id_shop` int(10) NOT NULL,
			PRIMARY KEY (`id`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$sq3 = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'_lang`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`id_lang` int(10) NOT NULL,
			`id_shop` int(10) NOT NULL,
			`subreference` varchar(32) NOT NULL,
			`blockreference` varchar(32) NOT NULL,
			`id_object` int(10) NOT NULL,
			`type` enum("cms", "product", "category") NOT NULL,
			`order` int(3) NOT NULL,
			PRIMARY KEY (`id`, `id_lang`, `id_shop`), 
			UNIQUE (`blockreference`, `subreference`, `id_object`, `type`, `id_lang`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		// error_log($sq3);
		
		$result1 = Db::getInstance()->execute($sql);
		$result2 = Db::getInstance()->execute($sq2);
		$result3 = Db::getInstance()->execute($sq3);

		$result = $result1 && $result2 && $result3;

		return $result;
	}

	public static function getBlockReferenceByObjectIdAndType($id_object, $type, $id_lang) {
		if (empty($id_object)) {
			error_log("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty id_object");
            return null;
			// throw new PrestaShopException("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty id_object");
        }
		if (empty($type)) {
            throw new PrestaShopException("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty type");
        }
		if (empty($id_lang)) {
            throw new PrestaShopException("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty id_lang");
        }

		$ps_collection = new PrestashopCollection('CodeCombination', $id_lang);

		// $whereString = 'id_object = ' . $id_object . ' AND type = ' . $type ;
		$whereString = 'id_object = "' . $id_object . '" AND type = "' . $type . '"' ;

		$ps_collection -> sqlWhere ($whereString);

		if (!empty($ps_collection[0])){
			return $ps_collection[0] -> blockreference;
		}
		else {
			return null;
		}
	}

	public static function getSubReferenceByObjectIdTypeAndBlockreference( $id_object, $type, $id_lang, $blockreference ) {
		if (empty($id_object)) {
			error_log("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty id_object");
            return null;
			// throw new PrestaShopException("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty id_object");
        }
		if (empty($type)) {
            throw new PrestaShopException("CodeCombination :: getBlockReferenceByObjectIdAndType:: Can't get codeCombination width an empty type");
        }
		if (empty($blockreference)) {
            throw new PrestaShopException("CodeCombination :: getSubReferenceByObjectIdTypeAndBlockreference:: Can't get codeCombination width an empty blockreferencetype");
        }
		if (empty($id_lang)) {
            throw new PrestaShopException("CodeCombination :: getSubReferenceByObjectIdTypeAndBlockreference:: Can't get codeCombination width an empty id_lang");
        }

		$ps_collection = new PrestashopCollection('CodeCombination', $id_lang);

		// $whereString = 'id_object = ' . $id_object . ' AND type = ' . $type ;
		$whereString = 'id_object = "' . $id_object . '" AND type = "' . $type . '" AND blockreference = "' . $blockreference .  '"' ;

		$ps_collection -> sqlWhere ($whereString);

		$result = array();
		
		foreach ($ps_collection as $combination) {
			$result[] = $combination -> subreference;
		}

		return $result;

	}


	public function save($null_values = false, $auto_date = true) {
		$languages = Language::getLanguages(false);
		$default_language = Configuration::get('PS_LANG_DEFAULT');
		
		$default_language_type = $this -> type [$default_language];
		
		if ((empty($default_language_type)) || (!in_array($default_language_type, self::$definition['fields']['type']['values']))) {
			return false;
		}
		else {
			foreach ($languages as $language) {
				if (empty($this -> type[$language['id_lang']])) {
					$this -> type[$language['id_lang']] = $default_language_type;
				}
			}
		}
		
		return parent::save();
	}

	public function update($null_values = false) {
		$languages = Language::getLanguages(false);
		$default_language = Configuration::get('PS_LANG_DEFAULT');
		
		$default_language_type = $this -> type [$default_language];
		
		if ((empty($default_language_type)) || (in_array($default_language_type, self::$definition['fields']['type']['values']))) {
			return false;
		}
		else {
			foreach ($languages as $language) {
				if (empty($this -> type[$language['id_lang']])) {
					$this -> type[$language['id_lang']] = $default_language_type;
				}
			}
		}
		
		return parent::update();
	}

}

?>