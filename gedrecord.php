<?php
/**
 * Parses gedcom file and displays record for given id in raw text
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
 * @version $Id$
 * @package webtrees
 * @subpackage Charts
 */

define('WT_SCRIPT_NAME', 'gedrecord.php');
require './includes/session.php';

require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';
header('Content-Type: text/html; charset=UTF-8');

$pid=safe_GET_xref('pid');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" <?php echo i18n::html_markup(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo i18n::translate('Record'), ': ',$pid ; ?></title>
	</head>
	<body><?php

if (!$SHOW_GEDCOM_RECORD && !WT_USER_CAN_ACCEPT) {
	echo "<span class=\"error\">", i18n::translate('This page has been disabled by the site administrator.'), "</span>\n";
	echo "</body></html>";
	exit;
}

$obj=GedcomRecord::getInstance($pid);

if (is_null($obj) || !$obj->canDisplayDetails()) {
	print_privacy_error($CONTACT_EMAIL);
	echo "</body></html>";
	exit;
}
if (!isset($fromfile)) {
	$indirec=$obj->getGedcomRecord();
} else  {
	$indirec=find_updated_record($pid, WT_GED_ID);
	$indirec=privatize_gedcom($indirec);
}
$indirec=htmlspecialchars($indirec);
$indirec=preg_replace("/@(\w+)@/", "@<a href=\"gedrecord.php?pid=$1\">$1</a>@", $indirec);
echo "<pre style=\"white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; white-space: pre-wrap; word-wrap: break-word;\">", $indirec, "</pre>";
echo "</body></html>";
