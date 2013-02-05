<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//add risks sql
$risk_id = intval(dPgetParam($_POST, 'risk_id', 0));
$del = intval(dPgetParam($_POST, 'del', 0));

$not = dPgetParam($_POST, 'notify', '0');
if ($not!='0') {
    $not='1';
}
$obj = new CRisks();
if ($risk_id) { 
	$obj->_message = 'updated';
} else {
	$obj->_message = 'added';
}

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('Risk');
// delete the item
if ($del) {
	$obj->load($risk_id);
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		if ($not=='1') {
                    $obj->notify();
                }
                $projectSelected = intval(dPgetParam($_GET, 'project_id'));
                if ($projectSelected != null) {                    
                    $AppUI->redirect("m=risks");
                }
                $AppUI->setMsg("deleted", UI_MSG_ALERT, true);
	}
}

if (($msg = $obj->store())) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
} else {
        $obj->load($obj->risk_id);
        if ($not=='1') {
        $obj->notify();
        }
        $AppUI->setMsg($risk_id ? 'LBL_UPDATED' : 'LBL_ADDED', UI_MSG_OK, true);
}

$AppUI->redirect();