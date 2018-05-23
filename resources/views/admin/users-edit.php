<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Individual; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-users') => I18N::translate('User administration'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form-horizontal" name="newform" method="post" autocomplete="off" action="<?= e(route('admin-users-edit')) ?>">
	<?= csrf_field() ?>
	<input type="hidden" name="user_id" value="<?= $user->getUserId() ?>">

	<!-- REAL NAME -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="real_name">
			<?= I18N::translate('Real name') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="text" id="real_name" name="real_name" required maxlength="64" value="<?= e($user->getRealName()) ?>" dir="auto">
			<p class="small text-muted">
				<?= I18N::translate('This is your real name, as you would like it displayed on screen.') ?>
			</p>
		</div>
	</div>

	<!-- USER NAME -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="username">
			<?= I18N::translate('Username') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="text" id="username" name="username" required maxlength="32" value="<?= e($user->getUserName()) ?>" dir="auto">
			<p class="small text-muted">
				<?= I18N::translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.') ?>
			</p>
		</div>
	</div>

	<!-- PASSWORD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="pass1">
			<?= I18N::translate('Password') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="password" id="pass1" name="pass1" pattern = "<?= WT_REGEX_PASSWORD ?>" placeholder="<?= I18N::plural('Use at least %s character.', 'Use at least %s characters.', WT_MINIMUM_PASSWORD_LENGTH, I18N::number(WT_MINIMUM_PASSWORD_LENGTH)) ?>" <?= $user->getUserId() ? '' : 'required' ?> onchange="form.pass2.pattern = regex_quote(this.value);" autocomplete="new-password">
			<p class="small text-muted">
				<?= I18N::translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.') ?>
			</p>
		</div>
	</div>

	<!-- CONFIRM PASSWORD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="pass2">
			<?= I18N::translate('Confirm password') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="password" id="pass2" name="pass2" pattern = "<?= WT_REGEX_PASSWORD ?>" placeholder="<?= I18N::translate('Type the password again.') ?>" <?= $user->getUserId() ? '' : 'required' ?> autocomplete="new-password">
		</div>
	</div>

	<!-- EMAIL ADDRESS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="email">
			<?= I18N::translate('Email address') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" type="email" id="email" name="email" required maxlength="64" value="<?= e($user->getEmail()) ?>">
			<p class="small text-muted">
				<?= I18N::translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.') ?>
			</p>
		</div>
	</div>

	<!-- EMAIL VERIFIED -->
	<!-- ACCOUNT APPROVED -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="verified">
			<?= I18N::translate('Account approval and email verification') ?>
		</label>
		<div class="col-sm-9">
			<div class="form-check">
				<label>
					<input type="checkbox" name="verified" value="1" <?= $user->getPreference('verified') ? 'checked' : '' ?>>
					<?= I18N::translate('Email verified') ?>
				</label>
				<label>
					<input type="checkbox" name="approved" value="1" <?= $user->getPreference('verified_by_admin') ? 'checked' : '' ?>>
					<?= I18N::translate('Approved by administrator') ?>
				</label>
				<p class="small text-muted">
					<?= I18N::translate('When a user registers for an account, an email is sent to their email address with a verification link. When they follow this link, we know the email address is correct, and the “email verified” option is selected automatically.') ?>
				</p>
				<p class="small text-muted">
					<?= I18N::translate('If an administrator creates a user account, the verification email is not sent, and the email must be verified manually.') ?>
				</p>
				<p class="small text-muted">
					<?= I18N::translate('You should not approve an account unless you know that the email address is correct.') ?>
				</p>
				<p class="small text-muted">
					<?= I18N::translate('A user will not be able to sign in until both “email verified” and “approved by administrator” are selected.') ?>
				</p>
			</div>
		</div>
	</div>

	<!-- LANGUAGE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="language">
			<?= /* I18N: A configuration setting */ I18N::translate('Language') ?>
		</label>
		<div class="col-sm-9">
			<select id="language" name="language" class="form-control">
				<?php foreach ($locales as $locale): ?>
					<option value="<?= $locale->languageTag() ?>" <?= $user->getPreference('language', $default_locale) === $locale->languageTag() ? 'selected' : '' ?>>
						<?= $locale->endonym() ?>
					</option>
				<?php endforeach ?>
			</select>
		</div>
	</div>

	<!-- TIMEZONE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="timezone">
			<?= /* I18N: A configuration setting */ I18N::translate('Time zone') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select(array_combine(\DateTimeZone::listIdentifiers(), \DateTimeZone::listIdentifiers()), $user->getPreference('TIMEZONE', 'UTC'), ['id' => 'timezone', 'name' => 'timezone']) ?>
			<p class="small text-muted">
				<?= I18N::translate('The time zone is required for date calculations, such as knowing today’s date.') ?>
			</p>
		</div>
	</div>

	<!-- AUTO ACCEPT -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="auto_accept">
			<?= I18N::translate('Changes') ?>
		</label>
		<div class="col-sm-9">
			<div class="form-check">
				<label>
					<input type="checkbox" name="auto_accept" value="1" <?= $user->getPreference('auto_accept') ? 'checked' : '' ?>>
					<?= I18N::translate('Automatically accept changes made by this user') ?>
				</label>
				<p class="small text-muted">
					<?= I18N::translate('Normally, any changes made to a family tree need to be reviewed by a moderator. This option allows a user to make changes without needing a moderator.') ?>
				</p>
			</div>
		</div>
	</div>

	<!-- VISIBLE ONLINE -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="visible_online">
			<?= /* I18N: A configuration setting */ I18N::translate('Visible online') ?>
		</label>
		<div class="col-sm-9">
			<div class="form-check">
				<label>
					<input type="checkbox" id="visible_online" name="visible_online" value="1" <?= $user->getPreference('visibleonline') ? 'checked' : '' ?>>
					<?= /* I18N: A configuration setting */ I18N::translate('Visible to other users when online') ?>
				</label>
				<p class="small text-muted">
					<?= I18N::translate('You can choose whether to appear in the list of users who are currently signed-in.') ?>
				</p>
			</div>
		</div>
	</div>

	<!-- CONTACT METHOD -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="contactmethod">
			<?= /* I18N: A configuration setting */ I18N::translate('Preferred contact method') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select($contact_methods, $user->getPreference('contactmethod'), ['id' => 'contact_method', 'name' => 'contact_method']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Preferred contact method” configuration setting */
				I18N::translate('Site members can send each other messages. You can choose to how these messages are sent to you, or choose not receive them at all.') ?>
			</p>
		</div>
	</div>

	<!-- THEME -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="theme">
			<?= I18N::translate('Theme') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::select($theme_options, $user->getPreference('theme'), ['id' => 'theme', 'name' => 'theme']) ?>
		</div>
	</div>

	<!-- COMMENTS -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="comment">
			<?= I18N::translate('Administrator comments on user') ?>
		</label>
		<div class="col-sm-9">
			<textarea class="form-control" id="comment" name="comment" rows="5" maxlength="255"><?= e($user->getPreference('comment')) ?></textarea>
		</div>
	</div>

	<!-- ADMINISTRATOR -->
	<div class="row form-group">
		<label class="col-sm-3 col-form-label" for="admin">
		</label>
		<div class="col-sm-9">
			<div class="form-check">
				<label>
					<input type="checkbox" id="admin" name="canadmin" value="1" <?= $user->getPreference('canadmin') ? 'checked' : '' ?>  <?= $user->getUserId() === Auth::id() ? 'disabled' : '' ?>>
					<?= I18N::translate('Administrator') ?>
				</label>
			</div>
		</div>
	</div>

	<h3><?= I18N::translate('Access to family trees') ?></h3>

	<p>
		<?= I18N::translate('A role is a set of access rights, which give permission to view data, change preferences, etc. Access rights are assigned to roles, and roles are granted to users. Each family tree can assign different access to each role, and users can have a different role in each family tree.') ?>
	</p>

	<div class="row">
		<div class="col-xs-4">
			<h4>
				<?= I18N::translate('Visitor') ?>
			</h4>
			<p class="small text-muted">
				<?= I18N::translate('Everybody has this role, including visitors to the website and search engines.') ?>
			</p>
			<h4>
				<?= I18N::translate('Member') ?>
			</h4>
			<p class="small text-muted">
				<?= I18N::translate('This role has all the permissions of the visitor role, plus any additional access granted by the family tree configuration.') ?>
			</p>
		</div>
		<div class="col-xs-4">
			<h4>
				<?= I18N::translate('Editor') ?>
			</h4>
			<p class="small text-muted">
				<?= I18N::translate('This role has all the permissions of the member role, plus permission to add/change/delete data. Any changes will need to be reviewed by a moderator, unless the user has the “automatically accept changes” option enabled.') ?>
			</p>
			<h4>
				<?= I18N::translate('Moderator') ?>
			</h4>
			<p class="small text-muted">
				<?= I18N::translate('This role has all the permissions of the editor role, plus permission to accept/reject changes made by other users.') ?>
			</p>
		</div>
		<div class="col-xs-4">
			<h4>
				<?= I18N::translate('Manager') ?>
			</h4>
			<p class="small text-muted">
				<?= I18N::translate('This role has all the permissions of the moderator role, plus any additional access granted by the family tree configuration, plus permission to change the settings/configuration of a family tree.') ?>
			</p>
			<h4>
				<?= I18N::translate('Administrator') ?>
			</h4>
			<p class="small text-muted">
				<?= I18N::translate('This role has all the permissions of the manager role in all family trees, plus permission to change the settings/configuration of the website, users, and modules.') ?>
			</p>
		</div>
	</div>

	<table class="table table-bordered table-sm">
		<thead>
			<tr>
				<th>
					<?= I18N::translate('Family tree') ?>
				</th>
				<th>
					<?= I18N::translate('Role') ?>
				</th>
				<th>
					<?= I18N::translate('Individual record') ?>
				</th>
				<th>
					<?= I18N::translate('Restrict to immediate family') ?>
				</th>
			</tr>
			<tr>
				<td>
				</td>
				<td>
				</td>
				<td>
					<p class="small text-muted">
						<?= I18N::translate('Link this user to an individual in the family tree.') ?>
					</p>
				</td>
				<td>
					<p class="small text-muted">
						<?= I18N::translate('Where a user is associated to an individual record in a family tree and has a role of member, editor, or moderator, you can prevent them from accessing the details of distant, living relations. You specify the number of relationship steps that the user is allowed to see.') ?>
						<?= I18N::translate('For example, if you specify a path length of 2, the individual will be able to see their grandson (child, child), their aunt (parent, sibling), their step-daughter (spouse, child), but not their first cousin (parent, sibling, child).') ?>
						<?= I18N::translate('Note: longer path lengths require a lot of calculation, which can make your website run slowly for these users.') ?>
					</p>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($trees as $tree): ?>
				<tr>
					<td>
						<?= e($tree->getTitle()) ?>
					</td>
					<td>
						<select class="form-control" name="canedit<?= $tree->getTreeId() ?>">
							<?php foreach ($roles as $role => $description): ?>
								<option value="<?= $role ?>"
									<?= $role === $tree->getUserPreference($user, 'canedit') ? 'selected' : '' ?>>
									<?= $description ?>
								</option>
							<?php endforeach ?>
						</select>
					</td>
					<td>
						<?= FunctionsEdit::formControlIndividual($tree, Individual::getInstance($tree->getUserPreference($user, 'gedcomid'), $tree), ['id' => 'gedcomid' . $tree->getTreeId(), 'name' => 'gedcomid' . $tree->getTreeId()]) ?>
					</td>
					<td>
						<select class="form-control" name="RELATIONSHIP_PATH_LENGTH<?= $tree->getTreeId() ?>" id="RELATIONSHIP_PATH_LENGTH<?= $tree->getTreeId() ?>" class="relpath">
							<?php for ($n = 0; $n <= 10; ++$n): ?>
								<option value="<?= $n ?>" <?= $tree->getUserPreference($user, 'RELATIONSHIP_PATH_LENGTH') == $n ? 'selected' : '' ?>>
									<?= $n ? $n : I18N::translate('No') ?>
								</option>
							<?php endfor ?>
						</select>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?= I18N::translate('save') ?>
			</button>
		</div>
	</div>
</form>

<?php View::push('javascript') ?>
<script>
  $(".relpath").change(function() {
    var fieldIDx = $(this).attr("id");
    var idNum = fieldIDx.replace("RELATIONSHIP_PATH_LENGTH","");
    var newIDx = "gedcomid"+idNum;
    if ($("#"+newIDx).val() === "" && $("#".fieldIDx).val() !== "0") {
      alert("<?= I18N::translate('You must specify an individual record before you can restrict the user to their immediate family.') ?>");
      $(this).val("0");
    }
  });
  function regex_quote(str) {
    return str.replace(/[\\.?+*()[\](){}|]/g, "\\$&");
  }
</script>
<?php View::endpush() ?>
