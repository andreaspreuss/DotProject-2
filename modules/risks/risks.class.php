<?php /* CONTACTS $Id: risks.class.php,v 1.1 2005/07/04 02:10:22 cyberhorse Exp $ */
/*
Copyright (c) 2005 CaseySoftware, LLC <info@caseysoftware.com> 
Initial Work:	Richard Thompson - Belfast, Northern Ireland 
Developers:		Keith Casey - Washington, DC keith@caseysoftware.com 
				Ivan Peevski - Adelaide, Australia cyberhorse@users.sourceforge.net
*/
##
## CContact Class
##

class CRisk {
	var $risk_id = NULL;
	var $risk_project = NULL;
	var $risk_task = NULL;
	var $risk_owner = NULL;
	var $risk_name = NULL;
	var $risk_description = NULL;
	var $risk_probability = NULL;
	var $risk_status = NULL;
	var $risk_impact = NULL;
	var $risk_notes = NULL;

	function CRisk() {
		// empty constructor
	}

	function load( $oid ) {
		$q = new DBQuery();
		$q->addQuery('*');
		$q->addTable('risks');
		$q->addWhere('risk_id = ' . $oid);
		return db_loadObject( $q->prepare(), $this );
	}

	function bind( $hash ) {
		if (!is_array( $hash )) {
			return get_class( $this )."::bind failed";
		} else {
			bindHashToObject( $hash, $this );
			return NULL;
		}
	}

	function check() {
	
		return NULL; 
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed";
		}
		if( $this->risk_id ) {
			$ret = db_updateObject( 'risks', $this, 'risk_id' );
		} else {
			$ret = db_insertObject( 'risks', $this, 'risk_id' );
		}
		if( !$ret ) {
			return get_class( $this )."::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}

	function delete() {
		$q = new DBQuery();
		$q->setDelete('risks');
		$q->addWhere('risk_id = ' . $this->risk_id);
		if (!$q->exec())
			return db_error();
		else 
			return null;
	}
}
?>
