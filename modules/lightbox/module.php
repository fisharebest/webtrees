<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2009 John Finlay
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
 * @subpackage Modules
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';
require_once WT_ROOT.'modules/lightbox/lb_defaultconfig.php';

class lightbox_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return i18n::translate('Album');
	}

	// Extend WT_Module
	public function getDescription() {
		return i18n::translate('Adds a tab (Album) to the individual page which an alternate way to view and work with media.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
		case 'album':
		case 'lb_editconfig':
			// TODO: these files should be methods in this class
			require WT_ROOT.'modules/'.$this->getName().'/'.$mod_action.'.php';
			break;
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&mod_action=lb_editconfig';
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 60;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		global $MULTI_MEDIA;
		return $MULTI_MEDIA && $this->get_media_count()>0;
	}
	
	// Implement WT_Module_Tab
	public function getTabContent() {
		global $MULTI_MEDIA, $SHOW_ID_NUMBERS, $MEDIA_EXTERNAL;
		global $GEDCOM, $MEDIATYPE;
		global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $is_media;
		global $cntm1, $cntm2, $cntm3, $cntm4, $t, $mgedrec ;
		global $edit ;
		global $pid, $tabno;
		global $Fam_Navigator, $NAV_ALBUM;

		ob_start();
		$mediacnt = $this->get_media_count();
		require_once 'modules/lightbox/functions/lb_head.php';
		echo "<div id=\"lightbox2_content\">";

		$media_found = false;
		if (!$this->controller->indi->canDisplayDetails()) {
			print "<table class=\"facts_table\" cellpadding=\"0\">\n";
			print "<tr><td class=\"facts_value\">";
			print_privacy_error();
			print "</td></tr>";
			print "</table>";
		}else{
			if (file_exists("modules/lightbox/album.php")) {
				include_once('modules/lightbox/album.php');
			}
		}
		echo "</div>";

		$out = ob_get_contents();
		ob_end_clean();
		$out .= "</div>";
		return $out;
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		ob_start();
		require_once WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
		return ob_get_clean(); 
	}
	
	// Implement WT_Module_Tab
	public function getJSCallback() {
		return 'CB_Init();';
	}

	protected $mediaCount = null;
	
	private function get_media_count() {
		if ($this->mediaCount===null) {
			$ct = preg_match("/\d OBJE/", $this->controller->indi->getGedcomRecord());
			foreach ($this->controller->indi->getSpouseFamilies() as $k=>$sfam)
				$ct += preg_match("/\d OBJE/", $sfam->getGedcomRecord());
			$this->mediaCount = $ct;
		}
		return $this->mediaCount;
	}

}
