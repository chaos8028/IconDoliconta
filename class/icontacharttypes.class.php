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
 *  \file       dev/skeletons/icontacharttypes.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 * 				Initialy built by build_class_from_table on 2012-07-26 15:32
 */
// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 * 	Put here description of your class
 */
class Icontacharttypes { //extends CommonObject {

    var $db;       //!< To store db handler
    var $error;       //!< To return error code (or message)
    var $errors = array();    //!< To return several error codes (or messages)
    //var $element='icontacharttypes';			//!< Id that identify managed objects
    //var $table_element='icontacharttypes';	//!< Name of table without prefix where object is stored
    var $id;
    var $name;
    var $class_id;
    var $parent;
    var $inactive;
    var $treearray = array();
    var $ArrayOfchildsLedger= array();
    var $class_name;

    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db) {
        $this->db = $db;
        return 1;
    }

    function GetnomUrl($idgroup)
    {
       $rid = $this->fetch($idgroup);
       $url = '<a href="'.DOL_URL_ROOT . '/iconconta/accountgroup?id=' . $this->id.'">'. img_picto('grupo', 'object_category').$this->name.'</a>';
        return $url;
    }
    
    function GetchildGroups($idgroup)
    {
        
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
        if (isset($this->name))
            $this->name = trim($this->name);
        if (isset($this->class_id))
            $this->class_id = trim($this->class_id);
        if (isset($this->parent))
            $this->parent = trim($this->parent);
        if (isset($this->inactive))
            $this->inactive = trim($this->inactive);



        // Check parameters
        // Put here code to add control on parameters values
        // Insert request
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "iconta_chart_types(";

        $sql.= "name,";
        $sql.= "class_id,";
        $sql.= "parent,";
        $sql.= "inactive";


        $sql.= ") VALUES (";

        $sql.= " " . (!isset($this->name) ? 'NULL' : "'" . $this->db->escape($this->name) . "'") . ",";
        $sql.= " " . (!isset($this->class_id) ? 'NULL' : "'" . $this->db->escape($this->class_id) . "'") . ",";
        $sql.= " " . (!isset($this->parent) ? 'NULL' : "'" . $this->parent . "'") . ",";
        $sql.= " " . (!isset($this->inactive) ? 'NULL' : "'" . $this->inactive . "'") . "";


        $sql.= ")";

        $this->db->begin();

        dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (!$resql) {
            $error++;
            $this->errors[] = "Error " . $this->db->lasterror();
        }

        if (!$error) {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "iconta_chart_types");

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
        $sql .= " c.class_name,";
        $sql.= " t.rowid,";
        $sql.= " t.name,";
        $sql.= " t.class_id,";
        $sql.= " t.parent,";
        $sql.= " t.inactive";
        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_chart_types as t";
        $sql.= " LEFT OUTER JOIN " . MAIN_DB_PREFIX ."iconta_chart_class as c ON t.class_id = c.rowid";
        $sql.= " WHERE t.rowid = " . $id;
        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($this->db->num_rows($resql)) {
                $obj = $this->db->fetch_object($resql);
                $this->id = $obj->rowid;
                $this->name = $obj->name;
                $this->class_name = $obj->class_name;
                $this->class_id = $obj->class_id;
                $this->parent = $obj->parent;
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

        if (isset($this->id))
            $this->id = trim($this->id);
        if (isset($this->name))
            $this->name = trim($this->name);
        if (isset($this->class_id))
            $this->class_id = trim($this->class_id);
        if (isset($this->parent))
            $this->parent = trim($this->parent);
        if (isset($this->inactive))
            $this->inactive = trim($this->inactive);



        // Check parameters
        // Put here code to add control on parameters values
        // Update request
        $sql = "UPDATE " . MAIN_DB_PREFIX . "iconta_chart_types SET";

        $sql.= " name=" . (isset($this->name) ? "'" . $this->db->escape($this->name) . "'" : "null") . ",";
        $sql.= " class_id=" . (isset($this->class_id) ? "'" . $this->db->escape($this->class_id) . "'" : "null") . ",";
        $sql.= " parent=" . (isset($this->parent) ? $this->parent : "null") . ",";
        $sql.= " inactive=" . (isset($this->inactive) ? $this->inactive : "null") . "";


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
            $sql = "DELETE FROM " . MAIN_DB_PREFIX . "iconta_chart_types";
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

        $object = new Icontacharttypes($this->db);

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

    function listArray($parent="") {

        global $langs;
        $RetunArray = Array();


        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.= " t.name,";
        $sql.= " t.class_id,";
        $sql.= " t.parent,";
        $sql.= " t.inactive";
        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_chart_types as t ";
        if ($parent!="")   $sql.= " WHERE parent =".$parent;
        $sql.= " order by parent";
        
        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            $t = $this->db->num_rows($resql);
            if ($t) {

                for ($i = 0; $i < $t; $i++) {
                    $ReturnArray[] = $this->db->fetch_array($resql);
                }
            }
            $this->db->free($resql);

            return $ReturnArray;
        } else {
            $this->error = "Error " . $this->db->lasterror();
            dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
            return $ReturnArray;
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
        $this->name = '';
        $this->class_id = '';
        $this->parent = '';
        $this->inactive = '';
    }

    function arrayToTreeul($a, $start = 0) {

        $result = '';

        if (isset($a[$start])) {
            if ($start == 0) {
                $result .= '<ul>';
            } else {
                $result .= '<ul>';
            }

            foreach ($a[$start] as $v) {
                $result .= '<li id=' . $v['id'] . '><a id="A_f_' . $v['id'] . '" href="' . DOL_URL_ROOT . '/iconconta/accountgroup.php?id=' . $v['id'] . '">' . $v['name'] . "</a>";

                $result .= self::arrayToTreeul($a, $v['id']);
            }
        }

        if (!empty($result)) {
            $result .= "</li></ul>";
        }

        return $result;
    }

    function arrayToTreeOption($a, $start = 0, $level = 0,$selected='') {

        $result = '';
        $arrow = "->";

        if ($level == 0)
            $arrow = "";

        if (isset($a[$start])) {


            foreach ($a[$start] as $v) {
                if($selected !='' && $v['id']== $selected) $sel = 'selected="true"';
                $result .= '<option value=' . $v['id'] . ' '.$sel.'>' . str_repeat("&nbsp;&nbsp;", $level)
                        . $arrow . $v['name'] . "</option>";
        $sel = "";
                $result .= self::arrayToTreeOption($a, $v['id'], $level + 1,$selected);
            }
        }

        if (!empty($result)) {
            //$result .= "</li></ul>";
        }

        return $result;
    }

    function ArrayForTree() {

        $sql = "SELECT rowid,name,parent FROM " . MAIN_DB_PREFIX . "iconta_chart_types order by parent";
        $resql = $this->db->query($sql);
        if ($resql) {

            $num = $this->db->num_rows($resql);
            $i = 0;
            if ($num) {
                while ($i < $num) {
                    $obj = $this->db->fetch_object($resql);
                    if ($obj) {
                        $parent = 0;
                        if (isset($obj->parent) || $obj->parent != '') {
                            $parent = $obj->parent;
                        }
                        $this->treearray[$parent][] = array('id' => $obj->rowid, 'name' => $obj->name);
                    }
                    $i++;
                }
            }
        }
    
        return $this->treearray;
    }

    function get_optionlist($s="") {
        $this->ArrayForTree();
        $htmlres = $this->arrayToTreeOption($this->treearray,0,0,$s);
        return $htmlres;
    }
    
        /**
     *  Create object into database
     *
     *  @param	groupid 	$groupid       group of accounts that should be fetched in $ArrayOfchildsLedger
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function GetAccountGroup($groupid, $limit='') {

        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.= " t.account_code,";
        $sql.= " t.account_code2,";
        $sql.= " t.account_name,";
        $sql.= " t.account_type,";
        $sql.= " t.inactive";


        $sql.= " FROM " . MAIN_DB_PREFIX . "iconta_chart_master as t";
        $sql.= " WHERE t.account_type = " . $groupid;
         $sql.= " AND t.inactive = 0";
        // ad limits and pagination

        dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            $num=$this->db->num_rows($resql);
            if ($num) {
                for($i=0;$i<$num;$i++){
                $this->ArrayOfchildsLedger[] = $this->db->fetch_array($resql);}
            }
            $this->db->free($resql);

            return 1;
        } else {
            $this->error = "Error " . $this->db->lasterror();
            dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
            return -1;
        }
    }

}

?>
