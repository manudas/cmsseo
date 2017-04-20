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
class CombinationSeoCodeExtractModuleFrontController extends ModuleFrontController
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

    
    public function getCodeExtractCollection ($blockreference, $id_lang, $subreferenceList = null) {
		
		if (empty($blockreference)) {
            throw new PrestaShopException($this -> name .":: getCodeExtractCollection:: Can't get set width an empty blockreference");
        }

		$codeExtractCollection = new PrestashopCollection('CodeExtract', $id_lang);
		
		$subreferenceString = "";
        if (!empty($subreferenceList)) {
            $subreferenceString = ' AND subreference IN ("'.implode('","',$subreferenceList).'")';
        }

		$whereString = 'blockreference = "' . $blockreference . '"' . $subreferenceString;

		$codeExtractCollection -> sqlWhere ($whereString);

//$codeExtractCollection -> getAll(true);

		$result = array();

		if (!empty($codeExtractCollection[0])) {
            foreach ($codeExtractCollection as $extract) {
                $result [$extract -> blockreference] [$extract -> subreference] ['text'] = $extract -> text;
                $result [$extract -> blockreference] [$extract -> subreference] ['object'] = $extract;
            }
        }

		return $result;

	}

}
