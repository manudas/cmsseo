<?php

class CodeCombination extends ObjectModel
{
	public $id;
	public $subreference;
	public $blockreference;
	public $id_cms;
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
		// 'multishop' => true,
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			'id' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
			'id_shop' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'shop' => true),
			'subreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => TRUE),
			'blockreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => TRUE),
			'id_cms' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'lang' => TRUE),
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
			`subreference` varchar(32) NOT NULL,
			`blockreference` varchar(32) NOT NULL,
			`id_cms` int(10) NOT NULL,
			`order` int(3) NOT NULL,
			PRIMARY KEY (`id`, `id_lang`), UNIQUE (`blockreference`, `subreference`, `id_cms`, `order`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$result1 = Db::getInstance()->execute($sql);
		$result2 = Db::getInstance()->execute($sq2);
		$result3 = Db::getInstance()->execute($sq3);

		$result = $result1 && $result2 && $result3;

		return $result;
	}

}

?>