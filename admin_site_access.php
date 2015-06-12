<?php
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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use PDO;

define('WT_SCRIPT_NAME', 'admin_site_access.php');
require './includes/session.php';

$rules_display = array(
	'unknown' => I18N::translate('unknown'),
	'allow'   => /* I18N: An access rule - allow access to the site */ I18N::translate('allow'),
	'deny'    => /* I18N: An access rule - deny access to the site */  I18N::translate('deny'),
	'robot'   => /* I18N: http://en.wikipedia.org/wiki/Web_crawler */  I18N::translate('robot'),
);

$rules_edit = array(
	'unknown' => I18N::translate('unknown'),
	'allow'   => /* I18N: An access rule - allow access to the site */ I18N::translate('allow'),
	'deny'    => /* I18N: An access rule - deny access to the site */  I18N::translate('deny'),
	'robot'   => /* I18N: http://en.wikipedia.org/wiki/Web_crawler */  I18N::translate('robot'),
);

// Form actions
switch (Filter::post('action')) {
case 'save':
	if (Filter::checkCsrf()) {
		$site_access_rule_id = Filter::postInteger('site_access_rule_id');
		$ip_address_start    = Filter::post('ip_address_start', WT_REGEX_IPV4);
		$ip_address_end      = Filter::post('ip_address_end', WT_REGEX_IPV4);
		$user_agent_pattern  = Filter::post('user_agent_pattern');
		$rule                = Filter::post('rule', 'allow|deny|robot');
		$comment             = Filter::post('comment');
		$user_agent_string   = Filter::server('HTTP_USER_AGENT');
		$ip_address          = WT_CLIENT_IP;

		if ($ip_address_start !== null && $ip_address_end !== null && $user_agent_pattern !== null && $rule !== null) {
			// This doesn't work with named placeholders.  The :user_agent_string parameter is not recognised...
			$oops = $rule !== 'allow' && Database::prepare(
				"SELECT INET_ATON(:ip_address) BETWEEN INET_ATON(:ip_address_start) AND INET_ATON(:ip_address_end)" .
				" AND :user_agent_string LIKE :user_agent_pattern"
			)->execute(array(
				'ip_address'         => $ip_address,
				'ip_address_start'   => $ip_address_start,
				'ip_address_end'     => $ip_address_end,
				'user_agent_string'  => $user_agent_string,
				'user_agent_pattern' => $user_agent_pattern,
			))->fetchOne();

			if ($oops) {
				FlashMessages::addMessage(I18N::translate('You cannot create a rule which would prevent yourself from accessing the website.'), 'danger');
			} elseif ($site_access_rule_id === null) {
				Database::prepare(
					"INSERT INTO `##site_access_rule` (ip_address_start, ip_address_end, user_agent_pattern, rule, comment) VALUES (INET_ATON(:ip_address_start), INET_ATON(:ip_address_end), :user_agent_pattern, :rule, :comment)"
				)->execute(array(
					'ip_address_start'    => $ip_address_start,
					'ip_address_end'      => $ip_address_end,
					'user_agent_pattern'  => $user_agent_pattern,
					'rule'                => $rule,
					'comment'             => $comment,
				));
				FlashMessages::addMessage(I18N::translate('The website access rule has been created.'), 'success');
			} else {
				Database::prepare(
					"UPDATE `##site_access_rule` SET ip_address_start = INET_ATON(:ip_address_start), ip_address_end = INET_ATON(:ip_address_end), user_agent_pattern = :user_agent_pattern, rule = :rule, comment = :comment WHERE site_access_rule_id = :site_access_rule_id"
				)->execute(array(
					'ip_address_start'    => $ip_address_start,
					'ip_address_end'      => $ip_address_end,
					'user_agent_pattern'  => $user_agent_pattern,
					'rule'                => $rule,
					'comment'             => $comment,
					'site_access_rule_id' => $site_access_rule_id,
				));
				FlashMessages::addMessage(I18N::translate('The website access rule has been updated.'), 'success');
			}
		}
	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;

case 'delete':
	if (Filter::checkCsrf()) {
		$site_access_rule_id = Filter::postInteger('site_access_rule_id');
		Database::prepare(
			"DELETE FROM `##site_access_rule` WHERE site_access_rule_id = :site_access_rule_id"
		)->execute(array(
			'site_access_rule_id' => $site_access_rule_id,
		));
		FlashMessages::addMessage(I18N::translate('The website access rule has been deleted.'), 'success');
	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
}

// Delete any "unknown" visitors that are now "known".
// This could happen every time we create/update a rule.
Database::exec(
	"DELETE unknown" .
	" FROM `##site_access_rule` AS unknown" .
	" JOIN `##site_access_rule` AS known ON (unknown.user_agent_pattern LIKE known.user_agent_pattern)" .
	" WHERE unknown.rule='unknown' AND known.rule<>'unknown'" .
	" AND unknown.ip_address_start BETWEEN known.ip_address_start AND known.ip_address_end"
);

$controller = new PageController;
$controller
	->restrictAccess(Auth::isAdmin())
	->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
	->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
	->setPageTitle(I18N::translate('Website access rules'));

$action = Filter::get('action');
switch ($action) {
case 'load':
	// AJAX callback for datatables
	$search = Filter::get('search');
	$search = $search['value'];
	$start  = Filter::getInteger('start');
	$length = Filter::getInteger('length');

	$sql =
		"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS" .
		" '', INET_NTOA(ip_address_start), ip_address_start, INET_NTOA(ip_address_end), ip_address_end, user_agent_pattern, rule, comment, site_access_rule_id" .
		" FROM `##site_access_rule`";
	$args = array();

	if ($search) {
		$sql .=
			" WHERE (INET_ATON(:search_1) BETWEEN ip_address_start AND ip_address_end" .
			" OR INET_NTOA(ip_address_start) LIKE CONCAT('%', :search_2, '%')" .
			" OR INET_NTOA(ip_address_end) LIKE CONCAT('%', :search_3, '%')" .
			" OR user_agent_pattern LIKE CONCAT('%', :search_4, '%')" .
			" OR comment LIKE CONCAT('%', :search_5, '%'))";
		$args['search_1'] = Filter::escapeLike($search);
		$args['search_2'] = Filter::escapeLike($search);
		$args['search_3'] = Filter::escapeLike($search);
		$args['search_4'] = Filter::escapeLike($search);
		$args['search_5'] = Filter::escapeLike($search);
	}

	$order = Filter::getArray('order');
	$sql .= ' ORDER BY';
	if ($order) {
		foreach ($order as $key => $value) {
			if ($key > 0) {
				$sql .= ',';
			}
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			switch ($value['dir']) {
			case 'asc':
				$sql .= " :col_" . $key . " ASC";
				break;
			case 'desc':
				$sql .= " :col_" . $key . " DESC";
				break;
			}
			$args['col_' . $key] = 1 + $value['column'];
		}
	} else {
		$sql .= ' 1 ASC';
	}

	if ($length > 0) {
		$sql .= " LIMIT :length OFFSET :start";
		$args['length'] = $length;
		$args['start']  = $start;
	}

	// This becomes a JSON list, not a JSON array, so we need numeric keys.
	$data = Database::prepare($sql)->execute($args)->fetchAll(PDO::FETCH_NUM);
	// Reformat the data for display
	foreach ($data as &$datum) {
		$site_access_rule_id = $datum[8];

		$datum[0] = '<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-pencil"></i> <span class="caret"></span></button><ul class="dropdown-menu" role="menu"><li><a href="?action=edit&amp;site_access_rule_id=' . $site_access_rule_id . '"><i class="fa fa-fw fa-pencil"></i> ' . I18N::translate('Edit') . '</a></li><li class="divider"><li><a href="#" onclick="if (confirm(\'' . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJs($datum[5])) . '\')) delete_site_access_rule(' . $site_access_rule_id . '); return false;"><i class="fa fa-fw fa-trash-o"></i> ' . I18N::translate('Delete') . '</a></li></ul></div>';
		$datum[5] = '<span dir="ltr">' . $datum[5] . '</span>';
		$datum[6] = $rules_display[$datum[6]];
		$datum[7] = '<span dir="auto">' . $datum[7] . '</span>';
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal    = Database::prepare("SELECT COUNT(*) FROM `##site_access_rule`")->fetchOne();

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode(array(
		'draw'            => Filter::getInteger('draw'),
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data,
	));
	break;

case 'edit':
case 'create':
	if (Filter::get('action') === 'edit') {
		$controller->setPageTitle(I18N::translate('Edit a website access rule'));
	} else {
		$controller->setPageTitle(I18N::translate('Create a website access rule'));
	}

	$controller->pageHeader();

	$site_access_rule = Database::prepare(
		"SELECT site_access_rule_id, INET_NTOA(ip_address_start) AS ip_address_start, INET_NTOA(ip_address_end) AS ip_address_end, user_agent_pattern, rule, comment" .
		" FROM `##site_access_rule` WHERE site_access_rule_id = :site_access_rule_id"
	)->execute(array(
		'site_access_rule_id' => Filter::getInteger('site_access_rule_id'),
	))->fetchOneRow();

	$site_access_rule_id = $site_access_rule ? $site_access_rule->site_access_rule_id : null;
	$ip_address_start    = $site_access_rule ? $site_access_rule->ip_address_start : '0.0.0.0';
	$ip_address_end      = $site_access_rule ? $site_access_rule->ip_address_end : '255.255.255.255';
	$user_agent_pattern  = $site_access_rule ? $site_access_rule->user_agent_pattern : '%';
	$rule                = $site_access_rule ? $site_access_rule->rule : 'allow';
	$comment             = $site_access_rule ? $site_access_rule->comment : '';

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
		<li><a href="admin_site_access.php"><?php echo I18N::translate('Website access rules'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>

	<h1><?php echo $controller->getPageTitle(); ?></h1>

	<form method="post" class="form form-horizontal">
		<input type="hidden" name="action" value="save">
		<input type="hidden" name="site_access_rule_id" value="<?php echo $site_access_rule_id; ?>">
		<?php echo Filter::getCsrf(); ?>

		<!-- IP_ADDRESS_START -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="ip_address_start">
				<?php echo I18N::translate('Start IP address'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="ip_address_start" name="ip_address_start" required pattern="<?php echo WT_REGEX_IPV4; ?>" value="<?php echo Filter::escapeHtml($ip_address_start); ?>">
			</div>
		</div>

		<!-- IP_ADDRESS_END -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="ip_address_end">
				<?php echo I18N::translate('End IP address'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="ip_address_end" name="ip_address_end" required pattern="<?php echo WT_REGEX_IPV4; ?>" value="<?php echo Filter::escapeHtml($ip_address_end); ?>">
			</div>
		</div>

		<!-- USER_AGENT_PATTERN -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="user_agent_pattern">
				<?php echo I18N::translate('User-agent string'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="user_agent_pattern" name="user_agent_pattern" required value="<?php echo Filter::escapeHtml($user_agent_pattern); ?>" maxlength="255" dir="ltr">
				<p class="small text-muted">
					<?php echo I18N::translate('The “%” character is a wildcard, and will match zero or more other characters.'); ?>
				</p>
			</div>
		</div>

		<!-- RULE -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="rule">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Rule'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo FunctionsEdit::selectEditControl('rule', $rules_edit, null, $rule, 'class="form-control"'); ?>
			</div>
		</div>

		<!-- COMMENT -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="comment">
				<?php echo I18N::translate('Comment'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="comment" name="comment" value="<?php echo Filter::escapeHtml($comment); ?>" maxlength="255" dir="auto">
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<button type="submit" class="btn btn-primary">
					<?php echo I18N::translate('save'); ?>
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
				ajax: "' . WT_BASE_URL . WT_SCRIPT_NAME . '?action=load",
				serverSide: true,
				' . I18N::datatablesI18N() . ',
				processing: true,
				stateSave: true,
				stateDuration: 180,
				sorting: [[1, "asc"]],
				columns: [
					/* 0 <edit>                  */ { sortable: false },
					/* 1 ip_address_start        */ { dataSort: 2, class: "ip_address" },
					/* 2 ip_address_start (sort) */ { type: "num", visible: false },
					/* 3 ip_address_end          */ { dataSort: 4, class: "ip_address" },
					/* 4 ip_address_end (sort)   */ { type: "num", visible: false },
					/* 5 user_agent_pattern      */ { class: "ua_string" },
					/* 6 comment                 */ { },
					/* 7 rule                    */ { }
				]
			});
		');

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>

	<h1><?php echo $controller->getPageTitle(); ?></h1>

	<p><?php echo /* I18N: http://en.wikipedia.org/wiki/User_agent */ I18N::translate('Restrict access to the website, using IP addresses and user-agent strings.'); ?></p>

	<table class="table table-hover table-condensed table-bordered table-site-access-rules">
		<caption>
			<?php echo I18N::translate('The following rules are used to decide whether a visitor is a human being (allow full access), a search-engine robot (allow restricted access) or an unwanted crawler (deny all access).'); ?>
		</caption>
		<thead>
		<tr>
			<th><?php echo I18N::translate('Edit'); ?></th>
			<th><?php echo /* I18N [...] of a range of addresses */ I18N::translate('Start IP address'); ?></th>
			<th>-</th>
			<th><?php echo /* I18N [...] of a range of addresses */ I18N::translate('End IP address'); ?></th>
			<th>-</th>
			<th><?php echo /* I18N: http://en.wikipedia.org/wiki/User_agent_string */ I18N::translate('User-agent string'); ?></th>
			<th><?php echo /* I18N: noun */ I18N::translate('Rule'); ?></th>
			<th><?php echo I18N::translate('Comment'); ?></th>
		</tr>
		</thead>
	</table>

	<!-- Implement the delete action -->
	<form class="hide" method="post" id="delete-form">
		<?php echo Filter::getCsrf(); ?>
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
