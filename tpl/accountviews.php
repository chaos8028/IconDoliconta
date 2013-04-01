<?php if($action==create || $action=="edit") { ?>
<form action="accountmaster.php" method="post">
    <table width="100%" class="border">
  <tr>
    <td width="19%"><span class="fieldrequired"> Codigo-Contable1 </span></td>
    <td width="81%"><input type="text" name="account_code"  maxlength="15" value="<?php echo $object->account_code?>"/></td>
  </tr>
  <tr>
    <td>Codigo-Contable2</td>
    <td><input type="text" name="account_code2" maxlength="15" value="<?php echo $object->account_code2?>"/></td>
  </tr>
  <tr>
    <td><span class="fieldrequired">Etiqueta</span></td>
    <td><input name="account_name" type="text" size="70" maxlength="60" value="<?php echo $object->account_name?>" /></td>
  </tr>
  <tr>
    <td><span class="fieldrequired">Grupo</span></td>
    <td><select name="account_type">
            <?php print($options); ?> 
    </select>    </td>
  </tr>
  <tr>
    <td>Inactiva?</td>
    <td><input type="checkbox" name="inactive" value="1" <?php if($object->inactive) echo 'checked="true"'?> /></td>
  </tr>
  <tr>
  <td>Descripci&oacute;n:</td>
  <td><textarea name="description" rows="5"><?php echo $object->description?></textarea></td></tr>
  
   <?php if ($action=='create'){ ?>
  <tr>
    <td>Saldo Inicial </td>
    <td><select name="debhbr">
      <option value="debe">debe</option>
      <option value="haber">haber</option>
    </select>
    <input name="saldoinicial" type="text" id="saldoinicial" align="right" /></td>
  </tr>
  <tr>
    <td>Descripci&oacute;n</td>
    <td><textarea name="comments" cols="70" rows="4" maxlength="255"></textarea></td>
  </tr>
<?php } ?>
</table>
  <?php if ($action=='create'){ ?> <br>
<center>
<input class="button" value="Crear Cuenta" type="submit"></center>
<input name="action" type="hidden" value="add" /> <?php } else { 
      print '</div><div class="tabsAction">';
print '<input name="action" type="hidden" value="update" />';
print '<input name="id" type="hidden" value="'.$object->id.'" />';
print '<input class="butAction" value="Actualizar" type="submit">';
print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">'.$langs->trans('Cancel').'</a>';

                    print '</div>';
} ?>

</form>
<?php } 
if (empty($action) && !empty($id))
{

?>

<form action="accountmaster.php" method="post">
    <table width="100%" class="border">
  <tr>
    <td width="19%"><span class="fieldrequired"> Codigo-Contable1 </span></td>
    <td width="81%"><?php echo $object->account_code?></td>
  </tr>
  <tr>
    <td>Codigo-Contable2</td>
    <td><?php echo $object->account_code2?></td>
  </tr>
  <tr>
    <td><span class="fieldrequired">Etiqueta</span></td>
    <td><?php echo $object->account_name?></td>
  </tr>
  <tr>
    <td><span class="fieldrequired">Grupo</span></td>
    <td><?php echo  $urlgroup ?></td>
  </tr>
  <tr>
    <td>Estado:</td>
    <td><?php echo $activesign?></td>
  </tr>
  <tr>
    <td>Descripci&oacute;n:</td>
    <td><?php echo $object->description?></td>
  </tr>
</table>
<?php } ?>