<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
/**
 *  Name: Costs
 *  Directory: costs
 *  Version 1.0
 *  Type: user
 *  UI Name: Costs
 *  UI Icon: ?
 */
$config = array();
$config['mod_name'] = 'Costs'; // name the module
$config['mod_version'] = '1.0.1'; // add a version number
$config['mod_directory'] = 'costs'; // tell dotProject where to find this module
$config['mod_setup_class'] = 'SSetupCosts'; // the name of the PHP setup class (used below)
$config['mod_type'] = 'user'; //'core' for standard dP modules, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Costs'; // the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'costs.png'; // name of a related icon //TODO
$config['mod_description'] = 'Costs Plan'; // some description of the module //TODO
$config['mod_config'] = false; // show 'configure' link in viewmods
$config['permissions_item_table'] = 'costs'; // tell dotProject the database table name
$config['permissions_item_field'] = 'cost_id'; // identify table's primary key (for permissions)
$config['permissions_item_label'] = 'cost_name'; // identify "title" field in table

if (@$a == 'setup') {
    echo dPshowModuleConfig($config);
}

class SSetupCosts {

    function configure() {
        return true;
    }

    function install() {

        $ok = true;
        $q = new DBQuery;
        $sql = "(
 			cost_id integer not null auto_increment,
                        cost_type_id integer not null,
                        cost_project_id integer not null,
			cost_description varchar(50) not null,
                        cost_quantity int(11),
			cost_date_begin datetime default null,
			cost_date_end datetime default null,
                        cost_value_unitary decimal(9,2),
                        cost_value_total decimal (9,2),
			primary key (cost_id),
                        foreign key (cost_project_id) references `dotp_projects`(project_id)
		)ENGINE=MYISAM";
        $q->createTable('costs');
        $q->createDefinition($sql);
        $ok = $ok && $q->exec();
        $q->clear();
      
        
        $sql = "(
 			budget_reserve_id integer not null auto_increment,
			budget_reserve_project_id integer not null,
			budget_reserve_risk_id integer not null,
                        budget_reserve_description varchar(50),
                        budget_reserve_financial_impact int(11),
                        budget_reserve_inicial_month datetime default null,
                        budget_reserve_final_month datetime default null,
                        budget_reserve_value_total decimal(9,2),
			primary key (budget_reserve_id),
			foreign key (budget_reserve_project_id) references `dotp_projects`(project_id),
                        foreign key (budget_reserve_risk_id) references `dotp_risks` (risk_id)
		)ENGINE=MYISAM";
        $q->createTable('budget_reserve');
        $q->createDefinition($sql);
        $ok = $ok && $q->exec();
        $q->clear();


        $sql = "(
 			budget_id integer not null auto_increment,
			budget_project_id integer not null,
			budget_reserve_management decimal(9,2)not null,
                        budget_sub_total decimal(9,2) not null,
                        budget_total decimal(9,2) not null,
                        primary key (budget_id),
			foreign key (budget_project_id) references `dotp_projects`(project_id)
		)ENGINE=MYISAM";
        $q->createTable('budget');
        $q->createDefinition($sql);
        $ok = $ok && $q->exec();
        $q->clear();


        if (!$ok) {
            return false;
        }
        return null;
    }

    function remove() {
        $q = new DBQuery();
        $q->dropTable('costs');
        $q->exec();
        $q->clear();

        $q->dropTable('budget_reserve');
        $q->exec();
        $q->clear();

        $q->dropTable('budget');
        $q->exec();
        $q->clear();
    }

    function upgrade($old_version) {

        return true;
    }

}

?>
