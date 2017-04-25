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
    /*
    * module: combinationseo
    * date: 2017-04-21 18:37:02
    * version: 0.2
    */
    public function __construct($id = null, $id_lang = null, $id_shop = null){
        parent::__construct($id, $id_lang , $id_shop);


        $is_subbmit_updatecms = Tools::isSubmit('updatecms');
        
        if ($is_subbmit_updatecms == true) {
            return;
        } 

        /* Loading the module we ensure we have included the desired
         * ObjectModel classes we are going to need next
         */
        $combinationseo_module = Module :: getInstanceByName ('combinationseo');

        // this makes the controller to be included and loaded
        $commonUtilsFronController = $combinationseo_module -> getModuleFrontControllerByName('CommonUtils'); 

        $code_LANG = CombinationSeoCommonUtilsModuleFrontController::getPreparedCodeContent($id, 'cms', $id_lang, $id_shop);
        
        $COMBINATIONSEO_CONCATENATE_RESULT = Configuration::get('COMBINATIONSEO_CONCATENATE_RESULT');   
        if (!empty($code_LANG) && !is_array($code_LANG) && !empty($id_lang)) {
            $code_LANG = array ($id_lang => $code_LANG);
        }
        if (!empty($code_LANG)) {
        
            $this -> id = $id;
            $this -> active = "1";
            $this -> id_shop = $id_shop;
        
            foreach ($code_LANG as $lang_id => $code) {
                if (!empty($id_lang) && $id_lang != $lang_id) {
                    continue;
                }
                $blockreference = CodeCombination::getBlockReferenceByObjectIdAndType ($id, 'cms', $lang_id, $id_shop);
                $meta_LANG = CombinationSeoCommonUtilsModuleFrontController::getPreparedMetadata($id, 'cms', $lang_id, $id_shop, $blockreference);
             
                $content = $code_LANG [$lang_id];

                if (empty($id_lang)) { // multilang
                   
                    if ($COMBINATIONSEO_CONCATENATE_RESULT == 'true') {
                        $this -> content[$lang_id] = $content . $this -> content[$lang_id];
                                    
                        $this -> meta_title[$lang_id] = $meta_LANG -> meta_title[$lang_id] . $this -> meta_title[$lang_id];
                        $this -> meta_description[$lang_id] = $meta_LANG -> meta_description[$lang_id] . $this -> meta_description[$lang_id];
                        $this -> meta_keywords[$lang_id] = $meta_LANG -> meta_keywords[$lang_id] . $this -> meta_keywords[$lang_id];
                        $this -> link_rewrite[$lang_id] = $meta_LANG -> link_rewrite[$lang_id] . $this -> link_rewrite[$lang_id];
                    }
                    else {
                        $this -> content[$lang_id] = $content;
                        $this -> meta_title[$lang_id] = $meta_LANG -> meta_title[$lang_id];
                        $this -> meta_description[$lang_id] = $meta_LANG -> meta_description[$lang_id];
                        $this -> meta_keywords[$lang_id] = $meta_LANG -> meta_keywords[$lang_id];
                        $this -> link_rewrite[$lang_id] = $meta_LANG -> link_rewrite[$lang_id];
                        
                    }
                }
                else {
                    if ($COMBINATIONSEO_CONCATENATE_RESULT == 'true') {
                        $this -> content = $content . $this -> content;
                        $this -> meta_title = $meta_LANG -> meta_title . $this -> meta_title;
                        $this -> meta_description = $meta_LANG -> meta_description . $this -> meta_description;
                        $this -> meta_keywords = $meta_LANG -> meta_keywords . $this -> meta_keywords;
                        $this -> link_rewrite = $meta_LANG -> link_rewrite . $this -> link_rewrite;
                    }
                    else {
                        $this -> content = $content;
                        $this -> meta_title = $meta_LANG -> meta_title;
                        $this -> meta_description = $meta_LANG -> meta_description;
                        $this -> meta_keywords = $meta_LANG -> meta_keywords;
                        $this -> link_rewrite = $meta_LANG -> link_rewrite;
                    }
                }
            
            
                if (!empty($id_lang)) { // SOLO PROCESAMOS UNA LENGUA QUE ACABAMOS DE PROCESAR
                    break;
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
    /*
    * module: combinationseo
    * date: 2017-04-21 18:37:02
    * version: 0.2
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
        $code_LANG = CombinationSeoCommonUtilsModuleFrontController::getPreparedCodeContent($idCms, 'cms', $idLang, $idShop);
        
        if (!empty ($code_LANG)) {
            /* code_LANG siempre es un solo elemento pues idLang
            * siempre esta seteado, o a un idioma pasado como
            * parámetro o al idioma por defecto de la tienda
            */
            $blockreference = CodeCombination::getBlockReferenceByObjectIdAndType ($idCms, 'cms', $idLang, $idShop);
            $meta_LANG = CombinationSeoCommonUtilsModuleFrontController::getPreparedMetadata($idCms, 'cms', $idLang, $idShop, $blockreference);
            $COMBINATIONSEO_CONCATENATE_RESULT = Configuration::get('COMBINATIONSEO_CONCATENATE_RESULT');
            if ($COMBINATIONSEO_CONCATENATE_RESULT == 'true') {
                $cms_result = parent :: getCMSContent($idCms, $idLang, $idShop);
                $final_result = array ('content' => $code_LANG . $cms_result['content']);
            }
            else {
                $final_result = array ('content' => $code_LANG);
            }
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
    /*
    * module: combinationseo
    * date: 2017-04-21 18:37:02
    * version: 0.2
    */
    public function isAssociatedToShop($id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = Context::getContext()->shop->id;
        }
        $cache_id = 'objectmodel_shop_'.$this->def['classname'].'_'.(int)$this->id.'-'.(int)$id_shop;
        if (!ObjectModel::$cache_objects || !Cache::isStored($cache_id)) {

            $blockReference = CodeCombination::getBlockReferenceByObjectIdAndType ($this -> id, 'cms', null, $id_shop);      

            if (!empty($blockReference)) {
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