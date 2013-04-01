<?php

// Change this following line to use the correct relative path (../, ../../, etc)
$res = 0;
if (!$res && file_exists("../main.inc.php"))
    $res = @include("../main.inc.php");
if (!$res && file_exists("../../main.inc.php"))
    $res = @include("../../main.inc.php");
if (!$res && file_exists("../../../main.inc.php"))
    $res = @include("../../../main.inc.php");
if (!$res && file_exists("../../../dolibarr/htdocs/main.inc.php"))
    $res = @include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (!$res && file_exists("../../../../dolibarr/htdocs/main.inc.php"))
    $res = @include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (!$res && file_exists("../../../../../dolibarr/htdocs/main.inc.php"))
    $res = @include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (!$res)
    die("Include of main fails");
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
//require_once(DOL_DOCUMENT_ROOT."/skeleton/skeleton_class.class.php");
require_once(DOL_DOCUMENT_ROOT . "/moreprestamos/class/iconloan.class.php");
require_once(DOL_DOCUMENT_ROOT . "/societe/class/societe.class.php");

dol_htmloutput_mesg($mesg);

$langs->load("companies");
$langs->load("suppliers");

// Security check
$contactid = isset($_GET["id"]) ? $_GET["id"] : '';
if ($user->societe_id)
    $socid = $user->societe_id;
$result = restrictedArea($user, 'contact', $contactid, '');

$search_cod = GETPOST("search_cod");
$search_numid = GETPOST("search_numid");
$search_societe = GETPOST("search_societe");
$search_estado = GETPOST("search_estado");
$search_fprimeracuota = dol_mktime(12, 0, 0, $_POST['search_fprimeracuotamonth'], $_POST['search_fprimeracuotaday'], $_POST['search_fprimeracuotayear']);
$search_fultimacuota = dol_mktime(12, 0, 0, $_POST['search_fultimacuotamonth'], $_POST['search_fultimacuotaday'], $_POST['search_fultimacuotayear']);
$selectarray = array(
    0 => "Borrador",
    2 => "Validado",
    3 => "En aprobacion",
    4 => "Aprobado",
    5 => "Rechazado"
);

$type = GETPOST("type");
$view = GETPOST("view");

$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$page = GETPOST("page");

if (!$sortorder)
    $sortorder = "ASC";
if (!$sortfield)
    $sortfield = "p.rowid";
if ($page < 0) {
    $page = 0;
}
$limit = $conf->liste_limit;
$offset = $limit * $page;

$titre.= " $text";

if ($_POST["button_removefilter"]) {
    $search_cod = "";
    $search_numid = "";
    $search_societe = "";
    $search_estado = "";
    $search_fprimeracuota = "";
    $search_fultimacuota = "";
}
if ($search_estado < 0) {
    $search_estado = '';
}



/*
 * View
 */

$title = "Listado de prestamos";
llxHeader('', $title, 'EN:Module_Third_Parties|FR:Module_Tiers|ES:M&oacute;dulo_Empresas');

$form = new Form($db);

$sql = "SELECT p.rowid as preid, p.loan_number, s.nom as name, p.status, s.code_client,";
$sql.= " u.login, s.rowid as socid, p.datec as fc, p.datea as fu";
$sql.= " FROM " . MAIN_DB_PREFIX . "icon_loan as p";
$sql.= " LEFT JOIN " . MAIN_DB_PREFIX . "societe as s ON s.rowid = p.fk_societe";
$sql.= " LEFT JOIN " . MAIN_DB_PREFIX . "user as u ON u.rowid = p.fk_user_creat";
if (!$user->rights->societe->client->voir && !$socid)
    $sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "societe_commerciaux as sc ON s.rowid = sc.fk_soc";
$sql.= ' WHERE s.entity IN (' . getEntity('societe', 1) . ')';

