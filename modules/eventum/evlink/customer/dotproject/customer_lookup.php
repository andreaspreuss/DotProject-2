<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | Eventum - Issue Tracking System                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003, 2004 MySQL AB                                    |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 2 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// | Authors: João Prado Maia <jpm@mysql.com>                             |
// +----------------------------------------------------------------------+
//
// @(#) $Id: customer_lookup.php,v 1.2 2008/06/27 00:02:23 ajdonnison Exp $
//
require_once(dirname(__FILE__).'/../../init.php');
require_once(APP_INC_PATH . "db_access.php");
require_once(APP_INC_PATH . "class.template.php");

$tpl = new Template_API();
$tpl->setTemplate("customer/dotproject/customer_lookup.tpl.html");

Auth::checkAuthentication(APP_COOKIE);
$usr_id = Auth::getUserID();
$prj_id = Auth::getCurrentProject();

//die($usr_id);
// only customers should be able to use this page
$role_id = User::getRoleByUser($usr_id, $prj_id);
if ($role_id < User::getRoleID('Developer')) {
    Auth::redirect(APP_RELATIVE_URL . "list.php");
}

if (@$HTTP_POST_VARS['cat'] == 'lookup') {
    $tpl->assign("results", Customer::lookup($prj_id, $POST['field'], $POST['value']));
}

$tpl->displayTemplate();
?>
