
<?php 
global $AppUI, $tab, $df, $canEdit, $m;

$filter = intval( dPgetParam( $_GET, 'filter', 0 ) );
$order_by = dPgetParam( $_GET, 'order_by', 'SGD_Logs_document_name' );
?>

<table width="100%">
<form name="filterView" method="GET" action="./index.php">
 <input type="hidden" name="m" value="registers">
 <input type="hidden" name="tab" value="2">

<?php
 $sql="select user_id,user_username from users";
 echo "<td>";
 $users=arrayMerge(array("0"=>""),db_loadhashList($sql));
echo $AppUI->_('Filter User: ') ." " .arraySelect( $users, 'filter', 'size="1" class="text" onChange="document.filterView.submit();"',$filter, false );
 echo "</td>";
 echo "<td align=\"right\">";
 echo $AppUI->_('Order by: ') ." ";
 $list=array("SGD_Logs_user_id"=>"User","SGD_Logs_action"=>"Action","SGD_Logs_document_name"=>"Document Name");
 echo arraySelect ($list,'order_by','size="1" class="text" onChange="document.filterView.submit();"',$order_by, false );
?>
</form>
</table>
<table border="0" cellpadding="2" cellspacing="1" width="100%" class="tbl">

<tr>
	<th width="50"><?php echo $AppUI->_('Document');?></th>
	<th width="100"><?php echo $AppUI->_('User');?></th>
	<th width="100"><?php echo $AppUI->_('Date Log');?></th>
	<th width="100"><?php echo $AppUI->_('Action');?></th>
</tr>
<?php
$sql = "
SELECT SGD_Logs.*
FROM SGD_Logs";

if ($filter > 0)
 $sql .= " WHERE SGD_Logs_user_id=$filter";

$sql .= " ORDER BY $order_by";

$logs = db_loadList( $sql );

$sql = "select user_id,user_username from users";
$users = db_loadhashList($sql);
$s = '';
foreach ($logs as $row) {
	$s .= '<tr bgcolor="white" valign="top">';
	$s .= '<td width="100">'.$row["SGD_Logs_document_name"].'</td>';
	$s .= '<td width="100">'.$users[$row["SGD_Logs_user_id"]] .'</td>';
	$s .= '<td width="100">'.$row["SGD_Logs_date"] .'</td>';
	$s .= '<td width="100">'.$row["SGD_Logs_action"] .'</td>';
	$s .= '</tr>';
}
echo $s;
?>
</table>
