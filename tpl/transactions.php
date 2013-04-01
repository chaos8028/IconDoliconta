
<?php if ($action == 'create') { ?>

    <table class="border" width="100%">
        <tbody>
              <tr>
                <td style="vertical-align: top; width: 281px;">Tipo de entrada:<br>
                </td>
                <td style="vertical-align: top; width: 681px;">
                    <select  name="sourcetype">
                        <option value="0">Asiento comun </option>
                        <?php foreach ($listarr as $k) {?>
                         <option value="<?php echo $k['label'] ?>"><?php echo $k['label'] ?></option>
<?php } ?> </select>
                </td>
            </tr>
             <tr>
                <td style="vertical-align: top; width: 281px;">Número:<br>
                </td>
                <td style="vertical-align: top; width: 681px;"><input
                    readonly="true" name="number" value="<?php echo $numtrans ?>"><br>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 281px;">Fecha del Asiento<br>
                </td>
                <td style="vertical-align: top; width: 681px;">
                    <?php $form->select_date('', 'datec', 0, 0, 0, "formtrans"); ?>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 281px;">Descripción<br>
                </td>
                <td style="vertical-align: top; width: 681px;"><textarea
                        cols="100" rows="3" name="label"></textarea><br>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: center;">
        <p>&nbsp;    </p>
        <p>
            <input name="Create"
                   value="Crear asiento" 
                   type="submit"/>
            &nbsp; 
<!--            <input name="Cancel"
                   value="Cancelar" 
                   type="reset" />-->
            <br>
        </p>
    </div>

<?php } else { ?>

    <table class="border" width="100%">
        <tbody>
            <tr>
                <td width="10%">Numero</td>
                <td width="76%"><?php echo $object->number ?></td>
                <td width="14%">&nbsp;</td>
            </tr>
            <tr>
                <td>Fecha</td>
                <td><?php print strftime('%Y-%m-%d', $object->datec); ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><div>
                        <div>Descripcion</div>
                        <div><a href=" "> <?php echo img_picto('Editar Descripcion', 'edit')?></a></div>
                    </div>
                </td>
                <td><?php echo $object->label ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Status</td>
                <td><?php echo $object->LibStatut($object->status, 3) . $langs->trans($object->labelstatut[$object->status]) ?></td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>


   
    </div>
</div>
    <?php
    print '<div class="tabsAction">';

    // Valid

    
    print '<a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=validate">' . $langs->trans('Validate') . '</a>';

    // Edit

     print '<a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=edit">' . $langs->trans('Modify') . '</a>';
    print '<a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&amp;action=delete">' . $langs->trans('Delete') . '</a>';

    print '</div>';
?><div class="fiche">

 <p>Detalle de la transaccion</p>


    <form name="formtrans" action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id ?>" method="post" enctype="multipart/form-data">
        <table id="tablelines" class="noborder noshadow" width="100%">

            <tr class="liste_titre nodrag nodrop">
                <td>Codigo de cuenta / Cuenta </td>
                <td width="50" align="right">Debe</td>
                <td width="80" align="right">Haber</td>
                <td align="right">accion  </td>
            <tr class="">
                <td><input name="codcta" type="text" id="codcta" size="10" value="<?php print GETPOST('codcta', 'alpha') ?>" />
                    <input name="cta" type="text" id="cta" size="55"   value="<?php print stripslashes($_POST['cta']) ?>" />
                    <input name="accountid" type="hidden" id="accountid" value="<?php print stripslashes($_POST['accountid']) ?>"  />
                    <input name="action" type="hidden" value="addline" /></td>
                <td align="right"><input name="debe" type="text" id="debe" size="10"  value="<?php print stripslashes($_POST['debe']) ?>" /></td>
                <td align="right"><input name="haber" type="text" id="haber" size="10" value="<?php print stripslashes($_POST['haber']) ?>" /></td>
                <td align="right"><input type="submit" name="Submit" value="Agregar" /></td>
                <?php
                $parity = false;
                foreach ($object->child as $key => $value) {

                    $indentation = ($value['direction'] == '-') ? 'style="text-indent:30px"' : ''
                    ?>
                <tr class="<?php echo ($parity == true) ? 'pair' : 'impair' ?>">
                    <td <?php echo $indentation ?> ><?php echo $value['account_code'] . '-' . $value['account_code2'] . '-' . $value['account_name'] ?></td>
                    <td align="right"><?php echo ($value['direction'] == '+') ? price($value['amount']) : '' ?></td>
                    <td align="right"><?php echo ($value['direction'] == '-') ? price($value['amount']) : '' ?></td>
                    <td align="right"><a href="transaction.php?idline=<?php echo $value['rowid'] ?>&action=deleteline"><?php echo img_delete() ?></a></td></tr>
                <?php
                $parity = !$parity;
            }
            ?>
        </table>
    </form>
</div
<?php } ?>