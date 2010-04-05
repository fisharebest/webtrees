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
require_once WT_ROOT.'includes/controllers/family_ctrl.php';

$controller = new FamilyController();
$controller->init();

print_header($controller->getPageTitle());
// completely prevent display if privacy dictates so
if (!$controller->family){
	echo "<b>", i18n::translate('Unable to find record with ID'), "</b><br /><br />";
	print_footer();
	exit;
}
else if (!$controller->family->canDisplayDetails()) {
	print_privacy_error($CONTACT_EMAIL);
	print_footer();
	exit;
}

// LB added for Lightbox viewer ==============================================================
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require_once WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}
// LB ======================================================================================

$PEDIGREE_FULL_DETAILS = "1";		// Override GEDCOM configuration
$show_full = "1";

?>
<?php if ($controller->family->isMarkedDeleted()) echo "<span class=\"error\">".i18n::translate('This record has been marked for deletion upon admin approval.')."</span>"; ?>
<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record(shownew) {
		fromfile="";
		if (shownew=="yes") fromfile='&fromfile=1';
		var recwin = window.open("gedrecord.php?pid=<?php echo $controller->getFamilyID(); ?>"+fromfile, "_blank", "top=50, left=50, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");
	}
	function showchanges() {
		window.location = 'family.php?famid=<?php echo $controller->famid; ?>&show_changes=yes';
	}
//-->
</script>
<?php
if (empty($SEARCH_SPIDER) && !$controller->isPrintPreview() && $controller->accept_success) {
	print "<b>".i18n::translate('Changes successfully accepted into database')."</b><br />";
}
?>
<table align="center" width="95%">
	<tr>
		<td>
		<?php print_family_header($controller->famid); ?>
		</td>
	</tr>
</table>
<table align="center" width="95%">
	<tr valign="top">
		<td align="left" valign="top" style="width: <?php echo $pbwidth+30 ?>px;"><!--//List of children//-->
			<?php print_family_children($controller->getFamilyID());?>
		</td>
		<td> <!--//parents pedigree chart and Family Details//-->
			<table align="left" width="100%">
				<tr>
					<td class="subheaders" valign="top"><?php echo i18n::translate('Parents');?></td>
					<td class="subheaders" valign="top"><?php echo i18n::translate('Grandparents');?></td>
				</tr>
				<tr>
					<td colspan="2">
						<table><tr><td> <!--//parents pedigree chart //-->
						<?php
						echo print_family_parents($controller->getFamilyID());
						if (!$controller->isPrintPreview() && $controller->display && WT_USER_CAN_EDIT) {
							$husb = $controller->getHusband();
							if (empty($husb)) { ?>
			<a href="javascript <?php echo i18n::translate('Add a new father'); ?>" onclick="return addnewparentfamily('', 'HUSB', '<?php echo $controller->famid; ?>');"><?php echo i18n::translate('Add a new father'), help_link('edit_add_parent'); ?></a><br />
						<?php }
							$wife = $controller->getWife();
							if (empty($wife))  { ?>
			<a href="javascript <?php echo i18n::translate('Add a new mother'); ?>" onclick="return addnewparentfamily('', 'WIFE', '<?php echo $controller->famid; ?>');"><?php echo i18n::translate('Add a new mother'), help_link('edit_add_parent'); ?></a><br />
						<?php }
						}
						?>
						</td></tr></table>
					</td>
				</tr>
				<tr>
					<td align="left" colspan="2">
						<br /><hr />
						<?php print_family_facts($controller->family);?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br />
<?php
if(empty($SEARCH_SPIDER))
	print_footer();
else {
	if($SHOW_SPIDER_TAGLINE)
		echo i18n::translate('Search Engine Spider Detected').": ".$SEARCH_SPIDER;
	echo "\n</div>\n\t</body>\n</html>";
}
