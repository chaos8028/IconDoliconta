<?php

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
require_once(DOL_DOCUMENT_ROOT . "/core/lib/report.lib.php");
require_once(DOL_DOCUMENT_ROOT . "/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT . "/compta/facture/class/facture.class.php");


$langs->load("companies");
$langs->load("other");
$langs->load("compta");

// Protection if external user
if ($user->societe_id > 0) {
    accessforbidden();
}


/* * *****************************************************************
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 * ****************************************************************** */


/* * *************************************************
 * PAGE
 *
 * Put here all code to build page
 * ************************************************** */
 $morejs = array('/iconconta/js/jquery.validate.min.js');
 llxHeader('', 'Listado de Pagos a Prestamo Pendientes de contabilizar', '', '', '', '', $morejs, '', 0, 0);


$html = new Form($db);

// Put here content of your page
// ...
$sourcetypes = array('JournalEntry', 'CustomerBill', 'VendorBill', 'LoanExpenditure', 'BankMovement', 'CustomerBillPayment', 'VendorBillPayment', 'loanPayment');


$sourcelist = '<select name="sourcetype[]" size="4" multiple>';
foreach ($sourcetypes as $v) {
    $selected = '';
    if (!empty($_POST['sourcetype'])) {
        if (in_array($v, $_POST['sourcetype']))
            $selected = ' selected="true"';
    }

    $sourcelist .= '<option value="' . $v . '" ' . $selected . ' >' . $langs->trans($v) . '</option>';
}
$sourcelist .= '</select>';
if (isset($_POST['sourcetype'])) {
    $sourcetypelist = implode("','", $_POST['sourcetype']);
    $sourcetypelist = "'" . $sourcetypelist . "'";
}

$year_current = strftime("%Y", dol_now());
$pastmonth = strftime("%m", dol_now());
$pastmonthyear = $year_current;
if ($pastmonth == 0) {
    $pastmonth = 12;
    $pastmonthyear--;
}

$date_start = dol_mktime(0, 0, 0, $_REQUEST["date_startmonth"], $_REQUEST["date_startday"], $_REQUEST["date_startyear"]);
$date_end = dol_mktime(23, 59, 59, $_REQUEST["date_endmonth"], $_REQUEST["date_endday"], $_REQUEST["date_endyear"]);

if (empty($date_start) || empty($date_end)) { // We define date_start and date_end
    $date_start = dol_get_first_day($pastmonthyear, $pastmonth, false);
    $date_end = dol_get_last_day($pastmonthyear, $pastmonth, false);
}

$nom = $langs->trans("PendingLoanPaymentList");
//$nomlink=;
$builddate = time();
$description = $langs->trans("DescPendingLoanPaymentList");
$period = $html->select_date($date_start, 'date_start', 0, 0, 0, '', 1, 0, 1) . ' - ' . $html->select_date($date_end, 'date_end', 0, 0, 0, '', 1, 0, 1);
if (count($_POST['status']) > 0) {
    $statuses = $_POST['status'];
}


$sql = "SELECT
     pm.rowid AS payrowid,
     l.loan_number,
     s.nom,
     s.rowid AS socid,
     l.rowid AS loanid,
     sum(pd.interest) AS i,
     sum(pd.capital) AS c,
     sum(pd.vfee) AS v,
     sum(pd.mora) AS m,
     (sum(pd.interest)+ sum(pd.capital)+sum(pd.vfee)+ sum(pd.mora)) AS totalp,
     pd.numcuota,
     pm.datep,
     pm.comment
FROM
     " . MAIN_DB_PREFIX . "societe s INNER JOIN llx_icon_loan l ON s.rowid = l.fk_societe
     INNER JOIN " . MAIN_DB_PREFIX . "icon_loan_payment_master pm ON l.rowid = pm.fk_loan
     INNER JOIN " . MAIN_DB_PREFIX . "icon_loan_payment_detail pd ON pm.rowid = pd.fk_master";

$sql.= " WHERE 1 = 1 ";

if ($date_start && $date_end)
    $sql .= " AND pm.datep >= '" . $db->idate($date_start) . "' AND pm.datep <= '" . $db->idate($date_end) . "'";

$sql.= " GROUP BY
     pm.rowid,
     l.loan_number";


//if ($statuses != 1)
  //  $sql .= ' AND tt.fk_loan_payment  IS NULL';

//if (substr($sourcetypelist , 1, -1)!='')  $sql .= ' AND t.sourcetype IN ('.$sourcetypelist.')'; 
//echo $sql;
//@porhacer:Implementar los ordenamientos y paginacion
//$sql .= " order by t.number,d.direction,t.tms";
echo $sql;

$result = $db->query($sql);
if ($result) {
    $num = $db->num_rows($result);
    $i = 0;
    $resligne = array();
    while ($i < $num) {
        $obj = $db->fetch_object($result);

        $key = $obj->payrowid;
        //la ligne facture
        $insertarr["third"] = $obj->nom;
        $insertarr['datep'] = $obj->datep;
        $insertarr['totalp'] = $obj->totalp;
        $insertarr['loan_number'] = $obj->loan_number;
        $insertarr["tid"] = $obj->fk_accounting_transaction;
        $resligne[$key][] = $insertarr;
        $i++;
    }
} else {
    dol_print_error($db);
}


/*
 * Show result array
 */
// view


global $langs;

print "\n\n<!-- debut cartouche rapport -->\n";
print '<script type="text/javascript" language="javascript">
function onSubmit() 
{ 
  var fields = $("input[name=\'posta[]\']").serializeArray(); 
  if (fields.length == 0) 
  { 
    alert(\'NO ha Seleccionado Nada\'); 
    return false;
  } 
  else 
  { 
    formpost.submit();
    return true;
  } 
}
</script>';
$h = 0;
$head[$h][0] = $_SERVER["PHP_SELF"];
$head[$h][1] = $langs->trans("PendingLoanPaymentList");
dol_fiche_head($head, $hselected, $societe->nom);

print '<form name="formquery" method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
print '<table width="100%" class="border"><tbody>';

print'<tr><td valign="top" width="194">' . $langs->trans("ReportName") . '</td>
          <td width="581" colspan="3">' . $langs->trans("AccountJournal") . '</td></tr>';
print' <tr><td>' . $langs->trans("ReportPeriod") . '</td>
            <td colspan="3">' . $period . '</td></tr>';
print '<tr><td>' . $langs->trans("TypeOfTransaction") . ' </td>
        <td colspan="3">';
$checked = ($statuses) ? 'checked="true"' : '';
print '<input name="status" type="checkbox" id="status" value="1" ' . $checked . '>
    Mostrar tambien los contabilizados</td></tr>';
print '<tr><td valign="top">' . $langs->trans("ReportDescription") . '</td><td colspan="3">' . $description . '</td></tr>';
print '<tr><td>' . $langs->trans("GeneratedOn") . '</td><td colspan="3">';
print dol_print_date($builddate);
print '</td></tr><tr><td colspan="4" align="center"><input class="button" name="submit" value="Refrescar" type="submit"></td></tr></tbody>';
print '</form>';
// detail
$i = 0;
print '<form name="formpost" id="formpost" method="post" action="registerpayment.php">';
print "<table class=\"noborder\" width=\"100%\">";
print "<tr class=\"liste_titre\">";
print '<td align="right" width="5px">' . $langs->trans("Select") . '</td>';
print "<td>" . $langs->trans("Loan") . "</td>";
print "<td>" . $langs->trans("ThirdParty") . "</td>";
print "<td>" . $langs->trans("Date") . "</td>";
print "<td>" . $langs->trans("Amount") . "</td>";
print "<td>" . $langs->trans("Transaction") . "</td>";
print "</tr>\n<br>";

$var = true;
$r = '';

//$invoicestatic=new Facture($db);
//print_r($resligne);
foreach ($resligne as $key => $v) {

    $posted = empty($v[0]['tid']) ? false : true;

    print "<tr " . $bc[$var] . ">";

    print '<td >';
    if ($posted == false) {
        print '<input name="posta[]" type="checkbox" id="posta[]" value="' . $key . '">';
    }
    print '</td>';

    print '<td >' . $v[0]['loan_number'] . "</td>";
    print '<td >' . $v[0]['third'] . "</td>";
    print '<td >' . $v[0]['datep'] . "</td>";
    print '<td >' . $v[0]['totalp'] . "</td>";

    print '<td >';
    if ($posted == true) {
        print '<a href="transaction.php?id=' . $v[0]['tid'] . '">' . 'Ir...' . '</a>';
    }
    print '</td>';

    print "</tr>";


    $var = !$var;
}
// product


print "</table>";
    print '</form></div>';
     print '<div class="tabsAction">';
print '<a href="#" class="butAction" onclick="onSubmit();"> Contabilizar</a>';

 print '</div>';



// End of page
$db->close();
llxFooter();
?>