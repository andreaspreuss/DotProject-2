
<?php 
global $AppUI, $tab, $df, $canEdit, $m;

$filter = intval( dPgetParam( $_GET, 'filter', 0 ) );
$order_by = dPgetParam( $_GET, 'order_by', 'register_start_date' );
?>

<table width="100%">
<form name="filterView" method="GET" action="./index.php">
 <input type="hidden" name="m" value="registers">

<?php
 echo "<td>";
 $register_format=dPgetsysVal('RegisterCode');
 echo $AppUI->_('Filter: ') ." " .arraySelect( $register_format, 'filter', 'size="1" class="text" onChange="document.filterView.submit();"',$filter, false );
 echo "</td>";
 echo "<td align=\"right\">";
 echo $AppUI->_('Order by: ') ." ";
 $list=array("register_start_date"=>"Start Date","register_code"=>"Code","register_client"=>"Client","register_project"=>"Project");
 echo arraySelect ($list,'order_by','size="1" class="text" onChange="document.filterView.submit();"',$order_by, false );
/* echo "<select name=\"oder_by\" class=\"text\">";
 echo "<option></option>";
 echo "</select>";
 echo "<td>";*/
?>
</form>
</table>
<table border="0" cellpadding="2" cellspacing="1" width="100%" class="tbl">

<tr>
	<th width="50"><?php echo $AppUI->_('Formato');?></th>
	<th width="100"><?php echo $AppUI->_('Codigo');?></th>
	<th width="100"><?php echo $AppUI->_('Fecha Inicio');?></th>
	<th width="100"><?php echo $AppUI->_('Fecha Finalizacion');?></th>
	<th width="100%"><?php echo $AppUI->_('Observaciones');?></th>
	<th width="100"><?php echo $AppUI->_('Responsable');?></th>
	<th width="100"><?php echo $AppUI->_('Cliente');?></th>
	<th width="100"><?php echo $AppUI->_('Proyecto');?></th>
	<th width="100"><?php echo $AppUI->_('Referencia');?></th>
	<th width="100"><?php echo $AppUI->_('Estado');?></th>
</tr>
<?php
$register_state=dPgetsysVal('RegisterState');

$sql = "
SELECT registers.*
FROM registers";

if ($filter > 0)
 $sql .= " WHERE register_format=$filter";

$sql .= " ORDER BY $order_by";

$logs = db_loadList( $sql );

$sql = "select user_id,user_username from users";
$users = db_loadhashList($sql);
$sql = "select project_id, project_name from projects";
$projects = db_loadhashList($sql);
$sql = "select contact_id,contact_company from contacts";
$contacts = db_loadhashList($sql);
$s = '';
foreach ($logs as $row) {
	$s .= '<tr bgcolor="white" valign="top">';
/*	$s .= "\n\t<td>";
	if (!getDenyEdit($m, $task_id) ) {
		$s .= "\n\t\t<a href=\"?m=tasks&a=view&task_id=$task_id&tab=1&task_log_id=".@$row['register_id']."\">"
			. "\n\t\t\t". dPshowImage( './images/icons/stock_edit-16.png', 16, 16, '' )
			. "\n\t\t</a>";
	}
	$s .= "\n\t</td>";*/
	$s .= '<td width="100">'.$register_format[$row["register_format"]].'</td>';
	$s .= '<td nowrap="nowrap">'.$row["register_code"] .'</td>';
	$s .= '<td width="100">'.@$row["register_start_date"] .'</td>';
	$s .= '<td width="100">'.@$row["register_end_date"] . '</td>';
	$s .= '<td width="100">'.@$row["register_description"].'</td>';
	$s .= '<td width="100">'.($row["register_owner"] ? $users[$row["register_owner"]] : null) .'</td>';
	$s .= '<td width="100">'.($row["register_client"] ? $contacts[$row["register_client"]] : null).'</td>';
	$s .= '<td width="100">'.($row["register_project"] ? $projects[$row["register_project"]] : null) .'</td>';
	$s .= '<td width="100">'.($row["register_ref_id"] ? $row["register_ref_id"] : null).'</td>';
	$s .= '<td width="100">'.($row["register_state"] ? $register_state[$row["register_state"]] : null).'</td>';
/*	$s .= '<td width="100">';

// dylan_cuthbert: auto-transation system in-progress, leave these lines
	$transbrk = "\n[translation]\n";
	$descrip = str_replace( "\n", "<br />", $row['register_description'] );
	$tranpos = strpos( $descrip, str_replace( "\n", "<br />", $transbrk ) );
	if ( $tranpos === false) $s .= $descrip;
	else
	{
		$descrip = substr( $descrip, 0, $tranpos );
		$tranpos = strpos( $row['register_description'], $transbrk );
		$transla = substr( $row['register_description'], $tranpos + strlen( $transbrk ) );
		$transla = trim( str_replace( "'", '"', $transla ) );
		$s .= $descrip."<div style='font-weight: bold; text-align: right'><a title='$transla' class='hilite'>[".$AppUI->_("translation")."]</a></div>";
	}
// end auto-translation code
			
	$s .= '</td>';*/
/*	$s .= "\n\t<td>";
	if ($canEdit) {
		$s .= "\n\t\t<a href=\"javascript:delIt2({$row['task_log_id']});\" title=\"".$AppUI->_('delete log')."\">"
			. "\n\t\t\t". dPshowImage( './images/icons/stock_delete-16.png', 16, 16, '' )
			. "\n\t\t</a>";
	}
	$s .= "\n\t</td>";*/
	$s .= '</tr>';
}
echo $s;
?>
</table>
