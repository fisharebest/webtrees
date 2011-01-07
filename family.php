<?php
/**
 * Parses gedcom file and displays information about a family.
 *
 * You must supply a $famid value with the identifier for the family.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'family.php');
require './includes/session.php';

$controller = new WT_Controller_Family();
$controller->init();

print_header($controller->getPageTitle());
// completely prevent display if privacy dictates so
if (!$controller->family) {
	echo "<b>", WT_I18N::translate('Unable to find record with ID'), "</b><br /><br />";
	print_footer();
	exit;
}
else if (!$controller->family->canDisplayDetails()) {
	print_privacy_error();
	print_footer();
	exit;
}

// LB added for Lightbox viewer ==============================================================
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require_once WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}
// LB ======================================================================================

$PEDIGREE_FULL_DETAILS = "1"; // Override GEDCOM configuration
$show_full = "1";

?>
<?php if ($controller->family->isMarkedDeleted()) echo "<span class=\"error\">".WT_I18N::translate('This record has been marked for deletion upon admin approval.')."</span>"; ?>
<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record(shownew) {
		fromfile="";
		if (shownew=="yes") fromfile='&fromfile=1';
		var recwin = window.open("gedrecord.php?pid=<?php echo $controller->getFamilyID(); ?>"+fromfile, "_blank", "top=50, left=50, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");
	}
	function showchanges() {
		window.location = '<?php echo $controller->family->getRawUrl(); ?>&show_changes=yes';
	}
//-->
</script>
<?php
if (empty($SEARCH_SPIDER) && $controller->accept_success) {
	echo "<b>".WT_I18N::translate('Changes successfully accepted into database')."</b><br />";
}
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
		<td align="left" valign="top" style="width: <?php echo $pbwidth+30; ?>px;"><!--//List of children//-->
			<?php print_family_children($controller->getFamilyID()); ?>
		</td>
		<td> <!--//parents pedigree chart and Family Details//-->
			<table align="left" width="100%">
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
					<td align="left" colspan="2">
						<br /><hr />
						<?php print_family_facts($controller->family); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br />
<?php
print_footer();
