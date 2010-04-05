<?php
/**
* Individual Page
*
* Display all of the information about an individual
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

define('WT_SCRIPT_NAME', 'individual.php');
require './includes/session.php';
require WT_ROOT.'includes/controllers/individual_ctrl.php';

$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;

$controller=new IndividualController();
$controller->init();

// tell tabs that use jquery that it is already loaded
define('WT_JQUERY_LOADED', 1);

// We have finished writing to $_SESSION, so release the lock
session_write_close();
print_header($controller->getPageTitle());

if (!$controller->indi){
	echo "<b>", i18n::translate('Unable to find record with ID'), "</b><br /><br />";
	print_footer();
	exit;
}
else if (!$controller->indi->canDisplayName()) {
	print_privacy_error($CONTACT_EMAIL);
	print_footer();
	exit;
}
$linkToID=$controller->pid; // -- Tell addmedia.php what to link to

//$last_clicked=safe_get('tab');

?>

<script language="JavaScript" type="text/javascript">
// <![CDATA[
// javascript function to open a window with the raw gedcom in it
function show_gedcom_record(shownew) {
	fromfile="";
	if (shownew=="yes") fromfile='&fromfile=1';
	var recwin = window.open("gedrecord.php?pid=<?php echo $controller->pid; ?>"+fromfile, "_blank", "top=50,left=50,width=600,height=400,scrollbars=1,scrollable=1,resizable=1");
}
<?php if (WT_USER_CAN_EDIT) { ?>
function open_link_remote(pid){
	window.open("addremotelink.php?pid="+pid, "_blank", "top=50,left=50,width=600,height=500,scrollbars=1,scrollable=1,resizable=1");
	return false;
}

function showchanges() {
	window.location = '<?php echo $controller->indi->getLinkUrl(); ?>&show_changes=yes';
}
<?php } ?>

// function is required by cloudy theme
function resize_content_div(i) {
	// check for container ..
	var cont = document.getElementById("content");
	if (!cont) cont = document.getElementById("container");
	if (cont) {
		if (document.getElementById("marker"+i)) {
			var y = getAbsoluteTop("marker"+i);
			if (y<300) y=600;
			cont.style.height =y.toString()+'px';
		}
	}
}
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
var selectedTab="<?php echo safe_get('tab')?>";
if (selectedTab != "" && selectedTab != "undefined" && selectedTab != null) {
	var selectedTab = selectedTab;
}else{
	var selectedTab = 0;
}

var tabCache = new Array();

var pinned = false;
function enable_static_tab() {
	jQuery(".static_tab").addClass("static_tab_<?php echo $TEXT_DIRECTION;?>");
	jQuery(".static_tab_content").removeClass("ui-tabs-hide");
	jQuery(".static_tab_content").removeClass("ui-tabs-panel");
	// jQuery(".static_tab_content").removeClass("ui-widget-content");
	jQuery(".static_tab_content").addClass("ui-corner-all");
	var top = jQuery(".static_tab").offset().top+jQuery(".static_tab").height();
	jQuery(".static_tab_content").css("top", top+"px");
	jQuery(".static_tab_content").addClass("static_tab_content_<?php echo $TEXT_DIRECTION;?>");
	if (!pinned) jQuery(".static_tab_content").hide();
}

jQuery(document).ready(function(){
	// TODO: change images directory when the common images will be deleted.
	// jQuery('#tabs').tabs({ spinner: '<img src=\"<?php echo $WT_IMAGE_DIR; ?>/loading.gif\" height=\"18\" border=\"0\" />' });
	jQuery('#tabs').tabs({ spinner: '<img src=\"images/loading.gif\" height=\"18\" border=\"0\" alt=\"\" />' });
	jQuery("#tabs").tabs({ cache: true, selected: <?php echo $controller->default_tab?> });
	var $tabs = jQuery('#tabs');
	jQuery('#tabs').bind('tabsshow', function(event, ui) {
		selectedTab = ui.tab.name;
		tabCache[selectedTab] = true;

	<?php
	foreach($controller->modules as $mod) {
		if ($mod!=$controller->static_tab && $mod instanceof WT_Module_Tab) {
			echo $mod->getJSCallbackAllTabs()."\n";
			$modjs = $mod->getJSCallback();
			if (!empty($modjs)) {
				echo 'if (ui.tab.name == "'.$mod->getName().'") { '.$modjs.' }';
			}
		}
	}
	?>
	<?php if ($controller->static_tab){ 
		echo "enable_static_tab();";
	}?>
	});
	// static tab changes
	<?php if ($controller->static_tab){?>
	jQuery('#tabs').bind('tabsselect', function(event, ui) {
			if (ui.panel.id=='<?php echo $controller->static_tab->getName();?>') {
				if (!pinned) {
					if (jQuery(".static_tab_content").css("display")=="none") {
							jQuery(".static_tab_content").show();
							<?php echo $controller->static_tab->getJSCallbackAllTabs()."\n";
							$modjs = $controller->static_tab->getJSCallback();
							echo $modjs;
							?>
					}
					else jQuery(".static_tab_content").hide();
				}
				return false;
			}
	});
	enable_static_tab();

	jQuery('#pin').toggle(
			function() {
				jQuery('#pin img').attr('src', '<?php echo $WT_IMAGE_DIR.'/'.$WT_IMAGES['pin-in']['other'];?>');
				var newwidth = jQuery('.static_tab').position().left-40;
				<?php if ($TEXT_DIRECTION=='rtl') {?>
				newwidth = jQuery('.static_tab').width() + 40;
				newwidth = jQuery('#tabs').width() - newwidth;
				<?php } ?>
				// --- NOTE: --- REM next line to avoid the "page shift" when Navigator is pinned. (Purely a preference choice)
				jQuery('#tabs > div').css('width', newwidth+'px');
				jQuery('.static_tab_content').show();
				<?php echo $controller->static_tab->getJSCallbackAllTabs()."\n";
				$modjs = $controller->static_tab->getJSCallback();
				echo $modjs;
				?>
				pinned = true;
				jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=true');
			},
			function() {
				jQuery('#pin img').attr('src', '<?php echo $WT_IMAGE_DIR.'/'.$WT_IMAGES['pin-out']['other'];?>');
				jQuery('#tabs div').css('width', '');
				jQuery('.static_tab_content').hide();
				pinned = false;
				jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=false');
			});
		<?php  if (isset($_SESSION['WT_pin']) && $_SESSION['WT_pin']) { ?>
			jQuery('#pin').click();
		<?php }
	}
	
	$tabcount = 0;
	foreach($controller->modules as $mod) {
		if ($mod!=$controller->static_tab && $mod instanceof WT_Module_Tab) {
			if ($tabcount==$controller->default_tab || !$mod->canLoadAjax()) {
				$modjs = $mod->getJSCallback();
				echo $modjs."\n";
			}
			if ($mod->hasTabContent()) $tabcount++; 
		}
	}
	?>
});

//]]>
</script>
<style type="text/css">
#pin {
	float: <?php echo $TEXT_DIRECTION=='rtl'?'left':'right';?>;
}
</style>
<div id="indi_main_blocks">
<div id="indi_top">
		<div id="indi_mainimage">
		<?php if ($controller->canShowHighlightedObject()) {
			echo $controller->getHighlightedObject();
		} ?>
		</div>
		<div id="indi_name">
		<?php
		if ((empty($SEARCH_SPIDER))&&($controller->accept_success)) echo "<b>", i18n::translate('Changes successfully accepted into database'), "</b><br />";
		if ($controller->indi->isMarkedDeleted()) echo "<span class=\"error\">".i18n::translate('This record has been marked for deletion upon admin approval.')."</span>"; 
		?>
		<span class="name_head"><?php
		if ($TEXT_DIRECTION=="rtl") echo "&nbsp;";
			echo PrintReady($controller->indi->getFullName());
			echo "&nbsp;&nbsp;";
			echo PrintReady("(".$controller->pid.")");
			if (WT_USER_IS_ADMIN) {
				$user_id=get_user_from_gedcom_xref(WT_GED_ID, $controller->pid);
				if ($user_id) {
					$pgvuser=get_user_name($user_id);
					echo "&nbsp;";
					echo printReady("(<a href=\"useradmin.php?action=edituser&amp;username={$pgvuser}\">{$pgvuser}</a>)");
				}
			}
		?></span><br /><br />
		<?php if (strlen($controller->indi->getAddName()) > 0) echo "<span class=\"name_head\">", PrintReady($controller->indi->getAddName()), "</span><br />"; ?>
		
		<div id="indi_details">
		<?php if ($controller->indi->canDisplayDetails()) { ?>
		<?php
			$col=0; $maxcols=7; // 4 with data and 3 spacers
			$globalfacts=$controller->getGlobalFacts();
			$nameSex = array('NAME', 'SEX');
			foreach ($globalfacts as $key=>$value) {
				$fact = $value->getTag();
				if (in_array($fact, $nameSex)) {
						if ($col>0) {
							++$col;
						}
					if ($fact=="SEX") $controller->print_sex_record($value);
					if ($fact=="NAME") $controller->print_name_record($value);
						++$col;
						if ($col==$maxcols) {
							echo '</div><div id="indi_eventdetails">';
							$col=0;
						}
					}
			}
			// Display summary birth/death info.
			$summary=$controller->indi->format_first_major_fact(WT_EVENTS_BIRT, 2);
			if (!($controller->indi->isDead())) {
				// If alive display age
				$bdate=$controller->indi->getBirthDate();
				$age = GedcomDate::GetAgeGedcom($bdate);
				if ($age!="")
					$summary.= "<dt class=\"label\">".i18n::translate('Age')."</dt><dd class=\"field\">".get_age_at_event($age, true)."</dd>";
			}
			$summary.=$controller->indi->format_first_major_fact(WT_EVENTS_DEAT, 2);
			if ($SHOW_LDS_AT_GLANCE) {
				$summary.='<b>'.get_lds_glance($controller->indi->getGedcomRecord()).'</b>';
			}
			if ($summary) {
				++$col;
				echo '<div id="mainfacts">', "\n\t<dl>\n", $summary, "\n\t</dl>\n</div>\n";
			}
		?>
		</div>
	</div><div class="clearfloat"></div>
		<div id="hitcounter">
		<?php
		if($SHOW_COUNTER && (empty($SEARCH_SPIDER))) {
			//print indi counter only if displaying a non-private person
			require WT_ROOT.'includes/hitcount.php';
			echo i18n::translate('Hit Count:'), " ", $hitCount;
		}
		// if individual is a remote individual
		// if information for this information is based on a remote site
		if ($controller->indi->isRemote())
		{
			?><br />
			<?php echo i18n::translate('The information for this individual was linked from a remote site.'); ?><!--<br />--><!--take this out if you want break the remote site and the fact that it was remote into two separate lines-->
			<a href="<?php echo encode_url($controller->indi->getLinkUrl()); ?>"><?php echo $controller->indi->getLinkTitle(); ?></a>
			<?php
		}
		// if indivual is not a remote individual
		// if information for this individual is based on this local site
		// this is not need to be printed, but may be uncommented if desired
		/*else
			echo("This is a local individual.");*/
	}
	?>
	</div>
	</div>
	
