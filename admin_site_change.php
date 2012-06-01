<?php
// Change log viewer.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// $Id$

define('WT_SCRIPT_NAME', 'admin_site_change.php');
require './includes/session.php';

$controller=new WT_Controller_Base();
$controller
	->requireManagerLogin()
	->setPageTitle(WT_I18N::translate('Changes'));

require WT_ROOT.'includes/functions/functions_edit.php';

$statuses=array(
	''        =>'',
	'accepted'=>/* I18N: the status of an edit accepted/rejected/pending */ WT_I18N::translate('accepted'),
	'rejected'=>/* I18N: the status of an edit accepted/rejected/pending */ WT_I18N::translate('rejected'),
	'pending' =>/* I18N: the status of an edit accepted/rejected/pending */ WT_I18N::translate('pending' ),
);

$earliest=WT_DB::prepare("SELECT DATE(MIN(change_time)) FROM `##change`")->execute(array())->fetchOne();
$latest  =WT_DB::prepare("SELECT DATE(MAX(change_time)) FROM `##change`")->execute(array())->fetchOne();

// Filtering
$action=safe_GET('action');
$from  =safe_GET('from', '\d\d\d\d-\d\d-\d\d', $earliest);
$to    =safe_GET('to',   '\d\d\d\d-\d\d-\d\d', $latest);
$type  =safe_GET('type', array_keys($statuses));
$oldged=safe_GET('oldged');
$newged=safe_GET('newged');
$xref  =safe_GET('xref');
$user  =safe_GET('user');
if (WT_USER_IS_ADMIN) {
	// Administrators can see all logs
	$gedc=safe_GET('gedc');
} else {
	// Managers can only see logs relating to this gedcom
	$gedc=WT_GEDCOM;
}

$query=array();
$args =array();
if ($from) {
	$query[]='change_time>=?';
	$args []=$from;
}
if ($to) {
	$query[]='change_time<TIMESTAMPADD(DAY, 1 , ?)'; // before end of the day
	$args []=$to;
}
if ($type) {
	$query[]='status=?';
	$args []=$type;
}
if ($oldged) {
	$query[]="old_gedcom LIKE CONCAT('%', ?, '%')";
	$args []=$oldged;
}
if ($newged) {
	$query[]="new_gedcom LIKE CONCAT('%', ?, '%')";
	$args []=$newged;
}
if ($xref) {
	$query[]="xref LIKE CONCAT('%', ?, '%')";
	$args []=$xref;
}
if ($user) {
	$query[]="user_name LIKE CONCAT('%', ?, '%')";
	$args []=$user;
}
if ($gedc) {
	$query[]="gedcom_name LIKE CONCAT('%', ?, '%')";
	$args []=$gedc;
}

$SELECT1=
	"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS change_time, status, xref, old_gedcom, new_gedcom, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name".
	" FROM `##change`".
	" LEFT JOIN `##user`   USING (user_id)".   // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted
$SELECT2=
	"SELECT COUNT(*) FROM `##change`".
	" LEFT JOIN `##user`   USING (user_id)".   // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted
if ($query) {
	$WHERE=" WHERE ".implode(' AND ', $query);
} else {
	$WHERE='';
}

