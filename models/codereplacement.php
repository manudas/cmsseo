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
			'search' =>   		   	array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
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
			`search` varchar(128) NOT NULL,
			PRIMARY KEY (`id`), UNIQUE (`blockreference`, `search`, `id_shop`),
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

    private static function sortReplacementsByBlockReference($replacementsCollection) {
        $result = array();
		if  (count ($replacementsCollection) > 0){
			foreach ($replacementsCollection as $replacement) {
				$result[$replacement -> blockreference][] = $replacement;
			}
		}
		return $result;
    }


    public static function getXML_Backup_File($blockReferences, $shops, $langs) {
		
		$replacementsCollection = new PrestashopCollection('CodeReplacement');

		if (!empty($blockReferences)) {
			$replacementsCollection -> where ('blockreference', 'in', $blockReferences);
		}

		if (!empty($shops)) {
			$replacementsCollection -> where ('id_shop', 'in', $shops);
		}

		$xml = new DOMDocument( "1.0", "utf-8" );
		$root_element = $xml -> createElement( "replacementlist" );

		$xml -> appendChild( $root_element );

		if (count($replacementsCollection) > 0) {

            if (!empty($langs) && !is_array($langs)) {
				$langs = array($langs);
			}

            $languages = Language :: getLanguages(false);

            $sortedObjectsByBlockReference = CodeReplacement::sortReplacementsByBlockReference($replacementsCollection);

            foreach ($sortedObjectsByBlockReference as $blockreference => $replacementList) {
                $reference_node = $xml -> createElement( "reference" );
                $reference_node -> setAttribute( "blockreference", $blockreference );

                $root_element -> appendChild( $reference_node );

                foreach ( $replacementList as $replacement ) {
                   

					if (empty($replacement -> search)) {
						// no ponemos 
						continue;
					}

                    $id_shop = $replacement -> id_shop;
                    $shop = new Shop($id_shop);
                    $shop_name = $shop -> name;
                    
                    $replacement_element = $xml -> createElement( "replacement" );
                    $replacement_element -> setAttribute( "shop", $shop_name );

                    $reference_node -> appendChild ($replacement_element);

                    $search = $xml -> createElement( "search" );
                    $CDATA = $xml -> createCDATASection($replacement -> search);
                    $search -> appendChild($CDATA);

					$replacement_element -> appendChild($search);

                    foreach ($languages as $language) {
                        $iso_code = $language['iso_code'];
                        $id_lang = $language['id_lang'];

                        if ( empty($replacement -> replace[$id_lang]) ) {
                            continue;
                        }
                        if (!empty($langs) && !in_array($id_lang, $lang)) {
                            continue;
                        }

                        $language_node = $xml -> createElement( $language ['iso_code'] );
                        $replacement_element -> appendChild ($language_node);

                        $replace = $xml -> createElement( "replace" );
                        $CDATA = $xml -> createCDATASection($replacement -> replace[$id_lang]);
                        $replace -> appendChild($CDATA);

                        $language_node -> appendChild($replace);
                    }

                }
            }
        }

		$result_string = $xml -> saveXml();

		$filename = "REPLACEMENTS_". date('Y-m-d');

		if (empty($blockReferences) && empty($shops) && empty($langs)) {

			$filename .= "_FULL";

		}

		$filename .= "_BACKUP.XML";

		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		die ($result_string);
	}

	public static function getReplacementObjectModel($blockReference, $search_string, $id_shop) {
		if (empty($blockReference)) {
            throw new PrestaShopException(get_called_class() .":: getReplacementObjectModel:: Can't get Replacement object width an empty blockReference");
        }
		if (empty($search_string)) {
            throw new PrestaShopException(get_called_class() .":: getReplacementObjectModel:: Can't get Replacement object width an empty search_string");
        }
		if (empty($id_shop)) {
            throw new PrestaShopException(get_called_class() .":: getReplacementObjectModel:: Can't get Replacement object width an empty id_shop");
        }

		$replacement_collection = new PrestashopCollection('CodeReplacement') ;

		$replacement_collection -> where ('blockreference', 'in', $blockReference);
		$replacement_collection -> where ('search', 'in', $search_string);
		$replacement_collection -> where ('id_shop', 'in', $id_shop);

		if (count ($replacement_collection) > 0){
			// solo podemos tener uno pues la tripleta blockreference, search e id_shop es clave unica (UNIQUE)
			return $replacement_collection -> getFrist();
		}
		else {
			return null;
		}


	}

    public static function saveXML_Restore_File($filename) {
		$xml = new DOMDocument();
		$xml -> load($filename);
		
		$reference_nodelist = $xml -> getElementsByTagName('reference');

		if (count($reference_nodelist) > 0 ) {

			foreach ($reference_nodelist as $reference_node) {


				$blockreference = 			$reference_node -> getAttribute ( 'blockreference' );

				$replacementNodeList = 		$reference_node -> getElementsByTagName('replacement');

				if (count ($replacementNodeList) > 0) {
					foreach ($replacementNodeList as $replacement) {
						$shop_name = $replacement -> getAttribute ( 'shop' );

						$shop_id = 				Shop :: getIdByName($shop_name);

						$search_node = $replacement -> getElementsByTagName('search');

						$search_text = $search_node -> firstChild -> textContent;

						$replacement_obj = self::getReplacementObjectModel($blockreference, $search_text, $shop_id);

						if ($replacement_obj == null) {

							$replacement_obj = new CodeReplacement();

							$replacement_ob -> id_shop = $shop_id;
							$replacement_ob -> search = $search_text;
							$replacement_ob -> blockreference = $blockreference;

						}

						$childnodes = $replacement -> childNodes;

						if (count($childnodes) > 0) {
							foreach ($childnodes as $node) {

								if (($node -> nodeType == XML_TEXT_NODE) || ($node -> tagName == 'search')){
									// no nos interesan los text nodes (intros, espacios, etc...) y el nodo search ya lo hemos procesado antes
									continue;
								}
								else {
									
									$lang_iso_code = $node -> tagName;

									// es un language node
									$lang_id = Language :: getIdByIso($lang_iso_code);
									$replace_node = $node -> getElementsByTagName('replace');
									$replace_text = $replace_node -> firstChild -> textContent;

									$replacement_ob -> replace[$lang_id] = $replace_text;

								}
							}
						}
						$replacement_ob -> save();
					}
				}

			}
			
		}
	}

}

?>