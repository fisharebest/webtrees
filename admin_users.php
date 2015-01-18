<?php
// Administrative User Interface.
//
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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
use WT\Log;
use WT\Theme;
use WT\User;

define('WT_SCRIPT_NAME', 'admin_users.php');
require './includes/session.php';

$controller = new WT_Controller_Page;
$controller->restrictAccess(Auth::isAdmin());

require_once WT_ROOT . 'includes/functions/functions_edit.php';

// Valid values for form variables
$ALL_EDIT_OPTIONS = array(
	'none'  => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Visitor'),
	'access'=> /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Member'),
	'edit'  => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Editor'),
	'accept'=> /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Moderator'),
	'admin' => /* I18N: Listbox entry; name of a role */ WT_I18N::translate('Manager')
);

// Form actions
switch (WT_Filter::post('action')) {
case 'save':
	if (WT_Filter::checkCsrf()) {
		$user_id        = WT_Filter::postInteger('user_id');
		$user           = User::find($user_id);
		$username       = WT_Filter::post('username', WT_REGEX_USERNAME);
		$real_name      = WT_Filter::post('real_name');
		$email          = WT_Filter::postEmail('email');
		$pass1          = WT_Filter::post('pass1', WT_REGEX_PASSWORD);
		$pass2          = WT_Filter::post('pass2', WT_REGEX_PASSWORD);
		$theme          = WT_Filter::post('theme', implode('|', array_keys(Theme::installedThemes())));
		$language       = WT_Filter::post('language', implode('|', array_keys(WT_I18N::installed_languages())), WT_LOCALE);
		$contact_method = WT_Filter::post('contact_method');
		$comment        = WT_Filter::post('comment');
		$auto_accept    = WT_Filter::postBool('auto_accept');
		$admin          = WT_Filter::postBool('admin');
		$visible_online = WT_Filter::postBool('visible_online');
		$edit_account   = WT_Filter::postBool('edit_account');
		$verified       = WT_Filter::postBool('verified');
		$approved       = WT_Filter::postBool('approved');

		if ($user_id === 0) {
			// Create a new user
			if (User::findByIdentifier($username)) {
				WT_FlashMessages::addMessage(WT_I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'));
			} elseif (User::findByIdentifier($email)) {
				WT_FlashMessages::addMessage(WT_I18N::translate('Duplicate email address.  A user with that email already exists.'));
			} elseif ($pass1 !== $pass2) {
				WT_FlashMessages::addMessage(WT_I18N::translate('Passwords do not match.'));
			} else {
				$user = User::create($username, $real_name, $email, $pass1);
				$user->setPreference('reg_timestamp', date('U'))->setPreference('sessiontime', '0');
				Log::addAuthenticationLog('User ->' . $username . '<- created');
			}
		} else {
			$user = User::find($user_id);
			if ($user) {
				$user->setEmail($email);
				$user->setUserName($username);
				$user->setRealName($real_name);
				if ($pass1 !== null && $pass1 === $pass2) {
					$user->setPassword($pass1);
				}
			}
		}

		if ($user) {
			$user
				->setPreference('theme', $theme)
				->setPreference('language', $language)
				->setPreference('contactmethod', $contact_method)
				->setPreference('comment', $comment)
				->setPreference('auto_accept', $auto_accept ? '1' : '0')
				->setPreference('admin', $admin ? '1' : '0')
				->setPreference('visibleonline', $visible_online ? '1' : '0')
				->setPreference('editaccount', $edit_account ? '1' : '0')
				->setPreference('verified', $verified ? '1' : '0')
				->setPreference('verified_by_admin', $approved ? '1' : '0');

			foreach (WT_Tree::getAll() as $tree) {
				$tree->setUserPreference($user, 'gedcomid', WT_Filter::post('gedcomid' . $tree->tree_id, WT_REGEX_XREF));
				$tree->setUserPreference($user, 'rootid', WT_Filter::post('rootid' . $tree->tree_id, WT_REGEX_XREF));
				$tree->setUserPreference($user, 'canedit', WT_Filter::post('canedit' . $tree->tree_id, implode('|', array_keys($ALL_EDIT_OPTIONS))));
				if (WT_Filter::post('gedcomid' . $tree->tree_id, WT_REGEX_XREF)) {
					$tree->setUserPreference($user, 'RELATIONSHIP_PATH_LENGTH', WT_Filter::postInteger('RELATIONSHIP_PATH_LENGTH' . $tree->tree_id, 0, 10, 0));
				} else {
					// Do not allow a path length to be set if the individual ID is not
					$tree->setUserPreference($user, 'RELATIONSHIP_PATH_LENGTH', null);
				}
			}
		}
	}

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);

	return;
}

