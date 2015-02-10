<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use PDO;
use Zend_Session;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

define('WT_SCRIPT_NAME', 'admin_site_logs.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Website logs'));

$earliest = Database::prepare("SELECT DATE(MIN(log_time)) FROM `##log`")->execute(array())->fetchOne();
$latest   = Database::prepare("SELECT DATE(MAX(log_time)) FROM `##log`")->execute(array())->fetchOne();

// Filtering
$action = Filter::get('action');
$from   = Filter::get('from', '\d\d\d\d-\d\d-\d\d', $earliest);
$to     = Filter::get('to', '\d\d\d\d-\d\d-\d\d', $latest);
$type   = Filter::get('type', 'auth|change|config|debug|edit|error|media|search');
$text   = Filter::get('text');
$ip     = Filter::get('ip');
$user   = Filter::get('user');

$search = Filter::get('search');
$search = isset($search['value']) ? $search['value'] : null;

if (Auth::isAdmin()) {
	// Administrators can see all logs
	$gedc = Filter::get('gedc');
} else {
	// Managers can only see logs relating to this gedcom
	$gedc = WT_GEDCOM;
}

$WHERE = " WHERE 1";
$args = array();
if ($search) {
	$WHERE .= " AND log_message LIKE CONCAT('%', :search, '%')";
	$args['search'] = $search;
}
if ($from) {
	$WHERE .= " AND log_time >= :from";
	$args['from'] = $from;
}
if ($to) {
	$WHERE .= " AND log_time < TIMESTAMPADD(DAY, 1 , :to)"; // before end of the day
	$args['to'] = $to;
}
if ($type) {
	$WHERE .= " AND log_type = :type";
	$args['type'] = $type;
}
if ($text) {
	$WHERE .= " AND log_message LIKE CONCAT('%', :text, '%')";
	$args['text'] = $text;
}
if ($ip) {
	$WHERE .= " AND ip_address LIKE CONCAT('%', :ip, '%')";
	$args['ip'] = $ip;
}
if ($user) {
	$WHERE .= " AND user_name LIKE CONCAT('%', :user, '%')";
	$args['user'] = $user;
}
if ($gedc) {
	$WHERE .= " AND gedcom_name LIKE CONCAT('%', :gedc, '%')";
	$args['gedc'] = $gedc;
}

$SELECT_FILTERED =
	"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS log_time, log_type, log_message, ip_address, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name" .
	" FROM `##log`" .
	" LEFT JOIN `##user`   USING (user_id)" . // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)" . // gedcom may be deleted
	$WHERE;

$SELECT_UNFILTERED =
	"SELECT COUNT(*) FROM `##log`" .
	" LEFT JOIN `##user`   USING (user_id)" . // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted

$DELETE =
	"DELETE `##log` FROM `##log`" .
	" LEFT JOIN `##user`   USING (user_id)" . // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)" . // gedcom may be deleted
	$WHERE;

