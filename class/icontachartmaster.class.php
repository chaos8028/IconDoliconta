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
 *  \file       dev/skeletons/icontachartmaster.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 * 				Initialy built by build_class_from_table on 2012-07-31 15:42
 */
// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 * 	Put here description of your class
 */
class Icontachartmaster  { // extends CommonObject {

    var $db;       //!< To store db handler
    var $error;       //!< To return error code (or message)
    var $errors = array();    //!< To return several error codes (or messages)
    //var $element='icontachartmaster';			//!< Id that identify managed objects
    //var $table_element='icontachartmaster';	//!< Name of table without prefix where object is stored
    var $id;
    var $account_code;
    var $account_code2;
    var $account_name;
    var $account_type;
    var $inactive;
    var $numrows;
    var $description;
    

    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db) {
        $this->db = $db;
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

        if (isset($this->account_code))
            $this->account_code = trim($this->account_code);
        if (isset($this->account_code2))
            $this->account_code2 = trim($this->account_code2);
        if (isset($this->account_name))
            $this->account_name = trim($this->account_name);
        if (isset($this->account_type))
            $this->account_type = trim($this->account_type);
        if (isset($this->inactive))
            $this->inactive = trim($this->inactive);



        // Check parameters
        // Put here code to add control on parameters values
        // Insert request
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "iconta_chart_master(";

        $sql.= "account_code,";
        $sql.= "account_code2,";
        $sql.= "account_name,";
        $sql.= "account_type,";
        $sql.= "description,";
        $sql.= "inactive";


        $sql.= ") VALUES (";

        $sql.= " " . (!isset($this->account_code) ? 'NULL' : "'" . $this->db->escape($this->account_code) . "'") . ",";
        $sql.= " " . (!isset($this->account_code2) ? 'NULL' : "'" . $this->db->escape($this->account_code2) . "'") . ",";
        $sql.= " " . (!isset($this->account_name) ? 'NULL' : "'" . $this->db->escape($this->account_name) . "'") . ",";
        $sql.= " " . (!isset($this->account_type) ? 'NULL' : "'" . $this->db->escape($this->account_type) . "'") . ",";
         $sql.= " " . (!isset($this->description) ? 'NULL' : "'" . $this->db->escape($this->description) . "'") . ",";
        $sql.= " " . (!isset($this->inactive) ? '0' : "'" . $this->inactive . "'");


        $sql.= ")";
        $this->db->begin();

        dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (!$resql) {
            $error++;
            $this->errors[] = "Error " . $this->db->lasterror();
        }

        if (!$error) {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "iconta_chart_master");

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
     *  Load object in memory from database by code1
     *
     *  @param	varchar		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetchbycode1($code1) {
        global $langs;
        $sql = "SELECT";
        $sql.= " t.rowid,";

        $sql.= " t.account_code,";
        $sql.= " t.account_code2,";
        $sql.= " t.account_name,";
        $sql.= " t.account_type,";
        $sql.= " t.inactive";


        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_chart_master as t";
        $sql.= " WHERE t.account_code = '". $code1."'";

        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            $this->numrows=$this->db->num_rows($resql);
            if ($this->numrows>0) {
                $obj = $this->db->fetch_object($resql);
                 
                $this->id = $obj->rowid;

                $this->account_code = $obj->account_code;
                $this->account_code2 = $obj->account_code2;
                $this->account_name = $obj->account_name;
                $this->account_type = $obj->account_type;
                $this->inactive = $obj->inactive;
            }
            $this->db->free($resql);

            return 1;
        } else {
            $this->error = "Error " . $this->db->lasterror();
            dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
            return -1;
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

        $sql.= " t.account_code,";
        $sql.= " t.account_code2,";
        $sql.= " t.account_name,";
        $sql.= " t.account_type,";
        $sql.= "t.description,";
        $sql.= " t.inactive";


        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_chart_master as t";
        $sql.= " WHERE t.rowid = " . $id;

        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($this->db->num_rows($resql)) {
                $obj = $this->db->fetch_object($resql);

                $this->id = $obj->rowid;

                $this->account_code = $obj->account_code;
                $this->account_code2 = $obj->account_code2;
                $this->account_name = $obj->account_name;
                $this->account_type = $obj->account_type;
                $this->description = $obj->description;
                $this->inactive = $obj->inactive;
            }
            $this->db->free($resql);

            return 1;
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

        if (isset($this->account_code))
            $this->account_code = trim($this->account_code);
        if (isset($this->account_code2))
            $this->account_code2 = trim($this->account_code2);
        if (isset($this->account_name))
            $this->account_name = trim($this->account_name);
        if (isset($this->account_type))
            $this->account_type = trim($this->account_type);
        
         if (isset($this->description))
            $this->description = trim($this->description);
        
        
        
        if (isset($this->inactive))
            $this->inactive = trim($this->inactive);



        // Check parameters
        // Put here code to add control on parameters values
        // Update request
        $sql = "UPDATE " . MAIN_DB_PREFIX . "iconta_chart_master SET";

        $sql.= " account_code=" . (isset($this->account_code) ? "'" . $this->db->escape($this->account_code) . "'" : "null") . ",";
        $sql.= " account_code2=" . (isset($this->account_code2) ? "'" . $this->db->escape($this->account_code2) . "'" : "null") . ",";
        $sql.= " account_name=" . (isset($this->account_name) ? "'" . $this->db->escape($this->account_name) . "'" : "null") . ",";
        $sql.= " account_type=" . (isset($this->account_type) ? "'" . $this->db->escape($this->account_type) . "'" : "null") . ",";
         $sql.= "description=" . (isset($this->description) ? "'" . $this->db->escape($this->description) . "'" : "null") . ",";
        $sql.= " inactive=" . (isset($this->inactive) ? $this->inactive : "null") ;


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
            $sql = "DELETE FROM " . MAIN_DB_PREFIX . "iconta_chart_master";
            $sql.= " WHERE rowid=" . $this->id;

            dol_syslog(get_class($this) . "::delete sql=" . $sql);
            $resql = $this->db->query($sql);
            if (!$resql) {
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

        $object = new Icontachartmaster($this->db);

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

        $this->account_code = '';
        $this->account_code2 = '';
        $this->account_name = '';
        $this->account_type = '';
        $this->inactive = '';
    }
    
     function getMasterArray() 
     {
     
     }

}

?>
