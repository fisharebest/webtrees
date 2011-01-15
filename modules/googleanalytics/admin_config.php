<?php
/**
 * Online UI for editing config.php site configuration variables
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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

print_header(WT_I18N::translate('Google Analytics Configuration'));

if (!WT_USER_IS_ADMIN) {
	echo'<div class="warning">', WT_I18N::translate('Page only for Administrators'), '</div>';
	print_footer();
	exit;
}

if ($action=='update' && !isset($security_user)) {
	set_module_setting('googleanalytics', 'GA_KEY',  $_POST['NEW_GA_KEY']);

	AddToLog('Google Analytics config updated', 'config');
}

?>
<form method="post" name="configform" action="module.php?mod=googleanalytics&amp;mod_action=admin_config">
<input type="hidden" name="action" value="update" />
	<table id="ga_config">
		<tr>
			<td width="40%"><?php echo WT_I18N::translate('Google Analytics Key'); ?><?php echo help_link('ga_mod_admin', $this->getName()); ?></td>
			<td width="60%">
				<input type="text" name="NEW_GA_KEY" value="<?php echo get_module_setting('googleanalytics', 'GA_KEY', ''); ?>" size="20" />
			</td>
		</tr>
	</table>
	<input type="submit" value="<?php echo WT_I18N::translate('Save configuration'); ?>" onclick="closeHelp();" />
	&nbsp;&nbsp;
	<input type="reset" value="<?php echo WT_I18N::translate('Reset'); ?>" />
</form>
<?php print_footer();
