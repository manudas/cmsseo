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
    private function getSortedCombination($type, $lang_id, $blockreference = null, $subreferenceList = null) {
        if (empty($type)) {
            throw new PrestaShopException($this -> name .":: getSortedCombination:: Can't get set width an empty type");
        }
        $subreferenceString = "";
        if (!empty($subreferenceList)) {
            $subreferenceString = ' AND subreference IN ("'.implode('","',$subreferenceList).'")';
            // $subreferenceString = ' AND subreference IN ('.implode(',',$subreferenceList).')';
        }
        $blockreferenceString = "";
        if (!empty($blockreference)) {
            $blockreferenceString = ' AND blockreference = "' . $blockreference . '"';
        }
        $typeString = " type = '$type' ";

        $combinationCollection = new PrestashopCollection('CodeCombination', $lang_id);
        
        $whereString = $typeString . $blockreferenceString . $subreferenceString;

        $combinationCollection -> sqlOrderBy ('`order` ASC');

        $combinationCollection -> sqlWhere ($whereString);

// $combinationCollection -> getAll(true);

        $result = array();

        if (!empty($combinationCollection[0])) {
            foreach ($combinationCollection as $combination) {
                $result [$combination -> blockreference] [$combination -> order] ['subreference'] = $combination -> subreference;
                $result [$combination -> blockreference] [$combination -> order] ['object'] = $combination;
            }
        }
        
        return $result;
    }

	private function getCombinationReferenceStructure ($type, $id_lang, $blockreference = null, $subreferenceList = null){
		if (empty($type)) {
            throw new PrestaShopException($this -> name .":: getCombinationReferenceStructure:: Can't get set width an empty type");
        }

        $subreferenceString = "";
        if (!empty($subreferenceList)) {
            $subreferenceString = ' AND subreference IN ("'.implode('","',$subreferenceList).'")';
            // $subreferenceString = ' AND subreference IN ('.implode(',',$subreferenceList).')';
        }
        $blockreferenceString = "";
        if (!empty($blockreference)) {
            $blockreferenceString = ' AND blockreference = "' . $blockreference . '"';
        }
        $typeString = " type = '$type' ";

        $combinationCollection = new PrestashopCollection('CodeCombination', $id_lang);

        $whereString = $typeString . $blockreferenceString . $subreferenceString;

        $combinationCollection -> sqlOrderBy ('`order` ASC');

        $combinationCollection -> sqlWhere ($whereString);

        $result = array();

        if (!empty($combinationCollection)) {
            foreach ($combinationCollection as $combination) {
                $result [$combination -> blockreference] [$combination -> subreference] = $combination;
            }
        }
        
        return $result;
	}

	private function replaceBlockReferenceInSortedCombination ($type, $lang_id, $blockreference = null, $subreferenceList = null){
		$combinationseo_module = /* Module :: getInstanceByName ( */ $this -> module /* -> name)*/;

		$codeExtractAdminController = $combinationseo_module -> getModuleFrontControllerByName('CodeExtract');

		$codeSubtitutionAdminController = $combinationseo_module -> getModuleFrontControllerByName('CodeSubtitution');

		$sortedCombination = $this -> getSortedCombination ( $type, $lang_id, $blockreference, $subreferenceList );

		$combinationReferenceStructure = $this -> getCombinationReferenceStructure ($type, $lang_id, $blockreference, $subreferenceList );

		if (!empty($sortedCombination)){

			$extractArr = array();			

			$sortedExtractArr = array();

			$subtitutedSortedArr = array();

			foreach ($combinationReferenceStructure as $key_block_reference => $combinationReference) {

				/* Normalmente el bloque del primer foreach solo se va a ejecutar una vez."
				 * Sin embargo se hace el foreach para producir fácilmente futuras
				 * ampliaciones.
				 */
                if (empty($subreferenceList)) {
				    $subreferenceList = array_keys ($combinationReference);	
                }		
				$extractArr = $extractArr + $codeExtractAdminController -> getCodeExtractCollection ($key_block_reference, $lang_id, $subreferenceList);

				$sortedBlock = $sortedCombination [$key_block_reference];

				$subtitutions = $codeSubtitutionAdminController -> getSubtitutions($key_block_reference);
				$searchArr = $subtitutions['search'];
				$replaceArr = $subtitutions['replace'];

				foreach ($sortedBlock as $order => $subreferenceStructure) {
					// $subject = $extractArr[$subreferenceStructure['subreference']]; // fills with non-subtituted text
					$subject = $extractArr[$key_block_reference][$subreferenceStructure['subreference']] ['text'];
                    $sortedExtractArr [$key_block_reference] [$order] = $subject;

                    if (!empty($searchArr) && !empty($replaceArr)) {
					    $subtitutedSortedArr [$key_block_reference] [$order] = $codeSubtitutionAdminController -> replaceTokenList ($searchArr, $replaceArr, $subject);
                    }
                    else {
                        $subtitutedSortedArr [$key_block_reference] [$order] = $sortedExtractArr [$key_block_reference] [$order];
                    }
				}
			}
		}
		
		return $subtitutedSortedArr;
	}

	public function getReplacedBlockString ($type, $lang_id, $blockreference = null, $subreferenceList = null) {
		$subtitutedBlockReferenceArr = $this -> replaceBlockReferenceInSortedCombination ($type, $lang_id, $blockreference, $subreferenceList);

		if (!empty($subtitutedBlockReferenceArr)) {
			
			$result = array ();

			firstforeachloop:			
			foreach ($subtitutedBlockReferenceArr as $block_reference_key => $sortedSubtitutedList) {

				/* Usually we only enter once in this loop (the fist foreach loop) */

				$current_string = "";
				innerforeachloop:
				foreach ($sortedSubtitutedList as $order => $subtitutedText){ 
					$current_string .= $subtitutedText;
				}

				$result[$block_reference_key] = $current_string;

			}
		}
		
		return $result;
	}

}
