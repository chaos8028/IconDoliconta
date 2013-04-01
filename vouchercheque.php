<?php

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingtransaction.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingdebcred.class.php");
require_once(DOL_DOCUMENT_ROOT . "/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT . "/core/class/html.formother.class.php");
require_once(DOL_DOCUMENT_ROOT . '/core/modules/vouchercheque/modules_vouchercheque.php');
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
$object->fetch($id);
$formfile = new FormFile($db);

switch ($object->sourcetype)
{
    case 'LoanExpenditure':
        
        require_once(DOL_DOCUMENT_ROOT . "/moreprestamos/class/iconloan.class.php");
        $prestamo = new Iconloan($db);
        $prestamo->fetch($object->fk_source);
        $prestamo->info();
        $objsociete=$prestamo->societe;
    //    print_r($objsociete);
        break;
}
//***************actions

 if ($action == 'builddoc') { // En get ou en post
    //$objectgrant->fetchArray($id);
    if (GETPOST('model')) {
        $object->setDocModel($user, GETPOST('model'));
    }

    // Define output language
//    $outputlangs = $langs;
//    $newlang='';
//    if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
//    if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
//    if (! empty($newlang))
//    {
//        $outputlangs = new Translate("",$conf);
//        $outputlangs->setDefaultLang($newlang);
//    }
    //$object->datadir = DOL_DATA_ROOT . "/vouchercheque/" . dol_sanitizeFileName($prestamo->loan_number);
    $result = vouchercheque_pdf_create($db, $object, $objsociete, $object->modelpdf, $outputlangs, GETPOST('hidedetails'), GETPOST('hidedesc'), GETPOST('hideref'), $hookmanager);
    //die($result);
    if ($result <= 0) {
        dol_print_error($db, $result);
        exit;
    } else {
        Header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $id . (empty($conf->global->MAIN_JUMP_TAG) ? '' : '#builddoc'));
        exit;
    }
}



//********************** view********************
//
//
llxHeader('', $langs->trans('Impresion de Vouchers y cheques'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas', '', 0, 0, $morejs, '', '');
dol_htmloutput_mesg($mesg);
print_fiche_titre("Transaccion Contable");

/*
 * Affichage onglets
 */

$head = $object->prepareHeader($user);
dol_fiche_head($head, 'vouchercheque', $langs->trans("Impresion de Cheque"));


print '<table class="border" width="100%">
<tbody>
  <tr>
    <td width="10%">Numero</td>
    <td width="76%">' . $object->number . '</td>
    <td width="14%">&nbsp;</td>
  </tr>
  <tr>
    <td>Fecha</td>
    <td>' . strftime('%Y-%m-%d', $object->datec) . '</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Descripcion</td>
    <td>' . $object->label . '</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Status</td>
    <td>' . $object->LibStatut($object->status, 3) . $langs->trans($object->labelstatut[$object->status]) . '</td>
    <td>&nbsp;</td>
  </tr>';

print '<tr>
    <td>Beneficiario</td>
    <td>'.$objsociete->getNomUrl(1).'</td>
    <td>&nbsp;</td>
  </tr>
</tbody>
</table></div></br>';


print '<table width="100%"><tr><td width="50%" valign="top">';
print '<a name="builddoc"></a>'; // ancre


/*
 * Documents generes
 */
//$filename="/grants/";
$filedir = DOL_DATA_ROOT . "/vouchercheque/" . dol_sanitizeFileName($object->number);
$filename = "/" . dol_sanitizeFileName($object->number);
$urlsource = $_SERVER["PHP_SELF"] . "?id=" . $id;
$genallowed = 1;
$delallowed = 1;
$var = true;
$somethingshown = $formfile->show_documents('vouchercheque', $filename, $filedir, $urlsource, $genallowed, $delallowed, $objectgrant->modelpdf, 1, 0, 0, 28, 0, '', 0, '', $soc->default_lang, $objectgrant->hooks);


/*
 * Linked object block
 */
//$somethingshown=$objectgrant->showLinkedObjectBlock();

print '</td><td valign="top" width="50%">';

//$gra List of actions on element
//		include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php');
//		$formactions=new FormActions($db);
//		$somethingshown=$formactions->showactions($object,'propal',$socid);

print '</td></tr></table>';
?>
