<?php
/**
 * TODO Block
 *
 * This block will print a list of things to do, based on _TODO records
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008  PGV Development Team.  All rights reserved.
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
 * @subpackage Blocks
 * @author Greg Roach, fisharebest@users.sourceforge.net
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_TODO_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_lists.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';

$WT_BLOCKS['print_todo']['name']     =i18n::translate('&quot;To Do&quot; tasks');
$WT_BLOCKS['print_todo']['descr']    =i18n::translate('The To Do block lists all outstanding _TODO facts in the database.');
$WT_BLOCKS['print_todo']['canconfig']=true;
$WT_BLOCKS['print_todo']['config']   =array(
	'cache'          =>0,
	'show_unassigned'=>true,  // show unassigned items
	'show_other'     =>false, // show items assigned to other users
	'show_future'    =>false  // show items with a future date
);

// this block prints a list of _TODO events in your gedcom
function print_todo($block=true, $config='', $side, $index) {
	global $ctype, $WT_IMAGE_DIR, $WT_IMAGES, $WT_BLOCKS;

	$block=true; // Always restrict this block's height

	if (empty($config)) {
		$config=$WT_BLOCKS['print_todo']['config'];
	}

	$id='todo';
	$title='';
	if ($WT_BLOCKS['print_todo']['canconfig']) {
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
			if ($ctype=='gedcom') {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('".encode_url("index_edit.php?name={$name}&ctype={$ctype}&action=configure&side={$side}&index={$index}")."', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">";
			$title .= "<img class=\"adminicon\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['admin']['small']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>";
		}
	}
	$title.=i18n::translate('&quot;To Do&quot; tasks').help_link('todo');
	$content='';

	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';

	$table_id = 'ID'.floor(microtime()*1000000); // sorttable requires a unique ID
	$content .= '<table id="'.$table_id.'" class="sortable list_table center">';
	$content .= '<tr>';
	$content .= '<th class="list_label">'.i18n::translate('DATE').'</th>';
	$content .= '<th class="list_label">'.i18n::translate('Record').'</th>';
	if ($config['show_unassigned']=='yes' || $config['show_other']=='yes') {
		$content .= '<th class="list_label">'.i18n::translate('User name').'</th>';
	}
	$content .= '<th class="list_label">'.i18n::translate('TEXT').'</th>';
	$content .= '</tr>';

	$found=false;
	$end_jd=$config['show_future']=='yes' ? 99999999 : client_jd();
	foreach (get_calendar_events(0, $end_jd, '_TODO', WT_GED_ID) as $todo) {
		$record=GedcomRecord::getInstance($todo['id']);
		if ($record && $record->canDisplayDetails()) {
			$pgvu=get_gedcom_value('_PGVU', 2, $todo['factrec']);
			if ($pgvu==WT_USER_NAME || !$pgvu && $config['show_unassigned']=='yes' || $pgvu && $config['show_other']=='yes') {
				$content.='<tr valign="top">';
				$content.='<td class="list_value_wrap">'.str_replace('<a', '<a name="'.$todo['date']->MinJD().'"', $todo['date']->Display(false)).'</td>';
				$name=$record->getListName();
				$content.='<td class="list_value_wrap" align="'.get_align(WT_GEDCOM).'"><a href="'.encode_url($record->getLinkUrl()).'">'.PrintReady($name).'</a></td>';
				if ($config['show_unassigned']=='yes' || $config['show_other']=='yes') {
					$content.='<td class="list_value_wrap">'.$pgvu.'</td>';
				}
				$text=get_gedcom_value('_TODO', 1, $todo['factrec']);
				$content.='<td class="list_value_wrap" align="'.get_align($text).'">'.PrintReady($text).'</td>';
				$content.='</tr>';
				$found=true;
			}
		}
	}

	$content .= '</table>';
	if (!$found) {
		$content.='<p>'.i18n::translate('There are no &quot;To Do&quot; tasks.').'</p>';
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}

function print_todo_config($config) {
	global $WT_BLOCKS, $DAYS_TO_SHOW_LIMIT;

	if (empty($config)) {
		$config=$WT_BLOCKS['print_todo']['config'];
	}

	echo '<tr><td class="descriptionbox wrap width33">';
	echo i18n::translate('Show other users\' tasks'), help_link('todo_show_other');
	echo '</td><td class="optionbox">';
	echo edit_field_yes_no('show_other', $config['show_other']);
	echo '</td></tr>';

	echo '<tr><td class="descriptionbox wrap width33">';
	echo i18n::translate('Show unassigned tasks'), help_link('todo_show_unassigned');
	echo '</td><td class="optionbox">';
	echo edit_field_yes_no('show_other', $config['show_unassigned']);
	echo '</td></tr>';

	echo '<tr><td class="descriptionbox wrap width33">';
	echo i18n::translate('Show future tasks'), help_link('todo_show_future');
	echo '</td><td class="optionbox">';
	echo edit_field_yes_no('show_other', $config['show_future']);
	echo '</td></tr>';

	// Cache file life is not configurable by user
	echo '<input type="hidden" name="cache" value="0" />';
}
?>
