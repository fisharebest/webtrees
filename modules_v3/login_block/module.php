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

class login_block_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Login');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Login” module */ WT_I18N::translate('An alternative way to login and logout.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $controller;
		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		$controller->addInlineJavascript('
			jQuery("#new_passwd").hide();
			jQuery("#passwd_click").click(function() {
				jQuery("#new_passwd").slideToggle(100, function() {
					jQuery("#new_passwd_username").focus();
				});
				return false;
			});
		');

		if (Auth::check()) {
			$title   = WT_I18N::translate('Logout');
			$content = '<div class="center"><form method="post" action="logout.php" name="logoutform" onsubmit="return true;">';
			$content .= '<br><a href="edituser.php" class="name2">'.WT_I18N::translate('Logged in as ') . ' ' . WT_Filter::escapeHtml(Auth::user()->getRealName()) . '</a><br><br>';
			$content .= '<input type="submit" value="' . WT_I18N::translate('Logout') . '">';

			$content .= '<br><br></form></div>';
		} else {
			$title   = WT_I18N::translate('Login');
			$content = '<div id="login-box">
				<form id="login-form" name="login-form" method="post" action="'. WT_LOGIN_URL. '" onsubmit="d=new Date(); this.timediff.value=d.getTimezoneOffset()*60;">
				<input type="hidden" name="action" value="login">
				<input type="hidden" name="timediff" value="">';
			$content.= '<div>
				<label for="username">'. WT_I18N::translate('Username').
					'<input type="text" id="username" name="username" class="formField">
				</label>
				</div>
				<div>
					<label for="password">'. WT_I18N::translate('Password').
						'<input type="password" id="password" name="password" class="formField">
					</label>
				</div>
				<div>
					<input type="submit" value="'. WT_I18N::translate('Login'). '">
				</div>
				<div>
					<a href="#" id="passwd_click">'. WT_I18N::translate('Request new password').'</a>
				</div>';
			if (WT_Site::getPreference('USE_REGISTRATION_MODULE')) {
				$content .= '<div><a href="'.WT_LOGIN_URL.'?action=register">'. WT_I18N::translate('Request new user account').'</a></div>';
			}
		$content .= '</form>'; // close "login-form"

		// hidden New Password block
		$content .= '<div id="new_passwd">
			<form id="new_passwd_form" name="new_passwd_form" action="'.WT_LOGIN_URL.'" method="post">
			<input type="hidden" name="time" value="">
			<input type="hidden" name="action" value="requestpw">
			<h4>'. WT_I18N::translate('Lost password request').'</h4>
			<div>
				<label for="new_passwd_username">'. WT_I18N::translate('Username or email address').
					'<input type="text" id="new_passwd_username" name="new_passwd_username" value="">
				</label>
			</div>
			<div><input type="submit" value="'. WT_I18N::translate('continue'). '"></div>
			</form>
		</div>'; //"new_passwd"
		$content .= '</div>';//"login-box"
		}

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
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}
}
