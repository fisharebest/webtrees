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

class user_welcome_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('My page');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “My page” module */ WT_I18N::translate('A greeting message and useful links for a user.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		$title = '<span dir="auto">' . /* I18N: A greeting; %s is the user’s name */ WT_I18N::translate('Welcome %s', Auth::user()->getRealName()) . '</span>';
		$content = '<table><tr>';
		if (Auth::user()->getPreference('editaccount')) {
			$content .= '<td><a href="edituser.php"><i class="icon-mypage"></i><br>'.WT_I18N::translate('My account').'</a></td>';
		}
		if (WT_USER_GEDCOM_ID) {
			$content .= '<td><a href="pedigree.php?rootid='.WT_USER_GEDCOM_ID.'&amp;ged='.WT_GEDURL.'"><i class="icon-pedigree"></i><br>'.WT_I18N::translate('My pedigree').'</a></td>';
			$content .= '<td><a href="individual.php?pid='.WT_USER_GEDCOM_ID.'&amp;ged='.WT_GEDURL.'"><i class="icon-indis"></i><br>'.WT_I18N::translate('My individual record').'</a></td>';
		}
		$content .= '</tr></table>';

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
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}
}
