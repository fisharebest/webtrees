<?php
// Parses gedcom file and displays information about a family.
//
// You must supply a $famid value with the identifier for the family.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
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

define('WT_SCRIPT_NAME', 'family.php');
require './includes/session.php';

$controller = new WT_Controller_Family();
$controller->init();

if ($controller->family && $controller->family->canDisplayDetails()) {
	print_header($controller->getPageTitle());
	if ($controller->family->isMarkedDeleted()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This family has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="' . $controller->family->getHtmlUrl() . '&amp;action=accept">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="' . $controller->family->getHtmlUrl() . '&amp;action=undo">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This family has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	} elseif (find_updated_record($controller->family->getXref(), WT_GED_ID)!==null) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This family has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="' . $controller->family->getHtmlUrl() . '&amp;action=accept">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="' . $controller->family->getHtmlUrl() . '&amp;action=undo">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This family has been edited.  The changes need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	}
} elseif ($controller->family && $SHOW_PRIVATE_RELATIONSHIPS) {
	print_header($controller->getPageTitle());
	// Continue - to display the children/parents/grandparents.
	// We'll check for showing the details again later
} else {
	print_header(WT_I18N::translate('Family'));
	echo '<p class="ui-state-error">', WT_I18N::translate('This family does not exist or you do not have permission to view it.'), '</p>';
	print_footer();
	exit;
}

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

if (WT_USE_LIGHTBOX) {
	require_once WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}

$PEDIGREE_FULL_DETAILS = "1"; // Override GEDCOM configuration
$show_full = "1";

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->family->getXref(), '", "_blank", "top=0, left=0, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");';
echo '}';
echo 'function showchanges() { window.location="'.$controller->family->getRawUrl().'"; }';
echo WT_JS_END;

?>
<table align="center" width="95%">
	<tr>
		<td>
			<p class="name_head"><?php echo $controller->family->getFullName(); ?></p>
		</td>
	</tr>
</table>
<table align="center" width="95%">
	<tr valign="top">
		<td valign="top" style="width: <?php echo $pbwidth+30; ?>px;"><!--//List of children//-->
			<?php print_family_children($controller->getFamilyID()); ?>
		</td>
		<td> <!--//parents pedigree chart and Family Details//-->
			<table width="100%">
				<tr>
					<td class="subheaders" valign="top"><?php echo WT_I18N::translate('Parents'); ?></td>
					<td class="subheaders" valign="top"><?php echo WT_I18N::translate('Grandparents'); ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<table><tr><td> <!--//parents pedigree chart //-->
						<?php
						echo print_family_parents($controller->getFamilyID());
						if (WT_USER_CAN_EDIT) {
							if ($controller->difffam) {
								$husb=$controller->difffam->getHusband();
							} else {
								$husb=$controller->family->getHusband();
							}
							if (!$husb) {
								echo '<a href="javascript: ', WT_I18N::translate('Add a new father'), '" onclick="return addnewparentfamily(\'\', \'HUSB\', \'', $controller->famid, '\');">', WT_I18N::translate('Add a new father'), help_link('edit_add_parent'), '</a><br />';
							}
							if ($controller->difffam) {
								$wife=$controller->difffam->getWife();
							} else {
								$wife=$controller->family->getWife();
							}
							if (!$wife)  {
								echo '<a href="javascript: ', WT_I18N::translate('Add a new mother'), '" onclick="return addnewparentfamily(\'\', \'WIFE\', \'', $controller->famid, '\');">', WT_I18N::translate('Add a new mother'), help_link('edit_add_parent'), '</a><br />';
							}
						}
						?>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
							if ($controller->family->canDisplayDetails()) {
								print_family_facts($controller->family);
							} else {
								echo '<p class="ui-state-highlight">', WT_I18N::translate('The details of this family are private.'), '</p>';
							}
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br />
<?php
print_footer();
