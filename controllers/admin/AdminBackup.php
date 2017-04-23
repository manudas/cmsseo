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


class AdminBackupController extends ModuleAdminController
{
	public function __construct()
	{
		// $this -> table = 'combinationseometadata';
		// $this -> identifier = 'id'; // identifier in the table where the data is stored (for renderList method)

		$this -> bootstrap = true;
		//$this->display = 'view';
		// $this->show_form_cancel_button = false;

		// $this -> className = 'CombinationSeoBackup'; 
		$this -> lang = true;

		$this -> name = 'Backup';

		$this->multishop_context = Shop::CONTEXT_SHOP;

		$this -> display = 'backupform';
// $this->bulk_actions = array('delete' => array('text' => $this->trans('Delete selected', array(), 'Modules.cmsseo.Admin'), 'confirm' => $this->trans('Delete selected items?', array(), 'Modules.cmsseo.Admin')));
		$this->context = Context::getContext();


		$this->setTemplate('backupform.tpl');


		parent::__construct();

		













/*
colorcar esto en algun metodo ? que hacer ?
$tpl_path = _PS_MODULE_DIR_ .'belvg_searchpro/views/templates/admin/features.tpl';
$data = $this->context->smarty->createTemplate($tpl_path, $this->context->smarty);
*/






		
	}


	public function display() {
		parent::display();
		// echo "<h1> This is a test of function display()</h1>";
    }

	public function renderForm()
	{

		// En principio este controlador no va a tener renderform

		return parent::renderForm();
	}


	public function postProcess()
	{
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
            // not sure what to do here for now
		}
	}

}