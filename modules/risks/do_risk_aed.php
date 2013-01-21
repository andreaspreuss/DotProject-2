<?php 
/*
Copyright (c) 2005 CaseySoftware, LLC <info@caseysoftware.com> 
Initial Work:	Richard Thompson - Belfast, Northern Ireland 
Developers:		Keith Casey - Washington, DC keith@caseysoftware.com 
				Ivan Peevski - Adelaide, Australia cyberhorse@users.sourceforge.net
*/
$del = isset($_POST['del']) ? $_POST['del'] : 0;

$risk = new CRisk();

if (($msg = $risk->bind( $_POST ))) {
	$AppUI->setMsg( $msg, UI_MSG_ERROR );
	$AppUI->redirect();
}

$AppUI->setMsg( 'Risk' );
if ($del) {
	if (($msg = $risk->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect( "m=risks" );
	}
} else {
	$isNotNew = @$_POST['risk_id'];
	if (!$isNotNew) {
		$risk->risk_owner = $AppUI->user_id;
	}
	if (($msg = $risk->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true );
	}
	$AppUI->redirect();
}
?>
