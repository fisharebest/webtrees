<?php
// Administrative User Interface.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
//
// Modifications Copyright (c) 2010 Greg Roach
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

define('WT_SCRIPT_NAME', 'admin_users_bulk.php');
require './includes/session.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Send broadcast messages'))
	->pageHeader();

?>

<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo WT_I18N::translate('Administration'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>
<h2><?php echo $controller->getPageTitle(); ?></h2>

<p>
	<a href="#" onclick="return message('all', 'messaging2', '');">
		<?php echo WT_I18N::translate('Send a message to all users'); ?>
	</a>
</p>
<p>
	<a href="#" onclick="return message('never_logged', 'messaging2', '');">
		<?php echo WT_I18N::translate('Send a message to users who have never logged in'); ?>
	</a>
</p>
<p>
	<a href="#" onclick="return message('last_6mo', 'messaging2', '');">
		<?php echo WT_I18N::translate('Send a message to users who have not logged in for 6 months'); ?>
	</a>
</p>
