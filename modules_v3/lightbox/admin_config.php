<?php
// Online UI for editing site configuration variables
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2007  PGV Development Team
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

$controller=new WT_Controller_Base();
$controller
	->requireAdminLogin()
	->setPageTitle(WT_I18N::translate('Lightbox-Album Configuration'))
	->pageHeader();

$action = safe_POST('action');

if ($action=='update') {
	set_module_setting('lightbox', 'LB_MUSIC_FILE',     $_POST['NEW_LB_MUSIC_FILE']);
	set_module_setting('lightbox', 'LB_SS_SPEED',       $_POST['NEW_LB_SS_SPEED']);
	set_module_setting('lightbox', 'LB_TRANSITION',     $_POST['NEW_LB_TRANSITION']);
	set_module_setting('lightbox', 'LB_URL_WIDTH',      $_POST['NEW_LB_URL_WIDTH']);
	set_module_setting('lightbox', 'LB_URL_HEIGHT',     $_POST['NEW_LB_URL_HEIGHT']);

	AddToLog('Lightbox config updated', 'config');
}

$LB_SS_SPEED=get_module_setting('lightbox', 'LB_SS_SPEED', '6');     // SlideShow speed in seconds.  [Min 2  max 25]
$LB_MUSIC_FILE=get_module_setting('lightbox', 'LB_MUSIC_FILE', WT_STATIC_URL.WT_MODULES_DIR.'lightbox/music/music.mp3');  // The music file. [mp3 only]
$LB_TRANSITION=get_module_setting('lightbox', 'LB_TRANSITION', 'warp');   // Next or Prvious Image Transition effect
          // Set to 'none'  No transtion effect.
          // Set to 'normal'  Normal transtion effect.
          // Set to 'double'  Fast transition effect.
          // Set to 'warp'  Stretch transtition effect. [Default]
$LB_URL_WIDTH =get_module_setting('lightbox', 'LB_URL_WIDTH',  '1000'); //  URL Window width in pixels
$LB_URL_HEIGHT=get_module_setting('lightbox', 'LB_URL_HEIGHT', '600'); //  URL Window height in pixels

?>
<form method="post" name="configform" action="module.php?mod=lightbox&amp;mod_action=admin_config">
<input type="hidden" name="action" value="update">
	<table id="album_config">
		<tr>
			<td><?php echo WT_I18N::translate('Slide Show speed'); ?><?php echo help_link('lb_ss_speed', $this->getName()); ?></td>
			<td>
				<select name="NEW_LB_SS_SPEED">
					<option value= "2" <?php if ($LB_SS_SPEED == 2)  echo 'selected="selected"'; ?>><?php echo  "2"; ?></option>
					<option value= "3" <?php if ($LB_SS_SPEED == 3)  echo 'selected="selected"'; ?>><?php echo  "3"; ?></option>
					<option value= "4" <?php if ($LB_SS_SPEED == 4)  echo 'selected="selected"'; ?>><?php echo  "4"; ?></option>
					<option value= "5" <?php if ($LB_SS_SPEED == 5)  echo 'selected="selected"'; ?>><?php echo  "5"; ?></option>
					<option value= "6" <?php if ($LB_SS_SPEED == 6)  echo 'selected="selected"'; ?>><?php echo  "6"; ?></option>
					<option value= "7" <?php if ($LB_SS_SPEED == 7)  echo 'selected="selected"'; ?>><?php echo  "7"; ?></option>
					<option value= "8" <?php if ($LB_SS_SPEED == 8)  echo 'selected="selected"'; ?>><?php echo  "8"; ?></option>
					<option value= "9" <?php if ($LB_SS_SPEED == 9)  echo 'selected="selected"'; ?>><?php echo  "9"; ?></option>
					<option value="10" <?php if ($LB_SS_SPEED ==10)  echo 'selected="selected"'; ?>><?php echo "10"; ?></option>
					<option value="12" <?php if ($LB_SS_SPEED ==12)  echo 'selected="selected"'; ?>><?php echo "12"; ?></option>
					<option value="15" <?php if ($LB_SS_SPEED ==15)  echo 'selected="selected"'; ?>><?php echo "15"; ?></option>
					<option value="20" <?php if ($LB_SS_SPEED ==20)  echo 'selected="selected"'; ?>><?php echo "20"; ?></option>
					<option value="25" <?php if ($LB_SS_SPEED ==25)  echo 'selected="selected"'; ?>><?php echo "25"; ?></option>
				</select>
			&nbsp;&nbsp;&nbsp; <?php echo WT_I18N::translate('Slide show timing in seconds'); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo WT_I18N::translate('Slideshow sound track'); ?><?php echo help_link('lb_music_file', $this->getName()); ?><p><?php echo WT_I18N::translate('(mp3 only)'); ?></p></td>
			<td>
				<input type="text" name="NEW_LB_MUSIC_FILE" value="<?php echo $LB_MUSIC_FILE; ?>" size="60"><br>
			<?php echo WT_I18N::translate('Location of sound track file (Leave blank for no sound track)'); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo WT_I18N::translate('Image Transition speed'); ?><?php echo help_link('lb_transition', $this->getName()); ?></td>
			<td>
				<select name="NEW_LB_TRANSITION">
					<option value="none"   <?php if ($LB_TRANSITION=='none')   echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('None'); ?></option>
					<option value="normal" <?php if ($LB_TRANSITION=='normal') echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Normal'); ?></option>
					<option value="double" <?php if ($LB_TRANSITION=='double') echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Double'); ?></option>
					<option value="warp"   <?php if ($LB_TRANSITION=='warp')   echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Warp'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo WT_I18N::translate('URL Window dimensions'); ?><b><?php echo help_link('lb_url_dimensions', $this->getName()); ?></td>
			<td>
				<input type="text" name="NEW_LB_URL_WIDTH"  value="<?php echo $LB_URL_WIDTH; ?>"  size="4">
				<?php echo WT_I18N::translate('Width'); ?>
				&nbsp;&nbsp;&nbsp;
				<input type="text" name="NEW_LB_URL_HEIGHT" value="<?php echo $LB_URL_HEIGHT; ?>" size="4">
				<?php echo WT_I18N::translate('Height'); ?><br>
			<?php echo WT_I18N::translate('Width and height of URL window in pixels'); ?>
			</td>
		</tr>
	</table>
	<input type="submit" value="<?php echo WT_I18N::translate('Save'); ?>"">
	&nbsp;&nbsp;
	<input type="reset" value="<?php echo WT_I18N::translate('Reset'); ?>">
</form>
