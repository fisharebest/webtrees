<?php
if (!defined('WT_SCRIPT_NAME')) define('WT_SCRIPT_NAME', 'sidebar.php');
require_once('includes/session.php');
require_once(WT_ROOT.'includes/classes/class_module.php');

$sb_action = safe_GET('sb_action', WT_REGEX_ALPHANUM, 'none');
//-- handle ajax calls
if ($sb_action!='none') {
	header('Content-type: text/html; charset=UTF-8');
	$sidebarmods = WT_Module::getActiveSidebars();
	class tempController {
		var $pid;
		var $famid;
	}
	
	$controller = new tempController();

	$pid = safe_GET_xref('pid', '');
	if (empty($pid)) $pid = safe_POST_xref('pid', '');
	if (!empty($pid)) {
		$controller->pid = $pid;
	}
	$pid = safe_GET_xref('rootid',  '');
	if (empty($pid)) $pid = safe_POST_xref('rootid', '');
	if (!empty($pid)) {
		$controller->pid = $pid;
	}
	$famid = safe_GET('famid', WT_REGEX_XREF, '');
	if (empty($famid)) $famid = safe_POST('famid', WT_REGEX_XREF, '');
	if (!empty($famid)) {
		$controller->famid = $famid;
	}
	$sid = safe_GET('sid', WT_REGEX_XREF, '');
	if (empty($sid)) $sid = safe_POST('sid', WT_REGEX_XREF, '');
	if (!empty($sid)) {
		$controller->sid = $sid;
	}
	
	if ($sb_action=='loadMods') {
		$counter = 0;
		foreach($sidebarmods as $mod) {
			if (isset($controller)) $mod->setController($controller);
			if ($mod->hasSidebarContent()) {
				?><h3 title="<?php echo $mod->getName()?>"><a href="#"><?php echo $mod->getTitle()?></a></h3>
				<div id="sb_content_<?php echo $mod->getName()?>">
				<?php if ($counter==0) echo $mod->getSidebarContent();
				else {?><img src="<?php echo $WT_IMAGE_DIR ?>/loading.gif" /><?php }?>
				</div>
				<?php 
				$counter++;
			}
		}
		exit;
	}
	if ($sb_action=='loadmod') {
		$modName = safe_GET('mod', WT_REGEX_URL, '');
		if (isset($sidebarmods[$modName])) {
			$mod = $sidebarmods[$modName];
			if (isset($controller)) $mod->setController($controller);
			echo $mod->getSidebarContent();
		}
		exit;
	}
	if (isset($sidebarmods[$sb_action])) {
		$mod = $sidebarmods[$sb_action];
		echo $mod->getSidebarAjaxContent();
	}
	exit;
}

