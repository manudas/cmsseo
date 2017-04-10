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

class cmsseo extends Module
{
    public function __construct()
    {
        $this->name = 'cmsseo';

        require_once (_PS_MODULE_DIR_.$this->name.'/models/codeextract.php');

        $this->author = 'Manuel José Pulgar Anguita';
        $this->version = '0.1';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('CMS SEO Module', array(), 'Modules.cmsseo.Admin');
        $this->description = $this->trans('Generate your CMS pages in an automated and easy way, so you can improve your SEO faster.', array(), 'Modules.cmsseo.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        // $this->templateFile = 'module:cmsseo/views/templates/hook/cmsseo.tpl';
    }

    public function install()
    {
        return $this->installDB()
            && $this->installTabs()
            && parent::install()
        ;
    }

    private function installTabs() {
        $sectionTab = $this -> createSection($this -> name);
        $parentTab = $this -> installTab('AdminCodeCombinator', 'Combinaciones de CMS', $this -> name);
        $parentTab2 = $this -> installTab('AdminCodeExtract', 'Extractos de CMS', $this -> name);
        return $sectionTab && $parentTab && $parentTab2;
    }


    private function installTab($className, $tabName, $tabParentName = false)
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
        if ($tabParentName) {
            $tab -> id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab -> id_parent = (int) Tab::getIdFromClassName('default') ;
        }
        $tab -> module = $this -> name;
        return $tab->add();
    }

    public function installDB()
    {
        $return = CodeExtract::createTables();
       
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
            && parent::uninstall();
    }

    public function uninstallDB()
    {
        $return = CodeExtract::dropTables();
        return $return;
    }


