<?php
/**
 * Module Administration User Interface.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team
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
 * @subpackage Module
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'module_admin.php');

require 'includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

if (!WT_USER_GEDCOM_ADMIN) {
	header("Location: login.php?url=module_admin.php");
	exit;
}

// Modules may have been added or updated to no longer provide a particular component
$installed_modules=WT_Module::getInstalledModules();
foreach ($installed_modules as $module_name=>$module) {
	// New module
	WT_DB::prepare("INSERT IGNORE INTO `##module` (module_name) VALUES (?)")->execute(array($module_name));

	// Removed component
	if (!$module instanceof WT_Module_Block) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='block'"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE module_name=?"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE FROM `##block` WHERE module_name=?"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Chart) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='chart'"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Menu) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='menu'"
		)->execute(array($module_name));
		WT_DB::prepare(
			"UPDATE `##module` SET menu_order=NULL WHERE module_name=?"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Report) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='report'"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Sidebar) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='sidebar'"
		)->execute(array($module_name));
		WT_DB::prepare(
			"UPDATE `##module` SET sidebar_order=NULL WHERE module_name=?"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Tab) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='tab'"
		)->execute(array($module_name));
		WT_DB::prepare(
			"UPDATE `##module` SET tab_order=NULL WHERE module_name=?"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Theme) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='theme'"
		)->execute(array($module_name));
	}
}

// Delete config for modules that no longer exist
$module_names=WT_DB::prepare("SELECT module_name FROM `##module`")->fetchOneColumn();
foreach ($module_names as $module_name) {
	if (!array_key_exists($module_name, $installed_modules)) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=?"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE module_name=?"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE FROM `##block` WHERE module_name=?"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE FROM `##module` WHERE module_name=?"
		)->execute(array($module_name));
	}
}

$action = safe_POST('action');

if ($action=='update_mods') {
	foreach (WT_Module::getInstalledModules() as $module) {
		$module_name=$module->getName();
		$status=safe_POST("status-{$module_name}");
		if ($status!==null) {
			WT_DB::prepare("UPDATE `##module` SET status=? WHERE module_name=?")->execute(array($status ? 'enabled' : 'disabled', $module_name));
		}
		foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
			if ($module instanceof WT_Module_Block) {
				$value = safe_POST("blockaccess-{$module_name}-{$ged_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
				WT_DB::prepare(
					"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'block', ?)"
				)->execute(array($module_name, $ged_id, $value));
			}

			if ($module instanceof WT_Module_Chart) {
				$value = safe_POST("chartaccess-{$module_name}-{$ged_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
				WT_DB::prepare(
					"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'chart', ?)"
				)->execute(array($module_name, $ged_id, $value));
			}

			if ($module instanceof WT_Module_Menu) {
				$value = safe_POST("menuaccess-{$module_name}-{$ged_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
				WT_DB::prepare(
					"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'menu', ?)"
				)->execute(array($module_name, $ged_id, $value));
			}

			if ($module instanceof WT_Module_Sidebar) {
				$value = safe_POST("sidebaraccess-{$module_name}-{$ged_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
				WT_DB::prepare(
					"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'sidebar', ?)"
				)->execute(array($module_name, $ged_id, $value));
			}

			if ($module instanceof WT_Module_Report) {
				$value = safe_POST("reportaccess-{$module_name}-{$ged_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
				WT_DB::prepare(
					"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'report', ?)"
				)->execute(array($module_name, $ged_id, $value));
			}

			if ($module instanceof WT_Module_Tab) {
				$value = safe_POST("tabaccess-{$module_name}-{$ged_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
				WT_DB::prepare(
					"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'tab', ?)"
				)->execute(array($module_name, $ged_id, $value));
			}

			if ($module instanceof WT_Module_Theme) {
				$value = safe_POST("themeaccess-{$module_name}-{$ged_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
				WT_DB::prepare(
				"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'theme', ?)"
				)->execute(array($module_name, $ged_id, $value));
			}
		}

		$value = safe_POST('menuorder-'.$module_name);
		if ($value) {
			WT_DB::prepare(
				"UPDATE `##module` SET menu_order=? WHERE module_name=?"
			)->execute(array($value, $module_name));
		}

		$value = safe_POST('taborder-'.$module_name);
		if ($value) {
			WT_DB::prepare(
				"UPDATE `##module` SET tab_order=? WHERE module_name=?"
			)->execute(array($value, $module_name));
		}

		$value = safe_POST('sidebarorder-'.$module_name);
		if ($value) {
			WT_DB::prepare(
				"UPDATE `##module` SET sidebar_order=? WHERE module_name=?"
			)->execute(array($value, $module_name));
		}
	}
}

print_header(i18n::translate('Module administration'));
?>
<style type="text/css">
<!--
.sortme {
	cursor: move;
}
.sortme img {
	cursor: pointer;
}
//-->
</style>
<script type="text/javascript">
//<![CDATA[

  function reindexMods(id) {
	  jQuery('#'+id+' input').each(
	  	function (index, value) {
	    	value.value = index+1;
	  	});
  }

  jQuery(document).ready(function(){
	//-- tabs
    jQuery("#tabs").tabs();

    //-- sortable menus and tabs tables
    jQuery("#menus_table, #tabs_table, #sidebars_table").sortable({items: '.sortme', forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: 'move', axis: 'y'});

    //-- update the order numbers after drag-n-drop sorting is complete
    jQuery('#menus_table').bind('sortupdate', function(event, ui) {
			var id = jQuery(this).attr('id');
			reindexMods(id);
  	  });

    jQuery('#tabs_table').bind('sortupdate', function(event, ui) {
		var id = jQuery(this).attr('id');
		reindexMods(id);
	  });

    jQuery('#sidebars_table').bind('sortupdate', function(event, ui) {
		var id = jQuery(this).attr('id');
		reindexMods(id);
	  });

    //-- enable the arrows buttons
    jQuery(".uarrow").click(function() {
        var curr = jQuery(this).parent().parent().get(0);
        var prev = jQuery(curr).prev();
        if (prev) jQuery(prev).insertAfter(curr);
        reindexMods('menus_table');
        reindexMods('tabs_table');
        reindexMods('sidebars_table');
    });

    jQuery(".udarrow").click(function() {
        var curr = jQuery(this).parent().parent().get(0);
        var prev = jQuery(curr).parent().children().get(0);
        if (prev) jQuery(curr).insertBefore(prev);
        reindexMods('menus_table');
        reindexMods('tabs_table');
        reindexMods('sidebars_table');
    });

    jQuery(".darrow").click(function() {
        var curr = jQuery(this).parent().parent().get(0);
        var next = jQuery(curr).next();
        if (next) jQuery(next).insertBefore(curr);
        reindexMods('menus_table');
        reindexMods('tabs_table');
        reindexMods('sidebars_table');
    });

    jQuery(".ddarrow").click(function() {
	    var curr = jQuery(this).parent().parent().get(0);
	    var prev = jQuery(curr).parent().children(":last").get(0);
	    if (prev) jQuery(curr).insertAfter(prev);
	    reindexMods('menus_table');
	    reindexMods('tabs_table');
	    reindexMods('sidebars_table');
	});

	// Table sorting and pageing
	jQuery("#installed_table")
		.tablesorter({
			sortList: [[2,0], [3,0]], widgets: ['zebra'],
			headers: { 0: { sorter: false }}
		})
		.tablesorterPager({
			container: jQuery("#pager"),
			positionFixed: false,
			size: 15
		});

});
//]]>
</script>

<div align="center">
	<p><?php echo "<h2>".i18n::translate('Module administration')."</h2>"; ?></p>
	<p><?php echo i18n::translate('Below is the list of all the modules installed in this instance of webtrees.  Modules are installed by placing them in the <i>modules</i> directory.  Here you can set the access level per GEDCOM for each module.  If a module includes tabs for the individual page or menus for the menu bar, you can also set the access level and order of each of them.')?></p>
	<p><input TYPE="button" VALUE="<?php echo i18n::translate('Return to Administration page');?>" onclick="javascript:window.location='admin.php'" /></p>
	<div id="tabs">
		<form method="post" action="module_admin.php">
			<input type="hidden" name="action" value="update_mods" />
			<!-- page tabs -->
				<ul>
					<li><a href="#installed_tab"><span><?php echo i18n::translate('All modules')?></span></a></li>
					<li><a href="#menus_tab"><span><?php echo i18n::translate('Menus')?></span></a></li>
					<li><a href="#tabs_tab"><span><?php echo i18n::translate('Tabs')?></span></a></li>
					<li><a href="#sidebars_tab"><span><?php echo i18n::translate('Sidebar')?></span></a></li>
					<li><a href="#blocks_tab"><span><?php echo i18n::translate('Blocks')?></span></a></li>
					<li><a href="#charts_tab"><span><?php echo i18n::translate('Charts')?></span></a></li>
					<li><a href="#reports_tab"><span><?php echo i18n::translate('Reports')?></span></a></li>
					<li><a href="#themes_tab"><span><?php echo i18n::translate('Themes')?></span></a></li>
				</ul>
			<!-- installed -->
			<div id="installed_tab">
				<table id="installed_table" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
					<thead>
					  <tr>
					  <th><?php echo i18n::translate('Enabled'); ?></th>
					  <th><?php echo i18n::translate('Configuration'); ?></th>
					  <th><?php echo i18n::translate('Module Name'); ?></th>
					  <th><?php echo i18n::translate('Description'); ?></th>
					  <th><?php echo i18n::translate('Menu'); ?></th>
					  <th><?php echo i18n::translate('Tab'); ?></th>
					  <th><?php echo i18n::translate('Sidebar'); ?></th>
					  <th><?php echo i18n::translate('Block'); ?></th>
					  <th><?php echo i18n::translate('Chart'); ?></th>
					  <th><?php echo i18n::translate('Report'); ?></th>
					  <th><?php echo i18n::translate('Theme'); ?></th>
					  </tr>
					</thead>
					<tbody>
						<?php
						foreach (WT_Module::getInstalledModules() as $module) {
							$status=WT_DB::prepare(
								"SELECT status FROM `##module` WHERE module_name=?"
							)->execute(array($module->getName()))->fetchOne();
							echo '<tr><td>', two_state_checkbox('status-'.$module->getName(), $status=='enabled'), '</td><td>';
							if ($module instanceof WT_Module_Config) echo '<a href="', $module->getConfigLink(), '"><img class="adminicon" src="', $WT_IMAGES["admin"], '" border="0" alt="', $module->getName(), '" /></a>'; ?></td>
							<td><?php echo $module->getTitle()?></td>
							<td><?php echo $module->getDescription()?></td>
							<td><?php if ($module instanceof WT_Module_Menu) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
							<td><?php if ($module instanceof WT_Module_Tab) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
							<td><?php if ($module instanceof WT_Module_Sidebar) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
							<td><?php if ($module instanceof WT_Module_Block) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
							<td><?php if ($module instanceof WT_Module_Chart) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
							<td><?php if ($module instanceof WT_Module_Report) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
							<td><?php if ($module instanceof WT_Module_Theme) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>
				<div id="pager" class="pager">
					<!--<form>-->
						<img src="<?php echo WT_THEME_DIR; ?>images/jquery/first.png" class="first"/>
						<img src="<?php echo WT_THEME_DIR; ?>images/jquery/prev.png" class="prev"/>
						<input type="text" class="pagedisplay"/>
						<img src="<?php echo WT_THEME_DIR; ?>images/jquery/next.png" class="next"/>
						<img src="<?php echo WT_THEME_DIR; ?>images/jquery/last.png" class="last"/>
						<select class="pagesize">
							<option value="10">10</option>
							<option selected="selected"  value="15">15</option>
							<option value="30">30</option>
							<option value="40">40</option>
							<option  value="50">50</option>
							<option  value="100">100</option>
						</select>
					<!--</form>-->
				</div>
			</div>
			<!-- menus -->
			<div id="menus_tab">
				<table id="menus_table" class="list_table">
					<thead>
					  <tr>
					  <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
					  <th class="list_label"><?php echo i18n::translate('Order')?></th>
					  <th class="list_label"><?php echo i18n::translate('Access level')?></th>
					  </tr>
					</thead>
					<tbody>
						<?php
						$order = 1;
						foreach(WT_Module::getInstalledMenus() as $module) {?>
						<tr class="sortme">
							<td class="list_value"><?php echo $module->getTitle()?></td>
							<td class="list_value"><input type="text" size="5" value="<?php echo $order; ?>" name="menuorder-<?php echo $module->getName() ?>" />
								<img class="uarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["uarrow"];?>" border="0" title="<?php echo i18n::translate('Move up')?>" />
								<img class="udarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["udarrow"];?>" border="0" title="<?php echo i18n::translate('Move to top')?>" />
								<img class="darrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["darrow"];?>" border="0" title="<?php echo i18n::translate('Move down')?>" />
								<img class="ddarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["ddarrow"];?>" border="0" title="<?php echo i18n::translate('Move to bottom')?>" />
							</td>
							<td class="list_value_wrap">
								 <table>
									<?php
										foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
											$varname = 'menuaccess-'.$module->getName().'-'.$ged_id;
											$access_level=WT_DB::prepare(
												"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='menu'"
											)->execute(array($ged_id, $module->getName()))->fetchOne();
											if ($access_level===null) {
												$access_level=$module->defaultAccessLevel();
											}
											echo '<tr><td>', htmlspecialchars($ged_name), '</td><td>';
											echo edit_field_access_level($varname, $access_level);
										}
									?>
								</table>
							</td>
						</tr>
						<?php
						$order++;
						}
						?>
					</tbody>
				</table>
			</div>
			<!-- tabs -->
			<div id="tabs_tab">
				<table id="tabs_table" class="list_table">
					<thead>
					  <tr>
					  <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
					  <th class="list_label"><?php echo i18n::translate('Order')?></th>
					  <th class="list_label"><?php echo i18n::translate('Access level')?></th>
					  </tr>
					</thead>
					<tbody>
						<?php
						$order = 1;
						foreach(WT_Module::getInstalledTabs() as $module) {?>
						<tr class="sortme">
							<td class="list_value"><?php echo $module->getTitle()?></td>
							<td class="list_value"><input type="text" size="5" value="<?php echo $order; ?>" name="taborder-<?php echo $module->getName() ?>" />
								<img class="uarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["uarrow"];?>" border="0" title="<?php echo i18n::translate('Move up')?>" />
								<img class="udarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["udarrow"];?>" border="0" title="<?php echo i18n::translate('Move to top')?>" />
								<img class="darrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["darrow"];?>" border="0" title="<?php echo i18n::translate('Move down')?>" />
								<img class="ddarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["ddarrow"];?>" border="0" title="<?php echo i18n::translate('Move to bottom')?>" />
							</td>
							<td class="list_value_wrap">
							<table>
								<?php
								foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
									$varname = 'tabaccess-'.$module->getName().'-'.$ged_id;
									$access_level=WT_DB::prepare(
										"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='tab'"
									)->execute(array($ged_id, $module->getName()))->fetchOne();
									if ($access_level===null) {
										$access_level=$module->defaultAccessLevel();
									}
									echo '<tr><td>', htmlspecialchars($ged_name), '</td><td>';
									echo edit_field_access_level($varname, $access_level);
								}
								?>
							</table>
							</td>
						</tr>
						<?php
						$order++;
						}
						?>
					</tbody>
				</table>
			</div>
			<!-- sidebars -->
			<div id="sidebars_tab">
				<table id="sidebars_table" class="list_table">
					<thead>
					  <tr>
					  <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
					  <th class="list_label"><?php echo i18n::translate('Order')?></th>
					  <th class="list_label"><?php echo i18n::translate('Access level')?></th>
					  </tr>
					</thead>
					<tbody>
						<?php
						$order = 1;
						foreach(WT_Module::getInstalledSidebars() as $module) {?>
							<tr class="sortme">
								<td class="list_value"><?php echo $module->getTitle()?></td>
								<td class="list_value"><input type="text" size="5" value="<?php echo $order; ?>" name="sidebarorder-<?php echo $module->getName() ?>" />
									<img class="uarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["uarrow"];?>" border="0" title="<?php echo i18n::translate('Move up')?>" />
								<img class="udarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["udarrow"];?>" border="0" title="<?php echo i18n::translate('Move to top')?>" />
								<img class="darrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["darrow"];?>" border="0" title="<?php echo i18n::translate('Move down')?>" />
								<img class="ddarrow" style="vertical-align:bottom;" src="<?php echo $WT_IMAGES["ddarrow"];?>" border="0" title="<?php echo i18n::translate('Move to bottom')?>" />
								</td>
								<td class="list_value_wrap">
									<table>
										<?php
										foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
											$varname = 'sidebaraccess-'.$module->getName().'-'.$ged_id;
											$access_level=WT_DB::prepare(
												"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='sidebar'"
											)->execute(array($ged_id, $module->getName()))->fetchOne();
											if ($access_level===null) {
												$access_level=$module->defaultAccessLevel();
											}
											echo '<tr><td>', htmlspecialchars($ged_name), '</td><td>';
											echo edit_field_access_level($varname, $access_level);
										}
										?>
									</table>
								</td>
							</tr>
						<?php
						$order++;
						}
						?>
					</tbody>
				</table>
			</div>
			<!-- blocks -->
			<div id="blocks_tab">
				<table id="blocks_table" class="list_table">
					<thead>
					  <tr>
					  <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
					  <th class="list_label"><?php echo i18n::translate('Access level')?></th>
					  </tr>
					</thead>
					<tbody>
						<?php
						$order = 1;
						foreach(WT_Module::getInstalledBlocks() as $module) {?>
						<tr class="sortme">
							<td class="list_value"><?php echo $module->getTitle()?></td>
							<td class="list_value_wrap">
								<table>
									<?php
									foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
										$varname = 'blockaccess-'.$module->getName().'-'.$ged_id;
										$access_level=WT_DB::prepare(
											"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='block'"
										)->execute(array($ged_id, $module->getName()))->fetchOne();
										if ($access_level===null) {
											$access_level=$module->defaultAccessLevel();
										}
										echo '<tr><td>', htmlspecialchars($ged_name), '</td><td>';
										echo edit_field_access_level($varname, $access_level);
									}
								?>
								</table>
							</td>
						</tr>
						<?php
						$order++;
						}
						?>
					</tbody>
				</table>
			</div>
			<!-- charts -->
			<div id="charts_tab">
				<table id="charts_table" class="list_table">
					<thead>
					  <tr>
					  <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
					  <th class="list_label"><?php echo i18n::translate('Access level')?></th>
					  </tr>
					</thead>
					<tbody>
						<?php
						$order = 1;
						foreach(WT_Module::getInstalledCharts() as $module) {?>
							<tr class="sortme">
								<td class="list_value"><?php echo $module->getTitle()?></td>
								<td class="list_value_wrap">
									<table>
										<?php
										foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
											$varname = 'chartaccess-'.$module->getName().'-'.$ged_id;
											$access_level=WT_DB::prepare(
												"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='chart'"
											)->execute(array($ged_id, $module->getName()))->fetchOne();
											if ($access_level===null) {
												$access_level=$module->defaultAccessLevel();
											}
											echo '<tr><td>', htmlspecialchars($ged_name), '</td><td>';
											echo edit_field_access_level($varname, $access_level);
										}
										?>
									</table>
								</td>
							</tr>
							<?php
						$order++;
						}
						?>
					</tbody>
				</table>
			</div>
			<!-- reports -->
			<div id="reports_tab">
				<table id="reports_table" class="list_table">
					<thead>
					  <tr>
					  <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
					  <th class="list_label"><?php echo i18n::translate('Access level')?></th>
					  </tr>
					</thead>
					<tbody>
						<?php
						$order = 1;
						foreach(WT_Module::getInstalledReports() as $module) {?>
							<tr class="sortme">
								<td class="list_value"><?php echo $module->getTitle()?></td>
								<td class="list_value_wrap">
									<table>
										<?php
										foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
											$varname = 'reportaccess-'.$module->getName().'-'.$ged_id;
											$access_level=WT_DB::prepare(
												"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='report'"
											)->execute(array($ged_id, $module->getName()))->fetchOne();
											if ($access_level===null) {
												$access_level=$module->defaultAccessLevel();
											}
											echo '<tr><td>', htmlspecialchars($ged_name), '</td><td>';
											echo edit_field_access_level($varname, $access_level);
										}
										?>
									</table>
								</td>
							</tr>
							<?php
							$order++;
							}
							?>
					</tbody>
				</table>
			</div>
			<!-- themes -->
			<div id="themes_tab">
				<table id="themes_table" class="list_table">
					<thead>
					  <tr>
					  <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
					  <th class="list_label"><?php echo i18n::translate('Access level')?></th>
					  </tr>
					</thead>
					<tbody>
						<?php
						$order = 1;
						foreach(WT_Module::getInstalledThemes() as $module) {?>
						<tr class="sortme">
							<td class="list_value"><?php echo $module->getTitle()?></td>
							<td class="list_value_wrap">
								<table>
									<?php
									foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
										$varname = 'themeaccess-'.$module->getName().'-'.$ged_id;
										$access_level=WT_DB::prepare(
											"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='theme'"
										)->execute(array($ged_id, $module->getName()))->fetchOne();
										if ($access_level===null) {
											$access_level=$module->defaultAccessLevel();
										}
										echo '<tr><td>', htmlspecialchars($ged_name), '</td><td>';
										echo edit_field_access_level($varname, $access_level);
									}
									?>
								</table>
							</td>
						</tr>
						<?php
						$order++;
						}
						?>
					</tbody>
				</table>
			</div>
			<input type="submit" value="<?php echo i18n::translate('Save')?>" />
		</form>
	</div>
</div>
<?php
print_footer();
?>
