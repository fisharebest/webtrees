<?php
// Online UI for editing site configuration variables
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team. All rights reserved.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$action=safe_REQUEST($_REQUEST, 'action');

$controller=new WT_Controller_Base();
$controller
	->requireAdminLogin()
	->setPageTitle(WT_I18N::translate('Google Maps configuration'))
	->pageHeader();

echo '<table id="gm_config"><tr>',
	'<th><a ', (safe_GET('mod_action')=="admin_editconfig" ? 'class="current" ' : ''), 'href="module.php?mod=googlemap&amp;mod_action=admin_editconfig">', WT_I18N::translate('Google Maps configuration'), '</a>', '</th>',
	'<th><a ', (safe_GET('mod_action')=="admin_places" ? 'class="current" ' : ''), 'href="module.php?mod=googlemap&amp;mod_action=admin_places">', WT_I18N::translate('Edit geographic place locations'), '</a>', '</th>',
	'<th><a ', (safe_GET('mod_action')=="admin_placecheck" ? 'class="current" ' : ''), 'href="module.php?mod=googlemap&amp;mod_action=admin_placecheck">', WT_I18N::translate('Place Check'), '</a>', help_link('GOOGLEMAP_PLACECHECK','googlemap'), '</th>',
	'</tr></table>';

if ($action=="update" && !isset($security_user)) {
	set_module_setting('googlemap', 'GM_MAP_TYPE',          $_POST['NEW_GM_MAP_TYPE']);
	set_module_setting('googlemap', 'GM_USE_STREETVIEW',    $_POST['NEW_GM_USE_STREETVIEW']);
	set_module_setting('googlemap', 'GM_MIN_ZOOM',          $_POST['NEW_GM_MIN_ZOOM']);
	set_module_setting('googlemap', 'GM_MAX_ZOOM',          $_POST['NEW_GM_MAX_ZOOM']);
	set_module_setting('googlemap', 'GM_XSIZE',             $_POST['NEW_GM_XSIZE']);
	set_module_setting('googlemap', 'GM_YSIZE',             $_POST['NEW_GM_YSIZE']);
	set_module_setting('googlemap', 'GM_PRECISION_0',       $_POST['NEW_GM_PRECISION_0']);
	set_module_setting('googlemap', 'GM_PRECISION_1',       $_POST['NEW_GM_PRECISION_1']);
	set_module_setting('googlemap', 'GM_PRECISION_2',       $_POST['NEW_GM_PRECISION_2']);
	set_module_setting('googlemap', 'GM_PRECISION_3',       $_POST['NEW_GM_PRECISION_3']);
	set_module_setting('googlemap', 'GM_PRECISION_4',       $_POST['NEW_GM_PRECISION_4']);
	set_module_setting('googlemap', 'GM_PRECISION_5',       $_POST['NEW_GM_PRECISION_5']);
	set_module_setting('googlemap', 'GM_DEFAULT_TOP_VALUE', $_POST['NEW_GM_DEFAULT_TOP_LEVEL']);
	set_module_setting('googlemap', 'GM_COORD',             $_POST['NEW_GM_COORD']);
	set_module_setting('googlemap', 'GM_PLACE_HIERARCHY',   $_POST['NEW_GM_PLACE_HIERARCHY']);
	set_module_setting('googlemap', 'GM_PH_XSIZE',          $_POST['NEW_GM_PH_XSIZE']);
	set_module_setting('googlemap', 'GM_PH_YSIZE',          $_POST['NEW_GM_PH_YSIZE']);
	set_module_setting('googlemap', 'GM_PH_MARKER',         $_POST['NEW_GM_PH_MARKER']);
	set_module_setting('googlemap', 'GM_DISP_SHORT_PLACE',  $_POST['NEW_GM_DISP_SHORT_PLACE']);
	set_module_setting('googlemap', 'GM_PH_WHEEL',          $_POST['NEW_GM_PH_WHEEL']);
	set_module_setting('googlemap', 'GM_PH_CONTROLS',       $_POST['NEW_GM_PH_CONTROLS']);
	set_module_setting('googlemap', 'GM_DISP_COUNT',        $_POST['NEW_GM_DISP_COUNT']);

	for ($i=1; $i<=9; $i++) {
		set_module_setting('googlemap', 'GM_PREFIX_'.$i,        $_POST['NEW_GM_PREFIX_'.$i]);
		set_module_setting('googlemap', 'GM_POSTFIX_'.$i,       $_POST['NEW_GM_POSTFIX_'.$i]);
		set_module_setting('googlemap', 'GM_PRE_POST_MODE_'.$i, $_POST['NEW_GM_PRE_POST_MODE_'.$i]);
	}

	AddToLog('Googlemap config updated', 'config');
	// read the config file again, to set the vars
	require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
}

