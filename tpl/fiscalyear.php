<br>

<form name="formfp" action="<?php print $_SERVER['PHP_SELF'] ?>" method="post">

    <table class="noborder" width="100%">
        <tbody>
            <tr class="liste_titre">
                <td style="width: 258px;">Etiqueta </td>
                <td style="width: 258px;">Fecha de inicio </td>
                <td style="width: 343px;">Fecha Final</td>
                <td style="width: 305px;" colspan="3">&nbsp;</td>
            </tr>
            <tr class="impair">
                <td style="width: 258px;"><input type="text" name="label"></td>
                <td style="width: 258px;"><?php $form->select_date('', 'begin', 0, 0, 0, "formfp"); ?></td>
                <td style="width: 343px;"><?php $form->select_date('', 'end', 0, 0, 0, "formfp"); ?></td>
                <td style="width: 305px;" colspan="3" align="right">
                    <?php if ($action != "edit") { ?>
                        <input value="Añadir" name="actionadd" class="button" type="submit">
                        <input value="add" name="action" type="hidden" />
                    <?php } ?>                </td>
            </tr>
            <tr>
                <td colspan="4" style="width: 343px;">&nbsp;</td>
            </tr>
            <tr class="liste_titre">

                <?php
                print_liste_field_titre($langs->trans('Datestart'), $_SERVER["PHP_SELF"], 't.end', '', $param, '', $sortfield, $sortorder);
                print_liste_field_titre($langs->trans('Dateend'), $_SERVER["PHP_SELF"], 't.start', '', $param, '', $sortfield, $sortorder);
                ?>
                <td colspan="4" class="liste_titre">&nbsp;</td>
            </tr>
            <?php
            $rowcolor = true;
            foreach ($itemlist as $k => $v) {
                ?>
                <tr class="<?php echo $rowcolor ? 'pair' : 'impair' ?>">
                    <?php if ($action == "edit" && $id == $v['rowid']) { ?>
                        <td style="width: 258px;">
                            <input type="hidden" name="inactive" value="<?php echo $v['inactive'] ?>" > 
                            <input name="id" value="<?php echo $v['rowid'] ?>" type="hidden">
                            <input name="begin" type="text" class="flat" id="begin" value="<?php echo $v['class_name'] ?>"> 
                            <input type="hidden" value="update" name="action" /></td>

                        <td style="width: 343px;"><input name="end" type="text" class="flat" id="end" value=""></td>
                        <td colspan="3" align="center">


                            <input value="Actualizar" name="actionadd" class="button" type="submit">
                            <input name="cancelup" type="reset" class="button" id="cancelup" value="Cancelar" /></td>

                    <?php
                    } else { //elseone  
                        ?>


                        <td style="width: 100px;"><?php echo $v['begin'] ?></td>
                        <td style="width: 100px;"><?php echo $v['end'] ?></td>
                        <?php if ($v['closed']) { ?>
                            <td style="width: 100px;" align="center" nowrap="nowrap"><?php print img_picto($langs->trans("Disabled"), 'off'); ?></td>

                        <?php } else { ?> 
                            <td style="width: 305px;" align="center" nowrap="nowrap"><?php print img_picto($langs->trans("Enabled"), 'on'); ?></td>

                        <?php } 
                        if($v['rowid']==$conf->global->ICONTA_CURRENT_FISCALPERIOD_ID ) {
                        ?>
                          <td align="center"><?php print img_picto($langs->trans("Enabled"), 'switch_on'); ?></td>  
                         <?php } else {
                            
                             ?>  
                            <td align="center"><?php if (!$v['closed']) { ?>
                                <a href="<?php echo $_SERVER["PHP_SELF"] . "?action=enable&id=" . $v['rowid'] . "#" . $v['rowid'] ?>">
                                    <?php print img_picto($langs->trans("Enabled"), 'switch_off'); ?></a><?php }?></td> 
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
