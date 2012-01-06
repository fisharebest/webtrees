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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class login_block_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Login');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Login" module */ WT_I18N::translate('An alternative way to login and logout.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $controller;
		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		if (WT_USER_ID) {
			$title = WT_I18N::translate('Logout');
			$content='';
			$content = '<div class="center"><form method="post" action="index.php?logout=1" name="logoutform" onsubmit="return true;">';
			$content .= '<br><a href="edituser.php" class="name2">'.WT_I18N::translate('Logged in as ').' ('.WT_USER_NAME.')</a><br><br>';

			$content .= "<input type=\"submit\" value=\"".WT_I18N::translate('Logout')."\">";

			$content .= "<br><br></form></div>";
		} else {
			$title = WT_I18N::translate('Login');
			$LOGIN_URL=get_site_setting('LOGIN_URL');		
			$controller
				->addInlineJavaScript('
					  jQuery("#new_passwd").hide();
					  jQuery("#passwd_click").click(function()
					  {
						jQuery("#new_passwd").slideToggle(500);
					  });
				');
			$content='';
			$content='<form id="login-form" name="loginform" method="post" action="'. get_site_setting('LOGIN_URL'). '" onsubmit="t = new Date(); document.loginform.usertime.value=t.getFullYear()+\'-\'+(t.getMonth()+1)+\'-\'+t.getDate()+\' \'+t.getHours()+\':\'+t.getMinutes()+\':\'+t.getSeconds(); return true;">
			<input type="hidden" name="action" value="login">
				<input type="hidden" name="url" value="index.php">
				<input type="hidden" name="ged" value="'; if (isset($ged)) $content.= htmlspecialchars($ged); else $content.= htmlentities(WT_GEDCOM); $content.= '">
				<input type="hidden" name="pid" value="'; if (isset($pid)) $content.= htmlspecialchars($pid); $content.= '">
				<input type="hidden" name="usertime" value="">';
			$content.= '<div>
				<label for="username">'. WT_I18N::translate('Username').'</label>'.
				'<input type="text" id="username" name="username" size="20" class="formField">
				</div>
				<div>
					<label for="password">'. WT_I18N::translate('Password').'</label>'.
					'<input type="password" id="password" name="password" size="20" class="formField">
				</div>
				<div>
					<input type="submit" value="'. WT_I18N::translate('Login'). '">
				</div>
				<div>
					<a href="#" id="passwd_click">'. WT_I18N::translate('Request new password').'</a>
				</div>';
			if (get_site_setting('USE_REGISTRATION_MODULE')) {
				$content.= '<div><a href="login.php?action=register">'. WT_I18N::translate('Request new user account').'</a></div>';
			}
		$content.= '</form>'; // close "login-form"
		
		// hidden New Password block
		$content.= '<div id="new_passwd">
			<form id="new_passwd_form" name="requestpwform" action="login.php" method="post" onsubmit="t = new Date(); document.requestpwform.time.value=t.toUTCString(); return checkform(this);">
			<input type="hidden" name="time" value="">
			<input type="hidden" name="action" value="requestpw">
			<h4>'. WT_I18N::translate('Lost password request').'</h4>
			<div>
				<label for="username">'. WT_I18N::translate('Username or email address'). '</label>
				<input type="text" id="username" name="user_name" value="" autofocus>
			</div>
			<div><input type="submit" value="'. WT_I18N::translate('Continue'). '"></div>
			</form>
		</div>'; //"new_passwd"
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