?>
<script type="text/javascript">
<!--
	function showSelectedLevels() {
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 1) {
			document.getElementById('level1').style.display = 'block';
		} else {
			document.getElementById('level1').style.display = 'none';
		}
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 2) {
			document.getElementById('level2').style.display = 'block';
		} else {
			document.getElementById('level2').style.display = 'none';
		}
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 3) {
			document.getElementById('level3').style.display = 'block';
		} else {
			document.getElementById('level3').style.display = 'none';
		}
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 4) {
			document.getElementById('level4').style.display = 'block';
		} else {
			document.getElementById('level4').style.display = 'none';
		}
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 5) {
			document.getElementById('level5').style.display = 'block';
		} else {
			document.getElementById('level5').style.display = 'none';
		}
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 6) {
			document.getElementById('level6').style.display = 'block';
		} else {
			document.getElementById('level6').style.display = 'none';
		}
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 7) {
			document.getElementById('level7').style.display = 'block';
		} else {
			document.getElementById('level7').style.display = 'none';
		}
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 8) {
			document.getElementById('level8').style.display = 'block';
		} else {
			document.getElementById('level8').style.display = 'none';
		}
		if (document.configform.NEW_GM_LEVEL_COUNT.value >= 9) {
			document.getElementById('level9').style.display = 'block';
		} else {
			document.getElementById('level9').style.display = 'none';
		}
	}

	//-->
</script>


<?php
echo WT_JS_START;
?>
jQuery(document).ready(function() {
jQuery("#tabs").tabs();
});
<?php 
echo WT_JS_END;
echo '<div id="tabs">',
		'<ul>',
			'<li><a href="#gm_basic"><span>', WT_I18N::translate('Basic'), '</span></a></li>',
			'<li><a href="#gm_advanced"><span>', WT_I18N::translate('Advanced'), '</span></a></li>',
		'</ul>';
?>		
<form method="post" name="configform" action="module.php?mod=googlemap&mod_action=admin_editconfig">
<input type="hidden" name="action" value="update">

