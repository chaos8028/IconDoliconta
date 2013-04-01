<?php
$h = 0;
$head[$h][0] = 'mysetuppage.php';
$head[$h][1] = $langs->trans("Inicio");
$head[$h][2] = 'inicio';
$h++;

$head[$h][0] = 'accountclass.php';
$head[$h][1] = $langs->trans("Clases de cuentas");
$head[$h][2] = 'clases';
$h++;


$head[$h][0] = 'accounttypes.php';
$head[$h][1] = $langs->trans("Grupos de cuentas");
$head[$h][2] = 'grupos';

$h++;

$head[$h][0] = 'fiscalyears.php';
$head[$h][1] = $langs->trans("Períodos Fiscales");
$head[$h][2] = 'periodos';

$h++;

$head[$h][0] = 'matchbankaccounts.php';
$head[$h][1] = $langs->trans("Caja y Bancos");
$head[$h][2] = 'enlazar';

$h++;



$head[$h][0] = 'misc.php';
$head[$h][1] = $langs->trans("Miscelaneas");
$head[$h][2] = 'miscelanea';
$h++;

dol_fiche_head($head, $hselected, "Configuración");

?>
