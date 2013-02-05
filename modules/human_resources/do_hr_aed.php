<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$del = dPgetParam($_POST, 'del', 0);
$obj = new CHumanResource;
$msg = '';
$roles_ids = dPgetParam($_POST, 'roles_ids', 0);

if (! $obj->bind($_POST)) {
  $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
  $AppUI->redirect();
}

$AppUI->setMsg('Human Resource');
if ($del) {
//if (! $obj->canDelete($msg)) {
//    $AppUI->setMsg($msg, UI_MSG_ERROR);
//    $AppUI->redirect();
//  }
	$human_resource_roles = new CHumanResourceRoles;
	$human_resource_roles->deleteAll($obj->human_resource_id);
  if (($msg = $obj->delete())) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
    $AppUI->redirect();
  }
  else {
	$AppUI->setMsg('deleted', UI_MSG_ALERT, true);
    $AppUI->redirect('', -1);
  }
} else {
  if (($msg = $obj->store())) {
	$AppUI->setMsg($msg, UI_MSG_ERROR);
  } else {
	if($roles_ids || $roles_ids == "") {
		$human_resource_roles = new CHumanResourceRoles;
		$roles_ids_array = explode(',', $roles_ids);
		$human_resource_roles->deleteAll($obj->human_resource_id);
		foreach($roles_ids_array as $role_id) {
			$human_resource_roles->store($role_id, $obj->human_resource_id);
		}
	}
    $AppUI->setMsg($_POST['human_resource_id'] ? 'updated' : 'added', UI_MSG_OK, true);
  }
  $AppUI->redirect();
}
?>
