<?php

/* Copyright (C) 2005-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis@dolibarr.fr>
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
 *      \file       htdocs/core/triggers/interface_all_Demo.class.php
 *      \ingroup    core
 *      \brief      Fichier de demo de personalisation des actions du workflow
 *      \remarks    Son propre fichier d'actions peut etre cree par recopie de celui-ci:
 *                  - Le nom du fichier doit etre: interface_modMymodule_Mytrigger.class.php
 * 					                           ou: interface_all_Mytrigger.class.php
 *                  - Le fichier doit rester stocke dans core/triggers
 *                  - Le nom de la classe doit etre InterfaceMytrigger
 *                  - Le nom de la methode constructeur doit etre InterfaceMytrigger
 *                  - Le nom de la propriete name doit etre Mytrigger
 */

/**
 *      \class      InterfaceDemo
 *      \brief      Class of triggers for demo module
 */
class InterfaceGrantloan {

    var $db;

    /**
     *   Constructor.
     *   @param      DB      Database handler
     */
    function InterfaceGrantloan($DB) {
        $this->db = $DB;

        $this->name = preg_replace('/^Interface/i', '', get_class($this));
        $this->family = "iconconta";
        $this->description = "This trigger creates special accounts in iconconta for each approved loan when loand module is active";
        $this->version = '3.2';            // 'development', 'experimental', 'dolibarr' or version
        $this->picto = 'technic';
    }

    /**
     *   Return name of trigger file
     *   @return     string      Name of trigger file
     */
    function getName() {
        return $this->name;
    }

    /**
     *   Return description of trigger file
     *   @return     string      Description of trigger file
     */
    function getDesc() {
        return $this->description;
    }

