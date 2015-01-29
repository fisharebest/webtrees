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

$rules_display = array(
	'unknown' => WT_I18N::translate('unknown'),
	'allow'   => /* I18N: An access rule - allow access to the site */ WT_I18N::translate('allow'),
	'deny'    => /* I18N: An access rule - deny access to the site */  WT_I18N::translate('deny'),
	'robot'   => /* I18N: http://en.wikipedia.org/wiki/Web_crawler */  WT_I18N::translate('robot'),
);

$rules_edit = array(
	'unknown' => WT_I18N::translate('unknown'),
	'allow'   => /* I18N: An access rule - allow access to the site */ WT_I18N::translate('allow'),
	'deny'    => /* I18N: An access rule - deny access to the site */  WT_I18N::translate('deny'),
	'robot'   => /* I18N: http://en.wikipedia.org/wiki/Web_crawler */  WT_I18N::translate('robot'),
);

// Form actions
switch (WT_Filter::post('action')) {
case 'save':
	if (WT_Filter::checkCsrf()) {
		$site_access_rule_id = WT_Filter::postInteger('site_access_rule_id');
		$ip_address_start    = WT_Filter::post('ip_address_start', WT_REGEX_IPV4);
		$ip_address_end      = WT_Filter::post('ip_address_end', WT_REGEX_IPV4);
		$user_agent_pattern  = WT_Filter::post('user_agent_pattern');
		$rule                = WT_Filter::post('rule', 'allow|deny|robot');
		$comment             = WT_Filter::post('comment');
		$user_agent_string   = $_SERVER['HTTP_USER_AGENT'];
		$ip_address          = $WT_REQUEST->getClientIp();

		if ($ip_address_start !== null && $ip_address_end !== null && $user_agent_pattern !== null && $rule !== null) {
			// This doesn't work with named placeholders.  The :user_agent_string parameter is not recognised...
			$oops = $rule !== 'allow' && WT_DB::prepare(
				"SELECT INET_ATON(?) BETWEEN INET_ATON(?) AND INET_ATON(?)" .
				" AND ? LIKE ?"
			)->execute(array(
				$ip_address,
				$ip_address_start,
				$ip_address_end,
				$user_agent_string,
				$user_agent_pattern,
			))->fetchOne();

			if ($oops) {
				WT_FlashMessages::addMessage(WT_I18N::translate('You cannot create a rule which would prevent yourself from accessing the site.'), 'danger');
			} elseif ($site_access_rule_id === null) {
				WT_DB::prepare(
					"INSERT INTO `##site_access_rule` (ip_address_start, ip_address_end, user_agent_pattern, rule, comment) VALUES (INET_ATON(:ip_address_start), INET_ATON(:ip_address_end), :user_agent_pattern, :rule, :comment)"
				)->execute(array(
					'ip_address_start'    => $ip_address_start,
					'ip_address_end'      => $ip_address_end,
					'user_agent_pattern'  => $user_agent_pattern,
					'rule'                => $rule,
					'comment'             => $comment,
				));
				WT_FlashMessages::addMessage(WT_I18N::translate('The site access rule has been created.'), 'success');
			} else {
				WT_DB::prepare(
					"UPDATE `##site_access_rule` SET ip_address_start = INET_ATON(:ip_address_start), ip_address_end = INET_ATON(:ip_address_end), user_agent_pattern = :user_agent_pattern, rule = :rule, comment = :comment WHERE site_access_rule_id = :site_access_rule_id"
				)->execute(array(
					'ip_address_start'    => $ip_address_start,
					'ip_address_end'      => $ip_address_end,
					'user_agent_pattern'  => $user_agent_pattern,
					'rule'                => $rule,
					'comment'             => $comment,
					'site_access_rule_id' => $site_access_rule_id,
				));
				WT_FlashMessages::addMessage(WT_I18N::translate('The site access rule has been updated.'), 'success');
			}
		}
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);

	return;

case 'delete':
	if (WT_Filter::checkCsrf()) {
		$site_access_rule_id = WT_Filter::postInteger('site_access_rule_id');
		WT_DB::prepare(
			"DELETE FROM `##site_access_rule` WHERE site_access_rule_id = :site_access_rule_id"
		)->execute(array(
			'site_access_rule_id' => $site_access_rule_id,
		));
		WT_FlashMessages::addMessage(WT_I18N::translate('The site access rule has been deleted.'), 'success');
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);

	return;
}

