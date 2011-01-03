<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'modules/lightbox/lb_defaultconfig.php';

class lightbox_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return WT_I18N::translate('Album');
	}

	// Extend WT_Module
	public function getDescription() {
		return WT_I18N::translate('Adds a tab (Album) to the individual page which an alternate way to view and work with media.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
		case 'album':
			// TODO: these files should be methods in this class
			require WT_ROOT.'modules/'.$this->getName().'/'.$mod_action.'.php';
			break;
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&mod_action=admin_config';
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 60;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		global $MULTI_MEDIA;
		return $MULTI_MEDIA && (WT_USER_CAN_EDIT || $this->get_media_count()>0);
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $MULTI_MEDIA, $MEDIA_EXTERNAL;
		global $GEDCOM, $MEDIATYPE;
		global $WORD_WRAPPED_NOTES, $MEDIA_DIRECTORY, $WT_IMAGES, $TEXT_DIRECTION, $is_media;
		global $cntm1, $cntm2, $cntm3, $cntm4, $t, $mgedrec ;
		global $edit ;
		global $tabno;
		global $Fam_Navigator, $NAV_ALBUM;

		ob_start();
		require WT_ROOT.'modules/lightbox/functions/lb_head.php';

		$media_found = false;
		if (!$this->controller->indi->canDisplayDetails()) {
			echo "<table class=\"facts_table\" cellpadding=\"0\">";
			echo "<tr><td class=\"facts_value\">";
			print_privacy_error();
			echo "</td></tr>";
			echo "</table>";
		} else {
			require WT_ROOT.'modules/lightbox/album.php';
		}
		return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
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
			foreach ($this->controller->indi->getSpouseFamilies() as $sfam)
				$ct += preg_match("/\d OBJE/", $sfam->getGedcomRecord());
			$this->mediaCount = $ct;
		}
		return $this->mediaCount;
	}

}
