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

define('WT_SCRIPT_NAME', 'admin_site_change.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Changes'));

$earliest = Database::prepare("SELECT DATE(MIN(change_time)) FROM `##change`")->execute(array())->fetchOne();
$latest   = Database::prepare("SELECT DATE(MAX(change_time)) FROM `##change`")->execute(array())->fetchOne();

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

$statuses = array(
	''         => '',
	'accepted' => /* I18N: the status of an edit accepted/rejected/pending */ I18N::translate('accepted'),
	'rejected' => /* I18N: the status of an edit accepted/rejected/pending */ I18N::translate('rejected'),
	'pending'  => /* I18N: the status of an edit accepted/rejected/pending */ I18N::translate('pending'),
);

if (Auth::isAdmin()) {
	// Administrators can see all logs
	$gedc = Filter::get('gedc');
} else {
	// Managers can only see logs relating to this gedcom
	$gedc = WT_GEDCOM;
}

$query = array();
$args = array();
if ($search) {
	$query[] = "(old_gedcom LIKE CONCAT('%', ?, '%') OR new_gedcom LIKE CONCAT('%', ?, '%'))";
	$args [] = $search;
	$args [] = $search;
}
if ($from) {
	$query[] = 'change_time>=?';
	$args [] = $from;
}
if ($to) {
	$query[] = 'change_time<TIMESTAMPADD(DAY, 1 , ?)'; // before end of the day
	$args [] = $to;
}
if ($type) {
	$query[] = 'status=?';
	$args [] = $type;
}
if ($oldged) {
	$query[] = "old_gedcom LIKE CONCAT('%', ?, '%')";
	$args [] = $oldged;
}
if ($newged) {
	$query[] = "new_gedcom LIKE CONCAT('%', ?, '%')";
	$args [] = $newged;
}
if ($xref) {
	$query[] = "xref = ?";
	$args [] = $xref;
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
	"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS change_time, status, xref, old_gedcom, new_gedcom, IFNULL(user_name, '<none>') AS user_name, IFNULL(gedcom_name, '<none>') AS gedcom_name" .
	" FROM `##change`" .
	" LEFT JOIN `##user`   USING (user_id)" . // user may be deleted
	" LEFT JOIN `##gedcom` USING (gedcom_id)"; // gedcom may be deleted
$SELECT2 =
	"SELECT COUNT(*) FROM `##change`" .
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
		"DELETE `##change` FROM `##change`" .
		" LEFT JOIN `##user`   USING (user_id)" . // user may be deleted
		" LEFT JOIN `##gedcom` USING (gedcom_id)" . // gedcom may be deleted
		$WHERE;
	Database::prepare($DELETE)->execute($args);
	break;
case 'export':
	Zend_Session::writeClose();
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="webtrees-changes.csv"');
	$rows = Database::prepare($SELECT1 . $WHERE . ' ORDER BY change_id')->execute($args)->fetchAll();
	foreach ($rows as $row) {
		$row->old_gedcom = str_replace('"', '""', $row->old_gedcom);
		$row->old_gedcom = str_replace("\n", '""', $row->old_gedcom);
		$row->new_gedcom = str_replace('"', '""', $row->new_gedcom);
		$row->new_gedcom = str_replace("\n", '""', $row->new_gedcom);
		echo
			'"', $row->change_time, '",',
			'"', $row->status, '",',
			'"', $row->xref, '",',
			'', $row->old_gedcom, '",',
			'', $row->new_gedcom, '",',
			'"', str_replace('"', '""', $row->user_name), '",',
			'"', str_replace('"', '""', $row->gedcom_name), '"',
			"\n";
	}

	return;
case 'load_json':
	Zend_Session::writeClose();
	$start  = Filter::getInteger('start');
	$length = Filter::getInteger('length');
	$search = Filter::get('search');
	$search = $search['value'];
	Auth::user()->setPreference('admin_site_change_page_size', $length);
	if ($length > 0) {
		$LIMIT = " LIMIT " . $start . ',' . $length;
	} else {
		$LIMIT = "";
	}

	$order = Filter::getArray('order');
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
				if (Filter::getInteger('iSortCol_' . $key) == 0) {
					$ORDER_BY .= 'change_id ASC '; // column 0 is "timestamp", using change_id gives the correct order for events in the same second
				} else {
					$ORDER_BY .= (1 + $value['column']) . ' ASC ';
				}
				break;
			case 'desc':
				if (Filter::getInteger('iSortCol_' . $key) == 0) {
					$ORDER_BY .= 'change_id DESC ';
				} else {
					$ORDER_BY .= (1 + $value['column']) . ' DESC ';
				}
				break;
			}
		}
	} else {
		$ORDER_BY = 'ORDER BY 1 DESC';
	}

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$data = Database::prepare($SELECT1 . $WHERE . $ORDER_BY . $LIMIT)->execute($args)->fetchAll(PDO::FETCH_NUM);
	foreach ($data as &$datum) {
		$datum[1] = I18N::translate($datum[1]);
		$datum[2] = '<a href="gedrecord.php?pid=' . $datum[2] . '&ged=' . $datum[6] . '" target="_blank">' . $datum[2] . '</a>';
		$datum[3] = '<pre>' . Filter::escapeHtml($datum[3]) . '</pre>';
		$datum[4] = '<pre>' . Filter::escapeHtml($datum[4]) . '</pre>';
		$datum[5] = Filter::escapeHtml($datum[5]);
		$datum[6] = Filter::escapeHtml($datum[6]);
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal = Database::prepare($SELECT2 . $WHERE)->execute($args)->fetchOne();

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
		jQuery(".table-site-changes").dataTable( {
			processing: true,
			serverSide: true,
			ajax: "'.WT_BASE_URL . WT_SCRIPT_NAME . '?action=load_json&from=' . $from . '&to=' . $to . '&type=' . $type . '&oldged=' . rawurlencode($oldged) . '&newged=' . rawurlencode($newged) . '&xref=' . rawurlencode($xref) . '&user=' . rawurlencode($user) . '&gedc=' . rawurlencode($gedc) . '",
			' . I18N::datatablesI18N(array(10, 20, 50, 100, 500, 1000, -1)) . ',
			sorting: [[ 0, "desc" ]],
			pageLength: ' . Auth::user()->getPreference('admin_site_change_page_size', 10) . ',
			columns: [
			/* Timestamp   */ { },
			/* Status      */ { },
			/* Record      */ { },
			/* Old data    */ { sortable: false },
			/* New data    */ { sortable: false },
			/* User        */ { },
			/* Family tree */ { }
			]
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
	'&amp;oldged=' . rawurlencode($oldged) .
	'&amp;newged=' . rawurlencode($newged) .
	'&amp;xref=' . rawurlencode($xref) .
	'&amp;user=' . rawurlencode($user) .
	'&amp;gedc=' . rawurlencode($gedc);

$users_array = array();
foreach (User::all() as $tmp_user) {
	$users_array[$tmp_user->getUserName()] = $tmp_user->getUserName();
}

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_trees_manage.php"><?php echo I18N::translate('Manage family trees'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<form class="form" name="logs">
	<input type="hidden" name="action" value="show">

	<div class="row">
		<div class="form-group col-xs-6 col-md-3">
			<label for="from">
				<?php echo /* I18N: label for the start of a date range (from x to y) */ I18N::translate('From'); ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="from" name="from" value="<?php echo Filter::escapeHtml($from); ?>">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="to">
				<?php /* I18N: label for the end of a date range (from x to y) */ echo I18N::translate('To'); ?>
			</label>
			<div class="input-group date">
				<input type="text" autocomplete="off" class="form-control" id="to" name="to" value="<?php echo Filter::escapeHtml($to); ?>">
				<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
			</div>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="type">
				<?php echo I18N::translate('Status'); ?>
			</label>
			<?php echo select_edit_control('type', $statuses, null, $type, 'class="form-control"'); ?>
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="text">
				<?php echo I18N::translate('Record'); ?>
			</label>
			<input class="form-control" type="text" id="xref" name="xref" value="<?php echo Filter::escapeHtml($xref); ?>">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-xs-6 col-md-3">
			<label for="text">
				<?php echo I18N::translate('Old data'); ?>
			</label>
			<input class="form-control" type="text" id="oldged" name="oldged" value="<?php echo Filter::escapeHtml($oldged); ?>">
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="text">
				<?php echo I18N::translate('New data'); ?>
			</label>
			<input class="form-control" type="text" id="newged" name="newged" value="<?php echo Filter::escapeHtml($newged); ?>">
		</div>

		<div class="form-group col-xs-6 col-md-3">
			<label for="user">
				<?php echo I18N::translate('User'); ?>
			</label>
			<?php echo select_edit_control('user', $users_array, '', $user, 'class="form-control"'); ?>
		</div>

		<div class="form-group col-xs-6 col-md-3">
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


		<button type="submit" class="btn btn-primary" onclick="if (confirm('<?php echo I18N::translate('Permanently delete these records?'); ?>')) {document.changes.action.value='delete'; return true;} else {return false;}" <?php echo $action === 'show' ? '' : 'disabled'; ?>>
			<?php echo I18N::translate('Delete'); ?>
		</button>
	</div>
</form>

<?php if ($action): ?>
<table class="table table-bordered table-condensed table-hover table-site-changes">
	<thead>
		<tr>
			<th><?php echo I18N::translate('Timestamp'); ?></th>
			<th><?php echo I18N::translate('Status'); ?></th>
			<th><?php echo I18N::translate('Record'); ?></th>
			<th><?php echo I18N::translate('Old data'); ?></th>
			<th><?php echo I18N::translate('New data'); ?></th>
			<th><?php echo I18N::translate('User'); ?></th>
			<th><?php echo I18N::translate('Family tree'); ?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?php endif; ?>
