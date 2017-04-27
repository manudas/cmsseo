<?php
/*
* 2017 Manuel José Pulgar Anguita
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
*  @author Manuel José Pulgar Anguita <cibermanu@hotmail.com>
*  @copyright  2017 Manuel José Pulgar Anguita

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/


if (!defined( '_PS_VERSION_' ))
	exit;

class combinationseo extends Module
{
    private static $_controllerCache = array();

    public function __construct()
    {
        $this -> name = 'combinationseo';

        require_once (_PS_MODULE_DIR_.$this->name.'/models/codeextract.php');
        require_once (_PS_MODULE_DIR_.$this->name.'/models/codecombination.php');
        require_once (_PS_MODULE_DIR_.$this->name.'/models/codereplacement.php');
        require_once (_PS_MODULE_DIR_.$this->name.'/models/combinationseometadata.php');

        $this->author = 'Manuel José Pulgar Anguita';
        $this->version = '0.4';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Combination SEO Module', array(), 'Modules.combinationseo.Admin');
        $this->description = $this->trans('Generate your pages in an automated and easy way, so you can improve your SEO faster.', array(), 'Modules.combinationseo.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        // $this->templateFile = 'module:combinationseo/views/templates/hook/combinationseo.tpl';
    }

    public function install()
    {
        return $this -> installDB()
            && $this -> installTabs()
            && $this -> installConfiguration()
            && parent::install()
        ;
    }

    private function deleteConfiguration() {

        Configuration::deleteByName( 'COMBINATIONSEO_CONCATENATE_RESULT' );
        Configuration::deleteByName( 'COMBINATIONSEO_DROP_DATABASE' );
        return true;
    }

    private function installConfiguration() {

        Configuration::updateValue( 'COMBINATIONSEO_CONCATENATE_RESULT' , 'false' );
        Configuration::updateValue( 'COMBINATIONSEO_DROP_DATABASE' , 'false' );
        return true;
    }

    private function installTabs() {
        
        $sectionTab = $this -> createSection($this -> name);

        $desplegableTab = $this -> installTab('AdminCodeCombinator', $this->trans('Combination SEO Module', array(), 'Modules.combinationseo.Admin') , $this -> name);
        
        $desplegableTab_id = (int) Tab::getIdFromClassName('AdminCodeCombinator');
        
        $tab = $this -> installTab('AdminCodeCombinator', $this->trans('Combinaciones de código', array(), 'Modules.combinationseo.Admin'), false, $desplegableTab_id);
        $tab2 = $this -> installTab('AdminCodeExtract', $this->trans('Extractos de código', array(), 'Modules.combinationseo.Admin'), false, $desplegableTab_id);
        $tab3 = $this -> installTab('AdminCodeReplacement', $this->trans('Sustituciones en código', array(), 'Modules.combinationseo.Admin'), false, $desplegableTab_id);
        $tab4 = $this -> installTab('AdminMetaData', $this->trans('Meta Data for objects', array(), 'Modules.combinationseo.Admin'), false, $desplegableTab_id);
        $tab5 = $this -> installTab('AdminCombinationSeoBackup', $this->trans('Import or export data for SEO', array(), 'Modules.combinationseo.Admin'), false, $desplegableTab_id);
        
        return $sectionTab && $desplegableTab && $tab && $tab2 && $tab3 && $tab4 && $tab5;
    
    }

    private function createSection($tab_section_name) {
        $tab = new Tab();
        $tab -> active = 1;
        $tab -> class_name = $this -> name; // solo sirve para hacer de "padre"
        $tab -> module = $this -> name;
        $tab -> name = array();
        if (is_array($tab_section_name)) {
            foreach (Language::getLanguages(true) as $lang) {
                $tab -> name[$lang['id_lang']] = $tab_section_name[$lang['id_lang']];
            }
        }
        else {
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = $tab_section_name;
            }
        }
        
        $tab -> id_parent = 0;
        
       
        $ok = $tab->add();
        if ($ok == true) {
            return $tab -> id;
        }
        else return null;
    }

    private function installTab($className, $tabName, $tabParentName = false, $parentID = -1)
    {
        $tab = new Tab();
        $tab -> active = 1;
        $tab -> class_name = $className;
        $tab -> name = array();
        if (is_array($tabName)) {
            foreach (Language::getLanguages(true) as $lang) {
                $tab -> name[$lang['id_lang']] = $tabName[$lang['id_lang']];
            }
        }
        else {
            foreach (Language::getLanguages(true) as $lang) {
                $tab -> name[$lang['id_lang']] = $tabName;
            }
        }
        if ($parentID > -1) {
            $tab -> id_parent = $parentID;
        }
        else if ($tabParentName) {
            $tab -> id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab -> id_parent = (int) Tab::getIdFromClassName('default') ;
        }
        $tab -> module = $this -> name;
        return $tab->add();
    }

    public function installDB()
    {
        $return1 = CodeExtract::createTables();
        $return2 = CodeCombination::createTables();
        $return3 = CodeReplacement::createTables();
        $return4 = CombinationSeoMetaData::createTables();

        $return = $return1 && $return2 && $return3 && $return4;

        return $return;
    }

    private function uninstallTabs() {
        $result = true;
        $tab_list = Tab::getCollectionFromModule($this -> name);
        if (!empty($tab_list)) {
            foreach ($tab_list as $tab) {
                $result &= $tab -> delete();
            }
        }
        return $result;
    }


    public function uninstall()
    {
        // return parent::uninstall();
        return $this -> uninstallTabs() 
            && $this -> uninstallDB() 
            && $this -> deleteConfiguration() // afecta a uninstallDB, lo ejecutamos después
            && parent::uninstall();
    }

    public function uninstallDB()
    {
        $dropDB = Configuration::get( 'COMBINATIONSEO_DROP_DATABASE' );
        if ($dropDB == 'true') {
            $return1 = CodeExtract::dropTables();
            $return2 = CodeCombination::dropTables();
            $return3 = CodeReplacement::dropTables();
            $return4 = CombinationSeoMetaData::dropTables();

            $return = $return1 && $return2 && $return3 && $return4;
        }
        else {
            $return = true;
        }
        return $return;
    }


    public function getContent()
    {
        $html = '';

        if (Tools::isSubmit('submitAdd'.$this -> name)) {
            $drop_tables_on_delete = Tools::getValue('deletedb');
            $drop_tables_on_delete_config_value = ($drop_tables_on_delete == '1' ? 'true':'false');
            
            $concatenate_module_content = Tools::getValue('concatenate');
            $concatenate_module_content_config_value = ($concatenate_module_content == '1' ? 'true':'false');
            
            
            Configuration::updateValue( 'COMBINATIONSEO_CONCATENATE_RESULT' , $concatenate_module_content_config_value );
            Configuration::updateValue( 'COMBINATIONSEO_DROP_DATABASE' , $drop_tables_on_delete_config_value );

            $html .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Modules.combinationseo.Admin'));
        }

        $helper = $this->initForm();

        $dropDB = Configuration::get( 'COMBINATIONSEO_DROP_DATABASE' );
        $concatenate = Configuration::get( 'COMBINATIONSEO_CONCATENATE_RESULT' );

        $helper -> fields_value['concatenate'] = ($concatenate == "true" ? true : false);
        $helper -> fields_value['deletedb'] = ($dropDB == "true" ? true : false);

        $html = $html.$helper->generateForm($this->fields_form);

        return $html;
    }

    protected function initForm()
    {

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->trans('Basic Combination SEO Module configuration', array(), 'Modules.combinationseo.Admin'),
            ),
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => $this->trans('Concatenate result', array(), 'Modules.combinationseo.Admin'),
                    'name' => 'concatenate',
                    'is_bool'   => true,
                    'values'    => array(       // $values contains the data itself.
                                        array(
                                            'id'    => 'concatenate_on',      // The content of the 'id' attribute of the <input> tag, and of the 'for' attribute for the <label> tag.
                                            'value' => 1,                     // The content of the 'value' attribute of the <input> tag.   
                                            'label' => $this->trans('Enabled', array(), 'Modules.combinationseo.Admin'), // The <label> for this radio button.
                                        ),
                                        array(
                                            'id'    => 'concatenate_off',
                                            'value' => 0,
                                            'label' => $this->trans('Disabled', array(), 'Modules.combinationseo.Admin'),
                                        )
                                   ),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->trans('Drop database on uninstall', array(), 'Modules.combinationseo.Admin'),
                    'name' => 'deletedb',
                    'is_bool'   => true,
                    'values'    => array(       // $values contains the data itself.
                                        array(
                                            'id'    => 'deletedb_on',      // The content of the 'id' attribute of the <input> tag, and of the 'for' attribute for the <label> tag.
                                            'value' => 1,                     // The content of the 'value' attribute of the <input> tag.   
                                            'label' => $this->trans('Enabled', array(), 'Modules.combinationseo.Admin'), // The <label> for this radio button.
                                        ),
                                        array(
                                            'id'    => 'deletedb_off',
                                            'value' => 0,
                                            'label' => $this->trans('Disabled', array(), 'Modules.combinationseo.Admin'),
                                        )
                                   ),
                ),
            ),
            
			'submit' => array(
				'title' => $this->trans('Save', array(), 'Modules.combinationseo.Admin'),
				// 'class' => 'button'
			)
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = 'combinationseo';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->title = $this->displayName;
        $helper->submit_action = 'submitAdd' . $this -> name;
     
        return $helper;
    }


    protected function _clearCache($template, $cacheId = null, $compileId = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function getModuleAdminControllerByName($name) {
        if (empty($name)) {
            throw new PrestaShopException($this -> name .":: getModuleAdminControllerByName:: Can't get controller width an empty name");
        }
        if (!empty(self::$_controllerCache[$name])) {
            return self::$_controllerCache[$name];
        }

        $include_string = __DIR__.'/controllers/admin/' . $name . '.php';

        require_once ($include_string);

        $class_name_string = $name . 'Controller';

        self::$_controllerCache[$name] = new $class_name_string();

        return self::$_controllerCache[$name];
    }

    public function getModuleFrontControllerByName($name) {
        if (empty($name)) {
            throw new PrestaShopException($this -> name .":: getModuleFrontControllerByName:: Can't get controller width an empty name");
        }
        if (!empty(self::$_controllerCache[$name])) {
            return self::$_controllerCache[$name];
        }

        $include_string = __DIR__.'/controllers/front/' . $name . '.php';

        require_once ($include_string);

        $class_name_string = $this -> name . $name . 'ModuleFrontController';

        self::$_controllerCache[$name] = new $class_name_string();

        return self::$_controllerCache[$name];
    }
}
