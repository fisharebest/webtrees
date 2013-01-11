<?php
// Miscellaneous administrative functions
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Partly Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
// $Id$

define('WT_SCRIPT_NAME', 'admin_site_other.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller=new WT_Controller_Base();
$controller->setPageTitle(WT_I18N::translate('Add unlinked records'));
$controller->pageHeader();

?>
<div id="other">
	<p>
		<?php echo WT_I18N::translate('Add unlinked records'); ?>
	</p>
	<p>
		<form method="post" action="#" name="tree">
			<?php echo select_edit_control('ged', WT_Tree::getNameList(), null, WT_GEDCOM, ' onchange="tree.submit();"'); ?>
		</form>
	</p>
	<table id="other">
		<tr>
			<td>
				<a href="#" onclick="addnewchild(''); return false;">
					<?php echo /* I18N: An individual that is not linked to any other record */ WT_I18N::translate('Create a new individual'); ?>
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href="#" onclick="addnewnote(''); return false;">
					<?php echo /* I18N: An note that is not linked to any other record */ WT_I18N::translate('Create a new note'); ?>
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href="#" onclick="addnewsource(''); return false;">
					<?php echo /* I18N: A source that is not linked to any other record */ WT_I18N::translate('Create a new source'); ?>
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href="#" onclick="window.open('addmedia.php?action=showmediaform&amp;linktoid=new', '_blank', edit_window_specs); return false;">
					<?php echo /* I18N: A media object that is not linked to any other record */ WT_I18N::translate('Create a new media object'); ?>
				</a>
			</td>
		</tr>
	</table>
</div>
