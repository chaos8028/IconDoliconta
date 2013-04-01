<?php

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontachartmaster.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontacharttypes.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontachartclass.class.php");
$langs->load("companies");
$langs->load("other");

// Get parameters
$id = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
//$myparam	= GETPOST('myparam','alpha');
// Protection if external user
if ($user->societe_id > 0) {
    accessforbidden();
}

$object = New Icontacharttypes($db);
$objectparent = New Icontacharttypes($db);
$objectaccclasses = New Icontachartclass($db);

$h = 0;
$head = array();

$head[$h][0] = DOL_URL_ROOT . '/iconconta/accountgroup?id=' . $id;
$head[$h][1] = $langs->trans("Ficha");
$head[$h][2] = 'general';
$h++;

$head[$h][0] = DOL_URL_ROOT . '/iconconta/grouplog?id=' . $id;
$head[$h][1] = $langs->trans("log");
$head[$h][2] = 'log';
$h++;
/* * *************************************************
 * actions
 *
 * Put here all code treat interaction
 * ************************************************** */
if ($action == 'create') {


    $morejs = array('/includes/jquery/plugins/jstree/jquery.jstree.min.js ', '/includes/jquery/plugins/jstree/jquery.cookie.js');
    llxHeader('', 'Agregar un nuevo Grupo de cuentas', '', '', '', '', $morejs, '', 0, 0);
    print_fiche_titre("Agregando un grupo contable");
    $object->ArrayForTree();
    $selected = (!empty($_GET['idg'])) ? $_GET['idg'] : ""; // en caso de que se desea preseleccionar
    $optionsclases = $objectaccclasses->listArray();

    $options = $object->arrayToTreeOption($object->treearray, 0, 0, $selected);
    $ultree = $object->arrayToTreeul($object->treearray);
    require_once("./tpl/accountgroupview.php");
    llxFooter();
    $db->close();
    exit();
}

if ($action == 'edit') {
    $object->fetch($id);
    dol_htmloutput_mesg($mesg);
    llxHeader('', 'Editar un grupo de cuentas', '', '', '', '', $morejs, '', 0, 0);
    print_fiche_titre("Editando un grupo contable");
    dol_fiche_head($head, 'general', $langs->trans("Grupo de Cuentas Contables"));
    $object->ArrayForTree();
    $selected = $object->parent; // preselecciona el padre
    $optionsclases = $objectaccclasses->listArray();

    $options = $object->arrayToTreeOption($object->treearray, 0, 0, $selected);
    print '<form action='.$_SERVER['PHP_SELF'].' name="formedit" method="post">';
    require_once("./tpl/accountgroupview.php");
    print '</div><div class="tabsAction" >';
    print '<input type="submit" class="butAction" value="Aceptar">';
    print '<input type="hidden" name="action" value="update">';
       print '<input type="hidden" name="id" value="'.$object->id.'">';
    print '<a href="' . $_SERVER['PHP_SELF'] . '?id='.$object->id.'" class="butAction">Cancelar</a>';
    print '</form></div>';
    llxFooter();
    $db->close();
    exit();
}


if ($action == 'add' || $action=='update') {
    if ($_POST["name"] == '') {
        $mesg = '<div class="error">' . "Debe asignar un nombre al grupo" . '</div>';
    } else {
        $object->name = $_POST["name"];
        $object->parent = $_POST["parent"];
        $object->class_id = $_POST["class_id"];
        $object->inactive = 0;
        if($action=='add'){
        $result = $object->create($user);
        $idret=$result;
        
        } else {
        $object->id=$id;
        $result = $object->update($user);   
        $idret=$id;
        
        }
        if ($result > 0) {
            $db->commit();
            Header("Location: " . DOL_URL_ROOT . "/iconconta/accountgroup.php?id=" . $idret);
            exit;
        } {
            // Creation KO
            $mesg = $object->error;
        }
    }
}

if ($action == 'confirm_delete' && $confirm == 'yes') {

    $db->begin();
    $object->fetch($id);
    $result = $object->delete($user);
    if ($result > 0) {
        $db->commit();
        Header("Location: " . DOL_URL_ROOT . "/iconconta/chartofaccounts.php");
        exit;
    } else {
        $mesg = '<div class="error">' . $object->error . '</div>';
        $db->rollback();
    }
}
/* * *************************************************
 * VIEW
 *
 * Put here all code to build page
 * ************************************************** */

$result = $object->fetch($id);
if ($result < 1) {

    echo $object->error;
    exit();
} else {

    llxHeader('', $langs->trans('Grupo de Cuentas'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas');

    $objectparent->fetch($object->parent);
    dol_htmloutput_mesg($mesg);
    print_fiche_titre("Grupo de Cuentas Contables");

    dol_fiche_head($head, 'general', $langs->trans("Grupo de Cuentas Contables"));
    $object->GetAccountGroup($id);
    $groupchildarray = $object->listArray($object->id);

    if (count($groupchildarray) > 0) {
        $htmlchilds = '<table class="noborder" width="100%"><tr class="liste_titre"><td align="center">Grupos hijos</td></tr>';
        foreach ($groupchildarray as $v) {
            $htmlchilds .= '<tr><td align="center"><a href="' . $_SERVER['PHP_SELF'] . '?id=' . $v['rowid'] . '">' . $v['name'] . '</a></td></tr>';
        }
        $htmlchilds .= '</table>';
    } else
        $htmlchilds = '<p align="center">No contiene grupos hijos</p>';
//print_r($groupchildarray);
    $form = new Form($db);
    require_once("./tpl/accountgroupview.php");
    print '</div><div class="tabsAction" >';

    $numaccounts = count($object->ArrayOfchildsLedger);

    if ($numaccounts < 1) {
        print '<a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=delete' . '">Eliminar</a> ';

        print '<a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&action=edit' . '">Editar</a> ';
    }
    print '</div><br>';
    $backtopage = $_SERVER['PHP_SELF'];
    $addaccount = "Agregar Cuenta";
    $buttoncreate = '<a class="addnewrecord" href="' . DOL_URL_ROOT . '/iconconta/accountmaster.php?groupid=' . $object->id . '&amp;action=create&amp;backtopage=' . urlencode($backtopage) . '">' . $addaccount . ' ' . img_picto($addcontact, 'filenew') . '</a>' . "\n";
    print_fiche_titre("Listado de Cuentas en este grupo", $buttoncreate, '');


    if ($numaccounts > 0) {
        require_once("./tpl/listchildaccounts.php");
    } else {
        print 'No hay cuentas en este grupo!';
    }

    if ($action == 'delete') {

        $ret = $form->form_confirm('accountgroup.php?id=' . $id, $langs->trans("DeleteAccountGroup"), $langs->trans("ConfirmDeleteAccountGroup"), 'confirm_delete', '', 0, 2);

        if ($ret == 'html')
            print '<br>';
    }
} //end else
llxFooter();
$db->close();
?>