switch($action) {
case 'delete':
	$DELETE=
		"DELETE `##change` FROM `##change`".
		" LEFT JOIN `##user`   USING (user_id)".   // user may be deleted
		" LEFT JOIN `##gedcom` USING (gedcom_id)". // gedcom may be deleted
		$WHERE;
	WT_DB::prepare($DELETE)->execute($args);
	break;
case 'export':
	Zend_Session::writeClose();
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="webtrees-changes.csv"');
	$rows=WT_DB::prepare($SELECT1.$WHERE.' ORDER BY change_id')->execute($args)->fetchAll();
	foreach ($rows as $row) {
		$row->old_gedcom = str_replace('"', '""', $row->old_gedcom);
		$row->old_gedcom = str_replace("\n", '""', $row->old_gedcom);
		$row->new_gedcom = str_replace('"', '""', $row->new_gedcom);
		$row->new_gedcom = str_replace("\n", '""', $row->new_gedcom);
		echo
			'"', $row->change_time, '",',
			'"', $row->status, '",',
			'"', $row->xref, '",',
			'"', $row->old_gedcom, '",',
			'"', $row->new_gedcom, '",',
			'"', str_replace('"', '""', $row->user_name), '",',
			'"', str_replace('"', '""', $row->gedcom_name), '"',
			"\n";
	}
	exit;
case 'load_json':
	Zend_Session::writeClose();
	$iDisplayStart =(int)safe_GET('iDisplayStart');
	$iDisplayLength=(int)safe_GET('iDisplayLength');
	set_user_setting(WT_USER_ID, 'admin_site_change_page_size', $iDisplayLength);
	if ($iDisplayLength>0) {
		$LIMIT=" LIMIT " . $iDisplayStart . ',' . $iDisplayLength;
	} else {
		$LIMIT="";
	}
	$iSortingCols=safe_GET('iSortingCols');
	if ($iSortingCols) {
		$ORDER_BY=' ORDER BY ';
		for ($i=0; $i<$iSortingCols; ++$i) {
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			switch (safe_GET('sSortDir_'.$i)) {
			case 'asc':
				if ((int)safe_GET('iSortCol_'.$i)==0) {
					$ORDER_BY.='change_id ASC '; // column 0 is "timestamp", using change_id gives the correct order for events in the same second
				} else {
					$ORDER_BY.=(1+(int)safe_GET('iSortCol_'.$i)).' ASC ';
				}
				break;
			case 'desc':
				if ((int)safe_GET('iSortCol_'.$i)==0) {
					$ORDER_BY.='change_id DESC ';
				} else {
					$ORDER_BY.=(1+(int)safe_GET('iSortCol_'.$i)).' DESC ';
				}
				break;
			}
			if ($i<$iSortingCols-1) {
				$ORDER_BY.=',';
			}
		}
	} else {
		$ORDER_BY='1 DESC';
	}

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$aaData=WT_DB::prepare($SELECT1.$WHERE.$ORDER_BY.$LIMIT)->execute($args)->fetchAll(PDO::FETCH_NUM);
	foreach ($aaData as &$row) {
		$row[2]='<a href="gedrecord.php?pid='.$row[2].'" target="_blank">'.$row[2].'</a>';
		$row[3]='<pre>'.htmlspecialchars($row[3]).'</pre>';
		$row[4]='<pre>'.htmlspecialchars($row[4]).'</pre>';
	}
	
	// Total filtered/unfiltered rows
	$iTotalDisplayRecords=WT_DB::prepare("SELECT FOUND_ROWS()")->fetchColumn();
	$iTotalRecords=WT_DB::prepare($SELECT2.$WHERE)->execute($args)->fetchColumn();

	header('Content-type: application/json');
	echo json_encode(array( // See http://www.datatables.net/usage/server-side
		'sEcho'               =>(int)safe_GET('sEcho'),
		'iTotalRecords'       =>$iTotalRecords,
		'iTotalDisplayRecords'=>$iTotalDisplayRecords,
		'aaData'              =>$aaData
	));
	exit;
}

