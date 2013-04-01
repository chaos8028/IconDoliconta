
<table width="100%" border="0">
  <?php foreach ($objclass->orderedclasses as $k=>$v){ ?>
    <tr>
        <td><h1><?php echo $v['class_name'] ?></h1></td>
  </tr>
  <tr>
    <td><p>Grupo</p>
    <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td><table class="noborder" width="100%">
      <tr class="liste_titre">
        <td width="7%">Cod1</td>
        <td width="6%">Cod2</td>
        <td width="59%">Cuenta</td>
        <td width="22%">Saldo</td>
        <td width="6%">&nbsp;</td>
      </tr>
      <?php foreach($object->ArrayOfchildsLedger as $v) { ?>
      <tr class="">
        <td><?php echo $v['account_code'] ?></td>
        <td><?php echo $v['account_code2'] ?></td>
        <td><?php echo $v['account_name'] ?></td>
        <td><?php echo "a calcular" ?></td>
        <td><?php echo $v['account_code'] ?></td>
      </tr>
      <?php } ?>
      <tr class="liste_titre">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>subtotal:</td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr> <?php } //end classes loop?>
  
</table>

