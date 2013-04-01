<?php

/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007-2010 Jean Heimburger  <jean@tiaris.info>
 * Copyright (C) 2011	   Juanjo Menent    <jmenent@2byte.es>
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
 *   	\file       htdocs/compta/journal/sellsjournal.php
 * 		\ingroup    societe, facture
 * 		\brief      Page with sells journal
 */
include("../main.inc.php");
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

llxHeader('', '', '');

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

$nom = $langs->trans("AccountJournal");
//$nomlink=;
$builddate = time();
$description = $langs->trans("DescSellsJournal");
$period = $html->select_date($date_start, 'date_start', 0, 0, 0, '', 1, 0, 1) . ' - ' . $html->select_date($date_end, 'date_end', 0, 0, 0, '', 1, 0, 1);
if (count($_POST['status']) > 0) {
    $statuses = implode(',', $_POST['status']);
}


$sql = "SELECT
     t.rowid,
     t.number,
     t.label,
     t.datec,
     t.fk_author,
     t.url,
     t.status,
     t.sourcetype,
     t.fk_source,
     t.tms,
     d.amount,
     d.direction,
     d.fk_accountid,
     a.account_type,
     a.account_code2,
     a.account_code,
     a.account_name
FROM
     " . MAIN_DB_PREFIX . "iconta_accountingtransaction t left JOIN " . MAIN_DB_PREFIX . "iconta_accountingdebcred d ON t.rowid = d.fk_transaction
     left JOIN " . MAIN_DB_PREFIX . "iconta_chart_master a ON d.fk_accountid = a.rowid";


$sql.= " WHERE t.entity = " . $conf->entity;
if ($date_start && $date_end)
    $sql .= " AND t.datec >= '" . $db->idate($date_start) . "' AND t.datec <= '" . $db->idate($date_end) . "'";

if ($statuses != '')
    $sql .= ' AND t.status IN (' . $statuses . ')';

if (substr($sourcetypelist, 1, -1) != '')
    $sql .= ' AND t.sourcetype IN (' . $sourcetypelist . ')';

//echo $sql;
//@porhacer:Implementar los ordenamientos y paginacion

$sql .= " order by t.number,d.direction,t.tms";
//echo $sql;

$result = $db->query($sql);
if ($result) {


    $num = $db->num_rows($result);
    $i = 0;
    $resligne = array();
    while ($i < $num) {
        $obj = $db->fetch_object($result);

        $key = $obj->number . "-" . substr($obj->label, 0, 50) . "...";
        //la ligne facture
        $insertarr["tid"] = $obj->rowid;
        $insertarr["sourcetype"] = $obj->sourcetype;
        $insertarr["account"] = $obj->account_code . '-' . $obj->account_code2 . '-' . $obj->account_name;
        $insertarr['direction'] = $obj->direction;
        $insertarr['amount'] = $obj->amount;
        $insertarr['status'] = $obj->status;
         $insertarr['datec'] = $obj->datec;
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

$h = 0;
$head[$h][0] = $_SERVER["PHP_SELF"];
$head[$h][1] = $langs->trans("Report");
dol_fiche_head($head, $hselected, $societe->nom);

print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
print '<table width="100%" class="border"><tbody>';

print'<tr><td valign="top" width="194">' . $langs->trans("ReportName") . '</td>
          <td width="581" colspan="3">' . $langs->trans("AccountJournal") . '</td></tr>';
print' <tr><td>' . $langs->trans("ReportPeriod") . '</td>
            <td colspan="3">' . $period . '</td></tr>';
print '<tr><td> ' . $langs->trans("TypeOfTransaction") . ' </td>
    <td colspan="3">' . $sourcelist . '</td> </tr>';
print '<tr><td>' . $langs->trans("TypeOfTransaction") . ' </td>
        <td colspan="3">';
$checked = (substr_count($statuses, '0') != false) ? 'checked="true"' : '';
print '<input name="status[]" type="checkbox" id="status[]" value="0" ' . $checked . '> Borrador ';
$checked = (substr_count($statuses, '1') != false) ? 'checked="true"' : '';
print '<input name="status[]" type="checkbox" id="status[]" value="1" ' . $checked . '>A contabilizar';
$checked = (substr_count($statuses, '2') != false) ? 'checked="true"' : '';
print' <input name="status[]" type="checkbox" id="status[]" value="2" ' . $checked . '>Contabilizado       
    </td></tr>';
print '<tr><td valign="top">' . $langs->trans("ReportDescription") . '</td><td colspan="3">&nbsp;</td></tr>';
print '<tr><td>' . $langs->trans("GeneratedOn") . '</td><td colspan="3">';
print dol_print_date($builddate);
print '</td></tr><tr><td colspan="4" align="center"><input class="button" name="submit" value="Refrescar" type="submit"></td></tr></tbody>';

$i = 0;
print "<table class=\"noborder\" width=\"100%\">";
print "<tr class=\"liste_titre\">";
print "<td>" . $langs->trans("Type") . "</td>
    <td>" . $langs->trans("Status") . "</td>";
print "<td>" . $langs->trans("Date") . "</td>
    <td>" . $langs->trans("Transaction") . "</td>";
print "<td>" . $langs->trans("Account") . "</td>";
print "<td align='right'>" . $langs->trans("Debit") . "</td><td align='right'>" . $langs->trans("Credit") . "</td>";
print "</tr>\n<br>";

$var = true;
$r = '';

//$invoicestatic=new Facture($db);
//print_r($resligne);
foreach ($resligne as $key => $v) {

    $rowspan = count($v) + 1;
    //print $key .'=>'.$v."=>".$rowspan."<br>";
    print "<tr " . $bc[$var] . ">";
    print '<td rowspan="' . $rowspan . '">' . $v[0]['sourcetype'] . "</td>";
    print '<td rowspan="' . $rowspan . '">' . $v[0]['status'] . "</td>";
    print '<td rowspan="' . $rowspan . '">' . $v[0]['datec'] . "</td>";
    print '<td rowspan="' . $rowspan . '"><a href="transaction.php?id=' . $v[0]['tid'] . '">' . $key . "</a></td>";
//	print "</tr>";

    foreach ($v as $k) {
        print "<tr " . $bc[$var] . ">";
        print "<td>" . ($k["direction"] == "-" ? '   ' : '') . $k["account"] . "</td>";
        print "<td align=\"right\">" . ($k["direction"] == "+" ? price($k["amount"]) : '') . "</td>";
        print "<td align=\"right\">" . ($k["direction"] == "-" ? price($k["amount"]) : '') . "</td>";
        print "</tr>";
    }

    $var = !$var;
}
// product


print "</table>";


// End of page
$db->close();
llxFooter();
?>