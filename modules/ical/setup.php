<?php
/* ICAL setup.php, v 0.1.0 2012/05/02 */
/*
* Copyright (c) 2012 Region Poitou-Charentes (France)
*
* Description:	Setup page of the ical module.
*
* Author:		Henri SAULME, <henri.saulme@gmail.com>
*
* License:		GNU/GPL
*
* CHANGE LOG
*
* version 0.1.0
* 	Creation
*
*/
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
 *  Name: Ical
 *  Directory: ical
 *  Version 1.0
 *  Type: user
 *  UI Name: ical
 *  UI Icon: ?
 */

$config = array();
$config['mod_name'] = 'ICal';
$config['mod_version'] = '1.0';
$config['mod_directory'] = 'ical';
$config['mod_setup_class'] = 'CSetupICal';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'ICal';
$config['mod_ui_icon'] = 'folder5.png';
$config['mod_description'] = 'This module permit to synchronise user agenda when you add or edit task';
$config['mod_config'] = false;


if (@$a == 'setup') {
	echo dPshowModuleConfig($config);
}


class CSetupICal {

	function configure() { return true; }

	function remove() {		
		$success = 1;
		$q = new DBQuery();
		$q -> dropTable('tasks_ical');
		$success = $success & $q->exec();
		if($success){
			return null;
		}
		return $success;
	}
  
	function upgrade($old_version) { return true; }

	function install() {
		$success = 1;
		$q = new DBQuery();
		$q -> createTable('tasks_ical');
		$q -> createDefinition('(
				  `task_id` int(10) NOT NULL,
				  `UID` varchar(30) DEFAULT NULL,
				  `created` varchar(15) NOT NULL,
				  `sequence` int(10) NOT NULL
				)');
		
		$success= $success& $q -> exec();
		if($success){
			return null;
		}		  

		return $success;
	}
}
