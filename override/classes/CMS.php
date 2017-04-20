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
        
        $blockReference = CodeCombination::getBlockReferenceByObjectIdAndType ($id, 'cms', $id_lang);
        

        if (!empty ($blockReference)) {

            $this -> id = $id;
            $this -> active = "1";
            $this -> id_shop = $id_shop;

            if (!is_array($blockReference) && !empty($id_lang)) {
                $blockReference = array ($id_lang => $blockReference);
            }

            foreach ($blockReference as $lang_id => $translated_blockReference) {

                if (!empty($id_lang) && $id_lang != $lang_id) {
                    continue;
                }


                $subreferenceList = CodeCombination::getSubReferenceByObjectIdTypeAndBlockreference( $id, 'cms', $id_lang, $translated_blockReference );

                $combination_seo_string = $frontCodeCombinatorController -> getReplacedBlockString ('cms', $id_lang, $translated_blockReference, $subreferenceList);
                
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
               
        $blockReference = CodeCombination::getBlockReferenceByObjectIdAndType ($idCms, 'cms', $idLang);
        
        if (!empty ($blockReference)) {

            /* Blockreference siempre es un solo elemento pues idLang
            * siempre esta seteado, o a un idioma pasado como
            * parámetro o al idioma por defecto de la tienda
            */
            if (!is_array($blockReference) && !empty($idLang)) {
                $blockReference = array ($idLang => $blockReference);
            }
            
            /* en este caso solo entra una vez al foreach
             * pues idLang o es un idioma dado o es nulo
             * y entonces se setea al idoma por defecto
             */

            // foreach ($blockReference as $lang_id => $translated_blockReference) {
/*
                if ($idLang != $lang_id) {
                    continue;
                }
*/
                $subreferenceList = CodeCombination::getSubReferenceByObjectIdTypeAndBlockreference( $idCms, 'cms', $lang_id, $blockReference );
                
                $combination_seo_string = $frontCodeCombinatorController -> getReplacedBlockString ('cms', $lang_id, $blockReference, $subreferenceList);
                
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
           // }
        }
        else {
            return parent :: getCMSContent($idCms, $idLang, $idShop);
        }
    }


    /**
     * Checks if current object is associated to a shop.
     *
     * @since 1.7
     * @param int|null $id_shop
     * @return bool
     */
    public function isAssociatedToShop($id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = Context::getContext()->shop->id;
        }

        $cache_id = 'objectmodel_shop_'.$this->def['classname'].'_'.(int)$this->id.'-'.(int)$id_shop;
        if (!ObjectModel::$cache_objects || !Cache::isStored($cache_id)) {
            
            $combination_collection = new PrestashopCollection('CodeCombination');
            $where_str = "a1.`type` = 'cms' AND a1.`id_object` = '" . $this -> id . "' AND a0.`id_shop` = '" . $id_shop ."'";
            $combination_collection -> sqlWhere($where_str);

            if (!empty($combination_collection [0])) {
                $associated = $id_shop;
            }


            if (!ObjectModel::$cache_objects) {
                return $associated;
            }

            Cache::store($cache_id, $associated);
            return $associated;
        }

        return Cache::retrieve($cache_id);
    }
}