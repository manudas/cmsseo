<?php

class codeExtractCore extends ObjectModel
{
	public $id;
	public $subreference;
	public $blockreference;
	public $text;

	public static $definition = array(
		'table' => 'codeextracts',
		'primary' => 'id',
		'multishop' => true,
		'multilang' => true,
		'fields' => array(
			'id' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
			'subreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'blockreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'text' =>      			array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true)
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
		$result = Db::getInstance()->execute($sql);
		return $result;
	}

	public static function createContentTable()
	{

		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'`(
			`id` int(10) unsigned NOT NULL auto_increment,
			`subreference` varchar(32) NOT NULL,
			`blockreference` varchar(32) NOT NULL,
			`text` text NOT NULL,
			PRIMARY KEY (`id`), UNIQUE (`reference`, `subreference`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

}

?>