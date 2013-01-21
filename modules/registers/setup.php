<?php
/*
 * Name:      History
 * Directory: history
 * Version:   0.1
 * Class:     user
 * UI Name:   History
 * UI Icon:
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Registers';
$config['mod_version'] = '0.1';
$config['mod_directory'] = 'registers';
$config['mod_setup_class'] = 'CSetupRegisters';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Registers';
$config['mod_ui_icon'] = 'ticketsmith.gif';
$config['mod_description'] = 'A module for add Registers';
$config['mod_ui_active'] = 1;


if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupRegisters {   

	function install() {
/*		$sql = "CREATE TABLE history ( " .
		  "history_id int(10) unsigned NOT NULL auto_increment," .
		  "history_user int(10) NOT NULL default '0'," .
		  "history_module int(10) NOT NULL default '0'," .
		  "history_project int(10) NOT NULL default '0'," .
		  "history_date datetime NOT NULL default '0000-00-00 00:00:00'," .
		  "history_description text," .
		  "PRIMARY KEY  (history_id)," .
		  "UNIQUE KEY history_id (history_id)" .
		  ") TYPE=MyISAM;";*/
		$sql = "CREATE TABLE registers (" .
			"register_id int(11) NOT NULL auto_increment," .
			"register_code varchar(20) default NULL," .
			"register_format int(11) NOT NULL default '0'," .
			"register_start_date varchar(15) default NULL," .
			"register_end_date varchar(15) default NULL," .
			"register_description text," .
			"register_owner int(11) default NULL," .
			"register_client int(11) default NULL," .
			"register_project int(11) default NULL," .
			"register_ref_id varchar(20) default NULL," .
			"register_state tinyint(4) default NULL," .
			"PRIMARY KEY  (register_id)" .
			") TYPE=MyISAM;";
		db_exec( $sql );
		$sql = "INSERT INTO sysvals(sysval_key_id,sysval_title,sysval_value) VALUES (1, 'RegisterCode', " .
			"'0|\r\n1|PMM-S02/R2\r\n2|PMM-S03/R2\r\n3|PMM-S04/R2\r\n" .
			"4|PMM-S08/R1\r\n5|PMM-N07/R3\r\n6|PMM-N08/R1');";
		db_exec( $sql);
		$sql = "INSERT INTO sysvals(sysval_key_id,sysval_title,sysval_value) VALUES (1,'RegisterState', " .
			"'1|Open\r\n2|In Progress\r\n3|Close');";
		db_exec( $sql );
/*		$sql = "UPDATE FROM modules set mod_ui_active=1 where mod_ui_name like 'Registers'";
		db_exec( $sql );*/
		return null;
	}
	
	function remove() {
		db_exec( "DROP TABLE registers" );
		db_exec( "delete from sysvals where sysval_title like 'RegisterCode'" );
		db_exec( "delete from sysvals where sysval_title like 'RegisterState'" );
		return null;
	}
	
	function upgrade() {
		return null;
	}
}

?>	
	
