<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use PDO;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Website logs'));

$earliest = Database::prepare("SELECT IFNULL(DATE(MIN(log_time)), CURDATE()) FROM `##log`")->execute([])->fetchOne();
$latest   = Database::prepare("SELECT IFNULL(DATE(MAX(log_time)), CURDATE()) FROM `##log`")->execute([])->fetchOne();

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
	$gedc = $WT_TREE->getName();
}

$sql_select =
	"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS log_id, log_time, log_type, log_message, ip_address, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name" .
	" FROM `##log`" .
	" LEFT JOIN `##user` USING (user_id)" . // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted

$where = " WHERE 1";
$args  = [];
if ($search) {
	$where .= " AND log_message LIKE CONCAT('%', :search, '%')";
	$args['search'] = $search;
}
if ($from) {
	$where .= " AND log_time >= :from";
	$args['from'] = $from;
}
if ($to) {
	$where .= " AND log_time < TIMESTAMPADD(DAY, 1 , :to)"; // before end of the day
	$args['to'] = $to;
}
if ($type) {
	$where .= " AND log_type = :type";
	$args['type'] = $type;
}
if ($text) {
	$where .= " AND log_message LIKE CONCAT('%', :text, '%')";
	$args['text'] = $text;
}
if ($ip) {
	$where .= " AND ip_address LIKE CONCAT('%', :ip, '%')";
	$args['ip'] = $ip;
}
if ($user) {
	$where .= " AND user_name LIKE CONCAT('%', :user, '%')";
	$args['user'] = $user;
}
if ($gedc) {
	$where .= " AND gedcom_name LIKE CONCAT('%', :gedc, '%')";
	$args['gedc'] = $gedc;
}

