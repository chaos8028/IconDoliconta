<?php

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingtransaction.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingdebcred.class.php");
//$langs->load("companies");
//$langs->load("other");
// Get parameters
$id = GETPOST('id', 'int');
$error = 0;
$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
//$myparam	= GETPOST('myparam','alpha');

if ($action == '')
    $action = 'view';

// Protection if external user
if ($user->societe_id > 0) {
    accessforbidden();
}

$object = New Icontaaccountingtransaction($db);


/* * *************************************************
 * actions
 *
 * Put here all code treat interaction
 * ************************************************** */
//@porhacer: agregar derecho de borrado en el if
//@porhacer: validar que solamente se pueda borrar una transaccion si es borrador
if ($action == 'confirm_delete' && $confirm == 'yes') {
    $db->begin();


    $object->fetch($id);
    $result = $object->delete($user);
    if ($result > 0) {
        $db->commit();
        Header("Location: " . DOL_URL_ROOT . "/iconconta/transactionslist.php");
        exit;
    } else {
        $mesg = '<div class="error">' . $object->error . '</div>';
        $db->rollback();
    }
}




if ($action == 'add') {
    $error=0;
    if ($_POST["label"] == '') {
    $mesg = '<div class="error">' . "Debe asignar una descripcion a la transaccion" . '</div>';
    $error++;
        $action = 'create';
    }
   
    if($error==0)
    {
    $mydate = dol_mktime(12, 0, 0, $_POST['datecmonth'], $_POST['datecday'], $_POST['datecyear']);
//print($mydate);

    $object->entity = $conf->entity;
    $object->label = $_POST["label"];
    $object->datec = $mydate;
    $object->number = $_POST["number"];
    $object->status = 0; // crea un borrador de transaccion
    $object->sourcetype = $_POST["sourcetype"];;
    $object->fk_fiscal_year = $conf->global->ICONTA_CURRENT_FISCALPERIOD_ID;
    $result = $object->create($user);

    // die($object->error);

    if ($result > 0) {
        // echo "se fue";
        //  $action = 'view';
        $id = $object->id;
        $headerstr = "Location: " . $_SERVER["PHP_SELF"] . '?id=' . $id;
        Header($headerstr);
        exit;
        //$mesg = '<div>' . "Se ha creado correctamente la transaccion" . '</div>';
    } else {
        // Creation KO
        $mesg = '<div class="error">'.$object->error."</div>";
        $action = 'create';
    }
    }// end if not error
}

if ($action == 'updatedate') {

}

if ($action == 'updatedesc') {

}


if ($action == 'create') {
    /*     * ************************************************************************* */
    /*                                                                            */
    /* Fiche creation                                                             */
    /*                                                                            */
    /*     * ************************************************************************* */
    //echo "que pex?";
    require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaentrytypes.class.php");
    $entrytypeobj = new Icontaentrytypes($db);
    llxHeader('', $langs->trans('Agregar un asiento'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas');
    print_fiche_titre($langs->trans("Nuevo Asiento en Diario"));

    dol_htmloutput_mesg($errmsg, $errmsgs, 'error');
    dol_htmloutput_mesg($mesg, $mesgs);
    $form = new Form($db);


    $numtrans = $object->getNextValue();
    $entrytypeobj->fetchArray();
    $listarr=$entrytypeobj->arrayFetched;

    print '<form name="formtrans" action="' . $_SERVER["PHP_SELF"] . '" method="post" enctype="multipart/form-data">';

    require_once('./tpl/transactions.php');

    print '<input type="hidden" name="action" value="add"> </form>';
    llxFooter();
    $db->close();
    exit();
}


// addlines 

if ($action == 'addline') {

    //echo 'agregando..';

    if (!empty($_POST['debe']) && doubleval($_POST['debe']) > 0) {
        $valor = doubleval($_POST['debe']);
        $side = '+';
    } elseif (!empty($_POST['haber']) && doubleval($_POST['haber']) > 0) {
        $valor = doubleval($_POST['haber']);
        $side = '-';
    } else {
        $mesg = '<div class="error">' . "Debe Ingresar un valor mayor que cero en el debe o el haber" . '</div>';
        $error++;
    }

    if (empty($_POST['accountid']) || trim($_POST['accountid']) == '' || (intval($_POST['accountid']) <= 0)) {
        $mesg = '<div class="error">' . "Debe Seleccionar una cuenta contable válida" . '</div>';
        $error++;
    }

    if ($error == 0) {
        $objline = new Icontaaccountingdebcred($db);
        $objline->fk_accountid = $_POST['accountid']; //@porhacer: agregar constraint en base de datos accountid debe estar en la tabla de cuentas
        $objline->amount = $valor;
        $objline->direction = $side;
        $objline->fk_transaction = $id;
        $result = $objline->create($user);
        if ($result < 1) {
            $error++;
            $mesg = '<div class="error">' . $objline->error . '</div>';
        } else {
            $headerstr = "Location: " . $_SERVER["PHP_SELF"] . '?id=' . $id;
            Header($headerstr);
            exit;
        }
    }
    //$action='view';
}
/* * *************************************************
 * VIEW
 *
 * Put here all code to build page
 * ************************************************** */
if (empty($conf->global->ICONTA_CURRENT_FISCALPERIOD_ID))
    die("debe incializar un período fiscal con el fin de poder realizar transacciones");

$result = $object->fetch($id);
if ($result <= 0) {
    accessforbidden();
} else {
    $morejs = array("/iconconta/js/autocompleteaccountcode.js");
    llxHeader('', $langs->trans('Vista de una transaccion'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas', '', 0, 0, $morejs, '', '');




    dol_htmloutput_mesg($mesg);



    print_fiche_titre("Transaccion Contable");

    /*
     * Affichage onglets
     */

    $head = $object->prepareHeader($user);

    dol_fiche_head($head, 'fichetrans', $langs->trans("Vista de una transacción"));


    $object->getChildEntries();



    require_once("./tpl/transactions.php");
    if ($action == 'delete') {
        $form = new Form($db);
        $ret = $form->form_confirm('transaction.php?id=' . $object->id, $langs->trans("DeleteTransaction"), $langs->trans("ConfirmDeleteTransaction"), 'confirm_delete', '', 0, 2);
        if ($ret == 'html')
            print '<br>';
    }
}

// End of page


llxFooter();
$db->close();
?>
