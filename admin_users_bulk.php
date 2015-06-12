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

define('WT_SCRIPT_NAME', 'admin_users_bulk.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(I18N::translate('Send broadcast messages'))
	->pageHeader();

?>

<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_users.php"><?php echo I18N::translate('User administration'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<p>
	<a href="#" onclick="return message('all', 'messaging2', '');">
		<?php echo I18N::translate('Send a message to all users'); ?>
	</a>
</p>
<p>
	<a href="#" onclick="return message('never_logged', 'messaging2', '');">
		<?php echo I18N::translate('Send a message to users who have never logged in'); ?>
	</a>
</p>
<p>
	<a href="#" onclick="return message('last_6mo', 'messaging2', '');">
		<?php echo I18N::translate('Send a message to users who have not logged in for 6 months'); ?>
	</a>
</p>
