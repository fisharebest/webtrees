<?php use Fisharebest\Webtrees\I18N; ?>

<p>
	<?= I18N::translate('When you add, edit, or delete information, the changes are not saved immediately. Instead, they are kept in a “pending” area. These pending changes need to be reviewed by a moderator before they are accepted.') ?>
</p>

<p>
	<?= I18N::translate('This process allows the site’s owner to ensure that the new information follows the site’s standards and conventions, has proper source attributions, etc.') ?>
</p>

<p>
	<?= I18N::translate('Pending changes are only shown when your account has permission to edit. When you sign out, you will no longer be able to see them. Also, pending changes are only shown on certain pages. For example, they are not shown in lists, reports, or search results.') ?>
</p>

<?php if ($is_admin): ?>
	<p>
		<?= I18N::translate('Each user account has an option to “automatically accept changes”. When this is enabled, any changes made by that user are saved immediately. Many administrators enable this for their own user account.') ?>
	</p>
<?php endif ?>
