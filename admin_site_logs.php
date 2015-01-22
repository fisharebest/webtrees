<?php
// Log viewer.
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
use WT\User;

define('WT_SCRIPT_NAME', 'admin_site_logs.php');
require './includes/session.php';

$controller = new WT_Controller_Page;
$controller
	->restrictAccess(Auth::isManager())
	->setPageTitle(WT_I18N::translate('Logs'));

require WT_ROOT . 'includes/functions/functions_edit.php';

$earliest = WT_DB::prepare("SELECT DATE(MIN(log_time)) FROM `##log`")->execute(array())->fetchOne();
$latest   = WT_DB::prepare("SELECT DATE(MAX(log_time)) FROM `##log`")->execute(array())->fetchOne();

// Filtering
$action = WT_Filter::get('action');
$from   = WT_Filter::get('from', '\d\d\d\d-\d\d-\d\d', $earliest);
$to     = WT_Filter::get('to', '\d\d\d\d-\d\d-\d\d', $latest);
$type   = WT_Filter::get('type', 'auth|change|config|debug|edit|error|media|search');
$text   = WT_Filter::get('text');
$ip     = WT_Filter::get('ip');
$user   = WT_Filter::get('user');

$search = WT_Filter::get('search');
$search = isset($search['value']) ? $search['value'] : null;

if (Auth::isAdmin()) {
	// Administrators can see all logs
	$gedc = WT_Filter::get('gedc');
} else {
	// Managers can only see logs relating to this gedcom
	$gedc = WT_GEDCOM;
}

$query = array();
$args = array();
if ($search) {
	$query[] = "log_message LIKE CONCAT('%', ?, '%')";
	$args [] = $search;
}
if ($from) {
	$query[] = 'log_time>=?';
	$args [] = $from;
}
if ($to) {
	$query[] = 'log_time<TIMESTAMPADD(DAY, 1 , ?)'; // before end of the day
	$args [] = $to;
}
if ($type) {
	$query[] = 'log_type=?';
	$args [] = $type;
}
if ($text) {
	$query[] = "log_message LIKE CONCAT('%', ?, '%')";
	$args [] = $text;
}
if ($ip) {
	$query[] = "ip_address LIKE CONCAT('%', ?, '%')";
	$args [] = $ip;
}
if ($user) {
	$query[] = "user_name LIKE CONCAT('%', ?, '%')";
	$args [] = $user;
}
if ($gedc) {
	$query[] = "gedcom_name LIKE CONCAT('%', ?, '%')";
	$args [] = $gedc;
}

$SELECT1 =
	"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS log_time, log_type, log_message, ip_address, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name" .
	" FROM `##log`" .
	" LEFT JOIN `##user`   USING (user_id)" . // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted
$SELECT2 =
	"SELECT COUNT(*) FROM `##log`" .
	" LEFT JOIN `##user`   USING (user_id)" . // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted
if ($query) {
	$WHERE = " WHERE " . implode(' AND ', $query);
} else {
	$WHERE = '';
}

