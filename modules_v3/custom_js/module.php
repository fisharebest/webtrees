<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * Copyright (C) 2015 JustCarmen
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

class custom_js_WT_Module extends Module implements ModuleConfigInterface, ModuleMenuInterface {

	// Extend WT_Module
	public function getTitle() {
		return I18N::translate('Custom JavaScript');
	}

	// Extend WT_Module
	public function getDescription() {
		return I18N::translate('Allows you to easily add Custom JavaScript to your webtrees site.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch ($mod_action) {
			case 'admin_config':
				$controller = new PageController;
				$controller
					->restrictAccess(Auth::isAdmin())
					->setPageTitle($this->getTitle())
					->addInlineJavascript("
					jQuery('head')
					.append(\"<style type='text/css'> \
						form {width:600px} \
						form fieldset{border:none} \
					</style>\");")
					->pageHeader();

				$action = Filter::post("action");

				if ($action == 'update') {
					$this->setSetting('CJS_FOOTER', Filter::post('NEW_CJS_FOOTER'));
					Log::addConfigurationLog($this->getTitle() . ' config updated');
				}

				$CJS_FOOTER = $this->getSetting('CJS_FOOTER');
				?>
				<h3><?php echo I18N::translate('Custom Javascript for Footer'); ?></h3>
				<form method="post" name="configform" action="<?php echo $this->getConfigLink(); ?>">
					<input type="hidden" name="action" value="update">
					<fieldset>
						<textarea rows="10" cols="60" name="NEW_CJS_FOOTER"><?php echo $CJS_FOOTER; ?></textarea>
					</fieldset>
					<input type="submit" value="<?php echo I18N::translate('Save configuration'); ?>">
				</form>
				<?php
				break;

			default:
				http_response_code(404);
				break;
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_config';
	}

	// Implement WT_Module_Menu
	public function defaultMenuOrder() {
		return 999;
	}

	// Implement WT_Module_Menu
	public function getMenu() {
		// We don't actually have a menu - this is just a convenient "hook" to execute
		// code at the right time during page execution
		global $controller;

		$cjs_footer = $this->getSetting('CJS_FOOTER');
		if (strpos($cjs_footer, '#') !== false) {
			# parse for embedded keywords
			$stats = new WT_Stats(WT_GEDCOM);
			$cjs_footer = $stats->embedTags($cjs_footer);
		}
		$controller->addInlineJavaScript($cjs_footer, BaseController::JS_PRIORITY_LOW);

		return null;
	}

}
