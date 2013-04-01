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
 *  \file       dev/skeletons/icontafiscalyear.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 * 				Initialy built by build_class_from_table on 2012-10-04 16:10
 */
// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 * 	Put here description of your class
 */
class Icontafiscalyear // extends CommonObject 
{

    var $db;       //!< To store db handler
    var $error;       //!< To return error code (or message)
    var $errors = array();    //!< To return several error codes (or messages)
    //var $element='icontafiscalyear';			//!< Id that identify managed objects
    //var $table_element='icontafiscalyear';	//!< Name of table without prefix where object is stored
//    var $id;

    var $id;
    var $begin = '';
    var $end = '';
    var $closed;
    var $arrayFetched = array();

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

        if (isset($this->id))
            $this->id = trim($this->id);
        if (isset($this->closed))
            $this->closed = trim($this->closed);

      

        // Check parameters
        // Put here code to add control on parameters values
        // Insert request
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "iconta_fiscal_year(";

        $sql.= "begin,";
        $sql.= "end,";
        $sql.= "closed";


        $sql.= ") VALUES (";

        $sql.= " " . (!isset($this->begin) || dol_strlen($this->begin) == 0 ? 'NULL' : $this->db->idate($this->begin)) . ",";
        $sql.= " " . (!isset($this->end) || dol_strlen($this->end) == 0 ? 'NULL' : $this->db->idate($this->end)) . ",";
        $sql.= " " . (!isset($this->closed) ? 'NULL' : "'" . $this->closed . "'") . "";


        $sql.= ")";

        $this->db->begin();

        dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (!$resql) {
            $error++;
            $this->errors[] = "Error " . $this->db->lasterror();
        }

        if (!$error) {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "iconta_fiscal_year");

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

    function rangevalid($date1, $date2)
    {
   $sql = "  SELECT sum( if(('".$date1."' BETWEEN begin and end) or ('".$date2."' BETWEEN begin and end),1,0) ) as VALID";
   $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_fiscal_year";
        dol_syslog(get_class($this) . "::rangevalid sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
          if( $obj->VALID == 0){
            $this->db->free($resql);
            return 1;
            
          } else {
              $this->error = "Error: el rango de fechas no es valido, ya existe  al menos uno que incluye estas fechas  ";
            return -1;
              
           }

           } else {
            $this->error = "Error " . $this->db->lasterror();
            dol_syslog(get_class($this) . "::rangevalid " . $this->error, LOG_ERR);
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
        $sql.= " t.begin,";
        $sql.= " t.end,";
        $sql.= " t.closed";


        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_fiscal_year as t";
        $sql.= " WHERE t.rowid = " . $id;

        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($this->db->num_rows($resql)) {
                $obj = $this->db->fetch_object($resql);

                $this->id = $obj->rowid;
                $this->begin = $this->db->jdate($obj->begin);
                $this->end = $this->db->jdate($obj->end);
                $this->closed = $obj->closed;
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
    function fetchArray($includeclosed=true) {
        global $langs;
        $sql = "SELECT";
        $sql.= " t.rowid,";


        $sql.= " t.begin,";
        $sql.= " t.end,";
        $sql.= " t.closed";


        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_fiscal_year as t";
        if ($includeclosed==false)
        $sql.= " WHERE t.closed = 0";

        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            $numrows = $this->db->num_rows($resql);
            if ($numrows > 1) {
                for ($i = 0; $i < $numrows; $i++) {
                    $this->arrayFetched[] = $this->db->fetch_array($resql);
                }
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

        if (isset($this->id))
            $this->id = trim($this->id);
        if (isset($this->closed))
            $this->closed = trim($this->closed);



        // Check parameters
        // Put here code to add control on parameters values
        // Update request
        $sql = "UPDATE " . MAIN_DB_PREFIX . "iconta_fiscal_year SET";
        $sql.= " begin=" . (dol_strlen($this->begin) != 0 ? "'" . $this->db->idate($this->begin) . "'" : 'null') . ",";
        $sql.= " end=" . (dol_strlen($this->end) != 0 ? "'" . $this->db->idate($this->end) . "'" : 'null') . ",";
        $sql.= " closed=" . (isset($this->closed) ? $this->closed : "null") . "";


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
            $sql = "DELETE FROM " . MAIN_DB_PREFIX . "iconta_fiscal_year";
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

        $object = new Icontafiscalyear($this->db);

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

        $this->id = '';
        $this->begin = '';
        $this->end = '';
        $this->closed = '';
    }

}

?>
