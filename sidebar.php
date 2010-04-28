<?php
if (!defined('WT_SCRIPT_NAME')) define('WT_SCRIPT_NAME', 'sidebar.php');
require_once('includes/session.php');
require_once(WT_ROOT.'includes/classes/class_module.php');

$sb_action = safe_GET('sb_action', WT_REGEX_ALPHANUM, 'none');
//-- handle ajax calls
if ($sb_action!='none') {
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
<style type="text/css">
<?php 
//-- standard theme styles
if ($TEXT_DIRECTION=='ltr') { ?>

<?php } 
//-- RTL styles
else {  ?>
/*
#sidebar {
	position: absolute;
	left: 1px;
	width: 0px;
	height: 450px;
	z-index: 50;
	margin-top: 10px;
	margin-left: 12px;
	background-color: #dddddd;
}
#sidebar_controls {
	position: absolute;
	float: right;
	right: -22px;
	margin-top: 0px;
	margin-right: 5px;
	height: 29px;
	width: 16px;
	z-index: 10;
}
#sidebar_open img {
	padding-top: 6px;
	padding-bottom: 7px;
	margin-right: 0px;
	height: 15px;
	background-color: #ffffff;
}
*/
<?php } ?>
</style>
<script type="text/javascript" src="js/jquery/jquery.scrollfollow.js"></script> 
<script type="text/javascript">
<!--
jQuery.noConflict(); // @see http://docs.jquery.com/Using_jQuery_with_Other_Libraries/
var loadedMods = new Array();
function closeCallback() {
	jQuery('#sidebarAccordion').hide();
	jQuery('#sidebar_pin').hide();
	if (pinned == true) {
		jQuery('#sidebar_pin').click();
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
   	   		   	var newwidth = 345;
   	   		   	newwidth = jQuery('#tabs').width() - newwidth;
   	   		   	<?php if ($TEXT_DIRECTION=='rtl') {?>
   	   		   		//newwidth = jQuery('.static_tab').width() + 40;
   	   				//newwidth = jQuery('#tabs').width() - newwidth;
   	   		   	<?php } ?>
				// --- NOTE: --- REM next line to avoid the "page shift" when Navigator is pinned. (Purely a preference choice)
   	   		 	jQuery('#tabs > div').css('width', newwidth+'px');
   	   		   	pinned = true;
   	   			jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=true');
   		   	},
   		   	function() {
   		   		jQuery('#sidebar_pin img').attr('src', '<?php echo $WT_IMAGE_DIR.'/'.$WT_IMAGES['pin-out']['other'];?>').attr('title', '<?php echo i18n::translate('Pin Sidebar');?>');
   		   		jQuery('#tabs div').css('width', '');
   		   		pinned = false;
   		   		jQuery.get('individual.php?pid=<?php echo $controller->pid;?>&action=ajax&pin=false');
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

	var modsLoaded = false;
	jQuery('#sidebar_open').toggle(function() {
		jQuery('#sidebar_open img').attr('src', '<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['slide_close']['other'];?>').attr('title', '<?php echo i18n::translate('Sidebar Close');?>');
		jQuery('#sidebar').animate({
			right: "0px",
			width: "310px"
		}, 500);
		if (!modsLoaded) {
			jQuery('#sidebarAccordion').load('sidebar.php', 'sb_action=loadMods&pid=<?php echo $pid?>&famid=<?php echo $famid?>', openCallback);
			modsLoaded=true;
		}
		else jQuery("#sidebarAccordion").accordion("resize");
		jQuery('#sidebarAccordion').show();
		jQuery('#sidebar_pin').show();
	}, function() {
		jQuery('#sidebar_open img').attr('src', '<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['slide_open']['other'];?>').attr('title', '<?php echo i18n::translate('Sidebar Open');?>');
		jQuery('#sidebar').css('left', '');
		jQuery('#sidebar').animate({
			right: "0px",
			width: "0px"
		}, 500, 'linear', closeCallback);
	});
   	<?php 
   	if (isset($_SESSION['WT_pin']) && $_SESSION['WT_pin']) { 
   	?>
	   		jQuery('#sidebar_open').click();
  	<?php 
  	} 
  	?>

});
//-->
</script>
<div id="sidebar">
	<div id="sidebar_controls" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top ui-state-focus">
		<a id="sidebar_open" href="#open"><img src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['slide_open']['other'];?>" border="0" title="<?php echo i18n::translate('Sidebar Open');?>"></a> 
		<a id="sidebar_pin" href="#pin"><img src="<?php echo $WT_IMAGE_DIR."/".$WT_IMAGES['pin-out']['other'];?>" border="0" title="<?php echo i18n::translate('Pin Sidebar');?>"></a> 
	</div>
	<div id="sidebarAccordion"></div>
	<span class="ui-icon ui-icon-grip-dotted-horizontal" style="margin:2px auto;"></span>
</div>
<div id="debug">
</div>
