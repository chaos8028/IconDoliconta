<?php 
$limite = 10;
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

$sql .= " AND a.rowid = " . $object->id;

//echo $sql;
//@porhacer:Implementar los ordenamientos y paginacion

$sql .= " order by t.datec DESC ";


$sql.= ' LIMIT 0, '.$limite;

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

if ($num <=0) {
     print 'No hay transacciones en esta cuenta!';
exit(0);
     
}
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
