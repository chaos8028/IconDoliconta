<?php
require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingtransaction.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontaaccountingdebcred.class.php");
$objtrans = new Icontaaccountingtransaction($db);
 $morejs = array('/iconconta/js/jquery.validate.min.js');
 llxHeader('', 'Listado de Pagos a Prestamo Pendientes de contabilizar', '', '', '', '', $morejs, '', 0, 0);


$html = new Form($db);

print '<table class="noborder" align="center"><tr class="liste_titre">';
print '<td> Pago </td>';
print '<td> Status </td>';
print '<td> Transaccion </td></tr>';

foreach($_POST['posta'] as $p)
{
    print '<tr><td>'.$p. '</td>';
    $res= $objtrans->registerloanpayment($p, $user);
    if ($res>0) print '<td>'.  img_picto("OK", '', 'check'). '</td>';
    else print '<td><div style="color:red">'.$objtrans->error. '</div></td>';
     print '<td>'.'---'. '</td></tr>';
}
print '</table>';
$db->close();
llxFooter();
?>
