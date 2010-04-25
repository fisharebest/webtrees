<?php
/**
 * Log viewer.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'logs.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// Only admin users can access this page
if (!WT_USER_GEDCOM_ADMIN) {
	header('Location: login.php?url='.WT_SCRIPT_NAME);
	exit;
}

$earliest=WT_DB::prepare("SELECT DATE(MIN(log_time)) FROM {$TBLPREFIX}log")->execute(array())->fetchOne();
$latest  =WT_DB::prepare("SELECT DATE(MAX(log_time)) FROM {$TBLPREFIX}log")->execute(array())->fetchOne();

// Filtering
$from=safe_GET('from', '\d\d\d\d-\d\d-\d\d', $earliest);
$to  =safe_GET('to',   '\d\d\d\d-\d\d-\d\d', $latest);
$type=safe_GET('type', array('auth','change','config','debug','edit','error','media','search'));
$text=safe_GET('text');
$ip  =safe_GET('ip');
$user=safe_GET('user');
if (WT_USER_IS_ADMIN) {
	// Site admins can see all logs
	$gedc=safe_GET('gedc');
} else {
	// Gedcom admins can only see logs relating to this gedcom
	$gedc=WT_GEDCOM;
}

$query=array();
$args =array();
if ($from) {
	$query[]='log_time>=?';
	$args []=$from;
}
if ($to) {
	$query[]='log_time<TIMESTAMPADD(DAY, 1 , ?)'; // before end of the day
	$args []=$to;
}
if ($type) {
	$query[]='log_type=?';
	$args []=$type;
}
if ($text) {
	$query[]="log_message LIKE CONCAT('%', ?, '%')";
	$args []=$text;
}
if ($ip) {
	$query[]="ip_address LIKE CONCAT('%', ?, '%')";
	$args []=$ip;
}
if ($user) {
	$query[]="user_name LIKE CONCAT('%', ?, '%')";
	$args []=$user;
}
if ($gedc) {
	$query[]="gedcom_name LIKE CONCAT('%', ?, '%')";
	$args []=$gedc;
}

$sql1=
	"SELECT COUNT(*)".
	" FROM {$TBLPREFIX}log".
	" LEFT JOIN {$TBLPREFIX}user   USING (user_id)".   // user may be deleted
	" LEFT JOIN {$TBLPREFIX}gedcom USING (gedcom_id)"; // gedcom may be deleted

$sql2=
	"SELECT log_time, log_type, log_message, ip_address, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name".
	" FROM {$TBLPREFIX}log".
	" LEFT JOIN {$TBLPREFIX}user   USING (user_id)".   // user may be deleted
	" LEFT JOIN {$TBLPREFIX}gedcom USING (gedcom_id)"; // gedcom may be deleted

if ($query) {
	$sql1.=" WHERE ".implode(' AND ', $query);
	// Order ascending, otherwise the current OFFSET/LIMIT will change when new events are logged
	$sql2.=" WHERE ".implode(' AND ', $query)." ORDER BY log_id";
}

if (safe_GET('export', 'yes')=='yes') {
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="webtrees-logs.csv"');
	$rows=WT_DB::prepare($sql2)->execute($args)->fetchAll();
	foreach ($rows as $row) {
		echo
			'"', $row->log_time, '",',
			'"', $row->log_type, '",',
			'"', str_replace('"', '""', $row->log_message), '",',
			'"', $row->ip_address, '",',
			'"', str_replace('"', '""', $row->user_name), '",',
			'"', str_replace('"', '""', $row->gedcom_name), '"',
			"\n";
	}
	exit;
}

if (safe_GET('delete', 'yes')=='yes') {
	$sql3=
		"DELETE {$TBLPREFIX}log FROM {$TBLPREFIX}log".
		" LEFT JOIN {$TBLPREFIX}user   USING (user_id)".   // user may be deleted
		" LEFT JOIN {$TBLPREFIX}gedcom USING (gedcom_id)"; // gedcom may be deleted
	if ($query) {
		$sql3.=" WHERE ".implode(' AND ', $query);
	}
	WT_DB::prepare($sql3)->execute($args);
}

$total_rows=WT_DB::prepare($sql1)->execute($args)->fetchOne();

// Paging
$limit =safe_GET('limit',  '\d+', 50);
$offset=safe_GET('offset', '\d+', $total_rows-$limit);

if ($offset+$limit>$total_rows) {
	$offset=$total_rows-$limit;
}
if ($offset<0) {
	$offset=0;
}

$sql2.=" LIMIT {$limit} OFFSET {$offset}";
$rows=WT_DB::prepare($sql2)->execute($args)->fetchAll();

print_header(i18n::translate('Logs'));

echo
	'<br/><form name="logs" method="get" action="'.WT_SCRIPT_NAME.'">',
	'<table class="list_table"><tr>',
	'<td class="topbottombar" colspan="7">', i18n::translate('Logs'), '</td>',
	'</tr><tr>',
	'<td class="descriptionbox" nowrap>',
	// I18N: %s are both user-input date fields
	i18n::translate('From %s to %s', '<input name="from" size="8" value="'.htmlspecialchars($from).'" />', '<input name="to" size="8" value="'.htmlspecialchars($to).'" />'),
	'</td>',
	'<td class="descriptionbox" nowrap>',
	i18n::translate('Type'), ' ', select_edit_control('type', array(''=>'', 'auth'=>'auth','change'=>'change','config'=>'config','debug'=>'debug','edit'=>'edit','error'=>'error','media'=>'media','search'=>'search'), null, $type, ''),
	'</td>',
	'<td class="descriptionbox" nowrap>',
	i18n::translate('Message'), ' <input name="text" size="12" value="', htmlspecialchars($text), '" /> ',
	'</td>',
	'<td class="descriptionbox" nowrap>',
	i18n::translate('IP address'), ' <input name="ip" size="12" value="', htmlspecialchars($ip), '" /> ',
	'</td>',
	'<td class="descriptionbox" nowrap>',
	i18n::translate('User'), ' <input name="user" size="12" value="', htmlspecialchars($user), '" /> ',
	'</td>',
	'<td class="descriptionbox" nowrap>',
	i18n::translate('Gedcom'), ' <input name="gedc" size="12" value="', htmlspecialchars($gedc), '" ', WT_USER_IS_ADMIN ? '' : 'disabled', '/> ',
	'</td>',
	'<td class="descriptionbox" rowspan="2" nowrap valign="middle">',
	'<input type="submit" value="', i18n::translate('Filter'), '"/> ',
	'</td>',
	'</tr><tr>',
	'<td class="descriptionbox" nowrap colspan="6">',
	i18n::translate('Results per page'), ' ', select_edit_control('limit', array('10'=>'10', '25'=>'25','50'=>'50','100'=>'100','1000'=>'1000'), null, $limit, ''),
	'</td></tr></table></form>';

if ($rows) {
	echo
		'<p align="center">',
		i18n::translate('Showing results %d to %d of %d', $offset+1, min($offset+$limit, $total_rows), $total_rows);

	$url=
		WT_SCRIPT_NAME.'?from='.urlencode($from).
		'&amp;to='.urlencode($to).
		'&amp;type='.urlencode($type).
		'&amp;text='.urlencode($text).
		'&amp;ip='.urlencode($ip).
		'&amp;user='.urlencode($user).
		'&amp;gedc='.urlencode($gedc).
		'&amp;limit='.$limit.
		'&amp;offset=';

	if ($offset>0) {
		echo ' | <a href="', $url, 0, '">', i18n::translate_c('first page', 'first'), '</a>';
		echo ' | <a href="', $url, max(0, $offset-$limit), '">', i18n::translate('previous'), '</a>';
	}
	if ($offset+$limit<$total_rows) {
		echo ' | <a href="', $url, min($total_rows-$limit, $offset+$limit), '">', i18n::translate('next'), '</a>';
		echo ' | <a href="', $url, $total_rows-$limit, '">', i18n::translate('last'), '</a>';
	}
	if (WT_USER_IS_ADMIN) {
		echo ' | <a href="', $url, '&amp;export=yes">', i18n::translate('export'), '</a>';
		echo ' | <a href="', $url, '&amp;delete=yes" onclick="return confirm(\'', htmlspecialchars(i18n::plural('Permanently delete this %s record?', 'Permanently delete these %s records?', $total_rows, $total_rows)) , '\')">', i18n::translate('delete'), '</a>';
	}

	echo
		'</p>',
		'<table class="list_table"><tr>',
		'<td class="descriptionbox" nowrap>', i18n::translate('Timestamp'), '</td>',
		'<td class="descriptionbox" nowrap>', i18n::translate('Type'), '</td>',
		'<td class="descriptionbox" nowrap>', i18n::translate('Message'), '</td>',
		'<td class="descriptionbox" nowrap>', i18n::translate('IP address'), '</td>',
		'<td class="descriptionbox" nowrap>', i18n::translate('User'), '</td>',
		'<td class="descriptionbox" nowrap>', i18n::translate('GEDCOM'), '</td>',
		'</tr>';

	foreach ($rows as $row) {
		echo
			'<tr valign="top">',
			'<td class="optionbox">', $row->log_time, '</td>',
			'<td class="optionbox">', $row->log_type, '</td>',
			'<td class="optionbox wrap">', nl2br(htmlspecialchars($row->log_message)), '</td>',
			'<td class="optionbox">', $row->ip_address, '</td>',
			'<td class="optionbox">', htmlspecialchars($row->user_name), '</td>',
			'<td class="optionbox">', htmlspecialchars($row->gedcom_name), '</td>',
			'</tr>';
	}
	echo '</table>';
}

print_footer();
