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
			PRIMARY KEY (`id`), 
			UNIQUE (`blockreference`, `subreference`, `id_shop`), 
			UNIQUE (`id_object`, `type`, `id_shop`)
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

	public static function getBlockReferenceByObjectIdAndType($id_object, $type/*, $id_lang*/, $id_shop) {
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

		$ps_collection = new PrestashopCollection('CodeCombination'/*, $id_lang*/);

       
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


	public static function getSubReferenceByObjectIdTypeAndBlockreference( $id_object, $type/*, $id_lang*/, $id_shop, $blockreference) {
		if (empty($id_object)) {
			error_log("CodeCombination :: getSubReferenceByObjectIdTypeAndBlockreference:: Can't get codeCombination width an empty id_object");
            return null;
        }
		if (empty($type)) {
            throw new PrestaShopException("CodeCombination :: getSubReferenceByObjectIdTypeAndBlockreference:: Can't get codeCombination width an empty type");
        }
		if (empty($blockreference)) {
            throw new PrestaShopException("CodeCombination :: getSubReferenceByObjectIdTypeAndBlockreference:: Can't get codeCombination width an empty blockreferencetype");
        }

		if (empty($id_shop)) {
			$id_shop = Context::getContext()->shop->id;
		}

		$ps_collection = new PrestashopCollection('CodeCombination'/*, $id_lang*/);

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

	/**
    * @return An array with the following structure:
    * - principal index: blockreference
    * - secondary index: order of subreference
    * - third index: subreference and ObjectModel CodeCombination
    */
    public static function getSortedCombination($type/*, $lang_id*/, $id_shop, $blockreference = null, $subreferenceList = null) {
		
        if (empty($type)) {
            throw new PrestaShopException(get_called_class() .":: getSortedCombination:: Can't get set width an empty type");
        }

        if (empty($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }
        $combinationCollection = new PrestashopCollection('CodeCombination'/*, $lang_id*/);
        
        if (!empty($blockreference)) {
            $combinationCollection -> where ('blockreference', '=', $blockreference);
        }
        $combinationCollection -> where ('type', '=', $type);
        $combinationCollection -> where ('id_shop', '=', $id_shop);

        if (!empty($subreferenceList)) {
            $combinationCollection -> where ('subreference', 'in', $subreferenceList);
        }

        $combinationCollection -> orderBy ('order', 'asc');

        $result = array();

        if (!empty($combinationCollection[0])) {
            foreach ($combinationCollection as $combination) {
                $result [$combination -> blockreference] [$combination -> order] ['subreference'] = $combination -> subreference;
                $result [$combination -> blockreference] [$combination -> order] ['object'] = $combination;
            }
        }
        
        return $result;
    }

	public static function getCombinationReferenceStructure ($type/*, $id_lang*/, $id_shop, $blockreference = null, $subreferenceList = null){
		
		if (empty($type)) {
            throw new PrestaShopException(get_called_class() .":: getCombinationReferenceStructure:: Can't get set width an empty type");
        }

        if (empty($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }

        $combinationCollection = new PrestashopCollection('CodeCombination', $id_lang);

        if (!empty($blockreference)) {
            $combinationCollection -> where ('blockreference', '=', $blockreference);
        }
        $combinationCollection -> where ('type', '=', $type);
        $combinationCollection -> where ('id_shop', '=', $id_shop);

        if (!empty($subreferenceList)) {
            $combinationCollection -> where ('subreference', 'in', $subreferenceList);
        }

        $combinationCollection -> orderBy ('order', 'asc');


        $result = array();

        if (!empty($combinationCollection[0])) {
            foreach ($combinationCollection as $combination) {
                $result [$combination -> blockreference] [$combination -> subreference] = $combination;
            }
        }
        
        return $result;
	}

	private static function sortCollectionByBlockReference($combinationCollection) {
		$result = array();
		if  (count ($combinationCollection) > 0){
			foreach ($combinationCollection as $combination) {
				$result[$combination -> blockreference][] = $combination;
			}
		}
		return $result;
	}

	public static function getXML_Backup_File($blockReferences, $subreferences, $types, $ids, $shops/*, $langs*/) {
		
		$combinationCollection = new PrestashopCollection('CodeCombination');

		if (!empty($blockReferences)) {
			$combinationCollection -> where ('blockreference', 'in', $blockReferences);
		}
		if (!empty($subreferences)) {
			$combinationCollection -> where ('subreference', 'in', $subreferences);
		}
		if (!empty($types)) {
			$combinationCollection -> where ('type', 'in', $types);
		}
		if (!empty($ids)) {
			$combinationCollection -> where ('id_object', 'in', $ids);
		}
		if (!empty($shops)) {
			$combinationCollection -> where ('id_shop', 'in', $shops);
		}

		$sortedCombinationCollection = self::sortCollectionByBlockReference($combinationCollection);

		// $extractXML = new SimpleXMLElement("<extractlist></extractlist>");
		$xml = new DOMDocument( "1.0", "utf-8" );
		$root_element = $xml -> createElement( "combinationlist" );

		$xml -> appendChild( $root_element );

		if (count($sortedCombinationCollection) > 0) {

			foreach ($sortedCombinationCollection as $current_block_reference => $combinationList) {

				$combination_node = $xml -> createElement( "combination" );
				$combination_node -> setAttribute( "blockreference", $current_block_reference );

				$root_element -> appendChild ($combination_node);

				foreach ( $combinationList as $combination ) {

					$id_shop = $combination -> id_shop;
					$shop = new Shop($id_shop);
					$shop_name = $shop -> name;
					
					$subreference_node = $xml -> createElement( "subreference" );
					$subreference_node -> setAttribute( "name", $combination -> subreference );

					$subreference_node -> setAttribute( "id_object", $combination -> id_object );
					$subreference_node -> setAttribute( "type", $combination -> type );
					$subreference_node -> setAttribute( "shop", $shop_name );

					$combination_node -> appendChild( $subreference_node );

				}

			}
		}

		$result_string = $xml -> saveXml();

		$filename = "COMBINATIONS_". date('Y-m-d');

		if (empty($blockReferences) && empty($subreferences) 
					&& empty($types) && empty($ids) && empty($shops)) {

			$filename .= "_FULL";

		}

		$filename .= "_BACKUP.XML";

		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		die ($result_string);
	}

	public static function combinationExists($blockreference, $subreference, $id_object, $type, $shop, $order) {
		// deberíamos añadir caché aqui (y en muchos otros sitios, no parece optima tanta consulta a DB)
		
		$combination_collection = new PrestashopCollection('CodeCombination');

		if (!empty($blockreference)) {
			$combination_collection -> where ('blockreference' , '=', $blockreference);
		}
		if (!empty($subreference)) {
			$combination_collection -> where ('subreference' , '=', $subreference);
		}
		if (!empty($id_object)) {
			$combination_collection -> where ('id_object' , '=', $id_object);
		}
		if (!empty($blockreference)) {
			$combination_collection -> where ('type' , '=', $type);
		}
		if (!empty($blockreference)) {
			$combination_collection -> where ('id_shop' , '=', $shop);
		}
		if (!empty($order)) {
			$combination_collection -> where ('order' , '=', $order);
		}

		if (count($combination_collection) > 0) {
			return true;
		}
		else {
			return false;
		}


	}
	
	public static function saveXML_Restore_File($filename) {

		$xml = new DOMDocument();
		$xml -> load($filename);
		
		$combinations_nodelist = $xml -> getElementsByTagName('combination');

		if (count($combinations_nodelist) > 0 ) {

			foreach ($combinations_nodelist as $combination_node) {
				$blockreference = 		$combination_node -> getAttribute ( 'blockreference' );

				$subreference_nodelist = $combination_node -> getElementsByTagName( 'subreference' );

				if (count($combinations_nodelist) > 0 ) {
					foreach ($subreference_nodelist as $subreference_node) {

						$subreference = 		$subreference_node -> getElementsByTagName( 'name' );
						$id_object = 			$subreference_node -> getElementsByTagName( 'id_object' );
						$type = 				$subreference_node -> getElementsByTagName( 'type' );
						$shop_name = 			$subreference_node -> getElementsByTagName( 'shop' );

						$shop_id = 				Shop :: getIdByName($shop_name);

						if (!combinationExists($blockreference, $subreference, $id_object, $type, $shop_name, $order)){

							$combination = new CodeCombination();

							$combination -> blockreference = $blockreference;
							$combination -> subreference = $subreference;
							$combination -> id_object = $id_object;
							$combination -> type = $type;
							$combination -> id_shop = $shop_id;

							$combination -> save();

						}
					}
				}
			}		
		}
	}
}

?>