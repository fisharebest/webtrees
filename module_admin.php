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
 * @version $Id: admin.php 5151 2009-03-04 18:51:04Z canajun2eh $
 */

define('WT_SCRIPT_NAME', 'module_admin.php');
require_once 'includes/session.php';
require_once(WT_ROOT.'includes/classes/class_module.php');


if (!WT_USER_GEDCOM_ADMIN) {
	header("Location: login.php?url=module_admin.php");
	exit;
}

function write_access_option_numeric($checkVar) {
	echo "<option value=\"".WT_PRIV_PUBLIC."\"";
	echo ($checkVar==WT_PRIV_PUBLIC) ? " selected=\"selected\"" : '';
	echo ">".i18n::translate('Show to public')."</option>\n";

	echo "<option value=\"".WT_PRIV_USER."\"";
	echo ($checkVar==WT_PRIV_USER) ? " selected=\"selected\"" : '';
	echo ">".i18n::translate('Show only to authenticated users')."</option>\n";

	echo "<option value=\"".WT_PRIV_NONE."\"";
	echo ($checkVar==WT_PRIV_NONE) ? " selected=\"selected\"" : '';
	echo ">".i18n::translate('Show only to admin users')."</option>\n";

	echo "<option value=\"".WT_PRIV_HIDE."\"";
	echo ($checkVar==WT_PRIV_HIDE) ? " selected=\"selected\"" : '';
	echo ">".i18n::translate('Hide even from admin users')."</option>\n";
}

$action = safe_POST('action');

$modules = WT_Module::getInstalledList();
uasort($modules, "WT_Module::compare_name");

if ($action=='update_mods') {
  foreach($modules as $mod) {
    foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
      $varname = 'accessLevel-'.$mod->getName().'-'.$ged_id;
      $value = safe_POST($varname);
      if ($value!=null) $mod->setAccessLevel($value, $ged_id);

      $varname = 'menuaccess-'.$mod->getName().'-'.$ged_id;
      $value = safe_POST($varname);
      if ($value>$mod->getAccessLevel($ged_id)) $value=$mod->getAccessLevel($ged_id);
      if ($value!=null) $mod->setMenuEnabled($value, $ged_id);

      $varname = 'tabaccess-'.$mod->getName().'-'.$ged_id;
      $value = safe_POST($varname);
      if ($value>$mod->getAccessLevel($ged_id)) $value=$mod->getAccessLevel($ged_id);
      if ($value!=null) $mod->setTabEnabled($value, $ged_id);
      
      $varname = 'sidebaraccess-'.$mod->getName().'-'.$ged_id;
      $value = safe_POST($varname);
      if ($value>$mod->getAccessLevel($ged_id)) $value=$mod->getAccessLevel($ged_id);
      if ($value!=null) $mod->setSidebarEnabled($value, $ged_id);
    }

    $value = safe_POST_integer('taborder-'.$mod->getName(), 0, 100, $mod->getTaborder());
    $mod->setTaborder($value);
    $mod->setMenuorder(safe_POST_integer('menuorder-'.$mod->getName(), 0, 100, $mod->getMenuorder()));
    $mod->setSidebarorder(safe_POST_integer('sideorder-'.$mod->getName(), 0, 100, $mod->getSidebarorder()));
	WT_Module::updateModule($mod);
  }
}

print_header(i18n::translate('Module Administration'));
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
  });
//]]>
  </script>
<div align="center">
<div class="width75">

<p><?php echo "<h2>".i18n::translate('Module Administration')."</h2>"; ?></p>
<p><?php echo i18n::translate('Below is the list of all the modules installed in this instance of webtrees.  Modules are installed by placing them in the <i>modules</i> directory.  Here you can set the access level per GEDCOM for each module.  If a module includes tabs for the individual page or menus for the menu bar, you can also set the access level and order of each of them.')?></p>
<p><input TYPE="button" VALUE="<?php echo i18n::translate('Return to Administration page');?>" onclick="javascript:window.location='admin.php'" /></p>

<form method="post" action="module_admin.php"> 
	<input type="hidden" name="action" value="update_mods" />

