<?php

$res = 0;
if (!$res && file_exists("../../main.inc.php"))
    $res = @include("../../main.inc.php");
if (!$res)
    die("Include of main fails");
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
require_once(DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontafiscalyear.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$id = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');
$myparam = GETPOST('myparam', 'alpha');

// Protection if external user
if ($user->societe_id > 0) {
    accessforbidden();
}

$myobject = new Icontafiscalyear($db);

/* * *****************************************************************
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 * ****************************************************************** */

if ($action == 'add') {
//@porhacer: validar que se envien los datos correctamente y que los periodos no se intercalen
    $myobject->begin = dol_mktime(12, 0, 0, $_POST['beginmonth'], $_POST['beginday'], $_POST['beginyear']);
    $myobject->end = dol_mktime(12, 0, 0, $_POST['endmonth'], $_POST['endday'], $_POST['beginyear']);    
    $myobject->closed = 0;
    $res = $myobject->rangevalid( $db->idate($myobject->begin),  $db->idate($myobject->begin));
    if ($res==1){
    $result = $myobject->create($user);
    if ($result != 0)   $mesg = $myobject->error;
    } 
    else {
               $mesg = $myobject->error;
    }
}


if ($action == 'update') {
    //@porhacer: implementar actualiza y el boton cancelar
    if ($_POST["class_name"] == '') {
        $mesg = '<div class="error">' . "Debe asignar un nombre a la clase" . '</div>';
    } else {
        $myobject->fetch($id);
        $myobject->class_name = $_POST["class_name"];
        $myobject->ctype = $_POST["ctype"];
        $myobject->inactive = $_POST["inactive"];
        $myobject->ordernum = $_POST["ordernum"];
        $result = $myobject->update($user);
        if ($result > 0) {
            // Creation OK
        } {
            // Creation KO
            $mesg = '<div class="error">'.$myobject->error.'</div>';
        }
    }
}

if ($action == 'delete') {

    $myobject->fetch($id);
    $result = $myobject->delete($user);
    if ($result > 0) {
        // Creation OK
    } {
        // Creation KO
        $mesg = $myobject->error;
    }
}

if ( $action == 'enable') {
    $myobject->fetch($id);
    $rid = $myobject->id;
    $inactive = 1;
    $value = trim($rid);
    if(!$myobject->closed){
    if (dolibarr_set_const($db, "ICONTA_CURRENT_FISCALPERIOD_ID", $value, 'chaine', 0, '', $conf->entity)) {
          $conf->global->ICONTA_CURRENT_FISCALPERIOD_ID = $value;
    }
    }
}

/* * *************************************************
 * VIEW
 *
 * Put here all code to build page
 * ************************************************** */

llxHeader('', 'Periodos fiscales', '');

dol_htmloutput_mesg($mesg);
print_fiche_titre("Periodos Fiscales");
$hselected = 'periodos';
require_once("tabs.php");
$form = new Form($db);
$object = $myobject;

$object->fetchArray();
//die($object->error);
$itemlist = $object->arrayFetched;

require_once("../tpl/fiscalyear.php");


// Put here content of your page
// Example 1 : Adding jquery code
// Example 2 : Adding jquery code
//$somethingshown=$myobject->showLinkedObjectBlock();
// End of page
llxFooter();
$db->close();
?>
