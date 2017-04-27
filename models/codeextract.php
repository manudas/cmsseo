<?php

class CodeExtract extends ObjectModel
{
	public $id;
	public $subreference;
	public $blockreference;
	public $text;
	public $id_shop;

	public function __construct($id = null, $id_lang = null, $id_shop = null) {
		// $this -> id_shop = $id_shop;
		Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
		parent::__construct($id, $id_lang, $id_shop);
	}

	public static $definition = array(
		'table' => 'codeextracts',
		'primary' => 'id',
		//'multishop' => true,
		'multilang' => true,
        'multilang_shop' => true,
		'fields' => array(
			'id' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
			'id_shop' =>      		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'shop' => true),
			'subreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'blockreference' =>     array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'text' =>      			array('type' => self::TYPE_HTML, 'validate' => 'isString', 'required' => true, 'lang' => true)
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
			`subreference` varchar(32) NOT NULL,
			`blockreference` varchar(32) NOT NULL,
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
			`text` text NOT NULL,
			`id_shop` int(10) unsigned NOT NULL,
			PRIMARY KEY (`id`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$result = Db::getInstance()->execute($sq1) 
			&& Db::getInstance()->execute($sq2) 
			&& Db::getInstance()->execute($sq3);

		return $result;
	}

	public static function getCodeExtractCollection ($blockreference, $id_lang, $id_shop, $subreferenceList = null) {
		
		if (empty($blockreference)) {
            throw new PrestaShopException(get_called_class() .":: getCodeExtractCollection:: Can't get set width an empty blockreference");
        }


		if (empty($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }

		$codeExtractCollection = new PrestashopCollection('CodeExtract', $id_lang);


        $codeExtractCollection -> where ('blockreference', '=', $blockreference);

        $codeExtractCollection -> where ('id_shop', '=', $id_shop);

        if (!empty($subreferenceList)) {
            $codeExtractCollection -> where ('subreference', 'in', $subreferenceList);
        }

		$result = array();

		if (!empty($codeExtractCollection[0])) {
            foreach ($codeExtractCollection as $extract) {
                $result [$extract -> blockreference] [$extract -> subreference] ['text'] = $extract -> text;
                $result [$extract -> blockreference] [$extract -> subreference] ['object'] = $extract;
            }
        }

		return $result;

	}

    public static function getBlockReferencesArr() {
        $combinationCollection = new PrestashopCollection('CodeExtract');
        $result = array();
        if (count($combinationCollection) > 0 ){
            foreach ($combinationCollection as $combination) { 
                $result[] = $combination -> blockreference;
            }
        }
        return $result;
    }

    public static function getSubreferenceArrByBlockReference($blockReference) {
		
        if (empty($blockReference)) {
            throw new PrestaShopException(get_called_class() .":: getSubreferencesByBlockReference:: Can't get set width an empty blockReference");
        }

        $extractCollection = new PrestashopCollection('CodeExtract');
        
        $extractCollection -> where ('blockreference', '=', $blockReference);

        $result = array();
        
        if (count($extractCollection) > 0 ){
            foreach ($extractCollection as $extract) { 
                $result[] = $extract -> subreference;
            }
        }
        
        return $result;
    }

	public static function getBlockReferences () {

		$result = self::getBlockReferencesArr();
		
		return $result;
	}


	public static function getXML_Backup_File($blockReferences, $subreferences, $shops, $langs) {
		
		$extratCollection = new PrestashopCollection('CodeExtract');

		if (!empty($blockReferences)) {
			$extratCollection -> where ('blockreference', 'in', $blockReferences);
		}
		if (!empty($subreferences)) {
			$extratCollection -> where ('subreference', 'in', $subreferences);
		}
		if (!empty($shops)) {
			$extratCollection -> where ('id_shop', 'in', $shops);
		}

		$xml = new DOMDocument( "1.0", "utf-8" );
		$root_element = $xml -> createElement( "extractlist" );

		$xml -> appendChild( $root_element );

		if (count($extratCollection) > 0) {

			if (!empty($langs) && !is_array($langs)) {
				$langs = array($langs);
			}

			foreach ($extratCollection as $extract_object) {

				$id_shop = $extract_object -> id_shop;
				$shop = new Shop($id_shop);
				$shop_name = $shop -> name;

				$extract_node = $xml -> createElement( "extract" );
				$extract_node -> setAttribute( "blockreference", $extract_object -> blockreference );
				$extract_node -> setAttribute( "subreference", $extract_object -> subreference );
				$extract_node -> setAttribute( "shop", $shop_name );

				$root_element -> appendChild( $extract_node );

				$text = $extract_object -> text;			
				
				foreach ($text as $id_lang => $translated_text) {

					if (!empty($langs)) {
						if (!in_array($id_lang, $langs)) {
							continue;
						}
					}
					
					$language = new Language($id_lang);
					$iso_code = $language -> iso_code;

					$textNode = $xml -> createElement('text');
					$textNode -> setAttribute( "lang", $iso_code );
					$CDATA = $xml -> createCDATASection($translated_text);
					$textNode -> appendChild( $CDATA );
					$extract_node -> appendChild( $textNode );

				}
			}
		}

		$result_string = $xml -> saveXml();

		$filename = "EXTRACTS_". date('Y-m-d');

		if (empty($blockReferences) && empty($subreferences) && empty($shops) && empty($langs)) {

			$filename .= "_FULL";

		}

		$filename .= "_BACKUP.XML";

		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		die ($result_string);
	}


	public static function saveXML_Restore_File($filename) {
		$xml = new DOMDocument();
		$xml -> load($filename);
		
		$extracts_nodelist = $xml -> getElementsByTagName('extract');

		if (count($extracts_nodelist) > 0 ) {

			foreach ($extracts_nodelist as $extract_node) {
				$blockreference = 		$extract_node -> getAttribute ( 'blockreference' );
				$subreference = 		$extract_node -> getAttribute ( 'subreference' );
				$shop_name = 			$extract_node -> getAttribute ( 'shop' );

				$shop_id = 				Shop :: getIdByName($shop_name);

			    $extract_collection = 	self::getCodeExtractCollection ($blockreference, null, $shop_id, array($subreference));

				if (!empty($extract_collection[$blockreference][$subreference])) {
					$exists = true;
					$extract_objectModel = $extract_collection[$blockreference][$subreference]['object'];
				}
				else {
					$exists = true;
					$extract_objectModel = new CodeExtract();
					$extract_objectModel -> blockreference = $blockreference;
					$extract_objectModel -> subreference = $subreference;
					$extract_objectModel -> id_shop = $shop_id;
				}


				$text_nodelist = $extract_node -> getElementsByTagName('text');

				if (count($text_nodelist) > 0 ) {

					foreach ($text_nodelist as $text_element) {
						$language_iso_code = $text_element -> getAttribute ( 'lang' );

						$lang_id = Language :: getIdByIso($language_iso_code);

						if (empty($lang_id)) {
							error_log("Language $language_iso_code not found");
							continue;
						}
						else {
							$extract_objectModel -> text [$lang_id] = $text_element -> firstChild -> textContent;
						}
					}

				}

				$extract_objectModel -> save ();
			}
			
		}
	}
}

?>