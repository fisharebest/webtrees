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

use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Controller\PageController;
use PDO;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Changes'));

$earliest = Database::prepare("SELECT IFNULL(DATE(MIN(change_time)), CURDATE()) FROM `##change`")->execute([])->fetchOne();
$latest   = Database::prepare("SELECT IFNULL(DATE(MAX(change_time)), CURDATE()) FROM `##change`")->execute([])->fetchOne();

// Filtering
$action = Filter::get('action');
$from   = Filter::get('from', '\d\d\d\d-\d\d-\d\d', $earliest);
$to     = Filter::get('to', '\d\d\d\d-\d\d-\d\d', $latest);
$type   = Filter::get('type', 'accepted|rejected|pending');
$oldged = Filter::get('oldged');
$newged = Filter::get('newged');
$xref   = Filter::get('xref', WT_REGEX_XREF);
$user   = Filter::get('user');
$search = Filter::get('search');
$search = isset($search['value']) ? $search['value'] : null;

$statuses = [
	'accepted' => /* I18N: the status of an edit accepted/rejected/pending */ I18N::translate('accepted'),
	'rejected' => /* I18N: the status of an edit accepted/rejected/pending */ I18N::translate('rejected'),
	'pending'  => /* I18N: the status of an edit accepted/rejected/pending */ I18N::translate('pending'),
];

if (Auth::isAdmin()) {
	// Administrators can see all logs
	$gedc = Filter::get('gedc');
} else {
	// Managers can only see logs relating to this gedcom
	$gedc = $WT_TREE->getName();
}

$sql_select =
	"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS change_id, change_time, status, xref, old_gedcom, new_gedcom, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name" .
	" FROM `##change`" .
	" LEFT JOIN `##user` USING (user_id)" . // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted

