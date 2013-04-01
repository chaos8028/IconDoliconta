<form action="<?php print $_SERVER['PHP_SELF'] ?>" method="post">

    <table class="noborder" width="100%">
        <tbody>
            <tr class="liste_titre">
                <td style="width: 258px;">Clase*</td>
                <td style="width: 343px;">Tipo/orden<br>                </td>
                <td style="width: 305px;" colspan="3">&nbsp;</td>
            </tr><tr class="impair"><td style="width: 258px;"><input class="flat" value="" size="30" name="class_name" type="text"></td><td style="width: 343px;">
                    <select name="ctype">
                        <option value="RA">Real-Acreedora</option>
                        <option value="RD">Real-Deudora</option>
                        <option value="NA">Nominal-Acreedora</option>
                        <option value="ND">Nominal-Deudora</option>
                        <option value="OA">De Orden- Acreedora</option>
                        <option value="OD">De Orden Deudora</option>
                    </select>
/
<input class="flat" value="" size="3" name="ordernum" type="text">
                </td>

                <td style="width: 305px;" colspan="3" align="right">
                    <?php if ($action != "edit") { ?>
                        <input value="Añadir" name="actionadd" class="button" type="submit">
                        <input value="add" name="action" type="hidden" />
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td style="width: 343px;" colspan="4">*Etiqueta de la clasificación de la cuenta .</td>
            </tr><tr><td style="width: 343px;" colspan="4">&nbsp;</td></tr>
            <tr class="liste_titre">

                <?php
                print_liste_field_titre($langs->trans('Class'), $_SERVER["PHP_SELF"], 'p.ref', '', $param, '', $sortfield, $sortorder);
                print_liste_field_titre($langs->trans('Type'), $_SERVER["PHP_SELF"], 's.nom', '', $param, '', $sortfield, $sortorder);
                ?>



                <td colspan="3" class="liste_titre">&nbsp;</td></tr>



            <?php
            $rowcolor = true;
            foreach ($itemlist as $k => $v) {
                ?>
                <tr class="<?php echo $rowcolor ? 'pair' : 'impair' ?>">
    <?php if ($action == "edit" && $id == $v['rowid']) { ?>
                        <td style="width: 258px;">
                            <input type="hidden" name="inactive" value="<?php echo $v['inactive'] ?>" > 
                            <input name="id" value="<?php echo $v['rowid'] ?>" type="hidden">
                            <input class="flat" value="<?php echo $v['class_name'] ?>" size="30" name="class_name" type="text"> 
                            <input type="hidden" value="update" name="action" /></td>

                        <td style="width: 343px;"> <select name="ctype">
                                <option value="RA" <?php if ($v['ctype'] == 'RA')
            echo "selected"
            ?>>Real-Acreedora</option>
                                <option value="RD" <?php if ($v['ctype'] == 'RD')
            echo " selected"
            ?>>Real-Deudora</option>
                                <option value="NA" <?php if ($v['ctype'] == 'NA')
                                echo "selected"
                                ?>>Nominal-Acreedora</option>
                                <option value="ND" <?php if ($v['ctype'] == 'ND')
                                echo "selected"
            ?>>Nominal-Deudora</option>
                                <option value="OA" <?php if ($v['ctype'] == 'OA')
                                echo "selected"
                                ?>>De Orden- Acreedora</option>
                                <option value="OD" <?php if ($v['ctype'] == 'OD')
                                echo "selected"
                                ?>>De Orden Deudora</option>
                            </select>
                        /
<input class="flat" value="" size="3" name="ordernum" type="text"></td>
                        <td colspan="3" align="center">


                            <input value="Actualizar" name="actionadd" class="button" type="submit">
                            <input name="cancelup" type="reset" class="button" id="cancelup" value="Cancelar" /></td>

    <?php }
    else { //elseone  
        ?>


                        <td style="width: 258px;"><?php echo $v['class_name'] ?></td>
                        <td style="width: 343px;"><?php echo $v['ctype'] .'-'.$v['ordernum'] ?></td>
        <?php if ($v['inactive']) { ?>
                            <td style="width: 305px;" align="center" nowrap="nowrap"><a href="<?php echo $_SERVER["PHP_SELF"] . "?action=enable&id=" . $v['rowid'] ?>"><?php print img_picto($langs->trans("Disabled"), 'switch_off'); ?></a></td>

                    <?php } else { ?> 
                            <td style="width: 305px;" align="center" nowrap="nowrap"><a href="<?php echo $_SERVER["PHP_SELF"] . "?action=disable&id=" . $v['rowid'] ?>"><?php print img_picto($langs->trans("Enabled"), 'switch_on'); ?></a></td>

                    <?php } ?>
                        <td align="center"><a href="<?php echo $_SERVER["PHP_SELF"] . "?action=edit&id=" . $v['rowid'] . "#" . $v['rowid'] ?>"><?php print img_picto($langs->trans("Edit"), 'edit'); ?></a></td>
                        <td align="center"><a href="<?php echo $_SERVER["PHP_SELF"] . "?action=delete&id=" . $v['rowid'] ?>"><?php print img_picto($langs->trans("Delete"), 'delete'); ?></a></td>

    <?php } ?>  </tr>

    <?php
    $rowcolor = !$rowcolor;
} //end for 
?> 
        </tbody></table>
</form>
