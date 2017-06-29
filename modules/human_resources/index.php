<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();

$titleBlock = new CTitleBlock('Human Resources Organizational Configurations', 'applet3-48.png', $m, "$m.$a");
$titleBlock->show();

if (isset($_GET['tab'])) {
    $AppUI->setState('HumanResourcesIdxTab', $_GET['tab']);
}

$humanResourceTab = $AppUI->getState('HumanResourcesIdxTab', 0);
$tabBox = new CTabBox("?m=human_resources", DP_BASE_DIR.'/modules/human_resources/', $humanResourceTab);
$tabBox->add('vw_companies', 'HR Configurations');
$tabBox->add('vw_companies_roles', 'Roles');
$tabBox->add('vw_companies_policies', 'Companies policies');
$tabBox->show();
?>
