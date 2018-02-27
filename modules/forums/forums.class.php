<?php /* FORUMS $Id: forums.class.php 5422 2007-10-13 21:58:07Z caseydk $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

require_once( $AppUI->getSystemClass( 'libmail' ) );
require_once( $AppUI->getModuleClass( 'projects' ) );

class CForum extends CDpObject {
	var $forum_id = NULL;
	var $forum_project = NULL;
	var $forum_status = NULL;
	var $forum_owner = NULL;
	var $forum_name = NULL;
	var $forum_create_date = NULL;
	var $forum_last_date = NULL;
	var $forum_last_id = NULL;
	var $forum_message_count = NULL;
	var $forum_description = NULL;
	var $forum_moderated = NULL;

	function __construct() {
		// empty constructor
		parent::__construct('forums', 'forum_id');
	} 

	function bind( $hash ) {
		if (!is_array( $hash )) {
			return "CForum::bind failed";
		} else {
			bindHashToObject( $hash, $this );
			return NULL;
		}
	}

	function check() {
		if ($this->forum_id === NULL) {
			return 'forum_id is NULL';
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store($updateNulls = false) {
		$msg = $this->check();
		if( $msg ) {
			return "CForum::store-check failed<br />$msg";
		}
		if( $this->forum_id ) {
			$ret = db_updateObject( 'forums', $this, 'forum_id', $updateNulls); // ! Don't update null values
			$details['changes'] = $ret;
			if($this->forum_name) {
				// when adding messages, this functon is called without first setting 'forum_name'
				$details['name'] = $this->forum_name;
			}
			addHistory('forums', $this->forum_id, 'update', $details);
		} else {
			$this->forum_create_date = db_datetime( time() );
			$ret = db_insertObject( 'forums', $this, 'forum_id' );
			$details['changes'] = $ret;
			$details['name'] = $this->forum_name;
			addHistory('forums', $this->forum_id, 'add', $details);
		}
		if( !$ret ) {
			return "CForum::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}

	function delete($oid = NULL, $history_desc = '', $history_proj = 0) {
		$q  = new DBQuery;
		$q->setDelete('forum_visits');
		$q->addWhere('visit_forum = '.$this->forum_id);
		$q->exec(); // No error if this fails, it is not important.
		$q->clear();
		
		$q->setDelete('forums');
		$q->addWhere('forum_id = '.$this->forum_id);
		if (!$q->exec()) {
			$q->clear();
			return db_error();
		}
		$q->clear();
		$q->setDelete('forum_messages');
		$q->addWhere('message_forum = '.$this->forum_id);
		if (!$q->exec()) {
			$result =  db_error();
		} else {
			$details['name'] = $this->forum_name;
			addHistory('forums', $this->forum_id, 'delete', $details);
			$result =  NULL;
		}
		$q->clear();
		return $result;
	}
	
	function search($keyword)
	{
		global $AppUI;
		$perms = &$AppUI->acl();
		$list = parent::search($keyword);
		
		$q = new DBQuery();
		$q->addQuery('forum_name, message_id, message_forum, message_title');
		$q->addTable('forums');
		$q->addJoin('forum_messages', 'f', 'forum_id = message_forum');
		$q->addWhere("(message_title LIKE '%$keyword%' OR message_body LIKE '%$keyword%')");
		$messages = $q->loadList();
		foreach($messages as $message)
	    if ($perms->checkModuleItem($this->_tbl, 'view', $message['message_id']))
				$list[$message['message_forum']] = $message['forum_name'] . ' ==> ' . $message['message_title']; 
					
		return $list;
	}
}

class CForumMessage {
	var $message_id = NULL;
	var $message_forum = NULL;
	var $message_parent = NULL;
	var $message_author = NULL;
	var $message_editor = NULL;
	var $message_title = NULL;
	var $message_date = NULL;
	var $message_body = NULL;
	var $message_published = NULL;

	function __construct() {
 		// empty constructor
 	}

	function bind( $hash ) {
		if (!is_array( $hash )) {
			return "CForumMessage::bind failed";
		} else {
			bindHashToObject( $hash, $this );
			return NULL;
		}
	}

	function check() {
		if ($this->message_id === NULL) {
			return 'message_id is NULL';
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store($updateNulls = false) {
		$msg = $this->check();
		if( $msg ) {
			return "CForumMessage::store-check failed<br />$msg";
		}
		$q  = new DBQuery;
		if( $this->message_id ) {
			// First we need to remove any forum visits for this message
			// otherwise nobody will see that it has changed.
			$q->setDelete('forum_visits');
			$q->addWhere('visit_message = '.$this->message_id);
			$q->exec(); // No error if this fails, it is not important.
			$ret = db_updateObject( 'forum_messages', $this, 'message_id', $updateNulls); // ! Don't update null values
			$q->clear();
		} else {
			$date = new CDate();
			$this->message_date = $date->format( FMT_DATETIME_MYSQL );
			$new_id = db_insertObject( 'forum_messages', $this, 'message_id' ); ## TODO handle error now
			echo db_error(); //TODO handle error better

			$q->addTable('forum_messages');
			$q->addQuery('count(message_id), MAX(message_date)');
			$q->addWhere('message_forum = '.$this->message_forum);

			$res = $q->exec();
			echo db_error(); ## TODO handle error better
			$reply = db_fetch_row( $res );
			$q->clear();

			//update forum descriptor
			$forum = new CForum();
			$forum->forum_id = $this->message_forum;
			$forum->forum_message_count = $reply[0];
			$forum->forum_last_date = $reply[1];
			$forum->forum_last_id = $this->message_id;

			$forum->store(); ## TODO handle error now

			return $this->sendWatchMail( false );
		}

		if( !$ret ) {
			return "CForumMessage::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}

	function delete($oid = NULL, $history_desc = '', $history_proj = 0) {
		$q  = new DBQuery;
		$q->setDelete('forum_visits');
		$q->addWhere('visit_message = '.$this->message_id);
		$q->exec(); // No error if this fails, it is not important.
		$q->clear();

		$q->addTable('forum_messages');
		$q->addQuery('message_forum');
		$q->addWhere('message_id = ' . $this->message_id);
		$forumId = db_loadResult($q->prepare());
		$q->clear();

		$q->setDelete('forum_messages');
		$q->addWhere('message_id = '.$this->message_id);
		if (!$q->exec()) {
			$result = db_error();
		} else {
			$result = NULL;
		}
		$q->clear();

		$q->addTable('forum_messages');
		$q->addQuery('COUNT(*)');
		$q->addWhere('message_forum = ' . $forumId);
		$messageCount = db_loadResult($q->prepare());
		$q->clear();		

		$q->addTable('forums');
		$q->addUpdate('forum_message_count', $messageCount);
		$q->addWhere('forum_id = ' . $forumId);
		$q->exec();
		$q->clear();
		
		return $result;
	}

	function sendWatchMail( $debug=false ) {
		GLOBAL $AppUI, $debug;
		$subj_prefix = $AppUI->_('forumEmailSubj', UI_OUTPUT_RAW);
		$body_msg = $AppUI->_('forumEmailBody', UI_OUTPUT_RAW);
		
		// Get the message from details.
		$q  = new DBQuery;
		$q->addTable('users', 'u');
		$q->addQuery('contact_email, contact_first_name, contact_last_name');
		$q->addJoin('contacts', 'con', 'contact_id = user_contact');
		$q->addWhere("user_id = '{$this->message_author}'");
		$res = $q->exec();
		if ($row = db_fetch_assoc($res)) {
		  $message_from = "$row[contact_first_name] $row[contact_last_name] <$row[contact_email]>";
		} else {
		  $message_from = "Unknown user";
		}
		// Get the forum name;
		$q->clear();
		$q->addTable('forums');
		$q->addQuery('forum_name');
		$q->addWhere("forum_id = '{$this->message_forum}'");
		$res = $q->exec();
		if ($row = db_fetch_assoc($res)) {
		  $forum_name = $row['forum_name'];
		} else {
		  $forum_name = 'Unknown';
		}

		$q->clear();
		$q->addTable('users');
		$q->addQuery('DISTINCT contact_email, user_id, contact_first_name, contact_last_name');
		$q->addJoin('contacts', 'con', 'contact_id = user_contact');
		
		$q->addTable('forum_watch');
		$q->addWhere('user_id = watch_user');
		$q->addWhere('user_id <> ' . $AppUI->user_id);
		$q->addWhere("(watch_forum = $this->message_forum OR watch_topic = $this->message_parent)");

		if (!($res = $q->exec())) {
			$q->clear();
			return;
		}
		if (db_num_rows( $res ) < 1) {
			return;
		}

		$mail = new Mail;
		$mail->Subject( "$subj_prefix $this->message_title", isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : "");

		$body = "$body_msg";

		$body .= "\n\n" . $AppUI->_('Forum', UI_OUTPUT_RAW) . ": $forum_name";
		$body .= "\n" . $AppUI->_('Subject', UI_OUTPUT_RAW) . ": {$this->message_title}";
		$body .= "\n" . $AppUI->_('Message From', UI_OUTPUT_RAW) . ": $message_from";
		$body .= "\n\n" . DP_BASE_URL . '/index.php?m=forums&a=viewer&forum_id='.$this->message_forum;
		$body .= "\n\n$this->message_body";
 
		$mail->Body( $body, isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : ""  );
		$mail->From( $AppUI->_('forumEmailFrom', UI_OUTPUT_RAW) );

		while ($row = db_fetch_assoc( $res )) {
			if ($mail->ValidEmail( $row['contact_email'] )) {
				$mail->To( $row['contact_email'], true );
				$mail->Send();
			}
		}
		$q->clear();
		return;
	}
}
?>
