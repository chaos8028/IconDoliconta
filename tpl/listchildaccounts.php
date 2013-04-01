<table class="noborder" width="100%">
<tr class="liste_titre">
  <td width="7%">Cod1</td>
  <td width="6%">Cod2</td>
  <td width="59%">Cuenta</td>
  <td width="22%">Saldo</td>
  <td width="6%">&nbsp;</td>
</tr>
<?php foreach($object->ArrayOfchildsLedger as $v) { ?>
<tr class="">
  <td><a href="accountmaster.php?id=<?php echo $v['rowid'] ?>"><?php echo $v['account_code'] ?></a></td>
  <td><a href="accountmaster.php?id=<?php echo $v['rowid'] ?>"><?php echo $v['account_code2'] ?></a></td>
  <td><a href="accountmaster.php?id=<?php echo $v['rowid'] ?>"><?php echo $v['account_name'] ?></a></td>
  <td><?php echo "a calcular" ?></td>
  <td><?php echo $v['account_code'] ?></td>
</tr><?php } ?>
<tr class="liste_titre">
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>Total:</td>
  <td>&nbsp;</td>
</tr>
   </table>
