<?php /* FUNCTIONS $Id: projects_func.php 5872 2009-04-25 00:09:56Z merlinyoda $ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

// project statii
$pstatus = dPgetSysVal('ProjectStatus');
$ptype = dPgetSysVal('ProjectType');

$ppriority_name = dPgetSysVal('ProjectPriority');
$ppriority_color = dPgetSysVal('ProjectPriorityColor');

$priority = array();
foreach ($ppriority_name as $key => $val) {
    $priority[$key]['name'] = $val;
}
foreach ($ppriority_color as $key => $val) {
    $priority[$key]['color'] = $val;
}

?>
