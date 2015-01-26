<?php
// Restrict/allow site access based on IP address and user-agent string
//
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

define('WT_SCRIPT_NAME', 'admin_site_access.php');
require './includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

$rules = array(
	'allow' => /* I18N: An access rule - allow access to the site */ WT_I18N::translate('allow'),
	'deny'  => /* I18N: An access rule - deny access to the site */  WT_I18N::translate('deny'),
	'robot' => /* I18N: http://en.wikipedia.org/wiki/Web_crawler */  WT_I18N::translate('robot'),
);

$controller = new WT_Controller_Page;
$controller
	->restrictAccess(Auth::isAdmin())
	->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
	->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
	->setPageTitle(WT_I18N::translate('Site access rules'));

$action = WT_Filter::get('action');
switch ($action) {
case 'delete':
	$user_access_rule_id = WT_Filter::getInteger('site_access_rule_id');
	WT_DB::prepare("DELETE FROM `##site_access_rule` WHERE site_access_rule_id=?")->execute(array($user_access_rule_id));
	break;
case 'allow':
case 'deny':
case 'robot':
	$user_access_rule_id = WT_Filter::getInteger('site_access_rule_id');
	WT_DB::prepare("UPDATE `##site_access_rule` SET rule=? WHERE site_access_rule_id=?")->execute(array($action, $user_access_rule_id));
	break;
case 'load_rules':
	Zend_Session::writeClose();
	// AJAX callback for datatables
	$search = WT_Filter::get('search');
	$search = $search['value'];
	$start  = WT_Filter::getInteger('start');
	$length = WT_Filter::getInteger('length');

	$sql =
		"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS" .
		" INET_NTOA(ip_address_start), ip_address_start, INET_NTOA(ip_address_end), ip_address_end, user_agent_pattern, rule, comment, site_access_rule_id" .
		" FROM `##site_access_rule`" .
		" WHERE rule<>'unknown'";
	$args = array();

	if ($search) {
		$sql .=
			" AND (INET_ATON(?) BETWEEN ip_address_start AND ip_address_end" .
			" OR INET_NTOA(ip_address_start) LIKE CONCAT('%', ?, '%')" .
			" OR INET_NTOA(ip_address_end) LIKE CONCAT('%', ?, '%')" .
			" OR user_agent_pattern LIKE CONCAT('%', ?, '%')" .
			" OR comment LIKE CONCAT('%', ?, '%'))";
		$args[] = $search;
		$args[] = $search;
		$args[] = $search;
		$args[] = $search;
		$args[] = $search;
	}

	$order = WT_Filter::getArray('order');
	if ($order) {
		$sql .= ' ORDER BY ';
		foreach ($order as $key => $value) {
			if ($key > 0) {
				$sql .= ',';
			}
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			switch ($value['dir']) {
			case 'asc':
				$sql .= (1 + $value['column']) . ' ASC ';
				break;
			case 'desc':
				$sql .= (1 + $value['column']) . ' DESC ';
				break;
			}
		}
	} else {
		$sql .= 'ORDER BY 1 ASC';
	}

	if ($length > 0) {
		$sql .= " LIMIT " . $start . ',' . $length;
	}

	// This becomes a JSON list, not a JSON array, so we need numeric keys.
	$data = WT_DB::prepare($sql)->execute($args)->fetchAll(PDO::FETCH_NUM);
	// Reformat the data for display
	foreach ($data as &$datum) {
		$site_access_rule_id = $datum[7];
		$user_agent = $datum[4];
		$datum[5] = $rules[$datum[5]];
		$datum[7] = '<button class="btn btn-danger" onclick="if (confirm(\'' . WT_Filter::escapeHtml(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($user_agent))) . '\')) { document.location=\'' . WT_SCRIPT_NAME . '?action=delete&amp;site_access_rule_id=' . $site_access_rule_id . '\'; }"><i class="fa fa-trash-o"></i></button>';
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal = WT_DB::prepare("SELECT COUNT(*) FROM `##site_access_rule` WHERE rule <> 'unknown'")->fetchOne();

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode(array(
		'draw'            => WT_Filter::getInteger('draw'), // Always an integer
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data
	));

	return;
case 'load_unknown':
	Zend_Session::writeClose();
	// AJAX callback for datatables
	$search = WT_Filter::get('search');
	$search = $search['value'];
	$start  = WT_Filter::getInteger('start');
	$length = WT_Filter::getInteger('length');

	$sql =
		"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS" .
		" INET_NTOA(ip_address_start), ip_address_start, user_agent_pattern, DATE(updated) AS updated, site_access_rule_id" .
		" FROM `##site_access_rule`" .
		" WHERE rule='unknown'";
	$args = array();

	if ($search) {
		$sql .=
			" AND (INET_ATON(ip_address_start) LIKE CONCAT('%', ?, '%')" .
			" OR user_agent_pattern LIKE CONCAT('%', ?, '%'))";
		$args[] = $search;
		$args[] = $search;
	}

	$order = WT_Filter::getArray('order');
	if ($order) {
		$sql .= ' ORDER BY ';
		foreach ($order as $key => $value) {
			if ($key > 0) {
				$sql .= ',';
			}
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			switch ($value['dir']) {
			case 'asc':
				$sql .= (1 + $value['column']) . ' ASC ';
				break;
			case 'desc':
				$sql .= (1 + $value['column']) . ' DESC ';
				break;
			}
		}
	} else {
		$sql .= 'ORDER BY 1 ASC';
	}


	if ($length > 0) {
		$sql .= " LIMIT " . $start . ',' . $length;
	}

	// This becomes a JSON list, not a JSON array, so we need numeric keys.
	$data = WT_DB::prepare($sql)->execute($args)->fetchAll(PDO::FETCH_NUM);
	// Reformat the data for display
	foreach ($data as &$datum) {
		$site_access_rule_id = $datum[4];
		$datum[4] = '<button class="btn btn-primary" onclick="document.location=\'' . WT_SCRIPT_NAME . '?action=allow&amp;site_access_rule_id=' . $site_access_rule_id . '\';"><i class="fa fa-check"></i></button>';
		$datum[5] = '<button class="btn btn-primary" onclick="document.location=\'' . WT_SCRIPT_NAME . '?action=deny&amp;site_access_rule_id=' . $site_access_rule_id . '\';"><i class="fa fa-ban"></i></button>';
		$datum[6] = '<button class="btn btn-primary" onclick="document.location=\'' . WT_SCRIPT_NAME . '?action=robot&amp;site_access_rule_id=' . $site_access_rule_id . '\';"><i class="fa fa-android"></i></button>';
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal    = WT_DB::prepare("SELECT COUNT(*) FROM `##site_access_rule` WHERE rule = 'unknown'")->fetchOne();

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode(array(
		'draw'            => WT_Filter::getInteger('draw'), // Always an integer
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data
	));

	return;
}

$controller
	->pageHeader()
	->addInlineJavascript('
		jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
		jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
		jQuery(".table-site-access-rules").dataTable({
			ajax: "' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?action=load_rules",
			serverSide: true,
			' . WT_I18N::datatablesI18N() . ',
			processing: true,
			stateSave: true,
			stateDuration: 180,
			columns: [
				/* 0 ip_address_start        */ { dataSort: 1, class: "ip_address" },
				/* 1 ip_address_start (sort) */ { type: "num", visible: false },
				/* 2 ip_address_end          */ { dataSort: 3, class: "ip_address" },
				/* 3 ip_address_end (sort)   */ { type: "num", visible: false },
				/* 4 user_agent_pattern      */ { class: "ua_string" },
				/* 5 comment                 */ { },
				/* 6 rule                    */ { },
				/* 7 <delete>                */ { sortable: false, class: "center" }
			],
		});

		jQuery(".table-unknown-site-visitors").dataTable({
			ajax: "' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?action=load_unknown",
			serverSide: true,
			' . WT_I18N::datatablesI18N() . ',
			processing: true,
			stateSave: true,
			stateDuration: 180,
			columns: [
				/* 0 ip_address         */ { dataSort: 1, class: "ip_address" },
				/* 0 ip_address (sort)  */ { type: "num", visible: false },
				/* 1 user_agent_pattern */ { class: "ua_string" },
				/* 2 updated            */ { class: "ua_string" },
				/* 3 <allowed>          */ { sortable: false, class: "center" },
				/* 4 <banned>           */ { sortable: false, class: "center" },
				/* 5 <search-engine>    */ { sortable: false, class: "center" }
			]
		});
	');

// Delete any "unknown" visitors that are now "known".
// This could happen every time we create/update a rule.
WT_DB::exec(
	"DELETE unknown" .
	" FROM `##site_access_rule` AS unknown" .
	" JOIN `##site_access_rule` AS known ON (unknown.user_agent_pattern LIKE known.user_agent_pattern)" .
	" WHERE unknown.rule='unknown' AND known.rule<>'unknown'" .
	" AND unknown.ip_address_start BETWEEN known.ip_address_start AND known.ip_address_end"
);

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo WT_I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<p><?php echo /* I18N: http://en.wikipedia.org/wiki/User_agent */ WT_I18N::translate('Restrict access to the site, using IP addresses and user-agent strings'); ?></p>

<p><?php echo WT_I18N::translate('The following rules are used to decide whether a visitor is a human being (allow full access), a search-engine robot (allow restricted access) or an unwanted crawler (deny all access).'); ?></p>

<table class="table table-hover table-condensed table-bordered table-site-access-rules">
	<caption class="sr-only">
		<?php echo WT_I18N::translate('The following rules are used to decide whether a visitor is a human being (allow full access), a search-engine robot (allow restricted access) or an unwanted crawler (deny all access).'); ?>
	</caption>
	<thead>
		<tr>
			<th><?php echo /* I18N [...] of a range of addresses */ WT_I18N::translate('Start IP address'); ?></th>
			<th>-</th>
			<th><?php echo /* I18N [...] of a range of addresses */ WT_I18N::translate('End IP address'); ?></th>
			<th>-</th>
			<th><?php echo /* I18N: http://en.wikipedia.org/wiki/User_agent_string */ WT_I18N::translate('User-agent string'); ?></th>
			<th><?php echo /* I18N: noun */ WT_I18N::translate('Rule'); ?></th>
			<th><?php echo WT_I18N::translate('Comment'); ?></th>
			<th><?php echo WT_I18N::translate('Delete'); ?></th>
		</tr>
	</thead>
</table>

<p><?php echo WT_I18N::translate('The following visitors were not recognized, and were assumed to be search engines.'); ?></p>

<table class="table table-hover table-condensed table-bordered table-unknown-site-visitors">
	<caption class="sr-only">
		<?php echo WT_I18N::translate('The following visitors were not recognized, and were assumed to be search engines.'); ?>
	</caption>
	<thead>
		<tr>
			<th rowspan="2"><?php /* I18N: http://en.wikipedia.org/wiki/IP_address */ echo WT_I18N::translate('IP address'); ?></th>
			<th rowspan="2">-</th>
			<th rowspan="2"><?php echo WT_I18N::translate('User-agent string'); ?></th>
			<th rowspan="2"><?php echo WT_I18N::translate('Date'); ?></th>
			<th colspan="3"><?php echo WT_I18N::translate('Create a new rule'); ?></th>
		</tr>
		<tr>
			<th><?php echo WT_I18N::translate('allow'); ?></th>
			<th><?php echo WT_I18N::translate('deny'); ?></th>
			<th><?php echo WT_I18N::translate('robot'); ?></th>
		</tr>
	</thead>
</table>
