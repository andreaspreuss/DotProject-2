<?php /* COMPANIES $Id: companies.class.php 5606 2008-01-15 22:56:09Z gregorerhardt $ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
 *	@package dotProject
 *	@subpackage modules
*/

require_once( $AppUI->getSystemClass ('dp' ) );

class CAnnotations extends CDpObject {

	var $annotation_id = NULL;
	var $annotation_project = NULL;
	var $annotation_previous = NULL;
	var $annotation_revised_priority = NULL;
	var $annotation_next = NULL;
	var $annotation_date = NULL;
	var $annotation_scope = NULL;
	var $annotation_resources = NULL;
	var $annotation_time = NULL;
	var $annotation_scope_desc = NULL;
	var $annotation_resources_desc = NULL;
	var $annotation_time_desc = NULL;
	var $annotation_strategy = NULL;
	var $annotation_sholders = NULL;
	var $annotation_risks = NULL;
	var $annotation_sizing = NULL;
	var $annotation_horizontality = NULL;
	var $annotation_costbenefit = NULL;
	var $annotation_rationale = NULL;
	var $annotation_must = NULL;
	var $annotation_must_rationale = NULL;
	var $annotation_team = NULL;
	var $annotation_flag = NULL;
	var $annotation_subject = NULL;

	//The Constructor
	function __construct() {  
		parent::__construct( 'annotations', 'annotation_id' );
	}
	

	// overload check
	function check() {
		if ($this->annotation_id === NULL) {
			return 'annotations id is NULL';
		}
		$this->annotation_id = intval( $this->annotation_id );

		return NULL; // object is ok
	}
}

?>
