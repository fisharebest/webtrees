<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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

class lightbox_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Album');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Album" module */ WT_I18N::translate('An alternative to the “media” tab, and an enhanced image viewer.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
			$this->config();
			break;
		case 'js':
			// tell browser to cache this javascript for 5 minutes
			$expireOffset = 60 * 5;
			$expireHeader = gmdate("D, d M Y H:i:s", WT_TIMESTAMP + $expireOffset) . " GMT";
			header('Content-type: application/javascript');
			header('Cache-control:');
			header('Pragma:');

			header("Expires: " . $expireHeader);
			echo file_get_contents(WT_MODULES_DIR.$this->getName().'/js/Sound.js');
			echo file_get_contents(WT_MODULES_DIR.$this->getName().'/js/clearbox.js');
			echo file_get_contents(WT_MODULES_DIR.$this->getName().'/js/wz_tooltip.js');
			echo file_get_contents(WT_MODULES_DIR.$this->getName().'/js/tip_centerwindow.js');
			echo file_get_contents(WT_MODULES_DIR.$this->getName().'/js/clsource_music.js');
			echo file_get_contents(WT_MODULES_DIR.$this->getName().'/js/tip_balloon.js');
			break;
		default:
			header('HTTP/1.0 404 Not Found');
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 60;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return WT_USER_CAN_EDIT || $this->get_media_count()>0;
	}

	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return $this->get_media_count()==0;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $controller, $sort_i;

		require_once WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lightbox_print_media.php';
		$html='<div id="'.$this->getName().'_content">';
		//Show Lightbox-Album header Links
		if (WT_USER_CAN_EDIT) {
			$html.='<table class="facts_table"><tr>';
			$html.='<td class="descriptionbox rela">';
			// Add a new media object
			if (get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
				$html.='<span><a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid='.$controller->record->getXref().'\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=780,width=600\');return false;">';
				$html.='<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/image_add.gif" id="head_icon" class="icon" title="'.WT_I18N::translate('Add a new media object').'" alt="'.WT_I18N::translate('Add a new media object').'">';
				$html.=WT_I18N::translate('Add a new media object');
				$html.='</a></span>';
				// Link to an existing item
				$html.='<span><a href="#" onclick="window.open(\'inverselink.php?linktoid='.$controller->record->getXref().'&linkto=person\', \'_blank\', \'resizable=1,scrollbars=1,top=50,height=300,width=450\');">';
				$html.= '<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/image_link.gif" id="head_icon" class="icon" title="'.WT_I18N::translate('Link to an existing media object').'" alt="'.WT_I18N::translate('Link to an existing media object').'">';
				$html.=WT_I18N::translate('Link to an existing media object');
				$html.='</a></span>';
			}
			if (WT_USER_GEDCOM_ADMIN && $this->get_media_count()>1) {
				// Popup Reorder Media
				$html.='<span><a href="#" onclick="reorder_media(\''.$controller->record->getXref().'\')">';
				$html.='<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'lightbox/images/images.gif" id="head_icon" class="icon" title="'.WT_I18N::translate('Re-order media').'" alt="'.WT_I18N::translate('Re-order media').'">';
				$html.=WT_I18N::translate('Re-order media');
				$html.='</a></span>';
				$html.='</td>';
			}
			$html.='</tr></table>';
		}
		$media_found = false;

		// Used when sorting media on album tab page
		$html.='<table width="100%" cellpadding="0" border="0"><tr>';
		$html.='<td width="100%" valign="top" >';
		ob_start();
		lightbox_print_media($controller->record->getXref(), 0, true, 1); // map, painting, photo, tombstone)
		lightbox_print_media($controller->record->getXref(), 0, true, 2); // card, certificate, document, magazine, manuscript, newspaper
		lightbox_print_media($controller->record->getXref(), 0, true, 3); // electronic, fiche, film
		lightbox_print_media($controller->record->getXref(), 0, true, 4); // audio, book, coat, video, other
		lightbox_print_media($controller->record->getXref(), 0, true, 5); // footnotes
		return
			$html.
			ob_get_clean().
			'</td></tr></table></div>';
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER; // Search engines cannot use AJAX
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		$this->getJS();
	}

	// Implement WT_Module_Tab
	public function getJSCallback() {
		return 'CB_Init();';
	}

	protected $mediaCount = null;

	private function get_media_count() {
		global $controller;

		if ($this->mediaCount===null) {
			$ct = preg_match_all("/\d OBJE/", $controller->record->getGedcomRecord(), $match);
			foreach ($controller->record->getSpouseFamilies() as $sfam)
				$ct += preg_match_all("/\d OBJE/", $sfam->getGedcomRecord(), $match);
			$this->mediaCount = $ct;
		}
		return $this->mediaCount;
	}

	private function getJS() {
		global $controller, $TEXT_DIRECTION;

		$LB_MUSIC_FILE=get_module_setting('lightbox', 'LB_MUSIC_FILE', WT_STATIC_URL.WT_MODULES_DIR.'lightbox/music/music.mp3');
		$js='var CB_ImgDetails = "'.WT_I18N::translate('Details').'";
		var CB_Detail_Info = "'.WT_I18N::translate('View image details').'";
		var CB_ImgNotes = "'.WT_I18N::translate('Notes').'";
		var CB_Note_Info = "";
		var CB_Pause_SS = "'.WT_I18N::translate('Pause Slideshow').'";
		var CB_Start_SS = "'.WT_I18N::translate('Start Slideshow').'";
		var CB_Music = "'.WT_I18N::translate('Turn Music On/Off').'";
		var CB_Zoom_Off = "'.WT_I18N::translate('Disable Zoom').'";
		var CB_Zoom_On = "'.WT_I18N::translate('Zoom is enabled ... Use mousewheel or i and o keys to zoom in and out').'";
		var CB_Close_Win = "'.WT_I18N::translate('Close Lightbox window').'";
		var CB_Balloon = "false";'; // Notes Tooltip Balloon or not
		if ($TEXT_DIRECTION=='ltr') {
			$js.='var CB_Alignm = "left";'; // Notes LTR Tooltip Balloon Text align
		} else {
			$js.='var CB_Alignm = "right";'; // Notes RTL Tooltip Balloon Text align
		}
		$js.='var CB_ImgNotes2 = "'.WT_I18N::translate('Notes').'";'; // Notes RTL Tooltip for Full Image
		if ($LB_MUSIC_FILE == '') {
			$js.='var myMusic = null;';
		} else {
			$js.='var myMusic  = "'.$LB_MUSIC_FILE.'";';   // The music file
		}
		$js.='var CB_SlShowTime  = "'.get_module_setting('lightbox', 'LB_SS_SPEED', '6').'"; // Slide show timer
		var CB_Animation = "'.get_module_setting('lightbox', 'LB_TRANSITION', 'warp').'";'; // Next/Prev Image transition effect

		$controller->addExternalJavascript('module.php?mod='.$this->getName().'&mod_action=js');
		$controller->addInlineJavascript($js, WT_Controller_Base::JS_PRIORITY_HIGH); // Run this *before* loading the library files
		return true;
	}

	static public function getMediaListMenu($mediaobject) {
		$html='<div id="lightbox-menu"><ul class="makeMenu lb-menu">';
		$menu = new WT_Menu(WT_I18N::translate('Edit Details'), '#', 'lb-image_edit');
		$menu->addOnclick("return window.open('addmedia.php?action=editmedia&amp;pid=".$mediaobject->getXref()."', '_blank', edit_window_specs);");
		$html.=$menu->getMenuAsList().'</ul><ul class="makeMenu lb-menu">';
		$menu = new WT_Menu(WT_I18N::translate('Set link'), '#', 'lb-image_link');
		$menu->addOnclick("return ilinkitem('".$mediaobject->getXref()."','person')");
		$submenu = new WT_Menu(WT_I18N::translate('To Person'), '#');
		$submenu->addOnclick("return ilinkitem('".$mediaobject->getXref()."','person')");
		$menu->addSubMenu($submenu);
		$submenu = new WT_Menu(WT_I18N::translate('To Family'), '#');
		$submenu->addOnclick("return ilinkitem('".$mediaobject->getXref()."','family')");
		$menu->addSubMenu($submenu);
		$submenu = new WT_Menu(WT_I18N::translate('To Source'), '#');
		$submenu->addOnclick("return ilinkitem('".$mediaobject->getXref()."','source')");
		$menu->addSubMenu($submenu);
		$html.=$menu->getMenuAsList().'</ul><ul class="makeMenu lb-menu">';
		$menu = new WT_Menu(WT_I18N::translate('View Details'), $mediaobject->getHtmlUrl(), 'lb-image_view');
		$html.=$menu->getMenuAsList();
		$html.='</ul></div>';
		return $html;
	}
	
	private function config() {
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
			<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
		</form>
		<?php
	}
}
