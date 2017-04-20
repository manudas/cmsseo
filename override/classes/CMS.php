<?php
/**
 * 2017 Manuel José Pulgar Anguita
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Manuel José Pulgar Anguita <cibermanu@hotmail.com>
 * @copyright 2017 Manuel José Pulgar Anguita
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class CMSCore
 */
class CMS extends CMSCore
{



    public function __construct($id = null, $id_lang = null, $id_shop = null){
        parent::__construct($id, $id_lang , $id_shop);

        /* Loading the module we ensure we have included the desired
         * ObjectModel classes we are going to need next
         */
        $combinationseo_module = Module :: getInstanceByName ('combinationseo');

        $frontCodeCombinatorController =  $combinationseo_module -> getModuleFrontControllerByName('CodeCombinator');
        
        $blockReference = CodeCombination::getBlockReferenceByObjectIdAndType ($id, 'cms');
        
        if (!empty ($blockReference)) {

            foreach ($blockReference as $lang_id => $translated_blockReference) {

                if (!empty($id_lang) && $id_lang != $lang_id) {
                    continue;
                }

                $combination_seo_string = $frontCodeCombinatorController -> getReplacedBlockString ('cms', $lang_id, $translated_blockReference);
                
                $partial_result = array ('content' => $combination_seo_string[$translated_blockReference]);

                $COMBINATIONSEO_CONCATENATE_RESULT = Configuration::get('COMBINATIONSEO_CONCATENATE_RESULT');
            
            

                if ($COMBINATIONSEO_CONCATENATE_RESULT == 'true') {

                    // $cms_result = parent :: getCMSContent($idCms, $idLang, $idShop);
                    if (!empty($id_lang)) {
                        $cms_result = $this -> content;
                    }
                    else {
                        $cms_result = $this -> content['$lang_id'];
                    }
                    $final_result = array ('content' => $partial_result['content'] . $cms_result['content']);
                
                }
                else {
                    $final_result = $partial_result;
                }
                
                if (!empty($id_lang)) {
                    $this -> content = $final_result['content'];
                }
                else {
                    $this -> content[$lang_id] = $final_result['content'];
                }
            }
        }
    }



    /**
     * @param int      $idCms
     * @param int|null $idLang
     * @param int|null $idShop
     *
     * @return array|bool|null|object
     */
    public static function getCMSContent($idCms, $idLang = null, $idShop = null)
    {

        if (is_null($idLang)) {
            $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        }
        if (is_null($idShop)) {
            $idShop = (int) Configuration::get('PS_SHOP_DEFAULT');
        }

        /* Loading the module we ensure we have included the desired
         * ObjectModel classes we are going to need next
         */
        $combinationseo_module = Module :: getInstanceByName ('combinationseo');

        $frontCodeCombinatorController =  $combinationseo_module -> getModuleFrontControllerByName('CodeCombinator');
        
        $blockReference = CodeCombination::getBlockReferenceByObjectIdAndType ($idCms, 'cms');
        
        if (!empty ($blockReference)) {
            
            foreach ($blockReference as $lang_id => $translated_blockReference) {
                $combination_seo_string = $frontCodeCombinatorController -> getReplacedBlockString ('cms', $blockReference);
                
                $partial_result = array ('content' => $combination_seo_string);

                $COMBINATIONSEO_CONCATENATE_RESULT = Configuration::get('COMBINATIONSEO_CONCATENATE_RESULT');

                if ($COMBINATIONSEO_CONCATENATE_RESULT == 'true') {

                    $cms_result = parent :: getCMSContent($idCms, $idLang, $idShop);

                    $final_result = array ('content' => $partial_result['content'] . $cms_result['content']);
                }
                else {
                    $final_result = $partial_result;
                }

                return $final_result;
            }
        }
        else {
            return parent :: getCMSContent($idCms, $idLang, $idShop);
        }
    }
}