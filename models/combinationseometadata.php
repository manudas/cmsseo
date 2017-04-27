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
	public $id_shop;
	public $id_lang;

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
			'id_object' =>      	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'object_type' =>		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('cms', 'product', 'category'), 'required' => true),
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
			`id_object` int(10) NOT NULL,
			`object_type` enum("cms", "product", "category") NOT NULL,
			`id_shop` int(10) unsigned NOT NULL,
			PRIMARY KEY (`id`), UNIQUE (`id_object`, `object_type`, `id_shop`)
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

			`meta_title` varchar(128),
			`meta_description` varchar(255),
			`meta_keywords` varchar(255),
			`link_rewrite` varchar(128) NOT NULL,
			PRIMARY KEY (`id`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$result = Db::getInstance()->execute($sq1) 
			&& Db::getInstance()->execute($sq2) 
			&& Db::getInstance()->execute($sq3);

		return $result;
	}

	public static function getMetaDataObject ($id_object, $object_type, $id_lang, $id_shop)

    {		
		if (empty($id_object)) {
            throw new PrestaShopException(get_called_class() .":: getMetaDataObject:: Can't get set width an empty id_object");
        }
        if (empty($object_type)) {
            throw new PrestaShopException(get_called_class() .":: getMetaDataObject:: Can't get set width an empty object_type");
        }

		if (empty($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }

		$metaDataCollection = new PrestashopCollection('CombinationSeoMetaData', $id_lang);

        $metaDataCollection -> where ('id_object', '=', $id_object);

        $metaDataCollection -> where ('object_type', '=', $object_type);

        $metaDataCollection -> where ('id_shop', '=', $id_shop);

		$result = null;

		if (!empty($metaDataCollection[0])) { // solo debe haber un resultado (lang, shop, id y type conforman clave primaria)
            
            $result  = $metaDataCollection -> getFirst();
            
        }

		return $result;
	}

	public static function getXML_Backup_File($idList, $typeList, $shops, $langs) {
		
		$metaCollection = new PrestashopCollection('CombinationSeoMetaData');

		if (!empty($idList)) {
			$metaCollection -> where ('id_object', 'in', $idList);
		}
		if (!empty($typeList)) {
			$metaCollection -> where ('object_type', 'in', $typeList);
		}
		if (!empty($shops)) {
			$metaCollection -> where ('id_shop', 'in', $shops);
		}

		$xml = new DOMDocument( "1.0", "utf-8" );
		$root_element = $xml -> createElement( "metadatalist" );

		$xml -> appendChild( $root_element );

		if (count($metaCollection) > 0) {

			if (!empty($langs) && !is_array($langs)) {
				$langs = array($langs);
			}

			$languages = Language :: getLanguages (false);

			foreach ($metaCollection as $meta_object) {

				$id_shop = $meta_object -> id_shop;
				$shop = new Shop($id_shop);
				$shop_name = $shop -> name;

				$meta_node = $xml -> createElement( "metadata" );
				$meta_node -> setAttribute( "id_object", $meta_object -> id_object );
				$meta_node -> setAttribute( "type", $meta_object -> object_type );
				$meta_node -> setAttribute( "shop", $shop_name );

				$root_element -> appendChild( $meta_node );


				$titles = $meta_object -> meta_title;
				$descriptions = $meta_object -> meta_description;
				$keywords = $meta_object -> meta_keywords;
				$links_rewrite = $meta_object -> link_rewrite;
				
				
				foreach ($languages as $language) {
					
					$id_lang = $language['id_lang'];
					if (!empty($langs)) {
						if (!in_array($id_lang, $langs)) {
							continue;
						}
					}

					if (empty ($titles[$id_lang]) && empty ($descriptions[$id_lang]) && empty ($keywords[$id_lang]) && empty ($links_rewrite[$id_lang])) {
						continue;
					}

					$iso_code = $language['iso_code'];

					$language_node = $xml -> createElement($iso_code);
					$meta_node -> appendChild( $language_node );

					if (!empty($titles[$id_lang])){
						$metatitle_node = $xml -> createElement('metatitle');
						$CDATA = $xml -> createCDATASection($titles[$id_lang]);
						$metatitle_node -> appendChild( $CDATA );
						$language_node -> appendChild( $metatitle_node );
					}


					if (!empty($descriptions[$id_lang])){
						$description_node = $xml -> createElement('metadescription');
						$CDATA = $xml -> createCDATASection($descriptions[$id_lang]);
						$description_node -> appendChild( $CDATA );
						$language_node -> appendChild( $description_node );
					}


					if (!empty($keywords[$id_lang])){
						$keywords_node = $xml -> createElement('metakeywords');
						$CDATA = $xml -> createCDATASection($keywords[$id_lang]);
						$keywords_node -> appendChild( $CDATA );
						$language_node -> appendChild( $keywords_node );
					}


					if (!empty($links_rewrite[$id_lang])){
						$links_rewrite_node = $xml -> createElement('link-rewrite');
						$CDATA = $xml -> createCDATASection($links_rewrite[$id_lang]);
						$links_rewrite_node -> appendChild( $CDATA );
						$language_node -> appendChild( $links_rewrite_node );
					}

				}
			}
		}

		$result_string = $xml -> saveXml();

		$filename = "METADATA_". date('Y-m-d');

		if (empty($idList) && empty($typeList) && empty($shops) && empty($langs)) {

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
		
		$metadata_nodelist = $xml -> getElementsByTagName('metadata');

		if (count($metadata_nodelist) > 0 ) {

			foreach ($metadata_nodelist as $metadata_node) {


				// unique: id_object, type and id_shop
				$id_object = 		$metadata_node -> getAttribute ( 'id_object' );
				$object_type = 		$metadata_node -> getAttribute ( 'type' );
				$shop_name = 		$metadata_node -> getAttribute ( 'shop' );

				$shop_id = 				Shop :: getIdByName($shop_name);

				$meta_obj = self::getMetaDataObject($id_object, $object_type, null, $shop_id);
				if (empty($meta_obj)) {

					$meta_obj = new CombinationSeoMetaData();

					$meta_obj -> id_object = $id_object;
					$meta_obj -> object_type = $object_type;
					$meta_obj -> id_shop = $shop_id;

				}

				$language_nodes = $metadata_node -> getElementsByTagName('*'); // filters away the domtexts

				if (count($language_nodes) > 0) {
					foreach ($language_nodes as $iso_code) {
						$breakpoint = "";
						por aqui seguimos
					}
				}

				$meta_obj -> save ();

			}
			
		}
	}

}

?>