$where = " WHERE 1";
$args  = [];
if ($search) {
	$where .= " AND (old_gedcom LIKE CONCAT('%', :search_1, '%') OR new_gedcom LIKE CONCAT('%', :search_2, '%'))";
	$args['search_1'] = $search;
	$args['search_2'] = $search;
}
if ($from) {
	$where .= " AND change_time >= :from";
	$args['from'] = $from;
}
if ($to) {
	$where .= " AND change_time < TIMESTAMPADD(DAY, 1 , :to)"; // before end of the day
	$args['to'] = $to;
}
if ($type) {
	$where .= " AND status = :status";
	$args['status'] = $type;
}
if ($oldged) {
	$where .= " AND old_gedcom LIKE CONCAT('%', :old_ged, '%')";
	$args['old_ged'] = $oldged;
}
if ($newged) {
	$where .= " AND new_gedcom LIKE CONCAT('%', :new_ged, '%')";
	$args['new_ged'] = $newged;
}
if ($xref) {
	$where .= " AND xref = :xref";
	$args['xref'] = $xref;
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
		"DELETE `##change` FROM `##change`" .
		" LEFT JOIN `##user` USING (user_id)" . // user may be deleted
		" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted

	Database::prepare($sql_delete . $where)->execute($args);
	break;

case 'export':
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="webtrees-changes.csv"');
	$rows = Database::prepare($sql_select . $where . ' ORDER BY change_id')->execute($args)->fetchAll();
	foreach ($rows as $row) {
		echo
			'"', $row->change_time, '",',
			'"', $row->status, '",',
			'"', $row->xref, '",',
			'"', str_replace('"', '""', $row->old_gedcom), '",',
			'"', str_replace('"', '""', $row->new_gedcom), '",',
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
		$order_by = " ORDER BY 1 DESC";
	}

	if ($length) {
		Auth::user()->setPreference('admin_site_change_page_size', $length);
		$limit          = " LIMIT :limit OFFSET :offset";
		$args['limit']  = $length;
		$args['offset'] = $start;
	} else {
		$limit = "";
	}

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$rows = Database::prepare($sql_select . $where . $order_by . $limit)->execute($args)->fetchAll(PDO::FETCH_OBJ);
	// Total filtered/unfiltered rows
	$recordsFiltered = (int) Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal    = (int) Database::prepare("SELECT COUNT(*) FROM `##change`")->fetchOne();

	$data      = [];
	$algorithm = new MyersDiff;

	foreach ($rows as $row) {
		$old_lines = preg_split('/[\n]+/', $row->old_gedcom, -1, PREG_SPLIT_NO_EMPTY);
		$new_lines = preg_split('/[\n]+/', $row->new_gedcom, -1, PREG_SPLIT_NO_EMPTY);

		$differences = $algorithm->calculate($old_lines, $new_lines);
		$diff_lines  = [];

		foreach ($differences as $difference) {
			switch ($difference[1]) {
			case MyersDiff::DELETE:
				$diff_lines[] = '<del>' . $difference[0] . '</del>';
				break;
			case MyersDiff::INSERT:
				$diff_lines[] = '<ins>' . $difference[0] . '</ins>';
				break;
			default:
				$diff_lines[] = $difference[0];
			}
		}

		// Only convert valid xrefs to links
		$record = GedcomRecord::getInstance($row->xref, Tree::findByName($gedc));
		$data[] = [
			$row->change_id,
			$row->change_time,
			I18N::translate($row->status),
			$record ? '<a href="' . $record->getHtmlUrl() . '">' . $record->getXref() . '</a>' : $row->xref,
			'<div class="gedcom-data" dir="ltr">' .
				preg_replace_callback('/@(' . WT_REGEX_XREF . ')@/',
					function ($match) use ($gedc) {
						$record = GedcomRecord::getInstance($match[1], Tree::findByName($gedc));

						return $record ? '<a href="' . $record->getHtmlUrl() . '">' . $match[0] . '</a>' : $match[0];
					},
					implode("\n", $diff_lines)
				) .
			'</div>',
			$row->user_name,
			$row->gedcom_name,
		];
	}

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
	->addExternalJavascript(WT_MOMENT_JS_URL)
	->addExternalJavascript(WT_BOOTSTRAP_DATETIMEPICKER_JS_URL)
	->addInlineJavascript('
		$(".table-site-changes").dataTable( {
			processing: true,
			serverSide: true,
			ajax: "?action=load_json&from=' . $from . '&to=' . $to . '&type=' . $type . '&oldged=' . rawurlencode($oldged) . '&newged=' . rawurlencode($newged) . '&xref=' . rawurlencode($xref) . '&user=' . rawurlencode($user) . '&gedc=' . rawurlencode($gedc) . '",
			' . I18N::datatablesI18N([10, 20, 50, 100, 500, 1000, -1]) . ',
			sorting: [[ 0, "desc" ]],
			pageLength: ' . Auth::user()->getPreference('admin_site_change_page_size', 10) . ',
			columns: [
			/* change_id   */ { visible: false },
			/* Timestamp   */ { sort: 0 },
			/* Status      */ { },
			/* Record      */ { },
			/* Data        */ {sortable: false},
			/* User        */ { },
			/* Family tree */ { }
			]
		});
		$("#from, #to").parent("div").datetimepicker({
			format: "YYYY-MM-DD",
			minDate: "' . $earliest . '",
			maxDate: "' . $latest . '",
			locale: "' . WT_LOCALE . '",
			useCurrent: false,
			icons: {
				time: "fa fa-clock-o",
				date: "fa fa-calendar",
				up: "fa fa-arrow-up",
				down: "fa fa-arrow-down",
				previous: "fa fa-arrow-' . (I18N::direction() === 'rtl' ? 'right' : 'left') . '",
				next: "fa fa-arrow-' . (I18N::direction() === 'rtl' ? 'left' : 'right') . '",
				today: "fa fa-trash-o",
				clear: "fa fa-trash-o"
			}
		});
	');

$users_array = [];
foreach (User::all() as $tmp_user) {
	$users_array[$tmp_user->getUserName()] = $tmp_user->getUserName();
}

echo Bootstrap4::breadcrumbs([
	'admin.php'              => I18N::translate('Control panel'),
	'admin_trees_manage.php' => I18N::translate('Manage family trees'),
], $controller->getPageTitle());
?>

<h1><?= $controller->getPageTitle() ?></h1>

<form class="form" name="logs">
	<input type="hidden" name="action" value="show">

	<div class="row">
		<div class="form-group col-xs-6 col-md-3">
			<label for="from">
				<?= /* I18N: label for the start of a date range (from x to y) */ I18N::translate('From') ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="from" name="from" value="<?= Filter::escapeHtml($from) ?>">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="to">
				<?= /* I18N: label for the end of a date range (from x to y) */ I18N::translate('To') ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="to" name="to" value="<?= Filter::escapeHtml($to) ?>">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="type">
				<?= I18N::translate('Status') ?>
			</label>
			<?= Bootstrap4::select($statuses, $type, ['id' => 'type', 'name' => 'type']) ?>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="xref">
				<?= I18N::translate('Record') ?>
			</label>
			<input class="form-control" type="text" id="xref" name="xref" value="<?= Filter::escapeHtml($xref) ?>">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-xs-6 col-md-3">
			<label for="oldged">
				<?= I18N::translate('Old data') ?>
			</label>
			<input class="form-control" type="text" id="oldged" name="oldged" value="<?= Filter::escapeHtml($oldged) ?>">
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="newged">
				<?= I18N::translate('New data') ?>
			</label>
			<input class="form-control" type="text" id="newged" name="newged" value="<?= Filter::escapeHtml($newged) ?>">
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="user">
				<?= I18N::translate('User') ?>
			</label>
			<?= Bootstrap4::select($users_array, $user, ['id' => 'user', 'name' => 'user']) ?>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="gedc">
				<?= I18N::translate('Family tree') ?>
			</label>
			<?= Bootstrap4::select(Tree::getNameList(), $gedc, ['id' => 'gedc', 'name' => 'gedc', 'disabled' => !Auth::isAdmin()]) ?>
		</div>
	</div>

	<div class="row text-center">
		<button type="submit" class="btn btn-primary">
			<?= I18N::translate('search') ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="document.logs.action.value='export';return true;" <?= $action === 'show' ? '' : 'disabled' ?>>
			<?= /* I18N: A button label. */ I18N::translate('download') ?>
		</button>

		<button type="submit" class="btn btn-primary" onclick="if (confirm('<?= I18N::translate('Permanently delete these records?') ?>')) {document.logs.action.value='delete'; return true;} else {return false;}" <?= $action === 'show' ? '' : 'disabled' ?>>
			<?= I18N::translate('delete') ?>
		</button>
	</div>
</form>

<?php if ($action): ?>
<table class="table table-bordered table-condensed table-hover table-site-changes">
	<caption class="sr-only">
		<?= $controller->getPageTitle() ?>
	</caption>
	<thead>
		<tr>
			<th></th>
			<th><?= I18N::translate('Timestamp') ?></th>
			<th><?= I18N::translate('Status') ?></th>
			<th><?= I18N::translate('Record') ?></th>
			<th><?= I18N::translate('Data') ?></th>
			<th><?= I18N::translate('User') ?></th>
			<th><?= I18N::translate('Family tree') ?></th>
		</tr>
	</thead>
</table>
<?php endif ?>
