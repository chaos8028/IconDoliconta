<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2012 Manuel Munoz <manumunoz@yahoo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       /pagetop.php
 *		\ingroup    iconconta
 *		\brief      Initial Page of mini acounting module
 *					
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');	// If there is no menu to show
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');	// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');		// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs (do not remove DOL_DOCUMENT_ROOT)
//require_once(DOL_DOCUMENT_ROOT."/skeleton/skeleton_class.class.php");
llxHeader('',$langs->trans('Grupo de Cuentas'),'EN:Account_groups|FR:Group_comptes|ES:Grupo de Cuentas');


	print_fiche_titre("Bienvenidos a la seccion Contable");


 dol_htmloutput_mesg($mesg);

?>
<div class="fichecenter">

<div class="fichethirdleft"><table class="noborder" width="100%"><tbody><tr class="liste_titre"><th class="liste_titre" colspan="2">Información</th></tr><tr class="impair"><td nowrap="nowrap">Usuario</td><td><a href="/dolidev/dolibarr/htdocs/user/fiche.php?id=1">SuperAdmin</a></td></tr><tr class="pair"><td nowrap="nowrap">Conexión anterior</td><td>16/08/2012 09:47</td></tr>
</tbody></table>
<br><table class="noborder" width="100%"><tbody><tr class="liste_titre">
      <th class="liste_titre" colspan="3">Ultimas Actividades </th></tr><tr class="impair"><td width="16">&nbsp;</td><td>&nbsp;</td>
<td align="right">&nbsp;</td></tr><tr class="pair"><td width="16">&nbsp;</td><td>&nbsp;</td>
<td align="right">&nbsp;</td></tr><tr class="impair"><td width="16">&nbsp;</td><td>&nbsp;</td>
<td align="right">&nbsp;</td></tr><tr class="pair"><td width="16">&nbsp;</td><td>&nbsp;</td>
<td align="right">&nbsp;</td></tr><tr class="impair"><td width="16">&nbsp;</td><td>&nbsp;</td>
<td align="right">&nbsp;</td></tr><tr class="pair"><td width="16">&nbsp;</td><td>&nbsp;</td>
<td align="right">&nbsp;</td></tr><tr class="impair"><td width="16">&nbsp;</td><td>&nbsp;</td>
<td align="right">&nbsp;</td></tr></tbody></table>
</div><div class="fichetwothirdright">

<div class="ficheaddleft"><table class="noborder" width="100%"><tbody><tr class="liste_titre">
  <th class="liste_titre" colspan="7">Resumen de las cuentas </th></tr><tr class="impair">
    <td colspan="7">Activos</td>
  </tr>
  <tr class="impair">
    <td colspan="7">Pasivos</td>
  </tr>
  <tr class="impair">
    <td colspan="7">Patrimonio</td>
  </tr>
  <tr class="impair">
    <td colspan="7">Ingresos</td>
  </tr>
  <tr class="impair">
    <td colspan="7">Gastos</td>
  </tr>


</tbody></table>
</div></div>
</div>
<?php


// End of page
llxFooter();
$db->close();
?>
