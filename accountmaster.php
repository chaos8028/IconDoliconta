<?php

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontachartmaster.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontacharttypes.class.php");
$langs->load("companies");
$langs->load("other");

// Get parameters
$id = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');
//$myparam	= GETPOST('myparam','alpha');
// Protection if external user
if ($user->societe_id > 0) {
    accessforbidden();
}

$object = New Icontachartmaster($db);

$head = array();
$h = 0;
$head[$h][0] = DOL_URL_ROOT . '/iconconta/accountmaster?id=' . $id;
$head[$h][1] = $langs->trans("Ficha");
$head[$h][2] = 'general';
$h++;


/* * *************************************************
 * actions
 *
 * Put here all code treat interaction
 * ************************************************** */
if ($action == 'update') {
    $error = 0;
    $object->id = $id;
    $object->account_code = trim($_POST["account_code"]);
    $object->account_code2 = trim($_POST["account_code2"]);
    $object->account_name = trim($_POST["account_name"]);
    $object->account_type = trim($_POST["account_type"]);
    $object->inactive = $_POST["inactive"];
        $object->description = trim($_POST["description"]);

    if (empty($object->account_code)) {
        $error++;
        $mesg = '<div class="error">' . "Debe asignar un nombre a la cuenta" . '</div>';
    }
    if (empty($object->account_name)) {
        $error++;
        $mesg = '<div class="error">' . "Debe asignar un Codigo Primario a la cuenta" . '</div>';
    }
    if (empty($object->account_type) || $object->account_type == '') {
        $error++;
        $mesg = '<div class="error">' . "Debe Seleccionar un grupo de cuentas" . '</div>';
    }
    if ($error == 0) {

        $result = $object->update($user);
        if ($result > 0) {

            $headerstr = "Location: " . $_SERVER["PHP_SELF"] . '?id=' . $object->id;
            Header($headerstr);
            exit;
        } {
            $error++;
            // Creation KO
            $mesg = $object->error;
        }
    } else {
        $action = '';
    }
}

if ($action == 'add') {
    $error = 0;

    $object->account_code = trim($_POST["account_code"]);
    $object->account_code2 = trim($_POST["account_code2"]);
    $object->account_name = trim($_POST["account_name"]);
    $object->account_type = trim($_POST["account_type"]);
    $object->description = trim($_POST["description"]);
    $object->inactive = $_POST["inactive"];

    if (empty($object->account_code)) {
        $error++;
        $mesg = '<div class="error">' . "Debe asignar un nombre a la cuenta" . '</div>';
    }
    if (empty($object->account_name)) {
        $error++;
        $mesg = '<div class="error">' . "Debe asignar un Codigo Primario a la cuenta" . '</div>';
    }
    if (empty($object->account_type) || $object->account_type == '') {
        $error++;
        $mesg = '<div class="error">' . "Debe Seleccionar un grupo de cuentas" . '</div>';
    }
    if ($error == 0) {

        $result = $object->create($user);
        if ($result > 0) {

            $headerstr = "Location: " . $_SERVER["PHP_SELF"] . '?id=' . $result;
            Header($headerstr);
            exit;
        } {
            $error++;
            // Creation KO
            $mesg = $object->error;
        }
    } else {
        $action = 'create';
    }
}

if ($action == 'create') {
    llxHeader('', $langs->trans('Adicion de cuentas Contables'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas');
    dol_htmloutput_mesg($mesg);
    $form = new Form($db);
    $typesobj = new Icontacharttypes($db);
    $groupid=$_GET['groupid'];
    $options = $typesobj->get_optionlist($groupid);
    print_fiche_titre("Agregar una cuenta contable");
    require_once("./tpl/accountviews.php");
    llxFooter();
    $db->close();
    exit();
}

if ($action == 'confirm_delete') {
    $object->fetch($id);
    $idgroup = $object->account_type;
    $object->delete($user);
    if($object->error)
    {
       $mesg = '<div class="error">'.$object->error.'</div>';
    }
    else
    {
            $headerstr = "Location: " .  DOL_URL_ROOT . '/iconconta/accoungroup?id=' . $idgroup;
            Header($headerstr);
            exit();
    }


}


if ($action == "edit") {
    $result = $object->fetch($id);
if (!$result) {

    accessforbidden();
}
    llxHeader('', $langs->trans('Edición de una Cuenta Contable'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas');
    dol_htmloutput_mesg($mesg);
    $form = new Form($db);
    $typesobj = new Icontacharttypes($db);
    $options = $typesobj->get_optionlist($object->account_type);
    print_fiche_titre("Editar una cuenta contable");
    dol_fiche_head($head, 'general', $langs->trans("Cuenta Contable"));
    require_once("./tpl/accountviews.php");
    llxFooter();
    $db->close();
    exit();
}

/* * *************************************************
 * VIEW
 *
 * Put here all code to build page
 * ************************************************** */

$result = $object->fetch($id);
if (!$result) {

    accessforbidden();
}
$typesobj = new Icontacharttypes($db);
llxHeader('', $langs->trans('Gestión de cuentas Contables'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas');


dol_htmloutput_mesg($mesg);
//print_r( $form);

$form = new Form($db);

print_fiche_titre("Ficha de Cuenta Contables");

/*
 * Affichage onglets
 */

dol_fiche_head($head, 'general', $langs->trans("Cuenta Contable"));
$urlgroup = $typesobj->GetnomUrl($object->account_type);
$activesign=  img_picto('activa', 'on');
if ($object->inactive)$activesign=  img_picto('inactiva', 'off');

require_once("./tpl/accountviews.php");

      print '</div><div class="tabsAction">';   
      print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans('Edit').'</a>';
      print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a>';
                    print '</div>';

  print '</div><br>';
    $backtopage = $_SERVER['PHP_SELF'];
   // $addaccount = "Agregar Cuenta";
   // $buttoncreate = '<a class="addnewrecord" href="' . DOL_URL_ROOT . '/iconconta/accountmaster.php?groupid=' . $object->id . '&amp;action=create&amp;backtopage=' . urlencode($backtopage) . '">' . $addaccount . ' ' . img_picto($addcontact, 'filenew') . '</a>' . "\n";
    print_fiche_titre("ultimos movimientos en esta cuenta", '', '');



        require_once("./tpl/listchildtransactions.php");
  
   
  

    if ($action == 'delete') {

        $ret = $form->form_confirm('accountmaster.php?id=' . $id, $langs->trans("DeleteAccountGroup"), $langs->trans("ConfirmDeleteAccountMaster"), 'confirm_delete', '', 0, 2);

        if ($ret == 'html')
            print '<br>';
    }
llxFooter();
$db->close();
?>
