<?php

class CodeReplacement extends ObjectModel
{
	public $id;
	// public $subreference;
	public $blockreference;
	public $search;
	public $replace;
	public $id_shop;

	public static $definition = array(
		'table' => 'codeReplacements',
		'primary' => 'id',
		// 'multishop' => true,
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			'id' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
			'id_shop' =>      		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'shop' => true),
			'search' =>   		   	array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'lang' => TRUE),
			'replace' =>    	  	array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'lang' => TRUE),
			'blockreference' =>     array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
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
			`blockreference` varchar(32) NOT NULL,
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
			`search` varchar(128) NOT NULL,
			`replace` varchar(128) NOT NULL,
			PRIMARY KEY (`id`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$result1 = Db::getInstance()->execute($sql);
		$result2 = Db::getInstance()->execute($sq2);
		$result3 = Db::getInstance()->execute($sq3);

		$result = $result1 && $result2 && $result3;

		return $result;
	}

	public static function replaceToken ($search, $replace, $subject){
		
        if (empty($search)) {
            throw new PrestaShopException(get_called_class() .":: replaceToken:: Can't replace width an empty search");
        }
        if (empty($replace)) {
            throw new PrestaShopException(get_called_class() .":: replaceToken:: Can't replace width an empty replace");
        }
        if (empty($subject)) {
            throw new PrestaShopException(get_called_class() .":: replaceToken:: Can't replace width an empty subject");
        }
        $result_string = str_replace($search, $replace, $subject);
        return $result_string;
    }

    public static function replaceTokenList ($search, $replace, $subject){
		
        if (empty($search)) {
            throw new PrestaShopException(get_called_class() .":: replaceTokenList:: Can't replace width an empty search");
        }
        else if (!is_array($search)){
            throw new PrestaShopException(get_called_class() .":: replaceTokenList:: The method needs search to be an array");
        }
        if (empty($replace)) {
            throw new PrestaShopException(get_called_class() .":: replaceTokenList:: Can't replace width an empty replace");
        }
        else if (!is_array($replace)){
            throw new PrestaShopException(get_called_class() .":: replaceTokenList:: The method needs replace to be an array");
        }
        if (empty($subject)) {
            throw new PrestaShopException(get_called_class() .":: replaceTokenList:: Can't replace width an empty subject");
        }
        
        if (count($search) != count($replace)) {
            throw new PrestaShopException(get_called_class() .":: replaceTokenList:: Inconsistent array size: search and replace have to have the same size");
        }

        for ($i = 0; $i < count($search); $i++){
            $current_search = $search[$i];
            $current_replace = $replace[$i];

            $result_string = self::replaceToken($current_search, $current_replace, (empty($result_string) ? $subject : $result_string));
        }

        return $result_string;
    }

	public static function getReplacements($blockReference, $id_lang, $id_shop){
		
		if (empty($blockReference)) {
            throw new PrestaShopException(get_called_class() .":: getReplacementCollection:: Can't get Replacements width an empty blockReference");
        }

        if (empty($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }
		$ReplacementCollection = new PrestashopCollection('CodeReplacement', $id_lang);

        $ReplacementCollection -> where ('blockreference', '=', $blockReference);

        $ReplacementCollection -> where ('id_shop', '=', $id_shop);

		$searchArr = array();
        $replaceArr = array();

        if (!empty($ReplacementCollection[0])) {
            $i = 0;
            foreach ($ReplacementCollection as $Replacement) {
                $searchArr[$i] = $Replacement -> search;
                $replaceArr[$i] = $Replacement -> replace;
                $i ++;
            }
        }

		return array ('search' => $searchArr, 'replace' => $replaceArr);
	}

    public static function replaceBlock ($id_lang, $id_shop, $blockReference, $subreferenceList = null/*, $search, $replace, $subject*/){
		
        if (empty($blockReference)) {
            throw new PrestaShopException(get_called_class() .":: replaceBlock:: Can't replace width an empty blockReference");
        }

        $result = null;

        // no controlamos más errores, pues estos se controlan en métodos interiores

        if (empty($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }

		$Replacements = self::getReplacements($blockReference, $id_lang, $id_shop);


        $extractCollection = new PrestashopCollection('CodeExtract', $id_lang);


        $extractCollection -> where ('blockreference', '=', $blockReference);

        $extractCollection -> where ('id_shop', '=', $id_shop);

        if (!empty($subreferenceList)) {
            $extractCollection -> where ('subreference', 'in', $subreferenceList);
        }

		$searchArr = $Replacements['search'];
		$replaceArr = $Replacements['replace'];

        $subtitutedExtractArr = array();
        if (!empty($extractCollection)) {
            foreach ($extractCollection as $extract) {
                // $blockreference = $extract -> blockreference; // no necesario, lo pasamos como parámetro
                $subreference = $extract -> subreference;
                $text = $extract -> text;
                $subtitutedExtractArr [$blockReference] [$subreference] = self::replaceTokenList ($searchArr, $replaceArr, $text);
            }
            $result = $subtitutedExtractArr;
        }

        return $result;
    }

}

?>