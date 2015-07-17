<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Theme;

/**
 * Class LoginBlockModule
 */
class LoginBlockModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Login');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Login” module */ I18N::translate('An alternative way to login and logout.');
	}

	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = array()) {
		global $controller;
		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
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
			$title   = I18N::translate('Logout');
			$content = '<div class="center"><form method="post" action="logout.php" name="logoutform" onsubmit="return true;">';
			$content .= '<br><a href="../../edituser.php" class="name2">' . I18N::translate('Logged in as ') . ' ' . Auth::user()->getRealNameHtml() . '</a><br><br>';
			$content .= '<input type="submit" value="' . I18N::translate('Logout') . '">';

			$content .= '<br><br></form></div>';
		} else {
			$title   = I18N::translate('Login');
			$content = '<div id="login-box">
				<form id="login-form" name="login-form" method="post" action="' . WT_LOGIN_URL . '">
				<input type="hidden" name="action" value="login">';
			$content .= '<div>
				<label for="username">' . I18N::translate('Username') .
					'<input type="text" id="username" name="username" class="formField">
				</label>
				</div>
				<div>
					<label for="password">' . I18N::translate('Password') .
						'<input type="password" id="password" name="password" class="formField">
					</label>
				</div>
				<div>
					<input type="submit" value="' . I18N::translate('Login') . '">
				</div>
				<div>
					<a href="#" id="passwd_click">' . I18N::translate('Request new password') . '</a>
				</div>';
			if (Site::getPreference('USE_REGISTRATION_MODULE')) {
				$content .= '<div><a href="' . WT_LOGIN_URL . '?action=register">' . I18N::translate('Request new user account') . '</a></div>';
			}
		$content .= '</form>'; // close "login-form"

		// hidden New Password block
		$content .= '<div id="new_passwd">
			<form id="new_passwd_form" name="new_passwd_form" action="' . WT_LOGIN_URL . '" method="post">
			<input type="hidden" name="time" value="">
			<input type="hidden" name="action" value="requestpw">
			<h4>' . I18N::translate('Lost password request') . '</h4>
			<div>
				<label for="new_passwd_username">' . I18N::translate('Username or email address') .
					'<input type="text" id="new_passwd_username" name="new_passwd_username" value="">
				</label>
			</div>
			<div><input type="submit" value="' . I18N::translate('continue') . '"></div>
			</form>
		</div>'; //"new_passwd"
		$content .= '</div>'; //"login-box"
		}

		if ($template) {
			return Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return false;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id) {
	}
}
