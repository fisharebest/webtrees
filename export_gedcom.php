<?php
/**
 * Exports data from the database to a gedcom file
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'export_gedcom.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_export.php';

// Which gedcoms do we have permission to export?
$gedcoms=array();
foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
	if (userGedcomAdmin(WT_USER_ID, $ged_id)) {
		$gedcoms[$ged_id]=$gedcom;
	}
}

// If we don't have permission to administer any gedcoms, redirect to
// this page, which will force a login and provide a list.
if (empty($gedcoms)) {
	header('Location: editgedcoms.php');
}

// Which gedcom have we requested to export
$export = safe_GET('export', $gedcoms);

print_simple_header(i18n::translate('Export'));

if ($export) {
	$ged_id = get_id_from_gedcom($export);
	$filename = get_site_setting('INDEX_DIRECTORY').$export;
	echo '<h1>', i18n::translate('Export'), '</h1>';
	echo '<p>', htmlspecialchars(filename_decode($export)), ' => ', $filename, '</p>';
	flush();
	$gedout = fopen($filename.'.tmp', 'w');
	if ($gedout) {
		$start = microtime(true);

		$exportOptions = array();
		$exportOptions['privatize'] = 'none';
		$exportOptions['toANSI'] = 'no';
		$exportOptions['noCustomTags'] = 'no';
		$exportOptions['path'] = $MEDIA_DIRECTORY;
		$exportOptions['slashes'] = 'forward';

		export_gedcom($export, $gedout, $exportOptions);

		$end = microtime(true);
		fclose($gedout);
		@unlink($filename);
		rename($filename.'.tmp', $filename);
		$stat = stat($filename);
		echo sprintf('<p>%d bytes, %0.3f seconds</p>', $stat['size'], $end-$start);
	} else {
		echo '<p>Error: could not open file for writing</p>';
	}
} else {
	echo '<h1>Export data from database to gedcom file</h1>';
	echo '<ul>';
	foreach ($gedcoms as $ged_id=>$gedcom) {
		echo '<li><a href="?export=', urlencode($gedcom), '">', $gedcom, ' => ', htmlspecialchars(filename_decode(realpath(get_gedcom_setting($ged_id, 'path')))), '</a></li>';
	}
	echo '</ul>';
}

echo '<p><a href="javascript: ', i18n::translate('Close Window'), '" onclick="window.close();">', i18n::translate('Close Window'), '</a></p>';
print_simple_footer();
