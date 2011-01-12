<?php
/**
 * Administrative User Interface.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * @package webtrees
 * @subpackage Admin
 * @version $Id: admin_users_bulk.php 10239 2011-01-01 22:32:55Z greg $
*/

define('WT_SCRIPT_NAME', 'admin_users_bulk.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';

// Only admin users can access this page
if (!WT_USER_IS_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

print_header(WT_I18N::translate('Send broadcast messages'));

?>

<div id="users_bulk" class="<?php echo $TEXT_DIRECTION; ?>">
	<p><a href="javascript: <?php echo WT_I18N::translate('Send message to all users'); ?>" onclick="message('all', 'messaging2', '', ''); return false;"><?php echo WT_I18N::translate('Send message to all users'); ?></a></p>
	<p><a href="javascript: <?php echo WT_I18N::translate('Send message to users who have never logged in'); ?>" onclick="message('never_logged', 'messaging2', '', ''); return false;"><?php echo WT_I18N::translate('Send message to users who have never logged in'); ?></a></p>
	<p><a href="javascript: <?php echo WT_I18N::translate('Send message to users who have not logged in for 6 months'); ?>" onclick="message('last_6mo', 'messaging2', '', ''); return false;"><?php echo WT_I18N::translate('Send message to users who have not logged in for 6 months'); ?></a></p>
</div>

<?php
print_footer();
