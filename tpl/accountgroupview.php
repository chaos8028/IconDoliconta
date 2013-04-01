<?php if ($action == 'create') { ?>
<table align="center"> <tr > <td >    
<form action="<?php print $_SERVER['PHP_SELF'] ?>" method="post">
        <div>
            <div style="float: left;">
                <table class="border" cellpadding="2" cellspacing="0" width="100%">
                    <tbody>
                        <tr>
                            <td class="label">ref:</td>
                            <td><input name="ref" size="30" maxlength="50" value=""
                                       type="text"></td>
                        </tr>
                        <tr>
                        <tr>
                            <td class="label">Nombre:</td>
                            <td><input name="name" size="50" maxlength="50" value=""
                                       type="text"></td>
                        </tr>
                        <tr>
                            <td class="label">Subgrupo De:</td>
                            <td>
                                <select  name="parent"  >

                                    <?php
                                    echo $options;
                                    ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Tipo de grupo:</td>
                            <td><span id="_class_id_sel">
                                    <select name="class_id"
                                            class="combo" title="">
                                        <?php foreach($optionsclases as $v){ ?>
                                        <option value="<?php echo $v['rowid'] ?>"><?php echo $v['class_name'] ?></option>
                                 <?php } ?>
                                    </select>
                                </span> </td>
                        </tr>
                        <tr>
                            <td class="label" colspan="2" align="center">
                                <input type="hidden" name="action" value="add">
                                <input type="submit" value="Agregar" name="adbtn" class="button">
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="float: left;" id="mytree"> <?php echo $ultree ?></div>
        </div>
    </form>
        </td></tr></table>
    <script type="text/javascript" language="javascript">
        $(document).ready(function(){
    		
            $("#mytree").jstree({
                "themes" : {

                    "theme" : "apple",

                    "dots" : true,

                    "icons" : true

                },

                "plugins" : ["themes", "html_data", "cookies"]
            });

    	

        });
    </script>


<?php } elseif($action=='edit') { ?> 
    <table class="border" width="100%" >
        <tbody>
            <tr>
                <td width="20%">Ref.</td>
                <td width="30%"> <input name="ref" size="30" maxlength="50" value="<?php echo "ref" ?>"
                                       type="text"><br>
                </td>
                <td rowspan="4" valign="top">
                    <?php
                    print $htmlchilds;
                    ?>

                </td>
            </tr>
            <tr>
                <td width="20%">Nombre del Grupo </td>
                <td><input name="name" size="50" maxlength="50" value="<?php echo $object->name ?>"
                                       type="text"> 
                </td>
            </tr>
            <tr>
                <td width="20%">Contenida En </td>
                <td width="30%">  <select  name="parent"  >

                                    <?php
                                    echo $options;
                                    ?>

                                </select>
                </td>
            </tr>
            <tr>
                <td valign="top">Clase de Cuenta: </td>
                <td valign="top"> <select name="class_id"
                                            class="combo" title="">
                         <?php foreach($optionsclases as $v){ ?>
                                        <option value="<?php echo $v['rowid'] ?>" <?php if($object->class_id==$v['rowid']) echo 'selected' ?>><?php echo $v['class_name'] ?></option>
                                 <?php } ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    
    
    <?php }

else{?>
    <table class="border" width="100%" >
        <tbody>
            <tr>
                <td width="20%">Ref.</td>
                <td width="30%"> <?php echo "ref" ?><br>
                </td>
                <td rowspan="4" valign="top">
                    <?php
                    print $htmlchilds;
                    ?>

                </td>
            </tr>
            <tr>
                <td width="20%">Nombre del Grupo </td>
                <td> <?php echo $object->name ?>
                </td>
            </tr>
            <tr>
                <td width="20%">Contenida En </td>
                <td width="30%"> <?php echo '<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $objectparent->id . '">' . $objectparent->name . '</a>' ?>
                </td>
            </tr>
            <tr>
                <td valign="top">Clase de Cuenta: </td>
                <td valign="top"> <?php echo $object->class_name ?>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
<?php } ?>
