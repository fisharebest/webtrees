<?php
/**
 * Display an hourglass chart
 *
 * Set the root person using the $pid variable
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 23 August 2005
 *
 * @package webtrees
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'hourglass.php');
require './includes/session.php';
require_once WT_ROOT.'includes/controllers/hourglass_ctrl.php';

$controller = new HourglassController();
$controller->init();

// -- print html header information
print_header(PrintReady($controller->name)." ".i18n::translate('Hourglass chart'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

if ($controller->view=="preview") {
	print "<h2 style=\"text-align: center\">".i18n::translate('Hourglass chart').":&nbsp;&nbsp;&nbsp;".PrintReady($controller->name)."</h2>";
} else {
	print "<!-- // NOTE: Start table header -->";
	print "<table><tr><td valign=\"top\">";
	print "<h2>".i18n::translate('Hourglass chart').":<br />".PrintReady($controller->name)."</h2>";
}

$controller->setupJavascript();

// LBox =====================================================================================
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}
// ==========================================================================================

$gencount=0;
if ($view!="preview") {
?>
<!--	// NOTE: Start form and table -->
	</td><td width="50px">&nbsp;</td><td><form method="get" name="people" action="?">
	<input type="hidden" name="show_full" value="<?php print $controller->show_full; ?>" />
	<table><tr>

		<!-- // NOTE: Root ID -->
	<td class="descriptionbox">
	<?php echo i18n::translate('Root Person ID'), help_link('desc_rootid'); ?>
	</td>
	<td class="optionbox">
	<input class="pedigree_form" type="text" name="pid" id="pid" size="3" value="<?php print $controller->pid; ?>"	/>
	<?php print_findindi_link("pid",""); ?>
	</td>

	<!-- // NOTE: Show Details -->
	<td class="descriptionbox">
	<?php echo i18n::translate('Show Details'), help_link('show_full'); ?>
	</td>
	<td class="optionbox">
	<input type="checkbox" value="
	<?php
	if ($controller->show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';";
	else print "0\" onclick=\"document.people.show_full.value='1';";?>"
	/>
	</td>

	<!-- // NOTE: Submit button -->
	<td rowspan="3" class="topbottombar vmiddle">
	<input type="submit" value="<?php print i18n::translate('View'); ?>" />
	</td></tr>

	<!-- // NOTE: Generations -->
	<tr><td class="descriptionbox" >
	<?php echo i18n::translate('Generations'), help_link('desc_generations'); ?>
	</td>
	<td class="optionbox">
	<select name="generations">
	<?php
	for ($i=2; $i<=$MAX_DESCENDANCY_GENERATIONS; $i++) {
		print "<option value=\"".$i."\"" ;
		if ($i == $controller->generations) print " selected=\"selected\"";
		print ">".$i."</option>";
	}
	?>
	</select>
	</td>

	<!-- // NOTE: Show spouses -->
	<td class="descriptionbox">
	<?php echo i18n::translate('Show spouses'), help_link('show_spouse'); ?>
	</td>
	<td class="optionbox">
	<input type="checkbox" value="1" name="show_spouse"
	<?php
	if ($controller->show_spouse) print " checked=\"checked\""; ?> />
	</td></tr>

	<!-- // NOTE: Box width -->
	<tr><td class="descriptionbox">
	<?php echo i18n::translate('Box width'), help_link('box_width'); ?>
	</td>
	<td class="optionbox"><input type="text" size="3" name="box_width" value="<?php print $controller->box_width; ?>" />
	<b>%</b>
	</td>

	<!-- // NOTE: Empty field -->
	<td class="descriptionbox">&nbsp;</td><td class="optionbox">&nbsp;</td></tr>

	<!-- // NOTE: End table and form -->
	</table></form>

	<!-- // NOTE: Close table header -->
	</td></tr></table>
<?php
	if ($show_full==0) {
		echo '<br /><span class="details2">', i18n::translate('Click on any of the boxes to get more information about that person.'), '</span><br />';
	}
} ?>
<div id="hourglass_chart<?php if ($TEXT_DIRECTION=="rtl") print "_rtl"; ?>" <?php if ($controller->isPrintPreview()) print " style=\"top: 1px;\""; else print "style=\"width:98%; direction:".$TEXT_DIRECTION."; z-index:1;\""; ?> >
<table cellspacing="0" cellpadding="0" border="0"><tr>
<!-- // descendancy -->
<td valign="middle">
<?php
$controller->print_descendency($controller->pid, 1); ?>
</td>
<!-- // pedigree -->
<td valign="middle">
<?php
$controller->print_person_pedigree($controller->pid, 1); ?>
</td>
</tr></table>
</div>
<br /><br />
<script type="text/javascript">
<!--
sizeLines();
//-->
</script>
<?php
print_footer();
?>
