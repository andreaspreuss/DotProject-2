<?php /* $Id: timesheet.class.php,v 1.1.1.1 2003/12/04 17:53:45 iexposure Exp $ */
##
## Timesheet Class
##

class Timesheet {
	var $timesheet_id = NULL;
	var $user_id = NULL;
	var $timesheet_date = NULL;
	var $timesheet_time_in = NULL;
	var $timesheet_time_out = NULL;
	var $timesheet_time_break = NULL;
	var $timesheet_time_break_start = NULL;
	var $timesheet_note = NULL;
        
	function Timesheet() {
		// empty constructor
	}

	function load( $oid ) {
		$sql = "SELECT * FROM timesheet WHERE timesheet_id = $oid";
		return db_loadObject( $sql, $this );
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
		if ($this->timesheet_id === NULL) {
			return 'timesheet id is NULL';
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed";
		}
		if( $this->timesheet_id ) {
			$ret = db_updateObject( 'timesheet', $this, 'timesheet_id', false );
		} else {
			$ret = db_insertObject( 'timesheet', $this, 'timesheet_id' );
		}
		if( !$ret ) {
			return get_class( $this )."::store failed <br>" . db_error();
		} else {
			return NULL;
		}
	}
	function delete() {
		$sql = "DELETE FROM timesheet WHERE timesheet_id = $this->timesheet_id";
		if (!db_exec( $sql )) {
			return db_error();
		} else {
			return NULL;
		}
	}
}

?>