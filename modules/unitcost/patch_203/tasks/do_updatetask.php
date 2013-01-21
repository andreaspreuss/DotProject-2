<?php /* TASKS $Id: do_updatetask.php,v 1.1.1.1 2006/08/16 06:50:04 aimass Exp $ */

/* unitcost module patch start */

GLOBAL $task_costs, $task_log_costs, $task_log_id;
require_once $AppUI->getModuleClass('unitcost');

/* unitcost module patch end*/



//There is an issue with international UTF characters, when stored in the database an accented letter
//actually takes up two letters per say in the field length, this is a problem with costcodes since
//they are limited in size so saving a costcode as REDACIÓN would actually save REDACIÓ since the accent takes 
//two characters, so lets unaccent them, other languages should add to the replacements array too...
function cleanText($text){
	//This text file is not utf, its iso so we have to decode/encode
	$text = utf8_decode($text);
	$trade = array('á'=>'a','à'=>'a','ã'=>'a',
                 'ä'=>'a','â'=>'a',
                 'Á'=>'A','À'=>'A','Ã'=>'A',
                 'Ä'=>'A','Â'=>'A',
                 'é'=>'e','è'=>'e',
                 'ë'=>'e','ê'=>'e',
                 'É'=>'E','È'=>'E',
                 'Ë'=>'E','Ê'=>'E',
                 'í'=>'i','ì'=>'i',
                 'ï'=>'i','î'=>'i',
                 'Í'=>'I','Ì'=>'I',
                 'Ï'=>'I','Î'=>'I',
                 'ó'=>'o','ò'=>'o','õ'=>'o',
                 'ö'=>'o','ô'=>'o',
                 'Ó'=>'O','Ò'=>'O','Õ'=>'O',
                 'Ö'=>'O','Ô'=>'O',
                 'ú'=>'u','ù'=>'u',
                 'ü'=>'u','û'=>'u',
                 'Ú'=>'U','Ù'=>'U',
                 'Ü'=>'U','Û'=>'U',
                 'Ñ'=>'N','ñ'=>'n');
    $text = strtr($text,$trade);
	$text = utf8_encode($text);

	return $text;
}

$notify_owner =  isset($_POST['task_log_notify_owner']) ? $_POST['task_log_notify_owner'] : 0;

// dylan_cuthbert: auto-transation system in-progress, leave this line commented out for now
//include( '/usr/local/translator/translate.php' );

$del = dPgetParam( $_POST, 'del', 0 );

$obj = new CTaskLog();

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

// dylan_cuthbert: auto-transation system in-progress, leave these lines commented out for now
//if ( $obj->task_log_description ) {
//	$obj->task_log_description .= "\n\n[translation]\n".translator_make_translation( $obj->task_log_description );
//}

if ($obj->task_log_date) {
	$date = new CDate( $obj->task_log_date );
	$obj->task_log_date = $date->format( FMT_DATETIME_MYSQL );
}


/* unitcost module patch start */


// we assume this is never null
$unitcost_task_id = dPgetParam( $_POST, 'task_id', null );

// this may be null if task log is new (store below)
$unitcost_task_log_id = dPgetParam( $_POST, 'task_log_id', null );

// we need this here because extended log requires it
$unitcost_percent_complete = dPgetParam( $_POST, 'task_percent_complete', null );

// sanity
$unitcost_task_id = $unitcost_task_id ? $unitcost_task_id : 0;
$unitcost_task_log_id = $unitcost_task_log_id ? $unitcost_task_log_id : 0;

/* unitcost module patch end */


// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Task Log' );
if ($del) {
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT );

		/* unitcost module patch start */

		// base objects
		$task_costs = new Cunitcost_task_costs; // extended cost object
		$task_log_costs = new Cunitcost_task_log_costs; // extended log object
		
		// get the extended log 

		// if we are here, we assume this is not null!
		$task_log_costs->load($unitcost_task_log_id);

		// ...but we (extended task log) might not exist!
		if( $task_log_costs->task_log_id ){
		  
		  // ok, so there is an extended task log...

		  // we need to update the accumulated values on the task...
		  if ( $task_costs->load($unitcost_task_id) ){

		    /*
		    Currently dotProject does not adjust
		    task_percent_complete uppon deletion of the
		    current task log; I don't know if this is a bug or
		    it's the way it's supposed to work. For now, and
		    to be consistent with dotProject's current
		    functionallity this if() is just a placeholder so
		    we can adjust the accumulated costs in future
		    versions...

		    In any case, it is recommended to disable deletion
		    of task logs and also prevent people to be
		    changing the task_percent_complete directly on the
		    task edit screen. In fact, this module substitutes
		    the field in the task edit screen for a read-only
		    one. This way all task info MUST be update using
		    task logs. Not too much to worry for now, but it
		    may be a good topic for discussion and future
		    versions of DP and of this module.

		    */

		  } else {

		    /*
		      This is strange case because we assume that
		      there must be extended cost info for this task
		      if the unicost module is installed, but it may
		      happen if the module is installed/removed with
		      existing data. I will leave this condition here
		      just in case we may need it for future versions.
		    */

		  }
		  
		  // delete the extended task log...
		  if (($msg = $task_log_costs->delete())) {
		    $AppUI->setMsg( $msg, UI_MSG_ERROR );
		  }


		} else{
		  
		  /*
		    It seems there is no extended log info. Again this
		    is strange if we have the unitcost installed but
		    may happen if the module was installed with data
		    already in the system, I will leave this condition
		    here just in case we need to do something in these
		    cases in the future.
		  */
		  
		}
		
		/* unitcost module patch end */

	}


	$AppUI->redirect();
} else {
	$obj->task_log_costcode = cleanText($obj->task_log_costcode);
	if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( @$_POST['task_log_id'] ? 'updated' : 'inserted', UI_MSG_OK, true );

		/* unitcost module patch start */

		// base objects
		$task_costs = new Cunitcost_task_costs; // extended cost object
		$task_log_costs = new Cunitcost_task_log_costs; //extended log object

		// save or update the extended log
		if( ($unitcost_task_log_id == 0) || 
		    !($task_log_costs->load($unitcost_task_log_id)) ){

		  // make sure we insert new record
		  $task_log_costs->_tbl_key = NULL; // (new to the dpFramework comments welcome on this)

		  // recently created task log
		  $unitcost_task_log_id = $obj->task_log_id;
		}

		// set the extended task log info...
		$task_log_costs->task_log_id = $unitcost_task_log_id;
		$task_log_costs->task_percent_complete = $unitcost_percent_complete;
		$task_log_costs->task_actual_units = dPgetParam( $_POST, 'new_actual_units', null );
		$task_log_costs->task_actual_cost = dPgetParam( $_POST, 'new_actual_cost', null );
		$task_log_costs->store();
		
		// update task cost extended info
		$task_costs->load($obj->task_log_task);
		$task_costs->task_actual_units = $task_log_costs->task_actual_units;
		$task_costs->task_actual_cost = $task_log_costs->task_actual_cost;
		$task_costs->store();
		

		/* unitcost module patch end */

	}
}

$task = new CTask();
$task->load( $obj->task_log_task );
$task->check();

$task->task_percent_complete = dPgetParam( $_POST, 'task_percent_complete', null );

if(dPgetParam($_POST, "task_end_date", "") != ""){
	$task->task_end_date = $_POST["task_end_date"];
}

if ($task->task_percent_complete >= 100 && ( ! $task->task_end_date || $task->task_end_date == '0000-00-00 00:00:00')){
	$task->task_end_date = $obj->task_log_date;
}

if (($msg = $task->store())) {
	$AppUI->setMsg( $msg, UI_MSG_ERROR, true );
}

$new_task_end = new CDate($task->task_end_date);
if ($new_task_end->dateDiff($new_task_end))
	$task->addReminder();

if ($notify_owner) {
	if ($msg = $task->notifyOwner()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	}
}

// Check if we need to email the task log to anyone.
$email_assignees = dPgetParam($_POST, 'email_assignees', null);
$email_task_contacts = dPgetParam($_POST, 'email_task_contacts', null);
$email_project_contacts = dPgetParam($_POST, 'email_project_contacts', null);
$email_others = dPgetParam($_POST, 'email_others', '');
$email_extras = dPgetParam($_POST, 'email_extras', null);

if ($task->email_log($obj, $email_assignees, $email_task_contacts, $email_project_contacts, $email_others, $email_extras)) {
	$obj->store(); // Save the updated message. It is not an error if this fails.
}
 

$AppUI->redirect("m=tasks&a=view&task_id={$obj->task_log_task}&tab=0#tasklog{$obj->task_log_id}");
?>
