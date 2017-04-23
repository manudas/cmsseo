<?php
/**
* 2017 Manuel José Pulgar Anguita
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
*  @author    Manuel José Pulgar Anguita <cibermanu@hotmail.com>
*  @copyright 2017 Manuel José Pulgar Anguita
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/**
 * @since 1.7.0
 */
class CombinationSeoMetaDataModuleFrontController extends ModuleFrontController
{


    public function __construct()
    {
        $this->module = Module::getInstanceByName('combinationseo');
        if (!$this->module->active) {
            Tools::redirect('index');
        }

        $this->page_name = 'module-'.$this->module->name.'-'.Dispatcher::getInstance()->getController();

        FrontController::__construct();

        $this->controller_type = 'modulefront';
    }

    
    public function getMetaDataCollection ($id_object, $object_type, $id_lang, $id_shop)

    {		
		if (empty($id_object)) {
            throw new PrestaShopException($this -> name .":: getMetaDataCollection:: Can't get set width an empty id_object");
        }
        if (empty($object_type)) {
            throw new PrestaShopException($this -> name .":: getMetaDataCollection:: Can't get set width an empty object_type");
        }

		if (empty($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }
        /*
        // if (!empty($id_shop)) {
			$shop_where = '" AND id_shop = "' . $id_shop . '"';
		//}
		//else {
		//	$shop_where = '';
		//}
*/
		$metaDataCollection = new PrestashopCollection('CombinationSeoMetaData', $id_lang);

        $metaDataCollection -> where ('id_object', '=', $id_object);

        $metaDataCollection -> where ('object_type', '=', $object_type);

        $metaDataCollection -> where ('id_shop', '=', $id_shop);

/*
		$whereString = 'id_object = "' . $id_object . '" AND object_type = "' . $object_type .'"'. $shop_where;

		$metaDataCollection -> sqlWhere ($whereString);
*/
//$codeExtractCollection -> getAll(true);

		$result = null;

		if (!empty($metaDataCollection[0])) { // solo debe haber un resultado (lang, shop, id y type conforman clave primaria)
            // foreach ($metaDataCollection as $metadata) {
            $result  = $metaDataCollection -> getFirst();
            // }
        }

		return $result;

	}

}
