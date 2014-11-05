<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

class gedcom_block_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Home page');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Home page” module */ WT_I18N::translate('A greeting message for site visitors.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $controller;

		$indi_xref=$controller->getSignificantIndividual()->getXref();
		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		$title='<span dir="auto">'.WT_TREE_TITLE.'</span>';
		$content = '<table><tr>';
		$content .= '<td><a href="pedigree.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL.'"><i class="icon-pedigree"></i><br>'.WT_I18N::translate('Default chart').'</a></td>';
		$content .= '<td><a href="individual.php?pid='.$indi_xref.'&amp;ged='.WT_GEDURL.'"><i class="icon-indis"></i><br>'.WT_I18N::translate('Default individual').'</a></td>';
		if (WT_Site::getPreference('USE_REGISTRATION_MODULE') && !Auth::check()) {
			$content .= '<td><a href="'.WT_LOGIN_URL.'?action=register"><i class="icon-user_add"></i><br>'.WT_I18N::translate('Request new user account').'</a></td>';
		}
		$content .= "</tr>";
		$content .= "</table>";

		if ($template) {
			require WT_THEME_DIR.'templates/block_main_temp.php';
		} else {
			return $content;
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}
}