    public function getContent()
    {
        $html = '';
        $id_reassurance = (int)Tools::getValue('id_reassurance');

        if (Tools::isSubmit('savecmsseo')) {
            if ($id_reassurance = Tools::getValue('id_reassurance')) {
                $reassurance = new reassuranceClass((int)$id_reassurance);
            } else {
                $reassurance = new reassuranceClass();
            }

            $reassurance->copyFromPost();
            $reassurance->id_shop = $this->context->shop->id;

            if ($reassurance->validateFields(false) && $reassurance->validateFieldsLang(false)) {
                $reassurance->save();

                if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
                    if ($error = ImageManager::validateUpload($_FILES['image'])) {
                        return false;
                    } elseif (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmpName)) {
                        return false;
                    } elseif (!ImageManager::resize($tmpName, dirname(__FILE__).'/img/reassurance-'.(int)$reassurance->id.'-'.(int)$reassurance->id_shop.'.jpg')) {
                        return false;
                    }

                    unlink($tmpName);
                    $reassurance->file_name = 'reassurance-'.(int)$reassurance->id.'-'.(int)$reassurance->id_shop.'.jpg';
                    $reassurance->save();
                }
                $this->_clearCache('*');
            } else {
                $html .= '<div class="conf error">'.$this->trans('An error occurred while attempting to save.', array(), 'Admin.Notifications.Error').'</div>';
            }
        }

        if (Tools::isSubmit('updatecmsseo') || Tools::isSubmit('addcmsseo')) {
            $helper = $this->initForm();
            foreach (Language::getLanguages(false) as $lang) {
                if ($id_reassurance) {
                    $reassurance = new reassuranceClass((int)$id_reassurance);
                    $helper->fields_value['text'][(int)$lang['id_lang']] = $reassurance->text[(int)$lang['id_lang']];
                    $image = dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$reassurance->file_name;
                    $this->fields_form[0]['form']['input'][0]['image'] = '<img src="'.$this->getImageURL($reassurance->file_name).'" />';
                } else {
                    $helper->fields_value['text'][(int)$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang'], '');
                }
            }
            if ($id_reassurance = Tools::getValue('id_reassurance')) {
                $this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_reassurance');
                $helper->fields_value['id_reassurance'] = (int)$id_reassurance;
            }

            return $html.$helper->generateForm($this->fields_form);
        } elseif (Tools::isSubmit('deletecmsseo')) {
            $reassurance = new reassuranceClass((int)$id_reassurance);
            if (file_exists(dirname(__FILE__).'/img/'.$reassurance->file_name)) {
                unlink(dirname(__FILE__).'/img/'.$reassurance->file_name);
            }
            $reassurance->delete();
            $this->_clearCache('*');
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        } else {
            $content = $this->getListContent((int)Configuration::get('PS_LANG_DEFAULT'));
            $helper = $this->initList();
            $helper->listTotal = count($content);
            return $html.$helper->generateList($content, $this->fields_list);
        }

        if (isset($_POST['submitModule'])) {
            Configuration::updateValue('cmsseo_NBBLOCKS', ((isset($_POST['nbblocks']) && $_POST['nbblocks'] != '') ? (int)$_POST['nbblocks'] : ''));
            if ($this->removeFromDB() && $this->addToDB()) {
                $this->_clearCache('cmsseo.tpl');
                $output = '<div class="conf confirm">'.$this->trans('The block configuration has been updated.', array(), 'Modules.cmsseo.Admin').'</div>';
            } else {
                $output = '<div class="conf error"><img src="../img/admin/disabled.gif"/>'.$this->trans('An error occurred while attempting to save.', array(), 'Admin.Notifications.Error').'</div>';
            }
        }
    }

    protected function getListContent($id_lang)
    {
        return  Db::getInstance()->executeS('
            SELECT r.`id_reassurance`, r.`id_shop`, r.`file_name`, rl.`text`
            FROM `'._DB_PREFIX_.'reassurance` r
            LEFT JOIN `'._DB_PREFIX_.'reassurance_lang` rl ON (r.`id_reassurance` = rl.`id_reassurance`)
            WHERE `id_lang` = '.(int)$id_lang.' '.Shop::addSqlRestrictionOnLang());
    }

    protected function initForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->trans('New reassurance block', array(), 'Modules.cmsseo.Admin'),
            ),
            'input' => array(
                array(
                    'type' => 'file',
                    'label' => $this->trans('Image', array(), 'Admin.Global'),
                    'name' => 'image',
                    'value' => true,
                    'display_image' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Text', array(), 'Admin.Global'),
                    'lang' => true,
                    'name' => 'text',
                    'cols' => 40,
                    'rows' => 10
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = 'cmsseo';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'savecmsseo';
        $helper->toolbar_btn =  array(
            'save' =>
            array(
                'desc' => $this->trans('Save', array(), 'Admin.Actions'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' =>
            array(
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->trans('Back to list', array(), 'Admin.Actions'),
            )
        );
        return $helper;
    }

    protected function initList()
    {
        $this->fields_list = array(
            'id_reassurance' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'width' => 120,
                'type' => 'text',
                'search' => false,
                'orderby' => false
            ),
            'text' => array(
                'title' => $this->trans('Text', array(), 'Admin.Global'),
                'width' => 140,
                'type' => 'text',
                'search' => false,
                'orderby' => false
            ),
        );

        if (Shop::isFeatureActive()) {
            $this->fields_list['id_shop'] = array(
                'title' => $this->trans('ID Shop', array(), 'Modules.cmsseo.Admin'),
                'align' => 'center',
                'width' => 25,
                'type' => 'int'
            );
        }

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_reassurance';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->imageType = 'jpg';
        $helper->toolbar_btn['new'] =  array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->trans('Add new', array(), 'Admin.Actions')
        );

        $helper->title = $this->displayName;
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        return $helper;
    }

    protected function _clearCache($template, $cacheId = null, $compileId = null)
    {
        parent::_clearCache($this->templateFile);
    }

    private function createSection($tab_section_name) {
        $tab = new Tab();
        $tab -> active = 1;
        $tab -> class_name = $this -> name;
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

/*
    private function getImageURL($image)
    {
        return $this->context->link->getMediaLink(__PS_BASE_URI__.'modules/'.$this->name.'/img/'.$image);
    }
    */
}