<div id="gm_basic">
<table class="gm_edit_config">
	<tr>
		<th><?php echo WT_I18N::translate('Default map type'), help_link('GOOGLEMAP_MAP_TYPE','googlemap'); ?></th>
		<td>
			<select name="NEW_GM_MAP_TYPE">
				<option value="ROADMAP" <?php if ($GOOGLEMAP_MAP_TYPE=="ROADMAP") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Map'); ?></option>
				<option value="SATELLITE" <?php if ($GOOGLEMAP_MAP_TYPE=="SATELLITE") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Satellite'); ?></option>
				<option value="HYBRID" <?php if ($GOOGLEMAP_MAP_TYPE=="HYBRID") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Hybrid'); ?></option>
				<option value="TERRAIN" <?php if ($GOOGLEMAP_MAP_TYPE=="TERRAIN") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Terrain'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Enable Google Street View'), help_link('STREETVIEW_ENABLE','googlemap'); ?></th>
		<td><?php echo edit_field_yes_no('NEW_GM_USE_STREETVIEW', get_module_setting('googlemap', 'GM_USE_STREETVIEW')); ?></td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Size of map (in pixels)'), help_link('GOOGLEMAP_MAP_SIZE','googlemap'); ?></th>
		<td>
			<?php echo WT_I18N::translate('Width'); ?>
			<input type="text" name="NEW_GM_XSIZE" value="<?php echo $GOOGLEMAP_XSIZE; ?>" size="10">
			<?php echo WT_I18N::translate('Height'); ?>
			<input type="text" name="NEW_GM_YSIZE" value="<?php echo $GOOGLEMAP_YSIZE; ?>" size="10">
		</td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Use Googlemap for Place Hierarchy'), help_link('GOOGLEMAP_PH','googlemap'); ?></th>
		<td><?php echo edit_field_yes_no('NEW_GM_PLACE_HIERARCHY', get_module_setting('googlemap', 'GM_PLACE_HIERARCHY')); ?></td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Size of Place Hierarchy map (in pixels)'), help_link('GOOGLEMAP_PH_MAP_SIZE','googlemap'); ?></th>
		<td>
			<?php echo WT_I18N::translate('Width'); ?>
			<input type="text" name="NEW_GM_PH_XSIZE" value="<?php echo $GOOGLEMAP_PH_XSIZE; ?>" size="10">
			<?php echo WT_I18N::translate('Height'); ?>
			<input type="text" name="NEW_GM_PH_YSIZE" value="<?php echo $GOOGLEMAP_PH_YSIZE; ?>" size="10">
		</td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Type of place markers in Place Hierarchy'), help_link('GOOGLEMAP_PH_MARKER','googlemap'); ?></th>
		<td>
			<select name="NEW_GM_PH_MARKER">
				<option value="G_DEFAULT_ICON" <?php if ($GOOGLEMAP_PH_MARKER=="G_DEFAULT_ICON") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Standard'); ?></option>
				<option value="G_FLAG" <?php if ($GOOGLEMAP_PH_MARKER=="G_FLAG") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Flag'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Display short placenames'), help_link('GM_DISP_SHORT_PLACE','googlemap'); ?></th>
		<td><?php echo edit_field_yes_no('NEW_GM_DISP_SHORT_PLACE', $GM_DISP_SHORT_PLACE); ?></td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Display indis and families count'), help_link('GM_DISP_COUNT','googlemap'); ?></th>
		<td><?php echo edit_field_yes_no('NEW_GM_DISP_COUNT', $GM_DISP_COUNT); ?></td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Use mouse wheel for zoom'), help_link('GOOGLEMAP_PH_WHEEL','googlemap'); ?></th>
		<td><?php echo edit_field_yes_no('NEW_GM_PH_WHEEL', $GOOGLEMAP_PH_WHEEL); ?></td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Hide map controls'), help_link('GOOGLEMAP_PH_CONTROLS','googlemap'); ?></th>
		<td><?php echo edit_field_yes_no('NEW_GM_PH_CONTROLS', $GOOGLEMAP_PH_CONTROLS); ?></td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Display Map Co-ordinates'), help_link('GOOGLEMAP_COORD','googlemap'); ?></th>
		<td><?php echo edit_field_yes_no('NEW_GM_COORD', $GOOGLEMAP_COORD); ?></td>
	</tr>
	<tr>
		<th><?php echo WT_I18N::translate('Zoom factor of map'), help_link('GOOGLEMAP_MAP_ZOOM','googlemap'); ?></th>
		<td>
			<?php echo WT_I18N::translate('minimum'); ?>: <select name="NEW_GM_MIN_ZOOM">
			<?php for ($j=1; $j < 15; $j++) { ?>
			<option value="<?php echo $j, "\""; if ($GOOGLEMAP_MIN_ZOOM==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
			<?php } ?>
			</select>
			<?php echo WT_I18N::translate('maximum'); ?>: <select name="NEW_GM_MAX_ZOOM">
			<?php for ($j=1; $j < 21; $j++) { ?>
			<option value="<?php echo $j, "\""; if ($GOOGLEMAP_MAX_ZOOM==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
			<?php } ?>
			</select>
		</td>
	</tr>
</table>
</div>
<div id="gm_advanced">
<table class="gm_edit_config">
	<tr>
		<th colspan="2"><?php echo WT_I18N::translate('Precision of the latitude and longitude'), help_link('GOOGLEMAP_PRECISION','googlemap'); ?></th>
		<td>
			<table>
				<tr>
					<td><?php echo WT_I18N::translate('Country'); ?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_0">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_0==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
					</td>
				</tr>
				<tr>
					<td><?php echo WT_I18N::translate('State'); ?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_1">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_1==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
					</td>
				</tr>
				<tr>
					<td><?php echo WT_I18N::translate('City'); ?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_2">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_2==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
					</td>
				</tr>
				<tr><td><?php echo WT_I18N::translate('Neighborhood'); ?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_3">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_3==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
					</td>
				</tr>
				<tr><td><?php echo WT_I18N::translate('House'); ?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_4">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_4==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
					</td>
				</tr>
				<tr><td><?php echo WT_I18N::translate('Max'); ?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_5">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_5==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
					</td>
				</tr>
			</table>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th colspan="2"><?php echo WT_I18N::translate('Default value for top-level'), help_link('GM_DEFAULT_LEVEL_0','googlemap'); ?></th>
		<td><input type="text" name="NEW_GM_DEFAULT_TOP_LEVEL" value="<?php echo $GM_DEFAULT_TOP_VALUE; ?>" size="20"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th class="gm_prefix" colspan="4"><?php echo WT_I18N::translate('Configuration per level');?></th>
	</tr>
	<tr id="gm_level_titles">
		<th>&nbsp;</th>
		<th><?php echo WT_I18N::translate('Prefix'), help_link('GM_NAME_PREFIX','googlemap'); ?></th>
		<th><?php echo WT_I18N::translate('Postfix'), help_link('GM_NAME_POSTFIX','googlemap'); ?></th>
		<th><?php echo WT_I18N::translate('Prefix / Postfix order'), help_link('GM_NAME_PRE_POST','googlemap'); ?></th>
	<?php for ($level=1; $level < 10; $level++) { ?>
	<tr  class="gm_levels">
		<th>
			<?php 
			if ($level==1) {
				echo WT_I18N::translate('Country');
			} else {
				echo WT_I18N::translate('Level'), " ", $level;
			}
			?>
		</th>
		<td><input type="text" name="NEW_GM_PREFIX_<?php echo $level; ?>" value="<?php echo $GM_PREFIX[$level]; ?>"></td>
		<td><input type="text" name="NEW_GM_POSTFIX_<?php echo $level; ?>" value="<?php echo $GM_POSTFIX[$level]; ?>"></td>
		<td>
			<select name="NEW_GM_PRE_POST_MODE_<?php echo $level; ?>" dir="ltr" onchange="showSelectedLevels()">
				<option value="0"<?php if ($GM_PRE_POST_MODE[$level] == 0) echo ' selected="selected"'; ?>><?php echo WT_I18N::translate('No pre/postfix'); ?></option>
				<option value="1"<?php if ($GM_PRE_POST_MODE[$level] == 1) echo ' selected="selected"'; ?>><?php echo WT_I18N::translate('Normal, prefix, postfix, both'); ?></option>
				<option value="2"<?php if ($GM_PRE_POST_MODE[$level] == 2) echo ' selected="selected"'; ?>><?php echo WT_I18N::translate('Normal, postfix, prefix, both'); ?></option>
				<option value="3"<?php if ($GM_PRE_POST_MODE[$level] == 3) echo ' selected="selected"'; ?>><?php echo WT_I18N::translate('Prefix, postfix, both, normal'); ?></option>
				<option value="4"<?php if ($GM_PRE_POST_MODE[$level] == 4) echo ' selected="selected"'; ?>><?php echo WT_I18N::translate('Postfix, prefix, both, normal'); ?></option>
				<option value="5"<?php if ($GM_PRE_POST_MODE[$level] == 5) echo ' selected="selected"'; ?>><?php echo WT_I18N::translate('Prefix, postfix, normal, both'); ?></option>
				<option value="6"<?php if ($GM_PRE_POST_MODE[$level] == 6) echo ' selected="selected"'; ?>><?php echo WT_I18N::translate('Postfix, prefix, normal, both'); ?></option>
			</select>
		</td>
	</tr>
	<?php } ?>
</table>
</div>
</div>
<p>
	<input type="submit" value="<?php echo WT_I18N::translate('Save'); ?>" onclick="closeHelp();">
	&nbsp;&nbsp;
	<input type="reset" value="<?php echo WT_I18N::translate('Reset'); ?>">
</p>
</form>
<?php

if (empty($SEARCH_SPIDER)) {
} else {
	echo WT_I18N::translate('Search Engine Spider Detected'), ": ", $SEARCH_SPIDER;
	echo "</div></body></html>";
}