switch ($action) {
case 'delete':
	$DELETE =
		"DELETE `##log` FROM `##log`" .
		" LEFT JOIN `##user`   USING (user_id)" . // user may be deleted
		" LEFT JOIN `##gedcom` USING (gedcom_id)" . // gedcom may be deleted
		$WHERE;
	WT_DB::prepare($DELETE)->execute($args);
	break;
case 'export':
	Zend_Session::writeClose();
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="webtrees-logs.csv"');
	$rows = WT_DB::prepare($SELECT1 . $WHERE . ' ORDER BY log_id')->execute($args)->fetchAll();
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

	return;
case 'load_json':
	Zend_Session::writeClose();
	$start  = WT_Filter::getInteger('start');
	$length = WT_Filter::getInteger('length');
	Auth::user()->setPreference('admin_site_log_page_size', $length);

	if ($length > 0) {
		$LIMIT = " LIMIT " . $start . ',' . $length;
	} else {
		$LIMIT = "";
	}

	$order = WT_Filter::getArray('order');
	if ($order) {
		$ORDER_BY = ' ORDER BY ';
		foreach ($order as $key => $value) {
			if ($key > 0) {
				$ORDER_BY .= ',';
			}
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			switch ($value['dir']) {
			case 'asc':
				$ORDER_BY .= (1 + $value['column']) . ' ASC ';
				break;
			case 'desc':
				$ORDER_BY .= (1 + $value['column']) . ' DESC ';
				break;
			}
		}
	} else {
		$ORDER_BY = '1 ASC';
	}

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$data = WT_DB::prepare($SELECT1 . $WHERE . $ORDER_BY . $LIMIT)->execute($args)->fetchAll(PDO::FETCH_NUM);
	foreach ($data as &$datum) {
		$datum[2] = WT_Filter::escapeHtml($datum[2]);
		$datum[4] = WT_Filter::escapeHtml($datum[4]);
		$datum[5] = WT_Filter::escapeHtml($datum[5]);
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal = WT_DB::prepare($SELECT2 . $WHERE)->execute($args)->fetchOne();

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode(array(
		'sEcho'           => WT_Filter::getInteger('sEcho'), // Always an integer
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data
	));

	return;
}

$controller
	->pageHeader()
	->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
	->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
	->addExternalJavascript(WT_MOMENT_JS_URL)
	->addExternalJavascript(WT_BOOTSTRAP_DATETIMEPICKER_JS_URL)
	->addInlineJavascript('
		jQuery(".table-site-logs").dataTable( {
			processing: true,
			serverSide: true,
			ajax: "'.WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?action=load_json&from=' . $from . '&to=' . $to . '&type=' . $type . '&text=' . rawurlencode($text) . '&ip=' . rawurlencode($ip) . '&user=' . rawurlencode($user) . '&gedc=' . rawurlencode($gedc) . '",
			' . WT_I18N::datatablesI18N(array(10, 20, 50, 100, 500, 1000, -1)) . ',
			sorting: [[ 0, "desc" ]],
			pageLength: ' . Auth::user()->getPreference('admin_site_log_page_size', 20) . '
		});
		jQuery("#from,#to").parent("div").datetimepicker({
			format: "YYYY-MM-DD",
			minDate: "' . $earliest . '",
			maxDate: "' . $latest . '",
			locale: "' . WT_LOCALE . '",
			icons: {
				time: "fa fa-clock-o",
				date: "fa fa-calendar",
				up: "fa fa-arrow-up",
				down: "fa fa-arrow-down",
				previous: "fa fa-arrow-left",
				next: "fa fa-arrow-right",
				today: "fa fa-trash-o",
				clear: "fa fa-trash-o"
			}
		});
	');

$url =
	WT_SCRIPT_NAME . '?from=' . rawurlencode($from) .
	'&amp;to=' . rawurlencode($to) .
	'&amp;type=' . rawurlencode($type) .
	'&amp;text=' . rawurlencode($text) .
	'&amp;ip=' . rawurlencode($ip) .
	'&amp;user=' . rawurlencode($user) .
	'&amp;gedc=' . rawurlencode($gedc);

$users_array = array();
foreach (User::all() as $tmp_user) {
	$users_array[$tmp_user->getUserName()] = $tmp_user->getUserName();
}

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo WT_I18N::translate('Administration'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>
<h2><?php echo $controller->getPageTitle(); ?></h2>

<form class="form">
	<input type="hidden" name="action" value="show">

	<div class="row">
		<div class="form-group col-xs-6 col-sm-3">
			<label for="from">
				<?php echo /* I18N: label for the start of a date range (from x to y) */ WT_I18N::translate('From'); ?>
			</label>
			<div class="input-group date">
				<input type="text" class="form-control" id="from" name="from" value="<?php echo WT_Filter::escapeHtml($from); ?>">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-sm-3">
			<label for="to">
				<?php /* I18N: label for the end of a date range (from x to y) */ echo WT_I18N::translate('To'); ?>
			</label>
			<div class="input-group date">
				<input type="text" class="form-control" id="to" name="to" value="<?php echo WT_Filter::escapeHtml($to); ?>">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-sm-2">
			<label for="type">
				<?php echo WT_I18N::translate('Type'); ?>
			</label>
			<?php echo select_edit_control('type', array(''=>'', 'auth'=>'auth', 'config'=>'config', 'debug'=>'debug', 'edit'=>'edit', 'error'=>'error', 'media'=>'media', 'search'=>'search'), null, $type, 'class="form-control"'); ?>
		</div>

		<div class="form-group col-xs-6 col-sm-4">
			<label for="ip">
				<?php echo WT_I18N::translate('IP address'); ?>
			</label>
			<input class="form-control" type="text" id="ip" name="ip" value="<?php WT_Filter::escapeHtml($ip); ?>">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-sm-4">
			<label for="text">
				<?php echo WT_I18N::translate('Message'); ?>
			</label>
			<input class="form-control" type="text" id="text" name="text" value="<?php echo WT_Filter::escapeHtml($text); ?>">
		</div>

		<div class="form-group col-sm-4">
			<label for="user">
				<?php echo WT_I18N::translate('User'); ?>
			</label>
			<?php echo select_edit_control('user', $users_array, '', $user, 'class="form-control"'); ?>
		</div>

		<div class="form-group col-sm-4">
			<label for="gedc">
				<?php echo WT_I18N::translate('Family tree'); ?>
			</label>
			<?php echo select_edit_control('gedc', WT_Tree::getNameList(), '', $gedc, Auth::isAdmin() ? 'class="form-control"' : 'disabled class="form-control"'); ?>
		</div>
	</div>

	<div class="row text-center">
		<button type="submit" class="btn btn-primary">
			<?php echo WT_I18N::translate('Filter'); ?>
		</button>

		<button type="button" class="btn btn-primary" onclick="document.logs.action.value='export';return true;" <?php echo $action === 'show' ? '' : 'disabled'; ?>>
			<?php echo WT_I18N::translate('Export'); ?>
		</button>

		<button type="button" class="btn btn-primary" onclick="if (confirm('<?php echo WT_I18N::translate('Permanently delete these records?'); ?>')) {document.logs.action.value='delete'; return true;} else {return false;}" <?php echo $action === 'show' ? '' : 'disabled'; ?>>
			<?php echo WT_I18N::translate('Delete'); ?>
		</button>
	</div>
</form>

<?php if ($action): ?>
<table class="table table-bordered table-condensed table-hover table-site-logs">
	<thead>
		<tr>
			<th><?php echo WT_I18N::translate('Timestamp'); ?></th>
			<th><?php echo WT_I18N::translate('Type'); ?></th>
			<th><?php echo WT_I18N::translate('Message'); ?></th>
			<th><?php echo WT_I18N::translate('IP address'); ?></th>
			<th><?php echo WT_I18N::translate('User'); ?></th>
			<th><?php echo WT_I18N::translate('Family tree'); ?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?php endif; ?>