if ($search_cod) {        // filtre sur le nom
    $sql .= " AND p.loan_number LIKE '%" . $db->escape($search_cod) . "%'";
}
if ($search_numid) {     // filtre sur le prenom
    $sql .= " AND s.code_client LIKE '%" . $search_numid . "%'";
}
if ($search_fprimeracuota && $search_fultimacuota) {    // filtre sur la societe
    $sql .= " AND p.fecha_creat between '" . date("Y-m-d", $search_fprimeracuota) . "' AND '" . date("Y-m-d", $search_fultimacuota) . "'";
}
if ($search_estado) {    // filtre sur la societe
    $sql .= " AND p.status = '" . $selectarray[$search_estado] . "'";
}

if ($type == "o") {        // filtre sur type
    $sql .= " AND p.fk_societe IS NULL";
} else if ($type == "f") {        // filtre sur type
    $sql .= " AND s.fournisseur = 1";
} else if ($type == "c") {        // filtre sur type
    $sql .= " AND s.client IN (1, 3)";
} else if ($type == "p") {        // filtre sur type
    $sql .= " AND s.client IN (2, 3)";
}
if ($socid) {
    $sql .= " AND s.rowid = " . $socid;
}
// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
    $result = $db->query($sql);
    $nbtotalofrecords = $db->num_rows($result);
}
// Add order and limit
if ($view == "recent") {
    $sql.= " ORDER BY p.datec DESC ";
    $sql.= " " . $db->plimit($conf->liste_limit + 1, $offset);
} else {
    $sql.= " ORDER BY $sortfield $sortorder ";
    $sql.= " " . $db->plimit($conf->liste_limit + 1, $offset);
}

