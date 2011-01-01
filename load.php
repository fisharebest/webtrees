<?php
//
// Callback function for inline editing.
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @version $Id$

define('WT_SCRIPT_NAME', 'load.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';

// Header for an AJAX response.  There is no error/return value.
// Invalid requests just get an empty response - same as no data.
header('Content-type: text/html; charset=UTF-8');

// Which data set are we requesting?
$src=safe_GET('src');

switch ($src) {
case 'user_list':
	// callback for administration/user_list.php
	if (!WT_USER_IS_ADMIN) {
		exit;
	}
	$sSearch=safe_GET('sSearch');
	if ($sSearch) {
		$WHERE=
			" WHERE".
			" u.user_id>0 AND (".
			" real_name LIKE CONCAT('%', " . WT_DB::quote($sSearch) . ", '%') OR " .
			" user_name LIKE CONCAT('%', " . WT_DB::quote($sSearch) . ", '%') OR " .
			" email     LIKE CONCAT('%', " . WT_DB::quote($sSearch) . ", '%'))";
	} else {
		$WHERE=" WHERE u.user_id>0";
	}
	$iDisplayStart =safe_GET('iDisplayStart',  WT_REGEX_INTEGER);
	$iDisplayLength=safe_GET('iDisplayLength', WT_REGEX_INTEGER);
	if ($iDisplayLength>0) {
		$LIMIT=" LIMIT " . $iDisplayStart . ',' . $iDisplayLength;
	} else {
		$LIMIT="";
	}
	$iSortingCols  =safe_GET('iSortingCols',   WT_REGEX_INTEGER);
	if ($iSortingCols) {
		$ORDER_BY=' ORDER BY ';
		for ($i=0; $i<$iSortingCols; ++$i) {
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			$ORDER_BY.=(1+safe_GET('iSortCol_'.$i, WT_REGEX_INTEGER)).' '.safe_GET('sSortDir_'.$i, 'asc|desc').' ';
			if ($i<$iSortingCols-1) {
				$ORDER_BY.=',';
			}
		}
	} else {
		$ORDER_BY='';
	}

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$aaData=WT_DB::prepare(
		"SELECT SQL_CALC_FOUND_ROWS u.user_id AS user_id, real_name, user_name, email,".
		" us1.setting_value AS language,".
		" FROM_UNIXTIME(us2.setting_value) AS reg_timestamp,".
		" FROM_UNIXTIME(us3.setting_value) AS sessiontime,".
		" us4.setting_value AS verified,".
		" us5.setting_value AS verified_by_admin".
		" FROM `##user` u".
		" LEFT JOIN `##user_setting` us1 ON (u.user_id=us1.user_id AND us1.setting_name='language')".
		" LEFT JOIN `##user_setting` us2 ON (u.user_id=us2.user_id AND us2.setting_name='reg_timestamp')".
		" LEFT JOIN `##user_setting` us3 ON (u.user_id=us3.user_id AND us3.setting_name='sessiontime')".
		" LEFT JOIN `##user_setting` us4 ON (u.user_id=us4.user_id AND us4.setting_name='verified')".
		" LEFT JOIN `##user_setting` us5 ON (u.user_id=us5.user_id AND us5.setting_name='verified_by_admin')".
		$WHERE.
		$ORDER_BY.
		$LIMIT
	)->execute()->fetchAll(PDO::FETCH_NUM);

	// Reformat various columns for display
	foreach ($aaData as &$row) {
		$row[1]=edit_field_inline('user-real_name-'.$row[0], $row[1]);
		$row[2]=edit_field_inline('user-user_name-'.$row[0], $row[2]);
		$row[3]=edit_field_inline('user-email-'.    $row[0], $row[3]);
		$row[4]=edit_field_language_inline('user_setting-language-'.    $row[0], $row[4]);
		if ($row[0]==WT_USER_ID) {
			$row[7]=$row[7] ? WT_I18N::translate('yes') : WT_I18N::translate('no');
			$row[8]=$row[7] ? WT_I18N::translate('yes') : WT_I18N::translate('no');
		} else {
			// Cannot approve/unapprove your own account
			$row[7]=edit_field_yes_no_inline('user_setting-verified-'.         $row[0], $row[7]);
			$row[8]=edit_field_yes_no_inline('user_setting-verified_by_admin-'.$row[0], $row[8]);
		}
	}

	// Total filtered/unfiltered rows
	$iTotalDisplayRecords=WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$iTotalRecords=WT_DB::prepare("SELECT COUNT(*) FROM `##user` WHERE user_id>0")->fetchOne();

	echo json_encode(array(
		'sEcho'               =>(int)safe_GET('sEcho'), // See http://www.datatables.net/usage/server-side
		'iTotalRecords'       =>$iTotalRecords,
		'iTotalDisplayRecords'=>$iTotalDisplayRecords,
		'aaData'              =>$aaData
	));
	exit;
}
