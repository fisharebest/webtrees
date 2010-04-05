<?php
/**
 * Script to test the Tree Navigation modules.
 * To embed the tree for use in mashups or on blogs use code such as this:
 * <script type="text/javascript" src="http://yourserver/phpgedview/treenav.php?navAjax=embed&rootid=I14&width=400&height=300"></script>
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008  PGV Development Team.  All rights reserved.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 29 August 2005
 *
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'treenav.php');
require './includes/session.php';
require_once WT_ROOT.'includes/classes/class_treenav.php';

$zoom = 0;
$rootid = '';
$name = 'nav';
if (isset($_REQUEST['zoom'])) $zoom = $_REQUEST['zoom'];
if (isset($_REQUEST['rootid'])) $rootid = $_REQUEST['rootid'];
if (!empty($_REQUEST['jsname'])) $name = $_REQUEST['jsname'];
$nav = new TreeNav($rootid, $name, $zoom);
$nav->generations=6;
$nav->zoomLevel-=1;
print_header(i18n::translate('Interactive Tree'));
$nav->drawViewport('', "", "600px");
print_footer();
?>
