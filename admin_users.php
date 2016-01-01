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
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use PDO;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

define('WT_SCRIPT_NAME', 'admin_users.php');
require './includes/session.php';

$controller = new PageController;
$controller->restrictAccess(Auth::isAdmin());

// Valid values for form variables
$ALL_EDIT_OPTIONS = array(
	'none'   => /* I18N: Listbox entry; name of a role */ I18N::translate('Visitor'),
	'access' => /* I18N: Listbox entry; name of a role */ I18N::translate('Member'),
	'edit'   => /* I18N: Listbox entry; name of a role */ I18N::translate('Editor'),
	'accept' => /* I18N: Listbox entry; name of a role */ I18N::translate('Moderator'),
	'admin'  => /* I18N: Listbox entry; name of a role */ I18N::translate('Manager'),
);

// Form actions
switch (Filter::post('action')) {
case 'save':
	if (Filter::checkCsrf()) {
		$user_id        = Filter::postInteger('user_id');
		$user           = User::find($user_id);
		$username       = Filter::post('username');
		$real_name      = Filter::post('real_name');
		$email          = Filter::postEmail('email');
		$pass1          = Filter::post('pass1', WT_REGEX_PASSWORD);
		$pass2          = Filter::post('pass2', WT_REGEX_PASSWORD);
		$theme          = Filter::post('theme', implode('|', array_keys(Theme::installedThemes())), '');
		$language       = Filter::post('language');
		$timezone       = Filter::post('timezone');
		$contact_method = Filter::post('contact_method');
		$comment        = Filter::post('comment');
		$auto_accept    = Filter::postBool('auto_accept');
		$canadmin       = Filter::postBool('canadmin');
		$visible_online = Filter::postBool('visible_online');
		$verified       = Filter::postBool('verified');
		$approved       = Filter::postBool('approved');

		if ($user_id === 0) {
			// Create a new user
			if (User::findByIdentifier($username)) {
				FlashMessages::addMessage(I18N::translate('Duplicate user name.  A user with that user name already exists.  Please choose another user name.'));
			} elseif (User::findByIdentifier($email)) {
				FlashMessages::addMessage(I18N::translate('Duplicate email address.  A user with that email already exists.'));
			} elseif ($pass1 !== $pass2) {
				FlashMessages::addMessage(I18N::translate('Passwords do not match.'));
			} else {
				$user = User::create($username, $real_name, $email, $pass1);
				$user->setPreference('reg_timestamp', date('U'))->setPreference('sessiontime', '0');
				Log::addAuthenticationLog('User ->' . $username . '<- created');
			}
		} else {
			$user = User::find($user_id);
			if ($user && $username && $real_name) {
				$user->setEmail($email);
				$user->setUserName($username);
				$user->setRealName($real_name);
				if ($pass1 !== null && $pass1 === $pass2) {
					$user->setPassword($pass1);
				}
			}
		}

		if ($user) {
			// Approving for the first time?  Send a confirmation email
			if ($approved && !$user->getPreference('verified_by_admin') && $user->getPreference('sessiontime') == 0) {
				I18N::init($user->getPreference('language'));
				Mail::systemMessage(
					$WT_TREE,
					$user,
					I18N::translate('Approval of account at %s', WT_BASE_URL),
					I18N::translate('The administrator at the webtrees site %s has approved your application for an account.  You may now login by accessing the following link: %s', WT_BASE_URL, WT_BASE_URL)
				);
			}

			$user
				->setPreference('theme', $theme)
				->setPreference('language', $language)
				->setPreference('TIMEZONE', $timezone)
				->setPreference('contactmethod', $contact_method)
				->setPreference('comment', $comment)
				->setPreference('auto_accept', $auto_accept ? '1' : '0')
				->setPreference('visibleonline', $visible_online ? '1' : '0')
				->setPreference('verified', $verified ? '1' : '0')
				->setPreference('verified_by_admin', $approved ? '1' : '0');

			// We cannot change our own admin status.  Another admin will need to do it.
			if ($user->getUserId() !== Auth::id()) {
				$user->setPreference('canadmin', $canadmin ? '1' : '0');
			}

			foreach (Tree::getAll() as $tree) {
				$tree->setUserPreference($user, 'gedcomid', Filter::post('gedcomid' . $tree->getTreeId(), WT_REGEX_XREF));
				$tree->setUserPreference($user, 'rootid', Filter::post('rootid' . $tree->getTreeId(), WT_REGEX_XREF));
				$tree->setUserPreference($user, 'canedit', Filter::post('canedit' . $tree->getTreeId(), implode('|', array_keys($ALL_EDIT_OPTIONS))));
				if (Filter::post('gedcomid' . $tree->getTreeId(), WT_REGEX_XREF)) {
					$tree->setUserPreference($user, 'RELATIONSHIP_PATH_LENGTH', Filter::postInteger('RELATIONSHIP_PATH_LENGTH' . $tree->getTreeId(), 0, 10, 0));
				} else {
					// Do not allow a path length to be set if the individual ID is not
					$tree->setUserPreference($user, 'RELATIONSHIP_PATH_LENGTH', null);
				}
			}
		}
	}

	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
}

