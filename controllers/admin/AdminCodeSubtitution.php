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

class AdminCodeSubtitutionController extends ModuleAdminController
{
	public function __construct()
	{
		$this -> table = 'codesubtitutions';
		$this -> identifier = 'id'; // identifier in the table where the data is stored (for renderList method)

		$this -> bootstrap = true;
		$this -> className = 'CodeSubtitution'; // associated objectcontroller

		$this -> show_form_cancel_button = false; // don't show default cancel button in renderForm
		$this -> name = 'CodeSubtitution';

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
		$this -> bulk_actions = array(/* Thumbnail
		 * @todo Error, deletion of the image
		*/
		/*
		$image = ImageManager::thumbnail(_PS_IMG_DIR_.'region/'.$obj->id.'.jpg', $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350, $this->imageType, true);
		$this->fields_value = array(
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_IMG_DIR_.'example/'.$obj->id.'.jpg') / 1000 : false,
		);
		*/
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
			'search' => array(
				'title' => $this->trans('String to search in code', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			),
			'replace' => array(
				'title' => $this->trans('Replacement for the searched string', array(), 'Modules.cmsseo.Admin'),
				'width' => 'auto',
			)
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
					// 'lang' => true,
					'label' => $this->l('ID:'),
					'name' => 'id',
					//'required' => true,
					'readonly' => true,
					'size' => 32
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('String to search in the code:'),
					'name' => 'search',
					'required' => true,
					'size' => 128
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('Replacement of searched string:'),
					'name' => 'replace',
					'required' => true,
					'size' => 32
				),
				array(
					'type' => 'text',
					// 'lang' => true,
					'label' => $this->l('Related block reference:'),
					'name' => 'blockreference',
					'required' => true,
					'size' => 32
				),
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
		
		$id = Tools::getValue('id');

		if (!empty($id)) { // editing, not adding new meta
			$subtitution = new CodeSubtitution($id);
			foreach (CodeSubtitution::$definition['fields'] as $field){
				$this -> fields_value = array($field => $subtitution -> $field);
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

			$code_subtitution = new CodeSubtitution($id, null, $shop);
			$languages = Language::getLanguages(false);

			$code_subtitution -> subreference = 		Tools::getValue('subreference');
			$code_subtitution -> blockreference = 		Tools::getValue('blockreference');
			$code_subtitution -> search = array();
			$code_subtitution -> replace = array();
			
			foreach ($languages as $language){
			
				$code_subtitution->search[$language['id_lang']] = Tools::getValue('search_'.$language['id_lang']);
				$code_subtitution->replace[$language['id_lang']] = Tools::getValue('replace_'.$language['id_lang']);


				if ($default_lang == $language['id_lang']) {
					
					if (empty ($code_subtitution -> search[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: search couldn\'t be empty in the default language');
					}		
					if (empty ($code_subtitution -> replace[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: replace couldn\'t be empty in the default language');
					}		
				}
			}

			if (empty($this->errors)) {	
				// Save object
				if (!$code_subtitution->save()) {
					$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
				}
				else {
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				}
			}
		}
	}

}