//print $sql;
dol_syslog("contact/list.php sql=" . $sql);
$result = $db->query($sql);
if ($result) {
    $prestamo = new Iconloan($db);

    $begin = $_GET["begin"];
    $param = '&begin=' . urlencode($begin) . '&view=' . urlencode($view) . '&userid=' . urlencode($_GET["userid"]) . '&contactname=' . urlencode($sall);
    $param.='&type=' . urlencode($type) . '&view=' . urlencode($view) . '&search_nom=' . urlencode($search_nom) . '&search_prenom=' . urlencode($search_prenom) . '&search_societe=' . urlencode($search_societe) . '&search_email=' . urlencode($search_email);

    $num = $db->num_rows($result);
    $i = 0;

    $titre = "Listado de prestamos";
    print_barre_liste($titre, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords);

    print '<form id="barrafiltro" method="post" action="' . $_SERVER["PHP_SELF"] . '">';
    print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
    print '<input type="hidden" name="view" value="' . $view . '">';
    print '<input type="hidden" name="sortfield" value="' . $sortfield . '">';
    print '<input type="hidden" name="sortorder" value="' . $sortorder . '">';

    if ($sall) {
        print $langs->trans("Filter") . " (" . $langs->trans("Lastname") . ", " . $langs->trans("Firstname") . " " . $langs->trans("or") . " " . $langs->trans("EMail") . "): " . $sall;
    }

    print '<table class="liste" width="100%">';

    // Ligne des titres
    print '<tr class="liste_titre">';
    print_liste_field_titre("Numero de prestamo", $_SERVER["PHP_SELF"], "p.numerodeprestamo", $begin, $param, '', $sortfield, $sortorder);
    print_liste_field_titre("Documento", $_SERVER["PHP_SELF"], '', '', '', '', '', '');
    print_liste_field_titre("Numero de identidad", $_SERVER["PHP_SELF"], "s.code_client", $begin, $param, '', $sortfield, $sortorder);
    print_liste_field_titre("Consignatario", $_SERVER["PHP_SELF"], "s.nom", $begin, $param, '', $sortfield, $sortorder);
    print_liste_field_titre("Fecha", $_SERVER["PHP_SELF"], "p.fechapagoprimeracuota", $begin, $param, '', $sortfield, $sortorder);
    print_liste_field_titre("Fecha fin", $_SERVER["PHP_SELF"], "p.fechapagoprimeracuota", $begin, $param, '', $sortfield, $sortorder);
    print_liste_field_titre("Autor", $_SERVER["PHP_SELF"], "u.login", $begin, $param, '', $sortfield, $sortorder);
    print_liste_field_titre("Estado", $_SERVER["PHP_SELF"], "p.estado", $begin, $param, '', $sortfield, $sortorder);
    print '<td class="liste_titre">&nbsp;</td>';
    print "</tr>\n";

    // Ligne des champs de filtres
    print '<tr class="liste_titre">';
    print '<td class="liste_titre">';
    print '<input class="flat" type="text" name="search_cod" size="9" value="' . $search_cod . '">';
    print '</td>';
    print '<td class="liste_titre"></td>';
    print '<td class="liste_titre">';
    print '<input class="flat" type="text" name="search_numid" size="9" value="' . $search_numid . '">';
    print '</td>';
    print '<td class="liste_titre">';
    print '<input class="flat" type="text" name="search_societe" size="9" value="' . $search_societe . '">';
    print '</td>';
    print '<td class="liste_titre">';
    print $form->select_date($search_fprimeracuota, 'search_fprimeracuota', 0, 0, 1, "barrafiltro");
    print '</td>';
    print '<td class="liste_titre">';
    print $form->select_date($search_fultimacuota, 'search_fultimacuota', 0, 0, 1, "barrafiltro");
    print '</td>';
    print '<td class="liste_titre">&nbsp;</td>';
    print '<td class="liste_titre" align="center">';
    print $form->selectarray('search_estado', $selectarray, $search_estado, 1);
    print '</td>';
    print '<td class="liste_titre" align="right">';
    print '<input type="image" value="button_search" class="liste_titre" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/search.png" name="button_search" value="' . dol_escape_htmltag($langs->trans("Search")) . '" title="' . dol_escape_htmltag($langs->trans("Search")) . '">';
    print '&nbsp; ';
    print '<input type="image" value="button_removefilter" class="liste_titre" src="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/searchclear.png" name="button_removefilter" value="' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '" title="' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '">';
    print '</td>';
    print '</tr>';

    $var = True;
    while ($i < min($num, $limit)) {
        $obj = $db->fetch_object($result);

        $var = !$var;

        print "<tr $bc[$var]>";

        // Numero de prestamo
        print '<td valign="middle">';
        $prestamo->loan_number = $obj->loan_number;
        $prestamo->id = $obj->preid;
        print '<a href="' . DOL_URL_ROOT . '/moreprestamos/grant.php?id=' . $prestamo->id . '">' . $prestamo->loan_number . '</a>';
        print '</td>';

        // Documento
        print '<td>' . dol_trunc($obj->name, 20) . '</td>';

        // Numero de identidad
        print '<td>' . dol_trunc($obj->code_client, 20) . '</td>';

        // Consignatario

            print '<td>';
            if ($obj->socid) {
                print '<a href="' . DOL_URL_ROOT . '/comm/fiche.php?socid=' . $obj->socid . '">';
                print img_object($langs->trans("ShowCompany"), "company") . ' ' . dol_trunc($obj->name, 20) . '</a>';
            } else {
                print '&nbsp;';
            }
            print '</td>';
        

        // fecha creacion
        print '<td align="center">' . $obj->fc . '</td>';

        // fecha ultima modificacion
        print '<td align="center">' . $obj->fu . '</td>';


        // Date
        print '<td align="center">' . $obj->login . '</td>';

        // Private/Public
        print '<td align="center">' . $obj->status . '</td>';

        print "</tr>\n";
        $i++;
    }

    print "</table>";

    print '</form>';
    if ($num > $limit)
        print_barre_liste('', $page, $_SERVER["PHP_SELF"], '&amp;begin=' . $begin . '&amp;view=' . $view . '&amp;userid=' . $_GET["userid"], $sortfield, $sortorder, '', $num, $nbtotalofrecords, '');

    $db->free($result);
}else {
    dol_print_error($db);
}
// End of page
llxFooter();
$db->close();
?>