switch (Filter::get('action')) {
case 'load_json':
	// Generate an AJAX/JSON response for datatables to load a block of rows
	$search = Filter::postArray('search');
	$search = $search['value'];
	$start  = Filter::postInteger('start');
	$length = Filter::postInteger('length');
	$order  = Filter::postArray('order');

	$sql_select =
		"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS '', u.user_id, user_name, real_name, email, us1.setting_value, us2.setting_value, us2.setting_value, us3.setting_value, us3.setting_value, us4.setting_value, us5.setting_value" .
		" FROM `##user` u" .
		" LEFT JOIN `##user_setting` us1 ON (u.user_id=us1.user_id AND us1.setting_name='language')" .
		" LEFT JOIN `##user_setting` us2 ON (u.user_id=us2.user_id AND us2.setting_name='reg_timestamp')" .
		" LEFT JOIN `##user_setting` us3 ON (u.user_id=us3.user_id AND us3.setting_name='sessiontime')" .
		" LEFT JOIN `##user_setting` us4 ON (u.user_id=us4.user_id AND us4.setting_name='verified')" .
		" LEFT JOIN `##user_setting` us5 ON (u.user_id=us5.user_id AND us5.setting_name='verified_by_admin')" .
		" WHERE u.user_id > 0";

	$args = array();

	if ($search) {
		$sql_select .= " AND (user_name LIKE CONCAT('%', :search_1, '%') OR real_name LIKE CONCAT('%', :search_2, '%') OR email LIKE CONCAT('%', :search_3, '%'))";
		$args['search_1'] = $search;
		$args['search_2'] = $search;
		$args['search_3'] = $search;
	}

	if ($order) {
		$sql_select .= " ORDER BY ";
		foreach ($order as $key => $value) {
			if ($key > 0) {
				$sql_select .= ',';
			}
			// Datatables numbers columns 0, 1, 2, ...
			// MySQL numbers columns 1, 2, 3, ...
			switch ($value['dir']) {
			case 'asc':
				$sql_select .= (1 + $value['column']) . " ASC ";
				break;
			case 'desc':
				$sql_select .= (1 + $value['column']) . " DESC ";
				break;
			}
		}
	} else {
		$sql_select = " ORDER BY 1 ASC";
	}

	if ($length) {
		Auth::user()->setPreference('admin_users_page_size', $length);
		$sql_select .= " LIMIT :limit OFFSET :offset";
		$args['limit']  = $length;
		$args['offset'] = $start;
	}

	// This becomes a JSON list, not array, so need to fetch with numeric keys.
	$data = Database::prepare($sql_select)->execute($args)->fetchAll(PDO::FETCH_NUM);

	$installed_languages = array();
	foreach (I18N::installedLocales() as $installed_locale) {
		$installed_languages[$installed_locale->languageTag()] = $installed_locale->endonym();
	}

	// Reformat various columns for display
	foreach ($data as &$datum) {
		$user_id   = $datum[1];
		$user_name = $datum[2];

		if ($user_id != Auth::id()) {
			$admin_options = '<li><a href="#" onclick="return masquerade(' . $user_id . ')"><i class="fa fa-fw fa-user"></i> ' . /* I18N: Pretend to be another user, by logging in as them */
				I18N::translate('Masquerade as this user') . '</a></li>' . '<li><a href="#" onclick="delete_user(\'' . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJs($user_name)) . '\', \'' . Filter::escapeJs($user_id) . '\');"><i class="fa fa-fw fa-trash-o"></i> ' . I18N::translate('Delete') . '</a></li>';
		} else {
			// Do not delete ourself!
			$admin_options = '';
		}

		$datum[0] = '<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-pencil"></i> <span class="caret"></span></button><ul class="dropdown-menu" role="menu"><li><a href="?action=edit&amp;user_id=' . $user_id . '"><i class="fa fa-fw fa-pencil"></i> ' . I18N::translate('Edit') . '</a></li><li class="divider"><li><a href="index_edit.php?user_id=' . $user_id . '"><i class="fa fa-fw fa-th-large"></i> ' . I18N::translate('Change the blocks on this user’s “My page”') . '</a></li>' . $admin_options . '</ul></div>';
		// $datum[1] is the user ID
		// $datum[3] is the real name
		$datum[3] = '<span dir="auto">' . Filter::escapeHtml($datum[3]) . '</span>';
		// $datum[4] is the email address
		if ($user_id != Auth::id()) {
			$datum[4] = '<a href="#" onclick="return message(\'' . Filter::escapeHtml($datum[2]) . '\', \'\', \'\');">' . Filter::escapeHtml($datum[4]) . '</i></a>';
		}
		// $datum[2] is the user name
		$datum[2] = '<span dir="auto">' . Filter::escapeHtml($datum[2]) . '</span>';
		// $datum[5] is the langauge
		if (array_key_exists($datum[5], $installed_languages)) {
			$datum[5] = $installed_languages[$datum[5]];
		}
		// $datum[6] is the sortable registration timestamp
		$datum[7] = $datum[7] ? FunctionsDate::formatTimestamp($datum[7]) : '';
		if (date("U") - $datum[6] > 604800 && !$datum[10]) {
			$datum[7] = '<span class="red">' . $datum[7] . '</span>';
		}
		// $datum[8] is the sortable last-login timestamp
		if ($datum[8]) {
			$datum[9] = FunctionsDate::formatTimestamp($datum[8]) . '<br>' . I18N::timeAgo(WT_TIMESTAMP - $datum[8]);
		} else {
			$datum[9] = I18N::translate('Never');
		}
		$datum[10] = $datum[10] ? I18N::translate('yes') : I18N::translate('no');
		$datum[11] = $datum[11] ? I18N::translate('yes') : I18N::translate('no');
	}

	// Total filtered/unfiltered rows
	$recordsFiltered = (int) Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
	$recordsTotal    = User::count();

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode(array(
		'draw'            => Filter::getInteger('draw'),
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data,
	));

	return;

