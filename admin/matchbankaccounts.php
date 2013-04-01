<?php

// in case banks module is activated

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// script setear el año en curso
$res = 0;
if (!$res && file_exists("../../main.inc.php"))
    $res = @include("../../main.inc.php");
if (!$res)
    die("Include of main fails");
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontacharttypes.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontabankmatch.class.php");
require_once(DOL_DOCUMENT_ROOT . "/iconconta/class/icontachartmaster.class.php");

$action = GETPOST('action', 'alpha');

$langs->load("banks");
$langs->load("companies");
$langs->load("iconconta");

$myobject = new Icontacharttypes($db);
$options = $myobject->get_optionlist();

$accobj = new Icontachartmaster($db);

$objbm = new Icontabankmatch($db);
//actions
if ($action == 'linkba') {

    $account_code = $_POST['bankaccode'];
    $accobj->fetchbycode1($account_code);
    if ($accobj->numrows < 1) {
        //create the corresponding account and link it
        $account_type = $_POST['accgroup'];
        $account_name = $_POST['accname'];


        $error = 0;
        $db->begin();
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "iconta_chart_master(";
        $sql.= "account_code,";
        $sql.= "account_name,";
        $sql.= "account_type";
        $sql.= ") VALUES (";

        $sql.= " " . (!isset($account_code) ? 'NULL' : "'" . $db->escape($account_code) . "'") . ",";
        $sql.= " " . (!isset($account_name) ? 'NULL' : "'" . $db->escape($account_name) . "'") . ",";
        $sql.= " " . (!isset($account_type) ? 'NULL' : "'" . $db->escape($account_type) . "'");
        $sql.= ")";


        $result = $db->query($sql);
        if ($result) {
            $fk_id_account = $db->last_insert_id(MAIN_DB_PREFIX . "iconta_chart_master");
            $fk_id_bank_account = $_POST['bankactid'];

            $sql = "INSERT INTO " . MAIN_DB_PREFIX . "iconta_bankmatch SET";
            $sql.= " fk_id_bank_account=" . (isset($fk_id_bank_account) ? $fk_id_bank_account : "null") . ",";
            $sql.= " fk_id_account=" . (isset($fk_id_account) ? $fk_id_account : "null") . ",";
            //$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
            $sql.= " user=" . (isset($user->id) ? $user->id : "null") . "";

            $result2 = $db->query($sql);
            if (!$result2)
                $error++;
        } else {
            $error++;
        }

        if ($error == 0) {

            $db->commit();
            Header("Location: " . $_SERVER["PHP_SELF"]);
            exit;
        } else {
            $db->rollback();
            $mesg = "<font class=\"error\">" . $db->lasterror() . "</font>";
        }
    } else {

        $mesg = "<font class=\"error\">" . $langs->trans("Existe una cuenta contable con el codigo contable registrado para la cuenta de banco, revise sus cuentas contables o cambie el código contable") . "</font>";
    }

    // check for existing accounts accounts
}


//views
llxHeader('', $langs->trans('Parametros de contabilidad'), 'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas', '', 0, 0, $morejs, '', '');
dol_htmloutput_mesg($mesg);
print_fiche_titre("Enlazar Cuentas de Caja y Bancos");
$form = new Form($db);
$hselected = 'enlazar';
require_once("tabs.php");

print '<table width="100%" class=noborder>
  <tr class="liste_titre"   >
    <td width="30%" >Cuenta de bancos </td>
    <td width="18%">Tipo</td>
    <td width="9%">accion</td>
    <td>Enlazar con una cuenta en el Grupo contable </td>
    
  </tr>';

$sql = "SELECT
     ba.rowid AS idbankaccount,
     ba.courant,
     ba.ref,
     ba.label,
     ba.entity,
     ba.bank,
     ba.number,
     ba.account_number,
     ba.url,
     bm.rowid AS bankmatchid,
     bm.fk_id_bank_account,
     bm.fk_id_account,
     bm.tms,
     bm.user,
     m.account_code2,
     m.account_name,
     m.account_type,
     m.inactive,
     m.rowid AS ledgerid,
     m.account_code
FROM
     llx_iconta_bankmatch bm RIGHT OUTER JOIN llx_bank_account ba ON bm.fk_id_bank_account = ba.rowid
     LEFT OUTER JOIN llx_iconta_chart_master m ON bm.fk_id_account = m.rowid";
$sql.= " WHERE entity = " . $conf->entity;
// echo $sql;

dol_syslog("matchbankaccounts.php" . "::fetch sql=" . $sql);
$result = $db->query($sql);
if ($result) {
    $numrows = $db->num_rows($result);
    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
     $obj = $db->fetch_object($result);
            $formcode = '<form name="form' . $i . '" action="' . $_SERVER['PHP_SELF'] . '" method="post"  >';
            $formcode.= '<select name="accgroup">' . $options . '</select>';
            $formcode.= '<input type="hidden" name="action" value="linkba"> 
                        <input type="submit" value="Enlazar">';
            $formcode.= '<input type="hidden" name="bankactid" value="' . $obj->idbankaccount . '">';
            $formcode.= '<input type="hidden" name="accname" value="' . $obj->label . '">';
            $formcode.= '<input type="hidden" name="bankaccode" value="' . $obj->account_number . '">';

            if (!empty($obj->account_code)) {
                $formcode = $obj->account_name;
            }
       
            print '<tr>
                            <td>' . $obj->label . '</td>
                            <td>' . $langs->trans("BankType" . $obj->courant) . '</td>
                            <td width="2%">=&gt;</td>
                            <td width="41%">' . $formcode . '</td>
                            
                          </tr>';
        }
    }
}

print '</table>';
?>