$controller
	->pageHeader()
	->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
	->addInlineJavaScript('
		var oTable=jQuery("#log_list").dataTable( {
			"sDom": \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "'.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME.'?action=load_json&from='.$from.'&to='.$to.'&type='.$type.'&oldged='.rawurlencode($oldged).'&newged='.rawurlencode($newged).'&xref='.rawurlencode($xref).'&user='.rawurlencode($user).'&gedc='.rawurlencode($gedc).'",
			'.WT_I18N::datatablesI18N(array(10,20,50,100,500,1000,-1)).',
			"bJQueryUI": true,
			"bAutoWidth":false,
			"aaSorting": [[ 0, "desc" ]],
			"iDisplayLength": '.get_user_setting(WT_USER_ID, 'admin_site_change_page_size', 10).',
			"sPaginationType": "full_numbers"
		});
	');

$url=
	WT_SCRIPT_NAME.'?from='.rawurlencode($from).
	'&amp;to='.rawurlencode($to).
	'&amp;type='.rawurlencode($type).
	'&amp;oldged='.rawurlencode($oldged).
	'&amp;newged='.rawurlencode($newged).
	'&amp;xref='.rawurlencode($xref).
	'&amp;user='.rawurlencode($user).
	'&amp;gedc='.rawurlencode($gedc);

$gedc_array=array();
foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
	$gedc_array[$ged_name]=WT_I18N::translate('%s', get_gedcom_setting($ged_id, 'title'));
}
$users_array=array_combine(get_all_users(), get_all_users());
uksort($users_array, 'strnatcasecmp');

echo
	'<form name="changes" method="get" action="'.WT_SCRIPT_NAME.'">',
		'<input type="hidden" name="action", value="show">',
		'<table class="site_change">',
			'<tr>',
				'<td colspan="6">',
					// I18N: %s are both user-input date fields
					WT_I18N::translate('From %s to %s', '<input class="log-date" name="from" value="'.htmlspecialchars($from).'">', '<input class="log-date" name="to" value="'.htmlspecialchars($to).'">'),
				'</td>',
			'</tr><tr>',
				'<td>',
					WT_I18N::translate('Status'), '<br>', select_edit_control('type', $statuses, null, $type, ''),
				'</td>',
				'<td>',
					WT_I18N::translate('Record'), '<br><input class="log-filter" name="xref" value="', htmlspecialchars($xref), '"> ',
				'</td>',
				'<td>',
					WT_I18N::translate('Old data'), '<br><input class="log-filter" name="oldged" value="', htmlspecialchars($oldged), '"> ',
				'</td>',
				'<td>',
					WT_I18N::translate('New data'), '<br><input class="log-filter" name="newged" value="', htmlspecialchars($newged), '"> ',
				'</td>',
				'<td>',
					WT_I18N::translate('User'), '<br>', select_edit_control('user', $users_array, '', $user, ''),
				'</td>',
				'<td>',
					WT_I18N::translate('Family tree'), '<br>',  select_edit_control('gedc', $gedc_array, '', $gedc, WT_USER_IS_ADMIN ? '' : 'disabled'),
				'</td>',
			'</tr><tr>',
				'<td colspan="6">',
					'<input type="submit" value="', WT_I18N::translate('Filter'), '">',
					'<input type="submit" value="', WT_I18N::translate('Export'), '" onclick="document.changes.action.value=\'export\';return true;" ', ($action=='show' ? '' : 'disabled="disabled"'),'>',
					'<input type="submit" value="', WT_I18N::translate('Delete'), '" onclick="if (confirm(\'', htmlspecialchars(WT_I18N::translate('Permanently delete these records?')) , '\')) {document.changes.action.value=\'delete\';return true;} else {return false;}" ', ($action=='show' ? '' : 'disabled="disabled"'),'>',
				'</td>',
			'</tr>',
		'</table>',
	'</form>';

if ($action) {
	echo
		'<br>',
		'<table id="log_list">',
			'<thead>',
				'<tr>',
					'<th>', WT_I18N::translate('Timestamp'), '</th>',
					'<th>', WT_I18N::translate('Status'), '</th>',
					'<th>', WT_I18N::translate('Record'), '</th>',
					'<th>', WT_I18N::translate('Old data'), '</th>',
					'<th>', WT_I18N::translate('New data'), '</th>',
					'<th>', WT_I18N::translate('User'), '</th>',
					'<th>', WT_I18N::translate('Family tree'), '</th>',
				'</tr>',
			'</thead>',
			'<tbody>',
	 	'</tbody>',
		'</table>';
}
