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
        $this->description = "This trigger creates transactions for each grant in each loan";
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
        $error = 0;
        global $conf;
        dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);
        require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingtransaction.class.php");
        require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontabankmatch.class.php");
        require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingtransaction.class.php");
        require_once(DOL_DOCUMENT_ROOT . "/moreprestamos/class/iconloan.class.php");
        $objtrans = new Icontaaccountingtransaction($this->db);
        $fiscalyearid = $conf->global->ICONTA_CURRENT_FISCALPERIOD_ID;
        $prestamo = new Iconloan($this->db);
        $prestamo->fetch($object->fk_idloan);
        $prestamo->info();

        if ($action == 'ICONLOANGRANT_DELETE') {
 
            if(!empty($object->fk_accountingtransaction)) {
                $r = $objtrans->fetch($object->fk_accountingtransaction);
                if ($r < 0) {
                    $this->error = $objtrans->error;
                    return -1;
                }
                if ($objtrans->status > 0) {
                    $this->error = 'Error: no se puede eliminar esta línea ya que el asiento relacionado ya está posteado en la contabilidad';
                    return -1;
                }
                // borrar registro en la tabla de amarre
                $sql = "DELETE FROM " . MAIN_DB_PREFIX . "iconta_banktransaction ";
                $sql.=" WHERE id_accountingtrans=" . $object->fk_accountingtransaction;
                $sql.=" AND id_bank=" . $object->fk_bank;

                $resdelete = $this->db->query($sql);
                if (!$resdelete) {
                    $this->error = $this->db->error;
                    return(-1);
                }

                $r = $objtrans->delete($user);
                if (!$r) {
                    $this->error = $objtrans->error;
                    return(-1);
                }
            } //empty accountingid
        } 
        
        else if ($action == 'ICONLOANGRANT_CREATE') {
            $error = 0;
            dol_syslog("Trigger '" . $this->name . "' for action '$action' launched by " . __FILE__ . ". id=" . $object->id);
            $objtrans = new Icontaaccountingtransaction($this->db);
            //  $objbankmatch = new Icontabankmatch($this->db);
            $prestamo = new Iconloan($this->db);
            $prestamo->fetch($object->fk_idloan);
            $prestamo->info();

            // verificar el bankmatch
            $sql = "SELECT
                            fk_id_bank_account,
                            fk_id_account
                       FROM
                            " . MAIN_DB_PREFIX . "iconta_bankmatch,
                            " . MAIN_DB_PREFIX . "bank_account,
                            " . MAIN_DB_PREFIX . "iconta_chart_master
                       WHERE
                            `fk_id_bank_account` = " . MAIN_DB_PREFIX . "bank_account.rowid
                        AND `fk_id_account` = " . MAIN_DB_PREFIX . "iconta_chart_master.rowid";
            $sql.= " AND fk_id_bank_account = " . $object->fk_cashaccount;
            $resql = $this->db->query($sql);
            if ($this->db->num_rows($resql) < 1) {
                $this->error = "No hay bankmatch para la cuenta de banco con id" . $object->fk_cashaccount;
                $error++;
                return(-1);
            } else {
                $obj = $this->db->fetch_object($resql);
                $bankmatch = $obj->fk_id_account;
                $this->db->free($resql);
            }

            if ($this->error != 0) {
                die($this->error);
            } else {

                $arraytranslines = array();
                $objtrans->number = $objtrans->getNextValue();
                $objtrans->entity = $conf->entity;
                $objtrans->sourcetype = 'LoanExpenditure';
                $objtrans->fk_source = $object->id;
                $objtrans->label = "Desembolso a prestamo:" . $prestamo->loan_number . " A favor de:" . $prestamo->societe->name; //@porhacer:agregar la referencia del numero de cheque tambien en bancos
                $objtrans->datec = $object->date_granted;
                $objtrans->fk_author = $user->id;
                $objtrans->status = 0;
                $objtrans->fk_fiscal_year = $fiscalyearid;
                $res = $objtrans->create($user, 1);
                if ($res < 1) {
                    $this->error = $objtrans->error;
                    return(-1);
                } else {
                    $arraytranslines = array();
                    $arraytranslines[] = array("accountid" => $conf->global->ICONLOAN_RECEIVABLE_LOANS_ACCOUNT,
                        "valor" => $object->amount_granted,
                        "side" => '+');
                    $arraytranslines[] = array("accountid" => $bankmatch,
                        "valor" => $object->amount_granted,
                        "side" => '-');
                    $res1 = $objtrans->addLines($res, $arraytranslines);
                    if ($res1 < 0) {
                        $this->error = $objtrans->error;
                        return(-1);
                        exit(0);
                    } else {
                        // actualizar la linea del grant con la transaccion recien creada
                        $sql = "UPDATE " . MAIN_DB_PREFIX . "icon_loan_grant";
                        $sql.=" SET fk_accountingtransaction=" . $res;
                        $sql.=" WHERE rowid=" . $object->id;
                        $resupdate = $this->db->query($sql);
                        if (!$resupdate) {
                            die($sql);
                            $this->error = $this->db->error;
                            return(-1);
                        }
                    }
                }
            }
        } //end if $action
        else if ($action == 'ICONLOAN_MODIFY') {
// se ejecuta al actualizar el status
            $prestamo = $object;
            if ($object->status == 3) {
                $sql = "SELECT
                            count(rowid) as num from " . MAIN_DB_PREFIX . "iconta_accountingtransaction 
                            where sourcetype = 'LoanInterestProvision'";
                $sql.= " AND fk_source = " . $prestamo->id;
                $sql.= " AND status > 0 ";
                $resql = $this->db->query($sql);
                if ($resql < 1) {
                    $this->error = $this->db->error;
                    return(-1);
                } else {
                    $obj = $this->db->fetch_object($resql);
                    if ($obj->num > 0) {
                        $this->error = "Existe un registro de provision asociado a este prestamo que ya ha sido posteado, consulte con el administrador de sistema";
                        return(-1);
                    }
                }

                $sql = "DELETE FROM " . MAIN_DB_PREFIX . "iconta_accountingtransaction 
                            where sourcetype = 'LoanInterestProvision'";
                $sql.= " AND fk_source = " . $prestamo->id;

                $resql = $this->db->query($sql);
                if ($resql < 1) {
                    $this->error = $this->db->error;
                    return(-1);
                }
            } else if ($object->status == 4) {
                $sql = "DELETE FROM " . MAIN_DB_PREFIX . "iconta_accountingtransaction 
                            where sourcetype = 'LoanInterestProvision'";
                $sql.= " AND fk_source = " . $prestamo->id;

                $resql = $this->db->query($sql);
                if ($resql < 1) {
                    $this->error = $this->db->error;
                    return(-1);
                }
                $objtrans1 = new Icontaaccountingtransaction($this->db);
                $arraytranslines = array();
                $objtrans1->number = $objtrans->getNextValue();
                $objtrans1->entity = $conf->entity;
                $objtrans1->sourcetype = 'LoanInterestProvision';
                $objtrans1->fk_source = $object->id;
                $objtrans1->label = "Provision de intereses a prestamo:" . $prestamo->loan_number . " A favor de:" . $prestamo->societe->name; //@porhacer:agregar la referencia del numero de cheque tambien en bancos
                $objtrans1->datec = $prestamo->loaneffectivedate;
                $objtrans1->fk_author = $user->id;
                $objtrans1->status = 0;
                $objtrans1->fk_fiscal_year = $fiscalyearid;
                $res = $objtrans1->create($user, 1);
                if ($res < 1) {
                    $this->error = $objtrans1->error;
                    return(-1);
                } else {
                    $arraytranslines = array();
                    $arraytranslines[] = array("accountid" => $conf->global->ICONLOAN_RECEIVABLE_INTEREST_ACCOUNT,
                        "valor" => $prestamo->getTotalInterests(),
                        "side" => '+');
                    $arraytranslines[] = array("accountid" => $conf->global->ICONLOAN_DEFERRED_INTEREST_ACCOUNT,
                        "valor" => $prestamo->getTotalInterests(),
                        "side" => '-');
                    $res1 = $objtrans1->addLines($res, $arraytranslines);
                    if ($res1 < 0) {

                        $this->error = $objtrans->error;
                        return(-1);
                        exit(0);
                    }
                }
            }
//        // marcar el banktransaction metiendola en bank trans de forma que no se pueda borrar la trans bancaria
        }
////
    }

//end function
}

// end class
?>
