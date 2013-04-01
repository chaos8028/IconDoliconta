<?php
 function arrayToTreeul($a, $start = 0, $object,$parentclass) {

// aqui puedes ver si es un grupo hijo
     if($start!=0)
     {
         $parentclass='group-id-'.$start;
     }

        if (isset($a[$start])) {

            foreach ($a[$start] as $v) {
                $result .= '<tr id=group-id-'.$v['id'].' class=child-of-' . $parentclass . '>';
                $result .= '<td><span class="folder">'.$v['name'].'</h4></td>';
                $result .= '<td></td>';
                $result .= '<td></td>';
                $result .= '<td><a href=accountgroup.php?id='.$v['id'].'>ver..</a></td>';    
                $result .= '</tr>';  
                $object->ArrayOfchildsLedger=array();
                $object->GetAccountGroup($v['id']);
               if(count($object->ArrayOfchildsLedger)){

                    foreach($object->ArrayOfchildsLedger as $t) { 

                    $result.='<tr class="child-of-group-id-'.$v['id'].'">';
                 
                    $result.= '<td><span class="file">'.$t['account_name'] .'</span></td>';
                    $result.=  '<td>'. $t['account_code'].'</td>';
                    $result.= '<td>'.$t['account_code2'].'</td>';
                    $result.= ' <td><a href=accountmaster.php?id='.$t['rowid'].'>ver..</a></td>';
                    $result.= ' </tr>'; } 
                  //aquie puede ir total

               }
 // $parentclass='group-id-'.$v['id'];
               
               $result .= arrayToTreeul($a, $v['id'],$object,$parentclass);
            }
        }



        return $result;
    }

require("../main.inc.php");
//require_once(DOL_DOCUMENT_ROOT."/iconconta/class/icontachartmaster.class.php");
require_once(DOL_DOCUMENT_ROOT."/iconconta/class/icontacharttypes.class.php");
require_once(DOL_DOCUMENT_ROOT."/iconconta/class/icontachartclass.class.php");

$langs->load("companies");
$langs->load("other");

// Get parameters
$id		= GETPOST('id','int');
$action		= GETPOST('action','alpha');
//$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	accessforbidden();
}

$objclass= New Icontachartclass($db);
$objtypes = new Icontacharttypes($db);
//$objaccounts = new Icontachartmaster($db);

 /***************************************************
* actions
*
* Put here all code treat interaction
****************************************************/
if ($action == 'add')
{

}
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

$result= 1;
if(!$result){
{
	accessforbidden();
};
}

$morejs = array('/iconconta/js/jquery.treeTable.js', '/iconconta/js/persist-min.js');

llxHeader('', 'Arbol de Cuentas', '', '', '', '', $morejs, '' ,0, 0);
print_fiche_titre("Catálogo de Cuentas Contables");



      $objclass->listArray();
      ?>

<style>
    
.treeTable table thead tr th {
  border: 1px solid #888;
  font-weight: normal;
  padding: .3em 1.67em .1em 1.67em;
  text-align: left;
}

/* Body
 * ------------------------------------------------------------------------- */
table.treeTable  tbody tr td {
  cursor: default;
  padding: .3em 1.5em;
}

table.treeTable tbody tr.even {
  background: #f3f3f3;
}

table.treeTable  tbody tr.odd {
  background: #fff;
}

 table span {
  background-position: center left;
  background-repeat: no-repeat;
  padding: .2em 0 .2em 1.5em;
}

table span.file {
  background-image: url(./images/page_white_text.png);
}

table span.folder {
  background-image: url(./images/folder.png);
}
    
.treeTable tr td .expander {
  cursor: pointer;
  padding: 0;
  zoom: 1; /* IE7 Hack */
}

.treeTable tr td a.expander {
  background-position: left center;
  background-repeat: no-repeat;
  color: #000;
  text-decoration: none;
}

.treeTable tr td  {
  padding-left: 1.5em;
}

.treeTable tr.collapsed td a.expander {
  background-image: url(./images/toggle-expand-dark.png);
}

.treeTable tr.expanded td a.expander {
  background-image: url(./images/toggle-collapse-dark.png);
}

/* jquery.treeTable.sortable
 * ------------------------------------------------------------------------- */
.treeTable tr.selected, .treeTable tr.accept {
  background-color: #3875d7;
}

.treeTable tr.selected a.expander, .treeTable tr.accept a.expander {
  color: #fff;
}

.treeTable tr.collapsed.selected td a.expander, .treeTable tr.collapsed.accept td a.expander {
  background-image: url(./images/toggle-expand-light.png);
}

.treeTable tr.expanded.selected td a.expander, .treeTable tr.expanded.accept td a.expander {
  background-image: url(./images/toggle-collapse-light.png);
}

.treeTable .ui-draggable-dragging {
  color: #000;
  z-index: 1;
}

/* Layout helper taken from jQuery UI. This way I don't have to require the
 * full jQuery UI CSS to be loaded. */
.ui-helper-hidden { display: none; }

tr.over td {
    background-color: #bcd4ec;
}
</style>
<script type="text/javascript">

$(document).ready(function()  {


$(".noborder  tr").mouseover(function(){$(this).addClass("over");});

$(".noborder  tr").mouseout(function(){$(this).removeClass("over");});

$(".noborder tr:even").addClass("pair");

$(".noborder tr:odd").addClass("impair");




$("#chartofaccounts").treeTable(
{
    clickableNodeNames:true,
    expandable: true,
    persist: true    
    
});



});

</script>
<table width="100%"  id="chartofaccounts" class="noborder" >
   <thead> <tr class="liste_titre">
         <td>Cuenta o Grupo</td>
        <td>Codigo1</td>
        <td>Codigo2</td>
        <td>Accion</td>
    </tr></thead>
   <tbody>
  <?php foreach ($objclass->orderedclasses as $k=>$v){ ?>
    <tr id="class-id-<?php echo $v['rowid']; ?>" >
        <td><span class="folder"><?php echo $v['class_name'] ?></span></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
  <?php 
$r=$objclass->childTreeArray($v['rowid']);
//print_r($r);
//die(1);
$html=arrayToTreeul($r,0,$objtypes,'class-id-'.$v['rowid']);
    echo $html;
   // print_r($arrayofclasses);


    
    
    ?>
        
        
  
 <?php } //end classes loop?>
   </tbody>
</table>



<?php
// End of page
llxFooter();
$db->close();

  ?>
