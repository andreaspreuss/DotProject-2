<?php
/*
Copyright (c) 2005 CaseySoftware, LLC <info@caseysoftware.com>
Initial Work:	Richard Thompson - Belfast, Northern Ireland
Developers:		Keith Casey - Washington, DC keith@caseysoftware.com
				Ivan Peevski - Adelaide, Australia cyberhorse@users.sourceforge.net
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Risks';
$config['mod_version'] = '2.0';
$config['mod_directory'] = 'risks';
$config['mod_setup_class'] = 'SRisks';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Risks';
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'Risks management';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class SRisks {

	function install() {
		$ok=1;
		$q = new DBQuery();
		$q->createTable('risks');
		$sql = '(
 risk_id int(10) unsigned NOT NULL auto_increment,
 risk_name varchar(50) default NULL,
 risk_description text,
 risk_probability tinyint(3) default 100,
 risk_status text default NULL,
 risk_owner int(10) default NULL,
 risk_project int(10) default NULL,
 risk_task int(10) default NULL,
 risk_impact int(10) default NULL,
 risk_duration_type tinyint(10) default 1,
 risk_notes text,
 PRIMARY KEY  (risk_id),
 UNIQUE KEY risk_id (risk_id),
 KEY risk_id_2 (risk_id))';
		$q->createDefinition($sql);
		$ok=$ok && $q->exec();

		$q->clear();
		$q->createTable('risk_notes');
		$sql = '(
  risk_note_id int(11) NOT NULL auto_increment,
  risk_note_risk int(11) NOT NULL default \'0\',
  risk_note_creator int(11) NOT NULL default \'0\',
  risk_note_date datetime NOT NULL,
  risk_note_description text NOT NULL,
  PRIMARY KEY  (risk_note_id))';

		$q->createDefinition($sql);
		$ok= $ok && $q->exec();
		if(!$ok){
			return false;
		}
		return null;
	}

	function remove() {
		$q = new DBQuery;
		$q->dropTable('risks');
		$q->exec();
		$q->clear();
		$q->dropTable('risk_notes');
		$q->exec();
		return null;
	}

	function upgrade() {
		return null;
	}
}

?>