// Delete any "unknown" visitors that are now "known".
// This could happen every time we create/update a rule.
WT_DB::exec(
	"DELETE unknown" .
	" FROM `##site_access_rule` AS unknown" .
	" JOIN `##site_access_rule` AS known ON (unknown.user_agent_pattern LIKE known.user_agent_pattern)" .
	" WHERE unknown.rule='unknown' AND known.rule<>'unknown'" .
	" AND unknown.ip_address_start BETWEEN known.ip_address_start AND known.ip_address_end"
);

$controller = new WT_Controller_Page;
$controller
	->restrictAccess(Auth::isAdmin())
	->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
	->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
	->setPageTitle(WT_I18N::translate('Site access rules'));

$action = WT_Filter::get('action');
switch ($action) {
case 'load':
	Zend_Session::writeClose();
	// AJAX callback for datatables
	$search = WT_Filter::get('search');
	$search = $search['value'];
	$start  = WT_Filter::getInteger('start');
	$length = WT_Filter::getInteger('length');

	$sql  =
		"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS" .
		" '', INET_NTOA(ip_address_start), ip_address_start, INET_NTOA(ip_address_end), ip_address_end, user_agent_pattern, rule, comment, site_access_rule_id" .
		" FROM `##site_access_rule`";
	$args = array();

	if ($search) {
		$sql .=
			" WHERE (INET_ATON(?) BETWEEN ip_address_start AND ip_address_end" .
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
		$site_access_rule_id = $datum[8];

		$datum[0] = '<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-pencil"></i> <span class="caret"></span></button><ul class="dropdown-menu" role="menu"><li><a href="?action=edit&amp;site_access_rule_id=' . $site_access_rule_id . '"><i class="fa fa-fw fa-pencil"></i> ' . WT_I18N::translate('Edit') . '</a></li><li class="divider"><li><a href="#" onclick="if (confirm(\'' . WT_I18N::translate('Are you sure you want to delete “%s”?', WT_Filter::escapeJs($datum[5])) . '\')) delete_site_access_rule(' . $site_access_rule_id . '); return false;"><i class="fa fa-fw fa-trash-o"></i> ' . WT_I18N::translate('Delete') . '</a></li></ul></div>';

		$datum[6] = $rules_display[$datum[6]];
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal    = WT_DB::prepare("SELECT COUNT(*) FROM `##site_access_rule` WHERE rule <> 'unknown'")->fetchOne();

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode(array(
		'draw'            => WT_Filter::getInteger('draw'),
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data
	));
	break;

case 'edit':
case 'create':
	if (WT_Filter::get('action') === 'edit') {
		$controller->setPageTitle(WT_I18N::translate('Edit a site access rule'));
	} else {
		$controller->setPageTitle(WT_I18N::translate('Create a site access rule'));
	}

	$controller->pageHeader();

	$site_access_rule = WT_DB::prepare(
		"SELECT site_access_rule_id, INET_NTOA(ip_address_start) AS ip_address_start, INET_NTOA(ip_address_end) AS ip_address_end, user_agent_pattern, rule, comment" .
		" FROM `##site_access_rule` WHERE site_access_rule_id = :site_access_rule_id"
	)->execute(array(
		'site_access_rule_id' => WT_Filter::getInteger('site_access_rule_id'),
	))->fetchOneRow();

	$site_access_rule_id = $site_access_rule ? $site_access_rule->site_access_rule_id : null;
	$ip_address_start    = $site_access_rule ? $site_access_rule->ip_address_start : '0.0.0.0';
	$ip_address_end      = $site_access_rule ? $site_access_rule->ip_address_end : '255.255.255.255';
	$user_agent_pattern  = $site_access_rule ? $site_access_rule->user_agent_pattern : '%';
	$rule                = $site_access_rule ? $site_access_rule->rule : 'allow';
	$comment             = $site_access_rule ? $site_access_rule->comment : '';

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo WT_I18N::translate('Control panel'); ?></a></li>
		<li><a href="admin_site_access.php"><?php echo WT_I18N::translate('Site access rules'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>

	<h1><?php echo $controller->getPageTitle(); ?></h1>

	<form method="post" class="form form-horizontal">
		<input type="hidden" name="action" value="save">
		<input type="hidden" name="site_access_rule_id" value="<?php echo $site_access_rule_id; ?>">
		<?php echo WT_Filter::getCsrf(); ?>

		<!-- IP_ADDRESS_START -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="ip_address_start">
				<?php echo WT_I18N::translate('Start IP address'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="ip_address_start" name="ip_address_start" required pattern="<?php echo WT_REGEX_IPV4; ?>" value="<?php echo WT_Filter::escapeHtml($ip_address_start); ?>">
			</div>
		</div>

		<!-- IP_ADDRESS_END -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="ip_address_end">
				<?php echo WT_I18N::translate('End IP address'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="ip_address_end" name="ip_address_end" required pattern="<?php echo WT_REGEX_IPV4; ?>" value="<?php echo WT_Filter::escapeHtml($ip_address_end); ?>">
			</div>
		</div>

		<!-- USER_AGENT_PATTERN -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="user_agent_pattern">
				<?php echo WT_I18N::translate('User-agent string'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="user_agent_pattern" name="user_agent_pattern" required value="<?php echo WT_Filter::escapeHtml($user_agent_pattern); ?>" maxlength="255">
				<p class="small text-muted">
					<?php echo WT_I18N::noop('The “%” character is a wildcard, and will match zero or more other characters.'); ?>
				</p>
			</div>
		</div>

		<!-- RULE -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="rule">
				<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Rule'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo select_edit_control('rule', $rules_edit, null, $rule, 'class="form-control"'); ?>
			</div>
		</div>

		<!-- COMMET -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="comment">
				<?php echo WT_I18N::translate('Comment'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="comment" name="comment" value="<?php echo WT_Filter::escapeHtml($comment); ?>" maxlength="255">
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<button type="submit" class="btn btn-primary">
					<?php echo WT_I18N::translate('save'); ?>
				</button>
			</div>
		</div>
	</form>

	<?php
	break;

default:
	$controller
		->pageHeader()
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery(".table-site-access-rules").dataTable({
				ajax: "' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?action=load",
				serverSide: true,
				' . WT_I18N::datatablesI18N() . ',
				processing: true,
				stateSave: true,
				stateDuration: 180,
				columns: [
					/* 0 <edit>                  */ { sortable: false, class: "center" },
					/* 1 ip_address_start        */ { dataSort: 2, class: "ip_address" },
					/* 2 ip_address_start (sort) */ { type: "num", visible: false },
					/* 3 ip_address_end          */ { dataSort: 4, class: "ip_address" },
					/* 4 ip_address_end (sort)   */ { type: "num", visible: false },
					/* 5 user_agent_pattern      */ { class: "ua_string" },
					/* 6 comment                 */ { },
					/* 7 rule                    */ { }
				]
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

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo WT_I18N::translate('Control panel'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>

	<h1><?php echo $controller->getPageTitle(); ?></h1>

	<p><?php echo /* I18N: http://en.wikipedia.org/wiki/User_agent */ WT_I18N::translate('Restrict access to the site, using IP addresses and user-agent strings'); ?></p>

	<table class="table table-hover table-condensed table-bordered table-site-access-rules">
		<caption>
			<?php echo WT_I18N::translate('The following rules are used to decide whether a visitor is a human being (allow full access), a search-engine robot (allow restricted access) or an unwanted crawler (deny all access).'); ?>
		</caption>
		<thead>
		<tr>
			<th><?php echo WT_I18N::translate('Edit'); ?></th>
			<th><?php echo /* I18N [...] of a range of addresses */ WT_I18N::translate('Start IP address'); ?></th>
			<th>-</th>
			<th><?php echo /* I18N [...] of a range of addresses */ WT_I18N::translate('End IP address'); ?></th>
			<th>-</th>
			<th><?php echo /* I18N: http://en.wikipedia.org/wiki/User_agent_string */ WT_I18N::translate('User-agent string'); ?></th>
			<th><?php echo /* I18N: noun */ WT_I18N::translate('Rule'); ?></th>
			<th><?php echo WT_I18N::translate('Comment'); ?></th>
		</tr>
		</thead>
	</table>

	<!-- Implement the delete action -->
	<form class="hide" method="post" id="delete-form">
		<?php echo WT_Filter::getCsrf(); ?>
		<input type="hidden" name="site_access_rule_id" id="site-access-rule-id" value="">
		<input type="hidden" name="action" value="delete">
	</form>
	<script>
		function delete_site_access_rule(site_access_rule_id) {
			document.getElementById("site-access-rule-id").value = site_access_rule_id;
			document.getElementById("delete-form").submit();
		}
	</script>
	<?php
	break;
}