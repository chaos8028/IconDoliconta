<?php

/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis@dolibarr.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   iconconta     Module iconconta
 *  \brief      Example of a module descriptor.
 * 				Such a file must be copied into htdocs/iconconta/core/modules directory.
 *  \file       htdocs/iconconta/core/modules/modiconconta.class.php
 *  \ingroup    iconconta
 *  \brief      Description and activation file for module iconconta
 */
include_once(DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php");

/**
 *  Description and activation class for module iconconta
 */
class modiconconta extends DolibarrModules {

    /**
     *   Constructor. Define names, constants, directories, boxes, permissions
     *
     *   @param      DoliDB		$db      Database handler
     */
    function modiconconta($db) {
        global $langs, $conf;

        $this->db = $db;

        // Id for module (must be unique).
        // Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
        $this->numero = 810;
        // Key text used to identify module (for permissions, menus, etc...)
        $this->rights_class = 'iconconta';

        // Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
        // It is used to group modules in module setup page
        $this->family = "Icon Modules";
        // Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        // Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
        $this->description = "Mini Modulo de Contabilidad Genérica";
        // Possible values for version are: 'development', 'experimental', 'dolibarr' or version
        $this->version = '1.0';
        // Key used in llx_const table to save module status enabled/disabled (where iconconta is value of property name of module in uppercase)
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        // Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
        $this->special = 0;
        // Name of image file used for this module.
        // If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
        // If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
        $this->picto = 'generic';

        // Defined all module parts (triggers, login, substitutions, menus, css, etc...)
        // for default path (eg: /iconconta/core/xxxxx) (0=disable, 1=enable)
        // for specific path of parts (eg: /iconconta/core/modules/barcode)
        // for specific css file (eg: /iconconta/css/iconconta.css.php)
          $this->module_parts = array('triggers' => 1);
        // Set this to 1 if module has its own trigger directory
        //							'login' => 0,                                    // Set this to 1 if module has its own login method directory
        //							'substitutions' => 0,                            // Set this to 1 if module has its own substitution function file
        //							'menus' => 0,                                    // Set this to 1 if module has its own menus handler directory
        //							'barcode' => 0,                                  // Set this to 1 if module has its own barcode directory
        //							'models' => 0,                                   // Set this to 1 if module has its own models directory
        //							'css' => '/iconconta/css/iconconta.css.php',       // Set this to relative path of css if module has its own css file
        //							'hooks' => array('hookcontext1','hookcontext2')  // Set here all hooks context managed by module
        //							'workflow' => array('order' => array('WORKFLOW_ORDER_AUTOCREATE_INVOICE')) // Set here all workflow context managed by module
        //                        );
        //	$this->module_parts = array('hooks' => array('ordercard','invoicecard') );
        // Data directories to create when module is enabled.
        // Example: this->dirs = array("/iconconta/temp");
        $this->dirs = array();

        // Config pages. Put here list of php page, stored into iconconta/admin directory, to use to setup module.
        $this->config_page_url = array("mysetuppage.php@iconconta");

        // Dependencies
        $this->depends = array();  // List of modules id that must be enabled if this module is enabled
        $this->requiredby = array(); // List of modules id to disable if this one is disabled
        $this->phpmin = array(5, 0);     // Minimum version of PHP required by module
        $this->need_dolibarr_version = array(3, 0); // Minimum version of Dolibarr required by module
        $this->langfiles = array("langfiles@iconconta");

        // Constants
        // List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
        // Example: $this->const=array(0=>array('iconconta_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
        //                             1=>array('iconconta_MYNEWCONST2','chaine','myvalue','This is another constant to add',0)
        // );
        $this->const = array();

        // Array to add new pages in new tabs
        // Example: $this->tabs = array('objecttype:+tabname1:Title1:langfile@iconconta:$user->rights->iconconta->read:/iconconta/mynewtab1.php?id=__ID__',  // To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:langfile@iconconta:$user->rights->othermodule->read:/iconconta/mynewtab2.php?id=__ID__',  // To add another new tab identified by code tabname2
        //                              'objecttype:-tabname');                                                     // To remove an existing tab identified by code tabname
        // where objecttype can be
        // 'thirdparty'       to add a tab in third party view
        // 'intervention'     to add a tab in intervention view
        // 'order_supplier'   to add a tab in supplier order view
        // 'invoice_supplier' to add a tab in supplier invoice view
        // 'invoice'          to add a tab in customer invoice view
        // 'order'            to add a tab in customer order view
        // 'product'          to add a tab in product view
        // 'stock'            to add a tab in stock view
        // 'propal'           to add a tab in propal view
        // 'member'           to add a tab in fundation member view
        // 'contract'         to add a tab in contract view
        // 'user'             to add a tab in user view
        // 'group'            to add a tab in group view
        // 'contact'          to add a tab in contact view
        // 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
        $this->tabs = array();

        // Dictionnaries
        $this->dictionnaries = array();
        /* Example:
          if (! isset($conf->iconconta->enabled)) $conf->iconconta->enabled=0;	// This is to avoid warnings
          $this->dictionnaries=array(
          'langs'=>'iconconta@iconconta',
          'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
          'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
          'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
          'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
          'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionnary)
          'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
          'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
          'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
          'tabcond'=>array($conf->iconconta->enabled,$conf->iconconta->enabled,$conf->iconconta->enabled)												// Condition to show each dictionnary
          );
         */

        // Boxes
        // Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();   // List of boxes
        $r = 0;
        // Example:
        /*
          $this->boxes[$r][1] = "myboxa.php";
          $r++;
          $this->boxes[$r][1] = "myboxb.php";
          $r++;
         */

        // Permissions
        $this->rights = array();  // Permission array used by this module
        $r = 0;

        // Add here list of permission defined by an id, a label, a boolean and two constant strings.
        // Example:
        $this->rights[$r][0] = 8000; 				// Permission id (must not be already used)
        $this->rights[$r][1] = 'Permision label';	// Permission label
        $this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
        $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
        $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
        $r++;
        // Main menu entries
        $this->menus = array();   // List of menus to add
        $r = 0;

        // Add here entries to declare new menus
        //
		// Example to declare a new Top Menu entry and its Left menu entry:
        $this->menu[$r] = array('fk_menu' => 0, // Put 0 if this is a top menu
            'type' => 'top', // This is a Top menu entry
            'titre' => 'Contabilidad',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconconta',
            'url' => '/iconconta/index.php',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', // Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);                    // 0=Menu for internal users, 1=external users, 2=both
        $r++;




        $this->menu[$r] = array('fk_menu' => 'r=0', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Cuentas Contables',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '#',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);                    // 0=Menu for internal users, 1=external users, 2=both
        $r++;

        $this->menu[$r] = array('fk_menu' => 'r=0', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Transacciones',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/transactionsdashboard.php',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);                    // 0=Menu for internal users, 1=external users, 2=both
        $r++;

        $this->menu[$r] = array('fk_menu' => 'r=0', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Prestamos',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/loansdashboard.php',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);                    // 0=Menu for internal users, 1=external users, 2=both
        $r++;

        $this->menu[$r] = array('fk_menu' => 'r=0', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Setup',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/admin/mysetuppage.php',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);                    // 0=Menu for internal users, 1=external users, 2=both
        $r++;

        // Example to declare a Left Menu entry into an existing Top menu entry:
//		$this->menu[$r]=array(	'fk_menu'=>'r=1',	// Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
//									'type'=>'left',			                // This is a Left menu entry
//									'titre'=>'AccountClasses',
//									'mainmenu'=>'iconconta',
//									'leftmenu'=>'iconconta',
//									'url'=>'/iconconta/admin/accountclass.php',
//									'langs'=>'mylangfile',	                // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
//									'position'=>100,
//									'enabled'=>'1',  // Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
//									'perms'=>'1',			                // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
//									'target'=>'',
//									'user'=>0);				                // 0=Menu for internal users, 1=external users, 2=both
//		 $r++;
        $this->menu[$r] = array('fk_menu' => 'r=1', // Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Arbol de cuentas',
            'mainmenu' => '0',
            'leftmenu' => 'iconconta',
            'url' => '/iconconta/chartofaccounts.php',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', // Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);                    // 0=Menu for internal users, 1=external users, 2=both
        $r++;
             
            $this->menu[$r] = array('fk_menu' => 'r=1', // Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Nueva cuenta',
            'mainmenu' => '0',
            'leftmenu' => 'iconconta',
            'url' => '/iconconta/accountmaster.php?action=create',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', // Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);                    // 0=Menu for internal users, 1=external users, 2=both
        $r++;
        
        
        $this->menu[$r] = array('fk_menu' => 'r=1', // Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Nuevo Grupo',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconconta',
            'url' => '/iconconta/accountgroup.php?action=create',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', // Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);                    // 0=Menu for internal users, 1=external users, 2=both
        $r++;


                $this->menu[$r] = array('fk_menu' => 'r=2', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Apertura del ejercicio Fiscal',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/openingbalance.php?action=create',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);
        $r++;
        
        $this->menu[$r] = array('fk_menu' => 'r=2', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Nuevo Asiento',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/transaction.php?action=create',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0);
        $r++;
        $this->menu[$r] = array('fk_menu' => 'r=2', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Listado de Asientos',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/transactionslist.php',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0); // 0=Menu for internal users, 1=external users, 2=both
        $r++;

        $this->menu[$r] = array('fk_menu' => 'r=3', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Listado de Prestamos Aprobados',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/approvedloanslist.php',
            'langs' => 'mylangfile', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0); // 0=Menu for internal users, 1=external users, 2=both
        $r++;
         $this->menu[$r] = array('fk_menu' => 'r=3', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Pagos Pendientes de contabilizar',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/loanspaymentlist.php',
            'langs' => 'iconconta', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0); // 0=Menu for internal users, 1=external users, 2=both
        $r++;
             $this->menu[$r] = array('fk_menu' => 'r=3', // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type' => 'left', // This is a Left menu entry
            'titre' => 'Contabilizar Intereses devengados',
            'mainmenu' => 'iconconta',
            'leftmenu' => 'iconcontasetup',
            'url' => '/iconconta/earnedinterest.php',
            'langs' => 'iconconta', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position' => 100,
            'enabled' => '1', //'$conf->iconconta->enabled',	// Define condition to show or hide menu entry. Use '$conf->iconconta->enabled' if entry must be visible if module is enabled.
            'perms' => '1', // Use 'perms'=>'$user->rights->iconconta->level1->level2' if you want your menu with a permission rules
            'target' => '',
            'user' => 0); // 0=Menu for internal users, 1=external users, 2=both
        $r++;
        // Exports
        $r = 1;

        // Example:
        // $this->export_code[$r]=$this->rights_class.'_'.$r;
        // $this->export_label[$r]='CustomersInvoicesAndInvoiceLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
        // $this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
        // $this->export_permission[$r]=array(array("facture","facture","export"));
        // $this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.cp'=>'Zip','s.ville'=>'Town','s.fk_pays'=>'Country','s.tel'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4','s.code_compta'=>'CustomerAccountancyCode','s.code_compta_fournisseur'=>'SupplierAccountancyCode','f.rowid'=>"InvoiceId",'f.facnumber'=>"InvoiceRef",'f.datec'=>"InvoiceDateCreation",'f.datef'=>"DateInvoice",'f.total'=>"TotalHT",'f.total_ttc'=>"TotalTTC",'f.tva'=>"TotalVAT",'f.paye'=>"InvoicePaid",'f.fk_statut'=>'InvoiceStatus','f.note'=>"InvoiceNote",'fd.rowid'=>'LineId','fd.description'=>"LineDescription",'fd.price'=>"LineUnitPrice",'fd.tva_tx'=>"LineVATRate",'fd.qty'=>"LineQty",'fd.total_ht'=>"LineTotalHT",'fd.total_tva'=>"LineTotalTVA",'fd.total_ttc'=>"LineTotalTTC",'fd.date_start'=>"DateStart",'fd.date_end'=>"DateEnd",'fd.fk_product'=>'ProductId','p.ref'=>'ProductRef');
        // $this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.cp'=>'company','s.ville'=>'company','s.fk_pays'=>'company','s.tel'=>'company','s.siren'=>'company','s.siret'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.code_compta'=>'company','s.code_compta_fournisseur'=>'company','f.rowid'=>"invoice",'f.facnumber'=>"invoice",'f.datec'=>"invoice",'f.datef'=>"invoice",'f.total'=>"invoice",'f.total_ttc'=>"invoice",'f.tva'=>"invoice",'f.paye'=>"invoice",'f.fk_statut'=>'invoice','f.note'=>"invoice",'fd.rowid'=>'invoice_line','fd.description'=>"invoice_line",'fd.price'=>"invoice_line",'fd.total_ht'=>"invoice_line",'fd.total_tva'=>"invoice_line",'fd.total_ttc'=>"invoice_line",'fd.tva_tx'=>"invoice_line",'fd.qty'=>"invoice_line",'fd.date_start'=>"invoice_line",'fd.date_end'=>"invoice_line",'fd.fk_product'=>'product','p.ref'=>'product');
        // $this->export_sql_start[$r]='SELECT DISTINCT ';
        // $this->export_sql_end[$r]  =' FROM ('.MAIN_DB_PREFIX.'facture as f, '.MAIN_DB_PREFIX.'facturedet as fd, '.MAIN_DB_PREFIX.'societe as s)';
        // $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product as p on (fd.fk_product = p.rowid)';
        // $this->export_sql_end[$r] .=' WHERE f.fk_soc = s.rowid AND f.rowid = fd.fk_facture';
        // $r++;
    }

    /**
     * 		Function called when module is enabled.
     * 		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     * 		It also creates data directories
     *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
     *      @return     int             	1 if OK, 0 if KO
     */
    function init($options = '') {
        $sql = array();

        $result = $this->load_tables();

        return $this->_init($sql, $options);
    }

    /**
     * 		Function called when module is disabled.
     *      Remove from database constants, boxes and permissions from Dolibarr database.
     * 		Data directories are not deleted
     *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
     *      @return     int             	1 if OK, 0 if KO
     */
    function remove($options = '') {
        $sql = array();

        return $this->_remove($sql, $options);
    }

    /**
     * 		Create tables, keys and data required by module
     * 		Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
     * 		and create data commands must be stored in directory /Eyc/sql/
     * 		This function is called by this->init
     *
     * 		@return		int		<=0 if KO, >0 if OK
     */
    function load_tables() {
        return $this->_load_tables('/iconconta/sql/');
    }

}

?>
