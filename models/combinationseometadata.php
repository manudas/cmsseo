<?php

class CombinationSeoMetaData extends ObjectModel
{
	public $id;
	public $id_object;
	public $object_type;
	public $meta_title;
	public $meta_description;
	public $meta_keywords;
	public $link_rewrite;

	public function __construct($id = null, $id_lang = null, $id_shop = null) {
		// $this -> id_shop = $id_shop;
		Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
		parent::__construct($id, $id_lang, $id_shop);
	}

	public static $definition = array(
		'table' => 'combinationseometadata',
		'primary' => 'id',
		'multilang' => true,
        'multilang_shop' => true,
		'fields' => array(
			'id' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
			'id_shop' =>      		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'shop' => true),
			'id_object' =>      	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'lang' => TRUE ),
			'object_type' =>		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('cms', 'product', 'category'), 'required' => true, 'lang' => TRUE),
			'meta_title' =>     	array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => TRUE, 'size' => 128),
			'meta_description' =>   array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => TRUE, 'size' => 255),
			'meta_keywords' =>  	array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => TRUE, 'size' => 255),
			'link_rewrite' => 		array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'lang' => TRUE, 'size' => 128)
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


		$sq1 = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`id_shop` int(10) unsigned NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';


		$sq2 = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'_shop`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`id_shop` int(10) unsigned NOT NULL,
			PRIMARY KEY (`id`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';




		$sq3 = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'_lang`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`id_lang` int(10) NOT NULL,
			`id_shop` int(10) unsigned NOT NULL,
			`id_object` int(10) NOT NULL,
			`object_type` enum("cms", "product", "category") NOT NULL,
			`meta_title` varchar(128),
			`meta_description` varchar(255),
			`meta_keywords` varchar(255),
			`link_rewrite` varchar(128) NOT NULL,
			PRIMARY KEY (`id`, `id_lang`, `id_shop`), UNIQUE (`id_object`, `object_type`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$result = Db::getInstance()->execute($sq1) 
			&& Db::getInstance()->execute($sq2) 
			&& Db::getInstance()->execute($sq3);

		return $result;
	}

}

?>