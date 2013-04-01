<?php

// script setear el año en curso
$res = 0;
if (!$res && file_exists("../../main.inc.php"))
    $res = @include("../../main.inc.php");
if (!$res)
    die("Include of main fails");
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)


$action = GETPOST('action', 'alpha');

// actions

if ($action == "setfiscalperiod") {
   
    $value = trim($_POST['selectedfiscalperiod']);
    if (dolibarr_set_const($db, "ICONTA_CURRENT_FISCALPERIOD_ID", $value, 'chaine', 0, '', $conf->entity)) {
          $conf->global->ICONTA_CURRENT_FISCALPERIOD_ID = $value;
    }
}
//Actions




llxHeader('', $langs->trans('Parametros de contabilidad-comprobacion de la instalacion'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas', '', 0, 0, $morejs, '', '');
dol_htmloutput_mesg($mesg);
print_fiche_titre("Parametos contables");
$form = new Form($db);
$hselected = 'inicio';
require_once("tabs.php");

?>
<table width="100%" class="noborder">
  <tr class="liste_titre">
    <td>Parametro</td>
    <td>Descripcion</td>
    <td>Estado</td>
  </tr>
  <tr>
    <td>Match de Cuentas Bancarias </td>
    <td>Comprueba que cada cuenta definida en el modulo de caja y bancos tenga un par en el arbol contable </td>
    <td>Inconsistente</td>
  </tr>
  <tr>
    <td>Al Menos una clase de gr </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>