<?php
/**
 * UI for online updating of the config file.
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
 * @version $Id: admin_site_readme.php 10543 2011-01-21 10:06:41Z greg $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
$action = WT_Filter::post("action");
$controller=new WT_Controller_Page();
$controller
	->requireAdminLogin()
	->setPageTitle(WT_I18N::translate('MySQLDumper'))
	->pageHeader();
echo '<div id="mysqldumper">';
echo '<iframe src="mysqldumper/index.php" width="100%" height="580">'; // Change this src link to the location of your own installation of MySQLDumper
echo '<p>Sorry, your browser does not support iframes.</p>';
echo '</iframe>';
echo '</div>';