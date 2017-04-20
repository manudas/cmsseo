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
class CombinationSeoCodeSubtitutionModuleFrontController extends ModuleFrontController
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

    private function replaceToken ($search, $replace, $subject){
        if (empty($search)) {
            throw new PrestaShopException($this -> name .":: replaceToken:: Can't replace width an empty search");
        }
        if (empty($replace)) {
            throw new PrestaShopException($this -> name .":: replaceToken:: Can't replace width an empty replace");
        }
        if (empty($subject)) {
            throw new PrestaShopException($this -> name .":: replaceToken:: Can't replace width an empty subject");
        }
        $result_string = str_replace($search, $replace, $subject);
        return $result_string;
    }

    public function replaceTokenList ($search, $replace, $subject){
        if (empty($search)) {
            throw new PrestaShopException($this -> name .":: replaceTokenList:: Can't replace width an empty search");
        }
        else if (!is_array($search)){
            throw new PrestaShopException($this -> name .":: replaceTokenList:: The method needs search to be an array");
        }
        if (empty($replace)) {
            throw new PrestaShopException($this -> name .":: replaceTokenList:: Can't replace width an empty replace");
        }
        else if (!is_array($replace)){
            throw new PrestaShopException($this -> name .":: replaceTokenList:: The method needs replace to be an array");
        }
        if (empty($subject)) {
            throw new PrestaShopException($this -> name .":: replaceTokenList:: Can't replace width an empty subject");
        }
        
        if (count($search) != count($replace)) {
            throw new PrestaShopException($this -> name .":: replaceTokenList:: Inconsistent array size: search and replace have to have the same size");
        }

        for ($i = 0; $i < count($search); $i++){
            $current_search = $search[$i];
            $current_replace = $replace[$i];

            $result_string = $this -> replaceToken($current_search, $current_replace, (empty($result_string) ? $subject : $result_string));
        }

        return $result_string;
    }

	public function getSubtitutions($blockReference){
		if (empty($blockReference)) {
            throw new PrestaShopException($this -> name .":: getSubtitutionCollection:: Can't get subtitutions width an empty blockReference");
        }

		$subtitutionCollection = new PrestashopCollection('CodeSubtitution');
        $sqlWhere = 'blockreference = "' . $blockReference .'"';
        $subtitutionCollection -> sqlWhere($sqlWhere);
// $subtitutionCollection -> getAll(true);
		$searchArr = array();
        $replaceArr = array();

        if (!empty($subtitutionCollection[0])) {
            $i = 0;
            foreach ($subtitutionCollection as $subtitution) {
                $searchArr[$i] = $subtitution -> search;
                $replaceArr[$i] = $subtitution -> replace;
                $i ++;
            }
        }

		return array ('search' => $searchArr, 'replace' => $replaceArr);
	}

    public function replaceBlock ($blockReference, $subreferenceList = null/*, $search, $replace, $subject*/){
        if (empty($blockReference)) {
            throw new PrestaShopException($this -> name .":: replaceBlock:: Can't replace width an empty blockReference");
        }

        $result = null;

        // no controlamos más errores, pues estos se controlan en métodos interiores
        $subreferenceString = "";
        if (!empty($subreferenceList)) {
            $subreferenceString = ' AND subreference IN ("'.implode('","',$subreferenceList).'")';
            // $subreferenceString = ' AND subreference IN ('.implode(',',$subreferenceList).')';
        }

		$subtitutions = $this -> getSubtitutions($blockReference);

        $extractCollection = new PrestashopCollection('CodeExtract');
        $where_extract = 'blockreference = '. $blockreference . $subreferenceString;
        $extractCollection -> sqlWhere ($where_extract);

		$searchArr = $subtitutions['search'];
		$replaceArr = $subtitutions['replace'];

        $subtitutedExtractArr = array();
        if (!empty($extractCollection)) {
            foreach ($extractCollection as $extract) {
                // $blockreference = $extract -> blockreference; // no necesario, lo pasamos como parámetro
                $subreference = $extract -> subreference;
                $text = $extract -> text;
                $subtitutedExtractArr [$blockReference] [$subreference] = $this -> replaceTokenList ($searchArr, $replaceArr, $text);
            }
            $result = $subtitutedExtractArr;
        }

        return $result;
    }

}
