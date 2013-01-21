<?php /* $Id: setup.php,v 1.2 2007/04/19 19:57:09 caseydk Exp $ */
/*
dotProject Module

Name:      Timesheet
Directory: timesheet
Version:   0.1
Class:     user
UI Name:   Timesheet
UI Icon:

This file does no action in itself.
If it is accessed directory it will give a summary of the module parameters.
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Timesheet';
$config['mod_version'] = '0.1';
$config['mod_directory'] = 'timesheet';
$config['mod_setup_class'] = 'CSetupTimesheet';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Timesheet';
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'This is a Timesheet module';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

require_once dPgetConfig( 'root_dir' ).'/modules/system/syskeys/syskeys.class.php';

/*
// MODULE SETUP CLASS
	This class must contain the following methods:
	install - creates the required db tables
	remove - drop the appropriate db tables
	upgrade - upgrades tables from previous versions
*/
class CSetupTimesheet {
/*
	Install routine
*/
	function install() {
		$sql = "
			CREATE TABLE timesheet (
				timesheet_id int(11) not NULL auto_increment,
				user_id int(11) not NULL,
				timesheet_date date not NULL,
				timesheet_time_in time not NULL,
				timesheet_time_out time not NULL,
				timesheet_time_break time not NULL,
				timesheet_time_break_start time not NULL,
				timesheet_note varchar(255),
				PRIMARY KEY (timesheet_id)
			) TYPE=MyISAM
			";
			db_exec( $sql );
			
		$sv = new CSysVal( 1, 'BillingCategory', "0|Billable\n1|Unbillable" );
		$sv->store();
		$sv = new CSysVal( 1, 'WorkCategory', "0|Programming\n1|Design" );
		$sv->store();
		return true;
	}
/*
	Removal routine
*/
	function remove() {
		$sql = "DROP TABLE timesheet";
		db_exec( $sql );
		
		//$sql = "DELETE FROM sysvals WHERE sysval_title = 'BillingCategory' or sysval_title = 'WorkCategory'";
		//db_exec( $sql );

		return true;
	}
/*
	Upgrade routine
*/
	function upgrade() {
		return true;
	}
}

?>