<?php
/**
* Online UI for editing site configuration variables
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009  PGV Development Team. All rights reserved.
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
* This Page Is Valid XHTML 1.0 Transitional! > 17 September 2005
*
* @package webtrees
* @subpackage GoogleMap
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.'modules/googlemap/defaultconfig.php';

if (isset($_REQUEST['action'])) {
	$action  = $_REQUEST['action'];
}

function print_level_config_table($level) {
	global $GM_MARKER_COLOR, $GM_MARKER_SIZE, $GM_PREFIX;
	global $GM_POSTFIX, $GM_PRE_POST_MODE, $GM_MAX_NOF_LEVELS, $i;
?>
	<div id="level<?php echo $level;?>" style="display:<?php if ($GM_MAX_NOF_LEVELS >= $level) {echo "block";} else {echo "none";}?>">
		<table class="facts_table">
			<tr>
				<td class="descriptionbox" colspan="2">
					<?php echo i18n::translate('Level'), " ", $level; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo i18n::translate('Prefix'), help_link('GM_NAME_PREFIX','googlemap'); ?>
				</td>
				<td>
					<input type="text" name="NEW_GM_PREFIX_<?php echo $level;?>" value="<?php echo $GM_PREFIX[$level];?>" size="20" tabindex="<?php $i++; echo $i?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo i18n::translate('Postfix'), help_link('GM_NAME_POSTFIX','googlemap'); ?>
				</td>
				<td>
					<input type="text" name="NEW_GM_POSTFIX_<?php echo $level;?>" value="<?php echo $GM_POSTFIX[$level];?>" size="20" tabindex="<?php $i++; echo $i?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo i18n::translate('Prefix / Postfix order'), help_link('GM_NAME_PRE_POST','googlemap'); ?>
				</td>
				<td>
					<select name="NEW_GM_PRE_POST_MODE_<?php echo $level;?>" dir="ltr" tabindex="<?php $i++; echo $i?>" onchange="showSelectedLevels()">
						<option value="0"<?php if ($GM_PRE_POST_MODE[$level] == 0) echo ' selected="selected"';?>><?php echo i18n::translate('No pre/postfix');?></option>
						<option value="1"<?php if ($GM_PRE_POST_MODE[$level] == 1) echo ' selected="selected"';?>><?php echo i18n::translate('Normal, prefix, postfix, both');?></option>
						<option value="2"<?php if ($GM_PRE_POST_MODE[$level] == 2) echo ' selected="selected"';?>><?php echo i18n::translate('Normal, postfix, prefix, both');?></option>
						<option value="3"<?php if ($GM_PRE_POST_MODE[$level] == 3) echo ' selected="selected"';?>><?php echo i18n::translate('Prefix, postfix, both, normal');?></option>
						<option value="4"<?php if ($GM_PRE_POST_MODE[$level] == 4) echo ' selected="selected"';?>><?php echo i18n::translate('Postfix, prefix, both, normal');?></option>
						<option value="5"<?php if ($GM_PRE_POST_MODE[$level] == 5) echo ' selected="selected"';?>><?php echo i18n::translate('Prefix, postfix, normal, both');?></option>
						<option value="6"<?php if ($GM_PRE_POST_MODE[$level] == 6) echo ' selected="selected"';?>><?php echo i18n::translate('Postfix, prefix, normal, both');?></option>
					</select>
				</td>
			</tr>
		</table>
	</div>
<?php
}

print_header(i18n::translate('GoogleMap Configuration'));

echo '<span class="subheaders">', i18n::translate('GoogleMap Configuration'), '</span>';

if (!WT_USER_IS_ADMIN) {
	echo "<table class=\"facts_table\">";
	echo "<tr><td colspan=\"2\" class=\"facts_value\">", i18n::translate('Page only for Administrators');
	echo "</td></tr></table>";
	echo "<br /><br /><br />";
	print_footer();
	exit;
}

if ($action=="update" && !isset($security_user)) {
	set_site_setting('GM_ENABLED',           $_POST['NEW_GM_ENABLE']);
	set_site_setting('GM_API_KEY',           $_POST['NEW_GM_API_KEY']);
	set_site_setting('GM_MAP_TYPE',          $_POST['NEW_GM_MAP_TYPE']);
	set_site_setting('GM_MIN_ZOOM',          $_POST['NEW_GM_MIN_ZOOM']);
	set_site_setting('GM_MAX_ZOOM',          $_POST['NEW_GM_MAX_ZOOM']);
	set_site_setting('GM_XSIZE',             $_POST['NEW_GM_XSIZE']);
	set_site_setting('GM_YSIZE',             $_POST['NEW_GM_YSIZE']);
	set_site_setting('GM_PRECISION_0',       $_POST['NEW_GM_PRECISION_0']);
	set_site_setting('GM_PRECISION_1',       $_POST['NEW_GM_PRECISION_1']);
	set_site_setting('GM_PRECISION_2',       $_POST['NEW_GM_PRECISION_2']);
	set_site_setting('GM_PRECISION_3',       $_POST['NEW_GM_PRECISION_3']);
	set_site_setting('GM_PRECISION_4',       $_POST['NEW_GM_PRECISION_4']);
	set_site_setting('GM_PRECISION_5',       $_POST['NEW_GM_PRECISION_5']);
	set_site_setting('GM_DEFAULT_TOP_VALUE', $_POST['NEW_GM_DEFAULT_TOP_LEVEL']);
	set_site_setting('GM_MAX_NOF_LEVELS',    $_POST['NEW_GM_LEVEL_COUNT']);
	set_site_setting('GM_COORD',             $_POST['NEW_GM_COORD']);
	set_site_setting('GM_PLACE_HIERARCHY',   $_POST['NEW_GM_PLACE_HIERARCHY']);
	set_site_setting('GM_PH_XSIZE',          $_POST['NEW_GM_PH_XSIZE']);
	set_site_setting('GM_PH_YSIZE',          $_POST['NEW_GM_PH_YSIZE']);
	set_site_setting('GM_PH_MARKER',         $_POST['NEW_GM_PH_MARKER']);
	set_site_setting('GM_DISP_SHORT_PLACE',  $_POST['NEW_GM_DISP_SHORT_PLACE']);
	set_site_setting('GM_PH_WHEEL',          $_POST['NEW_GM_PH_WHEEL']);
	set_site_setting('GM_PH_CONTROLS',       $_POST['NEW_GM_PH_CONTROLS']);
	set_site_setting('GM_DISP_COUNT',        $_POST['NEW_GM_DISP_COUNT']);

	for ($i=1; $i<=9; $i++) {
		set_site_setting('GM_PREFIX_'.$i,        $_POST['NEW_GM_PREFIX_'.$i]);
		set_site_setting('GM_POSTFIX_'.$i,       $_POST['NEW_GM_POSTFIX_'.$i]);
		set_site_setting('GM_PRE_POST_MODE_'.$i, $_POST['NEW_GM_PRE_POST_MODE_'.$i]);
	}

	AddToLog('Googlemap config updated', 'config');
	// read the config file again, to set the vars
	require WT_ROOT.'modules/googlemap/defaultconfig.php';
}

$i = 0;

?>
<script language="JavaScript" type="text/javascript">
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

<form method="post" name="configform" action="module.php?mod=googlemap&pgvaction=editconfig">
<input type="hidden" name="action" value="update" />

<table class="facts_table">
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Enable GoogleMap'), help_link('GOOGLEMAP_ENABLE','googlemap'); ?></td>
		<td class="optionbox">
			<select name="NEW_GM_ENABLE" tabindex="<?php $i++; echo $i?>">
				<option value="0" <?php if ($GOOGLEMAP_ENABLED=="0") echo "selected=\"selected\""; ?>><?php echo i18n::translate('No');?></option>
				<option value="1" <?php if ($GOOGLEMAP_ENABLED=="1") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Yes');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('GoogleMap API key'), help_link('GOOGLEMAP_API_KEY','googlemap'); ?></td>
		<td class="optionbox"><input type="text" name="NEW_GM_API_KEY" value="<?php echo $GOOGLEMAP_API_KEY;?>" size="60" tabindex="<?php $i++; echo $i?>" /></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Default map type'), help_link('GOOGLEMAP_MAP_TYPE','googlemap'); ?></td>
		<td class="optionbox">
			<select name="NEW_GM_MAP_TYPE" tabindex="<?php $i++; echo $i?>">
				<option value="G_NORMAL_MAP" <?php if ($GOOGLEMAP_MAP_TYPE=="G_NORMAL_MAP") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Map');?></option>
				<option value="G_SATELLITE_MAP" <?php if ($GOOGLEMAP_MAP_TYPE=="G_SATELLITE_MAP") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Satellite');?></option>
				<option value="G_HYBRID_MAP" <?php if ($GOOGLEMAP_MAP_TYPE=="G_HYBRID_MAP") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Hybrid');?></option>
				<option value="G_PHYSICAL_MAP" <?php if ($GOOGLEMAP_MAP_TYPE=="G_PHYSICAL_MAP") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Terrain');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Size of map (in pixels)'), help_link('GOOGLEMAP_MAP_SIZE','googlemap'); ?></td>
		<td class="optionbox">
			<?php echo i18n::translate('Width'); ?>
			<input type="text" name="NEW_GM_XSIZE" value="<?php echo $GOOGLEMAP_XSIZE;?>" size="10" tabindex="<?php $i++; echo $i?>" />
			<?php echo i18n::translate('Height'); ?>
			<input type="text" name="NEW_GM_YSIZE" value="<?php echo $GOOGLEMAP_YSIZE;?>" size="10" tabindex="<?php $i++; echo $i?>" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Use Googlemap for Place Hierarchy'), help_link('GOOGLEMAP_PH','googlemap'); ?></td>
		<td class="optionbox">
			<select name="NEW_GM_PLACE_HIERARCHY" tabindex="<?php $i++; echo $i?>;">
				<option value="0" <?php if ($GOOGLEMAP_PLACE_HIERARCHY=="0") echo "selected=\"selected\""; ?>><?php echo i18n::translate('No');?></option>
				<option value="1" <?php if ($GOOGLEMAP_PLACE_HIERARCHY=="1") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Yes');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Size of Place Hierarchy map (in pixels)'), help_link('GOOGLEMAP_PH_MAP_SIZE','googlemap'); ?>
		</td>
		<td class="optionbox">
			<?php echo i18n::translate('Width'); ?>
			<input type="text" name="NEW_GM_PH_XSIZE" value="<?php echo $GOOGLEMAP_PH_XSIZE;?>" size="10" tabindex="<?php $i++; echo $i?>" />
			<?php echo i18n::translate('Height'); ?>
			<input type="text" name="NEW_GM_PH_YSIZE" value="<?php echo $GOOGLEMAP_PH_YSIZE;?>" size="10" tabindex="<?php $i++; echo $i?>" />
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Type of place markers in Place Hierarchy'), help_link('GOOGLEMAP_PH_MARKER','googlemap'); ?>
		</td>
		<td class="optionbox">
			<select name="NEW_GM_PH_MARKER" tabindex="<?php $i++; echo $i?>;">
				<option value="G_DEFAULT_ICON" <?php if ($GOOGLEMAP_PH_MARKER=="G_DEFAULT_ICON") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Standard');?></option>
				<option value="G_FLAG" <?php if ($GOOGLEMAP_PH_MARKER=="G_FLAG") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Flag');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Display short placenames'), help_link('GM_DISP_SHORT_PLACE','googlemap'); ?>
		</td>
		<td class="optionbox">
			<select name="NEW_GM_DISP_SHORT_PLACE" tabindex="<?php $i++; echo $i?>;">
				<option value="0" <?php if ($GM_DISP_SHORT_PLACE=="0") echo "selected=\"selected\""; ?>><?php echo i18n::translate('No');?></option>
				<option value="1" <?php if ($GM_DISP_SHORT_PLACE=="1") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Yes');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Display indis and families count'), help_link('GM_DISP_COUNT','googlemap'); ?>
		</td>
		<td class="optionbox">
			<select name="NEW_GM_DISP_COUNT" tabindex="<?php $i++; echo $i?>;">
				<option value="0" <?php if ($GM_DISP_COUNT=="0") echo "selected=\"selected\""; ?>><?php echo i18n::translate('No');?></option>
				<option value="1" <?php if ($GM_DISP_COUNT=="1") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Yes');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Use mouse wheel for zoom'), help_link('GOOGLEMAP_PH_WHEEL','googlemap'); ?>
		</td>
		<td class="optionbox">
			<select name="NEW_GM_PH_WHEEL" tabindex="<?php $i++; echo $i?>;">
				<option value="0" <?php if ($GOOGLEMAP_PH_WHEEL=="0") echo "selected=\"selected\""; ?>><?php echo i18n::translate('No');?></option>
				<option value="1" <?php if ($GOOGLEMAP_PH_WHEEL=="1") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Yes');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Hide map controls'), help_link('GOOGLEMAP_PH_CONTROLS','googlemap'); ?>
		</td>
		<td class="optionbox">
			<select name="NEW_GM_PH_CONTROLS" tabindex="<?php $i++; echo $i?>;">
				<option value="0" <?php if ($GOOGLEMAP_PH_CONTROLS=="0") echo "selected=\"selected\""; ?>><?php echo i18n::translate('No');?></option>
				<option value="1" <?php if ($GOOGLEMAP_PH_CONTROLS=="1") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Yes');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Display Map Co-ordinates'), help_link('GOOGLEMAP_COORD','googlemap'); ?>
		</td>
		<td class="optionbox">
			<select name="NEW_GM_COORD" tabindex="<?php $i++; echo $i?>">
				<option value="0" <?php if ($GOOGLEMAP_COORD=="0") echo "selected=\"selected\""; ?>><?php echo i18n::translate('No');?></option>
				<option value="1" <?php if ($GOOGLEMAP_COORD=="1") echo "selected=\"selected\""; ?>><?php echo i18n::translate('Yes');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Zoom factor of map'), help_link('GOOGLEMAP_MAP_ZOOM','googlemap'); ?>
		</td>
		<td class="optionbox">
			<?php echo i18n::translate('minimum');?>: <select name="NEW_GM_MIN_ZOOM" tabindex="<?php $i++; echo $i?>">
			<?php for ($j=1; $j < 15; $j++) { ?>
			<option value="<?php echo $j, "\""; if ($GOOGLEMAP_MIN_ZOOM==$j) echo " selected=\"selected\""; echo ">", $j;?></option>
			<?php } ?>
			</select>
			<?php echo i18n::translate('maximum');?>: <select name="NEW_GM_MAX_ZOOM" tabindex="<?php $i++; echo $i?>">
			<?php for ($j=1; $j < 15; $j++) { ?>
			<option value="<?php echo $j, "\""; if ($GOOGLEMAP_MAX_ZOOM==$j) echo " selected=\"selected\""; echo ">", $j;?></option>
			<?php } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Precision'), help_link('GOOGLEMAP_PRECISION','googlemap'); ?>
		</td>
		<td class="optionbox">
			<table>
				<tr>
					<td><?php echo i18n::translate('Country');?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_0" tabindex="<?php $i++; echo $i?>">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j;?>"<?php if ($GOOGLEMAP_PRECISION_0==$j) echo " selected=\"selected\""; echo ">", $j;?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo i18n::translate('digits');?>
					</td>
				</tr>
				<tr>
					<td><?php echo i18n::translate('State');?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_1" tabindex="<?php $i++; echo $i?>">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j;?>"<?php if ($GOOGLEMAP_PRECISION_1==$j) echo " selected=\"selected\""; echo ">", $j;?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo i18n::translate('digits');?>
					</td>
				</tr>
				<tr>
					<td><?php echo i18n::translate('City');?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_2" tabindex="<?php $i++; echo $i?>">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j;?>"<?php if ($GOOGLEMAP_PRECISION_2==$j) echo " selected=\"selected\""; echo ">", $j;?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo i18n::translate('digits');?>
					</td>
				</tr>
				<tr><td><?php echo i18n::translate('Neighborhood');?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_3" tabindex="<?php $i++; echo $i?>">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j;?>"<?php if ($GOOGLEMAP_PRECISION_3==$j) echo " selected=\"selected\""; echo ">", $j;?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo i18n::translate('digits');?>
					</td>
				</tr>
				<tr><td><?php echo i18n::translate('House');?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_4" tabindex="<?php $i++; echo $i?>">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j;?>"<?php if ($GOOGLEMAP_PRECISION_4==$j) echo " selected=\"selected\""; echo ">", $j;?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo i18n::translate('digits');?>
					</td>
				</tr>
				<tr><td><?php echo i18n::translate('Max');?>&nbsp;&nbsp;</td>
					<td><select name="NEW_GM_PRECISION_5" tabindex="<?php $i++; echo $i?>">
						<?php for ($j=0; $j < 10; $j++) { ?>
						<option value="<?php echo $j;?>"<?php if ($GOOGLEMAP_PRECISION_5==$j) echo " selected=\"selected\""; echo ">", $j;?></option>
						<?php } ?>
						</select>&nbsp;&nbsp;<?php echo i18n::translate('digits');?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Default top level value'), help_link('GM_DEFAULT_LEVEL_0','googlemap'); ?></td>
		<td class="optionbox"><input type="text" name="NEW_GM_DEFAULT_TOP_LEVEL" value="<?php echo $GM_DEFAULT_TOP_VALUE;?>" size="20" tabindex="<?php $i++; echo $i?>" /></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Number of levels'), help_link('GM_NOF_LEVELS','googlemap'); ?></td>
		<td class="optionbox">
			<select name="NEW_GM_LEVEL_COUNT" dir="ltr" tabindex="<?php $i++; echo $i?>" onchange="showSelectedLevels()">
				<option value="1"<?php if ($GM_MAX_NOF_LEVELS == 1) echo " selected=\"selected\"";?>>1</option>
				<option value="2"<?php if ($GM_MAX_NOF_LEVELS == 2) echo " selected=\"selected\"";?>>2</option>
				<option value="3"<?php if ($GM_MAX_NOF_LEVELS == 3) echo " selected=\"selected\"";?>>3</option>
				<option value="4"<?php if ($GM_MAX_NOF_LEVELS == 4) echo " selected=\"selected\"";?>>4</option>
				<option value="5"<?php if ($GM_MAX_NOF_LEVELS == 5) echo " selected=\"selected\"";?>>5</option>
				<option value="6"<?php if ($GM_MAX_NOF_LEVELS == 6) echo " selected=\"selected\"";?>>6</option>
				<option value="7"<?php if ($GM_MAX_NOF_LEVELS == 7) echo " selected=\"selected\"";?>>7</option>
				<option value="8"<?php if ($GM_MAX_NOF_LEVELS == 8) echo " selected=\"selected\"";?>>8</option>
				<option value="9"<?php if ($GM_MAX_NOF_LEVELS == 9) echo " selected=\"selected\"";?>>9</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox">
			<?php echo i18n::translate('Configuration per level');?>
		</td>
		<td class="optionbox">
			<?php
				print_level_config_table(1, $i);
				print_level_config_table(2, $i);
				print_level_config_table(3, $i);
				print_level_config_table(4, $i);
				print_level_config_table(5, $i);
				print_level_config_table(6, $i);
				print_level_config_table(7, $i);
				print_level_config_table(8, $i);
				print_level_config_table(9, $i);
			?>
		</td>
	</tr>
</table>
<table class="facts_table">
	<tr>
		<td class="descriptionbox" colspan="2" align="center">
			<a href="module.php?mod=googlemap&pgvaction=places"><?php echo i18n::translate('Edit geographic place locations');?></a>
		</td>
	<tr>
		<td class="descriptionbox" colspan="2" align="center">
			<input type="submit" tabindex="<?php $i++; echo $i?>" value="<?php echo i18n::translate('Save configuration');?>" onclick="closeHelp();" />
			&nbsp;&nbsp;
			<input type="reset" tabindex="<?php $i++; echo $i?>" value="<?php echo i18n::translate('Reset');?>" />
		</td>
	</tr>
</table>
</form>
<?php
				
if (empty($SEARCH_SPIDER)) {
	print_footer();
} else {
	echo i18n::translate('Search Engine Spider Detected'), ": ", $SEARCH_SPIDER;
	echo "</div></body></html>";
}

?>
