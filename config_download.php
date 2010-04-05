<?php
/**
* Download config files that could not be saved.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009 PGV Development Team
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
* @version $Id$
* @package webtrees
* @subpackage Admin
*/

define('WT_SCRIPT_NAME', 'config_download.php');
require './includes/session.php';

if (WT_ADMIN_USER_EXISTS && !WT_USER_IS_ADMIN && $CONFIGURED) {
	header('Location: admin.php');
	exit;
}

$file=safe_GET('file', WT_REGEX_NOSCRIPT, 'config.php');

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="'.$file.'"');

echo $_SESSION[$file];

?>