switch ($action) {
case 'delete':
	$sql_delete =
		"DELETE `##log` FROM `##log`" .
		" LEFT JOIN `##user` USING (user_id)" . // user may be deleted
		" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted

	Database::prepare($sql_delete . $where)->execute($args);
	break;

case 'export':
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="webtrees-logs.csv"');
	$rows = Database::prepare($sql_select . $where . ' ORDER BY log_id')->execute($args)->fetchAll();
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
	$start  = Filter::getInteger('start');
	$length = Filter::getInteger('length');
	$order  = Filter::getArray('order');

	if ($order) {
		$order_by = " ORDER BY ";
		foreach ($order as $key => $value) {
			if ($key > 0) {
				$order_by .= ',';
			}
			// Datatables numbers columns 0, 1, 2
			// MySQL numbers columns 1, 2, 3
			switch ($value['dir']) {
				case 'asc':
					$order_by .= (1 + $value['column']) . " ASC ";
					break;
				case 'desc':
					$order_by .= (1 + $value['column']) . " DESC ";
					break;
			}
		}
	} else {
		$order_by = " ORDER BY 1 ASC";
	}

	if ($length) {
		Auth::user()->setPreference('admin_site_log_page_size', $length);
		$limit          = " LIMIT :limit OFFSET :offset";
		$args['limit']  = $length;
		$args['offset'] = $start;
	} else {
		$limit = "";
	}

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$data = Database::prepare($sql_select . $where . $order_by . $limit)->execute($args)->fetchAll(PDO::FETCH_NUM);
	foreach ($data as &$datum) {
		$datum[2] = e($datum[2]);
		$datum[3] = '<span dir="auto">' . e($datum[3]) . '</span>';
		$datum[4] = '<span dir="auto">' . e($datum[4]) . '</span>';
		$datum[5] = '<span dir="auto">' . e($datum[5]) . '</span>';
		$datum[6] = '<span dir="auto">' . e($datum[6]) . '</span>';
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = (int) Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal    = (int) Database::prepare("SELECT COUNT(*) FROM `##log`")->fetchOne();

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode([
		'draw'            => Filter::getInteger('draw'),
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data,
	]);

	return;
}

$controller
	->pageHeader()
	->addInlineJavascript('
		$(".table-site-logs").dataTable( {
			processing: true,
			serverSide: true,
			ajax: "admin_site_logs.php?action=load_json&from=' . $from . '&to=' . $to . '&type=' . $type . '&text=' . rawurlencode($text) . '&ip=' . rawurlencode($ip) . '&user=' . rawurlencode($user) . '&gedc=' . rawurlencode($gedc) . '",
			' . I18N::datatablesI18N([10, 20, 50, 100, 500, 1000, -1]) . ',
			sorting: [[ 0, "desc" ]],
			pageLength: ' . Auth::user()->getPreference('admin_site_log_page_size', 10) . ',
			columns: [
			/* log_id      */ { visible: false },
			/* Timestamp   */ { sort: 0 },
			/* Type        */ { },
			/* message     */ { },
			/* IP address  */ { },
			/* User        */ { },
			/* Family tree */ { }
			]
		});
		$("#from,#to").parent("div").datetimepicker({
			format: "YYYY-MM-DD",
			minDate: "' . $earliest . '",
			maxDate: "' . $latest . '",
			locale: "' . WT_LOCALE . '",
			icons: {
				time: "far fa-clock",
				date: "fas fa-calendar-alt",
				up: "fas fa-arrow-up",
				down: "fas fa-arrow-down",
				previous: "fas fa-arrow-' . (I18N::direction() === 'rtl' ? 'right' : 'left') . '",
				next: "fas fa-arrow-' . (I18N::direction() === 'rtl' ? 'left' : 'right') . '",
				today: "far fa-trash-alt",
				clear: "far fa-trash-alt"
			}
		});
	');

$users_array = [];
foreach (User::all() as $tmp_user) {
	$users_array[$tmp_user->getUserName()] = $tmp_user->getUserName();
}

echo Bootstrap4::breadcrumbs([
	route('admin-control-panel') => I18N::translate('Control panel'),
], $controller->getPageTitle());
?>

<h1><?= $controller->getPageTitle() ?></h1>

<form class="form" name="logs">
	<input type="hidden" name="action" value="show">

	<div class="row">
		<div class="form-group col-xs-6 col-sm-3">
			<label for="from">
				<?= /* I18N: label for the start of a date range (from x to y) */ I18N::translate('From') ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="from" name="from" value="<?= e($from) ?>">
				<div class="input-group-append">
					<span class="input-group-text">
						<span class="fas fa-calendar-alt"></span>
					</span>
				</div>
			</div>
		</div>

		<div class="form-group col-xs-6 col-sm-3">
			<label for="to">
				<?= /* I18N: label for the end of a date range (from x to y) */ I18N::translate('To') ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="to" name="to" value="<?= e($to) ?>">
				<div class="input-group-append">
					<span class="input-group-text">
						<span class="fas fa-calendar-alt"></span>
					</span>
				</div>
			</div>
		</div>

		<div class="form-group col-xs-6 col-sm-2">
			<label for="type">
				<?= I18N::translate('Type') ?>
			</label>
			<?= Bootstrap4::select(['' => '', 'auth' => 'auth', 'config' => 'config', 'debug' => 'debug', 'edit' => 'edit', 'error' => 'error', 'media' => 'media', 'search' => 'search'], $type, ['id' => 'type', 'name' => 'type']) ?>
		</div>

		<div class="form-group col-xs-6 col-sm-4">
			<label for="ip">
				<?= I18N::translate('IP address') ?>
			</label>
			<input class="form-control" type="text" id="ip" name="ip" value="<?= e($ip) ?>">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-sm-4">
			<label for="text">
				<?= I18N::translate('Message') ?>
			</label>
			<input class="form-control" type="text" id="text" name="text" value="<?= e($text) ?>">
		</div>

		<div class="form-group col-sm-4">
			<label for="user">
				<?= I18N::translate('User') ?>
			</label>
			<?= Bootstrap4::select(['' => ''] + $users_array, $user, ['id' => 'user', 'name' => 'user']) ?>
		</div>

		<div class="form-group col-sm-4">
			<label for="gedc">
				<?= I18N::translate('Family tree') ?>
			</label>
			<?= Bootstrap4::select(['' => ''] + Tree::getNameList(), $gedc, ['id' => 'gedc', 'name' => 'gedc', 'disabled' => !Auth::isAdmin()]) ?>
		</div>
	</div>

	<div class="text-center">
		<button type="submit" class="btn btn-primary">
			<?= FontAwesome::decorativeIcon('search') ?>
 			<?= /* I18N: A button label. */ I18N::translate('search') ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="document.logs.action.value='export';return true;" <?= $action === 'show' ? '' : 'disabled' ?>>
			<?= FontAwesome::decorativeIcon('download') ?>
			<?= /* I18N: A button label. */ I18N::translate('download') ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="if (confirm('<?= I18N::translate('Permanently delete these records?') ?>')) {document.logs.action.value='delete'; return true;} else {return false;}" <?= $action === 'show' ? '' : 'disabled' ?>>
			<?= FontAwesome::decorativeIcon('delete') ?>
			<?= /* I18N: A button label. */ I18N::translate('delete') ?>
		</button>
	</div>
</form>

<?php if ($action): ?>
<table class="table table-bordered table-sm table-hover table-site-logs">
	<caption class="sr-only">
		<?= $controller->getPageTitle() ?>
	</caption>
	<thead>
		<tr>
			<th></th>
			<th><?= I18N::translate('Timestamp') ?></th>
			<th><?= I18N::translate('Type') ?></th>
			<th><?= I18N::translate('Message') ?></th>
			<th><?= I18N::translate('IP address') ?></th>
			<th><?= I18N::translate('User') ?></th>
			<th><?= I18N::translate('Family tree') ?></th>
		</tr>
	</thead>
</table>
<?php endif ?>
