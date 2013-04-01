<?php

/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *  \file       dev/skeletons/icontaaccountingtransaction.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 * 				Initialy built by build_class_from_table on 2012-08-16 16:53
 */
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingdebcred.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontafiscalyear.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 * 	Put here description of your class
 */
class Icontaaccountingtransaction extends CommonObject {

    var $db;       //!< To store db handler
    var $error;       //!< To return error code (or message)
    var $errors = array();    //!< To return several error codes (or messages)
    //var $element='icontaaccountingtransaction';			//!< Id that identify managed objects
    //var $table_element='icontaaccountingtransaction';	//!< Name of table without prefix where object is stored
    var $id;
    var $entity;
    var $number;
    var $label;
    var $datec = '';
    var $fk_author;
    var $tms = '';
    var $fk_source = 0;
    var $sourcetype = 'Journalentry';
    var $url;
    var $status;
    var $labelstatut = array('Draft', 'ToPost', 'Posted');
    var $child = array();
    var $fk_fiscal_year;

    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db) {
        $this->db = $db;

        return 1;
    }

    function datevalid($date1) {
        global $conf;
        $currentperiod = new Icontafiscalyear($this->db);
        $res = $currentperiod->fetch($conf->global->ICONTA_CURRENT_FISCALPERIOD_ID);
        if (!$res) {
            $this->error = 'Error: Existe un problema con los periodos fiscales' . $currentperiod->error;
            return -1;
        }
        $begin = $currentperiod->begin;
        $end = $currentperiod->end;
        if (!($date1 > $begin && $date1 < $end)) {
            $this->error = "Error: Fecha no válida, esta transaccion no corresponde al período fiscal Activo";
            return -1;
        }
        else
            return 1;
    }

    /**
     *  Create object into database
     *
     *  @param	User	$user        User that create
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger = 0) {
        global $conf, $langs;
        $error = 0;

        // Clean parameters
        if (isset($this->datec))
            $this->datec = trim($this->datec);
        if (isset($this->number))
            $this->number = trim($this->number);
        if (isset($this->label))
            $this->label = trim($this->label);
        if (isset($this->fk_author))
            $this->fk_author = trim($this->fk_author);
        if (isset($this->fk_source))
            $this->fk_source = trim($this->fk_source);
        if (isset($this->sourcetype))
            $this->sourcetype = trim($this->sourcetype);
        if (isset($this->url))
            $this->url = trim($this->url);
        if (isset($this->fk_fiscal_year))
            $this->fk_fiscal_year = trim($this->fk_fiscal_year);

        if ($this->datevalid(($this->datec)) == -1) {
            return -1;
        }
        // Check parameters
        // Put here code to add control on parameters values
        // Insert request
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "iconta_accountingtransaction(";

        $sql.= "label,";
        $sql.= "entity,";
        $sql.= "number,";
        $sql.= "datec,";
        $sql.= "fk_author,";
        $sql.= "fk_source,";
        $sql.= "sourcetype,";
        $sql.= "url,";
        $sql.= "fk_fiscal_year";


        $sql.= ") VALUES (";

        $sql.= " " . (!isset($this->label) ? 'NULL' : "'" . $this->db->escape($this->label) . "'") . ",";
        $sql.= " " . (!isset($this->entity) ? '0' : "'" . $this->entity . "'") . ",";
        $sql.= " " . (!isset($this->number) ? 'NULL' : "'" . $this->db->escape($this->number) . "'") . ",";
        $sql.= " " . (!isset($this->datec) || dol_strlen($this->datec) == 0 ? 'NULL' : $this->db->idate($this->datec)) . ",";
        $sql.= " " . (!isset($this->fk_author) ? "'" . $this->db->escape($user->id) . "'" : "'" . $this->db->escape($user->id) . "'") . ",";
        $sql.= " " . (!isset($this->fk_source) ? 'NULL' : "'" . $this->fk_source . "'") . ",";
        $sql.= " " . (!isset($this->sourcetype) ? 'NULL' : "'" . $this->db->escape($this->sourcetype) . "'") . ",";
        $sql.= " " . (!isset($this->url) ? 'NULL' : "'" . $this->db->escape($this->url) . "'") . ",";
        $sql.= " " . (!isset($this->fk_fiscal_year) ? 'NULL' : "'" . $this->db->escape($this->fk_fiscal_year) . "'") . "";

        $sql.= ")";
        //echo $sql;
        $this->db->begin();

        dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (!$resql) {
            $error++;
            $this->errors[] = "Error " . $this->db->lasterror();
        }

        if (!$error) {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "iconta_accountingtransaction");

            if (!$notrigger) {
                // Uncomment this and change MYOBJECT to your own tag if you
                // want this action call a trigger.
                //// Call triggers
                //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                //$interface=new Interfaces($this->db);
                //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
                //if ($result < 0) { $error++; $this->errors=$interface->errors; }
                //// End call triggers
            }
        }

        // Commit or rollback
        if ($error) {
            foreach ($this->errors as $errmsg) {
                dol_syslog(get_class($this) . "::create " . $errmsg, LOG_ERR);
                $this->error.=($this->error ? ', ' . $errmsg : $errmsg);
            }
            $this->db->rollback();
            return -1 * $error;
        } else {
            $this->db->commit();
            return $this->id;
        }
    }

    /**
     *  Load object in memory from database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id) {
        global $langs;
        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.= " t.number,";
        $sql.= " t.label,";
        $sql.= " t.datec,";
        $sql.= " t.fk_author,";
        $sql.= " t.tms,";
        $sql.= " t.fk_source,";
        $sql.= " t.sourcetype,";
        $sql.= " t.status,";
        $sql.= " t.url,";
        $sql.= " t.fk_fiscal_year";


        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_accountingtransaction as t";
        $sql.= " WHERE t.rowid = " . $id;

        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($this->db->num_rows($resql) > 0) {
                //echo "pedito ".$this->db->num_rows($resql);
                $obj = $this->db->fetch_object($resql);

                $this->id = $obj->rowid;
                $this->number = $obj->number;
                $this->label = $obj->label;
                $this->datec = $this->db->jdate($obj->datec);
                $this->fk_author = $obj->fk_author;
                $this->tms = $this->db->jdate($obj->tms);
                $this->fk_source = $obj->fk_source;
                $this->sourcetype = $obj->sourcetype;
                $this->status = $obj->status;
                $this->url = $obj->url;
                $this->fk_fiscal_year = $obj->fk_fiscal_year;
                $this->db->free($resql);
                return 1;
            } else {
                return 0;
            }
        } else {
            $this->error = "Error " . $this->db->lasterror();
            dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     *  Update object into database
     *
     *  @param	User	$user        User that modify
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user = 0, $notrigger = 0) {
        global $conf, $langs;
        $error = 0;

        // Clean parameters
        if (isset($this->number))
            $this->number = trim($this->number);
        if (isset($this->label))
            $this->label = trim($this->label);
        if (isset($this->fk_author))
            $this->fk_author = trim($this->fk_author);
        if (isset($this->fk_source))
            $this->fk_source = trim($this->fk_source);
        if (isset($this->sourcetype))
            $this->sourcetype = trim($this->sourcetype);
        if (isset($this->url))
            $this->url = trim($this->url);
        if (isset($this->fk_fiscal_year))
            $this->fk_fiscal_year = trim($this->fk_fiscal_year);


        // Check parameters
        // Put here code to add control on parameters values
        // Update request
        $sql = "UPDATE " . MAIN_DB_PREFIX . "iconta_accountingtransaction SET";

        $sql.= " label=" . (isset($this->label) ? "'" . $this->db->escape($this->label) . "'" : "null") . ",";
        $sql.= " datec=" . (dol_strlen($this->datec) != 0 ? "'" . $this->db->idate($this->datec) . "'" : 'null') . ",";
        $sql.= " fk_author=" . (isset($this->fk_author) ? "'" . $this->db->escape($this->fk_author) . "'" : "null") . ",";
        $sql.= " tms=" . (dol_strlen($this->tms) != 0 ? "'" . $this->db->idate($this->tms) . "'" : 'null') . ",";
        $sql.= " fk_source=" . (isset($this->fk_source) ? $this->fk_source : "null") . ",";
        $sql.= " sourcetype=" . (isset($this->sourcetype) ? "'" . $this->db->escape($this->sourcetype) . "'" : "null") . ",";
        $sql.= " url=" . (isset($this->url) ? "'" . $this->db->escape($this->url) . "'" : "null") . ",";
        $sql.= " fk_fiscal_year=" . (isset($this->fk_fiscal_year) ? "'" . $this->db->escape($this->fk_fiscal_year) . "'" : "null") . "";


        $sql.= " WHERE rowid=" . $this->id;

        $this->db->begin();

        dol_syslog(get_class($this) . "::update sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (!$resql) {
            $error++;
            $this->errors[] = "Error " . $this->db->lasterror();
        }

        if (!$error) {
            if (!$notrigger) {
                // Uncomment this and change MYOBJECT to your own tag if you
                // want this action call a trigger.
                //// Call triggers
                //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                //$interface=new Interfaces($this->db);
                //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
                //if ($result < 0) { $error++; $this->errors=$interface->errors; }
                //// End call triggers
            }
        }

        // Commit or rollback
        if ($error) {
            foreach ($this->errors as $errmsg) {
                dol_syslog(get_class($this) . "::update " . $errmsg, LOG_ERR);
                $this->error.=($this->error ? ', ' . $errmsg : $errmsg);
            }
            $this->db->rollback();
            return -1 * $error;
        } else {
            $this->db->commit();
            return 1;
        }
    }

    /**
     *  Delete object in database
     *
     * 	@param  User	$user        User that delete
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return	int					 <0 if KO, >0 if OK
     */
    function delete($user, $notrigger = 0) {
        global $conf, $langs;
        $error = 0;

        $this->db->begin();

        if (!$error) {
            if (!$notrigger) {
                // Uncomment this and change MYOBJECT to your own tag if you
                // want this action call a trigger.
                //// Call triggers
                //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
                //$interface=new Interfaces($this->db);
                //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
                //if ($result < 0) { $error++; $this->errors=$interface->errors; }
                //// End call triggers
            }
        }

        if (!$error) {
            $sql = "DELETE FROM " . MAIN_DB_PREFIX . "iconta_accountingdebcred";
            $sql.= " WHERE fk_transaction=" . $this->id . "; ";
            $resql1 = $this->db->query($sql);
            dol_syslog(get_class($this) . "::delete sql=" . $sql);

            $sql = "DELETE FROM " . MAIN_DB_PREFIX . "iconta_accountingtransaction";
            $sql.= " WHERE rowid=" . $this->id;
            dol_syslog(get_class($this) . "::delete sql=" . $sql);
            $resql = $this->db->query($sql);

            if (!$resql || !$resql1) {
                $error++;
                $this->errors[] = "Error " . $this->db->lasterror();
            }
        }

        // Commit or rollback
        if ($error) {
            foreach ($this->errors as $errmsg) {
                dol_syslog(get_class($this) . "::delete " . $errmsg, LOG_ERR);
                $this->error.=($this->error ? ', ' . $errmsg : $errmsg);
            }
            $this->db->rollback();
            return -1 * $error;
        } else {
            $this->db->commit();
            return 1;
        }
    }

    /**
     * 	Load an object from its id and create a new one in database
     *
     * 	@param	int		$fromid     Id of object to clone
     * 	@return	int					New id of clone
     */
    function createFromClone($fromid) {
        global $user, $langs;

        $error = 0;

        $object = new Icontaaccountingtransaction($this->db);

        $this->db->begin();

        // Load source object
        $object->fetch($fromid);
        $object->id = 0;
        $object->statut = 0;

        // Clear fields
        // ...
        // Create clone
        $result = $object->create($user);

        // Other options
        if ($result < 0) {
            $this->error = $object->error;
            $error++;
        }

        if (!$error) {
            
        }

        // End
        if (!$error) {
            $this->db->commit();
            return $object->id;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
     * 	Initialise object with example values
     * 	Id must be 0 if object instance is a specimen
     *
     * 	@return	void
     */
    function initAsSpecimen() {
        $this->id = 0;

        $this->label = '';
        $this->datec = '';
        $this->fk_author = '';
        $this->tms = '';
        $this->fk_source = '';
        $this->sourcetype = '';
        $this->url = '';
    }

    function getNextValue() {
        global $db, $conf;

        require_once(DOL_DOCUMENT_ROOT . "/core/lib/functions2.lib.php");

        // We get cursor rule
        $mask = $conf->global->ICONCONTA_TRANS_MASK;

        if (!$mask) {
            $this->error = 'NotConfigured';
            return 0;
        }

        $numFinal = get_next_value($db, $mask, 'iconta_accountingtransaction', 'number', '', '', $this->datec);

        return $numFinal;
    }

    /* This function obtains and stores in $this->child every child record in debcred table 
     * $this->id must be set     
     * returns -1 if query could not be done
     *          0 if query has 0 records
     *          1 if everything is OK
     */

    function getChildEntries() {
        if (empty($this->id)) {
            $this->error = "id is not set, cannot get child records";
            return -1;
        }
        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.= " t.fk_transaction,";
        $sql.= " t.fk_accountid,";
        $sql.= " t.amount,";
        $sql.= " t.direction,";
        $sql.= " c.account_code,";
        $sql.= " c.account_code2,";
        $sql.= " c.account_name";

        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_accountingdebcred as t";
        $sql.= " ," . MAIN_DB_PREFIX . "iconta_chart_master as c ";

        $sql.= " WHERE t.fk_transaction = " . $this->id;
        $sql.= " and t.fk_accountid = c.rowid ";
        $sql.= " ORDER BY t.direction ";
        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            $numrows = $this->db->num_rows($resql);
            if ($numrows > 0) {
                for ($i = 0; $i < $numrows; $i++) {
                    $this->child[] = $this->db->fetch_array($resql);
                }
                return 1;
            } else {
                return 0;
            }
        } else {
            $this->error = "Error " . $this->db->lasterror();
            dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
            return -1;
        }
    }

    function LibStatut($statut, $mode = 1) {
        global $langs;
        $langs->load("iconconta");
//
//        if ($mode == 0)
//        {
//            return $this->labelstatut[$statut];
//        }
//        if ($mode == 1)
//        {
//            return $this->labelstatut_short[$statut];
//        }
//        if ($mode == 2)
//        {
//            if ($statut==0) return img_picto($langs->trans('IcontaStatusDraftShort'),'statut0').' '.$this->labelstatut_short[$statut];
//            if ($statut==1) return img_picto($langs->trans('PropalStatusOpenedShort'),'statut1').' '.$this->labelstatut_short[$statut];
//            if ($statut==2) return img_picto($langs->trans('PropalStatusSignedShort'),'statut3').' '.$this->labelstatut_short[$statut];
//            if ($statut==3) return img_picto($langs->trans('PropalStatusNotSignedShort'),'statut5').' '.$this->labelstatut_short[$statut];
//            if ($statut==4) return img_picto($langs->trans('PropalStatusBilledShort'),'statut6').' '.$this->labelstatut_short[$statut];
//        }
        if ($mode == 3) {
            if ($statut == 0)
                return img_picto($langs->trans('IcontaStatusDraft'), 'statut0');
            if ($statut == 1)
                return img_picto($langs->trans('IcontaStatusReadytopost'), 'statut1');
            if ($statut == 2)
                return img_picto($langs->trans('IcontaStatusPosted'), 'statut3');
        }
//        if ($mode == 4)
//        {
//            if ($statut==0) return img_picto($langs->trans('PropalStatusDraft'),'statut0').' '.$this->labelstatut[$statut];
//            if ($statut==1) return img_picto($langs->trans('PropalStatusOpened'),'statut1').' '.$this->labelstatut[$statut];
//            if ($statut==2) return img_picto($langs->trans('PropalStatusSigned'),'statut3').' '.$this->labelstatut[$statut];
//            if ($statut==3) return img_picto($langs->trans('PropalStatusNotSigned'),'statut5').' '.$this->labelstatut[$statut];
//            if ($statut==4) return img_picto($langs->trans('PropalStatusBilled'),'statut6').' '.$this->labelstatut[$statut];
//        }
//        if ($mode == 5)
//        {
//            if ($statut==0) return $this->labelstatut_short[$statut].' '.img_picto($langs->trans('PropalStatusDraftShort'),'statut0');
//            if ($statut==1) return $this->labelstatut_short[$statut].' '.img_picto($langs->trans('PropalStatusOpenedShort'),'statut1');
//            if ($statut==2) return $this->labelstatut_short[$statut].' '.img_picto($langs->trans('PropalStatusSignedShort'),'statut3');
//            if ($statut==3) return $this->labelstatut_short[$statut].' '.img_picto($langs->trans('PropalStatusNotSignedShort'),'statut5');
//            if ($statut==4) return $this->labelstatut_short[$statut].' '.img_picto($langs->trans('PropalStatusBilledShort'),'statut6');
//        }
    }

    // this function adds a line to current object      
    function addLine($accountid, $valor, $side) {
        $objline = new Icontaaccountingdebcred($this->db);
        $objline->fk_accountid = $accountid; //@porhacer: agregar constraint en base de datos accountid debe estar en la tabla de cuentas
        $objline->amount = $valor;
        $objline->direction = $side;
        $objline->fk_transaction = $this->id;
        $result = $objline->create($user);
        if ($result < 0) {
            $this->error = $objline->error;
            return (-1);
        } else {
            return($result);
        }
    }

    function addLines($idtransaction, $arreglo) {
        if (is_array($arreglo)) {
            $objline = new Icontaaccountingdebcred($this->db);
            for ($i = 0; $i < count($arreglo); $i++) {
                $objline->fk_accountid = $arreglo[$i]['accountid']; //@porhacer: agregar constraint en base de datos accountid debe estar en la tabla de cuentas
                $objline->amount = $arreglo[$i]['valor'];
                $objline->direction = $arreglo[$i]['side'];
                $objline->fk_transaction = $idtransaction;
                $result = $objline->create($user);
                if ($result < 0) {
                    $this->error = $objline->error;
                    return (-1);
                }
            }
            return (1);
        } else {
            $this->error = "No se pasó un arreglo a addlines";
            return(-1);
        }
    }

    function prepareHeader($user) {
        $head = array();
//        if ($user->rights->moreprestamos->solicitud->view) {
        $arr = array(DOL_URL_ROOT . '/iconconta/transaction.php?id=' . $this->id,
            "ficha",
            "fichetrans");
        array_push($head, $arr);
//        }
        $arr = array(DOL_URL_ROOT . '/iconconta/vouchercheque.php?id=' . $this->id,
            "Voucher-cheque",
            "vouchercheque");
        array_push($head, $arr);
//        if ($user->rights->moreprestamos->log) {
        $arr = array(DOL_URL_ROOT . '/iconconta/transactionlog.php?id=' . $this->id,
            "Log",
            "log");
        array_push($head, $arr);
//        }
        return $head;
    }

    function registerloanpayment($idpayment, $user) {

        // primero obtenemos que va en cada cuenta

        global $langs, $conf;
        $sql = "
SELECT
     pm.rowid AS idpayment,
     l.loan_number,
     s.nom,
     s.rowid AS socid,
     l.rowid AS loanid,
     sum(pd.interest) AS intereses,
     sum(pd.capital) AS capital,
     sum(pd.vfee) AS capitalvoluntario,
     sum(pd.mora) AS mora,
     (sum(pd.interest)+sum(pd.capital)+sum(pd.vfee)+sum(pd.mora)) AS totalp,
     pd.numcuota,
     pm.datep,
     pm.comment,
     llx_iconta_bankmatch.fk_id_account as bankaccount,
     llx_iconta_bankmatch.fk_id_bank_account
FROM
     llx_societe s INNER JOIN llx_icon_loan l ON s.rowid = l.fk_societe
     INNER JOIN llx_icon_loan_payment_master pm ON l.rowid = pm.fk_loan
     INNER JOIN llx_icon_loan_payment_detail pd ON pm.rowid = pd.fk_master
     INNER JOIN llx_bank ON pm.fk_bank = llx_bank.rowid
     INNER JOIN llx_iconta_bankmatch ON llx_bank.fk_account = llx_iconta_bankmatch.fk_id_bank_account
     INNER JOIN llx_iconta_chart_master ON llx_iconta_bankmatch.fk_id_account = llx_iconta_chart_master.rowid
WHERE
     1 = 1";
        $sql.= " AND pm.rowid = " . $idpayment;
        $sql .= " GROUP BY     pm.rowid,     l.loan_number";


        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($this->db->num_rows($resql) < 1) {
                $this->error = "no se encontró ningun pago con codigo " . $idpayment . 'o este pago es inconsistente';
                return -1;
                exit();
            }

            $error = 0;
            $this->db->begin();

            $obj = $this->db->fetch_object($resql);
            $this->datec = $this->db->jdate($obj->datep); // setea la fecha del pago;
            $this->label = "Pago num. " . $obj->idpayment . " en abono al prestamo: " . $obj->loan_number . " de " . $obj->nom;
            $this->url = DOL_DOCUMENT_ROOT . "/moreprestamos/pagofiche.php";
            $this->number = $this->getNextValue();
            $this->fk_author = $user->id;
            $this->fk_fiscal_year = $conf->global->ICONTA_CURRENT_FISCALPERIOD_ID;
            $this->fk_source = $obj->idpayment;
            $this->entity = $conf->entity;
            $this->sourcetype = "loanPayment";
            $idasiento = $this->create($user);
            if ($idasiento > 0) {
                // crear la matriz del detalle del asiento


                $arreglo = array();
                //el total a la cuenta de banco
                $arreglo[0]['accountid'] = $obj->bankaccount; //@porhacer: agregar constraint en base de datos accountid debe estar en la tabla de cuentas
                $arreglo[0]['valor'] = $obj->intereses + $obj->capital + $obj->capitalvoluntario + $obj->mora;
                $arreglo[0]['side'] = '+';

                // el total de intereses
                $arreglo[1]['accountid'] = $conf->global->ICONLOAN_RECEIVABLE_LOANS_ACCOUNT;
                $arreglo[1]['valor'] = $obj->capital;
                $arreglo[1]['side'] = '-';
                //el total de capital
                $arreglo[2]['accountid'] = $conf->global->ICONLOAN_RECEIVABLE_INTEREST_ACCOUNT;
                $arreglo[2]['valor'] = $obj->intereses;
                $arreglo[2]['side'] = '-';
                //el total de mora
                $arreglo[3]['accountid'] = $conf->global->ICONLOAN_AREAR_INCOME_ACCOUNT;
                $arreglo[3]['valor'] = $obj->mora;
                $arreglo[3]['side'] = '-';

                //ajustar o crear un asiento de ajuste
                $r = $this->addLines($idasiento, $arreglo);
                if ($r != 1) {
                    $error++;
                } else {
                    //realiza el ajuste por abono a capital
                    if ($obj->capitalvoluntario > 0) {
                        $iajuste = 0;
                        $i1 = 0;
                        $i2 = 0;
                        require_once(DOL_DOCUMENT_ROOT . "/moreprestamos/class/iconloan.class.php");
                        $loan = new Iconloan($this->db);
                        $loan->fetch($obj->loanid);
                        $m2 = $loan->matrixPayplan($loan->id, 1, $loan->payment_period, $loan->approvedamount, $loan->fee, $loan->loaneffectivedate, 30);
                        $m1 = $loan->matrixPayplan($loan->id, 1, $loan->payment_period, $loan->approvedamount, $loan->fee, $loan->loaneffectivedate, 30, 30, $obj->idpayment);
                        foreach ($m2 as $k => $v) {
                            $i2 += $m2[$k]['interests'];
                            $i1 += $m1[$k]['interests'];
                        }

                        $iajuste = $i1 - $i2;
                        if ($iajuste < 0)
                            $iajuste = 0;

                        $this->label = "Pago num. " . $obj->idpayment . "ajuste por abono a capital voluntario al prestamo: " . $obj->loan_number . " de " . $obj->nom;
                        $this->url = DOL_DOCUMENT_ROOT . "/moreprestamos/pagofiche.php";
                        $this->number = $this->getNextValue();
                        $idasiento2 = $this->create($user);

                        // crear la matriz del detalle del asiento
                        if ($idasiento2 > 0) {

                            $arreglo = array();
                            //el total a la cuenta de banco
                            $arreglo[0]['accountid'] = $obj->bankaccount; //@porhacer: agregar constraint en base de datos accountid debe estar en la tabla de cuentas
                            $arreglo[0]['valor'] = $obj->capitalvoluntario;
                            $arreglo[0]['side'] = '+';

                            // el total de intereses
                            $arreglo[1]['accountid'] = $conf->global->ICONLOAN_RECEIVABLE_LOANS_ACCOUNT;
                            $arreglo[1]['valor'] = $obj->capitalvoluntario;
                            $arreglo[1]['side'] = '-';
                            //el total de capital
                            $arreglo[2]['accountid'] = $conf->global->ICONLOAN_RECEIVABLE_INTEREST_ACCOUNT;
                            $arreglo[2]['valor'] = $iajuste;
                            $arreglo[2]['side'] = '-';
                            //el total de mora
                            $arreglo[3]['accountid'] = $conf->global->ICONLOAN_DEFERRED_INTEREST_ACCOUNT;
                            $arreglo[3]['valor'] = $iajuste;
                            $arreglo[3]['side'] = '+';

                            //ajustar o crear un asiento de ajuste
                            $r = $this->addLines($idasiento2, $arreglo);
                            if ($r != 1)
                                $error++;
                        }
                    }
                }
            }
        } else {
            $this->error = $this->db->error;
            $error++;
        }

        if ($error) {
            foreach ($this->errors as $errmsg) {
                dol_syslog(get_class($this) . "::create " . $errmsg, LOG_ERR);
                $this->error.=($this->error ? ', ' . $errmsg : $errmsg);
            }
            $this->db->rollback();
            return -1 * $error;
        } else {
            $this->db->commit();
            return 1;
        }
    }

//end function
}

// ende class
?>
