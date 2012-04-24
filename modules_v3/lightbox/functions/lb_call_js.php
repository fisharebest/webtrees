<?php
// Lightbox Album module for webtrees
//
// Display media Items using Lightbox
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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

global $TEXT_DIRECTION;
$LB_MUSIC_FILE=get_module_setting('lightbox', 'LB_MUSIC_FILE', WT_STATIC_URL.WT_MODULES_DIR.'lightbox/music/music.mp3');
?>
<script type="text/javascript">
	var CB_ImgDetails = "<?php echo WT_I18N::translate('Details'); ?>";
	var CB_Detail_Info = "<?php echo WT_I18N::translate('View image details'); ?>";
	var CB_ImgNotes = "<?php echo WT_I18N::translate('Notes'); ?>";
	var CB_Note_Info = "";
	var CB_Pause_SS = "<?php echo WT_I18N::translate('Pause Slideshow'); ?>";
	var CB_Start_SS = "<?php echo WT_I18N::translate('Start Slideshow'); ?>";
	var CB_Music = "<?php echo WT_I18N::translate('Turn Music On/Off'); ?>";
	var CB_Zoom_Off = "<?php echo WT_I18N::translate('Disable Zoom'); ?>";
	var CB_Zoom_On = "<?php echo WT_I18N::translate('Zoom is enabled ... Use mousewheel or i and o keys to zoom in and out'); ?>";
	var CB_Close_Win = "<?php echo WT_I18N::translate('Close Lightbox window'); ?>";
	var CB_Balloon = "false"; // Notes Tooltip Balloon or not

	<?php if ($TEXT_DIRECTION=='rtl') { ?>
		var CB_Alignm = 'right'; // Notes RTL Tooltip Balloon Text align
		var CB_ImgNotes2 = "<?php echo WT_I18N::translate('Notes'); ?>"; // Notes RTL Tooltip for Full Image
	<?php } else { ?>
		var CB_Alignm = 'left'; // Notes LTR Tooltip Balloon Text align
		var CB_ImgNotes2 = "<?php echo WT_I18N::translate('Notes'); ?>"; // Notes LTR Tooltip for Full Image
	<?php } ?>
	<?php if ($LB_MUSIC_FILE == '') { ?>
		var myMusic = null;
	<?php } else { ?>
		var myMusic  = '<?php echo $LB_MUSIC_FILE; ?>';   // The music file
	<?php } ?>
	var CB_SlShowTime  = '<?php echo get_module_setting('lightbox', 'LB_SS_SPEED', '6'); ?>'; // Slide show timer
	var CB_Animation = '<?php echo get_module_setting('lightbox', 'LB_TRANSITION', 'warp'); ?>'; // Next/Prev Image transition effect
</script>

<script src="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>lightbox/js/Sound.js"  type="text/javascript"></script>
<script src="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>lightbox/js/clearbox.js"  type="text/javascript"></script>
<script src="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>lightbox/js/wz_tooltip.js"  type="text/javascript"></script>
<script src="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>lightbox/js/tip_centerwindow.js"  type="text/javascript"></script>
<?php if ($TEXT_DIRECTION=='rtl') { ?>
	<script src="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>lightbox/js/tip_balloon_RTL.js"  type="text/javascript"></script>
<?php } else { ?>
	<script src="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>lightbox/js/tip_balloon.js"  type="text/javascript"></script>
<?php }
