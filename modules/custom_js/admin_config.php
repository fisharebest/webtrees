<?php
/**
 * Online UI for editing config.php site configuration variables
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 * @package webtrees
 * @subpackage Modules
 * $Id: admin_config.php 10287 2011-01-03 03:13:57Z nigel $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$action = safe_POST("action");

print_header($this->getTitle().' '.WT_I18N::translate('Configuration'));

if (!WT_USER_IS_ADMIN) {
	echo'<div class="warning">', WT_I18N::translate('Page only for Administrators'), '</div>';
	print_footer();
	exit;
}

if ($action=='update' && !isset($security_user)) {
	set_module_setting('custom_js', 'CJS_FOOTER',  $_POST['NEW_CJS_FOOTER']);

	AddToLog($this->getTitle().' config updated', 'config');
}

$CJS_FOOTER=get_module_setting('custom_js', 'CJS_FOOTER');

?>
<form method="post" name="configform" action="<?php echo $this->getConfigLink(); ?>">
<input type="hidden" name="action" value="update" />
	<table id="cjs_config">
		<tr>
			<td><?php echo WT_I18N::translate('Custom Javascript for Footer'); ?><?php echo help_link('cjs_mod_footer', $this->getName()); ?></td>
			<td>
				<textarea rows="10" cols="60" name="NEW_CJS_FOOTER"><?php echo $CJS_FOOTER; ?></textarea>
			</td>
		</tr>
	</table>
	<input type="submit" value="<?php echo WT_I18N::translate('Save configuration'); ?>" onclick="closeHelp();" />
	&nbsp;&nbsp;
	<input type="reset" value="<?php echo WT_I18N::translate('Reset'); ?>" />
</form>
<?php print_footer();
