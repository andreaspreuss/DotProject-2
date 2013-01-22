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
	$AppUI->redirect('m=risks&a=view&risk_id=' . $risk_id);
}

?>
<form name="editFrm" action="?m=risks&a=vw_note_add" method="post">
	<input type="hidden" name="risk_id" value="<?php echo $risk_id;?>" />

<table>
<tr>
	<td align="right" valign="top"><?php echo $AppUI->_('Note');?>:</td>
	<td>
		<textarea name="risk_note_description" class="textarea" cols="50" rows="6"></textarea>
	</td>
	<td valign="top"><input class="text" type="submit" name="note" value="<?php echo $AppUI->_('Add note'); ?>" /></td>
</tr>
</table>
</form>