switch (WT_Filter::get('action')) {
case 'loadrows':
	// Generate an AJAX/JSON response for datatables to load a block of rows
	$search = WT_Filter::postArray('search');
	$search = $search['value'];
	$start  = WT_Filter::postInteger('start');
	$length = WT_Filter::postInteger('length');

	$WHERE = " WHERE u.user_id > 0";
	$ARGS  = array();
	if ($search) {
		$WHERE .= " AND (" . " user_name LIKE CONCAT('%', ?, '%') OR " . " real_name LIKE CONCAT('%', ?, '%') OR " . " email     LIKE CONCAT('%', ?, '%'))";
		$ARGS = array($search, $search, $search);
	}
	Auth::user()->setPreference('admin_users_page_size', $length);
	if ($length > 0) {
		$LIMIT = " LIMIT " . $start . ',' . $length;
	} else {
		$LIMIT = "";
	}
	$order = WT_Filter::postArray('order');
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

	$sql = "SELECT SQL_CACHE SQL_CALC_FOUND_ROWS '', u.user_id, user_name, real_name, email, us1.setting_value, us2.setting_value, us2.setting_value, us3.setting_value, us3.setting_value, us4.setting_value, us5.setting_value" . " FROM `##user` u" . " LEFT JOIN `##user_setting` us1 ON (u.user_id=us1.user_id AND us1.setting_name='language')" . " LEFT JOIN `##user_setting` us2 ON (u.user_id=us2.user_id AND us2.setting_name='reg_timestamp')" . " LEFT JOIN `##user_setting` us3 ON (u.user_id=us3.user_id AND us3.setting_name='sessiontime')" . " LEFT JOIN `##user_setting` us4 ON (u.user_id=us4.user_id AND us4.setting_name='verified')" . " LEFT JOIN `##user_setting` us5 ON (u.user_id=us5.user_id AND us5.setting_name='approved')" . $WHERE . $ORDER_BY . $LIMIT;

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$data = WT_DB::prepare($sql)->execute($ARGS)->fetchAll(PDO::FETCH_NUM);

	$installed_languages = WT_I18N::installed_languages();

	// Reformat various columns for display
	foreach ($data as &$datum) {
		$user_id   = $datum[1];
		$user_name = $datum[2];

		if ($user_id != Auth::id()) {
			$admin_options = '<li class="divider">' . '<li><a onclick="modalDialog(\'index_edit.php?user_id=' . $user_id . '\', \'' . /* I18N: %s is a user's name. */
				WT_I18N::translate('Change the blocks on this user’s “My page”') . '\');" href="#"><i class="fa fa-fw fa-th-large"></i> ' . WT_I18N::translate('Change the blocks on this user’s “My page”') . '</a></li>' . '<li><a href="#" onclick="return masquerade(' . $user_id . ')"><i class="fa fa-fw fa-user"></i> ' . /* I18N: Pretend to be another user, by logging in as them */
				WT_I18N::translate('Masquerade as this user') . '</a></li>' . '<li><a href="#" onclick="delete_user(\'' . WT_I18N::translate('Are you sure you want to delete “%s”?', WT_Filter::escapeJs($user_name)) . '\', \'' . WT_Filter::escapeJs($user_id) . '\');"><i class="fa fa-fw fa-trash-o"></i> ' . WT_I18N::translate('Delete') . '</a></li>';
		} else {
			// Do not delete ourself!
			$admin_options = '';
		}

		$datum[0] = '<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-pencil"></i> <span class="caret"></span></button><ul class="dropdown-menu" role="menu"><li><a href="?action=edit&amp;user_id=' . $user_id . '"><i class="fa fa-fw fa-pencil"></i> ' . WT_I18N::translate('Edit') . '</a></li></li>' . $admin_options . '</ul></div>';
		// $datum[1] is the user ID
		// $datum[2] is the user name
		// $datum[3] is the real name
		// $datum[4] is the email address
		if ($user_id != Auth::id()) {
			$datum[4] = '<a href="#" onclick="return message(\'' . $datum[2] . '\', \'\', \'\');">' . $datum[4] . '</i></a>';
		}
		// $datum[5] is the langauge
		if (array_key_exists($datum[5], $installed_languages)) {
			$datum[5] = $installed_languages[$datum[5]];
		}
		// $datum[6] is the sortable registration timestamp
		$datum[7] = $datum[7] ? format_timestamp($datum[7]) : '';
		if (date("U") - $datum[6] > 604800 && !$datum[10]) {
			$datum[7] = '<span class="red">' . $datum[7] . '</span>';
		}
		// $datum[8] is the sortable last-login timestamp
		if ($datum[8]) {
			$datum[9] = format_timestamp($datum[8]) . '<br>' . WT_I18N::timeAgo(WT_TIMESTAMP - $datum[8]);
		} else {
			$datum[9] = WT_I18N::translate('Never');
		}
		$datum[10] = $datum[10] ? WT_I18N::translate('yes') : WT_I18N::translate('no');
		$datum[11] = $datum[11] ? WT_I18N::translate('yes') : WT_I18N::translate('no');
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = (int) WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal    = User::count();

	Zend_Session::writeClose();
	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode(array(
		'draw'            => WT_Filter::getInteger('draw'),
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data
	));

	return;

case 'edit':
	$user_id = WT_Filter::getInteger('user_id');

	if ($user_id === 0) {
		$controller->setPageTitle(WT_I18N::translate('Add a new user'));
		$tmp = new \stdClass;
		$tmp->user_id   = '';
		$tmp->user_name = '';
		$tmp->real_name = '';
		$tmp->email     = '';
		$user = new User($tmp);
	} else {
		$controller->setPageTitle(WT_I18N::translate('Edit user'));
		$user = User::find($user_id);
	}

	$controller
		->pageHeader()
		->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
		->addInlineJavascript('autocomplete();')
		->addInlineJavascript('
			jQuery(".relpath").change(function() {
				var fieldIDx = jQuery(this).attr("id");
				var idNum = fieldIDx.replace("RELATIONSHIP_PATH_LENGTH","");
				var newIDx = "gedcomid"+idNum;
				if (jQuery("#"+newIDx).val()=="") {
					alert("'.WT_I18N::translate('You must specify an individual record before you can restrict the user to their immediate family.') . '");
					jQuery(this).val("");
				}
			});
			function regex_quote(str) {
				return str.replace(/[\\\\.?+*()[\](){}|]/g, "\\\\$&");
			}
		');

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo WT_I18N::translate('Administration'); ?></a></li>
		<li><a href="admin_users.php"><?php echo WT_I18N::translate('User administration'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>
	<h2><?php echo $controller->getPageTitle(); ?></h2>

	<form class="form-horizontal" name="newform" method="post" role="form" action="?action=edit" autocomplete="off">
		<?php echo WT_Filter::getCsrf(); ?>
		<input type="hidden" name="action" value="save">
		<input type="hidden" name="user_id" value="<?php echo $user->getUserId(); ?>">

		<!-- REAL NAME -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="real_name">
				<?php echo WT_I18N::translate('Real name'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="real_name" name="real_name" required maxlength="64" value="<?php echo WT_Filter::escapeHtml($user->getRealName()); ?>" autofocus>
				<p class="small text-muted">
					<?php echo WT_I18N::translate('This is your real name, as you would like it displayed on screen.'); ?>
				</p>
			</div>
		</div>

		<!-- USER NAME -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="username">
				<?php echo WT_I18N::translate('Username'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="username" name="username" required maxlength="32" value="<?php echo WT_Filter::escapeHtml($user->getUserName()); ?>" pattern="<?php echo WT_REGEX_USERNAME; ?>">
				<p class="small text-muted">
					<?php echo WT_I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.'), ' ', WT_I18N::translate('Usernames may not contain the following characters: &lt; &gt; &quot; %% { } ;'); ?>
				</p>
			</div>
		</div>

		<!-- PASSWORD -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="pass1">
				<?php echo WT_I18N::translate('Password'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="password" id="pass1" name="pass1" pattern = "<?php echo WT_REGEX_PASSWORD; ?>" placeholder="<?php echo WT_I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, WT_I18N::number(WT_MINIMUM_PASSWORD_LENGTH)); ?>" <?php echo $user->getUserId() ? '' : 'required'; ?> onchange="form.pass2.pattern = regex_quote(this.value);">
				<p class="small text-muted">
					<?php echo 'Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different to “SECRET”.'; ?>
				</p>
			</div>
		</div>

		<!-- CONFIRM PASSWORD -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="pass2">
				<?php echo WT_I18N::translate('Confirm password'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="password" id="pass2" name="pass2" pattern = "<?php echo WT_REGEX_PASSWORD; ?>" placeholder="<?php echo WT_I18N::translate('Type the password again.'); ?>" <?php echo $user->getUserId() ? '' : 'required'; ?>>
			</div>
		</div>

		<!-- EMAIL ADDRESS -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="email">
				<?php echo WT_I18N::translate('Email address'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="email" id="email" name="email" required maxlength="64" value="<?php echo WT_Filter::escapeHtml($user->getEmail()); ?>">
			</div>
		</div>

		<!-- EMAIL VERIFIED -->
		<!-- ACCOUNT APPROVED -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="verified">
				<?php echo WT_I18N::translate('Account approval and email verification'); ?>
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="verified" value="1" <?php echo $user->getPreference('verified') ? 'checked' : ''?>>
						<?php echo WT_I18N::translate('Email verified'); ?>
					</label>
					<label>
						<input type="checkbox" name="approved" value="1" <?php echo $user->getPreference('verified_by_admin') ? 'checked' : ''; ?>>
						<?php echo WT_I18N::translate('Approved by administrator'); ?>
					</label>
					<p class="small text-muted">
						<?php echo WT_I18N::translate('When a user registers for an account, an email is sent to their email address with a verification link.  When they click this link, we know the email address is correct, and the “email verified” option is selected automatically.'); ?>
					</p>
					<p class="small text-muted">
						<?php echo WT_I18N::translate('If an administrator creates a user account, the verification email is not sent, and the email must be verified manually.'); ?>
					</p>
					<p class="small text-muted">
						<?php echo WT_I18N::translate('You should not approve an account unless you know that the email address is correct.'); ?>
					</p>
					<p class="small text-muted">
						<?php echo WT_I18N::translate('A user will not be able to login until both the “email verified” and “approved by administrator” options are selected.'); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- LANGUAGE -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="language">
				<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Language'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo select_edit_control('language', WT_I18N::installed_languages(), null, $user->getPreference('language', WT_LOCALE), 'class="form-control"'); ?>
			</div>
		</div>

		<!-- AUTO ACCEPT -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="auto_accept">
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="auto_accept" value="1" <?php echo $user->getPreference('auto_accept') ? 'checked' : ''; ?>>
						<?php echo WT_I18N::translate('Automatically approve changes made by this user'); ?>
					</label>
					<p class="small text-muted">
						<?php echo WT_I18N::translate('Normally, any changes made to a family tree need to be approved by a moderator.  This option allows a user to make changes without needing a moderator’s approval.'); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- EDIT ACCOUNT -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="edit_account">
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="edit_account" value="1" <?php echo $user->getPreference('edit_account') ? 'checked' : ''; ?>>
						<?php echo WT_I18N::translate('Allow this user to edit his account information'); ?>
					</label>
					<p class="small text-muted">
						<?php echo WT_I18N::translate('If this box is checked, this user will be able to edit his account information.  Although this is not generally recommended, you can create a single user name and password for multiple users.  When this box is unchecked for all users with the shared account, they are prevented from editing the account information and only an administrator can alter that account.'); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- VISIBLE ONLINE -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="edit_account">
				<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Visible online'); ?>
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="visible_online" value="1" <?php echo $user->getPreference('visible_online') ? 'checked' : ''; ?>>
						<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Visible to other users when online'); ?>
					</label>
					<p class="small text-muted">
						<?php echo WT_I18N::translate('This checkbox controls your visibility to other users while you’re online.  It also controls your ability to see other online users who are configured to be visible.<br><br>When this box is unchecked, you will be completely invisible to others, and you will also not be able to see other online users.  When this box is checked, exactly the opposite is true.  You will be visible to others, and you will also be able to see others who are configured to be visible.'); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- CONTACT METHOD -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="contactmethod">
				<?php echo /* I18N: A configuration setting */ WT_I18N::translate('Preferred contact method'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo edit_field_contact('contactmethod', $user->getPreference('contact_method')); ?>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Preferred contact method” configuration setting */WT_I18N::translate('Site members can send each other messages.  You can choose to how these messages are sent to you, or choose not receive them at all.'); ?>
				</p>
			</div>
		</div>

		<!-- THEME -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="theme">
				<?php echo WT_I18N::translate('Theme'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo select_edit_control('theme', Theme::themeNames(), WT_I18N::translate('<default theme>'), $user->getPreference('theme'), 'class="form-control"'); ?>
			</div>
		</div>

		<!-- COMMENTS -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="comment">
				<?php echo WT_I18N::translate('Admin comments on user'); ?>
			</label>
			<div class="col-sm-9">
				<textarea class="form-control" id="comment" name="comment" rows="5" maxlength="255"><?php echo WT_Filter::escapeHtml($user->getPreference('comment')); ?></textarea>
			</div>
		</div>

		<!-- ADMINISTRATOR -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="admin">
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="admin" value="1" <?php echo $user->getPreference('admin') ? 'checked' : ''; ?>>
						<?php echo WT_I18N::translate('Administrator'); ?>
					</label>
					<p class="small text-muted">
						<?php echo help_link('role'); ?>
					</p>
				</div>
			</div>
		</div>

		<h3><?php echo WT_I18N::translate('Family tree access and settings'); ?></h3>

		<table class="table table-bordered table-condensed table-responsive">
			<thead>
				<tr>
					<th><?php echo WT_I18N::translate('Family tree'); ?></th>
					<th><?php echo WT_I18N::translate('Default individual'), help_link('default_individual'); ?></th>
					<th><?php echo WT_I18N::translate('Individual record'), help_link('useradmin_gedcomid'); ?></th>
					<th><?php echo WT_I18N::translate('Role'), help_link('role'); ?></th>
					<th><?php echo WT_I18N::translate('Restrict to immediate family'), help_link('RELATIONSHIP_PATH_LENGTH'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach (WT_Tree::getAll() as $tree): ?>
				<tr>
					<td>
						<?php echo $tree->tree_title_html; ?>
					</td>
					<td>
						<input
							data-autocomplete-type="INDI"
							data-autocomplete-ged="<?php echo $tree->tree_name_html; ?>"
							type="text"
							size="12"
							name="rootid<?php echo $tree->tree_id; ?>"
							id="rootid<?php echo $tree->tree_id; ?>"
							value="<?php echo WT_Filter::escapeHtml($tree->getUserPreference($user, 'rootid')); ?>"
						>
						<?php echo print_findindi_link('rootid' . $tree->tree_id, $tree->tree_name); ?>
					</td>
					<td>
						<input
							data-autocomplete-type="INDI"
							data-autocomplete-ged="<?php echo $tree->tree_name_html; ?>"
							type="text"
							size="12"
							name="gedcomid<?php echo $tree->tree_id; ?>"
							id="gedcomid<?php echo $tree->tree_id; ?>"
							value="<?php echo WT_Filter::escapeHtml($tree->getUserPreference($user, 'gedcomid')); ?>"
						>
						<?php echo print_findindi_link('gedcomid' . $tree->tree_id, '', $tree->tree_name); ?>
					</td>
					<td>
						<select name="canedit<?php echo $tree->tree_id; ?>">
							<?php foreach ($ALL_EDIT_OPTIONS as $EDIT_OPTION => $desc): ?>
							<option value="<?php echo $EDIT_OPTION; ?>"
								<?php if ($EDIT_OPTION === $tree->getUserPreference($user, 'canedit')): ?>
									selected="selected"
								<?php endif; ?>
							>
								<?php echo $desc; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<select name="RELATIONSHIP_PATH_LENGTH<?php echo $tree->tree_id; ?>" id="RELATIONSHIP_PATH_LENGTH<?php echo $tree->tree_id; ?>" class="relpath">
							<?php for ($n = 0; $n <= 10; ++$n): ?>
							<option value="<?php echo $n; ?>" <?php echo $tree->getUserPreference($user, 'RELATIONSHIP_PATH_LENGTH') === $n ? 'checked' : ''; ?>>
								<?php echo $n ? $n : WT_I18N::translate('No'); ?>
							</option>
							<?php endfor; ?>
						</select>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<button class="btn btn-primary" type="submit"><?php echo WT_I18N::translate('save'); ?></button>
	</form>
	<?php

	return;

case 'cleanup':

	$controller
		->setPageTitle(WT_I18N::translate('Delete inactive users'))
		->pageHeader();

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo WT_I18N::translate('Administration'); ?></a></li>
		<li><a href="admin_users.php"><?php echo WT_I18N::translate('User administration'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>
	<h2><?php echo $controller->getPageTitle(); ?></h2>

	<form method="post" action="?action=cleanup2">
	<table class="table table-bordered">
	<?php
	// Check for idle users
	$month = WT_Filter::getInteger('month', 1, 12, 6);
	echo '<tr><th colspan="2">', WT_I18N::translate('Number of months since the last login for a user’s account to be considered inactive: '), '</th>';
	echo '<td><select onchange="document.location=options[selectedIndex].value;">';
	for ($i = 1; $i <= 12; $i++) {
		echo '<option value="admin_users.php?action=cleanup&amp;month=' . $i . '"';
		if ($i == $month) {
			echo ' selected="selected"';
		}
		echo '>', $i, '</option>';
	}
	echo '</select></td></tr>';

	// Check users not logged in too long
	$ucnt = 0;
	foreach (User::all() as $user) {
		if ($user->getPreference('sessiontime') === '0') {
			$datelogin = (int) $user->getPreference('reg_timestamp');
		} else {
			$datelogin = (int) $user->getPreference('sessiontime');
		}
		if (mktime(0, 0, 0, (int) date('m') - $month, (int) date('d'), (int) date('Y')) > $datelogin && $user->getPreference('verified') && $user->getPreference('approved')) {
			$ucnt++;
			?>
			<tr>
				<td>
					<a href="?action=edit&amp;user_id=<?php echo $user->getUserId(); ?>">
						<?php echo WT_Filter::escapeHtml($user->getUserName()); ?>
						—
						<?php echo WT_Filter::escapeHtml($user->getRealName()); ?>
					</a>
				</td>
				<td>
					<?php echo WT_I18N::translate('User’s account has been inactive too long: ') . timestamp_to_gedcom_date($datelogin)->display(); ?>
				</td>
				<td>
					<input type="checkbox" name="del_<?php echo $user->getUserId(); ?>" value="1">
				</td>
			</tr>
		<?php
		}
	}

	// Check unverified users
	foreach (User::all() as $user) {
		if (((date('U') - (int) $user->getPreference('reg_timestamp')) > 604800) && !$user->getPreference('verified')) {
			$ucnt++;
			?>
			<tr>
				<td>
					<a href="?action=edit&amp;user_id=<?php echo $user->getUserId(); ?>">
						<?php echo WT_Filter::escapeHtml($user->getUserName()); ?>
						—
						<?php echo WT_Filter::escapeHtml($user->getRealName()); ?>
					</a>
				</td>
				<td>
					<?php echo WT_I18N::translate('User didn’t verify within 7 days.'); ?>
				</td>
				<td>
					<input type="checkbox" checked name="del_<?php echo $user->getUserId(); ?>" value="1">
				</td>
			</tr>
			<?php
		}
	}

	// Check users not verified by admin
	foreach (User::all() as $user) {
		if ($user->getUserId() !== Auth::id() && !$user->getPreference('approved') && $user->getPreference('verified')) {
			$ucnt++;
			?>
			<tr>
				<td>
					<a href="?action=edit&amp;user_id=<?php echo $user->getUserId(); ?>">
						<?php echo WT_Filter::escapeHtml($user->getUserName()); ?>
						—
						<?php echo WT_Filter::escapeHtml($user->getRealName()); ?>
					</a>
				</td>
				<td>
					<?php echo WT_I18N::translate('User not verified by administrator.'); ?>
				</td>
				<td>
					<input type="checkbox" name="del_<?php echo $user->getUserId(); ?>" value="1">
				</td>
			</tr>
			<?php
		}
	}
		?>
		</table>
		<p>
		<?php if ($ucnt): ?>
			<input type="submit" value="<?php echo WT_I18N::translate('delete'); ?>">
			<?php else: ?>
			<?php echo WT_I18N::translate('Nothing found to cleanup'); ?>
			<?php endif; ?>
		</p>
	</form>
	<?php
	break;

case 'cleanup2':
	foreach (User::all() as $user) {
		if (WT_Filter::post('del_' . $user->getUserId()) == '1') {
			Log::addAuthenticationLog('Deleted user: ' . $user->getUserName());
			WT_FlashMessages::addMessage(WT_I18N::translate('Deleted user: ') . $user->getUserName(), 'success');
			$user->delete();
		}
	}

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);
	break;
default:
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
		->addInlineJavascript('
			jQuery(".table-user-list").dataTable({
				' . WT_I18N::datatablesI18N() . ',
				processing: true,
				serverSide: true,
				ajax: {
					"url": "' . WT_SCRIPT_NAME . '?action=loadrows",
					"type": "POST"
				},
				search: {
					search: "' . WT_Filter::escapeJs(WT_Filter::get('filter')) . '"
				},
				autoWidth: false,
				pageLength: ' . Auth::user()->getPreference('admin_users_page_size', 10) . ',
				sorting: [[2,"asc"]],
				columns: [
					/* details           */ { sortable: false },
					/* user-id           */ { visible: false },
					/* user_name         */ null,
					/* real_name         */ null,
					/* email             */ null,
					/* language          */ null,
					/* registered (sort) */ { visible: false },
					/* registered        */ { dataSort: 7 },
					/* last_login (sort) */ { visible: false },
					/* last_login        */ { dataSort: 9 },
					/* verified          */ { class: "center" },
					/* approved          */ { class: "center" }
				]
			})
			.fnFilter("' . WT_Filter::get('filter') . '"); // View details of a newly created user
		')
		->pageHeader();

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo WT_I18N::translate('Administration'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>
	<h2><?php echo $controller->getPageTitle(); ?></h2>

	<table class="table table-condensed table-bordered table-user-list">
		<thead>
			<tr>
				<th><?php echo WT_I18N::translate('Edit'); ?></th>
				<th><!-- user id --></th>
				<th><?php echo WT_I18N::translate('Username'); ?></th>
				<th><?php echo WT_I18N::translate('Real name'); ?></th>
				<th><?php echo WT_I18N::translate('Email address'); ?></th>
				<th><?php echo WT_I18N::translate('Language'); ?></th>
				<th><!-- date registered --></th>
				<th><?php echo WT_I18N::translate('Date registered'); ?></th>
				<th><!-- last login --></th>
				<th><?php echo WT_I18N::translate('Last logged in'); ?></th>
				<th><?php echo WT_I18N::translate('Verified'); ?></th>
				<th><?php echo WT_I18N::translate('Approved'); ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<?php
	break;
}
