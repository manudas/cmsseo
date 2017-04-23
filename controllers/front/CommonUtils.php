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
class CombinationSeoCommonUtilsModuleFrontController extends ModuleFrontController
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

    private static function getReplacedCodeBlockReferenceString ($type, $lang_id, $id_shop, $blockreference = null, $subreferenceList = null) {
		$subtitutedBlockReferenceArr = self::replaceBlockReferenceCodeInSortedCombination ($type, $lang_id, $id_shop, $blockreference, $subreferenceList);

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

    private static function replaceBlockReferenceCodeInSortedCombination ($type, $lang_id, $id_shop, $blockreference = null, $subreferenceList = null){
		//$combinationseo_module = /* Module :: getInstanceByName ( */ $this -> module /* -> name)*/;
        $combinationseo_module = Module::getInstanceByName('combinationseo');
		
        $codeExtractFrontController = $combinationseo_module -> getModuleFrontControllerByName('CodeExtract');

		$codeSubtitutionFrontController = $combinationseo_module -> getModuleFrontControllerByName('CodeSubtitution');

        $codeCombinator = $combinationseo_module -> getModuleFrontControllerByName('CodeCombinator');

		$sortedCombination = $codeCombinator -> getSortedCombination ( $type, $lang_id, $id_shop, $blockreference, $subreferenceList );

		$combinationReferenceStructure = $codeCombinator -> getCombinationReferenceStructure ($type, $lang_id, $id_shop, $blockreference, $subreferenceList );

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
				$extractArr = $extractArr + $codeExtractFrontController -> getCodeExtractCollection ($key_block_reference, $lang_id, $id_shop, $subreferenceList);

				$sortedBlock = $sortedCombination [$key_block_reference];

				$subtitutions = $codeSubtitutionFrontController -> getSubtitutions($key_block_reference, $lang_id, $id_shop);
				$searchArr = $subtitutions['search'];
				$replaceArr = $subtitutions['replace'];

				foreach ($sortedBlock as $order => $subreferenceStructure) {
					// $subject = $extractArr[$subreferenceStructure['subreference']]; // fills with non-subtituted text
					$subject = $extractArr[$key_block_reference][$subreferenceStructure['subreference']] ['text'];
                    $sortedExtractArr [$key_block_reference] [$order] = $subject;

                    if (!empty($searchArr) && !empty($replaceArr)) {
					    $subtitutedSortedArr [$key_block_reference] [$order] = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $subject);
                    }
                    else {
                        $subtitutedSortedArr [$key_block_reference] [$order] = $sortedExtractArr [$key_block_reference] [$order];
                    }
				}
			}
		}
		
		return $subtitutedSortedArr;
	}

    public static function getPreparedMetadata($id_object, $object_type, $id_lang, $id_shop, $blockreference)
    {

        $combinationseo_module = Module :: getInstanceByName ('combinationseo');
        
        $metaDataController = $combinationseo_module -> getModuleFrontControllerByName('MetaData');

        $metadata = $metaDataController -> getMetaDataCollection ($id_object, $object_type, $id_lang, $id_shop);

        

        if (!empty($metadata)){

		    $codeSubtitutionFrontController = $combinationseo_module -> getModuleFrontControllerByName('CodeSubtitution');
		    
            $codeExtractFrontController = $combinationseo_module -> getModuleFrontControllerByName('CodeExtract');

            $meta_title = $metadata -> meta_title;
            $meta_description = $metadata -> meta_description;
            $meta_keywords = $metadata -> meta_keywords;
            $link_rewrite = $metadata -> link_rewrite;

            if (empty($id_lang)) {
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $lang_id = $language['id_lang'];
                    // pasamos lang_id pues id_lang es nulo
                    $subtitutions = $codeSubtitutionFrontController -> getSubtitutions($blockreference, $lang_id, $id_shop);
				    $searchArr = $subtitutions['search'];
				    $replaceArr = $subtitutions['replace'];

                    $metadata -> meta_title[$lang_id] = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $meta_title[$lang_id]);
                    $metadata -> meta_description[$lang_id] = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $meta_description[$lang_id]);
                    $metadata -> meta_keywords[$lang_id] = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $meta_keywords[$lang_id]);
                    $metadata -> link_rewrite[$lang_id] = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $link_rewrite[$lang_id]);

                }
            }

            else {
                $subtitutions = $codeSubtitutionFrontController -> getSubtitutions($blockreference, $id_lang, $id_shop);
				$searchArr = $subtitutions['search'];
				$replaceArr = $subtitutions['replace'];

                $metadata -> meta_title = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $meta_title);
                $metadata -> meta_description = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $meta_description);
                $metadata -> meta_keywords = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $meta_keywords);
                $metadata -> link_rewrite = $codeSubtitutionFrontController -> replaceTokenList ($searchArr, $replaceArr, $link_rewrite);
                    
            }
        }
        return $metadata;
    }


    public static function getPreparedCodeContent($id, $object_type, $id_lang, $id_shop) {

         /* Loading the module we ensure we have included the desired
         * ObjectModel classes we are going to need next
         */
        $combinationseo_module = Module :: getInstanceByName ('combinationseo');

        $frontCodeCombinatorController =  $combinationseo_module -> getModuleFrontControllerByName('CodeCombinator');
        
        $blockReference = CodeCombination::getBlockReferenceByObjectIdAndType ($id, $object_type, $id_lang, $id_shop);      

        if (!empty ($blockReference)) {

            if (!is_array($blockReference) && !empty($id_lang)) {
                $blockReference = array ($id_lang => $blockReference);
            }

            foreach ($blockReference as $lang_id => $translated_blockReference) {

                if (!empty($id_lang) && $id_lang != $lang_id) {
                    continue;
                }

                $subreferenceList = CodeCombination::getSubReferenceByObjectIdTypeAndBlockreference( $id, $object_type, $id_lang, $id_shop, $translated_blockReference );

                $combination_seo_string = self::getReplacedCodeBlockReferenceString ($object_type, $id_lang, $id_shop, $translated_blockReference, $subreferenceList);
                
                // $combination_seo_string[$translated_blockReference];

                if (!empty($id_lang)) {
                    if (!isset($result)) {
                        $result = array();
                    }
                    $result [$lang_id] = $combination_seo_string[$translated_blockReference];
                }
                else {
                    $result = $combination_seo_string[$translated_blockReference];
                    break;
                }
            }
        }
        return $result;
    }
}