<div id="tabs">
<ul>
	<li><a href="#installed_tab"><span><?php echo i18n::translate('Installed Modules')?></span></a></li>
	<li><a href="#menus_tab"><span><?php echo i18n::translate('Manage Menus')?></span></a></li>
	<li><a href="#tabs_tab"><span><?php echo i18n::translate('Manage Tabs')?></span></a></li>
	<li><a href="#sidebars_tab"><span><?php echo i18n::translate('Manage Sidebars')?></span></a></li>
</ul>
<div id="installed_tab">
<!-- installed -->
  <table id="installed_table" class="list_table">
    <thead>
      <tr>
      <th class="list_label"><?php echo i18n::translate('Active')?></th>
      <th class="list_label"><?php echo i18n::translate('Mod Settings')?></th>
      <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
      <th class="list_label"><?php echo i18n::translate('Description')?></th>
      <th class="list_label"><?php echo i18n::translate('Tab')?></th>
      <th class="list_label"><?php echo i18n::translate('Menu')?></th>
      <th class="list_label"><?php echo i18n::translate('Sidebar')?></th>
      <th class="list_label"><?php echo i18n::translate('Access Level')?></th>
      </tr>
    </thead>
    <tbody>
<?php
foreach($modules as $mod) {
	?><tr>
	<td class="list_value"><?php if ($mod->getId()>0) echo i18n::translate('Yes'); else echo i18n::translate('No'); ?></td>
	<td class="list_value"><?php if ($mod instanceof WT_Module_Config) echo '<a href="', $mod->getConfigLink(), '"><img class="adminicon" src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["admin"]["small"], '" border="0" alt="', $mod->getName(), '" /></a>'; ?></td>
	<td class="list_value"><?php echo $mod->getTitle()?></td>
	<td class="list_value_wrap"><?php echo $mod->getDescription()?></td>
	<td class="list_value"><?php if ($mod instanceof WT_Module_Tab) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
	<td class="list_value"><?php if ($mod instanceof WT_Module_Menu) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
	<td class="list_value"><?php if ($mod instanceof WT_Module_Sidebar) echo i18n::translate('Yes'); else echo i18n::translate('No');?></td>
	<td class="list_value_wrap">
	  <table>
	<?php
		foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
			$varname = 'accessLevel-'.$mod->getName().'-'.$ged_id;
			?>
			<tr><td><?php echo $ged_name ?></td><td>
			<select id="<?php echo $varname?>" name="<?php echo $varname?>">
				<?php write_access_option_numeric($mod->getAccessLevel($ged_id)) ?>
			</select></td></tr>
			<?php 
		} 
	?>
	  </table>
	</td>
	</tr>
	<?php 
}
?>
    </tbody>
  </table>
</div>
<div id="menus_tab">
<!-- menus -->
<table id="menus_table" class="list_table">
    <thead>
      <tr>
      <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
      <th class="list_label"><?php echo i18n::translate('Description')?></th>
      <th class="list_label"><?php echo i18n::translate('Order')?></th>
      <th class="list_label"><?php echo i18n::translate('Access Level')?></th>
      </tr>
    </thead>
    <tbody>