<?php // -- Sidebar --

		global $controller;
		if (method_exists($controller, 'getOtherMenu')) {	
			// echo "&nbsp;";
			require './sidebar.php'; 
		}

?>
<?php
foreach($controller->modules as $mod) {
	if ($mod instanceof WT_Module_Tab) {
		echo $mod->getPreLoadContent();
	}
} 
?>
<?php 
	if ((!$controller->isPrintPreview())&&(empty($SEARCH_SPIDER))) {
		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
	} ?>
</div>
<?php
if (!$controller->indi->canDisplayDetails()) {
	print "<table><tr><td class=\"facts_value\" >";
	print_privacy_error($CONTACT_EMAIL);
	print "</td></tr></table>";
} else {
	?>
	<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<?php
		$tabcount = 0; 
		foreach($controller->modules as $mod) {
			if ($mod!=$controller->static_tab && $mod instanceof WT_Module_Tab) {
				if ($tabcount==$controller->default_tab || !$mod->canLoadAjax()) {?>
					<li class="ui-state-default ui-corner-top"><a name="<?php echo $mod->getName(); ?>" href="#<?php echo $mod->getName()?>"><span><?php echo $mod->getTitle(); ?></span></a></li>
				<?php } else if ($mod instanceof WT_Module_Tab && ($mod->hasTabContent() || WT_USER_CAN_EDIT)) { ?>
					<li class="ui-state-default ui-corner-top"><a name="<?php echo $mod->getName(); ?>" href="individual.php?action=ajax&amp;module=<?php echo $mod->getName()?>&amp;pid=<?php echo $controller->pid?>">
						<span><?php echo $mod->getTitle()?></span>
						</a></li>
				<?php } 
				if ($mod->hasTabContent()) $tabcount++; 
			}
		}
		if ($controller->static_tab) {
			?><li class="ui-state-default ui-corner-top static_tab" style=" float:<?php echo ($TEXT_DIRECTION=='rtl')? 'left':'right';?>">
			<?php /*
				<a name="<?php echo $controller->static_tab->getName(); ?>" href="#<?php echo $controller->static_tab->getName()?>">
					<span><?php echo $controller->static_tab->getTitle(); ?></span>
				</a>
				<a id="pin" href="#pin"><img src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['pin-out']['other'];?>" border="0" alt=""/></a>
			*/ ?>
			<?php
			//global $controller;
			//if (method_exists($controller, 'getOtherMenu')) {	
				// echo "&nbsp;";
			//	require './sidebar.php'; 
			//}
			?>
			</li><?php 
		} 
		 ?>
	</ul>

	<?php 
	$tabcount = 0; 
	foreach($controller->modules as $mod) {
		if ($mod!=$controller->static_tab && $mod instanceof WT_Module_Tab) {
		if ($tabcount==$controller->default_tab || !$mod->canLoadAjax()) {?>
		<div id="<?php echo $mod->getName()?>" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
			<?php echo $mod->getTabContent(); ?>
		</div>	
		<?php }
		if ($mod->hasTabContent() || WT_USER_CAN_EDIT) $tabcount++;
		}
	} ?>
	</div> <!-- tabs -->
	<?php if ($controller->static_tab) { ?>
	<div class="static_tab_content ui-corner-bottom ui-corner-all" id="<?php echo $controller->static_tab->getName();?>">
	<?php echo $controller->static_tab->getTabContent(); ?>
	</div> 
	<!-- static tab -->
	<?php }
}?>
</div> <!--  end column 1 -->
<?php
echo WT_JS_START;
echo 'var catch_and_ignore; function paste_id(value) {catch_and_ignore = value;}';
echo 'if (typeof toggleByClassName == "undefined") {';
echo 'alert("webtrees.js: A javascript function is missing.  Please clear your Web browser cache");';
echo '}';
echo WT_JS_END;

if ($SEARCH_SPIDER) {
	if($SHOW_SPIDER_TAGLINE)
		echo i18n::translate('Search Engine Spider Detected'), ": ", $SEARCH_SPIDER;
	echo "</div></body></html>";
} else {
	print_footer();
}
?>
