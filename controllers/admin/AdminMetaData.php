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
 * Tab Metadata - Controller Admin Metadata
 *
 * @category   	Module / frontofficefeatures
 * @author     	Manuel José Pulgar Anguita <cibermanu@hotmail.com>
 * @copyright  	2017 Manuel José Pulgar Anguita
*/

class AdminMetaDataController extends ModuleAdminController
{
	public function __construct()
	{
		$this -> table = 'combinationseometadata';
		$this -> identifier = 'id'; // identifier in the table where the data is stored (for renderList method)

		$this -> bootstrap = true;
		//$this->display = 'view';
		$this->show_form_cancel_button = false;

		$this -> className = 'CombinationSeoMetaData'; 
		$this -> lang = true;

		$this -> name = 'MetaData';

		$this->multishop_context = Shop::CONTEXT_SHOP;

		parent::__construct();

		$this->bulk_actions = array('delete' => array('text' => $this->trans('Delete selected', array(), 'Modules.cmsseo.Admin'), 'confirm' => $this->trans('Delete selected items?', array(), 'Modules.cmsseo.Admin')));
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
				'text' => $this->trans('Delete selected', array(), 'Modules.cmsseo.Admin'),
				'confirm' => $this->trans('Delete selected items?', array(), 'Modules.cmsseo.Admin')
				)
			);
		$this->fields_list = array(
			'id' => array(
				'title' => $this->trans('ID', array(), 'Modules.cmsseo.Admin'),
				'align' => 'center',
				'width' => 25
			),
			'id_object' => array(
				'title' => $this->trans('Object ID', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			),
			'object_type' => array(
				'title' => $this->trans('Object type', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			),
			'link_rewrite' => array(
				'title' => $this->trans('Link to be rewrited to', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			),
		);
		// Gère les positions
		$this->fields_list['position'] = array(
			'title' => $this->trans('PUEDE QUE HAYA QUE BORRAR POSITION DE TODOS LOS CONTROLLERS Position', array(), 'Modules.cmsseo.Admin'),
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

		$type_selector_options = array (
			array ('id_option' => 'cms', 'name' => 'cms'),
			array ('id_option' => 'category', 'name' => 'category'),
			array ('id_option' => 'product', 'name' => 'product')
		);

		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->trans('Meta Data look at changing in all controllers cog.gif', array(), 'Modules.cmsseo.Admin'),
				'image' => '../img/admin/cog.gif'
			),

	
			'input' => array(
				array(
					'type' => 'text',
					// 'lang' => true,
					'label' => $this->trans('ID:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'id',
					'readonly' => true,
					'size' => 32
				),
				array(
					'type' => 'text',
					// 'lang' => true,
					'label' => $this->trans('Id object:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'id_object',
					'required' => true,
					'size' => 32
				),
				array(
					'type' => 'select',
					// 'lang' => true,
					'label' => $this->trans('Object type:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'object_type',
					'required' => true,
					'hint' => $this->trans('Accepted values are: cms, product and category', array(), 'Modules.cmsseo.Admin'),
					'desc' => $this->trans('Accepted values are: cms, product and category', array(), 'Modules.cmsseo.Admin'),
					'options' => array(
						'query' => $type_selector_options,  // $options contains the data itself.
						'id' => 'id_option',         		// The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
						'name' => 'name',             		// The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
					),
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->trans('Meta title:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'meta_title',
					'size' => 128
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->trans('Meta description:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'meta_description',
					'size' => 255
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->trans('Meta keywords:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'meta_keywords',
					'size' => 255
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->trans('Link rewrite:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'link_rewrite',
					'required' => true,
					'size' => 128
				)
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
				'title' => $this->trans('Save', array(), 'Modules.cmsseo.Admin'),
				'class' => 'button'
			)
		);
		if (!($obj = $this->loadObject(true)))
			return;

		$id = Tools::getValue('id');

		if (!empty($id)) { // editing, not adding new meta
			$meta = new CombinationSeoMetaData($id);
			foreach (CombinationSeoMetaData::$definition['fields'] as $field){
				$this -> fields_value = array($field => $meta -> $field);
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

			$metadata = new CombinationSeoMetaData($id, null, $shop);
			$languages = Language::getLanguages(false);

			$metadata -> id_object = 	Tools::getValue('id_object');
			$metadata -> object_type = 	Tools::getValue('object_type');
			$metadata -> meta_title = array();
			$metadata -> meta_description = array();
			$metadata -> meta_keywords = array();
			$metadata -> link_rewrite = array();

			$default_lang = Configuration::get('PS_LANG_DEFAULT');
			
			foreach ($languages as $language){
				// $metadata -> id_object[$language['id_lang']] = Tools::getValue('id_object_'.$language['id_lang']);
				// $metadata -> object_type[$language['id_lang']] = Tools::getValue('object_type_'.$language['id_lang']);
				$metadata -> meta_title[$language['id_lang']] = Tools::getValue('meta_title_'.$language['id_lang']);				
				$metadata -> meta_description[$language['id_lang']] = Tools::getValue('meta_description_'.$language['id_lang']);
				$metadata -> meta_keywords[$language['id_lang']] = Tools::getValue('meta_keywords_'.$language['id_lang']);
				$metadata -> link_rewrite[$language['id_lang']] = Tools::getValue('link_rewrite_'.$language['id_lang']);

				if ($default_lang == $language['id_lang']) {
					/*
					if (empty ($metadata -> id_object[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: id_object couldn\'t be empty in the default language');
					}
					if (empty ($metadata -> object_type[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: object_type couldn\'t be empty in the default language');
					}
					*/
					if (empty ($metadata -> link_rewrite[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: link_rewrite couldn\'t be empty in the default language');
					}				
				}
			}
				
			// Save object
			if (empty($this->errors)) {
				if (!$metadata->save()) {
					$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
				}
				else {
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				}
			}
		}
	}

}