<?php
uasort($modules, "WT_Module::compare_menu_order");
$order = 1;
foreach($modules as $mod) {
	if(!$mod instanceof WT_Module_Menu) continue;
if ($mod->getMenuorder()==0) $mod->setMenuorder($order);
	?><tr class="sortme">
	<td class="list_value"><?php echo $mod->getTitle()?></td>
	<td class="list_value_wrap"><?php echo $mod->getDescription()?></td>
	<td class="list_value"><input type="text" size="5" value="<?php echo $order; ?>" name="menuorder-<?php echo $mod->getName() ?>" />
		<br />
		<img class="uarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["uarrow"]["other"];?>" border="0" title="move up" />
		<img class="udarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["udarrow"]["other"];?>" border="0" title="move to top" />
		<img class="darrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["darrow"]["other"];?>" border="0" title="move down" />
		<img class="ddarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["ddarrow"]["other"];?>" border="0" title="move to bottom" />
	</td>
	<td class="list_value_wrap">
	  <table>
	<?php
		foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
			$varname = 'menuaccess-'.$mod->getName().'-'.$ged_id;
			?>
			<tr><td><?php echo $ged_name ?></td><td>
			<select id="<?php echo $varname?>" name="<?php echo $varname?>">
				<?php write_access_option_numeric($mod->getMenuEnabled($ged_id)) ?>
			</select></td></tr>
			<?php 
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
<div id="tabs_tab">
<!-- tabs -->
<table id="tabs_table" class="list_table">
    <thead>
      <tr>
      <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
      <th class="list_label"><?php echo i18n::translate('Description')?></th>
      <th class="list_label"><?php echo i18n::translate('Order')?></th>
      <th class="list_label"><?php echo i18n::translate('Access Level')?></th>
      </tr>
    </thead>
    <tbody>
<?php
uasort($modules, "WT_Module::compare_tab_order");
$order = 1;
foreach($modules as $mod) {
	if(!$mod instanceof WT_Module_Tab) continue;
	if ($mod->getTaborder()==0) $mod->setTaborder($order);
	?><tr class="sortme">
	<td class="list_value"><?php echo $mod->getTitle()?></td>
	<td class="list_value_wrap"><?php echo $mod->getDescription()?></td>
	<td class="list_value"><input type="text" size="5" value="<?php echo $order; ?>" name="taborder-<?php echo $mod->getName() ?>" />
		<br />
		<img class="uarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["uarrow"]["other"];?>" border="0" title="move up" />
		<img class="udarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["udarrow"]["other"];?>" border="0" title="move to top" />
		<img class="darrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["darrow"]["other"];?>" border="0" title="move down" />
		<img class="ddarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["ddarrow"]["other"];?>" border="0" title="move to bottom" />
	</td>
	<td class="list_value_wrap">
	<table>
	<?php
		foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
			$varname = 'tabaccess-'.$mod->getName().'-'.$ged_id;
			?>
			<tr><td><?php echo $ged_name ?></td><td>
			<select id="<?php echo $varname?>" name="<?php echo $varname?>">
				<?php write_access_option_numeric($mod->getTabEnabled($ged_id)) ?>
			</select></td></tr>
			<?php 
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
<div id="sidebars_tab">
<!-- sidebars -->
<table id="sidebars_table" class="list_table">
    <thead>
      <tr>
      <th class="list_label"><?php echo i18n::translate('Module Name')?></th>
      <th class="list_label"><?php echo i18n::translate('Description')?></th>
      <th class="list_label"><?php echo i18n::translate('Order')?></th>
      <th class="list_label"><?php echo i18n::translate('Access Level')?></th>
      </tr>
    </thead>
    <tbody>
<?php
uasort($modules, "WT_Module::compare_sidebar_order");
$order = 1;
foreach($modules as $mod) {
	if(!$mod instanceof WT_Module_Sidebar) continue;
	if ($mod->getSidebarorder()==0) $mod->setSidebarorder($order);
	?><tr class="sortme">
	<td class="list_value"><?php echo $mod->getTitle()?></td>
	<td class="list_value_wrap"><?php echo $mod->getDescription()?></td>
	<td class="list_value"><input type="text" size="5" value="<?php echo $order; ?>" name="sideorder-<?php echo $mod->getName() ?>" />
		<br />
		<img class="uarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["uarrow"]["other"];?>" border="0" title="move up" />
		<img class="udarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["udarrow"]["other"];?>" border="0" title="move to top" />
		<img class="darrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["darrow"]["other"];?>" border="0" title="move down" />
		<img class="ddarrow" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES["ddarrow"]["other"];?>" border="0" title="move to bottom" />
	</td>
	<td class="list_value_wrap">
	<table>
	<?php
		foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
			$varname = 'sidebaraccess-'.$mod->getName().'-'.$ged_id;
			?>
			<tr><td><?php echo $ged_name ?></td><td>
			<select id="<?php echo $varname?>" name="<?php echo $varname?>">
				<?php write_access_option_numeric($mod->getSidebarEnabled($ged_id)) ?>
			</select></td></tr>
			<?php 
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
</div>
</form>
</div>
</div>
<?php
print_footer();
?>
