<?php
// UI for online updating of the config file.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

use Michelf\MarkdownExtra;
use WT\Auth;

define('WT_SCRIPT_NAME', 'admin_site_readme.php');

require './includes/session.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('README documentation'))
	->pageHeader();

// The readme file contains code-quality badges before the first header
$readme = file_get_contents('README.md');
$readme = preg_replace('/.*(?=# webtrees)/s', '', $readme);

?>
<div class="markdown" dir="ltr" lang="en">
	<?php echo MarkdownExtra::defaultTransform($readme); ?>
</div>