case 'edit':
	$user_id = Filter::getInteger('user_id');

	if ($user_id === 0) {
		$controller->setPageTitle(I18N::translate('Add a new user'));
		$tmp            = new \stdClass;
		$tmp->user_id   = '';
		$tmp->user_name = '';
		$tmp->real_name = '';
		$tmp->email     = '';
		$user           = new User($tmp);
	} else {
		$controller->setPageTitle(I18N::translate('Edit user'));
		$user = User::find($user_id);
	}

	$controller
		->pageHeader()
		->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
		->addInlineJavascript('autocomplete();')
		->addInlineJavascript('
			jQuery(".relpath").change(function() {
				var fieldIDx = jQuery(this).attr("id");
				var idNum = fieldIDx.replace("RELATIONSHIP_PATH_LENGTH","");
				var newIDx = "gedcomid"+idNum;
				if (jQuery("#"+newIDx).val() === "" && jQuery("#".fieldIDx).val() !== "0") {
					alert("' . I18N::translate('You must specify an individual record before you can restrict the user to their immediate family.') . '");
					jQuery(this).val("0");
				}
			});
			function regex_quote(str) {
				return str.replace(/[\\\\.?+*()[\](){}|]/g, "\\\\$&");
			}
		');

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
		<li><a href="admin_users.php"><?php echo I18N::translate('User administration'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>

	<h1><?php echo $controller->getPageTitle(); ?></h1>

	<form class="form-horizontal" name="newform" method="post" role="form" action="?action=edit" autocomplete="off">
		<?php echo Filter::getCsrf(); ?>
		<input type="hidden" name="action" value="save">
		<input type="hidden" name="user_id" value="<?php echo $user->getUserId(); ?>">

		<!-- REAL NAME -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="real_name">
				<?php echo I18N::translate('Real name'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="real_name" name="real_name" required maxlength="64" value="<?php echo Filter::escapeHtml($user->getRealName()); ?>" dir="auto">
				<p class="small text-muted">
					<?php echo I18N::translate('This is your real name, as you would like it displayed on screen.'); ?>
				</p>
			</div>
		</div>

		<!-- USER NAME -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="username">
				<?php echo I18N::translate('Username'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" id="username" name="username" required maxlength="32" value="<?php echo Filter::escapeHtml($user->getUserName()); ?>" dir="auto">
				<p class="small text-muted">
					<?php echo I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.'); ?>
				</p>
			</div>
		</div>

		<!-- PASSWORD -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="pass1">
				<?php echo I18N::translate('Password'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="password" id="pass1" name="pass1" pattern = "<?php echo WT_REGEX_PASSWORD; ?>" placeholder="<?php echo I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, I18N::number(WT_MINIMUM_PASSWORD_LENGTH)); ?>" <?php echo $user->getUserId() ? '' : 'required'; ?> onchange="form.pass2.pattern = regex_quote(this.value);">
				<p class="small text-muted">
					<?php echo I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.'); ?>
				</p>
			</div>
		</div>

		<!-- CONFIRM PASSWORD -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="pass2">
				<?php echo I18N::translate('Confirm password'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="password" id="pass2" name="pass2" pattern = "<?php echo WT_REGEX_PASSWORD; ?>" placeholder="<?php echo I18N::translate('Type the password again.'); ?>" <?php echo $user->getUserId() ? '' : 'required'; ?>>
			</div>
		</div>

		<!-- EMAIL ADDRESS -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="email">
				<?php echo I18N::translate('Email address'); ?>
			</label>
			<div class="col-sm-9">
				<input class="form-control" type="email" id="email" name="email" required maxlength="64" value="<?php echo Filter::escapeHtml($user->getEmail()); ?>">
				<p class="small text-muted">
					<?php echo I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.'); ?>
				</p>
			</div>
		</div>

		<!-- EMAIL VERIFIED -->
		<!-- ACCOUNT APPROVED -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="verified">
				<?php echo I18N::translate('Account approval and email verification'); ?>
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="verified" value="1" <?php echo $user->getPreference('verified') ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Email verified'); ?>
					</label>
					<label>
						<input type="checkbox" name="approved" value="1" <?php echo $user->getPreference('verified_by_admin') ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Approved by administrator'); ?>
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('When a user registers for an account, an email is sent to their email address with a verification link.  When they click this link, we know the email address is correct, and the “email verified” option is selected automatically.'); ?>
					</p>
					<p class="small text-muted">
						<?php echo I18N::translate('If an administrator creates a user account, the verification email is not sent, and the email must be verified manually.'); ?>
					</p>
					<p class="small text-muted">
						<?php echo I18N::translate('You should not approve an account unless you know that the email address is correct.'); ?>
					</p>
					<p class="small text-muted">
						<?php echo I18N::translate('A user will not be able to login until both the “email verified” and “approved by administrator” options are selected.'); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- LANGUAGE -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="language">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Language'); ?>
			</label>
			<div class="col-sm-9">
				<select id="language" name="language" class="form-control">
					<?php foreach (I18N::installedLocales() as $installed_locale): ?>
						<option value="<?php echo $installed_locale->languageTag(); ?>" <?php echo $user->getPreference('language', WT_LOCALE) === $installed_locale->languageTag() ? 'selected' : ''; ?>>
							<?php echo $installed_locale->endonym(); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<!-- TIMEZONE -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="timezone">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Time zone'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo FunctionsEdit::selectEditControl('timezone', array_combine(\DateTimeZone::listIdentifiers(), \DateTimeZone::listIdentifiers()), null, $user->getPreference('TIMEZONE') ?: 'UTC', 'class="form-control"'); ?>
				<p class="small text-muted">
					<?php echo I18N::translate('The time zone is required for date calculations, such as knowing today’s date.'); ?>
				</p>
			</div>
		</div>

		<!-- AUTO ACCEPT -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="auto_accept">
				<?php echo I18N::translate('Changes'); ?>
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="auto_accept" value="1" <?php echo $user->getPreference('auto_accept') ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Automatically approve changes made by this user'); ?>
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('Normally, any changes made to a family tree need to be approved by a moderator.  This option allows a user to make changes without needing a moderator’s approval.'); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- VISIBLE ONLINE -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="visible_online">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Visible online'); ?>
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" id="visible_online" name="visible_online" value="1" <?php echo $user->getPreference('visibleonline') ? 'checked' : ''; ?>>
						<?php echo /* I18N: A configuration setting */ I18N::translate('Visible to other users when online'); ?>
					</label>
					<p class="small text-muted">
						<?php echo I18N::translate('This checkbox controls your visibility to other users while you’re online.  It also controls your ability to see other online users who are configured to be visible.<br><br>When this box is unchecked, you will be completely invisible to others, and you will also not be able to see other online users.  When this box is checked, exactly the opposite is true.  You will be visible to others, and you will also be able to see others who are configured to be visible.'); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- CONTACT METHOD -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="contactmethod">
				<?php echo /* I18N: A configuration setting */ I18N::translate('Preferred contact method'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo FunctionsEdit::editFieldContact('contact_method', $user->getPreference('contactmethod')); ?>
				<p class="small text-muted">
					<?php echo /* I18N: Help text for the “Preferred contact method” configuration setting */
					I18N::translate('Site members can send each other messages.  You can choose to how these messages are sent to you, or choose not receive them at all.'); ?>
				</p>
			</div>
		</div>

		<!-- THEME -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="theme">
				<?php echo I18N::translate('Theme'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo FunctionsEdit::selectEditControl('theme', Theme::themeNames(), I18N::translate('<default theme>'), $user->getPreference('theme'), 'class="form-control"'); ?>
			</div>
		</div>

		<!-- COMMENTS -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="comment">
				<?php echo I18N::translate('Administrator comments on user'); ?>
			</label>
			<div class="col-sm-9">
				<textarea class="form-control" id="comment" name="comment" rows="5" maxlength="255"><?php echo Filter::escapeHtml($user->getPreference('comment')); ?></textarea>
			</div>
		</div>

		<!-- ADMINISTRATOR -->
		<div class="form-group">
			<label class="control-label col-sm-3" for="admin">
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input
							type="checkbox" id="admin" name="canadmin" value="1"
							<?php echo $user->getPreference('canadmin') ? 'checked' : ''; ?>
							<?php echo $user->getUserId() === Auth::id() ? 'disabled' : ''; ?>
						>
						<?php echo I18N::translate('Administrator'); ?>
					</label>
				</div>
			</div>
		</div>

		<h3><?php echo I18N::translate('Family tree access and settings'); ?></h3>

		<p>
			<?php echo I18N::translate('A role is a set of access rights, which give permission to view data, change configuration settings, etc.  Access rights are assigned to roles, and roles are granted to users.  Each family tree can assign different access to each role, and users can have a different role in each family tree.'); ?>
		</p>

		<div class="row">
			<div class="col-xs-4">
				<h4>
					<?php echo I18N::translate('Visitor'); ?>
				</h4>
				<p class="small text-muted">
					<?php echo I18N::translate('Everybody has this role, including visitors to the website and search engines.'); ?>
				</p>
				<h4>
					<?php echo I18N::translate('Member'); ?>
				</h4>
				<p class="small text-muted">
					<?php echo I18N::translate('This role has all the permissions of the visitor role, plus any additional access granted by the family tree configuration.'); ?>
				</p>
			</div>
			<div class="col-xs-4">
				<h4>
					<?php echo I18N::translate('Editor'); ?>
				</h4>
				<p class="small text-muted">
					<?php echo I18N::translate('This role has all the permissions of the member role, plus permission to add/change/delete data.  Any changes will need to be approved by a moderator, unless the user has the “automatically accept changes” option enabled.'); ?>
				</p>
				<h4>
					<?php echo I18N::translate('Moderator'); ?>
				</h4>
				<p class="small text-muted">
					<?php echo I18N::translate('This role has all the permissions of the editor role, plus permission to approve/reject changes made by other users.'); ?>
				</p>
			</div>
			<div class="col-xs-4">
				<h4>
					<?php echo I18N::translate('Manager'); ?>
				</h4>
				<p class="small text-muted">
					<?php echo I18N::translate('This role has all the permissions of the moderator role, plus any additional access granted by the family tree configuration, plus permission to change the settings/configuration of a family tree.'); ?>
				</p>
				<h4>
					<?php echo I18N::translate('Administrator'); ?>
				</h4>
				<p class="small text-muted">
					<?php echo I18N::translate('This role has all the permissions of the manager role in all family trees, plus permission to change the settings/configuration of the website, users, and modules.'); ?>
				</p>
			</div>
		</div>

		<table class="table table-bordered table-condensed table-responsive">
			<thead>
				<tr>
					<th>
						<?php echo I18N::translate('Family tree'); ?>
					</th>
					<th>
						<?php echo I18N::translate('Role'); ?>
					</th>
					<th>
						<?php echo I18N::translate('Default individual'); ?>
					</th>
					<th>
						<?php echo I18N::translate('Individual record'); ?>
						</th>
					<th>
						<?php echo I18N::translate('Restrict to immediate family'); ?>
					</th>
				</tr>
				<tr>
					<td>
					</td>
					<td>
					</td>
					<td>
						<p class="small text-muted">
							<?php echo I18N::translate('This individual will be selected by default when viewing charts and reports.'); ?>
						</p>
					</td>
					<td>
						<p class="small text-muted">
							<?php echo I18N::translate('Link this user to an individual in the family tree.'); ?>
						</p>
					</td>
					<td>
						<p class="small text-muted">
								<?php echo I18N::translate('Where a user is associated to an individual record in a family tree and has a role of member, editor, or moderator, you can prevent them from accessing the details of distant, living relations.  You specify the number of relationship steps that the user is allowed to see.'); ?>
							<?php echo I18N::translate('For example, if you specify a path length of 2, the individual will be able to see their grandson (child, child), their aunt (parent, sibling), their step-daughter (spouse, child), but not their first cousin (parent, sibling, child).'); ?>
							<?php echo I18N::translate('Note: longer path lengths require a lot of calculation, which can make your website run slowly for these users.'); ?>
						</p>
					</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach (Tree::getAll() as $tree): ?>
				<tr>
					<td>
						<?php echo $tree->getTitleHtml(); ?>
					</td>
					<td>
						<select name="canedit<?php echo $tree->getTreeId(); ?>">
							<?php foreach ($ALL_EDIT_OPTIONS as $EDIT_OPTION => $desc): ?>
								<option value="<?php echo $EDIT_OPTION; ?>"
									<?php echo $EDIT_OPTION === $tree->getUserPreference($user, 'canedit') ? 'selected' : ''; ?>
									>
									<?php echo $desc; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<input
							data-autocomplete-type="INDI"
							data-autocomplete-ged="<?php echo Filter::escapeHtml($tree->getName()); ?>"
							type="text"
							size="12"
							name="rootid<?php echo $tree->getTreeId(); ?>"
							id="rootid<?php echo $tree->getTreeId(); ?>"
							value="<?php echo Filter::escapeHtml($tree->getUserPreference($user, 'rootid')); ?>"
						>
						<?php echo FunctionsPrint::printFindIndividualLink('rootid' . $tree->getTreeId(), '', $tree); ?>
					</td>
					<td>
						<input
							data-autocomplete-type="INDI"
							data-autocomplete-ged="<?php echo Filter::escapeHtml($tree->getName()); ?>"
							type="text"
							size="12"
							name="gedcomid<?php echo $tree->getTreeId(); ?>"
							id="gedcomid<?php echo $tree->getTreeId(); ?>"
							value="<?php echo Filter::escapeHtml($tree->getUserPreference($user, 'gedcomid')); ?>"
						>
						<?php echo FunctionsPrint::printFindIndividualLink('gedcomid' . $tree->getTreeId(), '', $tree); ?>
					</td>
					<td>
						<select name="RELATIONSHIP_PATH_LENGTH<?php echo $tree->getTreeId(); ?>" id="RELATIONSHIP_PATH_LENGTH<?php echo $tree->getTreeId(); ?>" class="relpath">
							<?php for ($n = 0; $n <= 10; ++$n): ?>
							<option value="<?php echo $n; ?>" <?php echo $tree->getUserPreference($user, 'RELATIONSHIP_PATH_LENGTH') == $n ? 'selected' : ''; ?>>
								<?php echo $n ? $n : I18N::translate('No'); ?>
							</option>
							<?php endfor; ?>
						</select>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
				<button type="submit" class="btn btn-primary">
					<?php echo I18N::translate('save'); ?>
				</button>
			</div>
		</div>
	</form>
	<?php

	return;

case 'cleanup':

	$controller
		->setPageTitle(I18N::translate('Delete inactive users'))
		->pageHeader();

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
		<li><a href="admin_users.php"><?php echo I18N::translate('User administration'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>
	<h1><?php echo $controller->getPageTitle(); ?></h1>

	<form method="post" action="?action=cleanup2">
	<table class="table table-bordered">
	<?php
	// Check for idle users
	$month = Filter::getInteger('month', 1, 12, 6);
	echo '<tr><th colspan="2">', I18N::translate('Number of months since the last login for a user’s account to be considered inactive: '), '</th>';
	echo '<td><select onchange="document.location=options[selectedIndex].value;">';
	for ($i = 1; $i <= 12; $i++) {
		echo '<option value="admin_users.php?action=cleanup&amp;month=' . $i . '" ';
		if ($i === $month) {
			echo 'selected';
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
						<?php echo Filter::escapeHtml($user->getUserName()); ?>
						—
						<?php echo $user->getRealNameHtml(); ?>
					</a>
				</td>
				<td>
					<?php echo I18N::translate('User’s account has been inactive too long: ') . FunctionsDate::timestampToGedcomDate($datelogin)->display(); ?>
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
						<?php echo Filter::escapeHtml($user->getUserName()); ?>
						—
						<?php echo $user->getRealNameHtml(); ?>
					</a>
				</td>
				<td>
					<?php echo I18N::translate('User didn’t verify within 7 days.'); ?>
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
						<?php echo Filter::escapeHtml($user->getUserName()); ?>
						—
						<?php echo $user->getRealNameHtml(); ?>
					</a>
				</td>
				<td>
					<?php echo I18N::translate('User not verified by administrator.'); ?>
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
			<input type="submit" value="<?php echo I18N::translate('delete'); ?>">
			<?php else: ?>
			<?php echo I18N::translate('Nothing found to cleanup'); ?>
			<?php endif; ?>
		</p>
	</form>
	<?php
	break;

case 'cleanup2':
	foreach (User::all() as $user) {
		if (Filter::post('del_' . $user->getUserId()) == '1') {
			Log::addAuthenticationLog('Deleted user: ' . $user->getUserName());
			$user->delete();
			I18N::translate('The user %s has been deleted.', Filter::escapeHtml($user->getUserName()));
		}
	}

	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);
	break;
default:
	$controller
		->setPageTitle(I18N::translate('User administration'))
		->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
		->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
		->addInlineJavascript('
			jQuery(".table-user-list").dataTable({
				' . I18N::datatablesI18N() . ',
				stateSave: true,
				stateDuration: 300,
				processing: true,
				serverSide: true,
				ajax: {
					"url": "' . WT_SCRIPT_NAME . '?action=load_json",
					"type": "POST"
				},
				search: {
					search: "' . Filter::escapeJs(Filter::get('filter')) . '"
				},
				autoWidth: false,
				pageLength: ' . Auth::user()->getPreference('admin_users_page_size', 10) . ',
				sorting: [[2, "asc"]],
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
					/* verified          */ null,
					/* approved          */ null
				]
			})
			.fnFilter("' . Filter::get('filter') . '"); // View details of a newly created user
		')
		->pageHeader();

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>
	<h1><?php echo $controller->getPageTitle(); ?></h1>

	<table class="table table-condensed table-bordered table-user-list">
		<thead>
			<tr>
				<th><?php echo I18N::translate('Edit'); ?></th>
				<th><!-- user id --></th>
				<th><?php echo I18N::translate('Username'); ?></th>
				<th><?php echo I18N::translate('Real name'); ?></th>
				<th><?php echo I18N::translate('Email address'); ?></th>
				<th><?php echo I18N::translate('Language'); ?></th>
				<th><!-- date registered --></th>
				<th><?php echo I18N::translate('Date registered'); ?></th>
				<th><!-- last login --></th>
				<th><?php echo I18N::translate('Last logged in'); ?></th>
				<th><?php echo I18N::translate('Verified'); ?></th>
				<th><?php echo I18N::translate('Approved'); ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<?php
	break;
}
