<?php
/**
 * Online UI for editing site configuration variables
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2007  PGV Development Team
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
 * @subpackage Lightbox
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $pid, $GEDCOM ;

$pid=safe_get('pid');
$action = safe_POST("action");

print_header(WT_I18N::translate('Lightbox-Album Configuration'));

require WT_ROOT.WT_MODULES_DIR.'lightbox/lb_defaultconfig.php';

if (!WT_USER_IS_ADMIN) {
	echo'<div class="warning">', WT_I18N::translate('Page only for Administrators'), '</div>';
	print_footer();
	exit;
}

if ($action=='update' && !isset($security_user)) {
	set_module_setting('lightbox', 'LB_AL_HEAD_LINKS',  $_POST['NEW_LB_AL_HEAD_LINKS']);
	set_module_setting('lightbox', 'LB_AL_THUMB_LINKS', $_POST['NEW_LB_AL_THUMB_LINKS']);
	set_module_setting('lightbox', 'LB_TT_BALLOON',     $_POST['NEW_LB_TT_BALLOON']);
	set_module_setting('lightbox', 'LB_ML_THUMB_LINKS', $_POST['NEW_LB_ML_THUMB_LINKS']);
	set_module_setting('lightbox', 'LB_MUSIC_FILE',     $_POST['NEW_LB_MUSIC_FILE']);
	set_module_setting('lightbox', 'LB_SS_SPEED',       $_POST['NEW_LB_SS_SPEED']);
	set_module_setting('lightbox', 'LB_TRANSITION',     $_POST['NEW_LB_TRANSITION']);
	set_module_setting('lightbox', 'LB_URL_WIDTH',      $_POST['NEW_LB_URL_WIDTH']);
	set_module_setting('lightbox', 'LB_URL_HEIGHT',     $_POST['NEW_LB_URL_HEIGHT']);

	AddToLog('Lightbox config updated', 'config');
	// read the config file again, to set the vars
	require WT_ROOT.WT_MODULES_DIR.'lightbox/lb_defaultconfig.php';
}

?>
<form method="post" name="configform" action="module.php?mod=lightbox&amp;mod_action=admin_config&amp;pid=<?php echo $pid; ?>">
<input type="hidden" name="action" value="update" />
	<table id="album_config">
		<tr>
			<td><?php echo WT_I18N::translate('Individual Page - Album Tab Header'); ?><?php echo help_link('lb_al_head_links', $this->getName()); ?><p><?php echo WT_I18N::translate('Link appearance'); ?></p></td>
			<td>
				<select name="NEW_LB_AL_HEAD_LINKS">
					<option value="icon" <?php if ($LB_AL_HEAD_LINKS=="icon") echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Icon'); ?></option>
					<option value="text" <?php if ($LB_AL_HEAD_LINKS=="text") echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Text'); ?></option>
					<option value="both" <?php if ($LB_AL_HEAD_LINKS=="both") echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Both'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo WT_I18N::translate('Individual Page - Album Tab Thumbnail - Notes Tooltip'); ?><?php echo help_link('lb_tt_balloon', $this->getName()); ?><p><?php echo WT_I18N::translate('Notes - Tooltip appearance'); ?></p></td>
			<td><select name="NEW_LB_TT_BALLOON">
					<option value="true"  <?php if ($LB_TT_BALLOON=="true")  echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Balloon'); ?></option>
					<option value="false" <?php if ($LB_TT_BALLOON=="false") echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Normal'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo WT_I18N::translate('Individual Page - Album Tab Thumbnails'); ?><?php echo help_link('lb_al_thumb_links', $this->getName()); ?><p><?php echo WT_I18N::translate('Link appearance'); ?></p></td>
			<td><select name="NEW_LB_AL_THUMB_LINKS">
					<option value="icon" <?php if ($LB_AL_THUMB_LINKS=="icon") echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Icon'); ?></option>
					<option value="text" <?php if ($LB_AL_THUMB_LINKS=="text") echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Text'); ?></option>
				</select>
			</td>
		</tr>
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
				<input type="text" name="NEW_LB_MUSIC_FILE" value="<?php echo $LB_MUSIC_FILE; ?>" size="60" /><br />
			<?php echo WT_I18N::translate('Location of sound track file (Leave blank for no sound track)'); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo WT_I18N::translate('Image Transition speed'); ?><?php echo help_link('lb_transition', $this->getName()); ?></td>
			<td>
				<select name="NEW_LB_TRANSITION">
					<option value="none"   <?php if ($LB_TRANSITION=="none")   echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('None'); ?></option>
					<option value="normal" <?php if ($LB_TRANSITION=="normal") echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Normal'); ?></option>
					<option value="double" <?php if ($LB_TRANSITION=="double") echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Double'); ?></option>
					<option value="warp"   <?php if ($LB_TRANSITION=="warp")   echo 'selected="selected"'; ?>><?php echo WT_I18N::translate('Warp'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo WT_I18N::translate('URL Window dimensions'); ?><b><?php echo help_link('lb_url_dimensions', $this->getName()); ?></td>
			<td>
				<input type="text" name="NEW_LB_URL_WIDTH"  value="<?php echo $LB_URL_WIDTH; ?>"  size="4" />
				<?php echo WT_I18N::translate('Width'); ?>
				&nbsp;&nbsp;&nbsp;
				<input type="text" name="NEW_LB_URL_HEIGHT" value="<?php echo $LB_URL_HEIGHT; ?>" size="4" />
				<?php echo WT_I18N::translate('Height'); ?><br />
			<?php echo WT_I18N::translate('Width and height of URL window in pixels'); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo WT_I18N::translate('Multimedia Page - Thumbnails'); ?><?php echo help_link('lb_ml_thumb_links', $this->getName()); ?><p><?php echo WT_I18N::translate('Link appearance'); ?></p></td>
			<td>
				<select name="NEW_LB_ML_THUMB_LINKS">
					<option value= "none" <?php if ($LB_ML_THUMB_LINKS == "none")  echo 'selected="selected"'; ?>><?php echo  WT_I18N::translate('None'); ?></option>
					<option value= "text" <?php if ($LB_ML_THUMB_LINKS == "text")  echo 'selected="selected"'; ?>><?php echo  WT_I18N::translate('Text'); ?></option>
					<option value= "icon" <?php if ($LB_ML_THUMB_LINKS == "icon")  echo 'selected="selected"'; ?>><?php echo  WT_I18N::translate('Icon'); ?></option>
					<option value= "both" <?php if ($LB_ML_THUMB_LINKS == "both")  echo 'selected="selected"'; ?>><?php echo  WT_I18N::translate('Both'); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<input type="submit" value="<?php echo WT_I18N::translate('Save configuration'); ?>" onclick="closeHelp();" />
	&nbsp;&nbsp;
	<input type="reset" value="<?php echo WT_I18N::translate('Reset'); ?>" />
	&nbsp;&nbsp;
	<?php if ($pid) { ?>
		<INPUT TYPE="button" VALUE="<?php echo WT_I18N::translate('Return to Album page'); ?>" onclick="javascript:window.location='individual.php?pid=<?php echo $pid; ?>&gedcom=<?php echo $GEDCOM; ?>#lightbox'" />
	<?php } else { ?>
		<INPUT TYPE="button" VALUE="<?php echo WT_I18N::translate('Return to Admin Page'); ?>" onclick="javascript:window.location='admin_modules.php'" />
	<?php } ?>
</form>
<?php print_footer();
