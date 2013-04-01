<?php
/* Copyright (C) 2010 Regis Houssin       <regis@dolibarr.fr>
 * Copyright (C) 2011 Laurent Destailleur <eldy@users.sourceforge.net>
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
 *       \file      htdocs/core/ajaxziptown.php
 *       \ingroup	core
 *       \brief     File to return Ajax response on zipcode or town request
 *       \version   $Id: ajaxziptown.php,v 1.12 2011/07/31 23:45:15 eldy Exp $
 */

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL',1); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');

require('../main.inc.php');
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");

$mode=GETPOST('mode');


/*
 * View
 */

// Ajout directives pour resoudre bug IE
//header('Cache-Control: Public, must-revalidate');
//header('Pragma: public');

//top_htmlhead("", "", 1);  // Replaced with top_httphead. An ajax page does not need html header.
top_httphead();

//print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

dol_syslog("GET is ".join(',',$_GET));
//var_dump($_GET);

// Generation of list of zip-town
if (! empty($_GET['term']) )
{
	$return_arr = array();
	//$formcompany = new FormCompany($db);

	// Define filter on text typed
	$codigo = $_GET['term']?$_GET['term']:'';

        $sql = "SELECT DISTINCT s.rowid,s.account_code,s.account_code2 ,s.account_name";
                $sql.= " FROM ".MAIN_DB_PREFIX.'iconta_chart_master as s';
        if($mode==1){
        $sql.= " WHERE s.account_code LIKE '".$db->escape($codigo)."%'";
        $sql.= " OR s.account_code2 LIKE '".$db->escape($codigo)."%'";
        }
        
      if($mode==2){
        $sql.= " WHERE s.account_name LIKE '%".$db->escape($codigo)."%'";
        }
            
        $sql.= " ORDER BY s.rowid";
        $sql.= $db->plimit(50); // Avoid pb with bad criteria
	

    //print $sql;
	$resql=$db->query($sql);
	//var_dump($db);
	if ($resql)
	{
		while ($row = $db->fetch_array($resql))
		{
			
			$row_array['id'] = $row['rowid'];
			$row_array['label'] = $row['account_code'].'-'.$row['account_code'].'-'. $row['account_name'];
                        $row_array['value'] = $row['account_code'];
                         if($mode==2)
                         {
                            $row_array['value'] = $row['account_code'].'-'.$row['account_code'].'-'. $row['account_name']; 
                         
                            $row_array['code']= $row['account_code'];
                         }
			array_push($return_arr,$row_array);
		}
	}

	echo json_encode($return_arr);
}
else
{

}

?>
