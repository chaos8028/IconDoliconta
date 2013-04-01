<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2012 Manuel Munoz <manumunoz@yahoo.com>
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
 *   	\file       /pagetop.php
 *		\ingroup    iconconta
 *		\brief      Initial Page of mini acounting module
 *					
 */

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
require_once(DOL_DOCUMENT_ROOT."/iconconta/class/icontachartclass.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$id		= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

$myobject=new Icontachartclass($db);

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($action == 'add')
{
if($_POST["class_name"]== '') { $mesg='<div class="error">'."Debe asignar un nombre a la clase".'</div>'; }
else {
	$myobject->class_name=$_POST["class_name"];
	$myobject->ctype=$_POST["ctype"];
         $myobject->ordernum=$_POST["ordernum"];
        $myobject->inactive=0;
	$result=$myobject->create($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$myobject->error;
	}
    } 
}


if ($action == 'update')
{
if($_POST["class_name"]== '') { $mesg='<div class="error">'."Debe asignar un nombre a la clase".'</div>'; }
else {
        $myobject->fetch($id);
       	$myobject->class_name=$_POST["class_name"];
	$myobject->ctype=$_POST["ctype"];
        $myobject->inactive=$_POST["inactive"];
         $myobject->ordernum=$_POST["ordernum"];
	$result=$myobject->update($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$myobject->error;
	}
 }
}

if ($action == 'delete')
{

        $myobject->fetch($id);
       	$result=$myobject->delete($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$myobject->error;
	}
 
}

if ($action == 'disable' || $action=='enable')
{
    $inactive=1;
    if($action=='enable') $inactive=0;
    
        $myobject->fetch($id);
        $myobject->inactive=$inactive;
       	$result=$myobject->update($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$myobject->error;
	}
 
}



/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('', $langs->trans('Clses de cuentas'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas', '', 0, 0, $morejs, '', '');
dol_htmloutput_mesg($mesg);
print_fiche_titre("Parametos contables");
$hselected = 'clases';
require_once("tabs.php");

$form=new Form($db);
$object = new Icontachartclass($db);
$itemlist=$object->listArray();
$itemlist=$object->orderedclasses;
 dol_htmloutput_mesg($mesg);
require_once("../tpl/accountclasstpl.php");
  


llxFooter();
$db->close();
?>