switch ($action) {
case 'delete':
	Database::prepare($DELETE)->execute($args);
	break;

case 'export':
	Zend_Session::writeClose();
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="webtrees-logs.csv"');
	$rows = Database::prepare($SELECT_FILTERED . ' ORDER BY log_id')->execute($args)->fetchAll();
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
	$start  = Filter::getInteger('start');
	$length = Filter::getInteger('length');
	$order  = Filter::getArray('order');

	if ($order) {
		$ORDER_BY = " ORDER BY ";
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
		$ORDER_BY = " ORDER BY 1 ASC";
	}

	if ($length) {
		Auth::user()->setPreference('admin_site_log_page_size', $length);
		$LIMIT = " LIMIT :limit OFFSET :offset";
		$args['limit'] = $length;
		$args['offset'] = $start;
	} else {
		$LIMIT = "";
	}

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$data = Database::prepare($SELECT_FILTERED . $ORDER_BY . $LIMIT)->execute($args)->fetchAll(PDO::FETCH_NUM);
	foreach ($data as &$datum) {
		$datum[2] = Filter::escapeHtml($datum[2]);
		$datum[4] = Filter::escapeHtml($datum[4]);
		$datum[5] = Filter::escapeHtml($datum[5]);
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal = Database::prepare($SELECT_UNFILTERED)->fetchOne();

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode(array(
		'draw'            => Filter::getInteger('draw'),
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data
	));

	return;
}

$controller
	->pageHeader()
	->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
	->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
	->addExternalJavascript(WT_MOMENT_JS_URL)
	->addExternalJavascript(WT_BOOTSTRAP_DATETIMEPICKER_JS_URL)
	->addInlineJavascript('
		jQuery(".table-site-logs").dataTable( {
			processing: true,
			serverSide: true,
			ajax: "'.WT_BASE_URL . WT_SCRIPT_NAME . '?action=load_json&from=' . $from . '&to=' . $to . '&type=' . $type . '&text=' . rawurlencode($text) . '&ip=' . rawurlencode($ip) . '&user=' . rawurlencode($user) . '&gedc=' . rawurlencode($gedc) . '",
			' . I18N::datatablesI18N(array(10, 20, 50, 100, 500, 1000, -1)) . ',
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

$users_array = array();
foreach (User::all() as $tmp_user) {
	$users_array[$tmp_user->getUserName()] = $tmp_user->getUserName();
}

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<form class="form" name="logs">
	<input type="hidden" name="action" value="show">

	<div class="row">
		<div class="form-group col-xs-6 col-sm-3">
			<label for="from">
				<?php echo /* I18N: label for the start of a date range (from x to y) */ I18N::translate('From'); ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="from" name="from" value="<?php echo Filter::escapeHtml($from); ?>" autocomplete="off">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-sm-3">
			<label for="to">
				<?php /* I18N: label for the end of a date range (from x to y) */ echo I18N::translate('To'); ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="to" name="to" value="<?php echo Filter::escapeHtml($to); ?>" autocomplete="off">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-sm-2">
			<label for="type">
				<?php echo I18N::translate('Type'); ?>
			</label>
			<?php echo select_edit_control('type', array(''=>'', 'auth'=>'auth', 'config'=>'config', 'debug'=>'debug', 'edit'=>'edit', 'error'=>'error', 'media'=>'media', 'search'=>'search'), null, $type, 'class="form-control"'); ?>
		</div>

		<div class="form-group col-xs-6 col-sm-4">
			<label for="ip">
				<?php echo I18N::translate('IP address'); ?>
			</label>
			<input class="form-control" type="text" id="ip" name="ip" value="<?php echo Filter::escapeHtml($ip); ?>">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-sm-4">
			<label for="text">
				<?php echo I18N::translate('Message'); ?>
			</label>
			<input class="form-control" type="text" id="text" name="text" value="<?php echo Filter::escapeHtml($text); ?>">
		</div>

		<div class="form-group col-sm-4">
			<label for="user">
				<?php echo I18N::translate('User'); ?>
			</label>
			<?php echo select_edit_control('user', $users_array, '', $user, 'class="form-control"'); ?>
		</div>

		<div class="form-group col-sm-4">
			<label for="gedc">
				<?php echo I18N::translate('Family tree'); ?>
			</label>
			<?php echo select_edit_control('gedc', Tree::getNameList(), '', $gedc, Auth::isAdmin() ? 'class="form-control"' : 'disabled class="form-control"'); ?>
		</div>
	</div>

	<div class="row text-center">
		<button type="submit" class="btn btn-primary">
			<?php echo I18N::translate('Filter'); ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="document.logs.action.value='export';return true;" <?php echo $action === 'show' ? '' : 'disabled'; ?>>
			<?php echo I18N::translate('Export'); ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="if (confirm('<?php echo I18N::translate('Permanently delete these records?'); ?>')) {document.logs.action.value='delete'; return true;} else {return false;}" <?php echo $action === 'show' ? '' : 'disabled'; ?>>
			<?php echo I18N::translate('Delete'); ?>
		</button>
	</div>
</form>

<?php if ($action): ?>
<table class="table table-bordered table-condensed table-hover table-site-logs">
	<caption class="sr-only">
		<?php echo $controller->getPageTitle(); ?>
	</caption>
	<thead>
		<tr>
			<th><?php echo I18N::translate('Timestamp'); ?></th>
			<th><?php echo I18N::translate('Type'); ?></th>
			<th><?php echo I18N::translate('Message'); ?></th>
			<th><?php echo I18N::translate('IP address'); ?></th>
			<th><?php echo I18N::translate('User'); ?></th>
			<th><?php echo I18N::translate('Family tree'); ?></th>
		</tr>
	</thead>
</table>
<?php endif; ?>
