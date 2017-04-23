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
class CombinationSeoCodeCombinatorModuleFrontController extends ModuleFrontController
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
    /**
    * @return An array with the following structure:
    * - principal index: blockreference
    * - secondary index: order of subreference
    * - third index: subreference and ObjectModel CodeCombination
    */
    public function getSortedCombination($type, $lang_id, $id_shop, $blockreference = null, $subreferenceList = null) {
        if (empty($type)) {
            throw new PrestaShopException($this -> name .":: getSortedCombination:: Can't get set width an empty type");
        }

        if (empty($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }
        $combinationCollection = new PrestashopCollection('CodeCombination', $lang_id);
        
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

	public function getCombinationReferenceStructure ($type, $id_lang, $id_shop, $blockreference = null, $subreferenceList = null){
		if (empty($type)) {
            throw new PrestaShopException($this -> name .":: getCombinationReferenceStructure:: Can't get set width an empty type");
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

    
}