    /**
     *   Return version of trigger file
     *   @return     string      Version of trigger file
     */
    function getVersion() {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'development')
            return $langs->trans("Development");
        elseif ($this->version == 'experimental')
            return $langs->trans("Experimental");
        elseif ($this->version == 'dolibarr')
            return DOL_VERSION;
        elseif ($this->version)
            return $this->version;
        else
            return $langs->trans("Unknown");
    }

    /**
     *      Function called when a Dolibarrr business event is done.
     *      All functions "run_trigger" are triggered if file is inside directory htdocs/core/triggers
     *      @param      action      Code de l'evenement
     *      @param      object      Objet concerne
     *      @param      user        Objet user
     *      @param      langs       Objet langs
     *      @param      conf        Objet conf
     *      @return     int         <0 if KO, 0 if no triggered ran, >0 if OK
     */
    function run_trigger($action, $object, $user, $langs, $conf) {
        // Put here code you want to execute when a Dolibarr business events occurs.
        // Data and type of action are stored into $object and $action
        // Users

        if ($action == 'ICONLOAN_MODIFY') {
       
            global $conf;
            $fiscalyearid = $conf->global->ICONTA_CURRENT_FISCALPERIOD_ID;
// at this point iconloans module should be enabled
            if ($object->status == 3) {// if approving loan
                dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);
                require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingtransaction.class.php");
                require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontabankmatch.class.php");
                $object->info();
                $objtrans = new Icontaaccountingtransaction($this->db);
                $objbankmatch = new Icontabankmatch($this->db);

                $grantdetailarray = $object->fetchGrantdetail($object->id); //SELECT ALL GRANTS AND PUT THEM IN AN ARRAY()   
                //@porhacer: verificar que exista un conjunto de grants de almenos uno sin no continuar
                // let's verify that each bank-account has a match in accounting
                $bankmatch = array();
                foreach ($grantdetailarray as $k) {
                    $sql ="SELECT
     fk_id_bank_account,
     fk_id_account
FROM
     " . MAIN_DB_PREFIX . "iconta_bankmatch,
     " . MAIN_DB_PREFIX . "bank_account,
     " . MAIN_DB_PREFIX . "iconta_chart_master
WHERE
     `fk_id_bank_account` = " . MAIN_DB_PREFIX . "bank_account.rowid
 AND `fk_id_account` = " . MAIN_DB_PREFIX . "iconta_chart_master.rowid";
                    $sql.= " AND fk_id_bank_account = " . $k['fk_cashaccount'];


                    $resql = $this->db->query($sql);
                    if ($resql) {

                        if ($this->db->num_rows($resql) < 1) {
                           
                            $this->error = "No hay bankmatch para la cuenta de banco con id" . $k['fk_cashaccount'];
                            return(-1);
                        
                            
                        } else {

                            $obj = $this->db->fetch_object($resql);
                            $bankmatch[$k['fk_cashaccount']] = $obj->fk_id_account;
                        }
                        $this->db->free($resql);
                    } else {
                        $this->error = $this->db->error;
                          return(-1);

                        
                    }
                }//end for

                if ($this->error != 0) {
                    die($this->error);
                } else {

                    //@porhacer: se deben crear las transacciones para el periodo fiscal en curso o impedir desde grant.php que pasen fechas fuera del periodo fiscal en curso 
                    //$this->db->begin();
                    $arraytranslines = array();
                    $arraytranslines[] = array("accountid" => $conf->global->ICONLOAN_RECEIVABLE_INTEREST_ACCOUNT,
                        "valor" => $object->InitialInterest(),
                        "side" => '+');
                    $arraytranslines[] = array("accountid" => $conf->global->ICONLOAN_DEFERRED_INTEREST_ACCOUNT,
                        "valor" => $object->InitialInterest(),
                        "side" => '-');

                    $objtrans->number = $objtrans->getNextValue();
                    $objtrans->entity = $conf->entity;
                    $objtrans->sourcetype = 'LoanExpenditure';
                    $objtrans->fk_source = $object->id;
                    $objtrans->label = "Desembolso a prestamo:" . $object->loan_number . " A favor de:" . $object->societe->name; //@porhacer:agregar la referencia del numero de cheque tambien en bancos
                    $objtrans->datec = $k['date_granted'];
                    $objtrans->fk_author = $user->id;
                    $objtrans->status = 0;
                    $objtrans->fk_fiscal_year = $fiscalyearid;
                    $res = $objtrans->create($user, 1);
                    if ($res < 1) {
                       $this->error=$objtrans->error;
                        return(-1);
                    
                        
                    }
                        else {
                        foreach ($grantdetailarray as $k) {
                            $arraytranslines[] = array("accountid" => $conf->global->ICONLOAN_RECEIVABLE_LOANS_ACCOUNT,
                                "valor" => $k['amount_granted'],
                                "side" => '+');
                            $arraytranslines[] = array("accountid" => $bankmatch[$k['fk_cashaccount']],
                                "valor" => $k['amount_granted'],
                                "side" => '-');
                        }

//print_r($arraytranslines);
//die('wait');
$res1 = $objtrans->addLines($res,$arraytranslines);
                        if ($res1 < 0) {
                            
                            $this->error = $objtrans->error;
                            return(-1);
                            exit(0);
                        } 

// actualizar la linea del grant con la transaccion recien creada
                        $sql = "UPDATE " . MAIN_DB_PREFIX . "icon_loan_grant";
                        $sql.=" SET fk_accountingtransaction=" . $res;
                        $sql.=" WHERE rowid=" . $k['rowid'];
                        $resupdate = $this->db->query($sql);
                        if (!$resupdate) {
                            die($sql);
                            $this->errors[] = $this->db->errors;
                            return(-1);
                        }

// marcar el banktransaction metiendola en bank trans de forma que no se pueda borrar la trans bancaria

                        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "iconta_banktransaction (id_accountingtrans,id_bank)";
                        $sql.=" VALUES (" . $res . "," . $k['fk_bank'] . ")";
                        $resinsert = $this->db->query($sql);
                        if (!$resinsert) {
                            //  die($sql);
                            $this->errors = $this->db->errors;
                            return(-1);
                        }
                    } 
                }
            }

//create accounts
// 
// 
            // step1 create accounting transaction
        }
        

        return 0;
    }

}// end class

?>