global $controller;
$pid='';
$famid='';
if (isset($controller)) {
	if (isset($controller->pid)) $pid = $controller->pid;
	if (isset($controller->rootid)) $pid = $controller->rootid;
	if (isset($controller->famid)) $famid = $controller->famid;
	if (isset($controller->sid)) $pid = $controller->sid;
} else {
	$pid = safe_GET_xref('pid', '');
	if (empty($pid)) $pid = safe_POST_xref('pid', '');
	if (empty($pid)) $pid = safe_GET_xref('rootid',  '');
	if (empty($pid)) $pid = safe_POST_xref('rootid', '');
	if (empty($pid)) $pid = safe_POST_xref('sid', '');
	if (empty($pid)) $pid = safe_GET_xref('sid', '');
	$famid = safe_GET('famid', WT_REGEX_XREF, '');
	if (empty($famid)) $famid = safe_POST('famid', WT_REGEX_XREF, '');
}
?>
<script type="text/javascript" src="js/jquery/jquery.scrollfollow.js"></script> 
<script type="text/javascript">
<!--
jQuery.noConflict(); // @see http://docs.jquery.com/Using_jQuery_with_Other_Libraries/
var loadedMods = new Array();
function closeCallback() {
	jQuery('#sidebarAccordion').hide();
	jQuery('#sidebar_pin').hide();
	if (pinned == false) {
		jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=false');
		pinned = false;
	}

}
function openCallback() {
	jQuery('#sidebarAccordion').accordion({
		fillSpace: true, 
		changestart: function(event, ui) {
			loadedMods[ui.oldHeader.attr('title')] = true;
			var active = ui.newHeader.attr('title');
			if (!loadedMods[active]) {
				jQuery('#sb_content_'+active).load('sidebar.php?sb_action=loadmod&mod='+active+'&pid=<?php echo $pid?>&famid=<?php echo $famid?>');
			}
		}
	});
}
jQuery(document).ready(function() {
	
	// Sidebar Pin Function ====================================
	jQuery('#sidebar_pin').toggle(
   		   	function() {
   	   		   	jQuery('#sidebar_pin img').attr('src', '<?php echo $WT_IMAGE_DIR.'/'.$WT_IMAGES['pin-in']['other'];?>').attr('title', '<?php echo i18n::translate('Unpin Sidebar');?>');
				// Shift content ---------------------------
				// -----------------------------------------
   	   			jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=true');
   	   			pinned = true;
   		   	},
   		   	function() {
   		   		jQuery('#sidebar_pin img').attr('src', '<?php echo $WT_IMAGE_DIR.'/'.$WT_IMAGES['pin-out']['other'];?>').attr('title', '<?php echo i18n::translate('Pin Sidebar');?>');
 				// Shift content back ----------------------
				// -----------------------------------------
   		   		jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=false');
   		   		pinned = false;
   		   	});
	   	<?php 	
	   	if (isset($_SESSION['WT_pin']) && $_SESSION['WT_pin']) {
	   	?>
	   		jQuery('#sidebar_pin').click();
	  	<?php
	  	}
	  	?>
   	// =========================================================

	//	jQuery('#sidebar').scrollFollow();
	jQuery('#sidebar_controls').show();	
	var modsLoaded = false;
	
	// Sidebar Open/Close Function =============================
	jQuery('#sidebar_open').toggle(function() {
		jQuery('#sidebar_open img').attr('style', 'margin-left:255px;' ).attr('src', '<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['slide_close']['other'];?>').attr('title', '<?php echo i18n::translate('Sidebar Close');?>');
		jQuery('#sidebar').animate({
			right: "0px",
			width: "260px"
		}, 500);
		if (!modsLoaded) {
			jQuery('#sidebarAccordion').load('sidebar.php', 'sb_action=loadMods&pid=<?php echo $pid?>&famid=<?php echo $famid?>', openCallback);
			modsLoaded=true;
		} else {
			jQuery("#sidebarAccordion").accordion("resize");
		}
		jQuery('#sidebarAccordion').show();
		jQuery('#sidebar_pin').show();		
		// Shift content -----------------------------------
   	   		var newwidth = 280;
	   		newwidth = jQuery('#tabs').width() - newwidth;
   	    	<?php if ($TEXT_DIRECTION=='rtl') {?>
   	   			//newwidth = jQuery('.static_tab').width() + 40;
   	   			//newwidth = jQuery('#tabs').width() - newwidth;
   	   		<?php } ?>
			// --- NOTE: --- REM next line to avoid the "page shift" when Navigator is opened. (Purely a preference choice)
   	   		jQuery('#tabs > div').css('width', newwidth+'px');
		// -------------------------------------------------
		// Check if pinned ---------------------------------
			<?php if (isset($_SESSION['WT_pin']) && $_SESSION['WT_pin']) { ?>
				jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=true');
			<?php } ?>
		// -------------------------------------------------
		sb_open=true;
	}, function() {
		jQuery('#sidebar_open img').attr('style', 'margin-left:0px;' ).attr('src', '<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['slide_open']['other'];?>').attr('title', '<?php echo i18n::translate('Sidebar Open');?>');
		jQuery('#sidebar').css('left', '');
		jQuery('#sidebar').animate({
			right: "0px",
			width: "0px"
		}, 500, 'linear', closeCallback);		
		// Shift content back ------------------------------
			jQuery('#tabs div').css('width', '');
		// -------------------------------------------------
		// Check if pinned ---------------------------------
			<?php if (isset($_SESSION['WT_pin']) && $_SESSION['WT_pin']) { ?>
				jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=true');
			<?php } ?>
		// -------------------------------------------------
		sb_open=false;
	});
	// =========================================================
	
   	<?php 
   	if ( isset($_SESSION['WT_pin']) && $_SESSION['WT_pin'] ) { 
  	?>
  		if (sb_open!=true) {
			jQuery('#sidebar_open').click();
		} else {
			jQuery('#sidebar_open').click();
		}
  	<?php 
  	}
  	?>
  	// Debug ---------------------
  	// alert("Pinned = "+sb_open);

});
-->
</script>
<div id="sidebar">
	<div id="sidebar_controls" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top ui-state-focus">
		<a id="sidebar_open" href="#open"><img style="margin-left:0px;" src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['slide_open']['other'];?>" border="0" title="<?php echo i18n::translate('Sidebar Open');?>"></a> 
		<a id="sidebar_pin" href="#pin"><img src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['pin-out']['other'];?>" border="0" title="<?php echo i18n::translate('Pin Sidebar');?>"></a> 
	</div>
	<div id="sidebarAccordion"></div>
	<span class="ui-icon ui-icon-grip-dotted-horizontal" style="margin:2px auto;"></span>
</div>
<div id="debug">
</div>
