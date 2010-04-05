<?php
/**
 * Lightbox Album module for phpGedView
 *
 * Display media Items using Lightbox
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @author Brian Holland
 */
if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $tabno, $LB_MUSIC_FILE, $LB_SS_SPEED, $LB_TRANSITION, $TEXT_DIRECTION;

// Get Javascript variables from lb_config.php ---------------------------
//			require_once WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
//			if ($theme_name=="Minimal") {
				// Force icon options to "text" when we're dealing with the Minimal theme
//				if ($LB_AL_HEAD_LINKS!="none") $LB_AL_HEAD_LINKS = "text";
//				if ($LB_AL_THUMB_LINKS!="none") $LB_AL_THUMB_LINKS = "text";
//				if ($LB_ML_THUMB_LINKS!="none") $LB_ML_THUMB_LINKS = "text";
//			}

			?>
			<script language="javascript" type="text/javascript">

			var CB_ImgDetails		= "<?php print i18n::translate('Details');			?>";		// = "Details"
			var CB_Detail_Info		= "<?php print i18n::translate('View this Media Item Details ...  Plus other Media Options - MediaViewer page');		?>";		// = "View this Media Item......etc"
			var CB_ImgNotes			= "<?php print i18n::translate('Notes');				?>";		// = "Notes"
			var CB_Note_Info		= "<?php print '';		?>";		// = ""
			var CB_Pause_SS			= "<?php print i18n::translate('Pause Slideshow'); 		?>";		// = "Pause Slideshow"
			var CB_Start_SS			= "<?php print i18n::translate('Start Slideshow'); 		?>";		// = "Start Slideshow"
			var CB_Music			= "<?php print i18n::translate('Turn Music On/Off');			?>";		// = "Music On/Off "
			var CB_Zoom_Off			= "<?php print i18n::translate('Disable Zoom');		?>";		// = "Disable Zoom"
			var CB_Zoom_On			= "<?php print i18n::translate('Zoom is enabled ... Use mousewheel or i and o keys to zoom in and out');			?>";		// = "Zoom is Enabled"
			var CB_Close_Win		= "<?php print i18n::translate('Close Lightbox window');		?>";		// = "Close Lightbox Window"
			var CB_Balloon			= "<?php print "false";							?>";		// Notes Tooltip Balloon or not

			<?php if ($TEXT_DIRECTION=="rtl") { ?>
				var CB_Alignm		= "<?php print "right";							?>";		// Notes RTL Tooltip Balloon Text align
				var CB_ImgNotes2	= "<?php print i18n::translate('Notes');				?>";		// Notes RTL Tooltip for Full Image
			<?php }else{ ?>
				var CB_Alignm		= "<?php print "left";							?>";		// Notes LTR Tooltip Balloon Text align
				var CB_ImgNotes2	= "<?php print i18n::translate('Notes');				?>";		// Notes LTR Tooltip for Full Image
			<?php }?>

			<?php if ($LB_MUSIC_FILE == "") { ?>
				var myMusic = null;
			<?php }else{ ?>
				var myMusic 	= '<?php print $LB_MUSIC_FILE; 			?>';  	// The music file
			<?php } ?>
			var CB_SlShowTime 	= '<?php print $LB_SS_SPEED; 			?>';	// Slide show timer
			var CB_Animation	= '<?php print $LB_TRANSITION; 			?>';	// Next/Prev Image transition effect

			</script>
			<?php
// -------------------------------------------------------------------------------
?>
<?php if ($TEXT_DIRECTION == "rtl") { ?>
		<script src="modules/lightbox/js/Sound.js" 					type="text/javascript"></script>
		<script src="modules/lightbox/js/clearbox.js" 				type="text/javascript"></script>
		<!--[if lte IE 7]>
		<link href ="modules/lightbox/css/album_page_RTL.css" 				rel="stylesheet" type="text/css" media="screen" />
		<![endif]-->

<?php }else{ ?>
		<script src="modules/lightbox/js/Sound.js" 					type="text/javascript"></script>
		<script src="modules/lightbox/js/clearbox.js" 				type="text/javascript"></script>

<?php  } ?>

		<script src="modules/lightbox/js/wz_tooltip.js" 			type="text/javascript"></script>
		<script src="modules/lightbox/js/tip_centerwindow.js" 		type="text/javascript"></script>
		<?php if ($TEXT_DIRECTION=="rtl") { ?>
			<script src="modules/lightbox/js/tip_balloon_RTL.js" 	type="text/javascript"></script>
		<?php }else{ ?>
			<script src="modules/lightbox/js/tip_balloon.js" 		type="text/javascript"></script>
		<?php } ?>

<?php

		require_once WT_ROOT.'js/prototype.js.htm';


// -------------------------------------------------------------------------------

?>
