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

class AdminCodeCombinatorController extends ModuleAdminController
{
	public function __construct()
	{
		$this -> table = 'codecombinations';
		$this -> identifier = 'id'; // identifier in the table where the data is stored (for renderList method)

		$this -> bootstrap = true;
		$this -> className = 'CodeCombination'; // associated objectcontroller

		$this -> show_form_cancel_button = false; // don't show default cancel button in renderForm
		$this -> name = 'CodeCombinator';

		$this -> lang = true;
		parent::__construct();
		// $this->deleted = false;
		// $this->colorOnBackground = false;
		$this -> bulk_actions = array('delete' => array('text' => $this->trans('Delete selected', array(), 'Modules.cmsseo.Admin'), 'confirm' => $this->trans('Delete selected items?', array(), 'Modules.cmsseo.Admin')));
		$this -> context = Context::getContext();
		// définition de l'upload, chemin par défaut _PS_IMG_DIR_
		// $this->fieldImageSettings = array('name' => 'image', 'dir' => 'example');
		
	}
	/**
	 * Function used to render the list to display for this controller
	 */
	public function renderList()
	{
		$this -> addRowAction('edit');
		$this -> addRowAction('delete');
		$this -> addRowAction('details');
		$this -> bulk_actions = array(
			'delete' => array(
				'text' => $this->trans('Delete selected', array(), 'Modules.cmsseo.Admin'),
				'confirm' => $this->trans('Delete selected items?', array(), 'Modules.cmsseo.Admin')
				)
			);
		$this -> fields_list = array(
			'id' => array(
				'title' => $this->trans('ID', array(), 'Modules.cmsseo.Admin'),
				'align' => 'center',
				'width' => 25
			),
			'blockreference' => array(
				'title' => $this->trans('Block reference', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			),
			'subreference' => array(
				'title' => $this->trans('Inner reference', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			),
			'id_cms' => array(
				'title' => $this->trans('Affected CMS', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			),
			'order' => array(
				'title' => $this->trans('Order of the subreference', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			)
		);
		// Gère les positions
		$this -> fields_list['position'] = array(
			'title' => $this->trans('Position', array(), 'Modules.cmsseo.Admin'),
			'width' => 70,
			'align' => 'center',
			'position' => 'position'
		);

		$lists = parent::renderList();
		parent::initToolbar();
		return $lists;
	}


	/**
	 * method call when ajax request is made with the details row action
	 * @see AdminController::postProcess()
	 */
	/*
	public function ajaxProcessDetails()
	{
		if (($id = Tools::getValue('id')))
		{
			// override attributes
			$this->display = 'list';
			$this->lang = false;
			$this->addRowAction('edit');
			$this->addRowAction('delete');
			$this->_select = 'b.*';
			$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'tab_lang` b ON (b.`id_tab` = a.`id_tab` AND b.`id_lang` = '.$this->context->language->id.')';
			$this->_where = 'AND a.`id_parent` = '.(int)$id;
			$this->_orderBy = 'position';
			// get list and force no limit clause in the request
			$this->getList($this->context->language->id);
			// Render list
			$helper = new HelperList();
			$helper->actions = $this->actions;
			$helper->list_skip_actions = $this->list_skip_actions;
			$helper->no_link = true;
			$helper->shopLinkType = '';
			$helper->identifier = $this->identifier;
			$helper->imageType = $this->imageType;
			$helper->toolbar_scroll = false;
			$helper->show_toolbar = false;
			$helper->orderBy = 'position';
			$helper->orderWay = 'ASC';
			$helper->currentIndex = self::$currentIndex;
			$helper->token = $this->token;
			$helper->table = $this->table;
			$helper->position_identifier = $this->position_identifier;
			// Force render - no filter, form, js, sorting ...
			$helper->simple_header = true;
			$content = $helper->generateList($this->_list, $this->fields_list);
			echo Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content));
		}
		die;
	}
	*/

	public function renderForm()
	{
		$this -> fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Accepted combinations'),
				'image' => '../img/admin/cog.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('ID:'),
					'name' => 'id',
					'size' => 32
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('Block reference:'),
					'name' => 'blockreference',
					'size' => 32
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('Inner reference:'),
					'name' => 'subreference',
					'size' => 32
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('ID CMS:'),
					'name' => 'id_cms',
					'size' => 32
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('Position:'),
					'name' => 'order',
					'size' => 32
				)
				/*
				,
				array(
					'type' => 'file',
					'label' => $this->l('Logo:'),
					'name' => 'image',
					'display_image' => true,
					'desc' => $this->l('Upload Example image from your computer')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Lorem:'),
					'name' => 'lorem',
					'readonly' => true,
					'disabled' => true,
					'size' => 40
				),
				array(
					'type' => 'date',
					'name' => 'exampledate',
				)
				*/
			),
			'buttons' => array(
                'cancelBlock' => array(
                    'title' => $this->trans('Cancel', array(), 'Modules.cmsseo.Admin'),
                    'href' => (Tools::safeOutput(Tools::getValue('back', false)))
                                ?: $this->context->link->getAdminLink('Admin'.$this->name),
                    'icon' => 'process-icon-cancel',
					'class' => 'pull-right'
                )
            ),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);
		if (!($obj = $this->loadObject(true)))
			return;
		/* Thumbnail
		 * @todo Error, deletion of the image
		*/
		/*
		$image = ImageManager::thumbnail(_PS_IMG_DIR_.'region/'.$obj->id.'.jpg', $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350, $this->imageType, true);
		$this->fields_value = array(
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_IMG_DIR_.'example/'.$obj->id.'.jpg') / 1000 : false,
		);
		*/
		$this -> fields_value = array('blockreference' => 'hacer');
		return parent::renderForm();
	}
	public function postProcess()
	{
		if (Tools::isSubmit('submitAdd'.$this->table))
		{


			$shop = Tools::getValue('id_shop');
			$id = Tools::getValue('id');

			/*
			'id' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
			'subreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => TRUE),
			'blockreference' =>      	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => TRUE),
			'id_cms' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'lang' => TRUE),
			'order' =>      			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'lang' => TRUE)
			*/
			$code_combination = new CodeCombination($id, null, $shop);
			$languages = Language::getLanguages(false);

			$code_combination->subreference = array();
			$code_combination->blockreference = array();
			$code_combination->id_cms = array();
			$code_combination->order = array();
			
			foreach ($languages as $language){
				$code_combination->subreference[$language['id_lang']] = Tools::getValue('subreference_'.$language['id_lang']);
				$code_combination->blockreference[$language['id_lang']] = Tools::getValue('blockreference_'.$language['id_lang']);
				$code_combination->id_cms[$language['id_lang']] = Tools::getValue('id_cms_'.$language['id_lang']);
				$code_combination->order[$language['id_lang']] = Tools::getValue('order_'.$language['id_lang']);
			}
				
			// Save object
			if (!$code_combination->save())
				$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
			else
				Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
		}
	}
}