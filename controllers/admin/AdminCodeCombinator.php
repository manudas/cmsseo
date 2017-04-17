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

		$this -> lang = true;
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

	public function renderForm()
	{
		$this -> fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' =>  $this->trans('Accepted combinations', array(), 'Modules.cmsseo.Admin'),
				'image' => '../img/admin/cog.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->trans('ID:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'id',
					'size' => 32,
					'readonly' => true
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->trans('Block reference:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'blockreference',
					'class' => 'blockreference',
					'required' => true,
					'size' => 32
				),
				array(
					'type' => 'select',
					'lang' => true,
					'label' => $this->trans('Inner reference:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'subreference',
					'required' => true,
					// 'size' => 32
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->trans('ID object:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'id_object',
					'required' => true,
					'size' => 32
				),
				array(
					'type' => 'select',
					'lang' => true,
					'label' => $this->trans('Type of page:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'type',
					'required' => true,
					'options' => array(
					'query' => CodeCombination::$_COMBINATION_TYPE_OPTIONS, // $options contains the data itself.
					'id' => 'id_option',         							// The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
					'name' => 'name'             							// The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
				)
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->trans('Position:', array(), 'Modules.cmsseo.Admin'),
					'name' => 'order',
					'required' => true,
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
		
		// @todo: fill data in $this -> fields_value

		$this -> fields_value = array('blockreference' => 'hacer');
		return parent::renderForm();
	}


	public function ajaxProcessGetSubreferencesOptions() {
		
		$options = "";

		if (Tools::isSubmit('blockreference'))
		{
			$blockreference = Tools::getValue('blockreference');

			$language_id = $this->context->language->id;

			$code_extract_collection = new PrestashopCollection('CodeExtract', $language_id);
			// $code_extract_collection -> sqlWhere ('LOWER(blockreference) like "%'. strtolower($blockreference). '%" AND id_lang = '.$language_id);
			$code_extract_collection -> sqlWhere ('blockreference = "'. $blockreference . '" AND id_lang = '.$language_id);

			// $hydrated_collection = $code_extract_collection -> getAll();
			// if (!empty($code_extract_collection -> results)) {
				
			foreach ($code_extract_collection as $extract) {
				$options .= "<option value='{$extract -> subreference}'>{$extract -> subreference}</option>";
			}
			// }
		}
		
		echo $options;
		die;
	}


	public function setMedia()
	{
		parent::setMedia();
		
		$this -> addJS(_MODULE_DIR_.$this->module->name.'/views/js/codecombinator.js');

		// $this -> registerJavascript('codecombinator', _MODULE_DIR_.$this->module->name.'/views/js/codecombinator.js');
	}

	public function display() {
		$script_url_combinator = "<script> url_code_combinator = '" . $this->context->link->getAdminLink('Admin'.$this->name, true) . "' </script>";
		echo $script_url_combinator;
		return parent::display();
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

			if (empty($this->errors)) {	
				// Save object
				if (!$code_combination->save()) {
					$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
				}
				else {
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this -> token);
				}
			}
		}

		parent::postProcess();
	}

	 /**
    * @return An array with the following structure:
    * - principal index: blockreference
    * - secondary index: order of subreference
    * - third index: subreference and ObjectModel CodeCombination
    */
    private function getSortedCombination($type, $blockreference = null, $subreferenceList = null) {
        if (empty($type)) {
            throw new PrestaShopException($this -> name .":: getSortedCombination:: Can't get set width an empty type");
        }
        $subreferenceString = "";
        if (!empty($subreferenceList)) {
            $subreferenceString = ' AND subreference IN ('.implode(',',$subreferenceList).')';
        }
        $blockreferenceString = "";
        if (!empty($blockreference)) {
            $blockreferenceString = ' AND blockreference = '.$blockreference;
        }
        $typeString = " type = '$type' ";

        $combinationCollection = new PrestashopCollection('CodeCombination');
        
        $whereString = $typeString . $blockreferenceString . $subreferenceString;

        $combinationCollection -> orderBy ('order');

        $combinationCollection -> sqlWhere ($whereString);

        $result = array();

        if (!empty($combinationCollection)) {
            foreach ($combinationCollection as $combination) {
                $result [$combination -> blockreference] [$combination -> order] ['subreference'] = $combination -> subreference;
                $result [$combination -> blockreference] [$combination -> order] ['object'] = $combination;
            }
        }
        
        return $result;
    }

	private function getCombinationReferenceStructure ($type, $blockreference = null, $subreferenceList = null){
		if (empty($type)) {
            throw new PrestaShopException($this -> name .":: getCombinationReferenceStructure:: Can't get set width an empty type");
        }

        $subreferenceString = "";
        if (!empty($subreferenceList)) {
            $subreferenceString = ' AND subreference IN ('.implode(',',$subreferenceList).')';
        }
        $blockreferenceString = "";
        if (!empty($blockreference)) {
            $blockreferenceString = ' AND blockreference = '.$blockreference;
        }
        $typeString = " type = '$type' ";

        $combinationCollection = new PrestashopCollection('CodeCombination');

        $whereString = $typeString . $blockreferenceString . $subreferenceString;

        $combinationCollection -> orderBy ('order');

        $combinationCollection -> sqlWhere ($whereString);

        $result = array();

        if (!empty($combinationCollection)) {
            foreach ($combinationCollection as $combination) {
                $result [$combination -> blockreference] [$combination -> subreference] = $combination;
            }
        }
        
        return $result;
	}

	private function replaceBlockReferenceInSortedCombination ($type, $blockreference = null, $subreferenceList = null){
		$combinationseo_module = /* Module :: getInstanceByName ( */ $this -> module /* -> name)*/;

		$codeExtractAdminController = $combinationseo_module -> getModuleAdminControllerByName('AdminCodeExtract');

		$codeSubtitutionAdminController = $combinationseo_module -> getModuleAdminControllerByName('AdminCodeSubtitution');

		$sortedCombination = $this -> getSortedCombination ( $type, $blockreference, $subreferenceList );

		$combinationReferenceStructure = $this -> getCombinationReferenceStructure ($type, $blockreference, $subreferenceList );

		if (!empty($sortedCombination)){

			$extractArr = array();			

			$sortedExtractArr = array();

			$subtitutedSortedArr = array();

			foreach ($combinationReferenceStructure as $key_block_reference => $combinationReference) {

				/* Normalmente el bloque del primer foreach solo se va a ejecutar una vez."
				 * Sin embargo se hace el foreach para producir fácilmente futuras
				 * ampliaciones.
				 */
				$subreferenceList = array_keys ($combinationReference);			
				$extractArr = $extractArr + $codeExtractAdminController -> getCodeExtractCollection ($key_block_reference, $subreferenceList);

				$sortedBlock = $sortedCombination [$key_block_reference];

				$subtitutions = $codeSubtitutionAdminController -> getSubtitutions($key_block_reference);
				$searchArr = $subtitutions['search'];
				$replaceArr = $subtitutions['replace'];

				foreach ($sortedBlock as $order => $subreferenceStructure) {
					$subject = $extractArr[$subreferenceStructure['subreference']]; // fills with non-subtituted text
					$sortedExtractArr [$key_block_reference] [$order] = $subject;

					$subtitutedSortedArr [$key_block_reference] [$order] = $codeSubtitutionAdminController -> replaceTokenList ($searchArr, $replaceArr, $subject);
				}
			}
		}
		
		return $subtitutedSortedArr;
	}

	public function getReplacedBlockString ($type, $blockreference = null, $subreferenceList = null) {
		$subtitutedBlockReferenceArr = $this -> replaceBlockReferenceInSortedCombination ($type, $blockreference, $subreferenceList);

		if (!empty($subtitutedBlockReferenceArr)) {
			
			$result = array ();

			firstforeachloop:			
			foreach ($subtitutedBlockReferenceArr as $block_reference_key => $sortedSubtitutedList) {

				/* Usually we only enter once in this loop (the fist foreach loop) */

				$current_string = "";
				innerforeachloop:
				foreach ($sortedSubtitutedList as $order => $subtitutedText){ 
					$current_string .= $subtitutedText;
				}

				$result[$block_reference_key] = $current_string;

			}
		}
		
		return $result;
	}

}