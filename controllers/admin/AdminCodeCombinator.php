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

		// $this->module_name = 'combinationseo'; // se hace de forma automática

		// $this -> lang = true;
		parent::__construct();
		// $this->deleted = false;
		// $this->colorOnBackground = false;
		$this -> bulk_actions = array('delete' => array('text' => $this->trans('Delete selected', array(), 'Modules.combinationseo.Admin'), 'confirm' => $this->trans('Delete selected items?', array(), 'Modules.combinationseo.Admin')));
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
				'text' => $this->trans('Delete selected', array(), 'Modules.combinationseo.Admin'),
				'confirm' => $this->trans('Delete selected items?', array(), 'Modules.combinationseo.Admin')
				)
			);
		$this -> fields_list = array(
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
				'title' => $this->trans('Inner reference', array(), 'Modules.combinationseo.Admin'),
				'width' => 'auto',
			),
			'id_object' => array(
				'title' => $this->trans('Affected Object', array(), 'Modules.combinationseo.Admin'),
				'width' => 'auto',
			),
			'type' => array(
				'title' => $this->trans('Type of page', array(), 'Modules.combinationseo.Admin'),
				'width' => 'auto',
			),
			'order' => array(
				'title' => $this->trans('Order of the subreference', array(), 'Modules.combinationseo.Admin'),
				'width' => 'auto',
			)
		);
		// Gère les positions
		$this -> fields_list['position'] = array(
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

	public function getBlockReferences () {

		$module = Module::getInstanceByName('combinationseo');
		
		$extractFrontController = $module -> getModuleFrontControllerByName('CodeExtract');

		$result = $extractFrontController -> getBlockReferencesArr();
		
		return $result;
	}

	public function getSubreferenceArrByBlockReference($blockReference){
		$module = Module::getInstanceByName('combinationseo');
		
		$extractFrontController = $module -> getModuleFrontControllerByName('CodeExtract');

		$result = $extractFrontController -> getSubreferenceArrByBlockReference($blockReference);
		
		return $result;
	}

	public function renderForm()
	{

		$blockreferences = $this -> getBlockReferences();
		
		$options_blockreferences = array();
		foreach ($blockreferences as $blockreference){
			$options_blockreferences[] = array('id_option' => $blockreference, 'name' => $blockreference );
		}

		$id = Tools::getValue('id');
		
		if (!empty($id)) { // is being edited, not adding any new combination
			$current_combination = new CodeCombination($id);
			$selected_breference = $current_combination -> blockreference;
			$selected_subreference = $current_combination -> subreference;
		}
		else {
			if (!empty($blockreferences)){
				$selected_breference = $blockreferences[0];
			}
		}

		if (!empty ($selected_breference)) {
			$subRefereceList = $this -> getSubreferenceArrByBlockReference($selected_breference);
		}

		if (empty($selected_subreference) && (!empty ($subRefereceList))){
			$selected_subreference = $subRefereceList[0];
		}

		$options_subreferences = array();
		foreach ($subRefereceList as $subreference){
			$options_subreferences[] = array('id_option' => $subreference, 'name' => $subreference );
		}

		$type_selector_options = array (
			array ('id_option' => 'cms', 'name' => 'cms'),
			array ('id_option' => 'category', 'name' => 'category'),
			array ('id_option' => 'product', 'name' => 'product')
		);
		if (!empty($id)) { // is being edited, not adding any new combination
			$type_selected = $current_combination -> type;
		}
		else {
			$type_selected = $type_selector_options[0]['id_option'];
		}

		
		$this -> fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' =>  $this->trans('Accepted combinations', array(), 'Modules.cmsseo.Admin'),
				'image' => '../img/admin/cog.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					// 'lang' => true,
					'label' => $this->trans('ID:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'id',
					'size' => 32,
					'readonly' => true
				),
				array(
					'type' => 'select',
					// 'lang' => true,
					'label' => $this->trans('Block reference:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'blockreference',
					'class' => 'blockreference',
					'hint' => $this->trans('You will find here the references entered in Code Extracts', array(), 'Modules.cmsseo.Admin'),
					'desc' => $this->trans('You will find here the references entered in Code Extracts', array(), 'Modules.cmsseo.Admin'),
					'required' => true,
					'options' => array(
									'query' => $options_blockreferences,
									'id' => 'id_option', 
									'name' => 'name'
								),
				),
				array(
					'type' => 'select',
					'label' => $this->trans('Inner reference:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'subreference',
					'required' => true,
					'class' => 'subreference',
					'hint' => $this->trans('You will find here the subreferences entered in Code Extracts depending on the selected reference', array(), 'Modules.cmsseo.Admin'),
					'desc' => $this->trans('You will find here the subreferences entered in Code Extracts depending on the selected reference', array(), 'Modules.cmsseo.Admin'),
					'options' => array(
									'query' => $options_subreferences,
									'id' => 'id_option', 
									'name' => 'name'
								),
				),
				array(
					'type' => 'text',
					// 'lang' => true,
					'label' => $this->trans('ID object:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'id_object',
					'required' => true,
					'size' => 32
				),
				array(
					'type' => 'select',
					'label' => $this->trans('Type of page:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'type',
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
					// 'lang' => true,
					'label' => $this->trans('Position:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'order',
					'required' => true,
					'hint' => $this->trans('Should be 1 or greater. Ascending order', array(), 'Modules.cmsseo.Admin'),
					'desc' => $this->trans('Should be 1 or greater. Ascending order', array(), 'Modules.cmsseo.Admin'),
					'size' => 32
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
		
		if (!empty($id)) {
			foreach (CodeCombination::$definition['fields'] as $field){
				$this -> fields_value = array($field => $current_combination -> $field);
			}
		}
		return parent::renderForm();
	}


	public function ajaxProcessGetSubreferencesOptions() {
		
		$options = "";

		if ((Tools::isSubmit('blockreference')) /*&& (Tools::isSubmit('subreference'))*/) 
		{
			$blockreference = Tools::getValue('blockreference');
			// $subreference = Tools::getValue('subreference');
			// $language_id = Tools::getValue('language');

			if (!empty($blockreference)) {
				$code_extract_collection = new PrestashopCollection('CodeExtract', null);
				// $code_extract_collection = new PrestashopCollection('CodeExtract', $language_id);

				// $code_extract_collection -> where('blockreference', 'regexp', $blockreference);
				$code_extract_collection -> where('blockreference', '=', $blockreference);

				/*
				if (!empty($subreference)) {
					$code_extract_collection -> where('subreference', 'regexp', $subreference);
				//$code_extract_collection -> sqlWhere ('LOWER(blockreference) = "'. strtolower(blockreference). '" AND LOWER(subreference) like "%'. strtolower($subreference). '%" AND id_lang = '.$language_id);
				}
				*/
			}
				
			foreach ($code_extract_collection as $extract) {
				$options .= "<option value='{$extract -> subreference}' >{$extract -> subreference}</option>";
			}
		}
		echo $options;
		die;
	}

/*
	public function ajaxProcessGetReferencesOptions() {
		
		$options = "";

		if (Tools::isSubmit('blockreference')) 
		{
			$blockreference = Tools::getValue('blockreference');

			// $language_id = Tools::getValue('language');
			// $code_extract_collection = new PrestashopCollection('CodeExtract', $language_id);

			$code_extract_collection = new PrestashopCollection('CodeExtract', null);
			// $code_extract_collection -> join ('codeextract_lang', 'id_lang');
			if (!empty($blockreference)){
				$code_extract_collection -> where('blockreference', 'regexp', $blockreference);
			}
			//$code_extract_collection -> where('subreference', 'regexp', $subreference);
			
			//$code_extract_collection -> sqlWhere ('LOWER(blockreference) like "%'. strtolower($blockreference). '%" AND id_lang = '.$language_id);

				
			foreach ($code_extract_collection as $extract) {
				$options .= "<option value='{$extract -> blockreference}' />";
			}
		}
		echo $options;
		die;
	}
*/
	public function setMedia()
	{
		parent::setMedia();
		
		$this -> addJS(_MODULE_DIR_.$this->module->name.'/views/js/codecombinator.js');

	}


	public function display() {
		if (!$this->ajax) {
			$script_url_combinator = "<script> url_code_combinator = '" . $this->context->link->getAdminLink('Admin'.$this->name, true) . "' </script>";
			echo $script_url_combinator;
		}
		parent::display(); // returns void, so no need to return anything
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
			// $languages = Language::getLanguages(false);

			$code_combination -> subreference = 	Tools::getValue('subreference');
			$code_combination -> blockreference = 	Tools::getValue('blockreference');
			$code_combination -> id_object = 		Tools::getValue('id_object');
			$code_combination -> order = 			Tools::getValue('order');
			$code_combination -> type = 			Tools::getValue('type');
/*
			foreach ($languages as $language){
				$code_combination -> subreference[$language['id_lang']] = Tools::getValue('subreference_'.$language['id_lang']);
				$code_combination -> blockreference[$language['id_lang']] = Tools::getValue('blockreference_'.$language['id_lang']);
				$code_combination -> id_object[$language['id_lang']] = Tools::getValue('id_object_'.$language['id_lang']);
				$code_combination -> order[$language['id_lang']] = Tools::getValue('order_'.$language['id_lang']);
				$code_combination -> type[$language['id_lang']] = Tools::getValue('type_'.$language['id_lang']);


				if ($default_lang == $language['id_lang']) {
					if (empty ($code_combination -> blockreference[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: blockreference couldn\'t be empty in the default language');
					}
					if (empty ($code_combination -> subreference[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: subreference couldn\'t be empty in the default language');
					}
					if (empty ($code_combination -> id_object[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: id_object couldn\'t be empty in the default language');
					}		
					if (empty ($code_combination -> order[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: order couldn\'t be empty in the default language');
					}		
					if (empty ($code_combination -> type[$language['id_lang']]) ) {
						$this->errors[] = Tools::displayError('An error has occurred: type couldn\'t be empty in the default language');
					}
				}

			}
*/
			//if (empty($this->errors)) {	
				// Save object
			if (!$code_combination->save()) {
				$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
			}
			else {
				Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this -> token);
			}
			//}
		}

		parent::postProcess();
	}

}