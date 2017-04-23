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
 * Tab CodeExtract - Controller Admin CodeExtract
 *
 * @category   	Module / frontofficefeatures
 * @author     	Manuel José Pulgar Anguita <cibermanu@hotmail.com>
 * @copyright  	2017 Manuel José Pulgar Anguita
*/

class AdminCodeExtractController extends ModuleAdminController
{
	public function __construct()
	{
		$this -> table = 'codeextracts';
		$this -> identifier = 'id'; // identifier in the table where the data is stored (for renderList method)

		$this -> bootstrap = true;
		//$this->display = 'view';
		$this->show_form_cancel_button = false;

		$this -> className = 'CodeExtract'; // if fails without core use: codeExtractCore
		$this -> lang = true;

		$this -> name = 'CodeExtract';

		$this->multishop_context = Shop::CONTEXT_SHOP;

		parent::__construct();

		$this->bulk_actions = array(
			'delete' => array(
							'text' => $this->trans('Delete selected', array(), 'Modules.combinationseo.Admin'), 
							'confirm' => $this->trans('Delete selected items?', array(), 'Modules.combinationseo.Admin')
						)
				);
		$this->context = Context::getContext();

		
	}
	/**
	 * Function used to render the list to display for this controller
	 */
	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('details');
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->trans('Delete selected', array(), 'Modules.combinationseo.Admin'),
				'confirm' => $this->trans('Delete selected items?', array(), 'Modules.combinationseo.Admin')
				)
			);
		$this->fields_list = array(
			'id' => array(
				'title' => $this->trans('ID', array(), 'Modules.combinationseo.Admin'),
				'align' => 'center',
				'width' => 25
			),
			'blockreference' => array(
				'title' => $this->trans('Block reference', array(), 'Modules.combinationseo.Admin'),
				'width' => 'auto',
			),
			'subreference' => array(
				'title' => $this->trans('Subreference of block', array(), 'Modules.combinationseo.Admin'),
				'width' => 'auto',
			),
			'text' => array(
				'title' => $this->trans('text', array(), 'Modules.combinationseo.Admin'),
				'width' => 'auto',
			),
		);
		// Gère les positions
		$this->fields_list['position'] = array(
			'title' => $this->trans('Position', array(), 'Modules.combinationseo.Admin'),
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
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->trans('Extract', array(), 'Modules.combinationseo.Admin'),
				'image' => '../img/admin/cog.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					// 'lang' => true,
					'label' => $this->trans('Block reference:', array(), 'Modules.combinationseo.Admin'),
					'name' => 'blockreference',
					'required' => true,
					'size' => 32
				),
				array(
					'type' => 'text',
					// 'lang' => true,
					'label' => $this->trans('Sub reference:', array(), 'Modules.combinationseo.Admin'),
					'name' => 'subreference',
					'required' => true,
					'size' => 32
				),
				array(
					'type' => 'textarea',
					'lang' => true,
					'label' => $this->trans('Text:', array(), 'Modules.combinationseo.Admin'),
					'name' => 'text',
					'cols' => 60,
					'rows' => 20,
					'class' => 'rte',
					'autoload_rte' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'required' => true,
					'size' => 65536
				)
			),
			'buttons' => array(
                'cancelBlock' => array(
                    'title' => $this->trans('Cancel', array(), 'Modules.combinationseo.Admin'),
                    'href' => (Tools::safeOutput(Tools::getValue('back', false)))
                                ?: $this->context->link->getAdminLink('Admin'.$this->name),
                    'icon' => 'process-icon-cancel',
					'class' => 'pull-right'
                )
            ),
			'submit' => array(
				'title' => $this->trans('Save', array(), 'Modules.combinationseo.Admin'),
				'class' => 'button'
			)
		);
		if (!($obj = $this->loadObject(true)))
			return;
		

		$id = Tools::getValue('id');

		if (!empty($id)) { // editing, not adding new meta
			$extract = new CodeExtract($id);
			foreach (CodeExtract::$definition['fields'] as $field){
				$this -> fields_value = array($field => $extract -> $field);
			}
		}


		return parent::renderForm();
	}
	public function postProcess()
	{
		if (Tools::isSubmit('submitAdd'.$this->table))
		{


			$shop = Tools::getValue('id_shop');
			$id = Tools::getValue('id');

			$code_extract = new CodeExtract($id, null, $shop);
			$languages = Language::getLanguages(false);

			$code_extract -> subreference = Tools::getValue('subreference');
			$code_extract -> blockreference =  Tools::getValue('blockreference');
			$code_extract -> text = array();

			$default_lang = Configuration::get('PS_LANG_DEFAULT');
			
			foreach ($languages as $language){
				
				$code_extract -> text[$language['id_lang']] = Tools::getValue('text_'.$language['id_lang']);

				if ($default_lang == $language['id_lang']) {
					
					if (empty ($code_extract -> text[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: text couldn\'t be empty in the default language');
					}				
				}
			}
				
			// Save object
			if (empty($this->errors)) {
				if (!$code_extract->save()) {
					$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
				}
				else {
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				}
			}
		}
	}

}