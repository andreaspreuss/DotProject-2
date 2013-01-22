<?php
/*
Copyright (c) 2005 CaseySoftware, LLC <info@caseysoftware.com>
Initial Work:	Richard Thompson - Belfast, Northern Ireland
Developers:		Keith Casey - Washington, DC keith@caseysoftware.com
				Ivan Peevski - Adelaide, Australia cyberhorse@users.sourceforge.net
*/
GLOBAL $AppUI;

// check permissions
$perms =& $AppUI->acl();
$canEdit = $perms->checkModuleItem( 'risks', 'edit', $risk_id );
if (! $canEdit)
	$AppUI->redirect("m=public&a=access_denied");

$viewNotes = false;
$addNotes = false;
$risk_id = intval( dPgetParam( $_REQUEST, 'risk_id', 0 ) );

$note = dPgetParam($_POST, 'note', false);

if ($note) {
	$q = new DBQuery();
	$q->addTable('risk_notes');
	$q->addInsert('risk_note_risk', $risk_id);
	$q->addInsert('risk_note_creator', $AppUI->user_id);
	$q->addInsert('risk_note_date', 'NOW()', false, true);
	$q->addInsert('risk_note_description', $_POST['risk_note_description']);
	$q->exec();
	$AppUI->setMsg('Note added', UI_MSG_OK);
	$AppUI->redirect();
}

$q = new DBQuery();
$q->clear();
$q->addQuery("*, CONCAT(contact_first_name, ' ', contact_last_name) as risk_note_owner");
$q->addTable('risk_notes');
$q->leftJoin('users', 'u', 'risk_note_creator = user_id');
$q->leftJoin('contacts', 'c', 'user_contact = contact_id');
$q->addWhere('risk_note_risk = ' . $risk_id);
$notes = $q->loadList();

echo '
<table cellpadding="5" width="100%" class="tbl">
<tr>
	<th>'.$AppUI->_('Date').'</th>
	<th>'.$AppUI->_('User').'</th>
	<th>'.$AppUI->_('Note').'</th>
</tr>';
foreach($notes as $n)
{
	echo '
<tr>
	<td nowrap>' . $n['risk_note_date'] . '</td>
	<td nowrap>' . $n['risk_note_owner'] . '</td>
	<td width="100%">' . $n['risk_note_description'] . '</td>
</tr>';
}
echo '</table>';
?>
