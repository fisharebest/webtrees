<?php
// Masquerade as another user
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id: admin_modules.php 14786 2013-02-06 22:28:50Z greg $

const WT_SCRIPT_NAME = 'admin_masquerade.php';

require 'includes/session.php';
require 'includes/functions/functions_edit.php';

$controller=new WT_Controller_Page();
$controller
	->requireAdminLogin()
	->setPageTitle(/* I18N: verb - pretend to be someone else */ WT_I18N::translate('Masquerade'));

$all_users = get_all_users('ASC', 'username');

$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

if (array_key_exists($user_id, $all_users)) {
	$WT_SESSION->wt_user = $user_id;
	Zend_Session::regenerateId();
	Zend_Session::writeClose();
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'index.php');
	exit;	
}

$controller->pageHeader();
?>

<h2><?php echo WT_I18N::translate('Masquerade as another user'); ?></h2>

<form method="post" action="<?php echo WT_SCRIPT_NAME; ?>">
	<?php echo select_edit_control('user_id', $all_users, null, null); ?>
	<input type="submit" value="<?php echo WT_I18N::translate('continue'); ?>">
</form>
