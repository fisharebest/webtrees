<?php
/**
 *  Entry point for SOAP web service
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'genservice.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';

// Our SOAP library uses lots of deprecated features - ignore them
if (version_compare(PHP_VERSION, '5.3')>0) {
	error_reporting(error_reporting() & ~E_DEPRECATED & ~E_STRICT);
} else {
	error_reporting(error_reporting() & ~E_STRICT);
}

require_once './webservice/wtServiceLogic.class.php';

$genealogyServer=new wtServiceLogic();
$genealogyServer->process();
