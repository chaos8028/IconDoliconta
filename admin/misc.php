<?php
function html_tableparam($name,$url,$title,$desc,$fields,$act)
{

print '<form name="'.$name.'" action="' . $url . '" method="post" enctype="multipart/form-data">';
print '<table id="tablelines" class="noborder" width="100%">';
print '<tr><td colspan="3"><br>'.$title.'</td></tr>';
print '<tr class="liste_titre nodrag nodrop">
            <td>Parametro</td>
            <td>valor</td>
            <td></td></tr>';
print '<tr>
            <td>'.$desc.'</td>
            <td>'.$fields.'</td>
            <td><input class="button" type="submit" value="cambiar">
            <input type="hidden" name="action"  value="'.$act.'"></td></tr>';
print '</table>';
print '</form>';
   
}
// script setear el año en curso
$res = 0;
if (!$res && file_exists("../../main.inc.php"))
    $res = @include("../../main.inc.php");
if (!$res)
    die("Include of main fails");
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
require_once(DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontafiscalyear.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontacharttypes.class.php");


$action = GETPOST('action', 'alpha');
$action = GETPOST('action','alpha');
$value = GETPOST('value','alpha');
$label = GETPOST('label','alpha');
$scandir = GETPOST('scandir','alpha');
$type='vouchercheque';
// actions

if ($action == "setfiscalperiod") {
   
    $value = trim($_POST['selectedfiscalperiod']);
    if (dolibarr_set_const($db, "ICONTA_CURRENT_FISCALPERIOD_ID", $value, 'chaine', 0, '', $conf->entity)) {
          $conf->global->ICONTA_CURRENT_FISCALPERIOD_ID = $value;
    }
}
//Actions


if ($action == 'set')
{
	$ret = addDocumentModel($value, $type, $label, $scandir);
}

else if ($action == 'del')
{
	$ret = delDocumentModel($value, $type);
	if ($ret > 0)
	{
        if ($conf->global->VOUCHERCHEQUE_ADDON_PDF == "$value") dolibarr_del_const($db, 'VOUCHERCHEQUE_ADDON_PDF',$conf->entity);
	}
}

else if ($action == 'setdoc')
{
    if (dolibarr_set_const($db, "VOUCHERCHEQUE_ADDON_PDF",$value,'chaine',0,'',$conf->entity))
	{
		$conf->global->VOUCHERCHEQUE_ADDON_PDF = $value;
	}

	// On active le modele
	$ret = delDocumentModel($value, $type);
	if ($ret > 0)
	{
		$ret = addDocumentModel($value, $type, $label, $scandir);
	}
}

else if ($action == 'setmod')
{
	// TODO Verifier si module numerotation choisi peut etre active
	// par appel methode canBeActivated

	dolibarr_set_const($db, "VOUCHERCHEQUE_ADDON",$value,'chaine',0,'',$conf->entity);
}




llxHeader('', $langs->trans('Parametros de contabilidad'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas', '', 0, 0, $morejs, '', '');
dol_htmloutput_mesg($mesg);
print_fiche_titre("Parametos contables");
$hselected = 'miscelanea';
require_once("tabs.php");

$form = new Form($db);


$dirmodels=array_merge(array('/'),(array) $conf->modules_parts['models']);
//print_r($dirmodels);

print '<table class="noborder"><tbody>';

// fiscal Period setting
$myobject = new Icontafiscalyear($db);
$myobject->fetchArray(false);
foreach ($myobject->arrayFetched as $v) {
    $selectarray[$v['rowid']] = $v['begin'] . '-' . $v['end'];
}
print '<tr><td>';
print '<form name="formfiscalyear" action="' . $_SERVER["PHP_SELF"] . '" method="post" enctype="multipart/form-data">';
print '<table id="tablelines" class="noborder" width="100%">';
print '<tr><td colspan="3"><br> Periodo fiscal Vigente</td></tr>';
print '<tr class="liste_titre nodrag nodrop">
            <td>Parametro</td>
            <td>valor</td>
            <td></td></tr>';
print '<tr>
            <td style="word-break: break-all">Seleccione un perido fical vigente: la seleccion de este periodo es determinante para el registro de transacciones contables</td>
            <td>' . $form->selectarray('selectedfiscalperiod', $selectarray, $conf->global->ICONTA_CURRENT_FISCALPERIOD_ID,1) . '</td>
            <td><input class="button" type="submit" value="cambiar">
            <input type="hidden" name="action"  value="setfiscalperiod"></td></tr>';
print '</table>';
print '</form>';
print '</td></tr>';

// end fiscal period table

// Debtor account group setting
$myobject = new Icontacharttypes($db);
$options= $myobject->get_optionlist();
print '<tr><td>';
print '<form name="formfiscalyear" action="' . $_SERVER["PHP_SELF"] . '" method="post" enctype="multipart/form-data">';
print '<table id="tablelines" class="noborder">';
print '<tr><td colspan="3"><br>Grupo de Cuentas para cuentas por cobrar Clientes</td></tr>';
print '<tr class="liste_titre nodrag nodrop">
            <td>Parametro</td>
            <td>valor</td>
            <td></td></tr>';
print '<tr>
            <td>Seleccione un grupo para las cuentas por cobrar: en este grupo se generar una cuenta deudora por cada tercer0 (cliente) registrado en el modulo de terceros, los codigos contables se generaran de acuerdo con la configuración del modulo de terceros</td>
            <td><select name="account_type">' . $options . '</select></td>
            <td><input class="button" type="submit" value="cambiar">
            <input type="hidden" name="action"  value="setfiscalperiod"></td></tr>';
print '</table>';
print '</form>';
print '</td></tr>';

/*
 * Modeles de documents
 */
print '<br>';
print '<tr><td>';
print_titre($langs->trans("Modelo de Voucher-cheque"));

// Defini tableau def de modele propal
$def = array();
$sql = "SELECT nom";
$sql.= " FROM ".MAIN_DB_PREFIX."document_model";
$sql.= " WHERE type = 'vouchercheque'";
$sql.= " AND entity = ".$conf->entity;
$resql=$db->query($sql);
if ($resql)
{
	$i = 0;
	$num_rows=$db->num_rows($resql);
	while ($i < $num_rows)
	{
		$array = $db->fetch_array($resql);
		array_push($def, $array[0]);
		$i++;
	}
}
else
{
	dol_print_error($db);
}


print "<table class=\"noborder\" width=\"100%\">\n";
print "<tr class=\"liste_titre\">\n";
print "  <td width=\"140\">".$langs->trans("Name")."</td>\n";
print "  <td>".$langs->trans("Description")."</td>\n";
print '<td align="center" width="40">'.$langs->trans("Status")."</td>\n";
print '<td align="center" width="40">'.$langs->trans("Default")."</td>\n";
print '<td align="center" width="40">'.$langs->trans("Infos").'</td>';
print '<td align="center" width="40">'.$langs->trans("Preview").'</td>';
print "</tr>\n";

clearstatcache();

$var=true;
foreach ($dirmodels as $reldir)
{
    foreach (array('','/doc') as $valdir)
    {
    	$dir = dol_buildpath($reldir."core/modules/vouchercheque".$valdir);

        if (is_dir($dir))
        {
            $handle=opendir($dir);
            if (is_resource($handle))
            {
                while (($file = readdir($handle))!==false)
                {
                    $filelist[]=$file;
                }
                closedir($handle);
                arsort($filelist);

                foreach($filelist as $file)
                {
                    if (preg_match('/\.modules\.php$/i',$file) && preg_match('/^(pdf_|doc_)/',$file))
                    {
                    	if (file_exists($dir.'/'.$file))
                    	{
                    		$name = substr($file, 4, dol_strlen($file) -16);
	                        $classname = substr($file, 0, dol_strlen($file) -12);

	                        require_once($dir.'/'.$file);
	                        $module = new $classname($db);

	                        $modulequalified=1;
	                        if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) $modulequalified=0;
	                        if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) $modulequalified=0;

	                        if ($modulequalified)
	                        {
	                            $var = !$var;
	                            print '<tr '.$bc[$var].'><td width="100">';
	                            print (empty($module->name)?$name:$module->name);
	                            print "</td><td>\n";
	                            if (method_exists($module,'info')) print $module->info($langs);
	                            else print $module->description;
	                            print '</td>';

	                            // Active
	                            if (in_array($name, $def))
	                            {
	                            	print '<td align="center">'."\n";
	                            	print '<a href="'.$_SERVER["PHP_SELF"].'?action=del&amp;value='.$name.'">';
	                            	print img_picto($langs->trans("Enabled"),'switch_on');
	                            	print '</a>';
	                            	print '</td>';
	                            }
	                            else
	                            {
	                                print "<td align=\"center\">\n";
	                                print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;value='.$name.'&amp;scandir='.$module->scandir.'&amp;label='.urlencode($module->name).'">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
	                                print "</td>";
	                            }

	                            // Defaut
	                            print "<td align=\"center\">";
	                            if ($conf->global->VOUCHERCHEQUE_ADDON_PDF == "$name")
	                            {
	                                print img_picto($langs->trans("Default"),'on');
	                            }
	                            else
	                            {
	                                print '<a href="'.$_SERVER["PHP_SELF"].'?action=setdoc&amp;value='.$name.'&amp;scandir='.$module->scandir.'&amp;label='.urlencode($module->name).'" alt="'.$langs->trans("Default").'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
	                            }
	                            print '</td>';

	                           // Info
	                            $htmltooltip =    ''.$langs->trans("Name").': '.$module->name;
	                            $htmltooltip.='<br>'.$langs->trans("Type").': '.($module->type?$module->type:$langs->trans("Unknown"));
	                            if ($module->type == 'pdf')
	                            {
	                                $htmltooltip.='<br>'.$langs->trans("Width").'/'.$langs->trans("Height").': '.$module->page_largeur.'/'.$module->page_hauteur;
	                            }
								$htmltooltip.='<br><br><u>'.$langs->trans("FeaturesSupported").':</u>';
								$htmltooltip.='<br>'.$langs->trans("Logo").': '.yn($module->option_logo,1,1);
								$htmltooltip.='<br>'.$langs->trans("PaymentMode").': '.yn($module->option_modereg,1,1);
								$htmltooltip.='<br>'.$langs->trans("PaymentConditions").': '.yn($module->option_condreg,1,1);
								$htmltooltip.='<br>'.$langs->trans("MultiLanguage").': '.yn($module->option_multilang,1,1);
								//$htmltooltip.='<br>'.$langs->trans("Escompte").': '.yn($module->option_escompte,1,1);
								//$htmltooltip.='<br>'.$langs->trans("CreditNote").': '.yn($module->option_credit_note,1,1);
								$htmltooltip.='<br>'.$langs->trans("WatermarkOnDraftProposal").': '.yn($module->option_draft_watermark,1,1);


	                            print '<td align="center">';
	                            print $form->textwithpicto('',$htmltooltip,1,0);
	                            print '</td>';

	                            // Preview
	                            print '<td align="center">';
	                            if ($module->type == 'pdf')
	                            {
	                                print '<a href="'.$_SERVER["PHP_SELF"].'?action=specimen&module='.$name.'">'.img_object($langs->trans("Preview"),'bill').'</a>';
	                            }
	                            else
	                            {
	                                print img_object($langs->trans("PreviewNotAvailable"),'generic');
	                            }
	                            print '</td>';

	                            print "</tr>\n";
	                        }
                    	}
                    }
                }
            }
        }
    }
}

print '</table>';
print '</td></tr>';

print '<br>';

print '</tbody></table>';
?>
