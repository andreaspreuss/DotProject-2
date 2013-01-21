<?php
/*
 * Name:      Invoices
 * Directory: invoices
 * Version:   0.1
 * Class:     user
 * UI Name:   Invoices
 * UI Icon:   monkeychat-48.png
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Invoices';
$config['mod_version'] = '0.1';
$config['mod_directory'] = 'invoices';
$config['mod_setup_class'] = 'CSetupInvoices';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Invoices';
$config['mod_ui_icon'] = 'applet3-48.png';
$config['mod_description'] = 'A module for invoices';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupInvoices {   

	function install() {
	  $sql = "CREATE TABLE invoice_product ( " .
	     "product_id int(11) NOT NULL auto_increment," .
	     "product_invoice int(11) NOT NULL default '0'," .
	     "product_costcode varchar(7) NOT NULL default '0'," .
	     "product_name varchar(150) NOT NULL default ''," .
	     "product_qty int(11) NOT NULL default '0'," .
	     "product_price double(6,2) NOT NULL default '0.00'," .
	     "PRIMARY KEY  (product_id)" .
	     ") TYPE=MyISAM";
	  db_exec( $sql );
	  $sql2 = "CREATE TABLE invoices (" .
	     "invoice_id int(11) NOT NULL auto_increment," .
	     "invoice_company int(11) NOT NULL default '0'," .
	     "invoice_date datetime NULL," .
	     "invoice_due datetime NULL," .
	     "invoice_terms text NOT NULL," .
	     "invoice_status tinyint(4) NOT NULL default '0'," . 
	     "invoice_owner int(11) NOT NULL default '0'," .
	     "KEY invoice_id (invoice_id)" . 
	     ") TYPE=MyISAM";
	     db_exec( $sql2 );
	  return null;
	}
	
	function remove() {
		db_exec( "DROP TABLE invoice_product" );
		db_exec( "DROP TABLE invoices" );
		db_exec( "delete from permissions where permission_grant_on like 'invoices'");
		return null;
	}
	
	function upgrade() {
		return null;
	}
}

?>	
	
